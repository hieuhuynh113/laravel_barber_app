@extends('layouts.admin')

@section('title', 'Chi tiết người dùng')

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết người dùng</h1>
        <div>
            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Chỉnh sửa
            </a>
            <a href="{{ route('admin.users.index', ['role' => $user->role]) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin cá nhân</h6>
                </div>
                <div class="card-body text-center">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="rounded-circle mb-3" width="150" height="150">
                    @else
                        <img src="{{ asset('images/default-avatar.jpg') }}" alt="{{ $user->name }}" class="rounded-circle mb-3" width="150" height="150">
                    @endif
                    
                    <h4 class="mb-0">{{ $user->name }}</h4>
                    <p class="text-muted mb-3">
                        @if($user->role == 'admin')
                            <span class="badge bg-danger">Quản trị viên</span>
                        @elseif($user->role == 'barber')
                            <span class="badge bg-primary">Thợ cắt tóc</span>
                        @else
                            <span class="badge bg-info">Khách hàng</span>
                        @endif
                    </p>
                    
                    <div class="mb-2">
                        <span class="badge bg-{{ $user->status ? 'success' : 'danger' }}">
                            {{ $user->status ? 'Hoạt động' : 'Không hoạt động' }}
                        </span>
                    </div>
                    
                    <div class="mb-3 text-start">
                        <p><i class="fas fa-envelope me-2"></i> {{ $user->email }}</p>
                        <p><i class="fas fa-phone me-2"></i> {{ $user->phone ?? 'Chưa cập nhật' }}</p>
                        <p><i class="fas fa-map-marker-alt me-2"></i> {{ $user->address ?? 'Chưa cập nhật' }}</p>
                        <p><i class="fas fa-calendar me-2"></i> Ngày tham gia: {{ $user->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            @if($user->role === 'barber' && $user->barber)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin thợ cắt tóc</h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="mb-2">Kinh nghiệm</h5>
                        <p>{{ $user->barber->experience ?? 0 }} năm</p>
                    </div>
                    
                    <div class="mb-4">
                        <h5 class="mb-2">Chuyên môn</h5>
                        <p>{{ $user->barber->specialties ?? 'Chưa cập nhật' }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <h5 class="mb-2">Tiểu sử</h5>
                        <p>{{ $user->barber->bio ?? 'Chưa cập nhật' }}</p>
                    </div>
                </div>
            </div>
            @endif
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Lịch hẹn gần đây</h6>
                </div>
                <div class="card-body">
                    @if($user->appointments && $user->appointments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Thợ cắt tóc</th>
                                        <th>Ngày</th>
                                        <th>Giờ</th>
                                        <th>Dịch vụ</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->appointments->take(5) as $appointment)
                                        <tr>
                                            <td>{{ $appointment->id }}</td>
                                            <td>{{ $appointment->barber->user->name ?? 'N/A' }}</td>
                                            <td>{{ date('d/m/Y', strtotime($appointment->appointment_date)) }}</td>
                                            <td>{{ $appointment->appointment_time }}</td>
                                            <td>
                                                @foreach($appointment->services as $service)
                                                    <span class="badge bg-secondary">{{ $service->name }}</span>
                                                @endforeach
                                            </td>
                                            <td>
                                                @if($appointment->status == 'pending')
                                                    <span class="badge bg-warning">Chờ xác nhận</span>
                                                @elseif($appointment->status == 'confirmed')
                                                    <span class="badge bg-primary">Đã xác nhận</span>
                                                @elseif($appointment->status == 'completed')
                                                    <span class="badge bg-success">Hoàn thành</span>
                                                @elseif($appointment->status == 'canceled')
                                                    <span class="badge bg-danger">Đã hủy</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end">
                            <a href="{{ route('admin.appointments.index', ['user_id' => $user->id]) }}" class="btn btn-primary btn-sm">
                                Xem tất cả lịch hẹn
                            </a>
                        </div>
                    @else
                        <p>Không có lịch hẹn nào.</p>
                    @endif
                </div>
            </div>
            
            @if($user->role === 'customer' && isset($user->reviews) && $user->reviews->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Đánh giá gần đây</h6>
                </div>
                <div class="card-body">
                    @foreach($user->reviews->take(3) as $review)
                        <div class="review-item border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <span class="fw-bold">{{ $review->service->name ?? 'Dịch vụ' }}</span>
                                    <span class="text-muted ms-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-secondary' }}"></i>
                                        @endfor
                                    </span>
                                </div>
                                <small class="text-muted">{{ $review->created_at->format('d/m/Y') }}</small>
                            </div>
                            <p class="mb-1">{{ $review->comment }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection 