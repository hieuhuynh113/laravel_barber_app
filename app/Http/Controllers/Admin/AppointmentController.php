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

        $oldStatus = $appointment->status;
        $newStatus = $request->status;

        $appointment->update([
            'status' => $newStatus,
        ]);

        // Nếu trạng thái thay đổi từ "confirmed" sang "completed"
        if ($oldStatus == 'confirmed' && $newStatus == 'completed') {
            // Tạo hóa đơn mới
            $this->createInvoiceFromAppointment($appointment);

            return redirect()->back()
                ->with('success', 'Trạng thái lịch hẹn đã được cập nhật thành công và hóa đơn đã được tạo.');
        }

        return redirect()->back()
            ->with('success', 'Trạng thái lịch hẹn đã được cập nhật thành công.');
    }

    /**
     * Tạo hóa đơn từ lịch hẹn
     */
    private function createInvoiceFromAppointment(Appointment $appointment)
    {
        // Kiểm tra xem đã có hóa đơn cho lịch hẹn này chưa
        if ($appointment->invoice) {
            return $appointment->invoice;
        }

        // Tạo mã hóa đơn
        $invoiceCode = 'INV-' . date('Ymd') . '-' . str_pad($appointment->id, 4, '0', STR_PAD_LEFT);

        // Tính tổng tiền từ các dịch vụ
        $subtotal = 0;
        foreach ($appointment->services as $service) {
            $subtotal += $service->pivot->price ?? $service->price;
        }

        // Tạo hóa đơn mới
        $invoice = new \App\Models\Invoice([
            'invoice_code' => $invoiceCode,
            'appointment_id' => $appointment->id,
            'user_id' => $appointment->user_id,
            'barber_id' => $appointment->barber_id,
            'invoice_number' => 'INV-' . time(),
            'subtotal' => $subtotal,
            'discount' => 0, // Có thể thêm logic giảm giá nếu cần
            'tax' => 0, // Có thể thêm thuế nếu cần
            'total' => $subtotal,
            'total_amount' => $subtotal,
            'payment_method' => $appointment->payment_method ?? 'cash',
            'payment_status' => 'paid', // Đã thanh toán
            'status' => 'completed', // Hoàn thành
            'notes' => 'Tự động tạo từ lịch hẹn #' . $appointment->id,
        ]);

        $invoice->save();

        // Thêm các dịch vụ vào hóa đơn
        foreach ($appointment->services as $service) {
            $price = $service->pivot->price ?? $service->price;
            $invoice->services()->attach($service->id, [
                'quantity' => 1,
                'price' => $price,
                'discount' => 0,
                'subtotal' => $price
            ]);
        }

        return $invoice;
    }


}