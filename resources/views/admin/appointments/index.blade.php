@extends('layouts.admin')

@section('title', 'Quản lý lịch hẹn')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0 text-gray-800">Quản lý lịch hẹn</h1>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('admin.appointments.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm lịch hẹn
            </a>
            <a href="{{ route('admin.appointments.calendar') }}" class="btn btn-info">
                <i class="fas fa-calendar-alt"></i> Xem lịch
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Bộ lọc</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.appointments.index') }}" method="GET" class="mb-0">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="status">Trạng thái</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Tất cả trạng thái</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                            <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Đã hủy</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="date">Ngày hẹn</label>
                        <input type="date" name="date" id="date" class="form-control" value="{{ request('date') }}">
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="barber_id">Thợ cắt tóc</label>
                        <select name="barber_id" id="barber_id" class="form-select">
                            <option value="">Tất cả thợ cắt tóc</option>
                            @foreach($barbers as $barber)
                                <option value="{{ $barber->barber->id }}" {{ request('barber_id') == $barber->barber->id ? 'selected' : '' }}>
                                    {{ $barber->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter"></i> Lọc
                        </button>
                        <a href="{{ route('admin.appointments.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Đặt lại
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách lịch hẹn</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Khách hàng</th>
                            <th>Thợ cắt tóc</th>
                            <th>Dịch vụ</th>
                            <th>Ngày & Giờ</th>
                            <th>Trạng thái</th>
                            <th>Tác vụ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointments as $appointment)
                            <tr>
                                <td>{{ $appointment->id }}</td>
                                <td>{{ $appointment->user->name }} <br> <small>{{ $appointment->user->phone }}</small></td>
                                <td>{{ $appointment->barber->user->name }}</td>
                                <td>
                                    @foreach($appointment->services as $service)
                                        <span class="badge bg-primary">{{ $service->name }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}
                                    <br>
                                    {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}
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
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Thao tác
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.appointments.show', $appointment->id) }}">
                                                    <i class="fas fa-eye"></i> Chi tiết
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.appointments.edit', $appointment->id) }}">
                                                    <i class="fas fa-edit"></i> Chỉnh sửa
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            
                                            <!-- Dropdown for changing status -->
                                            <li>
                                                <span class="dropdown-header">Thay đổi trạng thái</span>
                                            </li>
                                            @if($appointment->status != 'pending')
                                                <li>
                                                    <form action="{{ route('admin.appointments.updateStatus', $appointment->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="status" value="pending">
                                                        <button type="submit" class="dropdown-item text-warning">
                                                            <i class="fas fa-clock"></i> Chờ xác nhận
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                            
                                            @if($appointment->status != 'confirmed')
                                                <li>
                                                    <form action="{{ route('admin.appointments.updateStatus', $appointment->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="status" value="confirmed">
                                                        <button type="submit" class="dropdown-item text-primary">
                                                            <i class="fas fa-check"></i> Xác nhận
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                            
                                            @if($appointment->status != 'completed')
                                                <li>
                                                    <form action="{{ route('admin.appointments.updateStatus', $appointment->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="status" value="completed">
                                                        <button type="submit" class="dropdown-item text-success">
                                                            <i class="fas fa-check-double"></i> Hoàn thành
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                            
                                            @if($appointment->status != 'canceled')
                                                <li>
                                                    <form action="{{ route('admin.appointments.updateStatus', $appointment->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="status" value="canceled">
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fas fa-times"></i> Hủy
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                            
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('admin.appointments.destroy', $appointment->id) }}" method="POST" class="d-inline"
                                                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa lịch hẹn này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fas fa-trash"></i> Xóa
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Không có lịch hẹn nào</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $appointments->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 