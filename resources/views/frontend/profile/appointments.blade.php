@extends('layouts.frontend')

@section('title', 'Lịch hẹn của tôi')

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-body text-center">
                <img src="{{ $user->avatar ? asset($user->avatar) : asset('images/avatar-placeholder.jpg') }}" alt="{{ $user->name }}" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                <h5 class="card-title">{{ $user->name }}</h5>
                <p class="text-muted">Thành viên từ {{ $user->created_at->format('d/m/Y') }}</p>
                <a href="{{ route('profile.edit') }}" class="btn btn-primary mt-2">Chỉnh sửa hồ sơ</a>
            </div>
        </div>
        
        <div class="list-group mb-4">
            <a href="{{ route('profile.index') }}" class="list-group-item list-group-item-action">
                <i class="fas fa-user me-2"></i> Hồ sơ của tôi
            </a>
            <a href="{{ route('profile.appointments') }}" class="list-group-item list-group-item-action active">
                <i class="fas fa-calendar me-2"></i> Lịch hẹn của tôi
            </a>
            <a href="{{ route('profile.reviews') }}" class="list-group-item list-group-item-action">
                <i class="fas fa-star me-2"></i> Đánh giá của tôi
            </a>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Lịch hẹn của tôi</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Mã lịch hẹn</th>
                                <th>Ngày</th>
                                <th>Giờ</th>
                                <th>Barber</th>
                                <th>Dịch vụ</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($appointments as $appointment)
                            <tr>
                                <td>#{{ $appointment->id }}</td>
                                <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}</td>
                                <td>{{ $appointment->barber->user->name }}</td>
                                <td>
                                    @foreach($appointment->services as $service)
                                    <span class="badge bg-info">{{ $service->name }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    @if($appointment->status == 'pending')
                                    <span class="badge bg-warning">Chờ xác nhận</span>
                                    @elseif($appointment->status == 'confirmed')
                                    <span class="badge bg-success">Đã xác nhận</span>
                                    @elseif($appointment->status == 'completed')
                                    <span class="badge bg-primary">Hoàn thành</span>
                                    @elseif($appointment->status == 'cancelled')
                                    <span class="badge bg-danger">Đã hủy</span>
                                    @endif
                                </td>
                                <td>
                                    @if($appointment->status == 'pending' || $appointment->status == 'confirmed')
                                    <button class="btn btn-sm btn-danger" onclick="confirmCancel({{ $appointment->id }})">Hủy</button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">Bạn chưa có lịch hẹn nào.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{ $appointments->links() }}
                
                <div class="mt-4">
                    <a href="{{ route('appointment.step1') }}" class="btn btn-primary">Đặt lịch mới</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function confirmCancel(appointmentId) {
        if (confirm('Bạn có chắc chắn muốn hủy lịch hẹn này không?')) {
            // Nếu xác nhận, gửi request để hủy lịch hẹn
            // Phần này sẽ được hoàn thiện khi tạo route và controller để hủy lịch hẹn
            alert('Chức năng đang được phát triển');
        }
    }
</script>
@endsection 