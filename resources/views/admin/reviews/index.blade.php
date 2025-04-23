@extends('layouts.admin')

@section('title', 'Quản lý đánh giá')

@section('styles')
<style>
    /* Styling cho đánh giá sao */
    .star-rating {
        color: #ffc107;
        font-size: 14px;
        white-space: nowrap;
    }

    /* Styling cho trạng thái */
    .review-status {
        width: 10px;
        height: 10px;
        display: inline-block;
        border-radius: 50%;
        margin-right: 5px;
    }
    .status-active {
        background-color: #28a745;
    }
    .status-inactive {
        background-color: #dc3545;
    }

    /* Card styling */
    .filter-card {
        margin-bottom: 20px;
        border-radius: 8px;
    }

    /* Bảng đánh giá */
    .reviews-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #e3e6f0;
    }

    .reviews-table th {
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

    .reviews-table th:last-child {
        border-right: none;
    }

    .reviews-table td {
        padding: 12px 15px;
        vertical-align: middle;
        border-bottom: 1px solid #e3e6f0;
        border-right: 1px solid #e3e6f0;
        font-size: 0.9rem;
    }

    .reviews-table td:last-child {
        border-right: none;
    }

    .reviews-table tr:hover {
        background-color: #f8f9fc;
    }

    .reviews-table thead {
        border-bottom: 2px solid #e3e6f0;
    }

    /* Cột ID */
    .col-id {
        width: 60px;
        text-align: center;
        background-color: #f8f9fc;
    }

    /* Cột khách hàng */
    .col-customer {
        width: 15%;
    }

    /* Cột dịch vụ */
    .col-service {
        width: 15%;
    }

    /* Cột thợ cắt tóc */
    .col-barber {
        width: 15%;
    }

    /* Cột đánh giá */
    .col-rating {
        width: 10%;
        text-align: center;
    }

    /* Cột nhận xét */
    .col-comment {
        width: 20%;
    }

    /* Cột trạng thái */
    .col-status {
        width: 10%;
        text-align: center;
    }

    /* Cột ngày tạo */
    .col-date {
        width: 10%;
        text-align: center;
    }

    /* Cột thao tác */
    .col-actions {
        width: 120px;
        text-align: center;
        background-color: #f8f9fc;
    }

    /* Truncate text */
    .text-truncate-custom {
        max-width: 100%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
    }

    /* Ngày giờ */
    .date-display {
        text-align: center;
    }

    .date-display .date {
        font-weight: 500;
        display: block;
    }

    .date-display .time {
        color: #6c757d;
        font-size: 0.8rem;
        display: block;
        margin-top: 2px;
    }

    /* Nút thao tác */
    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 5px;
    }

    .action-buttons .btn {
        width: 32px;
        height: 32px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
    }

    .action-buttons .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
    }

    .action-buttons .btn-info {
        background-color: #36b9cc;
        border-color: #36b9cc;
    }

    .action-buttons .btn-primary {
        background-color: #4e73df;
        border-color: #4e73df;
    }

    .action-buttons .btn-warning {
        background-color: #f6c23e;
        border-color: #f6c23e;
    }

    .action-buttons .btn-success {
        background-color: #1cc88a;
        border-color: #1cc88a;
    }

    .action-buttons .btn-danger {
        background-color: #e74a3b;
        border-color: #e74a3b;
    }

    .action-buttons form {
        margin: 0;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .reviews-table {
            min-width: 1000px;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý đánh giá</h1>
        <a href="{{ route('admin.reviews.statistics') }}" class="btn btn-primary">
            <i class="fas fa-chart-bar"></i> Thống kê đánh giá
        </a>
    </div>

    <div class="card shadow mb-4 filter-card">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Bộ lọc</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.reviews.index') }}" method="GET" class="row">
                <div class="col-md-3 mb-3">
                    <label for="service_id" class="form-label">Dịch vụ</label>
                    <select name="service_id" id="service_id" class="form-select">
                        <option value="">Tất cả dịch vụ</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>
                                {{ $service->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="barber_id" class="form-label">Thợ cắt tóc</label>
                    <select name="barber_id" id="barber_id" class="form-select">
                        <option value="">Tất cả thợ cắt tóc</option>
                        @foreach($barbers as $barber)
                            <option value="{{ $barber->id }}" {{ request('barber_id') == $barber->id ? 'selected' : '' }}>
                                {{ $barber->user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="rating" class="form-label">Số sao</label>
                    <select name="rating" id="rating" class="form-select">
                        <option value="">Tất cả</option>
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                                {{ $i }} sao
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Hiển thị</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Ẩn</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="sort_by" class="form-label">Sắp xếp theo</label>
                    <select name="sort_by" id="sort_by" class="form-select">
                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Ngày tạo</option>
                        <option value="rating" {{ request('sort_by') == 'rating' ? 'selected' : '' }}>Số sao</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Lọc
                    </button>
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary">
                        <i class="fas fa-sync"></i> Đặt lại
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách đánh giá</h6>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="reviews-table">
                    <thead>
                        <tr>
                            <th class="col-id">ID</th>
                            <th class="col-customer">Khách hàng</th>
                            <th class="col-service">Dịch vụ</th>
                            <th class="col-barber">Thợ cắt tóc</th>
                            <th class="col-rating">Đánh giá</th>
                            <th class="col-comment">Nhận xét</th>
                            <th class="col-status">Trạng thái</th>
                            <th class="col-date">Ngày tạo</th>
                            <th class="col-actions">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                            <tr>
                                <td class="col-id">{{ $review->id }}</td>
                                <td class="col-customer">
                                    <a href="{{ route('admin.users.show', $review->user->id) }}" class="text-truncate-custom" title="{{ $review->user->name }}">
                                        {{ $review->user->name }}
                                    </a>
                                </td>
                                <td class="col-service">
                                    <a href="{{ route('admin.services.edit', $review->service->id) }}" class="text-truncate-custom" title="{{ $review->service->name }}">
                                        {{ $review->service->name }}
                                    </a>
                                </td>
                                <td class="col-barber">
                                    <a href="{{ route('admin.barbers.show', $review->barber->user->id) }}" class="text-truncate-custom" title="{{ $review->barber->user->name }}">
                                        {{ $review->barber->user->name }}
                                    </a>
                                </td>
                                <td class="col-rating">
                                    <div class="star-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star"></i>
                                        @endfor
                                    </div>
                                </td>
                                <td class="col-comment">
                                    <span class="text-truncate-custom" title="{{ $review->comment }}">{{ Str::limit($review->comment, 50) }}</span>
                                </td>
                                <td class="col-status">
                                    <span class="review-status {{ $review->status ? 'status-active' : 'status-inactive' }}"></span>
                                    {{ $review->status ? 'Hiển thị' : 'Ẩn' }}
                                </td>
                                <td class="col-date">
                                    <div class="date-display">
                                        <span class="date">{{ $review->created_at->format('d/m/Y') }}</span>
                                        <span class="time">{{ $review->created_at->format('H:i') }}</span>
                                    </div>
                                </td>
                                <td class="col-actions">
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.reviews.show', $review->id) }}" class="btn btn-info" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.reviews.edit', $review->id) }}" class="btn btn-primary" title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.reviews.toggleStatus', $review->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-{{ $review->status ? 'warning' : 'success' }}" title="{{ $review->status ? 'Ẩn đánh giá' : 'Hiển thị đánh giá' }}">
                                                <i class="fas {{ $review->status ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa đánh giá này?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">Không có đánh giá nào</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $reviews->links('admin.partials.pagination') }}
            </div>
        </div>
    </div>
</div>
@endsection
