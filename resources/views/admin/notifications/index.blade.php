@extends('layouts.admin')

@section('title', 'Thông báo')

@section('styles')
<style>
    .notification-item {
        border-bottom: 1px solid #e3e6f0;
        padding: 15px;
        transition: all 0.3s;
    }
    .notification-item:hover {
        background-color: #f8f9fc;
    }
    .notification-item.unread {
        background-color: #eef5ff;
    }
    .notification-item:last-child {
        border-bottom: none;
    }
    .notification-time {
        font-size: 0.8rem;
        color: #858796;
    }
    .notification-content {
        margin-top: 5px;
    }
    .notification-actions {
        margin-top: 10px;
    }
    .star-rating {
        color: #f6c23e;
    }
    .low-rating {
        color: #e74a3b;
        font-weight: bold;
    }
    .notification-icon {
        display: inline-flex;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
    }
    .icon-review {
        background-color: #fff3cd;
        color: #f6c23e;
    }
    .icon-appointment {
        background-color: #d1e7dd;
        color: #198754;
    }
    .icon-payment {
        background-color: #e2d4f0;
        color: #6f42c1;
    }
    .icon-contact {
        background-color: #cfe2ff;
        color: #0d6efd;
    }
    .notification-badge {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        margin-left: 0.5rem;
    }

    /* Custom pagination styles */
    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 1rem;
    }

    .pagination .page-item {
        margin: 0 2px;
    }

    .pagination .page-item .page-link {
        border-radius: 4px;
        padding: 0.4rem 0.75rem;
        color: #4e73df;
        border: 1px solid #dee2e6;
        background-color: #fff;
        font-size: 0.9rem;
        line-height: 1.25;
        text-align: center;
        transition: all 0.2s;
    }

    .pagination .page-item.active .page-link {
        background-color: #4e73df;
        border-color: #4e73df;
        color: white;
    }

    .pagination .page-item .page-link:hover {
        background-color: #eaecf4;
        border-color: #dee2e6;
        color: #224abe;
    }

    .pagination .page-item.disabled .page-link {
        color: #858796;
        pointer-events: none;
        background-color: #fff;
        border-color: #dee2e6;
    }

    /* Ensure pagination arrows are properly sized */
    .pagination .page-link i.fa-sm {
        font-size: 0.7rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thông báo</h1>
        <div>
            <form action="{{ route('admin.notifications.markAllAsRead') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check-double"></i> Đánh dấu tất cả đã đọc
                </button>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách thông báo</h6>
            <div>
                <a href="{{ route('admin.notifications.index', ['filter' => 'all']) }}" class="btn btn-sm {{ request('filter') == 'all' || !request('filter') ? 'btn-primary' : 'btn-outline-primary' }}">
                    Tất cả
                </a>
                <a href="{{ route('admin.notifications.index', ['filter' => 'unread']) }}" class="btn btn-sm {{ request('filter') == 'unread' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Chưa đọc
                </a>
                <a href="{{ route('admin.notifications.index', ['filter' => 'reviews']) }}" class="btn btn-sm {{ request('filter') == 'reviews' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Đánh giá
                </a>
                <a href="{{ route('admin.notifications.index', ['filter' => 'appointments']) }}" class="btn btn-sm {{ request('filter') == 'appointments' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Lịch hẹn
                </a>
                <a href="{{ route('admin.notifications.index', ['filter' => 'payments']) }}" class="btn btn-sm {{ request('filter') == 'payments' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Thanh toán
                </a>
                <a href="{{ route('admin.notifications.index', ['filter' => 'contacts']) }}" class="btn btn-sm {{ request('filter') == 'contacts' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Liên hệ
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            @if($notifications->count() > 0)
                <div class="list-group">
                    @foreach($notifications as $notification)
                        <div class="notification-item {{ is_null($notification->read_at) ? 'unread' : '' }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 d-flex align-items-center">
                                    @if($notification->type == 'App\\Notifications\\NewReviewNotification')
                                        <span class="notification-icon icon-review">
                                            <i class="fas fa-star"></i>
                                        </span>
                                        Đánh giá mới
                                        @if($notification->data['is_low_rating'])
                                            <span class="badge bg-danger notification-badge">Cần chú ý</span>
                                        @endif
                                    @elseif($notification->type == 'App\\Notifications\\NewAppointmentNotification')
                                        <span class="notification-icon icon-appointment">
                                            <i class="fas fa-calendar-alt"></i>
                                        </span>
                                        Lịch hẹn mới
                                        @if(isset($notification->data['is_urgent']) && $notification->data['is_urgent'])
                                            <span class="badge bg-warning notification-badge">Gấp</span>
                                        @endif
                                    @elseif($notification->type == 'App\\Notifications\\NewPaymentNotification')
                                        <span class="notification-icon icon-payment">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </span>
                                        Thanh toán mới
                                    @elseif($notification->type == 'App\\Notifications\\NewContactNotification')
                                        <span class="notification-icon icon-contact">
                                            <i class="fas fa-envelope"></i>
                                        </span>
                                        Liên hệ mới
                                    @elseif($notification->type == 'App\\Notifications\\AppointmentCanceledNotification')
                                        <span class="notification-icon" style="background-color: #f8d7da; color: #dc3545;">
                                            <i class="fas fa-calendar-times"></i>
                                        </span>
                                        Lịch hẹn đã hủy
                                    @elseif($notification->type == 'App\\Notifications\\ScheduleChangeRequestNotification')
                                        <span class="notification-icon" style="background-color: #e0f2f1; color: #009688;">
                                            <i class="fas fa-clock"></i>
                                        </span>
                                        Yêu cầu thay đổi lịch
                                        @if($notification->data['status'] == 'pending')
                                            <span class="badge bg-warning notification-badge">Chờ xử lý</span>
                                        @endif
                                    @else
                                        <span class="notification-icon">
                                            <i class="fas fa-bell text-primary"></i>
                                        </span>
                                        Thông báo
                                    @endif
                                </h6>
                                <span class="notification-time">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</span>
                            </div>

                            <div class="notification-content">
                                @if($notification->type == 'App\\Notifications\\NewReviewNotification')
                                    <p>
                                        <strong>{{ $notification->data['user_name'] }}</strong> đã đánh giá
                                        <strong>{{ $notification->data['rating'] }}</strong> sao cho dịch vụ
                                        <strong>{{ $notification->data['service_name'] }}</strong>
                                        (Thợ cắt tóc: {{ $notification->data['barber_name'] }})
                                    </p>
                                    <div class="star-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="{{ $i <= $notification->data['rating'] ? 'fas' : 'far' }} fa-star"></i>
                                        @endfor
                                    </div>
                                    <p class="mt-2">{{ Str::limit($notification->data['comment'], 100) }}</p>

                                @elseif($notification->type == 'App\\Notifications\\NewAppointmentNotification')
                                    <p>
                                        <strong>{{ $notification->data['user_name'] }}</strong> đã đặt lịch hẹn
                                        vào <strong>{{ \Carbon\Carbon::parse($notification->data['appointment_date'])->format('d/m/Y') }}</strong>
                                        lúc <strong>{{ $notification->data['appointment_time'] }}</strong>
                                        với thợ cắt tóc <strong>{{ $notification->data['barber_name'] }}</strong>
                                    </p>
                                    <p class="mt-2">Dịch vụ:
                                        @foreach($notification->data['services'] as $service)
                                            <span class="badge bg-light text-dark">{{ $service['name'] }}</span>
                                        @endforeach
                                    </p>
                                    <p>Trạng thái: <span class="badge bg-{{ $notification->data['status'] == 'pending' ? 'warning' : ($notification->data['status'] == 'confirmed' ? 'success' : 'danger') }}">{{ $notification->data['status'] == 'pending' ? 'Chờ xác nhận' : ($notification->data['status'] == 'confirmed' ? 'Đã xác nhận' : 'Hủy') }}</span></p>

                                @elseif($notification->type == 'App\\Notifications\\NewPaymentNotification')
                                    <p>
                                        <strong>{{ $notification->data['user_name'] }}</strong> đã gửi biên lai thanh toán
                                        cho lịch hẹn <strong>#{{ $notification->data['booking_code'] }}</strong>
                                    </p>
                                    <p>Ngày hẹn: <strong>{{ \Carbon\Carbon::parse($notification->data['appointment_date'])->format('d/m/Y') }}</strong> lúc <strong>{{ $notification->data['appointment_time'] }}</strong></p>
                                    <p>Tổng tiền: <strong>{{ number_format($notification->data['total_amount']) }} VNĐ</strong></p>

                                @elseif($notification->type == 'App\\Notifications\\NewContactNotification')
                                    <p>
                                        <strong>{{ $notification->data['name'] }}</strong> đã gửi tin nhắn liên hệ mới
                                    </p>
                                    <p>Email: <strong>{{ $notification->data['email'] }}</strong></p>
                                    <p>Chủ đề: <strong>{{ $notification->data['subject'] }}</strong></p>
                                    <p class="mt-2">{{ Str::limit($notification->data['message'], 100) }}</p>

                                @elseif($notification->type == 'App\\Notifications\\AppointmentCanceledNotification')
                                    <p>
                                        <strong>{{ $notification->data['user_name'] }}</strong> đã hủy lịch hẹn
                                    </p>
                                    <p>Mã đặt lịch: <strong>{{ $notification->data['booking_code'] }}</strong></p>
                                    <p>Ngày hẹn: <strong>{{ \Carbon\Carbon::parse($notification->data['appointment_date'])->format('d/m/Y') }}</strong></p>
                                    <p>Giờ hẹn: <strong>{{ $notification->data['appointment_time'] }}</strong></p>
                                    <p>Thợ cắt tóc: <strong>{{ $notification->data['barber_name'] }}</strong></p>
                                    <p>Thời gian hủy: <strong>{{ $notification->data['canceled_at'] }}</strong></p>
                                    <p>Dịch vụ:
                                        @if(isset($notification->data['services']) && is_array($notification->data['services']))
                                            @foreach($notification->data['services'] as $service)
                                                <span class="badge bg-info">{{ $service['name'] }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">Không có thông tin dịch vụ</span>
                                        @endif
                                    </p>

                                @elseif($notification->type == 'App\\Notifications\\ScheduleChangeRequestNotification')
                                    <p>
                                        <strong>{{ $notification->data['barber_name'] }}</strong> đã gửi yêu cầu thay đổi
                                        @if($notification->data['is_day_off'])
                                            <strong>ngày nghỉ</strong> vào ngày <strong>{{ $notification->data['day_name'] }}</strong>
                                        @else
                                            <strong>lịch làm việc</strong> vào ngày <strong>{{ $notification->data['day_name'] }}</strong>
                                            từ <strong>{{ $notification->data['start_time'] }}</strong> đến <strong>{{ $notification->data['end_time'] }}</strong>
                                        @endif
                                    </p>
                                    <p class="mt-2">
                                        <strong>Lý do:</strong> {{ $notification->data['reason'] }}
                                    </p>
                                    <p class="mt-2">
                                        <strong>Trạng thái:</strong>
                                        @if($notification->data['status'] == 'pending')
                                            <span class="badge bg-warning">Đang chờ</span>
                                        @elseif($notification->data['status'] == 'approved')
                                            <span class="badge bg-success">Đã phê duyệt</span>
                                        @elseif($notification->data['status'] == 'rejected')
                                            <span class="badge bg-danger">Đã từ chối</span>
                                        @endif
                                    </p>
                                @else
                                    <p>{{ $notification->data['message'] ?? json_encode($notification->data) }}</p>
                                @endif
                            </div>

                            <div class="notification-actions">
                                @if($notification->type == 'App\\Notifications\\NewReviewNotification')
                                    <a href="{{ route('admin.reviews.show', $notification->data['review_id']) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Xem chi tiết
                                    </a>
                                    @if($notification->data['is_low_rating'])
                                        <a href="{{ route('admin.reviews.edit', $notification->data['review_id']) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-reply"></i> Phản hồi
                                        </a>
                                    @endif

                                @elseif($notification->type == 'App\\Notifications\\NewAppointmentNotification')
                                    <a href="{{ route('admin.appointments.show', $notification->data['appointment_id']) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Xem chi tiết
                                    </a>
                                    <a href="{{ route('admin.appointments.edit', $notification->data['appointment_id']) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-check"></i> Xác nhận
                                    </a>

                                @elseif($notification->type == 'App\\Notifications\\NewPaymentNotification')
                                    <a href="{{ route('admin.payment-receipts.show', $notification->data['receipt_id']) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Xem biên lai
                                    </a>
                                    <a href="{{ route('admin.appointments.show', $notification->data['appointment_id']) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-check-circle"></i> Xác nhận thanh toán
                                    </a>

                                @elseif($notification->type == 'App\\Notifications\\NewContactNotification')
                                    <a href="{{ route('admin.contacts.show', $notification->data['contact_id']) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Xem tin nhắn
                                    </a>
                                    <a href="{{ route('admin.contacts.show', $notification->data['contact_id']) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-reply"></i> Phản hồi
                                    </a>
                                @elseif($notification->type == 'App\\Notifications\\AppointmentCanceledNotification')
                                    <a href="{{ route('admin.appointments.show', $notification->data['appointment_id']) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Xem chi tiết lịch hẹn
                                    </a>
                                @elseif($notification->type == 'App\\Notifications\\ScheduleChangeRequestNotification')
                                    <a href="{{ route('admin.schedule-requests.show', $notification->data['request_id']) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Xem chi tiết
                                    </a>
                                    @if($notification->data['status'] == 'pending')
                                        <a href="{{ route('admin.schedule-requests.show', $notification->data['request_id']) }}" class="btn btn-sm btn-success">
                                            <i class="fas fa-check"></i> Xử lý yêu cầu
                                        </a>
                                    @endif
                                @endif

                                @if(is_null($notification->read_at))
                                    <form action="{{ route('admin.notifications.markAsRead', $notification->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-check"></i> Đánh dấu đã đọc
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-3 p-3">
                    {{ $notifications->appends(request()->query())->links('admin.partials.pagination') }}
                </div>
            @else
                <div class="p-4 text-center">
                    <p>Không có thông báo nào.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
