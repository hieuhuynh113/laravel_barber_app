@extends('layouts.app')

@section('title', 'Trang quản lý của thợ cắt tóc')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
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

    /* Cải thiện giao diện thông báo */
    .notification-item {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        border-left: 4px solid #3498db;
        background-color: #f8f9fa;
        transition: all 0.2s ease;
    }

    .notification-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .notification-item:last-child {
        margin-bottom: 0;
    }

    .notification-header {
        margin-bottom: 8px;
    }

    .notification-title {
        font-weight: 600;
        color: #2c3e50;
    }

    .notification-badge {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
        margin-left: 5px;
    }

    .notification-time {
        font-size: 0.8rem;
        color: #7f8c8d;
    }

    .notification-content {
        color: #34495e;
        margin-bottom: 10px;
        font-size: 0.95rem;
    }

    .notification-actions {
        text-align: right;
    }

    .notification-link {
        font-size: 0.85rem;
        color: #3498db;
        text-decoration: none;
        font-weight: 600;
    }

    .notification-link:hover {
        text-decoration: underline;
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
        margin-bottom: 0.5rem;
    }

    .notification-badge {
        font-size: 0.7rem;
        padding: 0.2rem 0.5rem;
        margin-left: 0.5rem;
        vertical-align: middle;
    }

    .notification-actions {
        margin-top: 0.5rem;
    }

    .notification-link {
        color: #3498db;
        font-size: 0.85rem;
        text-decoration: none;
    }

    .notification-link:hover {
        text-decoration: underline;
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

    /* Tối ưu hiệu suất modal */
    .modal-content {
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .modal-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1rem 1.5rem;
    }

    .modal-footer {
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1rem 1.5rem;
    }

    .modal-body {
        padding: 1.5rem;
    }

    /* Tối ưu hiệu suất tooltip */
    .tooltip {
        pointer-events: none;
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
                            <a href="{{ route('barber.appointments.index') }}" class="btn btn-sm btn-outline-light" title="Xem tất cả lịch hẹn">
                                <i class="fas fa-external-link-alt"></i>
                                <span>Xem tất cả</span>
                            </a>
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
                                                            <div class="avatar">
                                                                <i class="fas fa-user"></i>
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
                                                                <span class="service-badge" title="{{ $service->name }}">{{ $service->name }}</span>
                                                            @endforeach
                                                        @else
                                                            <span class="text-muted">Chưa có dịch vụ</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($appointment->status == 'pending')
                                                            <span class="status-badge bg-warning text-dark" title="Đang chờ xác nhận">
                                                                <i class="fas fa-clock me-1"></i>Chờ xác nhận
                                                            </span>
                                                        @elseif($appointment->status == 'confirmed')
                                                            <span class="status-badge bg-primary" title="Đã xác nhận lịch hẹn">
                                                                <i class="fas fa-check me-1"></i>Đã xác nhận
                                                            </span>
                                                        @elseif($appointment->status == 'completed')
                                                            <span class="status-badge bg-success" title="Đã hoàn thành lịch hẹn">
                                                                <i class="fas fa-check-double me-1"></i>Hoàn thành
                                                            </span>
                                                        @elseif($appointment->status == 'canceled')
                                                            <span class="status-badge bg-danger" title="Lịch hẹn đã bị hủy">
                                                                <i class="fas fa-times me-1"></i>Đã hủy
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex">
                                                            <a href="{{ route('barber.appointments.show', $appointment->id) }}" class="btn btn-sm btn-info me-1" title="Xem chi tiết lịch hẹn">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            @if($appointment->status == 'pending')
                                                                <a href="{{ route('barber.appointments.show', $appointment->id) }}" class="btn btn-sm btn-primary me-1" title="Xác nhận lịch hẹn">
                                                                    <i class="fas fa-check-circle"></i>
                                                                </a>
                                                            @elseif($appointment->status == 'confirmed')
                                                                <button type="button" class="btn btn-sm btn-success"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#completeModal"
                                                                    data-appointment-id="{{ $appointment->id }}"
                                                                    data-appointment-route="{{ route('barber.appointments.complete', $appointment->id) }}"
                                                                    title="Đánh dấu hoàn thành">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                            @endif
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
                            @foreach(auth()->user()->unreadNotifications->take(3) as $notification)
                                <div class="notification-item">
                                    <div class="notification-header">
                                        <h6 class="notification-title">
                                            @if(str_contains($notification->type, 'Appointment'))
                                                Lịch hẹn
                                                @if(isset($notification->data['type']) && $notification->data['type'] == 'new')
                                                    <span class="badge bg-primary notification-badge">Mới</span>
                                                @elseif(isset($notification->data['type']) && $notification->data['type'] == 'confirmed')
                                                    <span class="badge bg-success notification-badge">Đã xác nhận</span>
                                                @elseif(isset($notification->data['type']) && $notification->data['type'] == 'canceled')
                                                    <span class="badge bg-danger notification-badge">Đã hủy</span>
                                                @endif
                                            @elseif(str_contains($notification->type, 'Schedule'))
                                                Lịch làm việc
                                                @if(isset($notification->data['status']) && $notification->data['status'] == 'approved')
                                                    <span class="badge bg-success notification-badge">Đã duyệt</span>
                                                @elseif(isset($notification->data['status']) && $notification->data['status'] == 'rejected')
                                                    <span class="badge bg-danger notification-badge">Từ chối</span>
                                                @endif
                                            @else
                                                Thông báo
                                            @endif
                                        </h6>
                                        <span class="notification-time">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</span>
                                    </div>
                                    <p class="notification-content">
                                        {{ $notification->data['message'] ?? 'Không có nội dung' }}
                                    </p>
                                    <div class="notification-actions">
                                        @if(str_contains($notification->type, 'Appointment') && isset($notification->data['appointment_id']))
                                            <a href="{{ route('barber.appointments.show', $notification->data['appointment_id']) }}" class="notification-link">
                                                <i class="fas fa-eye me-1"></i>Xem chi tiết
                                            </a>
                                        @elseif(str_contains($notification->type, 'Schedule'))
                                            <a href="{{ route('barber.schedules.index') }}" class="notification-link">
                                                <i class="fas fa-eye me-1"></i>Xem lịch làm việc
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            @if(auth()->user()->unreadNotifications->count() > 3)
                                <div class="text-center mt-3">
                                    <a href="{{ route('barber.notifications.index') }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-bell me-1"></i> Xem thêm {{ auth()->user()->unreadNotifications->count() - 3 }} thông báo
                                    </a>
                                </div>
                            @endif
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

<!-- Modal xác nhận hoàn thành chung -->
<div class="modal fade" id="completeModal" tabindex="-1" aria-labelledby="completeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="completeModalLabel">Xác nhận hoàn thành lịch hẹn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="completeForm" action="" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Bạn đang chuyển trạng thái lịch hẹn sang "Hoàn thành". Hệ thống sẽ tạo hóa đơn tự động.
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Trạng thái thanh toán:</label>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_status" id="payment-pending" value="pending" checked>
                            <label class="form-check-label" for="payment-pending">
                                <i class="fas fa-clock text-warning me-1"></i> Chưa thanh toán
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_status" id="payment-paid" value="paid">
                            <label class="form-check-label" for="payment-paid">
                                <i class="fas fa-check-circle text-success me-1"></i> Đã thanh toán
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Hủy
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i> Xác nhận
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Xử lý sự kiện khi modal được hiển thị
        $('#completeModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Nút được nhấn
            var appointmentId = button.data('appointment-id'); // Lấy thông tin từ data-* attributes
            var appointmentRoute = button.data('appointment-route');

            console.log("Modal opening for appointment ID:", appointmentId);
            console.log("Route:", appointmentRoute);

            // Cập nhật action của form
            $('#completeForm').attr('action', appointmentRoute);
        });

        // Khởi tạo tooltip
        $('[title]').tooltip({
            placement: 'top',
            trigger: 'hover',
            delay: { show: 300, hide: 0 }
        });

        // Tắt tooltip khi click vào nút
        $('.appointments-table .btn-sm').on('click', function() {
            $(this).tooltip('hide');
        });
    });
</script>
@endsection