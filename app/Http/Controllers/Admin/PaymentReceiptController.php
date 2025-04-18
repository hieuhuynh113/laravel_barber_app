<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentReceipt;
use App\Models\Appointment;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class PaymentReceiptController extends Controller
{
    /**
     * Hiển thị danh sách biên lai thanh toán
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $receipts = PaymentReceipt::with(['appointment.barber.user', 'appointment.services'])
            ->latest()
            ->paginate(10);

        return view('admin.payment_receipts.index', compact('receipts'));
    }

    /**
     * Hiển thị chi tiết biên lai thanh toán
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $receipt = PaymentReceipt::with(['appointment.barber.user', 'appointment.services'])
            ->findOrFail($id);

        return view('admin.payment_receipts.show', compact('receipt'));
    }

    /**
     * Cập nhật trạng thái biên lai thanh toán
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $receipt = PaymentReceipt::findOrFail($id);
        $appointment = $receipt->appointment;

        $receipt->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
        ]);

        // Nếu biên lai được chấp nhận, cập nhật trạng thái thanh toán của lịch hẹn
        if ($request->status === 'approved') {
            $appointment->update([
                'payment_status' => 'paid',
                'status' => 'confirmed',
            ]);

            // Gửi email xác nhận thanh toán cho khách hàng
            try {
                Mail::to($appointment->email)
                    ->send(new \App\Mail\PaymentConfirmation($appointment, $receipt));
            } catch (\Exception $e) {
                \Log::error("Không thể gửi email xác nhận thanh toán: " . $e->getMessage());
            }
        } else {
            // Nếu biên lai bị từ chối, gửi email thông báo cho khách hàng
            try {
                Mail::to($appointment->email)
                    ->send(new \App\Mail\PaymentRejected($appointment, $receipt));
            } catch (\Exception $e) {
                \Log::error("Không thể gửi email từ chối thanh toán: " . $e->getMessage());
            }
        }

        return redirect()->route('admin.payment-receipts.show', $receipt->id)
            ->with('success', 'Trạng thái biên lai thanh toán đã được cập nhật thành công.');
    }

    /**
     * Xóa biên lai thanh toán
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $receipt = PaymentReceipt::findOrFail($id);

        // Xóa file biên lai
        if (Storage::disk('public')->exists($receipt->file_path)) {
            Storage::disk('public')->delete($receipt->file_path);
        }

        $receipt->delete();

        return redirect()->route('admin.payment-receipts.index')
            ->with('success', 'Biên lai thanh toán đã được xóa thành công.');
    }
}
