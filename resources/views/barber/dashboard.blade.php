@extends('layouts.app')

@section('title', 'Dashboard Thợ Cắt Tóc')

@section('styles')
<style>
    .dashboard-card {
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        transition: transform 0.3s;
        overflow: hidden;
    }
    .dashboard-card:hover {
        transform: translateY(-5px);
    }
    .card-icon {
        font-size: 2.5rem;
        margin-bottom: 10px;
    }
    .stat-card {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        height: 100%;
    }
    .stat-value {
        font-size: 3rem;
        font-weight: bold;
        margin: 10px 0;
    }
    .stat-title {
        font-size: 1.1rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .appointment-card {
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }
    .appointment-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #eee;
        padding: 15px 20px;
        border-radius: 10px 10px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .appointment-table th {
        font-weight: 600;
        color: #495057;
    }
    .service-badge {
        margin-right: 5px;
        margin-bottom: 5px;
        padding: 8px 12px;
        border-radius: 50px;
        font-weight: 500;
    }
    .status-badge {
        padding: 8px 15px;
        border-radius: 50px;
        font-weight: 500;
    }
    .welcome-section {
        background: linear-gradient(135deg, #4a6bff 0%, #2948ff 100%);
        color: white;
        border-radius: 10px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }
    .welcome-title {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 10px;
    }
    .welcome-subtitle {
        opacity: 0.9;
        margin-bottom: 0;
    }
    .empty-appointments {
        text-align: center;
        padding: 40px 20px;
        color: #6c757d;
    }
    .empty-icon {
        font-size: 3rem;
        margin-bottom: 15px;
        opacity: 0.5;
    }
    .bg-light-primary {
        background-color: #e8f0fe;
    }
    .bg-light-secondary {
        background-color: #f0f0f0;
    }
    .bg-light-success {
        background-color: #e8f5e9;
    }
    .bg-light-danger {
        background-color: #feebee;
    }
    .bg-light-warning {
        background-color: #fff8e1;
    }
    .bg-light-info {
        background-color: #e3f2fd;
    }
</style>
@endsection

@section('content')
<div class="container py-5">
    <div class="welcome-section mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="welcome-title">Xin chào, {{ Auth::user()->name }}!</h1>
                <p class="welcome-subtitle">Chào mừng bạn đến với hệ thống quản lý của thợ cắt tóc. Dưới đây là tổng quan về lịch hẹn của bạn.</p>
            </div>
            <div class="col-md-4 text-md-end">
                <p class="mb-0">{{ \Carbon\Carbon::now()->format('l, d/m/Y') }}</p>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-3 mb-4">
            <div class="dashboard-card h-100">
                <div class="stat-card bg-primary text-white">
                    <i class="fas fa-calendar-day card-icon"></i>
                    <h3 class="stat-title">Lịch hẹn hôm nay</h3>
                    <div class="stat-value">{{ $todayAppointments }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="dashboard-card h-100">
                <div class="stat-card bg-success text-white">
                    <i class="fas fa-calendar-check card-icon"></i>
                    <h3 class="stat-title">Tổng số lịch hẹn</h3>
                    <div class="stat-value">{{ $totalAppointments }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="dashboard-card h-100">
                <div class="stat-card bg-info text-white">
                    <i class="fas fa-check-circle card-icon"></i>
                    <h3 class="stat-title">Hoàn thành</h3>
                    <div class="stat-value">{{ $completedAppointments }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="dashboard-card h-100">
                <div class="stat-card bg-warning text-white">
                    <i class="fas fa-chart-pie card-icon"></i>
                    <h3 class="stat-title">Tỷ lệ hoàn thành</h3>
                    <div class="stat-value">{{ $totalAppointments > 0 ? round(($completedAppointments / $totalAppointments) * 100) : 0 }}%</div>
                </div>
            </div>
        </div>
    </div>

    <div class="appointment-card">
        <div class="appointment-header">
            <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i> Lịch hẹn sắp tới</h5>
            <a href="#" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
        </div>
        <div class="card-body">
            @if($upcomingAppointments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover appointment-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-user me-2"></i>Khách hàng</th>
                                <th><i class="fas fa-calendar me-2"></i>Ngày</th>
                                <th><i class="fas fa-clock me-2"></i>Giờ</th>
                                <th><i class="fas fa-cut me-2"></i>Dịch vụ</th>
                                <th><i class="fas fa-info-circle me-2"></i>Trạng thái</th>
                                <th><i class="fas fa-cog me-2"></i>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($upcomingAppointments as $appointment)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-light-primary me-2 rounded-circle text-center" style="width: 40px; height: 40px; line-height: 40px;">
                                                <span>{{ substr($appointment->customer_name ?? $appointment->user->name ?? 'K', 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $appointment->customer_name ?? $appointment->user->name ?? 'Khách vãng lai' }}</h6>
                                                <small class="text-muted">{{ $appointment->phone }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($appointment->end_time)->format('H:i') }}</td>
                                    <td>
                                        <div>
                                            @foreach($appointment->services as $service)
                                                <span class="badge bg-light-secondary service-badge text-dark">{{ $service->name }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>
                                        @if($appointment->status == 'pending')
                                            <span class="badge bg-warning status-badge">Chờ xác nhận</span>
                                        @elseif($appointment->status == 'confirmed')
                                            <span class="badge bg-primary status-badge">Đã xác nhận</span>
                                        @elseif($appointment->status == 'completed')
                                            <span class="badge bg-success status-badge">Hoàn thành</span>
                                        @elseif($appointment->status == 'canceled')
                                            <span class="badge bg-danger status-badge">Đã hủy</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <li><a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i> Xem chi tiết</a></li>
                                                @if($appointment->status == 'confirmed')
                                                    <li><a class="dropdown-item" href="#"><i class="fas fa-check-circle me-2"></i> Đánh dấu hoàn thành</a></li>
                                                @endif
                                                @if($appointment->status == 'pending')
                                                    <li><a class="dropdown-item" href="#"><i class="fas fa-check me-2"></i> Xác nhận</a></li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-appointments">
                    <i class="fas fa-calendar-times empty-icon"></i>
                    <h5>Không có lịch hẹn sắp tới</h5>
                    <p class="text-muted">Hiện tại bạn không có lịch hẹn nào sắp tới.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Thêm FontAwesome nếu chưa có
    if (!document.querySelector('link[href*="fontawesome"]')) {
        const fontAwesome = document.createElement('link');
        fontAwesome.rel = 'stylesheet';
        fontAwesome.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css';
        document.head.appendChild(fontAwesome);
    }

    // Thêm màu nền cho các avatar
    document.addEventListener('DOMContentLoaded', function() {
        const avatars = document.querySelectorAll('.avatar');
        const colors = ['#e1f5fe', '#e8f5e9', '#fff3e0', '#f3e5f5', '#e8eaf6', '#e0f2f1'];

        avatars.forEach((avatar, index) => {
            avatar.style.backgroundColor = colors[index % colors.length];
        });
    });
</script>
@endsection