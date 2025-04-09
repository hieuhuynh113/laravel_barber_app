@extends('layouts.admin')

@section('title', 'Chi tiết lịch hẹn #' . $appointment->id)

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết lịch hẹn #{{ $appointment->id }}</h1>
        <div>
            <a href="{{ route('admin.appointments.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <a href="{{ route('admin.appointments.edit', $appointment->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Chỉnh sửa
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin lịch hẹn</h6>
                    <div>
                        @if($appointment->status == 'pending')
                            <span class="badge bg-warning">Chờ xác nhận</span>
                        @elseif($appointment->status == 'confirmed')
                            <span class="badge bg-primary">Đã xác nhận</span>
                        @elseif($appointment->status == 'completed')
                            <span class="badge bg-success">Hoàn thành</span>
                        @elseif($appointment->status == 'canceled')
                            <span class="badge bg-danger">Đã hủy</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="font-weight-bold">Thông tin khách hàng</h5>
                            <p><strong>Tên:</strong> {{ $appointment->user->name }}</p>
                            <p><strong>Email:</strong> {{ $appointment->user->email }}</p>
                            <p><strong>Điện thoại:</strong> {{ $appointment->user->phone }}</p>
                            <p><strong>Địa chỉ:</strong> {{ $appointment->user->address ?? 'N/A' }}</p>
                        </div>
                        
                        <div class="col-md-6">
                            <h5 class="font-weight-bold">Thông tin thợ cắt tóc</h5>
                            <p><strong>Tên:</strong> {{ $appointment->barber->user->name }}</p>
                            <p><strong>Email:</strong> {{ $appointment->barber->user->email }}</p>
                            <p><strong>Chuyên môn:</strong> {{ $appointment->barber->specialties ?? 'N/A' }}</p>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="font-weight-bold">Thời gian</h5>
                            <p><strong>Ngày hẹn:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}</p>
                            <p><strong>Giờ hẹn:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}</p>
                            <p><strong>Thời gian tạo:</strong> {{ $appointment->created_at->format('d/m/Y H:i') }}</p>
                            <p><strong>Cập nhật lần cuối:</strong> {{ $appointment->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                        
                        <div class="col-md-6">
                            <h5 class="font-weight-bold">Ghi chú</h5>
                            <p>{{ $appointment->note ?? 'Không có ghi chú' }}</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <h5 class="font-weight-bold">Dịch vụ đã chọn</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Tên dịch vụ</th>
                                            <th>Thời gian</th>
                                            <th class="text-end">Giá</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $total = 0; @endphp
                                        @foreach($appointment->services as $service)
                                            @php $total += $service->price; @endphp
                                            <tr>
                                                <td>{{ $service->name }}</td>
                                                <td>{{ $service->duration }} phút</td>
                                                <td class="text-end">{{ number_format($service->price) }} VNĐ</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2" class="text-end">Tổng cộng:</th>
                                            <th class="text-end">{{ number_format($total) }} VNĐ</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thao tác</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h5 class="font-weight-bold">Cập nhật trạng thái</h5>
                        <div class="list-group">
                            <form action="{{ route('admin.appointments.updateStatus', $appointment->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="pending">
                                <button type="submit" class="list-group-item list-group-item-action {{ $appointment->status == 'pending' ? 'active' : '' }}">
                                    <i class="fas fa-clock me-2"></i> Chờ xác nhận
                                </button>
                            </form>
                            
                            <form action="{{ route('admin.appointments.updateStatus', $appointment->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="confirmed">
                                <button type="submit" class="list-group-item list-group-item-action {{ $appointment->status == 'confirmed' ? 'active' : '' }}">
                                    <i class="fas fa-check me-2"></i> Xác nhận
                                </button>
                            </form>
                            
                            <form action="{{ route('admin.appointments.updateStatus', $appointment->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="list-group-item list-group-item-action {{ $appointment->status == 'completed' ? 'active' : '' }}">
                                    <i class="fas fa-check-double me-2"></i> Hoàn thành
                                </button>
                            </form>
                            
                            <form action="{{ route('admin.appointments.updateStatus', $appointment->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="canceled">
                                <button type="submit" class="list-group-item list-group-item-action {{ $appointment->status == 'canceled' ? 'active' : '' }}">
                                    <i class="fas fa-times me-2"></i> Hủy
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div>
                        <form action="{{ route('admin.appointments.destroy', $appointment->id) }}" method="POST" 
                              onsubmit="return confirm('Bạn có chắc chắn muốn xóa lịch hẹn này không?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block">
                                <i class="fas fa-trash me-2"></i> Xóa lịch hẹn
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 