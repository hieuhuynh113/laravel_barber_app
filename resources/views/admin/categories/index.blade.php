@extends('layouts.admin')

@section('title', 'Quản lý danh mục')

@section('styles')
<style>
    /* Bảng danh mục */
    .categories-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #e3e6f0;
    }

    .categories-table th {
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

    .categories-table th:last-child {
        border-right: none;
    }

    .categories-table td {
        padding: 12px 15px;
        vertical-align: middle;
        border-bottom: 1px solid #e3e6f0;
        border-right: 1px solid #e3e6f0;
        font-size: 0.9rem;
    }

    .categories-table td:last-child {
        border-right: none;
    }

    .categories-table tr:hover {
        background-color: #f8f9fc;
    }

    /* Cột trong bảng danh mục */
    .col-id {
        width: 50px;
        text-align: center;
        background-color: #f8f9fc;
    }

    .col-name {
        width: 18%;
    }

    .col-type {
        width: 100px;
        text-align: center;
    }

    .col-slug {
        width: 15%;
        font-family: monospace;
        font-size: 0.85rem;
        color: #666;
    }

    .col-description {
        width: 25%;
    }

    /* Styles for description content */
    .description-wrapper {
        position: relative;
        margin-left: 5px;
        display: inline-block;
        width: calc(100% - 25px);
        vertical-align: top;
    }

    .description-content {
        position: relative;
        max-height: 40px;
        overflow: hidden;
        margin-bottom: 0;
        line-height: 1.4;
        font-size: 0.9rem;
        color: #555;
        text-align: justify;
        transition: max-height 0.3s ease;
    }

    .description-content::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 20px;
        background: linear-gradient(to bottom, rgba(255,255,255,0), rgba(255,255,255,1));
        pointer-events: none;
    }

    .description-full {
        max-height: 500px;
    }

    .description-full::after {
        display: none;
    }

    .description-toggle {
        display: inline-block;
        margin-top: 5px;
        color: #4e73df;
        cursor: pointer;
        font-size: 0.8rem;
        font-weight: 500;
        background-color: #f8f9fc;
        padding: 2px 8px;
        border-radius: 12px;
        border: 1px solid #e3e6f0;
        transition: all 0.2s ease;
    }

    .description-toggle:hover {
        background-color: #eaecf4;
        color: #2e59d9;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        text-decoration: none;
    }

    /* Tooltip styles */
    .description-tooltip {
        position: relative;
        display: inline-block;
        vertical-align: top;
        margin-top: 2px;
    }

    .description-tooltip i {
        font-size: 16px;
        cursor: pointer;
    }

    .description-tooltip .tooltip-text {
        visibility: hidden;
        width: 300px;
        background-color: #333;
        color: #fff;
        text-align: left;
        border-radius: 6px;
        padding: 10px;
        position: absolute;
        z-index: 1;
        bottom: 125%;
        left: 50%;
        transform: translateX(-50%);
        opacity: 0;
        transition: opacity 0.3s;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        font-size: 0.85rem;
        line-height: 1.5;
    }

    .description-tooltip .tooltip-text::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #333 transparent transparent transparent;
    }

    .description-tooltip:hover .tooltip-text {
        visibility: visible;
        opacity: 1;
    }

    .col-items {
        width: 80px;
        text-align: center;
    }

    .col-status {
        width: 120px;
        text-align: center;
    }

    .col-actions {
        width: 100px;
        text-align: center;
        background-color: #f8f9fc;
    }

    /* Dashboard cards */
    .dashboard-card {
        border-radius: 0.35rem;
        transition: all 0.3s ease;
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }

    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }

    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
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

    /* Tab navigation */
    .nav-tabs .nav-link {
        padding: 0.75rem 1.25rem;
        border-radius: 0.25rem 0.25rem 0 0;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .nav-tabs .nav-link:not(.active) {
        background-color: #f8f9fc;
        border-color: #e3e6f0 #e3e6f0 #e3e6f0;
    }

    .nav-tabs .nav-link:hover:not(.active) {
        background-color: #eaecf4;
        border-color: #e3e6f0 #e3e6f0 #e3e6f0;
    }

    .nav-tabs .nav-link.active {
        font-weight: 600;
        color: #4e73df;
    }

    .nav-tabs .badge {
        margin-left: 5px;
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
        vertical-align: middle;
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Xử lý nút xem thêm/thu gọn mô tả
        $('.description-toggle').on('click', function() {
            var $this = $(this);
            var $content = $this.closest('.description-wrapper').find('.description-content');

            if ($this.data('action') === 'expand') {
                $content.addClass('description-full');
                $this.text('Thu gọn');
                $this.data('action', 'collapse');
            } else {
                $content.removeClass('description-full');
                $this.text('Xem thêm');
                $this.data('action', 'expand');
            }
        });

        // Khở tạo tooltip cho các nút
        $('[title]').tooltip({
            placement: 'top',
            trigger: 'hover'
        });
    });
