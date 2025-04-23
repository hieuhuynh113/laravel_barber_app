@extends('layouts.admin')

@section('title', 'Quản lý lịch hẹn')

@section('styles')
<style>
    /* Table layout styles */
    .table {
        table-layout: fixed;
        width: 100%;
    }

    /* Column width definitions */
    .id-cell {
        width: 50px !important;
        min-width: 50px !important;
        max-width: 50px !important;
    }

    .customer-name {
        width: 180px !important;
        min-width: 180px !important;
        max-width: 180px !important;
    }

    .services-cell {
        width: 150px !important;
        min-width: 150px !important;
        max-width: 150px !important;
    }

    .date-cell {
        width: 100px !important;
        min-width: 100px !important;
        max-width: 100px !important;
    }

    .status-cell {
        width: 120px !important;
        min-width: 120px !important;
        max-width: 120px !important;
    }

    .payment-cell {
        width: 140px !important;
        min-width: 140px !important;
        max-width: 140px !important;
    }

    .actions-cell {
        width: 140px !important;
        min-width: 140px !important;
        max-width: 140px !important;
        white-space: nowrap;
        text-align: center;
        position: relative;
        overflow: visible !important;
    }

    .action-buttons {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Styling for action buttons */
    .action-btn {
        width: 32px;
        height: 32px;
        padding: 0;
        margin: 0 2px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: all 0.2s;
    }

    .action-btn i {
        font-size: 0.8rem;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
    }

    /* Status button styling */
    .status-btn {
        width: 32px;
        height: 32px;
        padding: 0;
        margin: 0 2px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: all 0.2s;
    }

    .status-btn i {
        font-size: 0.8rem;
        color: white;
    }

    /* Status menu styling */
    .status-menu {
        position: absolute; /* Sử dụng absolute để định vị trí tương đối với nút */
        min-width: 180px;
        width: 180px;
        padding: 0.5rem 0;
        margin: 0;
        border: 1px solid rgba(0, 0, 0, 0.15);
        border-radius: 0.25rem;
        background-color: #fff;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.175);
        z-index: 99999; /* Tăng z-index lên cao hơn */
        display: none;
        max-height: 200px; /* Giới hạn chiều cao tối đa */
        overflow-y: auto !important; /* Thêm thanh cuộn dọc khi cần */
        overflow-x: hidden !important;
        left: 0; /* Đặt vị trí mặc định */
    }

    .status-menu-item {
        padding: 0.5rem 1rem;
        clear: both;
        font-weight: 400;
        color: #212529;
        text-align: inherit;
        white-space: nowrap;
        background-color: transparent;
        border: 0;
        display: flex;
        align-items: center;
        cursor: pointer;
        width: 100%;
        text-align: left;
        overflow: visible !important;
    }

    .status-menu-item i {
        margin-right: 8px;
        width: 16px;
        text-align: center;
    }

    .status-menu-item.active {
        color: #212529;
        text-decoration: none;
        background-color: rgba(0, 0, 0, 0.075);
        font-weight: 600;
    }

    .status-menu-item:hover {
        background-color: rgba(0, 0, 0, 0.05);
    }

    /* Button colors */
    .btn-warning {
        background-color: #f6c23e;
        border-color: #f6c23e;
        color: #212529;
    }

    .btn-primary {
        background-color: #4e73df;
        border-color: #4e73df;
        color: #fff;
    }

    .btn-success {
        background-color: #1cc88a;
        border-color: #1cc88a;
        color: #fff;
    }

    .btn-danger {
        background-color: #e74a3b;
        border-color: #e74a3b;
        color: #fff;
    }

    /* Cell content styles */
    .table td, .table th {
        padding: 0.75rem;
        vertical-align: middle;
        overflow: hidden;
    }

    /* Đảm bảo cell chứa dropdown menu không bị overflow hidden */
    .table td.actions-cell {
        overflow: visible !important;
    }

    /* Customer name styles */
    .customer-name-link {
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
    }

    .customer-phone {
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
        font-size: 0.8rem;
        color: #6c757d;
    }

    /* Services cell styles */
    .services-cell {
        overflow: hidden;
        text-align: left;
    }

    .service-list {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        margin-bottom: 3px;
    }

    .services-cell .service-badge {
        display: flex;
        align-items: center;
        margin: 2px 0;
        white-space: normal;
        text-align: left;
        word-break: normal;
        padding: 4px 8px;
        font-size: 0.75rem;
        font-weight: 500;
        max-width: 100%;
        border-radius: 6px;
        background-color: #f8f9fa;
        border: 1px solid rgba(0,0,0,0.1);
        color: #495057;
        transition: all 0.2s ease;
    }

    .services-cell .service-badge:hover {
        transform: translateX(2px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .services-cell .service-badge i {
        margin-right: 5px;
        font-size: 0.7rem;
    }

    /* Thêm màu khác nhau cho các loại dịch vụ */
    .services-cell .service-badge[data-type="uon"] {
        border-left: 3px solid #36b9cc;
    }

    .services-cell .service-badge[data-type="uon"] i {
        color: #36b9cc;
    }

    .services-cell .service-badge[data-type="goi"] {
        border-left: 3px solid #1cc88a;
    }

    .services-cell .service-badge[data-type="goi"] i {
        color: #1cc88a;
    }

    .services-cell .service-badge[data-type="nhuom"] {
        border-left: 3px solid #f6c23e;
    }

    .services-cell .service-badge[data-type="nhuom"] i {
        color: #f6c23e;
    }

    .services-cell .service-badge[data-type="other"] {
        border-left: 3px solid #4e73df;
    }

    .services-cell .service-badge[data-type="other"] i {
        color: #4e73df;
    }

    /* Hiển thị tối đa 3 dịch vụ */
    .service-list .service-badge:nth-child(n+4) {
        display: none;
    }

    .services-cell .more-badge {
        background-color: #f8f9fa;
        border: 1px dashed #858796;
        color: #4e73df;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        padding: 3px 8px;
        margin-top: 4px;
        border-radius: 4px;
        transition: all 0.2s;
    }

    .services-cell .more-badge i {
        margin-right: 4px;
        font-size: 0.65rem;
    }

    .services-cell .more-badge:hover {
        background-color: #4e73df;
        color: white;
        border-color: #4e73df;
        transform: translateY(-1px);
    }

    /* Status and payment styles */
    .status-cell {
        text-align: center;
    }

    .status-cell .badge,
    .payment-cell .badge {
        display: inline-block;
        width: auto;
        max-width: 100%;
        white-space: normal;
        padding: 6px 10px;
        font-size: 0.75rem;
        font-weight: 500;
        border-radius: 30px;
    }

    /* Payment cell specific styles */
    .payment-cell {
        text-align: center;
    }

    .payment-cell small {
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
        margin-top: 4px;
        font-size: 0.75rem;
    }

    .payment-cell .badge {
        margin-bottom: 2px;
    }

    .payment-cell small i {
        margin-right: 3px;
        width: 14px;
        text-align: center;
    }

    /* Date cell styles */
    .date-cell {
        text-align: center;
        white-space: nowrap;
    }

    /* Responsive styles */
    @media (max-width: 768px) {
        .actions-cell {
            width: 120px !important;
            min-width: 120px !important;
        }

        .action-btn, .status-btn {
            width: 28px;
            height: 28px;
            margin: 0 1px;
        }

        .action-btn i, .status-btn i {
            font-size: 0.7rem;
        }

        .customer-name {
            width: 150px !important;
            min-width: 150px !important;
        }

        .services-cell {
            width: 120px !important;
            min-width: 120px !important;
        }

        .payment-cell {
            width: 120px !important;
            min-width: 120px !important;
        }
    }

    @media (max-width: 576px) {
        .actions-cell {
            width: 100px !important;
            min-width: 100px !important;
        }

        .action-btn, .status-btn {
            width: 24px;
            height: 24px;
            margin: 0 1px;
        }

        .action-btn i, .status-btn i {
            font-size: 0.65rem;
        }

        .customer-name {
            width: 120px !important;
            min-width: 120px !important;
        }

        .services-cell {
            width: 100px !important;
            min-width: 100px !important;
        }

        .date-cell {
            width: 80px !important;
            min-width: 80px !important;
        }

        .status-cell {
            width: 100px !important;
            min-width: 100px !important;
        }

        .payment-cell {
            width: 100px !important;
            min-width: 100px !important;
        }

        /* Optimize table for small screens */
        .table-responsive {
            overflow-x: auto;
            overflow-y: visible !important;
        }

        .table {
            min-width: 800px; /* Ensure minimum width for scrolling */
        }
    }

    /* Pulse animation for button click */
    @keyframes pulse {
        0% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(0, 0, 0, 0.2);
        }
        70% {
            transform: scale(0.95);
            box-shadow: 0 0 0 5px rgba(0, 0, 0, 0);
        }
        100% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(0, 0, 0, 0);
        }
    }

    .pulse {
        animation: pulse 0.3s ease-in-out;
    }

    /* Custom pagination styles */
    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 1rem;
    }

    .pagination .page-item {
        margin: 0 2px;
    }

    .pagination .page-item .page-link {
        border-radius: 4px;
        padding: 0.4rem 0.75rem;
        color: #4e73df;
        border: 1px solid #dee2e6;
        background-color: #fff;
        font-size: 0.9rem;
        line-height: 1.25;
        text-align: center;
        transition: all 0.2s;
    }

    .pagination .page-item.active .page-link {
        background-color: #4e73df;
        border-color: #4e73df;
        color: white;
    }

    .pagination .page-item .page-link:hover {
        background-color: #eaecf4;
        border-color: #dee2e6;
        color: #224abe;
    }

    .pagination .page-item.disabled .page-link {
        color: #858796;
        pointer-events: none;
        background-color: #fff;
        border-color: #dee2e6;
    }

    /* Ensure pagination arrows are properly sized */
    .pagination .page-link i.fa-sm {
        font-size: 0.7rem;
    }
