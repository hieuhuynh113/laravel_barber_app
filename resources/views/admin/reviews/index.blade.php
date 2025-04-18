@extends('layouts.admin')

@section('title', 'Quản lý đánh giá')

@section('styles')
<style>
    .star-rating {
        color: #ffc107;
    }
    .review-status {
        width: 12px;
        height: 12px;
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
    .filter-card {
        margin-bottom: 20px;
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
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Khách hàng</th>
                            <th>Dịch vụ</th>
                            <th>Thợ cắt tóc</th>
                            <th>Đánh giá</th>
                            <th>Nhận xét</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                            <tr>
                                <td>{{ $review->id }}</td>
                                <td>
                                    <a href="{{ route('admin.users.show', $review->user->id) }}">
                                        {{ $review->user->name }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.services.edit', $review->service->id) }}">
                                        {{ $review->service->name }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.barbers.show', $review->barber->user->id) }}">
                                        {{ $review->barber->user->name }}
                                    </a>
                                </td>
                                <td>
                                    <div class="star-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star"></i>
                                        @endfor
                                    </div>
                                </td>
                                <td>{{ Str::limit($review->comment, 50) }}</td>
                                <td>
                                    <span class="review-status {{ $review->status ? 'status-active' : 'status-inactive' }}"></span>
                                    {{ $review->status ? 'Hiển thị' : 'Ẩn' }}
                                </td>
                                <td>{{ $review->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.reviews.show', $review->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.reviews.edit', $review->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.reviews.toggleStatus', $review->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-{{ $review->status ? 'warning' : 'success' }} btn-sm" title="{{ $review->status ? 'Ẩn đánh giá' : 'Hiển thị đánh giá' }}">
                                            <i class="fas {{ $review->status ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa đánh giá này?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
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
                {{ $reviews->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
