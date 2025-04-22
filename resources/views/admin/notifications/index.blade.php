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

                                @else
                                    <p>{{ json_encode($notification->data) }}</p>
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
                                    <a href="{{ route('admin.contacts.edit', $notification->data['contact_id']) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-reply"></i> Phản hồi
                                    </a>
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

                <div class="p-3">
                    {{ $notifications->links() }}
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
