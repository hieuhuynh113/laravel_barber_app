@extends('layouts.app')

@section('title', 'Thông báo')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/barber-dashboard.css') }}">
<style>
    .notification-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        margin-bottom: 1.5rem;
    }
    
    .notification-card .card-header {
        background-color: #2c3e50;
        color: #fff;
        border-radius: 10px 10px 0 0;
        padding: 1rem 1.5rem;
    }
    
    .notification-card .card-body {
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
    
    .notification-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
    }
    
    .notification-item.unread {
        background-color: #e8f4fd;
        border-left-color: #3498db;
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
    
    .notification-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 0.75rem;
    }
    
    .notification-link {
        color: #3498db;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s;
    }
    
    .notification-link:hover {
        color: #2980b9;
        text-decoration: underline;
    }
    
    .notification-mark-read {
        color: #7f8c8d;
        font-size: 0.85rem;
        text-decoration: none;
        transition: all 0.3s;
    }
    
    .notification-mark-read:hover {
        color: #2c3e50;
    }
    
    .notification-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        flex-shrink: 0;
    }
    
    .icon-appointment {
        background-color: #e8f4fd;
        color: #3498db;
    }
    
    .icon-schedule {
        background-color: #e8f8f5;
        color: #2ecc71;
    }
    
    .icon-review {
        background-color: #fff8e1;
        color: #f39c12;
    }
    
    .notification-wrapper {
        display: flex;
        align-items: flex-start;
    }
    
    .notification-badge {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
        border-radius: 50px;
        margin-left: 0.5rem;
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem 0;
    }
    
    .empty-state i {
        font-size: 4rem;
        color: #bdc3c7;
        margin-bottom: 1rem;
    }
    
    .empty-state h5 {
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }
    
    .empty-state p {
        color: #7f8c8d;
        max-width: 400px;
        margin: 0 auto;
    }
</style>
@endsection

@section('content')
<div class="container dashboard-container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="dashboard-title">Thông báo</h1>
                <form action="{{ route('barber.notifications.mark-all-as-read') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-check-double me-2"></i>Đánh dấu tất cả là đã đọc
                    </button>
                </form>
            </div>
            
            <div class="notification-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Tất cả thông báo</h5>
                    <span class="badge bg-primary">{{ $notifications->total() }} thông báo</span>
                </div>
                <div class="card-body">
                    @if($notifications->count() > 0)
                        @foreach($notifications as $notification)
                            <div class="notification-item {{ is_null($notification->read_at) ? 'unread' : '' }}">
                                <div class="notification-wrapper">
                                    @if(str_contains($notification->type, 'Appointment'))
                                        <div class="notification-icon icon-appointment">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                    @elseif(str_contains($notification->type, 'Schedule'))
                                        <div class="notification-icon icon-schedule">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                    @elseif(str_contains($notification->type, 'Review'))
                                        <div class="notification-icon icon-review">
                                            <i class="fas fa-star"></i>
                                        </div>
                                    @else
                                        <div class="notification-icon">
                                            <i class="fas fa-bell"></i>
                                        </div>
                                    @endif
                                    
                                    <div class="flex-grow-1">
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
                                                @elseif(str_contains($notification->type, 'Review'))
                                                    Đánh giá mới
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
                                            @else
                                                <span></span>
                                            @endif
                                            
                                            @if(is_null($notification->read_at))
                                                <form action="{{ route('barber.notifications.mark-as-read', $notification->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="notification-mark-read btn btn-link p-0">
                                                        <i class="fas fa-check me-1"></i>Đánh dấu đã đọc
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        
                        <!-- Phân trang -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $notifications->links() }}
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-bell-slash"></i>
                            <h5>Không có thông báo nào</h5>
                            <p>Bạn chưa có thông báo nào. Thông báo sẽ xuất hiện khi có lịch hẹn mới hoặc cập nhật từ hệ thống.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