</style>
@endsection

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
            <div class="table-responsive" style="overflow-y: visible !important; position: relative;">
                <table class="table table-bordered table-hover table-striped" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="id-cell">ID</th>
                            <th class="customer-name">Khách hàng</th>
                            <th class="customer-name">Thợ cắt tóc</th>
                            <th class="services-cell">Dịch vụ</th>
                            <th class="date-cell">Ngày & Giờ</th>
                            <th class="status-cell">Trạng thái</th>
                            <th class="payment-cell">Thanh toán</th>
                            <th class="actions-cell">Quản lý</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointments as $appointment)
                            <tr>
                                <td class="id-cell">{{ $appointment->id }}</td>
                                <td class="customer-name">
                                    @if($appointment->user)
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <a href="{{ route('admin.users.show', $appointment->user->id) }}" class="customer-name-link">
                                                    {{ $appointment->user->name }}
                                                </a>
                                                <span class="customer-phone">{{ $appointment->user->phone ?? 'Không có SĐT' }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">Không xác định</span>
                                    @endif
                                </td>
                                <td class="customer-name">
                                    @if($appointment->barber && $appointment->barber->user)
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <a href="{{ route('admin.users.show', $appointment->barber->user->id) }}" class="customer-name-link">
                                                    {{ $appointment->barber->user->name }}
                                                </a>
                                                @if($appointment->barber->specialty)
                                                    <span class="customer-phone">{{ $appointment->barber->specialty }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">Không xác định</span>
                                    @endif
                                </td>
                                <td class="services-cell">
                                    @php $serviceCount = count($appointment->services); @endphp
                                    @if($serviceCount > 0)
                                        <div class="service-list">
                                            @foreach($appointment->services as $index => $service)
                                                @php
                                                    $serviceType = 'other';
                                                    $serviceIcon = 'fa-cut';

                                                    if (strpos(strtolower($service->name), 'uốn') !== false) {
                                                        $serviceType = 'uon';
                                                        $serviceIcon = 'fa-wind';
                                                    } elseif (strpos(strtolower($service->name), 'gội') !== false) {
                                                        $serviceType = 'goi';
                                                        $serviceIcon = 'fa-shower';
                                                    } elseif (strpos(strtolower($service->name), 'nhuộm') !== false) {
                                                        $serviceType = 'nhuom';
                                                        $serviceIcon = 'fa-palette';
                                                    }
                                                @endphp
                                                <div class="service-badge" data-type="{{ $serviceType }}">
                                                    <i class="fas {{ $serviceIcon }}"></i>
                                                    <span>{{ $service->name }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                        @if($serviceCount > 3)
                                            <div class="more-badge" data-appointment-id="{{ $appointment->id }}" title="Xem tất cả {{ $serviceCount }} dịch vụ">
                                                <i class="fas fa-list-ul"></i> Xem tất cả ({{ $serviceCount }})
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-muted">Không có dịch vụ</span>
                                    @endif
                                </td>
                                <td class="date-cell">
                                    {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}
                                    <br>
                                    <span class="time-cell">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}</span>
                                </td>
                                <td class="status-cell">
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
                                <td class="payment-cell">
                                    <div>
                                        @if($appointment->payment_status == 'paid')
                                            <span class="badge bg-success">Đã thanh toán</span>
                                        @else
                                            <span class="badge bg-warning">Chưa thanh toán</span>
                                        @endif
                                    </div>
                                    <div class="mt-1">
                                        @if($appointment->payment_method == 'cash')
                                            <small><i class="fas fa-money-bill-wave text-success"></i> Tiền mặt</small>
                                        @elseif($appointment->payment_method == 'bank_transfer')
                                            <small><i class="fas fa-university text-primary"></i> Chuyển khoản</small>
                                            @if($appointment->paymentReceipt)
                                                @if($appointment->paymentReceipt->status == 'pending')
                                                    <span class="badge bg-info">Có biên lai</span>
                                                @elseif($appointment->paymentReceipt->status == 'approved')
                                                    <span class="badge bg-success">Đã xác nhận</span>
                                                @elseif($appointment->paymentReceipt->status == 'rejected')
                                                    <span class="badge bg-danger">Đã từ chối</span>
                                                @endif
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                <td class="actions-cell">
                                    <div class="action-buttons">
                                        <div class="d-flex justify-content-center">
                                            <!-- Nút chi tiết -->
                                            <a href="{{ route('admin.appointments.show', $appointment->id) }}" class="btn btn-sm btn-outline-info action-btn" title="Chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <!-- Nút chỉnh sửa -->
                                            <a href="{{ route('admin.appointments.edit', $appointment->id) }}" class="btn btn-sm btn-outline-primary action-btn" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <!-- Nút trạng thái hiện tại và thay đổi trạng thái -->
                                            <div class="status-container" style="position: relative; z-index: 1000;">
                                                <button type="button" class="btn btn-sm status-btn status-toggle-btn {{ $appointment->status == 'pending' ? 'btn-warning' : ($appointment->status == 'confirmed' ? 'btn-primary' : ($appointment->status == 'completed' ? 'btn-success' : 'btn-danger')) }}" data-appointment-id="{{ $appointment->id }}" title="Thay đổi trạng thái">
                                                    @if($appointment->status == 'pending')
                                                        <i class="fas fa-clock"></i>
                                                    @elseif($appointment->status == 'confirmed')
                                                        <i class="fas fa-check"></i>
                                                    @elseif($appointment->status == 'completed')
                                                        <i class="fas fa-check-double"></i>
                                                    @elseif($appointment->status == 'canceled')
                                                        <i class="fas fa-times"></i>
                                                    @endif
                                                </button>
                                            </div>

                                            <!-- Nút xóa -->
                                            <form action="{{ route('admin.appointments.destroy', $appointment->id) }}" method="POST"
                                                  onsubmit="return confirm('Bạn có chắc chắn muốn xóa lịch hẹn này?')" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger action-btn" title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Không có lịch hẹn nào</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $appointments->appends(request()->query())->links('admin.partials.pagination') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Status Menus (outside of table to avoid overflow issues) -->
    @foreach($appointments as $appointment)
        <div class="status-menu" id="status-menu-{{ $appointment->id }}">
            <form action="{{ route('admin.appointments.updateStatus', $appointment->id) }}" method="POST" id="status-form-pending-{{ $appointment->id }}">
                @csrf
                <input type="hidden" name="status" value="pending">
                <button type="button" class="status-menu-item {{ $appointment->status == 'pending' ? 'active' : '' }}" data-form="status-form-pending-{{ $appointment->id }}">
                    <i class="fas fa-clock text-warning"></i> Chờ xác nhận
                </button>
            </form>

            <form action="{{ route('admin.appointments.updateStatus', $appointment->id) }}" method="POST" id="status-form-confirmed-{{ $appointment->id }}">
                @csrf
                <input type="hidden" name="status" value="confirmed">
                <button type="button" class="status-menu-item {{ $appointment->status == 'confirmed' ? 'active' : '' }}" data-form="status-form-confirmed-{{ $appointment->id }}">
                    <i class="fas fa-check text-primary"></i> Xác nhận
                </button>
            </form>

            <form action="{{ route('admin.appointments.updateStatus', $appointment->id) }}" method="POST" id="status-form-completed-{{ $appointment->id }}">
                @csrf
                <input type="hidden" name="status" value="completed">
                <button type="button" class="status-menu-item {{ $appointment->status == 'completed' ? 'active' : '' }}" onclick="showCompletionModal({{ $appointment->id }})">
                    <i class="fas fa-check-double text-success"></i> Hoàn thành
                </button>
            </form>

            <form action="{{ route('admin.appointments.updateStatus', $appointment->id) }}" method="POST" id="status-form-canceled-{{ $appointment->id }}">
                @csrf
                <input type="hidden" name="status" value="canceled">
                <button type="button" class="status-menu-item {{ $appointment->status == 'canceled' ? 'active' : '' }}" data-form="status-form-canceled-{{ $appointment->id }}">
                    <i class="fas fa-times text-danger"></i> Hủy
                </button>
            </form>
        </div>
    @endforeach
    <!-- Modal xác nhận hoàn thành và trạng thái thanh toán -->
    <div class="modal fade" id="completionModal" tabindex="-1" aria-labelledby="completionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="completionModalLabel">Xác nhận hoàn thành lịch hẹn</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="completionForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>Bạn đang chuyển trạng thái lịch hẹn sang "Hoàn thành". Hệ thống sẽ tạo hóa đơn tự động.</p>
                        <p>Vui lòng chọn trạng thái thanh toán:</p>

                        <input type="hidden" name="status" value="completed">

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="payment_status" id="payment-pending" value="pending" checked>
                            <label class="form-check-label" for="payment-pending">
                                Chưa thanh toán
                            </label>
                            <small class="text-muted d-block">Hóa đơn sẽ được tạo với trạng thái "Chưa thanh toán" và có thể chỉnh sửa sau.</small>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_status" id="payment-paid" value="paid">
                            <label class="form-check-label" for="payment-paid">
                                Đã thanh toán
                            </label>
                            <small class="text-muted d-block">Hóa đơn sẽ được tạo với trạng thái "Đã thanh toán".</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-success">Xác nhận</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Thêm tooltip cho các nút
        $('[title]').tooltip({
            placement: 'top',
            trigger: 'hover'
        });

        // Hiệu ứng hover cho các nút
        $('.action-btn, .status-btn').hover(
            function() {
                $(this).addClass('shadow-sm');
            },
            function() {
                $(this).removeClass('shadow-sm');
            }
        );

        // Xử lý hiệu ứng khi nhấn nút
        $('.action-btn, .status-btn').on('click', function() {
            // Tạo hiệu ứng pulse khi nhấn nút
            $(this).addClass('pulse');
            setTimeout(function() {
                $('.pulse').removeClass('pulse');
            }, 300);
        });

        // Xử lý confirm trước khi xóa
        $('.action-buttons form[onsubmit]').on('submit', function(e) {
            var confirmMessage = $(this).attr('onsubmit').replace('return confirm(\'', '').replace('\');', '');
            if (!confirm(confirmMessage)) {
                e.preventDefault();
                return false;
            }
        });

        // Xử lý hiển thị và ẩn menu trạng thái
        $('.status-toggle-btn').on('click', function(e) {
            e.stopPropagation(); // Ngăn chặn sự kiện lan truyền

            var appointmentId = $(this).data('appointment-id');
            var $menu = $('#status-menu-' + appointmentId);
            var $button = $(this);

            // Đóng tất cả các menu đang mở
            $('.status-menu').not($menu).hide();

            // Lấy vị trí của nút
            var buttonPosition = $button.offset();
            var buttonHeight = $button.outerHeight();
            var buttonWidth = $button.outerWidth();

            // Đặt vị trí cho menu luôn hiển thị ngay dưới nút
            $menu.css({
                'top': (buttonPosition.top + buttonHeight) + 'px',
                'left': (buttonPosition.left - 75) + 'px'
            });

            // Hiển thị hoặc ẩn menu hiện tại
            $menu.toggle();
        });

        // Xử lý submit form khi click vào menu item (trừ nút hoàn thành đã có modal riêng)
        $(document).on('click', '.status-menu-item:not([data-bs-toggle="modal"])', function(e) {
            e.preventDefault();
            var formId = $(this).data('form');
            $('#' + formId).submit();
        });

        // Hàm hiển thị modal hoàn thành lịch hẹn
        window.showCompletionModal = function(appointmentId) {
            // Đặt action cho form
            var formAction = $('#status-form-completed-' + appointmentId).attr('action');
            $('#completionForm').attr('action', formAction);

            // Hiển thị modal
            var modal = new bootstrap.Modal(document.getElementById('completionModal'));
            modal.show();

            // Đóng menu dropdown
            $('.status-menu').hide();
        };

        // Đảm bảo modal được hiển thị đúng vị trí
        $('#completionModal').on('shown.bs.modal', function () {
            // Đảm bảo modal hiển thị đúng vị trí
            $(this).css('display', 'block');
            $(this).find('.modal-dialog').css('margin', '10vh auto');
        });

        // Đóng menu khi click ra ngoài
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.status-container').length) {
                $('.status-menu').hide();
            }
        });

        // Ngăn chặn sự kiện click trong menu để không đóng menu khi click vào form
        $(document).on('click', '.status-menu form', function(e) {
            e.stopPropagation();
        });

        // Xử lý hiển thị tất cả dịch vụ khi nhấn vào nút "Xem tất cả"
        $('.more-badge').on('click', function() {
            var appointmentId = $(this).data('appointment-id');
            var $cell = $(this).closest('.services-cell');
            var services = [];

            // Lấy tất cả dịch vụ trong cell với thông tin loại dịch vụ
            $cell.find('.service-badge').each(function() {
                services.push({
                    name: $(this).text(),
                    type: $(this).data('type') || 'other'
                });
            });

            // Tạo nội dung cho modal
            var modalContent = '<div class="list-group service-modal-list">';
            services.forEach(function(service, index) {
                var serviceIcon = 'fa-cut';
                var iconColor = '#4e73df';

                // Xác định biểu tượng và màu sắc dựa trên loại dịch vụ
                if (service.type === 'uon') {
                    serviceIcon = 'fa-wind';
                    iconColor = '#36b9cc';
                } else if (service.type === 'goi') {
                    serviceIcon = 'fa-shower';
                    iconColor = '#1cc88a';
                } else if (service.type === 'nhuom') {
                    serviceIcon = 'fa-palette';
                    iconColor = '#f6c23e';
                }

                modalContent += '<div class="list-group-item service-modal-item">' +
                                '<div class="d-flex align-items-center">' +
                                '<div class="service-icon-wrapper me-3" style="border-left: 3px solid ' + iconColor + '; padding-left: 10px;">' +
                                '<i class="fas ' + serviceIcon + '" style="color: ' + iconColor + ';"></i>' +
                                '</div>' +
                                '<div class="service-details">' +
                                '<div class="service-name fw-medium">' + (index + 1) + '. ' + service.name + '</div>' +
                                '</div>' +
                                '</div>' +
                                '</div>';
            });
            modalContent += '</div>';

            // Thêm CSS cho modal
            modalContent += '<style>' +
                            '.service-modal-list { border-radius: 8px; overflow: hidden; }' +
                            '.service-modal-item { padding: 12px 15px; transition: all 0.2s; }' +
                            '.service-modal-item:hover { background-color: #f8f9fa; }' +
                            '.service-icon-wrapper { display: flex; align-items: center; justify-content: center; width: 30px; }' +
                            '.service-details { flex: 1; }' +
                            '</style>';

            // Tạo và hiển thị modal
            var $modal = $('<div class="modal fade" tabindex="-1" role="dialog">' +
                '<div class="modal-dialog modal-dialog-centered" role="document">' +
                '<div class="modal-content">' +
                '<div class="modal-header" style="background-color: #f8f9fa; border-bottom: 1px solid rgba(0,0,0,0.05);">' +
                '<h5 class="modal-title"><i class="fas fa-list-ul me-2 text-primary"></i>Danh sách dịch vụ</h5>' +
                '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>' +
                '</div>' +
                '<div class="modal-body p-0">' + modalContent + '</div>' +
                '<div class="modal-footer" style="border-top: 1px solid rgba(0,0,0,0.05);">' +
                '<button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i>Đóng</button>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>');

            $('body').append($modal);
            $modal.modal('show');

            // Xóa modal khi đóng
            $modal.on('hidden.bs.modal', function() {
                $(this).remove();
            });
        });

        // Đảm bảo bảng có thể scroll ngang trên thiết bị di động
        if (window.innerWidth <= 768) {
            $('.table-responsive').css('overflow-x', 'auto');
        }
    });
</script>
@endsection