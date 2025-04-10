@extends('layouts.admin')

@section('title', 'Chi tiết thợ cắt tóc')

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết thợ cắt tóc</h1>
        <div>
            <a href="{{ route('admin.barbers.edit', $barber->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Chỉnh sửa
            </a>
            <a href="{{ route('admin.barbers.index') }}" class="btn btn-secondary">
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
                    @if($barber->avatar)
                        <img src="{{ asset('storage/' . $barber->avatar) }}" alt="{{ $barber->name }}" class="img-profile rounded-circle mb-3" width="150" height="150">
                    @else
                        <img src="{{ asset('images/default-avatar.jpg') }}" alt="{{ $barber->name }}" class="img-profile rounded-circle mb-3" width="150" height="150">
                    @endif
                    
                    <h4 class="mb-0">{{ $barber->name }}</h4>
                    <p class="text-muted mb-3">Thợ cắt tóc</p>
                    
                    <div class="mb-2">
                        <span class="badge {{ $barber->status ? 'bg-success' : 'bg-danger' }}">
                            {{ $barber->status ? 'Hoạt động' : 'Không hoạt động' }}
                        </span>
                    </div>
                    
                    <div class="mb-3">
                        <i class="fas fa-envelope me-2"></i> {{ $barber->email }}<br>
                        <i class="fas fa-phone me-2"></i> {{ $barber->phone ?? 'Chưa cập nhật' }}<br>
                        <i class="fas fa-map-marker-alt me-2"></i> {{ $barber->address ?? 'Chưa cập nhật' }}
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin chuyên môn</h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="mb-2">Kinh nghiệm</h5>
                        <p>{{ $barber->barber->experience ?? 0 }} năm</p>
                    </div>
                    
                    <div class="mb-4">
                        <h5 class="mb-2">Chuyên môn</h5>
                        <p>{{ $barber->barber->specialties ?? 'Chưa cập nhật' }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <h5 class="mb-2">Tiểu sử</h5>
                        <p>{{ $barber->barber->bio ?? 'Chưa cập nhật' }}</p>
                    </div>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Lịch hẹn gần đây</h6>
                </div>
                <div class="card-body">
                    @if($barber->appointments && $barber->appointments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Khách hàng</th>
                                        <th>Ngày</th>
                                        <th>Giờ</th>
                                        <th>Dịch vụ</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($barber->appointments->take(5) as $appointment)
                                        <tr>
                                            <td>{{ $appointment->id }}</td>
                                            <td>{{ $appointment->user->name }}</td>
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
                            <a href="{{ route('admin.appointments.index', ['barber_id' => $barber->barber->id]) }}" class="btn btn-primary btn-sm">
                                Xem tất cả lịch hẹn
                            </a>
                        </div>
                    @else
                        <p>Không có lịch hẹn nào.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('admin.barbers.edit', $barber->id) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Chỉnh sửa
        </a>
        <a href="{{ route('admin.schedules.index', ['barber_id' => $barber->id]) }}" class="btn btn-info">
            <i class="fas fa-calendar-alt"></i> Quản lý lịch làm việc
        </a>
        <form action="{{ route('admin.barbers.destroy', $barber->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa thợ cắt tóc này?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Xóa
            </button>
        </form>
        <a href="{{ route('admin.barbers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</div>
@endsection 