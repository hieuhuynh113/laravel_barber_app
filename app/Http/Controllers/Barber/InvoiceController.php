<?php

namespace App\Http\Controllers\Barber;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;

class InvoiceController extends Controller
{
    /**
     * Kiểm tra quyền truy cập hóa đơn và cập nhật barber_id nếu cần
     *
     * @param Invoice $invoice Hóa đơn cần kiểm tra
     * @param string $action Hành động đang thực hiện (xem, in, v.v.)
     * @return mixed|null Trả về response lỗi nếu không có quyền, null nếu có quyền
     */
    private function checkInvoiceAccess(Invoice $invoice, $action = 'xem')
    {
        // Tải các quan hệ cần thiết nếu chưa được tải
        if (!$invoice->relationLoaded('appointment')) {
            $invoice->load(['services', 'products', 'appointment', 'user']);
        }

        // Kiểm tra xem hóa đơn có thuộc về thợ cắt tóc hiện tại không
        $currentBarberId = Auth::user()->barber->id;

        // Nếu barber_id là null, kiểm tra thông qua appointment
        if ($invoice->barber_id === null && $invoice->appointment && $invoice->appointment->barber_id == $currentBarberId) {
            // Cập nhật barber_id cho hóa đơn
            $invoice->barber_id = $currentBarberId;
            $invoice->save();

            \Log::info("Đã cập nhật barber_id cho hóa đơn #{$invoice->id} từ null thành {$currentBarberId}");
            return null; // Có quyền truy cập
        }
        // Nếu không phải hóa đơn của barber hiện tại
        else if ($invoice->barber_id != $currentBarberId && (!$invoice->appointment || $invoice->appointment->barber_id != $currentBarberId)) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['error' => "Bạn không có quyền {$action} hóa đơn này."], 403);
            }

            return redirect()->route('barber.appointments.index')
                ->with('error', "Bạn không có quyền {$action} hóa đơn này.");
        }

        return null; // Có quyền truy cập
    }

    /**
     * Hiển thị chi tiết hóa đơn
     */
    public function show(Invoice $invoice)
    {
        // Kiểm tra quyền truy cập
        $accessCheck = $this->checkInvoiceAccess($invoice, 'xem');
        if ($accessCheck) {
            return $accessCheck;
        }

        return view('barber.invoices.show', compact('invoice'));
    }

    /**
     * In hóa đơn
     */
    public function print(Invoice $invoice)
    {
        // Kiểm tra quyền truy cập
        $accessCheck = $this->checkInvoiceAccess($invoice, 'in');
        if ($accessCheck) {
            return $accessCheck;
        }

        $pdf = PDF::loadView('barber.invoices.print', compact('invoice'));
        return $pdf->stream('invoice-' . $invoice->invoice_number . '.pdf');
    }

    /**
     * Trả về dữ liệu hóa đơn dưới dạng JSON
     */
    public function getInvoiceData(Invoice $invoice)
    {
        // Kiểm tra quyền truy cập
        $accessCheck = $this->checkInvoiceAccess($invoice, 'xem');
        if ($accessCheck) {
            return $accessCheck;
        }

        // Trả về dữ liệu hóa đơn dưới dạng JSON
        return response()->json([
            'id' => $invoice->id,
            'invoice_code' => $invoice->invoice_code,
            'invoice_number' => $invoice->invoice_number,
            'customer_name' => $invoice->user->name ?? $invoice->appointment->customer_name ?? 'Không xác định',
            'customer_email' => $invoice->user->email ?? $invoice->appointment->email ?? 'Không xác định',
            'customer_phone' => $invoice->user->phone ?? $invoice->appointment->phone ?? 'Không xác định',
            'created_at' => $invoice->created_at->format('d/m/Y H:i'),
            'payment_status' => $invoice->payment_status,
            'payment_method' => $invoice->payment_method,
            'subtotal' => $invoice->subtotal,
            'subtotal_formatted' => number_format($invoice->subtotal) . ' VNĐ',
            'discount' => $invoice->discount,
            'discount_formatted' => number_format($invoice->discount) . ' VNĐ',
            'tax' => $invoice->tax,
            'tax_formatted' => number_format($invoice->tax) . ' VNĐ',
            'total' => $invoice->total,
            'total_formatted' => number_format($invoice->total) . ' VNĐ',
            'notes' => $invoice->notes,
            'services' => $invoice->services->map(function($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'quantity' => $service->pivot->quantity,
                    'price' => $service->pivot->price,
                    'price_formatted' => number_format($service->pivot->price) . ' VNĐ',
                    'subtotal' => $service->pivot->subtotal,
                    'subtotal_formatted' => number_format($service->pivot->subtotal) . ' VNĐ',
                ];
            }),
            'products' => $invoice->products->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'quantity' => $product->pivot->quantity,
                    'price' => $product->pivot->price,
                    'price_formatted' => number_format($product->pivot->price) . ' VNĐ',
                    'subtotal' => $product->pivot->subtotal,
                    'subtotal_formatted' => number_format($product->pivot->subtotal) . ' VNĐ',
                ];
            }),
            'appointment' => $invoice->appointment ? [
                'id' => $invoice->appointment->id,
                'booking_code' => $invoice->appointment->booking_code,
                'appointment_date' => $invoice->appointment->appointment_date->format('d/m/Y'),
                'time_slot' => $invoice->appointment->time_slot,
                'status' => $invoice->appointment->status,
            ] : null,
            'view_url' => route('barber.invoices.show', $invoice->id),
            'print_url' => route('barber.invoices.print', $invoice->id),
        ]);
    }
}
