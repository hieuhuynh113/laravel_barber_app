@extends('layouts.admin')

@section('title', 'Quản lý tin tức')

@section('styles')
<style>
    /* Bảng tin tức */
    .news-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #e3e6f0;
    }

    .news-table th {
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

    .news-table th:last-child {
        border-right: none;
    }

    .news-table td {
        padding: 12px 15px;
        vertical-align: middle;
        border-bottom: 1px solid #e3e6f0;
        border-right: 1px solid #e3e6f0;
        font-size: 0.9rem;
    }

    .news-table td:last-child {
        border-right: none;
    }

    .news-table tr:hover {
        background-color: #f8f9fc;
    }

    /* Cột trong bảng tin tức */
    .col-id {
        width: 50px;
        text-align: center;
        background-color: #f8f9fc;
    }

    .col-image {
        width: 80px;
        text-align: center;
    }

    .col-title {
        width: 20%;
    }

    .col-category {
        width: 12%;
    }

    .col-author {
        width: 12%;
    }

    .col-views {
        width: 80px;
        text-align: center;
    }

    .col-featured {
        width: 80px;
        text-align: center;
    }

    .col-status {
        width: 100px;
        text-align: center;
    }

    .col-date {
        width: 100px;
        text-align: center;
    }

    .col-actions {
        width: 120px;
        text-align: center;
        background-color: #f8f9fc;
    }

    /* Badge trạng thái */
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-align: center;
        min-width: 100px;
        color: white; /* Đảm bảo chữ luôn có màu trắng */
        text-shadow: 0px 1px 1px rgba(0, 0, 0, 0.2); /* Thêm đổ bóng nhẹ cho chữ để tăng độ tương phản */
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

    .action-buttons form {
        margin: 0;
    }

    /* Hình ảnh tin tức */
    .news-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #e3e6f0;
    }

    /* Nút nổi bật */
    .featured-button {
        width: 32px;
        height: 32px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: all 0.2s ease;
    }

    .featured-button:hover {
        transform: scale(1.1);
    }

    .featured-button.active {
        background-color: #f6c23e;
        border-color: #f6c23e;
        color: #fff;
    }

    .featured-button.inactive {
        background-color: #fff;
        border-color: #f6c23e;
        color: #f6c23e;
    }

    /* Bộ lọc */
    .filter-card {
        margin-bottom: 1.5rem;
    }

    .filter-card .card-header {
        background-color: #f8f9fc;
        border-bottom: 1px solid #e3e6f0;
    }

    .filter-card .form-select {
        border-radius: 4px;
        border: 1px solid #d1d3e2;
        box-shadow: none;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .filter-card .form-select:focus {
        border-color: #bac8f3;
        box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý tin tức</h1>
        <a href="{{ route('admin.news.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Thêm bài viết mới
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4 filter-card">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Bộ lọc</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.news.index') }}" method="GET" class="row">
                <div class="col-md-3 mb-3">
                    <label for="category_id" class="form-label">Danh mục</label>
                    <select name="category_id" id="category_id" class="form-select">
                        <option value="">-- Tất cả danh mục --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Đã xuất bản</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Bản nháp</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="search" class="form-label">Tìm kiếm</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="Tìm kiếm theo tiêu đề hoặc nội dung..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2 mb-3 d-flex align-items-end">
                    <div class="d-grid gap-2 w-100">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Lọc
                        </button>
                        <a href="{{ route('admin.news.index') }}" class="btn btn-secondary">
                            <i class="fas fa-sync-alt"></i> Đặt lại
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách bài viết</h6>
            <span>Tổng số: {{ $news->total() }} bài viết</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="news-table">
                    <thead>
                        <tr>
                            <th class="col-id">ID</th>
                            <th class="col-image">Hình ảnh</th>
                            <th class="col-title">Tiêu đề</th>
                            <th class="col-category">Danh mục</th>
                            <th class="col-author">Tác giả</th>
                            <th class="col-views">Lượt xem</th>
                            <th class="col-featured">Nổi bật</th>
                            <th class="col-status">Trạng thái</th>
                            <th class="col-date">Ngày tạo</th>
                            <th class="col-actions">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($news as $item)
                            <tr>
                                <td class="col-id">{{ $item->id }}</td>
                                <td class="col-image">
                                    @if($item->image)
                                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}" class="news-image">
                                    @else
                                        <img src="{{ asset('images/default-news.jpg') }}" alt="{{ $item->title }}" class="news-image">
                                    @endif
                                </td>
                                <td class="col-title">
                                    <a href="{{ route('admin.news.edit', $item->id) }}" class="fw-bold text-decoration-none">
                                        {{ Str::limit($item->title, 50) }}
                                    </a>
                                </td>
                                <td class="col-category">
                                    @if($item->category)
                                        <span class="badge bg-light text-dark border">
                                            {{ $item->category->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">Không có</span>
                                    @endif
                                </td>
                                <td class="col-author">
                                    @if($item->user)
                                        <div class="d-flex align-items-center">
                                            <img src="{{ get_user_avatar($item->user, 'small') }}" alt="{{ $item->user->name }}" class="rounded-circle me-2" width="24" height="24">
                                            <span>{{ $item->user->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted">Không có</span>
                                    @endif
                                </td>
                                <td class="col-views">
                                    <span class="badge bg-secondary text-white">
                                        {{ $item->view_count }}
                                    </span>
                                </td>
                                <td class="col-featured">
                                    <form action="{{ route('admin.news.toggleFeatured', $item->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="featured-button {{ $item->is_featured ? 'active' : 'inactive' }}" title="{{ $item->is_featured ? 'Bỏ nổi bật' : 'Đánh dấu nổi bật' }}">
                                            <i class="fas fa-star"></i>
                                        </button>
                                    </form>
                                </td>
                                <td class="col-status">
                                    <span class="status-badge bg-{{ $item->status ? 'success' : 'warning' }} text-white">
                                        {{ $item->status ? 'Đã xuất bản' : 'Bản nháp' }}
                                    </span>
                                </td>
                                <td class="col-date">{{ $item->created_at->format('d/m/Y') }}</td>
                                <td class="col-actions">
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.news.show', $item->id) }}" class="btn btn-info btn-sm" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.news.edit', $item->id) }}" class="btn btn-primary btn-sm" title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.news.destroy', $item->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa bài viết này?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">Không có bài viết nào</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $news->appends(request()->query())->links('admin.partials.pagination') }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Không cần sử dụng DataTables vì đã có bảng tùy chỉnh
</script>
@endsection