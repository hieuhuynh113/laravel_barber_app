@extends('layouts.admin')

@section('title', 'Quản lý hóa đơn')

@section('styles')
<style>
    #dataTable td {
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Đảm bảo các nút thao tác không bị chồng lên nhau */
    #dataTable td:last-child {
        white-space: nowrap;
        text-align: center;
    }

    /* Tạo khoảng cách giữa các nút thao tác */
    .gap-1 {
        gap: 0.25rem !important;
    }

    /* Đảm bảo các nút có kích thước đồng nhất */
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        line-height: 1.5;
        border-radius: 0.2rem;
    }

    /* Đảm bảo mã hóa đơn hiển thị đầy đủ */
    #dataTable td:first-child {
        font-weight: 500;
    }

    /* Đảm bảo tiêu đề cột hiển thị đúng */
    #dataTable th {
        vertical-align: middle;
        text-align: center;
        font-size: 0.9rem;
        line-height: 1.3;
        padding: 0.5rem;
        word-wrap: break-word;
        white-space: normal;
    }

    /* CSS đặc biệt cho cột Phương thức thanh toán */
    .payment-method-header {
        font-size: 0.85rem;
        padding: 0.4rem 0.2rem;
    }

    .payment-method-cell {
        padding: 0.5rem 0.25rem;
    }

    /* CSS đặc biệt cho cột Ngày tạo */
    .date-column {
        text-align: center;
    }

    .date-cell {
        padding: 0.5rem 0.25rem !important;
        text-align: center;
    }

    .date-part {
        font-weight: 500;
        font-size: 0.9rem;
        line-height: 1.2;
    }

    .time-part {
        color: #6c757d;
        font-size: 0.85rem;
        margin-top: 2px;
    }

    /* Đảm bảo bảng hiển thị đúng trên các thiết bị di động */
    @media (max-width: 767.98px) {
        .table-responsive {
            overflow-x: auto;
        }

        #dataTable {
            min-width: 900px; /* Đảm bảo bảng không bị co lại quá nhỏ trên thiết bị di động */
        }

        #dataTable th {
            font-size: 0.85rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý hóa đơn</h1>
        <a href="{{ route('admin.invoices.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Tạo hóa đơn mới
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách hóa đơn</h6>
            <form action="{{ route('admin.invoices.index') }}" method="GET" class="d-flex">
                <div class="input-group">
                    <input type="text" class="form-control" name="search" placeholder="Tìm kiếm hóa đơn..." value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="status_filter">Trạng thái</label>
                        <select class="form-select" id="status_filter" onchange="filterByStatus(this.value)">
                            <option value="">Tất cả</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                            <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Đã hủy</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="payment_status_filter">Trạng thái thanh toán</label>
                        <select class="form-select" id="payment_status_filter" onchange="filterByPaymentStatus(this.value)">
                            <option value="">Tất cả</option>
                            <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                            <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Chưa thanh toán</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="date_filter">Ngày</label>
                        <input type="date" class="form-control" id="date_filter" value="{{ request('date') }}" onchange="filterByDate(this.value)">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width: 140px">Mã hóa đơn</th>
                            <th style="width: 180px">Khách hàng</th>
                            <th style="width: 140px" class="date-column">Ngày tạo</th>
                            <th style="width: 120px">Tổng tiền</th>
                            <th style="width: 120px">Trạng thái</th>
                            <th style="width: 120px">Trạng thái<br>thanh toán</th>
                            <th style="width: 150px" class="payment-method-header">Phương thức<br>thanh toán</th>
                            <th style="width: 120px">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr>
                                <td class="text-nowrap">{{ $invoice->invoice_code }}</td>
                                <td class="text-truncate">
                                    @if($invoice->user)
                                        <a href="{{ route('admin.users.show', $invoice->user_id) }}" title="{{ $invoice->user->name }}">{{ $invoice->user->name }}</a>
                                    @else
                                        <span title="{{ $invoice->customer_name ?? 'Khách vãng lai' }}">{{ $invoice->customer_name ?? 'Khách vãng lai' }}</span>
                                    @endif
                                </td>
                                <td class="text-center date-cell" title="{{ $invoice->created_at->format('d/m/Y H:i:s') }}">
                                    <div class="date-part">{{ $invoice->created_at->format('d/m/Y') }}</div>
                                    <div class="time-part">{{ $invoice->created_at->format('H:i') }}</div>
                                </td>
                                <td class="text-nowrap">{{ number_format($invoice->total_amount) }} VNĐ</td>
                                <td class="text-center">
                                    @if($invoice->status == 'pending')
                                        <span class="badge bg-warning">Chờ xác nhận</span>
                                    @elseif($invoice->status == 'confirmed')
                                        <span class="badge bg-primary">Đã xác nhận</span>
                                    @elseif($invoice->status == 'completed')
                                        <span class="badge bg-success">Hoàn thành</span>
                                    @elseif($invoice->status == 'canceled')
                                        <span class="badge bg-danger">Đã hủy</span>
                                        <i class="fas fa-ban text-danger ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Hóa đơn này đã bị hủy"></i>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($invoice->payment_status == 'paid')
                                        <span class="badge bg-success">Đã thanh toán</span>
                                    @else
                                        <span class="badge bg-warning">Chưa thanh toán</span>
                                    @endif
                                </td>
                                <td class="text-center payment-method-cell">
                                    @if($invoice->payment_method == 'cash')
                                        <span class="badge bg-secondary">Tiền mặt</span>
                                    @elseif($invoice->payment_method == 'card')
                                        <span class="badge bg-info">Thẻ</span>
                                    @elseif($invoice->payment_method == 'bank_transfer')
                                        <span class="badge bg-primary">Chuyển khoản</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $invoice->payment_method }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($invoice->status == 'canceled')
                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Hóa đơn đã hủy không thể chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @elseif($invoice->payment_status == 'paid')
                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Hóa đơn đã thanh toán không thể chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @else
                                            <a href="{{ route('admin.invoices.edit', $invoice->id) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        <form action="{{ route('admin.invoices.destroy', $invoice->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" {{ $invoice->payment_status == 'paid' || $invoice->status == 'canceled' ? 'disabled' : '' }} onclick="return confirm('Bạn có chắc chắn muốn xóa hóa đơn này?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Không có hóa đơn nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $invoices->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function filterByStatus(status) {
        const urlParams = new URLSearchParams(window.location.search);
        if (status) {
            urlParams.set('status', status);
        } else {
            urlParams.delete('status');
        }
        window.location.href = '{{ route("admin.invoices.index") }}?' + urlParams.toString();
    }

    function filterByPaymentStatus(paymentStatus) {
        const urlParams = new URLSearchParams(window.location.search);
        if (paymentStatus) {
            urlParams.set('payment_status', paymentStatus);
        } else {
            urlParams.delete('payment_status');
        }
        window.location.href = '{{ route("admin.invoices.index") }}?' + urlParams.toString();
    }

    function filterByDate(date) {
        const urlParams = new URLSearchParams(window.location.search);
        if (date) {
            urlParams.set('date', date);
        } else {
            urlParams.delete('date');
        }
        window.location.href = '{{ route("admin.invoices.index") }}?' + urlParams.toString();
    }

    // Kích hoạt tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection