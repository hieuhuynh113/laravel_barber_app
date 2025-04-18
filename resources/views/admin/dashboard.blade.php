@extends('layouts.admin')

@section('title', 'Tổng quan')

@section('styles')
<style>
    .dashboard-card {
        transition: all 0.3s;
    }
    .dashboard-card:hover {
        transform: translateY(-5px);
    }
    .dashboard-icon {
        transition: all 0.3s;
    }
    .dashboard-card:hover .dashboard-icon {
        color: #4e73df !important;
    }
    .status-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
        text-align: center;
        min-width: 100px;
    }
    .status-pending {
        background-color: #f6c23e;
        color: #fff;
    }
    .status-confirmed {
        background-color: #4e73df;
        color: #fff;
    }
    .status-completed {
        background-color: #1cc88a;
        color: #fff;
    }
    .status-canceled {
        background-color: #e74a3b;
        color: #fff;
    }

    /* Bảng lịch hẹn sắp tới */
    .upcoming-appointments-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #e3e6f0;
    }

    .upcoming-appointments-table th {
        background-color: #f8f9fc;
        font-weight: 600;
        text-align: left;
        padding: 12px 15px;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #4e73df;
        border-bottom: 1px solid #e3e6f0;
        border-right: 1px solid #e3e6f0;
    }

    .upcoming-appointments-table th:last-child {
        border-right: none;
    }

    .upcoming-appointments-table td {
        padding: 12px 15px;
        vertical-align: middle;
        border-bottom: 1px solid #e3e6f0;
        border-right: 1px solid #e3e6f0;
        font-size: 0.9rem;
    }

    .upcoming-appointments-table td:last-child {
        border-right: none;
    }

    .upcoming-appointments-table tr:hover {
        background-color: #f8f9fc;
    }

    /* Cột trong bảng lịch hẹn */
    .col-customer {
        width: 18%;
    }

    .col-barber {
        width: 18%;
    }

    .col-service {
        width: 20%;
    }

    .col-time {
        width: 15%;
        text-align: center;
    }

    .col-status {
        width: 15%;
        text-align: center;
    }

    .col-action {
        width: 80px;
        text-align: center;
    }

    /* Badge dịch vụ */
    .service-badge {
        display: inline-block;
        margin: 2px;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        background-color: #36b9cc;
        color: white;
    }

    /* Text truncate */
    .text-truncate-custom {
        max-width: 100%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
    }

    /* Date display */
    .date-display {
        text-align: center;
    }

    .date-display .date {
        font-weight: 500;
        display: block;
        font-size: 0.9rem;
    }

    .date-display .time {
        color: #6c757d;
        font-size: 0.8rem;
        display: block;
        margin-top: 2px;
    }
    .star-rating {
        color: #f6c23e;
    }
    .review-item {
        border-bottom: 1px solid #e3e6f0;
        padding-bottom: 10px;
        margin-bottom: 10px;
    }
    .review-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
        margin-bottom: 0;
    }
    .review-alert {
        border-left: 4px solid #e74a3b;
    }
    .invoice-item {
        transition: all 0.3s;
        border-radius: 0.25rem;
    }
    .invoice-item:hover {
        background-color: #f8f9fc;
    }
