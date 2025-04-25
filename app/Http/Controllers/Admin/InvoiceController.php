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
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;
use App\Models\User as AppUser;
use App\Models\Staff;
use App\Models\Customer;
use App\Models\InvoiceDetail;
use Illuminate\Support\Facades\Log;

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

        $users = AppUser::where('role', 'customer')->get();
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
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'tax_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank_transfer,card',
            'payment_status' => 'required|in:pending,paid',
            'status' => 'required|in:pending,completed,canceled',
            'notes' => 'nullable|string',
            'service_ids' => 'nullable|array',
            'service_ids.*' => 'exists:services,id',
            'service_quantities' => 'nullable|array',
            'service_prices' => 'nullable|array',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
            'product_quantities' => 'nullable|array',
            'product_prices' => 'nullable|array',
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
            'discount' => $request->discount_amount ?? 0,
            'tax' => $request->tax_amount ?? 0,
            'total' => $request->total_amount,
            'total_amount' => $request->total_amount, // Để tương thích với cấu trúc cũ
            'payment_method' => $request->payment_method,
            'payment_status' => $request->payment_status,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        // Cập nhật trạng thái lịch hẹn
        $appointment->update([
            'status' => 'completed',
            'payment_status' => $request->payment_status
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

        // Thêm sản phẩm vào hóa đơn
        if ($request->has('product_ids')) {
            $productData = [];
            foreach ($request->product_ids as $index => $productId) {
                $quantity = $request->product_quantities[$index] ?? 1;
                $product = Product::find($productId);
                $price = $request->product_prices[$index] ?? $product->price;
                $subtotal = $price * $quantity;

                // Giảm số lượng sản phẩm trong kho
                if ($product->stock >= $quantity) {
                    $product->stock -= $quantity;
                    $product->save();
                }

                $productData[$productId] = [
                    'quantity' => $quantity,
                    'price' => $price,
                    'discount' => 0,
                    'subtotal' => $subtotal
                ];
            }

            $invoice->products()->attach($productData);
        }

        return redirect()->route('admin.invoices.show', $invoice->id)
            ->with('success', 'Hóa đơn đã được tạo thành công.');
    }

    public function show(Invoice $invoice)
    {
        // Debug: Ghi log invoice data trước khi load
        \Illuminate\Support\Facades\Log::info('Invoice before load:', [
            'id' => $invoice->id,
            'total' => $invoice->total,
            'services_count' => $invoice->services()->count(),
            'products_count' => $invoice->products()->count(),
            'service_ids' => $invoice->services()->pluck('service_id')->toArray(),
            'product_ids' => $invoice->products()->pluck('product_id')->toArray()
        ]);

        // Xóa cache để đảm bảo dữ liệu mới nhất
        \Illuminate\Support\Facades\DB::statement('SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO"');

        // Tải lại invoice từ cơ sở dữ liệu để đảm bảo dữ liệu mới nhất
        // Sử dụng query builder để tránh cache
        $invoiceData = \Illuminate\Support\Facades\DB::table('invoices')
            ->where('id', $invoice->id)
            ->first();

        if (!$invoiceData) {
            return redirect()->route('admin.invoices.index')
                ->with('error', 'Hóa đơn không tồn tại');
        }

        // Tải lại invoice từ model
        $invoice = Invoice::with(['appointment.user', 'appointment.barber.user', 'user', 'barber'])->findOrFail($invoice->id);

        // Tải lại các mối quan hệ một cách rõ ràng
        // Sử dụng query builder với DISTINCT để tránh trùng lập
        $services = \Illuminate\Support\Facades\DB::table('invoice_service')
            ->where('invoice_id', $invoice->id)
            ->join('services', 'invoice_service.service_id', '=', 'services.id')
            ->select(
                'services.id',
                'services.name',
                'services.price as service_price',
                'invoice_service.quantity',
                'invoice_service.price',
                'invoice_service.discount',
                'invoice_service.subtotal'
            )
            ->distinct()
            ->get();

        $products = \Illuminate\Support\Facades\DB::table('invoice_product')
            ->where('invoice_id', $invoice->id)
            ->join('products', 'invoice_product.product_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.name',
                'products.price as product_price',
                'invoice_product.quantity',
                'invoice_product.price',
                'invoice_product.discount',
                'invoice_product.subtotal'
            )
            ->distinct()
            ->get();

        // Xóa cache của các mối quan hệ
        try {
            \Illuminate\Support\Facades\DB::statement('ANALYZE TABLE invoice_service');
            \Illuminate\Support\Facades\DB::statement('ANALYZE TABLE invoice_product');
            \Illuminate\Support\Facades\DB::statement('ANALYZE TABLE services');
            \Illuminate\Support\Facades\DB::statement('ANALYZE TABLE products');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Could not analyze tables: ' . $e->getMessage());
        }

        // Debug: Ghi log dữ liệu tải trực tiếp
        \Illuminate\Support\Facades\Log::info('Services loaded directly:', [
            'count' => $services->count(),
            'data' => $services
        ]);

        \Illuminate\Support\Facades\Log::info('Products loaded directly:', [
            'count' => $products->count(),
            'data' => $products
        ]);

        // Tải lại các mối quan hệ dịch vụ và sản phẩm
        $invoice->load(['services', 'products']);

        // Đảm bảo rằng các mối quan hệ được tải lại
        $invoice->refresh();

        // Kiểm tra xem có sự khác biệt giữa dữ liệu trực tiếp và dữ liệu từ model
        $modelServicesCount = $invoice->services->count();
        $modelProductsCount = $invoice->products->count();
        $directServicesCount = $services->count();
        $directProductsCount = $products->count();

        \Illuminate\Support\Facades\Log::info('Comparison:', [
            'model_services_count' => $modelServicesCount,
            'direct_services_count' => $directServicesCount,
            'model_products_count' => $modelProductsCount,
            'direct_products_count' => $directProductsCount
        ]);

        // Debug: Ghi log invoice data sau khi load
        \Illuminate\Support\Facades\Log::info('Invoice after load:', [
            'id' => $invoice->id,
            'total' => $invoice->total,
            'services_count' => $invoice->services->count(),
            'products_count' => $invoice->products->count(),
            'service_ids' => $invoice->services->pluck('id')->toArray(),
            'product_ids' => $invoice->products->pluck('id')->toArray()
        ]);

        // Luôn sử dụng dữ liệu tải trực tiếp để đảm bảo tính chính xác
        $directServices = $services;
        $directProducts = $products;

        // Nếu có sự khác biệt giữa dữ liệu trực tiếp và dữ liệu từ model, ghi log cảnh báo
        if ($modelServicesCount != $directServicesCount || $modelProductsCount != $directProductsCount) {
            \Illuminate\Support\Facades\Log::warning('Mismatch in relationship counts, using direct data:', [
                'model_services_count' => $modelServicesCount,
                'direct_services_count' => $directServicesCount,
                'model_products_count' => $modelProductsCount,
                'direct_products_count' => $directProductsCount
            ]);
        }

        // Nếu không có dịch vụ nào trong cơ sở dữ liệu nhưng có dịch vụ trong lịch hẹn, thêm dịch vụ từ lịch hẹn
        if ($directServicesCount == 0 && $invoice->appointment && $invoice->appointment->services->count() > 0) {
            \Illuminate\Support\Facades\Log::info('No services found in invoice, adding from appointment');

            // Tạo một collection mới để chứa dịch vụ từ lịch hẹn
            $appointmentServices = collect();
            foreach ($invoice->appointment->services as $service) {
                $serviceObj = (object) [
                    'id' => $service->id,
                    'name' => $service->name,
                    'price' => $service->pivot->price,
                    'quantity' => 1,
                    'discount' => 0,
                    'subtotal' => $service->pivot->price
                ];
                $appointmentServices->push($serviceObj);
            }

            // Sử dụng dịch vụ từ lịch hẹn
            $directServices = $appointmentServices;
            \Illuminate\Support\Facades\Log::info('Services added from appointment:', [
                'count' => $directServices->count(),
                'data' => $directServices
            ]);
        }

        // Thông tin cửa hàng
        $shopInfo = [
            'shop_name' => 'Barber Shop',
            'shop_address' => '123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh',
            'shop_phone' => '0123456789',
            'shop_email' => 'hieu0559764554@gmail.com',
        ];

        return view('admin.invoices.show', compact('invoice', 'shopInfo', 'directServices', 'directProducts'));
    }

    public function edit(Invoice $invoice)
    {
        // Kiểm tra xem hóa đơn đã thanh toán chưa
        if ($invoice->payment_status == 'paid') {
            return redirect()->route('admin.invoices.show', $invoice->id)
                ->with('error', 'Hóa đơn đã thanh toán không thể chỉnh sửa. Bạn chỉ có thể xem chi tiết hóa đơn.');
        }

        // Load các mối quan hệ cần thiết
        $invoice->load([
            'appointment.user',
            'appointment.barber.user',
            'appointment.services',
            'services',
            'products',
            'user',
            'barber'
        ]);

        $users = AppUser::where('role', 'customer')->get();
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
        // Kiểm tra xem hóa đơn đã thanh toán chưa
        if ($invoice->payment_status == 'paid') {
            return redirect()->route('admin.invoices.show', $invoice->id)
                ->with('error', 'Hóa đơn đã thanh toán không thể chỉnh sửa. Bạn chỉ có thể xem chi tiết hóa đơn.');
        }

        // Sử dụng transaction để đảm bảo tính toàn vẹn của dữ liệu
        return \Illuminate\Support\Facades\DB::transaction(function() use ($request, $invoice) {
            // Debug: Ghi log request data và invoice trước khi cập nhật
            \Illuminate\Support\Facades\Log::info('Invoice Update Request Data:', $request->all());
            \Illuminate\Support\Facades\Log::info('Invoice Before Update:', [
                'id' => $invoice->id,
                'total' => $invoice->total,
                'services' => $invoice->services()->pluck('service_id')->toArray(),
                'products' => $invoice->products()->pluck('product_id')->toArray()
            ]);

            $request->validate([
                'user_id' => 'nullable|exists:users,id',
                'barber_id' => 'nullable|exists:barbers,id',
                'subtotal' => 'required|numeric|min:0',
                'discount_amount' => 'nullable|numeric|min:0',
                'tax_rate' => 'nullable|numeric|min:0|max:100',
                'tax_amount' => 'nullable|numeric|min:0',
                'total_amount' => 'required|numeric|min:0',
                'payment_method' => 'required|in:cash,bank_transfer,card',
                'payment_status' => 'required|in:pending,paid',
                'status' => 'required|in:pending,completed,canceled',
                'notes' => 'nullable|string',
                'service_ids' => 'nullable|array',
                'service_ids.*' => 'nullable|exists:services,id',
                'service_quantities' => 'nullable|array',
                'service_prices' => 'nullable|array',
                'product_ids' => 'nullable|array',
                'product_ids.*' => 'nullable|exists:products,id',
                'product_quantities' => 'nullable|array',
                'product_prices' => 'nullable|array',
            ]);

            // Cập nhật thông tin hóa đơn
            $updateData = [
                'user_id' => $request->user_id,
                'barber_id' => $request->barber_id,
                'subtotal' => $request->subtotal,
                'discount' => $request->discount_amount ?? 0,
                'tax' => $request->tax_amount ?? 0,
                'total' => $request->total_amount,
                'total_amount' => $request->total_amount, // Để tương thích với cấu trúc cũ
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_status,
                'status' => $request->status,
                'notes' => $request->notes,
            ];

            // Debug: Ghi log dữ liệu cập nhật
            \Illuminate\Support\Facades\Log::info('Invoice Update Data:', $updateData);

            // Cập nhật hóa đơn
            $invoice->update($updateData);

            // Cập nhật trạng thái thanh toán của lịch hẹn
            if ($invoice->appointment) {
                $invoice->appointment->update([
                    'payment_status' => $request->payment_status
                ]);
            }

            // Lưu trữ dịch vụ hiện tại để sử dụng sau
            $currentServices = [];
            foreach ($invoice->services as $service) {
                $currentServices[$service->id] = [
                    'quantity' => $service->pivot->quantity,
                    'price' => $service->pivot->price,
                    'discount' => $service->pivot->discount,
                    'subtotal' => $service->pivot->subtotal
                ];
            }

            // Kiểm tra xem có dịch vụ nào được gửi từ form không
            $hasServiceData = $request->has('service_ids') && !empty(array_filter($request->service_ids));

            // Chỉ xóa dịch vụ nếu có dữ liệu dịch vụ mới
            // Và chỉ khi người dùng đang cập nhật phần dịch vụ
            if ($hasServiceData) {
                // Xóa tất cả các dịch vụ hiện tại
                $invoice->services()->detach();
                \Illuminate\Support\Facades\Log::info('All services detached from invoice');

                // Cập nhật dịch vụ trong hóa đơn (nếu có)
                // Debug: Ghi log service data
                \Illuminate\Support\Facades\Log::info('Service IDs', ['data' => $request->service_ids ?? []]);
                \Illuminate\Support\Facades\Log::info('Service Prices', ['data' => $request->service_prices ?? []]);
                \Illuminate\Support\Facades\Log::info('Service Quantities', ['data' => $request->service_quantities ?? []]);

                $serviceData = [];
                // Kiểm tra xem service_ids có tồn tại không trước khi sử dụng foreach
                if ($request->has('service_ids') && is_array($request->service_ids)) {
                    foreach ($request->service_ids as $index => $serviceId) {
                        if (empty($serviceId)) continue; // Bỏ qua các dịch vụ không được chọn

                        $service = Service::find($serviceId);
                        if (!$service) continue; // Bỏ qua nếu không tìm thấy dịch vụ

                        $quantity = isset($request->service_quantities[$index]) ? (int)$request->service_quantities[$index] : 1;
                        $price = isset($request->service_prices[$index]) ? (float)$request->service_prices[$index] : $service->price;
                        $subtotal = $price * $quantity;

                        $serviceData[$serviceId] = [
                            'quantity' => $quantity,
                            'price' => $price,
                            'discount' => 0,
                            'subtotal' => $subtotal
                        ];

                        // Debug: Ghi log chi tiết dịch vụ
                        \Illuminate\Support\Facades\Log::info("Service {$service->name} added:", [
                            'id' => $serviceId,
                            'quantity' => $quantity,
                            'price' => $price,
                            'subtotal' => $subtotal
                        ]);
                    }
                }

                if (!empty($serviceData)) {
                    $invoice->services()->attach($serviceData);
                    \Illuminate\Support\Facades\Log::info('Services attached:', $serviceData);
                }
            }

            // Debug: Ghi log sau khi cập nhật dịch vụ
            \Illuminate\Support\Facades\Log::info('Services after update:', [
                'count' => $invoice->services()->count(),
                'ids' => $invoice->services()->pluck('service_id')->toArray()
            ]);

            // Lưu trữ sản phẩm hiện tại để sử dụng sau
            $currentProducts = $invoice->products;
            $currentProductData = [];
            foreach ($currentProducts as $product) {
                $currentProductData[$product->id] = [
                    'quantity' => $product->pivot->quantity,
                    'price' => $product->pivot->price,
                    'discount' => $product->pivot->discount,
                    'subtotal' => $product->pivot->subtotal
                ];
            }

            // Kiểm tra xem có sản phẩm nào được gửi từ form không
            $hasProductData = $request->has('product_ids') && !empty(array_filter($request->product_ids));

            // Kiểm tra xem form có được gửi với phần sản phẩm không
            $isProductSectionSubmitted = $request->has('product_section_submitted');

            // Nếu phần sản phẩm được gửi, luôn xóa sản phẩm hiện tại
            // Và chỉ thêm lại nếu có dữ liệu sản phẩm mới
            if ($isProductSectionSubmitted) {
                // Hoàn trả số lượng sản phẩm vào kho
                foreach ($currentProducts as $product) {
                    $product->stock += $product->pivot->quantity;
                    $product->save();

                    // Debug: Ghi log hoàn trả sản phẩm
                    \Illuminate\Support\Facades\Log::info("Product {$product->name} returned to stock:", [
                        'id' => $product->id,
                        'quantity' => $product->pivot->quantity,
                        'new_stock' => $product->stock
                    ]);
                }

                // Xóa tất cả các sản phẩm hiện tại
                $invoice->products()->detach();
                \Illuminate\Support\Facades\Log::info('All products detached from invoice');

                // Cập nhật sản phẩm trong hóa đơn (nếu có)
                $productData = [];

                // Nếu không có dữ liệu sản phẩm mới, đảm bảo rằng không có sản phẩm nào được thêm lại
                if (!$hasProductData) {
                    \Illuminate\Support\Facades\Log::info('Product section submitted but no products selected, keeping products detached');
                }
                // Debug: Ghi log product data
                \Illuminate\Support\Facades\Log::info('Product IDs', ['data' => $request->product_ids ?? []]);
                \Illuminate\Support\Facades\Log::info('Product Prices', ['data' => $request->product_prices ?? []]);
                \Illuminate\Support\Facades\Log::info('Product Quantities', ['data' => $request->product_quantities ?? []]);

                // Kiểm tra xem product_ids có tồn tại không trước khi sử dụng foreach
                if ($request->has('product_ids') && is_array($request->product_ids)) {
                    foreach ($request->product_ids as $index => $productId) {
                        if (empty($productId)) continue; // Bỏ qua các sản phẩm không được chọn

                        $product = Product::find($productId);
                        if (!$product) continue; // Bỏ qua nếu không tìm thấy sản phẩm

                        $quantity = isset($request->product_quantities[$index]) ? (int)$request->product_quantities[$index] : 1;
                        $price = isset($request->product_prices[$index]) ? (float)$request->product_prices[$index] : $product->price;
                        $subtotal = $price * $quantity;

                        // Giảm số lượng sản phẩm trong kho
                        if ($product->stock >= $quantity) {
                            $product->stock -= $quantity;
                            $product->save();

                            // Debug: Ghi log giảm sản phẩm
                            \Illuminate\Support\Facades\Log::info("Product {$product->name} deducted from stock:", [
                                'id' => $product->id,
                                'quantity' => $quantity,
                                'new_stock' => $product->stock
                            ]);
                        } else {
                            // Nếu không đủ số lượng, ghi log
                            \Illuminate\Support\Facades\Log::warning("Not enough stock for product {$product->name}:", [
                                'id' => $product->id,
                                'requested' => $quantity,
                                'available' => $product->stock
                            ]);
                        }

                        $productData[$productId] = [
                            'quantity' => $quantity,
                            'price' => $price,
                            'discount' => 0,
                            'subtotal' => $subtotal
                        ];

                        // Debug: Ghi log chi tiết sản phẩm
                        \Illuminate\Support\Facades\Log::info("Product {$product->name} added:", [
                            'id' => $productId,
                            'quantity' => $quantity,
                            'price' => $price,
                            'subtotal' => $subtotal
                        ]);
                    }
                }

                if (!empty($productData)) {
                    $invoice->products()->attach($productData);
                    \Illuminate\Support\Facades\Log::info('Products attached:', $productData);
                }
            }

            // Debug: Ghi log sau khi cập nhật sản phẩm
            \Illuminate\Support\Facades\Log::info('Products after update:', [
                'count' => $invoice->products()->count(),
                'ids' => $invoice->products()->pluck('product_id')->toArray()
            ]);

            // Đảm bảo rằng các mối quan hệ được tải lại
            $invoice->refresh();

            // Debug: Ghi log invoice sau khi cập nhật
            \Illuminate\Support\Facades\Log::info('Invoice After Update:', [
                'id' => $invoice->id,
                'total' => $invoice->total,
                'services' => $invoice->services()->pluck('service_id')->toArray(),
                'products' => $invoice->products()->pluck('product_id')->toArray()
            ]);

            // Tải lại invoice từ cơ sở dữ liệu để đảm bảo dữ liệu mới nhất
            // Xóa cache để đảm bảo dữ liệu mới nhất
            \Illuminate\Support\Facades\DB::statement('SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO"');

            // Sử dụng query builder để tránh cache
            $invoiceData = \Illuminate\Support\Facades\DB::table('invoices')
                ->where('id', $invoice->id)
                ->first();

            if (!$invoiceData) {
                return redirect()->route('admin.invoices.index')
                    ->with('error', 'Hóa đơn không tồn tại');
            }

            // Tải lại invoice từ model
            $invoice = Invoice::findOrFail($invoice->id);

            // Đảm bảo rằng các mối quan hệ được tải lại
            $invoice->refresh();

            return redirect()->route('admin.invoices.show', $invoice->id)
                ->with('success', 'Hóa đơn đã được cập nhật thành công.');
        });
    }

    public function destroy(Invoice $invoice)
    {
        // Kiểm tra xem hóa đơn đã thanh toán chưa
        if ($invoice->payment_status == 'paid') {
            return redirect()->route('admin.invoices.show', $invoice->id)
                ->with('error', 'Hóa đơn đã thanh toán không thể xóa. Bạn chỉ có thể xem chi tiết hóa đơn.');
        }

        // Hoàn trả số lượng sản phẩm vào kho
        $invoice->load('products');
        foreach ($invoice->products as $product) {
            $product->stock += $product->pivot->quantity;
            $product->save();
        }

        // Xóa các mối quan hệ
        $invoice->services()->detach();
        $invoice->products()->detach();

        // Xóa hóa đơn
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
            'products',
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
        $monthlyRevenue = \Illuminate\Support\Facades\DB::table('invoices')
            ->select(\Illuminate\Support\Facades\DB::raw('MONTH(created_at) as month'), \Illuminate\Support\Facades\DB::raw('SUM(total) as total'))
            ->whereYear('created_at', $currentYear)
            ->where('payment_status', 'paid')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Thống kê doanh thu theo phương thức thanh toán
        $paymentMethodStats = \Illuminate\Support\Facades\DB::table('invoices')
            ->select('payment_method', \Illuminate\Support\Facades\DB::raw('COUNT(*) as count'), \Illuminate\Support\Facades\DB::raw('SUM(total) as total'))
            ->where('payment_status', 'paid')
            ->groupBy('payment_method')
            ->get();

        // Thống kê doanh thu theo trạng thái
        $statusStats = \Illuminate\Support\Facades\DB::table('invoices')
            ->select('payment_status as status', \Illuminate\Support\Facades\DB::raw('COUNT(*) as count'), \Illuminate\Support\Facades\DB::raw('SUM(total) as total'))
            ->groupBy('payment_status')
            ->get();

        // Thống kê doanh thu theo dịch vụ
        $serviceStats = \Illuminate\Support\Facades\DB::table('invoice_service')
            ->join('services', 'invoice_service.service_id', '=', 'services.id')
            ->join('invoices', 'invoice_service.invoice_id', '=', 'invoices.id')
            ->select('services.name', \Illuminate\Support\Facades\DB::raw('COUNT(*) as count'), \Illuminate\Support\Facades\DB::raw('SUM(invoice_service.subtotal) as total'), \Illuminate\Support\Facades\DB::raw("'service' as type"))
            ->where('invoices.payment_status', 'paid')
            ->groupBy('services.name')
            ->orderBy('total', 'desc')
            ->limit(10);

        // Thống kê doanh thu theo sản phẩm
        $productStats = \Illuminate\Support\Facades\DB::table('invoice_product')
            ->join('products', 'invoice_product.product_id', '=', 'products.id')
            ->join('invoices', 'invoice_product.invoice_id', '=', 'invoices.id')
            ->select('products.name', \Illuminate\Support\Facades\DB::raw('COUNT(*) as count'), \Illuminate\Support\Facades\DB::raw('SUM(invoice_product.subtotal) as total'), \Illuminate\Support\Facades\DB::raw("'product' as type"))
            ->where('invoices.payment_status', 'paid')
            ->groupBy('products.name')
            ->orderBy('total', 'desc')
            ->limit(10);

        // Kết hợp thống kê dịch vụ và sản phẩm
        $itemStats = $serviceStats->union($productStats)->orderBy('total', 'desc')->limit(10)->get();

        // Thống kê doanh thu theo thợ cắt tóc
        $barberStats = \Illuminate\Support\Facades\DB::table('invoices')
            ->join('barbers', 'invoices.barber_id', '=', 'barbers.id')
            ->join('users', 'barbers.user_id', '=', 'users.id')
            ->select('users.name', \Illuminate\Support\Facades\DB::raw('COUNT(*) as count'), \Illuminate\Support\Facades\DB::raw('SUM(invoices.total) as total'))
            ->where('invoices.payment_status', 'paid')
            ->groupBy('users.name')
            ->orderBy('total', 'desc')
            ->get();

        return view('admin.invoices.statistics', compact(
            'monthlyRevenue',
            'paymentMethodStats',
            'statusStats',
            'itemStats',
            'barberStats'
        ));
    }

    /**
     * Cập nhật trạng thái hóa đơn
     */
    public function updateStatus(Request $request, Invoice $invoice)
    {
        // Sử dụng transaction để đảm bảo tính toàn vẹn của dữ liệu
        return \Illuminate\Support\Facades\DB::transaction(function() use ($request, $invoice) {
            $request->validate([
                'status' => 'required|in:pending,completed,canceled',
                'payment_status' => 'required|in:pending,paid',
            ]);

            // Debug: Ghi log trước khi cập nhật
            \Illuminate\Support\Facades\Log::info('Invoice Status Before Update:', [
                'id' => $invoice->id,
                'status' => $invoice->status,
                'payment_status' => $invoice->payment_status
            ]);

            $invoice->update([
                'status' => $request->status,
                'payment_status' => $request->payment_status,
            ]);

            // Cập nhật trạng thái thanh toán của lịch hẹn
            if ($invoice->appointment) {
                $invoice->appointment->update([
                    'payment_status' => $request->payment_status
                ]);
            }

            // Debug: Ghi log sau khi cập nhật
            \Illuminate\Support\Facades\Log::info('Invoice Status After Update:', [
                'id' => $invoice->id,
                'status' => $invoice->status,
                'payment_status' => $invoice->payment_status
            ]);

            return redirect()->back()->with('success', 'Trạng thái hóa đơn đã được cập nhật thành công.');
        });
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
            // Sử dụng cách gửi email đơn giản nhất
            $data = [
                'invoice' => $invoice,
                'shopInfo' => [
                    'shop_name' => config('shop.name'),
                    'shop_address' => config('shop.address'),
                    'shop_phone' => config('shop.phone'),
                    'shop_email' => config('shop.email'),
                ]
            ];

            \Illuminate\Support\Facades\Mail::send('emails.invoice', $data, function($message) use ($user, $invoice) {
                $message->to($user->email)
                        ->subject('Hóa đơn #' . $invoice->invoice_code);

                // Tạo PDF đính kèm
                $pdf = app('dompdf.wrapper');
                $pdf->getDomPDF()->set_option('defaultFont', 'DejaVu Sans');
                $pdf->getDomPDF()->set_option('isUnicode', true);
                $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);

                $html = view('admin.invoices.pdf', [
                    'invoice' => $invoice,
                    'shopInfo' => [
                        'shop_name' => config('shop.name'),
                        'shop_address' => config('shop.address'),
                        'shop_phone' => config('shop.phone'),
                        'shop_email' => config('shop.email'),
                    ]
                ])->render();

                $pdf->loadHTML($html);

                $message->attachData($pdf->output(), "Hoa-don-{$invoice->invoice_code}.pdf", [
                    'mime' => 'application/pdf',
                ]);
            });

            // Cập nhật trạng thái đã gửi email
            $invoice->update([
                'email_sent' => true,
                'email_sent_at' => now()
            ]);

            return redirect()->back()->with('success', 'Hóa đơn đã được gửi đến email ' . $user->email);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Lỗi gửi email hóa đơn: ' . $e->getMessage(), [
                'invoice_id' => $invoice->id,
                'user_email' => $user->email,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi gửi email: ' . $e->getMessage());
        }
    }

    /**
     * Hủy hóa đơn
     */
    public function cancelInvoice(Request $request, Invoice $invoice)
    {
        // Sử dụng transaction để đảm bảo tính toàn vẹn của dữ liệu
        return \Illuminate\Support\Facades\DB::transaction(function() use ($request, $invoice) {
            $request->validate([
                'cancel_reason' => 'required|string|max:500',
            ], [
                'cancel_reason.required' => 'Vui lòng nhập lý do hủy hóa đơn',
                'cancel_reason.max' => 'Lý do hủy hóa đơn không được vượt quá 500 ký tự',
            ]);

            // Ghi log trước khi cập nhật
            \Illuminate\Support\Facades\Log::info('Invoice Before Cancel:', [
                'id' => $invoice->id,
                'status' => $invoice->status,
                'payment_status' => $invoice->payment_status,
                'cancel_reason' => $request->cancel_reason
            ]);

            // Cập nhật trạng thái hóa đơn thành 'canceled'
            $invoice->update([
                'status' => 'canceled',
                'notes' => ($invoice->notes ? $invoice->notes . "\n\n" : '') . "Hủy hóa đơn: " . $request->cancel_reason . "\nNgày hủy: " . now()->format('d/m/Y H:i:s'),
            ]);

            // Nếu hóa đơn có liên kết với lịch hẹn, cập nhật trạng thái lịch hẹn
            if ($invoice->appointment) {
                // Chỉ cập nhật trạng thái lịch hẹn nếu nó đã hoàn thành
                if ($invoice->appointment->status == 'completed') {
                    $invoice->appointment->update([
                        'status' => 'confirmed', // Đưa về trạng thái đã xác nhận
                    ]);
                }
            }

            // Hoàn trả số lượng sản phẩm vào kho (nếu có)
            $invoice->load('products');
            foreach ($invoice->products as $product) {
                $product->stock += $product->pivot->quantity;
                $product->save();

                // Ghi log hoàn trả sản phẩm
                \Illuminate\Support\Facades\Log::info("Product {$product->name} returned to stock:", [
                    'id' => $product->id,
                    'quantity' => $product->pivot->quantity,
                    'new_stock' => $product->stock
                ]);
            }

            // Ghi log sau khi cập nhật
            \Illuminate\Support\Facades\Log::info('Invoice After Cancel:', [
                'id' => $invoice->id,
                'status' => $invoice->status,
                'payment_status' => $invoice->payment_status
            ]);

            return redirect()->route('admin.invoices.show', $invoice->id)
                ->with('success', 'Hóa đơn đã được hủy thành công.');
        });
    }
}