<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
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
        
        $users = User::where('role', 'customer')->get();
        $products = Product::where('status', 1)->get();
        $services = Service::where('status', 1)->get();
        
        return view('admin.invoices.create', compact('appointments', 'users', 'products', 'services'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank_transfer',
            'payment_status' => 'required|in:pending,paid',
            'notes' => 'nullable|string',
        ]);
        
        $appointment = Appointment::findOrFail($request->appointment_id);
        
        if ($appointment->invoice) {
            return redirect()->back()->with('error', 'Hóa đơn cho lịch hẹn này đã tồn tại.');
        }
        
        // Tạo hóa đơn
        $invoice = Invoice::create([
            'appointment_id' => $request->appointment_id,
            'invoice_number' => 'INV-' . time(),
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'payment_status' => $request->payment_status,
            'notes' => $request->notes,
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
        $users = User::where('role', 'customer')->get();
        $products = Product::where('status', 1)->get();
        $services = Service::where('status', 1)->get();
        
        return view('admin.invoices.edit', compact('invoice', 'users', 'products', 'services'));
    }
    
    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank_transfer',
            'payment_status' => 'required|in:pending,paid',
            'notes' => 'nullable|string',
        ]);
        
        $invoice->update([
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'payment_status' => $request->payment_status,
            'notes' => $request->notes,
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
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(amount) as total'))
            ->whereYear('created_at', $currentYear)
            ->where('payment_status', 'paid')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        // Thống kê doanh thu theo phương thức thanh toán
        $paymentMethodStats = DB::table('invoices')
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->where('payment_status', 'paid')
            ->groupBy('payment_method')
            ->get();
        
        // Thống kê doanh thu theo trạng thái
        $statusStats = DB::table('invoices')
            ->select('payment_status as status', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('payment_status')
            ->get();
        
        return view('admin.invoices.statistics', compact('monthlyRevenue', 'paymentMethodStats', 'statusStats'));
    }
} 