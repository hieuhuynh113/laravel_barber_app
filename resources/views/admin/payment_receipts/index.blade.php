@extends('layouts.admin')

@section('title', 'Quản lý biên lai thanh toán')

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
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý biên lai thanh toán</h1>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách biên lai thanh toán</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width: 60px">ID</th>
                            <th style="width: 120px">Mã đặt lịch</th>
                            <th style="width: 180px">Khách hàng</th>
                            <th style="width: 120px" class="date-column">Ngày hẹn</th>
                            <th style="width: 120px">Tổng tiền</th>
                            <th style="width: 140px" class="date-column">Ngày tải lên</th>
                            <th style="width: 120px">Trạng thái</th>
                            <th style="width: 180px">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($receipts as $receipt)
                        <tr>
                            <td class="text-center">{{ $receipt->id }}</td>
                            <td class="text-nowrap">{{ $receipt->appointment->booking_code }}</td>
                            <td class="text-truncate" title="{{ $receipt->appointment->customer_name }}">{{ $receipt->appointment->customer_name }}</td>
                            <td class="text-center date-cell" title="{{ \Carbon\Carbon::parse($receipt->appointment->appointment_date)->format('d/m/Y') }}">
                                <div class="date-part">{{ \Carbon\Carbon::parse($receipt->appointment->appointment_date)->format('d/m/Y') }}</div>
                            </td>
                            <td class="text-nowrap text-end">{{ number_format($receipt->appointment->services->sum('pivot.price')) }} VNĐ</td>
                            <td class="text-center date-cell" title="{{ $receipt->created_at->format('d/m/Y H:i:s') }}">
                                <div class="date-part">{{ $receipt->created_at->format('d/m/Y') }}</div>
                                <div class="time-part">{{ $receipt->created_at->format('H:i') }}</div>
                            </td>
                            <td class="text-center">
                                @if($receipt->status == 'pending')
                                <span class="badge bg-warning">Chờ xác nhận</span>
                                @elseif($receipt->status == 'approved')
                                <span class="badge bg-success">Đã xác nhận</span>
                                @elseif($receipt->status == 'rejected')
                                <span class="badge bg-danger">Đã từ chối</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('admin.payment-receipts.show', $receipt->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> Xem
                                    </a>
                                    @if($receipt->status == 'pending')
                                    <form action="{{ route('admin.payment-receipts.update-status', $receipt->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xác nhận biên lai này?')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.payment-receipts.update-status', $receipt->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn từ chối biên lai này?')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $receipts->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Tắt DataTables để kiểm tra vấn đề
    // $(document).ready(function() {
    //     $('#dataTable').DataTable({
    //         "paging": false,
    //         "ordering": true,
    //         "info": false,
    //         "searching": true,
    //         "columnDefs": [
    //             { "visible": true, "targets": '_all' }
    //         ]
    //     });
    // });
</script>
@endsection
