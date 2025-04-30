<?php

namespace App\Http\Controllers\Barber;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    /**
     * Hiển thị danh sách lịch hẹn của thợ cắt tóc
     */
    public function index(Request $request)
    {
        $barber = Auth::user()->barber;
        $status = $request->input('status');
        $date = $request->input('date');

        $query = Appointment::where('barber_id', $barber->id);

        if ($status) {
            $query->where('status', $status);
        }

        if ($date) {
            $query->whereDate('appointment_date', $date);
        }

        $appointments = $query->latest()->paginate(10);

        return view('barber.appointments.index', compact('appointments', 'status', 'date'));
    }

    /**
     * Hiển thị chi tiết lịch hẹn
     */
    public function show(Appointment $appointment)
    {
        // Kiểm tra xem lịch hẹn có thuộc về thợ cắt tóc hiện tại không
        if ($appointment->barber_id != Auth::user()->barber->id) {
            return redirect()->route('barber.appointments.index')
                ->with('error', 'Bạn không có quyền xem lịch hẹn này.');
        }

        return view('barber.appointments.show', compact('appointment'));
    }

    /**
     * Cập nhật trạng thái lịch hẹn thành hoàn thành
     */
    public function markAsCompleted(Request $request, Appointment $appointment)
    {
        // Kiểm tra xem lịch hẹn có thuộc về thợ cắt tóc hiện tại không
        if ($appointment->barber_id != Auth::user()->barber->id) {
            return redirect()->route('barber.appointments.index')
                ->with('error', 'Bạn không có quyền cập nhật lịch hẹn này.');
        }

        // Kiểm tra xem lịch hẹn có thể đánh dấu hoàn thành không
        if ($appointment->status != 'confirmed') {
            return redirect()->route('barber.appointments.show', $appointment->id)
                ->with('error', 'Chỉ có thể đánh dấu hoàn thành cho lịch hẹn đã được xác nhận.');
        }

        $oldStatus = $appointment->status;
        $appointment->status = 'completed';
        $appointment->save();

        // Cập nhật trạng thái thanh toán nếu được cung cấp
        if ($request->has('payment_status')) {
            $appointment->payment_status = $request->payment_status;
            $appointment->save();
        }

        // Tạo hóa đơn mới
        $this->createInvoiceFromAppointment($appointment);

        return redirect()->route('barber.appointments.show', $appointment->id)
            ->with('success', 'Lịch hẹn đã được đánh dấu hoàn thành thành công.');
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

        // Tính tổng tiền từ các dịch vụ
        $subtotal = 0;
        foreach ($appointment->services as $service) {
            $subtotal += $service->pivot->price ?? $service->price;
        }

        // Tạo mã hóa đơn
        $invoiceCode = 'INV-' . time() . '-' . $appointment->id;

        // Tạo hóa đơn mới
        $invoice = new Invoice([
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
            'payment_status' => $appointment->payment_status ?? 'pending',
            'status' => 'completed',
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
                'subtotal' => $price,
            ]);
        }

        return $invoice;
    }
}
