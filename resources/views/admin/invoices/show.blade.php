@extends('layouts.admin')

@section('title', 'Chi tiết hóa đơn')

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết hóa đơn: #{{ $invoice->invoice_code }}</h1>
        <div>
            <a href="{{ route('admin.invoices.edit', $invoice->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Chỉnh sửa
            </a>
            <a href="{{ route('admin.invoices.print', $invoice->id) }}" class="btn btn-info" target="_blank">
                <i class="fas fa-print"></i> In hóa đơn
            </a>
            <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin hóa đơn</h6>
                </div>
                <div class="card-body">
                    <div class="invoice-details">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="text-gray-800">Thông tin cửa hàng</h5>
                                <p class="mb-0"><strong>{{ config('app.name') }}</strong></p>
                                <p class="mb-0">Địa chỉ: {{ $shopInfo['shop_address'] ?? 'Chưa cập nhật' }}</p>
                                <p class="mb-0">Điện thoại: {{ $shopInfo['shop_phone'] ?? 'Chưa cập nhật' }}</p>
                                <p class="mb-0">Email: {{ $shopInfo['shop_email'] ?? 'Chưa cập nhật' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="text-gray-800">Thông tin khách hàng</h5>
                                @if($invoice->user)
                                    <p class="mb-0"><strong>{{ $invoice->user->name }}</strong></p>
                                    <p class="mb-0">Điện thoại: {{ $invoice->user->phone ?? 'Chưa cập nhật' }}</p>
                                    <p class="mb-0">Email: {{ $invoice->user->email }}</p>
                                    <p class="mb-0">Địa chỉ: {{ $invoice->user->address ?? 'Chưa cập nhật' }}</p>
                                @else
                                    <p class="mb-0"><strong>{{ $invoice->customer_name ?? 'Khách vãng lai' }}</strong></p>
                                    <p class="mb-0">Điện thoại: {{ $invoice->customer_phone ?? 'Chưa cập nhật' }}</p>
                                    <p class="mb-0">Email: {{ $invoice->customer_email ?? 'Chưa cập nhật' }}</p>
                                    <p class="mb-0">Địa chỉ: {{ $invoice->customer_address ?? 'Chưa cập nhật' }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p class="mb-0">
                                    <strong>Mã hóa đơn:</strong> #{{ $invoice->invoice_code }}
                                </p>
                                <p class="mb-0">
                                    <strong>Ngày tạo:</strong> {{ $invoice->created_at->format('d/m/Y H:i:s') }}
                                </p>
                                <p class="mb-0">
                                    <strong>Trạng thái:</strong>
                                    @if($invoice->status == 'pending')
                                        <span class="badge bg-warning">Chờ xác nhận</span>
                                    @elseif($invoice->status == 'confirmed')
                                        <span class="badge bg-primary">Đã xác nhận</span>
                                    @elseif($invoice->status == 'completed')
                                        <span class="badge bg-success">Hoàn thành</span>
                                    @elseif($invoice->status == 'canceled')
                                        <span class="badge bg-danger">Đã hủy</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-0">
                                    <strong>Phương thức thanh toán:</strong>
                                    @if($invoice->payment_method == 'cash')
                                        <span class="badge bg-secondary">Tiền mặt</span>
                                    @elseif($invoice->payment_method == 'card')
                                        <span class="badge bg-info">Thẻ</span>
                                    @elseif($invoice->payment_method == 'transfer')
                                        <span class="badge bg-primary">Chuyển khoản</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $invoice->payment_method }}</span>
                                    @endif
                                </p>
                                <p class="mb-0">
                                    <strong>Thanh toán:</strong>
                                    @if($invoice->payment_status)
                                        <span class="badge bg-success">Đã thanh toán</span>
                                    @else
                                        <span class="badge bg-warning">Chưa thanh toán</span>
                                    @endif
                                </p>
                                <p class="mb-0">
                                    <strong>Ngày thanh toán:</strong>
                                    {{ $invoice->payment_date ? $invoice->payment_date->format('d/m/Y H:i:s') : 'Chưa thanh toán' }}
                                </p>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Sản phẩm/Dịch vụ</th>
                                        <th>Đơn giá</th>
                                        <th>Số lượng</th>
                                        <th>Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoice->items as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                {{ $item->name }}
                                                @if($item->type == 'service')
                                                    <span class="badge bg-primary">Dịch vụ</span>
                                                @else
                                                    <span class="badge bg-info">Sản phẩm</span>
                                                @endif
                                            </td>
                                            <td>{{ number_format($item->price) }} VNĐ</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ number_format($item->price * $item->quantity) }} VNĐ</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-right">Tạm tính:</th>
                                        <td>{{ number_format($invoice->subtotal) }} VNĐ</td>
                                    </tr>
                                    @if($invoice->discount_amount > 0)
                                        <tr>
                                            <th colspan="4" class="text-right">Giảm giá:</th>
                                            <td>{{ number_format($invoice->discount_amount) }} VNĐ</td>
                                        </tr>
                                    @endif
                                    @if($invoice->tax_amount > 0)
                                        <tr>
                                            <th colspan="4" class="text-right">Thuế ({{ $invoice->tax_rate }}%):</th>
                                            <td>{{ number_format($invoice->tax_amount) }} VNĐ</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th colspan="4" class="text-right">Tổng cộng:</th>
                                        <td class="font-weight-bold">{{ number_format($invoice->total_amount) }} VNĐ</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        @if($invoice->notes)
                            <div class="mt-4">
                                <h6 class="font-weight-bold">Ghi chú:</h6>
                                <p>{{ $invoice->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thao tác</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.invoices.update-status', $invoice->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label for="status" class="form-label">Cập nhật trạng thái</label>
                            <select class="form-select" id="status" name="status">
                                <option value="pending" {{ $invoice->status == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                                <option value="confirmed" {{ $invoice->status == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                                <option value="completed" {{ $invoice->status == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                <option value="canceled" {{ $invoice->status == 'canceled' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="payment_status" class="form-label">Tình trạng thanh toán</label>
                            <select class="form-select" id="payment_status" name="payment_status">
                                <option value="0" {{ !$invoice->payment_status ? 'selected' : '' }}>Chưa thanh toán</option>
                                <option value="1" {{ $invoice->payment_status ? 'selected' : '' }}>Đã thanh toán</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-save"></i> Cập nhật
                            </button>
                        </div>
                    </form>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <a href="{{ route('admin.invoices.print', $invoice->id) }}" class="btn btn-info w-100" target="_blank">
                            <i class="fas fa-print"></i> In hóa đơn
                        </a>
                    </div>
                    
                    <div class="mb-3">
                        <a href="{{ route('admin.invoices.send-email', $invoice->id) }}" class="btn btn-success w-100">
                            <i class="fas fa-envelope"></i> Gửi email hóa đơn
                        </a>
                    </div>
                    
                    <div class="mb-3">
                        <form action="{{ route('admin.invoices.destroy', $invoice->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Bạn có chắc chắn muốn xóa hóa đơn này?')">
                                <i class="fas fa-trash"></i> Xóa hóa đơn
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Lịch sử</h6>
                </div>
                <div class="card-body">
                    @if(isset($invoice->history) && count($invoice->history) > 0)
                        <div class="timeline">
                            @foreach($invoice->history as $history)
                                <div class="timeline-item">
                                    <div class="timeline-date">{{ $history->created_at->format('d/m/Y H:i') }}</div>
                                    <div class="timeline-content">
                                        <p class="mb-0">{{ $history->description }}</p>
                                        <small>{{ $history->user ? $history->user->name : 'Hệ thống' }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center">Chưa có lịch sử cập nhật</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 20px;
    }
    
    .timeline-item {
        position: relative;
        padding-bottom: 20px;
        padding-left: 15px;
        border-left: 2px solid #e3e6f0;
    }
    
    .timeline-date {
        font-weight: bold;
        margin-bottom: 5px;
    }
    
    .timeline-content {
        background-color: #f8f9fc;
        padding: 10px;
        border-radius: 5px;
        border-left: 3px solid #4e73df;
    }
</style>
@endsection 