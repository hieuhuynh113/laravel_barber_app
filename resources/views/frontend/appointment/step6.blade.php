@extends('layouts.frontend')

@section('title', 'Đặt lịch - Bước 6: Xác nhận')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Đặt lịch hẹn</h4>
                    </div>
                    <div class="card-body">
                        <!-- Thanh tiến trình đặt lịch -->
                        <div class="progress-steps mb-5">
                            <div class="d-flex justify-content-between">
                                <div class="step completed">
                                    <div class="step-circle">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="step-text">Chọn dịch vụ</div>
                                </div>
                                <div class="step completed">
                                    <div class="step-circle">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="step-text">Chọn thợ cắt tóc</div>
                                </div>
                                <div class="step completed">
                                    <div class="step-circle">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="step-text">Chọn thời gian</div>
                                </div>
                                <div class="step completed">
                                    <div class="step-circle">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="step-text">Thông tin cá nhân</div>
                                </div>
                                <div class="step completed">
                                    <div class="step-circle">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="step-text">Thanh toán</div>
                                </div>
                                <div class="step active">
                                    <div class="step-circle">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="step-text">Xác nhận</div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mb-4">
                            <div class="mb-4">
                                <span class="success-icon">
                                    <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                                </span>
                            </div>
                            <h3 class="text-success mb-3">Đặt lịch thành công!</h3>
                            <p class="lead">Cảm ơn bạn đã đặt lịch tại Barber Shop của chúng tôi.</p>
                            <p>Mã đặt lịch của bạn: <strong>{{ $appointment->booking_code }}</strong></p>
                            <p>Chúng tôi đã gửi email xác nhận đến <strong>{{ $appointment->email }}</strong></p>
                            <hr class="my-4">
                        </div>

                        <div class="appointment-details mb-4">
                            <h5 class="card-title">Chi tiết cuộc hẹn</h5>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h6 class="card-title">Thông tin cá nhân</h6>
                                            <p class="mb-1"><strong>Họ tên:</strong> {{ $appointment->customer_name }}</p>
                                            <p class="mb-1"><strong>Email:</strong> {{ $appointment->email }}</p>
                                            <p class="mb-1"><strong>Số điện thoại:</strong> {{ $appointment->phone }}</p>
                                            @if($appointment->address)
                                                <p class="mb-1"><strong>Địa chỉ:</strong> {{ $appointment->address }}</p>
                                            @endif
                                            @if($appointment->notes)
                                                <p class="mb-0"><strong>Ghi chú:</strong> {{ $appointment->notes }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h6 class="card-title">Chi tiết lịch hẹn</h6>
                                            <p class="mb-1">
                                                <strong>Ngày:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}
                                            </p>
                                            <p class="mb-1"><strong>Giờ:</strong> {{ $appointment->appointment_time }}</p>
                                            <p class="mb-1">
                                                <strong>Thợ cắt tóc:</strong> {{ $appointment->barber->user->name }}
                                            </p>
                                            <p class="mb-1">
                                                <strong>Trạng thái:</strong> 
                                                <span class="badge bg-warning">Chờ xác nhận</span>
                                            </p>
                                            <p class="mb-0">
                                                <strong>Phương thức thanh toán:</strong> 
                                                @if($appointment->payment_method == 'cash')
                                                    <span>Tiền mặt tại cửa hàng</span>
                                                @elseif($appointment->payment_method == 'momo')
                                                    <span>MoMo</span>
                                                @elseif($appointment->payment_method == 'vnpay')
                                                    <span>VNPay</span>
                                                @else
                                                    <span>Chuyển khoản ngân hàng</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Dịch vụ đã chọn</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Dịch vụ</th>
                                                    <th>Thời gian</th>
                                                    <th class="text-end">Giá</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $totalPrice = 0; $totalDuration = 0; @endphp
                                                @foreach($appointment->services as $service)
                                                    <tr>
                                                        <td>{{ $service->name }}</td>
                                                        <td>{{ $service->duration }} phút</td>
                                                        <td class="text-end">{{ number_format($service->price) }} VNĐ</td>
                                                    </tr>
                                                    @php 
                                                        $totalPrice += $service->price; 
                                                        $totalDuration += $service->duration;
                                                    @endphp
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Tổng</th>
                                                    <th>{{ $totalDuration }} phút</th>
                                                    <th class="text-end">{{ number_format($totalPrice) }} VNĐ</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="additional-info mb-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Thông tin hữu ích</h6>
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-2">
                                            <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                            <strong>Địa chỉ:</strong> 123 Đường Nguyễn Huệ, Quận 1, TP.HCM
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-phone-alt text-primary me-2"></i>
                                            <strong>Điện thoại:</strong> (028) 1234 5678
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-clock text-primary me-2"></i>
                                            <strong>Giờ mở cửa:</strong> 8:00 - 20:00 (Thứ 2 - Chủ nhật)
                                        </li>
                                        <li>
                                            <i class="fas fa-info-circle text-primary me-2"></i>
                                            <strong>Lưu ý:</strong> Vui lòng đến trước 5 phút để check-in
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                <a href="{{ route('profile.appointments') }}" class="btn btn-primary me-md-2">
                                    <i class="fas fa-calendar-alt me-1"></i> Quản lý lịch hẹn
                                </a>
                                <a href="{{ route('home') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-home me-1"></i> Về trang chủ
                                </a>
                            </div>
                            
                            @if($appointment->payment_method !== 'cash')
                                <p class="mt-3 text-muted">
                                    Nếu bạn chưa thanh toán, vui lòng thanh toán trong vòng 24 giờ để giữ lịch hẹn
                                </p>
                            @endif
                            
                            <div class="mt-4">
                                <a href="#" class="btn btn-success btn-sm mx-1">
                                    <i class="fas fa-download me-1"></i> Tải về PDF
                                </a>
                                <a href="mailto:?subject=Lịch hẹn tại Barber Shop&body=Mã đặt lịch: {{ $appointment->booking_code }}" class="btn btn-info btn-sm text-white mx-1">
                                    <i class="fas fa-envelope me-1"></i> Gửi qua email
                                </a>
                                <a href="#" class="btn btn-dark btn-sm mx-1">
                                    <i class="fas fa-calendar-plus me-1"></i> Thêm vào lịch
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('styles')
<style>
    .progress-steps {
        position: relative;
    }
    
    .progress-steps:before {
        content: '';
        position: absolute;
        top: 20px;
        left: 0;
        width: 100%;
        height: 2px;
        background-color: #e9ecef;
        z-index: 0;
    }
    
    .step {
        text-align: center;
        z-index: 1;
        flex: 1;
        position: relative;
    }
    
    .step-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #e9ecef;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 8px;
        font-weight: bold;
    }
    
    .step.active .step-circle {
        background-color: #0d6efd;
        color: white;
    }
    
    .step.completed .step-circle {
        background-color: #28a745;
        color: white;
    }
    
    .step-text {
        font-size: 0.875rem;
        color: #6c757d;
    }
    
    .step.active .step-text {
        color: #0d6efd;
        font-weight: bold;
    }
    
    .step.completed .step-text {
        color: #28a745;
    }
    
    .success-icon {
        display: inline-block;
        animation: bounce 1s ease infinite;
    }
    
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
        40% {transform: translateY(-20px);}
        60% {transform: translateY(-10px);}
    }
</style>
@endsection 