@extends('layouts.admin')

@section('title', 'Quản lý hóa đơn')

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
                        <label for="date_filter">Ngày</label>
                        <input type="date" class="form-control" id="date_filter" value="{{ request('date') }}" onchange="filterByDate(this.value)">
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Mã hóa đơn</th>
                            <th>Khách hàng</th>
                            <th>Ngày tạo</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Phương thức thanh toán</th>
                            <th style="width: 150px">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr>
                                <td>{{ $invoice->invoice_code }}</td>
                                <td>
                                    @if($invoice->user)
                                        <a href="{{ route('admin.users.show', $invoice->user_id) }}">{{ $invoice->user->name }}</a>
                                    @else
                                        {{ $invoice->customer_name ?? 'Khách vãng lai' }}
                                    @endif
                                </td>
                                <td>{{ $invoice->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ number_format($invoice->total_amount) }} VNĐ</td>
                                <td>
                                    @if($invoice->status == 'pending')
                                        <span class="badge bg-warning">Chờ xác nhận</span>
                                    @elseif($invoice->status == 'confirmed')
                                        <span class="badge bg-primary">Đã xác nhận</span>
                                    @elseif($invoice->status == 'completed')
                                        <span class="badge bg-success">Hoàn thành</span>
                                    @elseif($invoice->status == 'canceled')
                                        <span class="badge bg-danger">Đã hủy</span>
                                    @endif
                                </td>
                                <td>
                                    @if($invoice->payment_method == 'cash')
                                        <span class="badge bg-secondary">Tiền mặt</span>
                                    @elseif($invoice->payment_method == 'card')
                                        <span class="badge bg-info">Thẻ</span>
                                    @elseif($invoice->payment_method == 'transfer')
                                        <span class="badge bg-primary">Chuyển khoản</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $invoice->payment_method }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.invoices.edit', $invoice->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.invoices.destroy', $invoice->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa hóa đơn này?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Không có hóa đơn nào.</td>
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

    function filterByDate(date) {
        const urlParams = new URLSearchParams(window.location.search);
        if (date) {
            urlParams.set('date', date);
        } else {
            urlParams.delete('date');
        }
        window.location.href = '{{ route("admin.invoices.index") }}?' + urlParams.toString();
    }
</script>
@endsection 