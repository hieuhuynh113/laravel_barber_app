@extends('layouts.frontend')

@section('title', 'Đặt lịch - Bước 4: Thông tin cá nhân')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header text-white" style="background-color: #9E8A78;">
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
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-primary text-white py-3">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thông tin đã chọn</h6>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="service-summary">
                                                <h6 class="text-primary border-bottom pb-2 mb-3"><i class="fas fa-cut me-2"></i>Dịch vụ</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-borderless mb-0">
                                                        <tbody>
                                                            @php $totalPrice = 0; $totalDuration = 0; @endphp
                                                            @foreach(session('appointment_services', []) as $service)
                                                                <tr>
                                                                    <td>{{ $service->name }}</td>
                                                                    <td class="text-end fw-medium">{{ number_format($service->price) }} VNĐ</td>
                                                                </tr>
                                                                @php
                                                                    $totalPrice += $service->price;
                                                                    $totalDuration += $service->duration;
                                                                @endphp
                                                            @endforeach
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th>Tổng cộng</th>
                                                                <th class="text-end text-primary">{{ number_format($totalPrice) }} VNĐ</th>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2" class="text-muted pt-0">
                                                                    <small><i class="far fa-clock me-1"></i>Thời gian dự kiến: {{ $totalDuration }} phút</small>
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="barber-summary">
                                                <h6 class="text-primary border-bottom pb-2 mb-3"><i class="fas fa-user me-2"></i>Thợ cắt tóc</h6>
                                                @php $barber = session('appointment_barber'); @endphp
                                                <div class="d-flex align-items-center">
                                                    <div class="barber-avatar me-3">
                                                        @if($barber->user->avatar)
                                                            <img src="{{ asset('storage/' . $barber->user->avatar) }}" alt="{{ $barber->user->name }}" class="rounded-circle border shadow-sm" width="60" height="60">
                                                        @else
                                                            <img src="{{ asset('images/default-avatar.jpg') }}" alt="{{ $barber->user->name }}" class="rounded-circle border shadow-sm" width="60" height="60">
                                                        @endif
                                                    </div>
                                                    <div class="barber-info">
                                                        <h6 class="mb-1">{{ $barber->user->name }}</h6>
                                                        <div class="d-flex align-items-center mb-1">
                                                            <span class="badge bg-warning text-dark me-2">
                                                                <i class="fas fa-star me-1"></i>{{ $barber->experience }} năm kinh nghiệm
                                                            </span>
                                                        </div>
                                                        <small class="text-muted">{{ $barber->specialty }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="time-summary">
                                                <h6 class="text-primary border-bottom pb-2 mb-3"><i class="fas fa-calendar-alt me-2"></i>Thời gian</h6>
                                                <div class="time-details">
                                                    <div class="d-flex align-items-center mb-3">
                                                        <div class="icon-wrapper bg-light rounded-circle p-2 me-3">
                                                            <i class="fas fa-calendar-day text-primary"></i>
                                                        </div>
                                                        <div>
                                                            <div class="text-muted small">Ngày</div>
                                                            <div class="fw-medium">{{ \Carbon\Carbon::parse(session('appointment_date'))->format('d/m/Y') }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="icon-wrapper bg-light rounded-circle p-2 me-3">
                                                            <i class="fas fa-clock text-primary"></i>
                                                        </div>
                                                        <div>
                                                            <div class="text-muted small">Giờ</div>
                                                            <div class="fw-medium">{{ session('appointment_start_time') }} - {{ session('appointment_end_time') }}</div>
                                                        </div>
                                                    </div>
                                                </div>
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
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-circle me-2 fs-4"></i>
                                    <div>
                                        @if($errors->has('name') || $errors->has('email') || $errors->has('phone'))
                                            <strong>
                                                @if($errors->has('name'))
                                                    {{ $errors->first('name') }}
                                                @elseif($errors->has('email'))
                                                    {{ $errors->first('email') }}
                                                @elseif($errors->has('phone'))
                                                    {{ $errors->first('phone') }}
                                                @endif
                                            </strong>
                                        @else
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                </div>
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
    /* Styling for selected info card */
    .selected-info .card-header {
        background-color: #9E8A78 !important;
    }

    .selected-info .text-primary {
        color: #9E8A78 !important;
    }

    .selected-info .icon-wrapper {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .selected-info .table-borderless tr:not(:last-child) td {
        padding-bottom: 0.5rem;
    }

    .selected-info .table-borderless tfoot {
        border-top: 1px dashed #dee2e6;
    }

    .selected-info .table-borderless tfoot th {
        padding-top: 0.75rem;
    }

    .barber-avatar img {
        border: 3px solid #fff;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }

    /* Progress steps styling */
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