</script>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý danh mục</h1>
        <div>
            <a href="{{ route('admin.categories.create', ['type' => $activeType !== 'all' ? $activeType : 'service']) }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Thêm danh mục mới
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Bộ lọc và tìm kiếm -->
    <div class="card shadow mb-4 filter-card">
        <div class="card-body">
            <form action="{{ route('admin.categories.index') }}" method="GET" class="row align-items-end">
                <div class="col-md-4 mb-3 mb-md-0">
                    <label for="type" class="form-label">Loại danh mục</label>
                    <select name="type" id="type" class="form-select" onchange="this.form.submit()">
                        <option value="all" {{ $activeType === 'all' ? 'selected' : '' }}>Tất cả danh mục</option>
                        <option value="service" {{ $activeType === 'service' ? 'selected' : '' }}>Dịch vụ ({{ $serviceCategoriesCount }})</option>
                        <option value="product" {{ $activeType === 'product' ? 'selected' : '' }}>Sản phẩm ({{ $productCategoriesCount }})</option>
                        <option value="news" {{ $activeType === 'news' ? 'selected' : '' }}>Tin tức ({{ $newsCategoriesCount }})</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3 mb-md-0">
                    <label for="search" class="form-label">Tìm kiếm</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="search" name="search" placeholder="Tìm kiếm theo tên hoặc mô tả..." value="{{ $search }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-2 text-md-end">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary w-100">
                        <i class="fas fa-sync-alt"></i> Đặt lại
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Thống kê nhanh -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-4 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Dịch vụ</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $serviceCategoriesCount }} danh mục</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cut fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-4 mb-4">
            <div class="card border-left-success shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Sản phẩm</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $productCategoriesCount }} danh mục</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-bag fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-4 mb-4">
            <div class="card border-left-info shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tin tức</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $newsCategoriesCount }} danh mục</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-newspaper fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách danh mục</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="categories-table">
                    <thead>
                        <tr>
                            <th class="col-id">ID</th>
                            <th class="col-name">Tên danh mục</th>
                            <th class="col-type">Loại</th>
                            <th class="col-slug">Slug</th>
                            <th class="col-description">Mô tả</th>
                            <th class="col-items">Số mục</th>
                            <th class="col-status">Trạng thái</th>
                            <th class="col-actions">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td class="col-id">{{ $category->id }}</td>
                                <td class="col-name">
                                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="fw-bold text-decoration-none">
                                        {{ $category->name }}
                                    </a>
                                </td>
                                <td class="col-type">
                                    @if($category->type == 'service')
                                        <span class="badge bg-primary">Dịch vụ</span>
                                    @elseif($category->type == 'product')
                                        <span class="badge bg-success">Sản phẩm</span>
                                    @elseif($category->type == 'news')
                                        <span class="badge bg-info">Tin tức</span>
                                    @endif
                                </td>
                                <td class="col-slug">{{ $category->slug }}</td>
                                <td class="col-description">
                                    @if($category->description)
                                        <div class="description-tooltip">
                                            <i class="fas fa-info-circle text-primary" title="Xem mô tả đầy đủ"></i>
                                            <div class="tooltip-text">{{ $category->description }}</div>
                                        </div>
                                        <div class="description-wrapper">
                                            <p class="description-content">{{ $category->description }}</p>
                                            @if(strlen($category->description) > 100)
                                                <span class="description-toggle" data-action="expand">Xem thêm</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">Không có mô tả</span>
                                    @endif
                                </td>
                                <td class="col-items text-center">
                                    @if($category->type == 'service')
                                        {{ $category->services->count() }}
                                    @elseif($category->type == 'product')
                                        {{ $category->products->count() }}
                                    @elseif($category->type == 'news')
                                        {{ $category->news->count() }}
                                    @endif
                                </td>
                                <td class="col-status">
                                    <span class="status-badge bg-{{ $category->status ? 'success' : 'danger' }}">
                                        {{ $category->status ? 'Hoạt động' : 'Không hoạt động' }}
                                    </span>
                                </td>
                                <td class="col-actions">
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-primary btn-sm" title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">Không có danh mục nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</div>
@endsection