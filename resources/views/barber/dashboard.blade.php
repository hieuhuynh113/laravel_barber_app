@extends('layouts.app')

@section('title', 'Trang quản lý của thợ cắt tóc')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/barber-dashboard.css') }}">
<style>
    .stats-card {
        transition: all 0.3s ease;
    }

    .notifications-card, .schedule-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        margin-bottom: 1.5rem;
    }

    .notifications-card .card-header, .schedule-card .card-header {
        background-color: #2c3e50;
        color: #fff;
        border-radius: 10px 10px 0 0;
        padding: 1rem 1.5rem;
    }

    .notifications-card .card-body, .schedule-card .card-body {
        padding: 1.5rem;
    }

    .notification-item {
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1rem;
        border-left: 4px solid #3498db;
        background-color: #f8f9fa;
        transition: all 0.3s;
    }

    .notification-item:last-child {
        margin-bottom: 0;
    }

    .notification-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.5rem;
    }

    .notification-title {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0;
    }

    .notification-time {
        color: #7f8c8d;
        font-size: 0.85rem;
    }

    .notification-content {
        color: #34495e;
        margin-bottom: 0;
    }

    .schedule-info {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 1.25rem;
        border-left: 4px solid #3498db;
    }

    .time-info {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .time-info i {
        color: #3498db;
        margin-right: 0.5rem;
    }

    .time-value {
        font-weight: 600;
    }

    .max-appointments {
        display: flex;
        align-items: center;
    }

    .max-appointments i {
        color: #2ecc71;
        margin-right: 0.5rem;
    }

    .day-off-alert {
        text-align: center;
        padding: 2rem 0;
    }

    .day-off-alert i {
        font-size: 3rem;
        color: #e74c3c;
        margin-bottom: 1rem;
    }

    .empty-state {
        text-align: center;
        padding: 2rem 0;
    }

    .empty-state i {
        font-size: 3rem;
        color: #bdc3c7;
        margin-bottom: 1rem;
    }
</style>
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

            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="appointments-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Lịch hẹn sắp tới</h5>
                            <a href="{{ route('barber.appointments.index') }}" class="btn btn-sm btn-outline-light"><i class="fas fa-external-link-alt me-1"></i>Xem tất cả</a>
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
                                                <a href="{{ route('barber.appointments.show', $appointment->id) }}" class="btn btn-sm btn-info me-1">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($appointment->status == 'confirmed')
                                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#completeModal{{ $appointment->id }}">
                                                        <i class="fas fa-check"></i>
                                                    </button>

                                                    <!-- Modal xác nhận hoàn thành -->
                                                    <div class="modal fade" id="completeModal{{ $appointment->id }}" tabindex="-1" aria-labelledby="completeModalLabel{{ $appointment->id }}" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="completeModalLabel{{ $appointment->id }}">Xác nhận hoàn thành lịch hẹn</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <form action="{{ route('barber.appointments.complete', $appointment->id) }}" method="POST">
                                                                    @csrf
                                                                    <div class="modal-body">
                                                                        <p>Bạn đang chuyển trạng thái lịch hẹn sang "Hoàn thành". Hệ thống sẽ tạo hóa đơn tự động.</p>
                                                                        <p>Vui lòng chọn trạng thái thanh toán:</p>

                                                                        <div class="form-check mb-3">
                                                                            <input class="form-check-input" type="radio" name="payment_status" id="payment-pending-{{ $appointment->id }}" value="pending" checked>
                                                                            <label class="form-check-label" for="payment-pending-{{ $appointment->id }}">
                                                                                Chưa thanh toán
                                                                            </label>
                                                                        </div>

                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="payment_status" id="payment-paid-{{ $appointment->id }}" value="paid">
                                                                            <label class="form-check-label" for="payment-paid-{{ $appointment->id }}">
                                                                                Đã thanh toán
                                                                            </label>
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
                                                @endif
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

        <div class="col-md-4">
            <!-- Thông báo mới -->
            <div class="notifications-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-bell me-2"></i>Thông báo mới</h5>
                    <a href="{{ route('barber.notifications.index') }}" class="btn btn-sm btn-outline-light"><i class="fas fa-external-link-alt me-1"></i>Xem tất cả</a>
                </div>
                <div class="card-body">
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <div class="notifications-list">
                            @foreach(auth()->user()->unreadNotifications->take(5) as $notification)
                                <div class="notification-item">
                                    <div class="notification-header">
                                        <h6 class="notification-title">
                                            @if(str_contains($notification->type, 'Appointment'))
                                                Lịch hẹn
                                            @elseif(str_contains($notification->type, 'Schedule'))
                                                Lịch làm việc
                                            @else
                                                Thông báo
                                            @endif
                                        </h6>
                                        <span class="notification-time">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</span>
                                    </div>
                                    <p class="notification-content">
                                        {{ $notification->data['message'] ?? 'Không có nội dung' }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-bell-slash"></i>
                            <h5>Không có thông báo mới</h5>
                            <p class="text-muted">Bạn không có thông báo mới nào.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Lịch làm việc hôm nay -->
            <div class="schedule-card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Lịch làm việc hôm nay</h5>
                    <a href="{{ route('barber.schedules.index') }}" class="btn btn-sm btn-outline-light"><i class="fas fa-external-link-alt me-1"></i>Xem tất cả</a>
                </div>
                <div class="card-body">
                    @php
                        $today = \Carbon\Carbon::now()->dayOfWeek;
                        $schedule = auth()->user()->barber->schedules()->where('day_of_week', $today)->first();
                    @endphp

                    @if($schedule)
                        @if($schedule->is_day_off)
                            <div class="day-off-alert">
                                <i class="fas fa-calendar-times"></i>
                                <h5>Hôm nay là ngày nghỉ</h5>
                                <p class="text-muted">Bạn không có lịch làm việc hôm nay.</p>
                            </div>
                        @else
                            <div class="schedule-info">
                                <div class="time-info">
                                    <i class="far fa-clock"></i>
                                    <span class="time-value">{{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}</span>
                                </div>
                                <div class="max-appointments">
                                    <i class="fas fa-users"></i>
                                    <span>Số lượng khách tối đa: {{ $schedule->max_appointments }}</span>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <h5>Không có thông tin lịch làm việc</h5>
                            <p class="text-muted">Không tìm thấy thông tin lịch làm việc cho hôm nay.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Thêm hiệu ứng cho các card thống kê
        $('.stats-card').hover(
            function() {
                $(this).addClass('shadow-lg').css('transform', 'translateY(-5px)');
            },
            function() {
                $(this).removeClass('shadow-lg').css('transform', 'translateY(0)');
            }
        );

        // Thêm hiệu ứng cho các thông báo
        $('.notification-item').hover(
            function() {
                $(this).addClass('shadow-sm').css('transform', 'translateY(-2px)');
            },
            function() {
                $(this).removeClass('shadow-sm').css('transform', 'translateY(0)');
            }
        );
    });
</script>
@endsection