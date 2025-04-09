<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $date = $request->input('date');
        
        $query = Invoice::with(['appointment.user', 'appointment.barber.user', 'appointment.services']);
        
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($date) {
            $query->whereDate('created_at', $date);
        }
        
        $invoices = $query->latest()->paginate(10);
        
        return view('admin.invoices.index', compact('invoices', 'status', 'date'));
    }
    
    public function create()
    {
        $appointments = Appointment::with(['user', 'barber.user', 'services'])
            ->where('status', 'completed')
            ->whereDoesntHave('invoice')
            ->get();
        
        return view('admin.invoices.create', compact('appointments'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'amount' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,card,transfer',
            'status' => 'required|in:paid,unpaid,partial',
            'note' => 'nullable|string',
        ]);
        
        $appointment = Appointment::findOrFail($request->appointment_id);
        
        if ($appointment->invoice) {
            return redirect()->back()->with('error', 'Hóa đơn cho lịch hẹn này đã tồn tại.');
        }
        
        // Tính tổng tiền
        $subtotal = $request->amount;
        $discount = $request->discount ?? 0;
        $tax = $request->tax ?? 0;
        $total = $subtotal - $discount + $tax;
        
        // Tạo hóa đơn
        $invoice = Invoice::create([
            'appointment_id' => $request->appointment_id,
            'invoice_number' => 'INV-' . time(),
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'total' => $total,
            'payment_method' => $request->payment_method,
            'status' => $request->status,
            'note' => $request->note,
        ]);
        
        return redirect()->route('admin.invoices.show', $invoice->id)
            ->with('success', 'Hóa đơn đã được tạo thành công.');
    }
    
    public function show(Invoice $invoice)
    {
        $invoice->load(['appointment.user', 'appointment.barber.user', 'appointment.services']);
        return view('admin.invoices.show', compact('invoice'));
    }
    
    public function edit(Invoice $invoice)
    {
        $invoice->load(['appointment.user', 'appointment.barber.user', 'appointment.services']);
        return view('admin.invoices.edit', compact('invoice'));
    }
    
    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,card,transfer',
            'status' => 'required|in:paid,unpaid,partial',
            'note' => 'nullable|string',
        ]);
        
        // Tính tổng tiền
        $subtotal = $request->amount;
        $discount = $request->discount ?? 0;
        $tax = $request->tax ?? 0;
        $total = $subtotal - $discount + $tax;
        
        $invoice->update([
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'total' => $total,
            'payment_method' => $request->payment_method,
            'status' => $request->status,
            'note' => $request->note,
        ]);
        
        return redirect()->route('admin.invoices.show', $invoice->id)
            ->with('success', 'Hóa đơn đã được cập nhật thành công.');
    }
    
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        
        return redirect()->route('admin.invoices.index')
            ->with('success', 'Hóa đơn đã được xóa thành công.');
    }
    
    public function print(Invoice $invoice)
    {
        $invoice->load(['appointment.user', 'appointment.barber.user', 'appointment.services']);
        return view('admin.invoices.print', compact('invoice'));
    }
    
    public function statistics()
    {
        // Thống kê doanh thu theo tháng trong năm hiện tại
        $currentYear = Carbon::now()->year;
        $monthlyRevenue = DB::table('invoices')
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(total) as total'))
            ->whereYear('created_at', $currentYear)
            ->where('status', 'paid')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        // Thống kê doanh thu theo phương thức thanh toán
        $paymentMethodStats = DB::table('invoices')
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->where('status', 'paid')
            ->groupBy('payment_method')
            ->get();
        
        // Thống kê doanh thu theo trạng thái
        $statusStats = DB::table('invoices')
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('status')
            ->get();
        
        return view('admin.invoices.statistics', compact('monthlyRevenue', 'paymentMethodStats', 'statusStats'));
    }
} 