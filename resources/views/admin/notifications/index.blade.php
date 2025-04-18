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
            </div>
        </div>
        <div class="card-body p-0">
            @if($notifications->count() > 0)
                <div class="list-group">
                    @foreach($notifications as $notification)
                        <div class="notification-item {{ is_null($notification->read_at) ? 'unread' : '' }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    @if($notification->type == 'App\\Notifications\\NewReviewNotification')
                                        <i class="fas fa-star text-warning me-2"></i> Đánh giá mới
                                        @if($notification->data['is_low_rating'])
                                            <span class="badge bg-danger">Cần chú ý</span>
                                        @endif
                                    @else
                                        <i class="fas fa-bell text-primary me-2"></i> Thông báo
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
