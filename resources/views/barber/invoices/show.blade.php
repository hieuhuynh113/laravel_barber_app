@extends('layouts.app')

@section('title', 'Chi tiết hóa đơn')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/barber-dashboard.css') }}">
<style>
    .invoice-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        margin-bottom: 1.5rem;
    }
    
    .invoice-card .card-header {
        background-color: #2c3e50;
        color: #fff;
        border-radius: 10px 10px 0 0;
        padding: 1rem 1.5rem;
    }
    
    .invoice-card .card-body {
        padding: 1.5rem;
    }
    
    .invoice-info {
        margin-bottom: 2rem;
    }
    
    .invoice-info-item {
        margin-bottom: 0.5rem;
    }
    
    .invoice-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .invoice-table th, .invoice-table td {
        padding: 0.75rem;
        border-bottom: 1px solid #e3e6f0;
    }
    
    .invoice-table th {
        background-color: #f8f9fc;
        font-weight: 600;
        text-align: left;
    }
    
    .invoice-table tfoot td {
        font-weight: 600;
    }
    
    .invoice-total {
        background-color: #f8f9fc;
        padding: 1rem;
        border-radius: 5px;
        margin-top: 1rem;
    }
    
    .invoice-total-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }
    
    .invoice-total-row:last-child {
        margin-bottom: 0;
        padding-top: 0.5rem;
        border-top: 1px solid #e3e6f0;
        font-weight: 700;
    }
    
    .payment-badge {
        padding: 0.35rem 0.5rem;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .payment-pending {
        background-color: #fff3cd;
        color: #856404;
    }
    
    .payment-paid {
        background-color: #d4edda;
        color: #155724;
    }
    
    .btn-print {
        background-color: #3498db;
        color: #fff;
        border: none;
        transition: all 0.3s;
    }
    
    .btn-print:hover {
        background-color: #2980b9;
        color: #fff;
        transform: translateY(-2px);
    }
</style>
@endsection

@section('content')
<div class="container dashboard-container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="dashboard-title">Chi tiết hóa đơn</h1>
                    <p class="dashboard-subtitle">Mã hóa đơn: #{{ $invoice->invoice_code }}</p>
                </div>
                <div>
                    <a href="{{ route('barber.appointments.index') }}" class="btn btn-outline-primary me-2">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
                    </a>
                    <a href="{{ route('barber.invoices.print', $invoice->id) }}" class="btn btn-print" target="_blank">
                        <i class="fas fa-print me-2"></i>In hóa đơn
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <!-- Thông tin hóa đơn -->
                    <div class="invoice-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Thông tin hóa đơn</h5>
                            <span class="payment-badge {{ $invoice->payment_status == 'paid' ? 'payment-paid' : 'payment-pending' }}">
                                @if($invoice->payment_status == 'paid')
                                    <i class="fas fa-check-circle me-1"></i>Đã thanh toán
                                @else
                                    <i class="fas fa-clock me-1"></i>Chưa thanh toán
                                @endif
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row invoice-info">
                                <div class="col-md-6">
                                    <div class="invoice-info-item">
                                        <strong>Mã hóa đơn:</strong> {{ $invoice->invoice_code }}
                                    </div>
                                    <div class="invoice-info-item">
                                        <strong>Ngày tạo:</strong> {{ $invoice->created_at->format('d/m/Y H:i') }}
                                    </div>
                                    <div class="invoice-info-item">
                                        <strong>Phương thức thanh toán:</strong>
                                        @if($invoice->payment_method == 'cash')
                                            <span><i class="fas fa-money-bill-wave me-1"></i>Tiền mặt</span>
                                        @elseif($invoice->payment_method == 'bank_transfer')
                                            <span><i class="fas fa-university me-1"></i>Chuyển khoản</span>
                                        @else
                                            <span>{{ $invoice->payment_method }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="invoice-info-item">
                                        <strong>Khách hàng:</strong> {{ $invoice->user->name ?? $invoice->appointment->customer_name ?? 'Không xác định' }}
                                    </div>
                                    <div class="invoice-info-item">
                                        <strong>Email:</strong> {{ $invoice->user->email ?? $invoice->appointment->email ?? 'Không xác định' }}
                                    </div>
                                    <div class="invoice-info-item">
                                        <strong>Số điện thoại:</strong> {{ $invoice->user->phone ?? $invoice->appointment->phone ?? 'Không xác định' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Danh sách dịch vụ -->
                            <h6 class="mb-3 font-weight-bold"><i class="fas fa-cut me-2"></i>Dịch vụ</h6>
                            <div class="table-responsive mb-4">
                                <table class="invoice-table">
                                    <thead>
                                        <tr>
                                            <th>Dịch vụ</th>
                                            <th class="text-center">Số lượng</th>
                                            <th class="text-end">Đơn giá</th>
                                            <th class="text-end">Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoice->services as $service)
                                            <tr>
                                                <td>{{ $service->name }}</td>
                                                <td class="text-center">{{ $service->pivot->quantity }}</td>
                                                <td class="text-end">{{ number_format($service->pivot->price) }} VNĐ</td>
                                                <td class="text-end">{{ number_format($service->pivot->subtotal) }} VNĐ</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Danh sách sản phẩm (nếu có) -->
                            @if($invoice->products->count() > 0)
                                <h6 class="mb-3 font-weight-bold"><i class="fas fa-shopping-bag me-2"></i>Sản phẩm</h6>
                                <div class="table-responsive mb-4">
                                    <table class="invoice-table">
                                        <thead>
                                            <tr>
                                                <th>Sản phẩm</th>
                                                <th class="text-center">Số lượng</th>
                                                <th class="text-end">Đơn giá</th>
                                                <th class="text-end">Thành tiền</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($invoice->products as $product)
                                                <tr>
                                                    <td>{{ $product->name }}</td>
                                                    <td class="text-center">{{ $product->pivot->quantity }}</td>
                                                    <td class="text-end">{{ number_format($product->pivot->price) }} VNĐ</td>
                                                    <td class="text-end">{{ number_format($product->pivot->subtotal) }} VNĐ</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                            <!-- Tổng cộng -->
                            <div class="invoice-total">
                                <div class="invoice-total-row">
                                    <span>Tổng tiền dịch vụ:</span>
                                    <span>{{ number_format($invoice->subtotal) }} VNĐ</span>
                                </div>
                                @if($invoice->discount > 0)
                                    <div class="invoice-total-row">
                                        <span>Giảm giá:</span>
                                        <span>{{ number_format($invoice->discount) }} VNĐ</span>
                                    </div>
                                @endif
                                @if($invoice->tax > 0)
                                    <div class="invoice-total-row">
                                        <span>Thuế ({{ number_format($invoice->tax_rate, 1) }}%):</span>
                                        <span>{{ number_format($invoice->tax) }} VNĐ</span>
                                    </div>
                                @endif
                                <div class="invoice-total-row">
                                    <span>Tổng thanh toán:</span>
                                    <span>{{ number_format($invoice->total) }} VNĐ</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Thông tin lịch hẹn -->
                    @if($invoice->appointment)
                        <div class="invoice-card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Thông tin lịch hẹn</h5>
                            </div>
                            <div class="card-body">
                                <div class="invoice-info-item">
                                    <strong>Mã đặt lịch:</strong> {{ $invoice->appointment->booking_code }}
                                </div>
                                <div class="invoice-info-item">
                                    <strong>Ngày hẹn:</strong> {{ \Carbon\Carbon::parse($invoice->appointment->appointment_date)->format('d/m/Y') }}
                                </div>
                                <div class="invoice-info-item">
                                    <strong>Giờ hẹn:</strong> {{ $invoice->appointment->time_slot }}
                                </div>
                                <div class="invoice-info-item">
                                    <strong>Trạng thái:</strong>
                                    @if($invoice->appointment->status == 'pending')
                                        <span class="badge bg-warning">Chờ xác nhận</span>
                                    @elseif($invoice->appointment->status == 'confirmed')
                                        <span class="badge bg-primary">Đã xác nhận</span>
                                    @elseif($invoice->appointment->status == 'completed')
                                        <span class="badge bg-success">Hoàn thành</span>
                                    @elseif($invoice->appointment->status == 'canceled')
                                        <span class="badge bg-danger">Đã hủy</span>
                                    @endif
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('barber.appointments.show', $invoice->appointment->id) }}" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="fas fa-eye me-1"></i>Xem chi tiết lịch hẹn
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Ghi chú -->
                    <div class="invoice-card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Ghi chú</h5>
                        </div>
                        <div class="card-body">
                            @if($invoice->notes)
                                <p>{{ $invoice->notes }}</p>
                            @else
                                <p class="text-muted">Không có ghi chú</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