</style>
@endsection

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

    <!-- Thống kê doanh thu -->
    <div class="row mb-4">
        <!-- Doanh thu hôm nay -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Doanh thu hôm nay</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($todayRevenue) }} VNĐ</div>
                            <div class="small text-muted">{{ $todayInvoiceCount }} hóa đơn</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300 dashboard-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Doanh thu tuần này -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Doanh thu tuần này</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($weekRevenue) }} VNĐ</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-week fa-2x text-gray-300 dashboard-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Doanh thu tháng này -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Doanh thu tháng này</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($monthRevenue) }} VNĐ</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300 dashboard-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Doanh thu năm nay -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Doanh thu năm nay</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($yearRevenue) }} VNĐ</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300 dashboard-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Đánh giá tổng quan -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Tổng số đánh giá</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalReviews }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300 dashboard-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Điểm đánh giá trung bình</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 d-flex align-items-center">
                                {{ number_format($averageRating, 1) }}
                                <div class="star-rating ms-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= round($averageRating))
                                            <i class="fas fa-star"></i>
                                        @elseif($i - 0.5 <= $averageRating)
                                            <i class="fas fa-star-half-alt"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300 dashboard-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('admin.reviews.index', ['rating' => 5]) }}" class="text-decoration-none">
                <div class="card border-left-success shadow h-100 py-2 dashboard-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Đánh giá 5 sao</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    @php
                                        $fiveStarCount = \App\Models\Review::where('rating', 5)->count();
                                        $fiveStarPercentage = $totalReviews > 0 ? round(($fiveStarCount / $totalReviews) * 100) : 0;
                                    @endphp
                                    {{ $fiveStarCount }} ({{ $fiveStarPercentage }}%)
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-thumbs-up fa-2x text-gray-300 dashboard-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('admin.reviews.index', ['rating' => 1]) }}" class="text-decoration-none">
                <div class="card border-left-warning shadow h-100 py-2 dashboard-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Đánh giá 1 sao</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    @php
                                        $oneStarCount = \App\Models\Review::where('rating', 1)->count();
                                        $oneStarPercentage = $totalReviews > 0 ? round(($oneStarCount / $totalReviews) * 100) : 0;
                                    @endphp
                                    {{ $oneStarCount }} ({{ $oneStarPercentage }}%)
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-thumbs-down fa-2x text-gray-300 dashboard-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Lịch hẹn và đánh giá -->
    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <!-- Lịch hẹn sắp tới -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold">Lịch hẹn sắp tới</h6>
                    <a href="{{ route('admin.appointments.index') }}" class="btn btn-sm btn-primary">Xem tất cả</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="upcoming-appointments-table">
                            <thead>
                                <tr>
                                    <th class="col-customer">Khách hàng</th>
                                    <th class="col-barber">Thợ cắt tóc</th>
                                    <th class="col-service">Dịch vụ</th>
                                    <th class="col-time">Thời gian</th>
                                    <th class="col-status">Trạng thái</th>
                                    <th class="col-action">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($upcomingAppointments as $appointment)
                                <tr>
                                    <td class="col-customer">
                                        <a href="{{ route('admin.users.show', $appointment->user ? $appointment->user->id : '#') }}" class="text-truncate-custom" title="{{ $appointment->user ? $appointment->user->name : 'Không xác định' }}">
                                            {{ $appointment->user ? $appointment->user->name : 'Không xác định' }}
                                        </a>
                                    </td>
                                    <td class="col-barber">
                                        <a href="{{ route('admin.barbers.show', $appointment->barber && $appointment->barber->user ? $appointment->barber->user->id : '#') }}" class="text-truncate-custom" title="{{ $appointment->barber && $appointment->barber->user ? $appointment->barber->user->name : 'Không xác định' }}">
                                            {{ $appointment->barber && $appointment->barber->user ? $appointment->barber->user->name : 'Không xác định' }}
                                        </a>
                                    </td>
                                    <td class="col-service">
                                        @foreach($appointment->services as $service)
                                            <span class="service-badge">{{ $service->name }}</span>
                                        @endforeach
                                    </td>
                                    <td class="col-time">
                                        <div class="date-display">
                                            <span class="date">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}</span>
                                            <span class="time">{{ $appointment->appointment_time }}</span>
                                        </div>
                                    </td>
                                    <td class="col-status">
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
                                    <td class="col-action">
                                        <a href="{{ route('admin.appointments.show', $appointment->id) }}" class="btn btn-info btn-sm" title="Xem chi tiết">
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

            <!-- Đánh giá gần đây -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold">Đánh giá gần đây</h6>
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-sm btn-primary">Xem tất cả</a>
                </div>
                <div class="card-body">
                    @if($recentReviews->count() > 0)
                        @foreach($recentReviews as $review)
                            <div class="review-item">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ get_user_avatar($review->user, 'small') }}" alt="{{ $review->user->name }}" class="rounded-circle me-2" width="40" height="40">
                                        <div>
                                            <h6 class="mb-0">{{ $review->user->name }}</h6>
                                            <small class="text-muted">{{ $review->created_at->format('d/m/Y H:i') }}</small>
                                        </div>
                                    </div>
                                    <div class="star-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star"></i>
                                        @endfor
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <strong>Dịch vụ:</strong> {{ $review->service->name }} |
                                    <strong>Thợ cắt tóc:</strong> {{ $review->barber->user->name }}
                                </div>
                                <p class="mb-1">{{ Str::limit($review->comment, 100) }}</p>
                                <div class="mt-2">
                                    <a href="{{ route('admin.reviews.show', $review->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-center">Chưa có đánh giá nào.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <!-- Liên kết nhanh -->
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
                            <a href="{{ route('admin.invoices.create') }}" class="btn btn-success btn-icon-split btn-block">
                                <span class="icon text-white-50">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </span>
                                <span class="text">Tạo hóa đơn mới</span>
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
                            <a href="{{ route('admin.services.index') }}" class="btn btn-primary btn-icon-split btn-block">
                                <span class="icon text-white-50">
                                    <i class="fas fa-concierge-bell"></i>
                                </span>
                                <span class="text">Quản lý dịch vụ</span>
                            </a>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <a href="{{ route('admin.reviews.index') }}" class="btn btn-danger btn-icon-split btn-block">
                                <span class="icon text-white-50">
                                    <i class="fas fa-star"></i>
                                </span>
                                <span class="text">Quản lý đánh giá</span>
                            </a>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <a href="{{ route('admin.invoices.statistics') }}" class="btn btn-warning btn-icon-split btn-block">
                                <span class="icon text-white-50">
                                    <i class="fas fa-chart-bar"></i>
                                </span>
                                <span class="text">Thống kê doanh thu</span>
                            </a>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Hóa đơn gần đây -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold">Hóa đơn gần đây</h6>
                    <a href="{{ route('admin.invoices.index') }}" class="btn btn-sm btn-primary">Xem tất cả</a>
                </div>
                <div class="card-body">
                    @if($recentInvoices->count() > 0)
                        @foreach($recentInvoices as $invoice)
                            <div class="invoice-item mb-3 p-3 border-left-success shadow-sm">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <h6 class="mb-0">{{ $invoice->invoice_code }}</h6>
                                        <small class="text-muted">{{ $invoice->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                    <div>
                                        <span class="badge bg-success">{{ number_format($invoice->total) }} VNĐ</span>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <strong>Khách hàng:</strong> {{ $invoice->user->name ?? $invoice->appointment->user->name ?? 'Không xác định' }} <br>
                                    <strong>Thợ cắt tóc:</strong> {{ $invoice->barber->user->name ?? $invoice->appointment->barber->user->name ?? 'Không xác định' }}
                                </div>
                                <div class="mb-2">
                                    <strong>Dịch vụ:</strong>
                                    @if($invoice->services->count() > 0)
                                        @foreach($invoice->services as $service)
                                            <span class="badge bg-info">{{ $service->name }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">Không có dịch vụ</span>
                                    @endif
                                </div>
                                <div class="mt-2">
                                    <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Xem chi tiết
                                    </a>
                                    <a href="{{ route('admin.invoices.print', $invoice->id) }}" class="btn btn-sm btn-secondary" target="_blank">
                                        <i class="fas fa-print"></i> In hóa đơn
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-center">Chưa có hóa đơn nào.</p>
                    @endif
                </div>
            </div>

            <!-- Đánh giá cần chú ý -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-danger text-white">
                    <h6 class="m-0 font-weight-bold text-white">Đánh giá cần chú ý (1-2 sao)</h6>
                </div>
                <div class="card-body">
                    @if($lowRatingReviews->count() > 0)
                        @foreach($lowRatingReviews as $review)
                            <div class="review-item review-alert p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <h6 class="mb-0">{{ $review->user->name }}</h6>
                                        <small class="text-muted">{{ $review->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                    <div class="star-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star"></i>
                                        @endfor
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <strong>Dịch vụ:</strong> {{ $review->service->name }} <br>
                                    <strong>Thợ cắt tóc:</strong> {{ $review->barber->user->name }}
                                </div>
                                <p class="mb-1">{{ Str::limit($review->comment, 100) }}</p>
                                <div class="mt-2">
                                    <a href="{{ route('admin.reviews.edit', $review->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-reply"></i> Phản hồi
                                    </a>
                                    <a href="{{ route('admin.reviews.show', $review->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-center">Không có đánh giá tiêu cực nào. Tốt lắm!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Dịch vụ và thợ cắt tóc được đánh giá cao nhất -->
    <div class="row">
        <!-- Dịch vụ được đánh giá cao nhất -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold">Dịch vụ được đánh giá cao nhất</h6>
                    <a href="{{ route('admin.reviews.statistics') }}" class="btn btn-sm btn-primary">Xem thống kê</a>
                </div>
                <div class="card-body">
                    @if($topServices->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Dịch vụ</th>
                                        <th>Số đánh giá</th>
                                        <th>Điểm trung bình</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topServices as $service)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.services.edit', $service->id) }}?tab=reviews">
                                                    {{ $service->name }}
                                                </a>
                                            </td>
                                            <td>{{ $service->reviews_count }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="me-2">{{ number_format($service->reviews_avg_rating, 1) }}</span>
                                                    <div class="star-rating">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            @if($i <= round($service->reviews_avg_rating))
                                                                <i class="fas fa-star"></i>
                                                            @elseif($i - 0.5 <= $service->reviews_avg_rating)
                                                                <i class="fas fa-star-half-alt"></i>
                                                            @else
                                                                <i class="far fa-star"></i>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center">Chưa có dịch vụ nào được đánh giá.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Thợ cắt tóc được đánh giá cao nhất -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold">Thợ cắt tóc được đánh giá cao nhất</h6>
                    <a href="{{ route('admin.reviews.statistics') }}" class="btn btn-sm btn-primary">Xem thống kê</a>
                </div>
                <div class="card-body">
                    @if($topBarbers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Thợ cắt tóc</th>
                                        <th>Số đánh giá</th>
                                        <th>Điểm trung bình</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topBarbers as $barber)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ get_user_avatar($barber->user, 'small') }}" alt="{{ $barber->user->name }}" class="rounded-circle me-2" width="40" height="40">
                                                    <a href="{{ route('admin.barbers.show', $barber->user->id) }}?tab=reviews">
                                                        {{ $barber->user->name }}
                                                    </a>
                                                </div>
                                            </td>
                                            <td>{{ $barber->reviews_count }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="me-2">{{ number_format($barber->reviews_avg_rating, 1) }}</span>
                                                    <div class="star-rating">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            @if($i <= round($barber->reviews_avg_rating))
                                                                <i class="fas fa-star"></i>
                                                            @elseif($i - 0.5 <= $barber->reviews_avg_rating)
                                                                <i class="fas fa-star-half-alt"></i>
                                                            @else
                                                                <i class="far fa-star"></i>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center">Chưa có thợ cắt tóc nào được đánh giá.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection