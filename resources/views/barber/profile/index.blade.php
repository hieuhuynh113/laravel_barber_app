@extends('layouts.app')

@section('title', 'Thông tin cá nhân')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/barber-dashboard.css') }}">
<style>
    .profile-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        margin-bottom: 1.5rem;
    }
    
    .profile-card .card-header {
        background-color: #2c3e50;
        color: #fff;
        border-radius: 10px 10px 0 0;
        padding: 1rem 1.5rem;
    }
    
    .profile-card .card-body {
        padding: 1.5rem;
    }
    
    .profile-image {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid #fff;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        margin-bottom: 1.5rem;
    }
    
    .profile-name {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }
    
    .profile-role {
        color: #3498db;
        font-weight: 600;
        margin-bottom: 1rem;
    }
    
    .profile-status {
        padding: 0.5rem 0.75rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.8rem;
        display: inline-block;
        margin-bottom: 1.5rem;
    }
    
    .status-active {
        background-color: #e8f8f5;
        color: #2ecc71;
        border: 1px solid #2ecc71;
    }
    
    .status-inactive {
        background-color: #fdeeee;
        color: #e74c3c;
        border: 1px solid #e74c3c;
    }
    
    .info-group {
        margin-bottom: 1.5rem;
    }
    
    .info-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }
    
    .info-value {
        color: #34495e;
    }
    
    .action-btn {
        padding: 0.75rem 1.5rem;
        border-radius: 5px;
        margin-right: 0.5rem;
        transition: all 0.3s;
        font-weight: 600;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .stats-card {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 1.25rem;
        margin-bottom: 1.25rem;
        border-left: 4px solid #3498db;
        transition: all 0.3s;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .stats-title {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }
    
    .stats-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #3498db;
    }
</style>
@endsection

@section('content')
<div class="container dashboard-container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h1 class="dashboard-title">Thông tin cá nhân</h1>
            <p class="dashboard-subtitle">Xem và quản lý thông tin cá nhân của bạn</p>
            
            <div class="row">
                <div class="col-md-4">
                    <!-- Thông tin cá nhân -->
                    <div class="profile-card text-center">
                        <div class="card-body">
                            <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/default-avatar.png') }}" alt="{{ $user->name }}" class="profile-image">
                            <div class="profile-name">{{ $user->name }}</div>
                            <div class="profile-role">Thợ cắt tóc</div>
                            <div class="profile-status {{ $user->status ? 'status-active' : 'status-inactive' }}">
                                {{ $user->status ? 'Đang hoạt động' : 'Không hoạt động' }}
                            </div>
                            <div class="d-grid gap-2">
                                <a href="{{ route('barber.profile.edit') }}" class="btn btn-primary action-btn">
                                    <i class="fas fa-edit me-2"></i>Chỉnh sửa thông tin
                                </a>
                                <a href="{{ route('barber.profile.change-password-form') }}" class="btn btn-outline-secondary action-btn">
                                    <i class="fas fa-key me-2"></i>Đổi mật khẩu
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Thống kê -->
                    <div class="profile-card">
                        <div class="card-header">
                            <h5 class="mb-0">Thống kê</h5>
                        </div>
                        <div class="card-body">
                            <div class="stats-card">
                                <div class="stats-title">Kinh nghiệm</div>
                                <div class="stats-value">{{ $user->barber->experience ?? 0 }} năm</div>
                            </div>
                            <div class="stats-card">
                                <div class="stats-title">Lịch hẹn đã hoàn thành</div>
                                <div class="stats-value">{{ $user->barber->appointments()->where('status', 'completed')->count() }}</div>
                            </div>
                            <div class="stats-card">
                                <div class="stats-title">Đánh giá</div>
                                <div class="stats-value">{{ $user->barber->reviews()->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-8">
                    <!-- Thông tin chi tiết -->
                    <div class="profile-card">
                        <div class="card-header">
                            <h5 class="mb-0">Thông tin chi tiết</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-group">
                                        <div class="info-label">Email</div>
                                        <div class="info-value">{{ $user->email }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-group">
                                        <div class="info-label">Số điện thoại</div>
                                        <div class="info-value">{{ $user->phone }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="info-group">
                                <div class="info-label">Địa chỉ</div>
                                <div class="info-value">{{ $user->address ?: 'Chưa cập nhật' }}</div>
                            </div>
                            <div class="info-group">
                                <div class="info-label">Chuyên môn</div>
                                <div class="info-value">{{ $user->barber->specialty ?: 'Chưa cập nhật' }}</div>
                            </div>
                            <div class="info-group">
                                <div class="info-label">Mô tả</div>
                                <div class="info-value">{{ $user->barber->description ?: 'Chưa cập nhật' }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Lịch làm việc -->
                    <div class="profile-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Lịch làm việc</h5>
                            <a href="{{ route('barber.schedules.index') }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-calendar-alt me-1"></i>Xem chi tiết
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Ngày</th>
                                            <th>Giờ làm việc</th>
                                            <th>Trạng thái</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($user->barber->schedules()->orderBy('day_of_week')->get() as $schedule)
                                            <tr>
                                                <td>{{ $schedule->day_name }}</td>
                                                <td>
                                                    @if($schedule->is_day_off)
                                                        <span class="text-danger">Ngày nghỉ</span>
                                                    @else
                                                        {{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($schedule->is_day_off)
                                                        <span class="badge bg-danger">Nghỉ</span>
                                                    @else
                                                        <span class="badge bg-success">Làm việc</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
