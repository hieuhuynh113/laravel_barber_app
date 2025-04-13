@extends('layouts.app')

@section('title', 'Trang quản lý của thợ cắt tóc')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/barber-dashboard.css') }}">
@endsection

@section('content')
<div class="container dashboard-container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h1 class="dashboard-title">Chào {{ $barberName }}!</h1>
            <p class="dashboard-subtitle">Chúc bạn có một ngày làm việc hiệu quả và thành công</p>

            <div class="row mb-4">
                <div class="col-md-3 mb-4">
                    <div class="stats-card primary-card text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title">Lịch hẹn hôm nay</h5>
                                    <p class="card-text">{{ $todayAppointments }}</p>
                                </div>
                                <div class="card-icon">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="stats-card success-card text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title">Tổng số lịch hẹn</h5>
                                    <p class="card-text">{{ $totalAppointments }}</p>
                                </div>
                                <div class="card-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="stats-card info-card text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title">Hoàn thành</h5>
                                    <p class="card-text">{{ $completedAppointments }}</p>
                                </div>
                                <div class="card-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="stats-card warning-card text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title">Tỷ lệ hoàn thành</h5>
                                    <p class="card-text">
                                        {{ $totalAppointments > 0 ? round(($completedAppointments / $totalAppointments) * 100) : 0 }}%
                                    </p>
                                </div>
                                <div class="card-icon">
                                    <i class="fas fa-chart-pie"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="appointments-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Lịch hẹn sắp tới</h5>
                    <a href="#" class="btn btn-sm btn-outline-light"><i class="fas fa-sync-alt me-1"></i>Làm mới</a>
                </div>
                <div class="card-body">
                    @if($upcomingAppointments->count() > 0)
                        <div class="table-responsive">
                            <table class="table appointments-table">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-user me-2"></i>Khách hàng</th>
                                        <th><i class="fas fa-calendar me-2"></i>Ngày</th>
                                        <th><i class="fas fa-clock me-2"></i>Giờ</th>
                                        <th><i class="fas fa-cut me-2"></i>Dịch vụ</th>
                                        <th><i class="fas fa-info-circle me-2"></i>Trạng thái</th>
                                        <th><i class="fas fa-cog me-2"></i>Tùy chọn</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingAppointments as $appointment)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar me-2 bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-user text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $appointment->customer_name ?? ($appointment->user->name ?? 'Khách hàng') }}</h6>
                                                        <small class="text-muted">{{ $appointment->customer_phone ?? 'Không có số điện thoại' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($appointment->end_time)->format('H:i') }}</td>
                                            <td>
                                                @if(isset($appointment->services) && count($appointment->services) > 0)
                                                    @foreach($appointment->services as $service)
                                                        <span class="service-badge">{{ $service->name }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">Chưa có dịch vụ</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($appointment->status == 'pending')
                                                    <span class="status-badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Chờ xác nhận</span>
                                                @elseif($appointment->status == 'confirmed')
                                                    <span class="status-badge bg-primary"><i class="fas fa-check me-1"></i>Đã xác nhận</span>
                                                @elseif($appointment->status == 'completed')
                                                    <span class="status-badge bg-success"><i class="fas fa-check-double me-1"></i>Hoàn thành</span>
                                                @elseif($appointment->status == 'canceled')
                                                    <span class="status-badge bg-danger"><i class="fas fa-times me-1"></i>Đã hủy</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton{{ $appointment->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $appointment->id }}">
                                                        <li><a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i>Xem chi tiết</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="fas fa-check-circle me-2"></i>Đánh dấu hoàn thành</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="fas fa-times-circle me-2"></i>Hủy lịch hẹn</a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <h5>Không có lịch hẹn sắp tới</h5>
                            <p class="text-muted">Bạn không có lịch hẹn nào trong thời gian tới.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection