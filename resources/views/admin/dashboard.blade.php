@extends('layouts.admin')

@section('title', 'Tổng quan')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Tổng quan hệ thống</h2>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Lịch hẹn hôm nay</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $todayAppointments }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300 dashboard-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Lịch hẹn chờ xác nhận</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingAppointments }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300 dashboard-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Tổng số khách hàng</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $customers }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300 dashboard-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Tin nhắn chưa đọc</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $unreadMessages }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope fa-2x text-gray-300 dashboard-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold">Lịch hẹn sắp tới</h6>
                    <a href="{{ route('admin.appointments.index') }}" class="btn btn-sm btn-primary">Xem tất cả</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Khách hàng</th>
                                    <th>Thợ cắt tóc</th>
                                    <th>Dịch vụ</th>
                                    <th>Thời gian</th>
                                    <th>Trạng thái</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($upcomingAppointments as $appointment)
                                <tr>
                                    <td>{{ $appointment->user->name }}</td>
                                    <td>{{ $appointment->barber->user->name }}</td>
                                    <td>
                                        @foreach($appointment->services as $service)
                                            <span class="badge bg-info">{{ $service->name }}</span>
                                        @endforeach
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }} {{ $appointment->appointment_time }}</td>
                                    <td>
                                        @if($appointment->status == 'pending')
                                            <span class="status-badge status-pending">Chờ xác nhận</span>
                                        @elseif($appointment->status == 'confirmed')
                                            <span class="status-badge status-confirmed">Đã xác nhận</span>
                                        @elseif($appointment->status == 'completed')
                                            <span class="status-badge status-completed">Hoàn thành</span>
                                        @elseif($appointment->status == 'canceled')
                                            <span class="status-badge status-canceled">Đã hủy</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.appointments.show', $appointment->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Không có lịch hẹn nào sắp tới</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold">Liên kết nhanh</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col">
                            <a href="{{ route('admin.appointments.create') }}" class="btn btn-primary btn-icon-split btn-block">
                                <span class="icon text-white-50">
                                    <i class="fas fa-calendar-plus"></i>
                                </span>
                                <span class="text">Tạo lịch hẹn mới</span>
                            </a>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <a href="{{ route('admin.barbers.index') }}" class="btn btn-info btn-icon-split btn-block">
                                <span class="icon text-white-50">
                                    <i class="fas fa-cut"></i>
                                </span>
                                <span class="text">Quản lý thợ cắt tóc</span>
                            </a>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <a href="{{ route('admin.services.index') }}" class="btn btn-success btn-icon-split btn-block">
                                <span class="icon text-white-50">
                                    <i class="fas fa-concierge-bell"></i>
                                </span>
                                <span class="text">Quản lý dịch vụ</span>
                            </a>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <a href="{{ route('admin.news.create') }}" class="btn btn-warning btn-icon-split btn-block">
                                <span class="icon text-white-50">
                                    <i class="fas fa-newspaper"></i>
                                </span>
                                <span class="text">Đăng tin tức mới</span>
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary btn-icon-split btn-block">
                                <span class="icon text-white-50">
                                    <i class="fas fa-cogs"></i>
                                </span>
                                <span class="text">Cài đặt hệ thống</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 