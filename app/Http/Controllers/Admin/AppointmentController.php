<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $date = $request->input('date');
        $barberId = $request->input('barber_id');

        $query = Appointment::with(['user', 'barber.user', 'services']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($date) {
            $query->whereDate('appointment_date', $date);
        }

        if ($barberId) {
            $query->where('barber_id', $barberId);
        }

        $appointments = $query->latest()->paginate(10);
        $barbers = User::where('role', 'barber')->get();

        return view('admin.appointments.index', compact('appointments', 'barbers', 'status', 'date', 'barberId'));
    }

    public function create()
    {
        $customers = User::where('role', 'customer')->get();
        $barbers = User::where('role', 'barber')->with('barber')->get();
        $services = Service::active()->get();

        return view('admin.appointments.create', compact('customers', 'barbers', 'services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'barber_id' => 'required|exists:barbers,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'service_ids' => 'required|array',
            'service_ids.*' => 'exists:services,id',
            'note' => 'nullable|string',
            'status' => 'required|in:pending,confirmed,completed,canceled',
        ]);

        $appointment = Appointment::create([
            'user_id' => $request->user_id,
            'barber_id' => $request->barber_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'note' => $request->note,
            'status' => $request->status,
        ]);

        $appointment->services()->attach($request->service_ids);

        return redirect()->route('admin.appointments.index')
            ->with('success', 'Lịch hẹn đã được tạo thành công.');
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['user', 'barber.user', 'services']);
        return view('admin.appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        $customers = User::where('role', 'customer')->get();
        $barbers = User::where('role', 'barber')->with('barber')->get();
        $services = Service::active()->get();

        $selectedServices = $appointment->services->pluck('id')->toArray();

        return view('admin.appointments.edit', compact('appointment', 'customers', 'barbers', 'services', 'selectedServices'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'barber_id' => 'required|exists:barbers,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'service_ids' => 'required|array',
            'service_ids.*' => 'exists:services,id',
            'note' => 'nullable|string',
            'status' => 'required|in:pending,confirmed,completed,canceled',
        ]);

        $appointment->update([
            'user_id' => $request->user_id,
            'barber_id' => $request->barber_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'note' => $request->note,
            'status' => $request->status,
        ]);

        $appointment->services()->sync($request->service_ids);

        return redirect()->route('admin.appointments.index')
            ->with('success', 'Lịch hẹn đã được cập nhật thành công.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->services()->detach();
        $appointment->delete();

        return redirect()->route('admin.appointments.index')
            ->with('success', 'Lịch hẹn đã được xóa thành công.');
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,canceled',
        ]);

        $appointment->update([
            'status' => $request->status,
        ]);

        return redirect()->back()
            ->with('success', 'Trạng thái lịch hẹn đã được cập nhật thành công.');
    }


}