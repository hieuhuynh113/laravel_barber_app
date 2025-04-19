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

        // Kiểm tra xem time slot có còn chỗ trống không
        $timeSlot = \App\Models\TimeSlot::where('barber_id', $request->barber_id)
            ->where('date', $request->appointment_date)
            ->where('time_slot', $request->appointment_time)
            ->first();

        // Nếu không tìm thấy time slot, tạo mới
        if (!$timeSlot) {
            // Lấy lịch làm việc của thợ cắt tóc
            $dayOfWeek = \Carbon\Carbon::parse($request->appointment_date)->dayOfWeek;
            $schedule = \App\Models\BarberSchedule::where('barber_id', $request->barber_id)
                ->where('day_of_week', $dayOfWeek)
                ->first();

            if (!$schedule || $schedule->is_day_off) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Không thể đặt lịch vào ngày nghỉ của thợ cắt tóc.');
            }

            $maxBookings = $schedule ? $schedule->max_appointments : 2;

            $timeSlot = \App\Models\TimeSlot::create([
                'barber_id' => $request->barber_id,
                'date' => $request->appointment_date,
                'time_slot' => $request->appointment_time,
                'booked_count' => 0,
                'max_bookings' => $maxBookings,
            ]);
        }

        // Kiểm tra xem time slot có còn chỗ trống không
        if (!$timeSlot->isAvailable() && $request->status != 'canceled') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Khung giờ này đã đầy. Vui lòng chọn khung giờ khác.');
        }

        $appointment = Appointment::create([
            'user_id' => $request->user_id,
            'barber_id' => $request->barber_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'time_slot' => $request->appointment_time, // Thêm time_slot
            'note' => $request->note,
            'status' => $request->status,
        ]);

        $appointment->services()->attach($request->service_ids);

        // Tăng số lượng đặt chỗ trong time slot nếu lịch hẹn không bị hủy
        if ($request->status != 'canceled') {
            $timeSlot->incrementBookedCount();
        }

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

        // Lưu thông tin cũ để cập nhật time slot
        $oldDate = $appointment->appointment_date;
        $oldTime = $appointment->time_slot;
        $oldBarberId = $appointment->barber_id;
        $oldStatus = $appointment->status;

        // Cập nhật lịch hẹn
        $appointment->update([
            'user_id' => $request->user_id,
            'barber_id' => $request->barber_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'time_slot' => $request->appointment_time, // Cập nhật time_slot
            'note' => $request->note,
            'status' => $request->status,
        ]);

        $appointment->services()->sync($request->service_ids);

        // Nếu lịch hẹn không bị hủy và thời gian hoặc thợ cắt tóc thay đổi
        if ($oldStatus != 'canceled' && $appointment->status != 'canceled') {
            // Giảm số lượng đặt chỗ trong time slot cũ
            if ($oldTime) {
                $oldTimeSlot = \App\Models\TimeSlot::where('barber_id', $oldBarberId)
                    ->where('date', $oldDate)
                    ->where('time_slot', $oldTime)
                    ->first();

                if ($oldTimeSlot) {
                    $oldTimeSlot->decrementBookedCount();
                }
            }

            // Tăng số lượng đặt chỗ trong time slot mới
            if ($appointment->time_slot) {
                $newTimeSlot = \App\Models\TimeSlot::where('barber_id', $appointment->barber_id)
                    ->where('date', $appointment->appointment_date)
                    ->where('time_slot', $appointment->time_slot)
                    ->first();

                // Nếu không tìm thấy time slot, tạo mới
                if (!$newTimeSlot) {
                    // Lấy lịch làm việc của thợ cắt tóc
                    $dayOfWeek = \Carbon\Carbon::parse($appointment->appointment_date)->dayOfWeek;
                    $schedule = \App\Models\BarberSchedule::where('barber_id', $appointment->barber_id)
                        ->where('day_of_week', $dayOfWeek)
                        ->first();

                    $maxBookings = $schedule ? $schedule->max_appointments : 2;

                    $newTimeSlot = \App\Models\TimeSlot::create([
                        'barber_id' => $appointment->barber_id,
                        'date' => $appointment->appointment_date,
                        'time_slot' => $appointment->time_slot,
                        'booked_count' => 0,
                        'max_bookings' => $maxBookings,
                    ]);
                }

                if ($newTimeSlot->isAvailable()) {
                    $newTimeSlot->incrementBookedCount();
                } else {
                    // Nếu time slot mới đã đầy, khôi phục time slot cũ
                    if ($oldTime) {
                        $oldTimeSlot = \App\Models\TimeSlot::where('barber_id', $oldBarberId)
                            ->where('date', $oldDate)
                            ->where('time_slot', $oldTime)
                            ->first();

                        if ($oldTimeSlot) {
                            $oldTimeSlot->incrementBookedCount();
                        }
                    }

                    // Khôi phục thông tin cũ cho lịch hẹn
                    $appointment->update([
                        'appointment_date' => $oldDate,
                        'appointment_time' => $oldTime,
                        'time_slot' => $oldTime,
                        'barber_id' => $oldBarberId,
                    ]);

                    return redirect()->route('admin.appointments.index')
                        ->with('error', 'Không thể cập nhật lịch hẹn vì khung giờ mới đã đầy.');
                }
            }
        }

        return redirect()->route('admin.appointments.index')
            ->with('success', 'Lịch hẹn đã được cập nhật thành công.');
    }

    public function destroy(Appointment $appointment)
    {
        // Giảm số lượng đặt chỗ trong time slot nếu có
        if ($appointment->time_slot) {
            $timeSlot = \App\Models\TimeSlot::where('barber_id', $appointment->barber_id)
                ->where('date', $appointment->appointment_date)
                ->where('time_slot', $appointment->time_slot)
                ->first();

            if ($timeSlot) {
                $timeSlot->decrementBookedCount();
            }
        }

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

        // Nếu trạng thái thay đổi sang "canceled", giảm số lượng đặt chỗ trong time slot
        if ($newStatus == 'canceled' && $oldStatus != 'canceled' && $appointment->time_slot) {
            $timeSlot = \App\Models\TimeSlot::where('barber_id', $appointment->barber_id)
                ->where('date', $appointment->appointment_date)
                ->where('time_slot', $appointment->time_slot)
                ->first();

            if ($timeSlot) {
                $timeSlot->decrementBookedCount();
            }
        }

        // Nếu trạng thái thay đổi từ "canceled" sang trạng thái khác, tăng số lượng đặt chỗ
        if ($oldStatus == 'canceled' && $newStatus != 'canceled' && $appointment->time_slot) {
            $timeSlot = \App\Models\TimeSlot::where('barber_id', $appointment->barber_id)
                ->where('date', $appointment->appointment_date)
                ->where('time_slot', $appointment->time_slot)
                ->first();

            if ($timeSlot && $timeSlot->isAvailable()) {
                $timeSlot->incrementBookedCount();
            } else if ($timeSlot && !$timeSlot->isAvailable()) {
                // Nếu time slot đã đầy, không thể khôi phục lịch hẹn
                return redirect()->back()
                    ->with('error', 'Không thể khôi phục lịch hẹn vì khung giờ này đã đầy.');
            }
        }

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

        // Lưu ý: Hóa đơn tạo từ lịch hẹn ban đầu chưa có sản phẩm
        // Các sản phẩm có thể được thêm vào sau khi chỉnh sửa hóa đơn
        // hoặc khi khách hàng mua thêm sản phẩm ngoài lịch hẹn

        return $invoice;
    }


}