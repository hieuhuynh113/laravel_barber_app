@extends('layouts.app')

@section('title', 'Chi tiết lịch hẹn')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/barber-dashboard.css') }}">
<style>
    .appointment-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        margin-bottom: 1.5rem;
    }
    
    .appointment-card .card-header {
        background-color: #2c3e50;
        color: #fff;
        border-radius: 10px 10px 0 0;
        padding: 1rem 1.5rem;
    }
    
    .appointment-card .card-body {
        padding: 1.5rem;
    }
    
    .info-group {
        margin-bottom: 1.5rem;
    }
    
    .info-group:last-child {
        margin-bottom: 0;
    }
    
    .info-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }
    
    .info-value {
        color: #34495e;
    }
    
    .status-badge {
        padding: 0.5rem 0.75rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.8rem;
    }
    
    .status-pending {
        background-color: #f8f9fa;
        color: #f39c12;
        border: 1px solid #f39c12;
    }
    
    .status-confirmed {
        background-color: #e8f4fd;
        color: #3498db;
        border: 1px solid #3498db;
    }
    
    .status-completed {
        background-color: #e8f8f5;
        color: #2ecc71;
        border: 1px solid #2ecc71;
    }
    
    .status-canceled {
        background-color: #fdeeee;
        color: #e74c3c;
        border: 1px solid #e74c3c;
    }
    
    .service-item {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 1rem;
        border-left: 4px solid #3498db;
    }
    
    .service-name {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.25rem;
    }
    
    .service-price {
        color: #e74c3c;
        font-weight: 600;
    }
    
    .service-duration {
        color: #7f8c8d;
        font-size: 0.9rem;
    }
    
    .action-btn {
        padding: 0.75rem 1.5rem;
        border-radius: 5px;
        margin-right: 0.5rem;
        transition: all 0.3s;
        font-weight: 600;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection

@section('content')
<div class="container dashboard-container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="dashboard-title">Chi tiết lịch hẹn</h1>
                <a href="{{ route('barber.appointments.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                </a>
            </div>
            
            <div class="row">
                <div class="col-md-8">
                    <!-- Thông tin lịch hẹn -->
                    <div class="appointment-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Thông tin lịch hẹn #{{ $appointment->booking_code }}</h5>
                            @if($appointment->status == 'pending')
                                <span class="status-badge status-pending">Chờ xác nhận</span>
                            @elseif($appointment->status == 'confirmed')
                                <span class="status-badge status-confirmed">Đã xác nhận</span>
                            @elseif($appointment->status == 'completed')
                                <span class="status-badge status-completed">Hoàn thành</span>
                            @elseif($appointment->status == 'canceled')
                                <span class="status-badge status-canceled">Đã hủy</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-group">
                                        <div class="info-label">Khách hàng</div>
                                        <div class="info-value">{{ $appointment->customer_name }}</div>
                                    </div>
                                    <div class="info-group">
                                        <div class="info-label">Email</div>
                                        <div class="info-value">{{ $appointment->email }}</div>
                                    </div>
                                    <div class="info-group">
                                        <div class="info-label">Số điện thoại</div>
                                        <div class="info-value">{{ $appointment->phone }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-group">
                                        <div class="info-label">Ngày hẹn</div>
                                        <div class="info-value">{{ $appointment->appointment_date->format('d/m/Y') }}</div>
                                    </div>
                                    <div class="info-group">
                                        <div class="info-label">Giờ hẹn</div>
                                        <div class="info-value">{{ $appointment->time_slot }}</div>
                                    </div>
                                    <div class="info-group">
                                        <div class="info-label">Phương thức thanh toán</div>
                                        <div class="info-value">
                                            @if($appointment->payment_method == 'cash')
                                                <i class="fas fa-money-bill-wave me-1"></i> Tiền mặt
                                            @elseif($appointment->payment_method == 'bank_transfer')
                                                <i class="fas fa-university me-1"></i> Chuyển khoản
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            @if($appointment->notes)
                                <div class="info-group mt-3">
                                    <div class="info-label">Ghi chú</div>
                                    <div class="info-value">{{ $appointment->notes }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Dịch vụ đã chọn -->
                    <div class="appointment-card">
                        <div class="card-header">
                            <h5 class="mb-0">Dịch vụ đã chọn</h5>
                        </div>
                        <div class="card-body">
                            @foreach($appointment->services as $service)
                                <div class="service-item">
                                    <div class="service-name">{{ $service->name }}</div>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <div class="service-price">{{ number_format($service->price) }} VNĐ</div>
                                        <div class="service-duration"><i class="far fa-clock me-1"></i> {{ $service->duration }} phút</div>
                                    </div>
                                </div>
                            @endforeach
                            
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div class="fw-bold">Tổng thời gian:</div>
                                <div>{{ $appointment->services->sum('duration') }} phút</div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <div class="fw-bold">Tổng tiền:</div>
                                <div class="text-danger fw-bold">{{ number_format($appointment->services->sum('price')) }} VNĐ</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <!-- Thao tác -->
                    <div class="appointment-card">
                        <div class="card-header">
                            <h5 class="mb-0">Thao tác</h5>
                        </div>
                        <div class="card-body">
                            @if($appointment->status == 'confirmed')
                                <button type="button" class="btn btn-success action-btn w-100 mb-3" data-bs-toggle="modal" data-bs-target="#completeModal">
                                    <i class="fas fa-check me-2"></i>Đánh dấu hoàn thành
                                </button>
                                
                                <!-- Modal xác nhận hoàn thành -->
                                <div class="modal fade" id="completeModal" tabindex="-1" aria-labelledby="completeModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="completeModalLabel">Xác nhận hoàn thành lịch hẹn</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('barber.appointments.complete', $appointment->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <p>Bạn đang chuyển trạng thái lịch hẹn sang "Hoàn thành". Hệ thống sẽ tạo hóa đơn tự động.</p>
                                                    <p>Vui lòng chọn trạng thái thanh toán:</p>
                                                    
                                                    <div class="form-check mb-3">
                                                        <input class="form-check-input" type="radio" name="payment_status" id="payment-pending" value="pending" checked>
                                                        <label class="form-check-label" for="payment-pending">
                                                            Chưa thanh toán
                                                        </label>
                                                        <small class="text-muted d-block">Hóa đơn sẽ được tạo với trạng thái "Chưa thanh toán".</small>
                                                    </div>
                                                    
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="payment_status" id="payment-paid" value="paid">
                                                        <label class="form-check-label" for="payment-paid">
                                                            Đã thanh toán
                                                        </label>
                                                        <small class="text-muted d-block">Hóa đơn sẽ được tạo với trạng thái "Đã thanh toán".</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                    <button type="submit" class="btn btn-success">Xác nhận</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @elseif($appointment->status == 'completed')
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>Lịch hẹn đã được hoàn thành
                                </div>
                                
                                @if($appointment->invoice)
                                    <a href="#" class="btn btn-primary action-btn w-100">
                                        <i class="fas fa-file-invoice-dollar me-2"></i>Xem hóa đơn
                                    </a>
                                @endif
                            @elseif($appointment->status == 'canceled')
                                <div class="alert alert-danger">
                                    <i class="fas fa-times-circle me-2"></i>Lịch hẹn đã bị hủy
                                </div>
                            @elseif($appointment->status == 'pending')
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-circle me-2"></i>Lịch hẹn đang chờ xác nhận từ quản trị viên
                                </div>
                            @endif
                            
                            <a href="{{ route('barber.appointments.index') }}" class="btn btn-outline-secondary action-btn w-100 mt-3">
                                <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
                            </a>
                        </div>
                    </div>
                    
                    <!-- Thông tin thêm -->
                    <div class="appointment-card">
                        <div class="card-header">
                            <h5 class="mb-0">Thông tin thêm</h5>
                        </div>
                        <div class="card-body">
                            <div class="info-group">
                                <div class="info-label">Ngày tạo</div>
                                <div class="info-value">{{ $appointment->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                            @if($appointment->status == 'completed')
                                <div class="info-group">
                                    <div class="info-label">Ngày hoàn thành</div>
                                    <div class="info-value">{{ $appointment->updated_at->format('d/m/Y H:i') }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
