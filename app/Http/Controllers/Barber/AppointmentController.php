<?php

namespace App\Http\Controllers\Barber;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Notifications\BarberAppointmentNotification;

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
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền cập nhật lịch hẹn này.'
                ], 403);
            }

            return redirect()->route('barber.appointments.index')
                ->with('error', 'Bạn không có quyền cập nhật lịch hẹn này.');
        }

        // Kiểm tra xem lịch hẹn có thể đánh dấu hoàn thành không
        if ($appointment->status != 'confirmed') {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có thể đánh dấu hoàn thành cho lịch hẹn đã được xác nhận.'
                ], 400);
            }

            return redirect()->route('barber.appointments.show', $appointment->id)
                ->with('error', 'Chỉ có thể đánh dấu hoàn thành cho lịch hẹn đã được xác nhận.');
        }

        try {
            // Bắt đầu transaction để đảm bảo tính toàn vẹn dữ liệu
            \DB::beginTransaction();

            $oldStatus = $appointment->status;
            $appointment->status = 'completed';
            $appointment->save();

            // Cập nhật trạng thái thanh toán nếu được cung cấp
            if ($request->has('payment_status')) {
                $appointment->payment_status = $request->payment_status;
                $appointment->save();
            }

            // Tạo hóa đơn mới
            $invoice = $this->createInvoiceFromAppointment($appointment);

            // Commit transaction
            \DB::commit();

            // Ghi log
            Log::info("Lịch hẹn #{$appointment->id} đã được đánh dấu hoàn thành bởi barber #{Auth::user()->barber->id}");

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lịch hẹn đã được đánh dấu hoàn thành thành công.',
                    'appointment' => $appointment,
                    'invoice' => $invoice
                ]);
            }

            return redirect()->route('barber.appointments.show', $appointment->id)
                ->with('success', 'Lịch hẹn đã được đánh dấu hoàn thành thành công.');
        } catch (\Exception $e) {
            // Rollback transaction nếu có lỗi
            \DB::rollBack();

            // Ghi log lỗi
            Log::error("Lỗi khi đánh dấu hoàn thành lịch hẹn #{$appointment->id}: " . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đã xảy ra lỗi khi xử lý yêu cầu: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('barber.appointments.show', $appointment->id)
                ->with('error', 'Đã xảy ra lỗi khi xử lý yêu cầu: ' . $e->getMessage());
        }
    }

    /**
     * Xác nhận lịch hẹn
     */
    public function confirmAppointment(Request $request, Appointment $appointment)
    {
        // Kiểm tra xem lịch hẹn có thuộc về thợ cắt tóc hiện tại không
        if ($appointment->barber_id != Auth::user()->barber->id) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xác nhận lịch hẹn này.'
                ], 403);
            }

            return redirect()->route('barber.appointments.index')
                ->with('error', 'Bạn không có quyền xác nhận lịch hẹn này.');
        }

        // Kiểm tra xem lịch hẹn có thể xác nhận không
        if ($appointment->status != 'pending') {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có thể xác nhận lịch hẹn đang ở trạng thái chờ xác nhận.'
                ], 400);
            }

            return redirect()->route('barber.appointments.show', $appointment->id)
                ->with('error', 'Chỉ có thể xác nhận lịch hẹn đang ở trạng thái chờ xác nhận.');
        }

        try {
            // Bắt đầu transaction để đảm bảo tính toàn vẹn dữ liệu
            \DB::beginTransaction();

            $oldStatus = $appointment->status;
            $appointment->status = 'confirmed';
            $appointment->save();

            // Gửi email xác nhận cho khách hàng
            Mail::to($appointment->email)
                ->send(new \App\Mail\AppointmentConfirmed($appointment));
            Log::info("Email xác nhận lịch hẹn đã được gửi đến {$appointment->email} cho lịch hẹn {$appointment->booking_code} bởi barber {$appointment->barber->user->name}");

            // Gửi thông báo cho barber về lịch hẹn đã được xác nhận
            $barberUser = Auth::user();
            $barberUser->notify(new BarberAppointmentNotification($appointment, 'confirmed'));
            Log::info("Thông báo xác nhận lịch hẹn đã được gửi đến barber {$barberUser->name} cho lịch hẹn {$appointment->booking_code}");

            // Commit transaction
            \DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lịch hẹn đã được xác nhận thành công và email xác nhận đã được gửi cho khách hàng.',
                    'appointment' => $appointment
                ]);
            }

            return redirect()->route('barber.appointments.show', $appointment->id)
                ->with('success', 'Lịch hẹn đã được xác nhận thành công và email xác nhận đã được gửi cho khách hàng.');
        } catch (\Exception $e) {
            // Rollback transaction nếu có lỗi
            \DB::rollBack();

            // Ghi log lỗi
            Log::error("Lỗi khi xác nhận lịch hẹn #{$appointment->id}: " . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đã xảy ra lỗi khi xử lý yêu cầu: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('barber.appointments.show', $appointment->id)
                ->with('error', 'Đã xảy ra lỗi khi xử lý yêu cầu: ' . $e->getMessage());
        }
    }

    /**
     * Tạo hóa đơn từ lịch hẹn
     */
    private function createInvoiceFromAppointment(Appointment $appointment)
    {
        // Kiểm tra xem đã có hóa đơn cho lịch hẹn này chưa
        if ($appointment->invoice) {
            Log::info("Sử dụng hóa đơn đã tồn tại cho lịch hẹn #{$appointment->id}: #{$appointment->invoice->id}");
            return $appointment->invoice;
        }

        // Tính tổng tiền từ các dịch vụ
        $subtotal = 0;
        foreach ($appointment->services as $service) {
            $subtotal += $service->pivot->price ?? $service->price;
        }

        // Tạo mã hóa đơn
        $invoiceCode = 'INV-' . time() . '-' . $appointment->id;

        // Đảm bảo barber_id không bị null
        $barberId = $appointment->barber_id;
        if (!$barberId) {
            // Nếu barber_id là null, sử dụng barber_id của người dùng hiện tại
            $barberId = Auth::user()->barber->id ?? null;
            Log::warning("Lịch hẹn #{$appointment->id} có barber_id là null, sử dụng barber_id của người dùng hiện tại: {$barberId}");
        }

        // Tạo hóa đơn mới
        $invoice = new Invoice([
            'invoice_code' => $invoiceCode,
            'appointment_id' => $appointment->id,
            'user_id' => $appointment->user_id,
            'barber_id' => $barberId,
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

        Log::info("Đã tạo hóa đơn mới #{$invoice->id} cho lịch hẹn #{$appointment->id}");

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

        // Đảm bảo hóa đơn được tải lại với các quan hệ
        $invoice = Invoice::with(['services', 'appointment', 'user'])->find($invoice->id);

        Log::info("Trả về hóa đơn #{$invoice->id} với ID: {$invoice->id}");

        return $invoice;
    }
}
