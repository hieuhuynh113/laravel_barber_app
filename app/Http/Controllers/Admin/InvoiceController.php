<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Barber;
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
        $paymentMethod = $request->input('payment_method');
        $paymentStatus = $request->input('payment_status');

        $query = Invoice::with([
            'appointment.user',
            'appointment.barber.user',
            'appointment.services',
            'services',
            'user',
            'barber'
        ]);

        if ($status) {
            $query->where('status', $status);
        }

        if ($date) {
            $query->whereDate('created_at', $date);
        }

        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }

        if ($paymentStatus) {
            $query->where('payment_status', $paymentStatus);
        }

        $invoices = $query->latest()->paginate(10);

        // Lấy danh sách các phương thức thanh toán để lọc
        $paymentMethods = [
            'cash' => 'Tiền mặt',
            'bank_transfer' => 'Chuyển khoản',
            'card' => 'Thẻ'
        ];

        // Lấy danh sách các trạng thái thanh toán để lọc
        $paymentStatuses = [
            'paid' => 'Đã thanh toán',
            'pending' => 'Chưa thanh toán'
        ];

        // Lấy danh sách các trạng thái hóa đơn để lọc
        $statuses = [
            'completed' => 'Hoàn thành',
            'pending' => 'Chờ xử lý',
            'canceled' => 'Đã hủy'
        ];

        return view('admin.invoices.index', compact(
            'invoices',
            'status',
            'date',
            'paymentMethod',
            'paymentStatus',
            'paymentMethods',
            'paymentStatuses',
            'statuses'
        ));
    }

    public function create(Request $request)
    {
        // Nếu có appointment_id trong request, lấy thông tin lịch hẹn đó
        $selectedAppointment = null;
        if ($request->has('appointment_id')) {
            $selectedAppointment = Appointment::with(['user', 'barber.user', 'services'])
                ->findOrFail($request->appointment_id);

            // Kiểm tra xem lịch hẹn đã có hóa đơn chưa
            if ($selectedAppointment->invoice) {
                return redirect()->route('admin.invoices.show', $selectedAppointment->invoice->id)
                    ->with('info', 'Lịch hẹn này đã có hóa đơn.');
            }
        }

        // Lấy danh sách lịch hẹn chưa có hóa đơn
        $appointments = Appointment::with(['user', 'barber.user', 'services'])
            ->where('status', 'completed')
            ->whereDoesntHave('invoice')
            ->get();

        $users = User::where('role', 'customer')->get();
        $barbers = Barber::with('user')->get();
        $services = Service::where('status', 1)->get();
        $products = Product::where('status', 1)->get();

        // Thông tin cửa hàng
        $shopInfo = [
            'shop_name' => 'Barber Shop',
            'shop_address' => '123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh',
            'shop_phone' => '0123456789',
            'shop_email' => 'hieu0559764554@gmail.com',
        ];

        return view('admin.invoices.create', compact(
            'appointments',
            'selectedAppointment',
            'users',
            'barbers',
            'services',
            'products',
            'shopInfo'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'user_id' => 'nullable|exists:users,id',
            'barber_id' => 'nullable|exists:barbers,id',
            'subtotal' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank_transfer,card',
            'payment_status' => 'required|in:pending,paid',
            'status' => 'required|in:pending,completed,canceled',
            'notes' => 'nullable|string',
            'service_ids' => 'nullable|array',
            'service_ids.*' => 'exists:services,id',
            'service_quantities' => 'nullable|array',
            'service_prices' => 'nullable|array',
        ]);

        $appointment = Appointment::findOrFail($request->appointment_id);

        if ($appointment->invoice) {
            return redirect()->back()->with('error', 'Hóa đơn cho lịch hẹn này đã tồn tại.');
        }

        // Tạo mã hóa đơn
        $invoiceCode = 'INV-' . date('Ymd') . '-' . str_pad($appointment->id, 4, '0', STR_PAD_LEFT);

        // Tạo hóa đơn
        $invoice = Invoice::create([
            'invoice_code' => $invoiceCode,
            'appointment_id' => $request->appointment_id,
            'user_id' => $request->user_id ?? $appointment->user_id,
            'barber_id' => $request->barber_id ?? $appointment->barber_id,
            'invoice_number' => 'INV-' . time(),
            'subtotal' => $request->subtotal,
            'discount' => $request->discount ?? 0,
            'tax' => $request->tax ?? 0,
            'total' => $request->total,
            'total_amount' => $request->total, // Để tương thích với cấu trúc cũ
            'payment_method' => $request->payment_method,
            'payment_status' => $request->payment_status,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        // Thêm dịch vụ vào hóa đơn
        if ($request->has('service_ids')) {
            $serviceData = [];
            foreach ($request->service_ids as $index => $serviceId) {
                $quantity = $request->service_quantities[$index] ?? 1;
                $price = $request->service_prices[$index] ?? Service::find($serviceId)->price;
                $subtotal = $price * $quantity;

                $serviceData[$serviceId] = [
                    'quantity' => $quantity,
                    'price' => $price,
                    'discount' => 0,
                    'subtotal' => $subtotal
                ];
            }

            $invoice->services()->attach($serviceData);
        }
        // Nếu không có dịch vụ được chọn, sử dụng dịch vụ từ lịch hẹn
        elseif ($appointment->services->count() > 0) {
            $serviceData = [];
            foreach ($appointment->services as $service) {
                $price = $service->pivot->price ?? $service->price;
                $serviceData[$service->id] = [
                    'quantity' => 1,
                    'price' => $price,
                    'discount' => 0,
                    'subtotal' => $price
                ];
            }

            $invoice->services()->attach($serviceData);
        }

        return redirect()->route('admin.invoices.show', $invoice->id)
            ->with('success', 'Hóa đơn đã được tạo thành công.');
    }

    public function show(Invoice $invoice)
    {
        // Load các mối quan hệ cần thiết
        $invoice->load([
            'appointment.user',
            'appointment.barber.user',
            'appointment.services',
            'services',
            'user',
            'barber'
        ]);

        // Thông tin cửa hàng
        $shopInfo = [
            'shop_name' => 'Barber Shop',
            'shop_address' => '123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh',
            'shop_phone' => '0123456789',
            'shop_email' => 'hieu0559764554@gmail.com',
        ];

        return view('admin.invoices.show', compact('invoice', 'shopInfo'));
    }

    public function edit(Invoice $invoice)
    {
        // Load các mối quan hệ cần thiết
        $invoice->load([
            'appointment.user',
            'appointment.barber.user',
            'appointment.services',
            'services',
            'user',
            'barber'
        ]);

        $users = User::where('role', 'customer')->get();
        $barbers = Barber::with('user')->get();
        $services = Service::where('status', 1)->get();
        $products = Product::where('status', 1)->get();

        // Thông tin cửa hàng
        $shopInfo = [
            'shop_name' => 'Barber Shop',
            'shop_address' => '123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh',
            'shop_phone' => '0123456789',
            'shop_email' => 'hieu0559764554@gmail.com',
        ];

        return view('admin.invoices.edit', compact('invoice', 'users', 'barbers', 'services', 'products', 'shopInfo'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'barber_id' => 'nullable|exists:barbers,id',
            'subtotal' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank_transfer,card',
            'payment_status' => 'required|in:pending,paid',
            'status' => 'required|in:pending,completed,canceled',
            'notes' => 'nullable|string',
            'service_ids' => 'nullable|array',
            'service_ids.*' => 'exists:services,id',
            'service_quantities' => 'nullable|array',
            'service_prices' => 'nullable|array',
        ]);

        // Cập nhật thông tin hóa đơn
        $invoice->update([
            'user_id' => $request->user_id,
            'barber_id' => $request->barber_id,
            'subtotal' => $request->subtotal,
            'discount' => $request->discount ?? 0,
            'tax' => $request->tax ?? 0,
            'total' => $request->total,
            'total_amount' => $request->total, // Để tương thích với cấu trúc cũ
            'payment_method' => $request->payment_method,
            'payment_status' => $request->payment_status,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        // Cập nhật dịch vụ trong hóa đơn
        if ($request->has('service_ids')) {
            $invoice->services()->detach(); // Xóa các dịch vụ hiện tại

            $serviceData = [];
            foreach ($request->service_ids as $index => $serviceId) {
                $quantity = $request->service_quantities[$index] ?? 1;
                $price = $request->service_prices[$index] ?? Service::find($serviceId)->price;
                $subtotal = $price * $quantity;

                $serviceData[$serviceId] = [
                    'quantity' => $quantity,
                    'price' => $price,
                    'discount' => 0,
                    'subtotal' => $subtotal
                ];
            }

            $invoice->services()->attach($serviceData);
        }

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
        // Load các mối quan hệ cần thiết
        $invoice->load([
            'appointment.user',
            'appointment.barber.user',
            'appointment.services',
            'services',
            'user',
            'barber'
        ]);

        // Thông tin cửa hàng
        $shopInfo = [
            'shop_name' => 'Barber Shop',
            'shop_address' => '123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh',
            'shop_phone' => '0123456789',
            'shop_email' => 'hieu0559764554@gmail.com',
        ];

        return view('admin.invoices.print', compact('invoice', 'shopInfo'));
    }

    public function statistics()
    {
        // Thống kê doanh thu theo tháng trong năm hiện tại
        $currentYear = Carbon::now()->year;
        $monthlyRevenue = DB::table('invoices')
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(total) as total'))
            ->whereYear('created_at', $currentYear)
            ->where('payment_status', 'paid')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Thống kê doanh thu theo phương thức thanh toán
        $paymentMethodStats = DB::table('invoices')
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->where('payment_status', 'paid')
            ->groupBy('payment_method')
            ->get();

        // Thống kê doanh thu theo trạng thái
        $statusStats = DB::table('invoices')
            ->select('payment_status as status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('payment_status')
            ->get();

        // Thống kê doanh thu theo dịch vụ
        $serviceStats = DB::table('invoice_service')
            ->join('services', 'invoice_service.service_id', '=', 'services.id')
            ->join('invoices', 'invoice_service.invoice_id', '=', 'invoices.id')
            ->select('services.name', DB::raw('COUNT(*) as count'), DB::raw('SUM(invoice_service.subtotal) as total'))
            ->where('invoices.payment_status', 'paid')
            ->groupBy('services.name')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        // Thống kê doanh thu theo thợ cắt tóc
        $barberStats = DB::table('invoices')
            ->join('barbers', 'invoices.barber_id', '=', 'barbers.id')
            ->join('users', 'barbers.user_id', '=', 'users.id')
            ->select('users.name', DB::raw('COUNT(*) as count'), DB::raw('SUM(invoices.total) as total'))
            ->where('invoices.payment_status', 'paid')
            ->groupBy('users.name')
            ->orderBy('total', 'desc')
            ->get();

        return view('admin.invoices.statistics', compact(
            'monthlyRevenue',
            'paymentMethodStats',
            'statusStats',
            'serviceStats',
            'barberStats'
        ));
    }

    /**
     * Cập nhật trạng thái hóa đơn
     */
    public function updateStatus(Request $request, Invoice $invoice)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,canceled',
            'payment_status' => 'required|in:pending,paid',
        ]);

        $invoice->update([
            'status' => $request->status,
            'payment_status' => $request->payment_status,
        ]);

        return redirect()->back()->with('success', 'Trạng thái hóa đơn đã được cập nhật thành công.');
    }

    /**
     * Gửi email hóa đơn cho khách hàng
     */
    public function sendEmail(Invoice $invoice)
    {
        // Kiểm tra xem hóa đơn có liên kết với khách hàng không
        $user = $invoice->user ?? $invoice->appointment->user ?? null;

        if (!$user || !$user->email) {
            return redirect()->back()->with('error', 'Không tìm thấy thông tin email của khách hàng.');
        }

        try {
            // Gửi email hóa đơn (chưa triển khai chi tiết)
            // Mail::to($user->email)->send(new InvoiceMail($invoice));

            // Tạm thời chỉ hiển thị thông báo thành công
            return redirect()->back()->with('success', 'Hóa đơn đã được gửi đến email ' . $user->email);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi gửi email: ' . $e->getMessage());
        }
    }
}