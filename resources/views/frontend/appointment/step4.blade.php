@extends('layouts.frontend')

@section('title', 'Đặt lịch - Bước 4: Thông tin cá nhân')

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
                                <div class="step active">
                                    <div class="step-circle">4</div>
                                    <div class="step-text">Thông tin cá nhân</div>
                                </div>
                                <div class="step">
                                    <div class="step-circle">5</div>
                                    <div class="step-text">Thanh toán</div>
                                </div>
                                <div class="step">
                                    <div class="step-circle">6</div>
                                    <div class="step-text">Xác nhận</div>
                                </div>
                            </div>
                        </div>

                        <!-- Hiển thị tóm tắt thông tin đã chọn -->
                        <div class="selected-info mb-4">
                            <h6>Thông tin đã chọn:</h6>
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <div class="card bg-light">
                                        <div class="card-body py-2">
                                            <h6 class="mb-1">Dịch vụ</h6>
                                            @php $totalPrice = 0; $totalDuration = 0; @endphp
                                            @foreach(session('appointment_services', []) as $service)
                                                <div class="d-flex justify-content-between">
                                                    <small>{{ $service->name }}</small>
                                                    <small>{{ number_format($service->price) }} VNĐ</small>
                                                </div>
                                                @php 
                                                    $totalPrice += $service->price; 
                                                    $totalDuration += $service->duration;
                                                @endphp
                                            @endforeach
                                            <div class="mt-1 d-flex justify-content-between fw-bold">
                                                <small>Tổng</small>
                                                <small class="text-primary">{{ number_format($totalPrice) }} VNĐ</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="card bg-light">
                                        <div class="card-body py-2">
                                            <h6 class="mb-1">Thợ cắt tóc</h6>
                                            @php $barber = session('appointment_barber'); @endphp
                                            <div class="d-flex align-items-center">
                                                @if($barber->user->avatar)
                                                    <img src="{{ asset('storage/' . $barber->user->avatar) }}" alt="{{ $barber->user->name }}" class="rounded-circle me-2" width="30" height="30">
                                                @else
                                                    <img src="{{ asset('images/default-avatar.jpg') }}" alt="{{ $barber->user->name }}" class="rounded-circle me-2" width="30" height="30">
                                                @endif
                                                <div>
                                                    <small>{{ $barber->user->name }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="card bg-light">
                                        <div class="card-body py-2">
                                            <h6 class="mb-1">Thời gian</h6>
                                            <div>
                                                <small>Ngày: {{ \Carbon\Carbon::parse(session('appointment_date'))->format('d/m/Y') }}</small>
                                            </div>
                                            <div>
                                                <small>Giờ: {{ session('appointment_time') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form thông tin cá nhân -->
                        <h5 class="card-title mb-4">Bước 4: Thông tin cá nhân</h5>
                        
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <form action="{{ route('appointment.post.step4') }}" method="POST">
                            @csrf
                            
                            @auth
                                <div class="alert alert-info">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <i class="fas fa-info-circle me-2"></i>
                                        </div>
                                        <div>
                                            Bạn đang đặt lịch với tài khoản <strong>{{ auth()->user()->name }}</strong>. 
                                            Chúng tôi sẽ sử dụng thông tin liên hệ từ tài khoản của bạn.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Họ và tên</label>
                                        <input type="text" class="form-control" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" value="{{ old('email', auth()->user()->email) }}" required>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Số điện thoại</label>
                                        <input type="tel" class="form-control" name="phone" value="{{ old('phone', auth()->user()->phone) }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Địa chỉ</label>
                                        <input type="text" class="form-control" name="address" value="{{ old('address', auth()->user()->address) }}">
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info mb-4">
                                    <div class="d-flex">
                                        <div>
                                            <i class="fas fa-info-circle me-2"></i>
                                        </div>
                                        <div>
                                            Bạn đang đặt lịch như một khách. Đăng nhập hoặc đăng ký để quản lý lịch hẹn dễ dàng hơn. 
                                            <a href="{{ route('login') }}?redirect=appointment.step4" class="text-decoration-underline">Đăng nhập</a> | 
                                            <a href="{{ route('register') }}?redirect=appointment.step4" class="text-decoration-underline">Đăng ký</a>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control" name="phone" value="{{ old('phone') }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Địa chỉ</label>
                                        <input type="text" class="form-control" name="address" value="{{ old('address') }}">
                                    </div>
                                </div>
                            @endif
                            
                            <div class="mb-3">
                                <label class="form-label">Ghi chú (không bắt buộc)</label>
                                <textarea class="form-control" name="notes" rows="3" placeholder="Yêu cầu đặc biệt hoặc lưu ý cho thợ cắt tóc">{{ old('notes') }}</textarea>
                            </div>
                            
                            <div class="mt-4 d-flex justify-content-between">
                                <a href="{{ route('appointment.step3') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left"></i> Quay lại
                                </a>
                                <button type="submit" class="btn btn-primary">Tiếp tục <i class="fas fa-arrow-right"></i></button>
                            </div>
                        </form>
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
</style>
@endsection 