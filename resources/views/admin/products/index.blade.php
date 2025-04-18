@extends('layouts.admin')

@section('title', 'Quản lý sản phẩm')

@section('styles')
<style>
    /* Bảng sản phẩm */
    .products-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #e3e6f0;
    }

    .products-table th {
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

    .products-table th:last-child {
        border-right: none;
    }

    .products-table td {
        padding: 12px 15px;
        vertical-align: middle;
        border-bottom: 1px solid #e3e6f0;
        border-right: 1px solid #e3e6f0;
        font-size: 0.9rem;
    }

    .products-table td:last-child {
        border-right: none;
    }

    .products-table tr:hover {
        background-color: #f8f9fc;
    }

    /* Cột trong bảng sản phẩm */
    .col-id {
        width: 60px;
        text-align: center;
        background-color: #f8f9fc;
    }

    .col-image {
        width: 80px;
        text-align: center;
    }

    .col-name {
        width: 25%;
    }

    .col-category {
        width: 15%;
    }

    .col-price {
        width: 15%;
        text-align: right;
    }

    .col-stock {
        width: 10%;
        text-align: center;
    }

    .col-status {
        width: 12%;
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

    /* Hình ảnh sản phẩm */
    .product-image {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #e3e6f0;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý sản phẩm</h1>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Thêm sản phẩm mới
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Bộ lọc và tìm kiếm -->
    <div class="card shadow mb-4 filter-card">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Bộ lọc</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.products.index') }}" method="GET" class="row">
                <div class="col-md-4 mb-3">
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
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Không hoạt động</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="search" class="form-label">Tìm kiếm</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="Tìm kiếm sản phẩm..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2 mb-3 d-flex align-items-end">
                    <div class="d-grid gap-2 w-100">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Lọc
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-sync-alt"></i> Đặt lại
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách sản phẩm</h6>
            <span>Tổng số: {{ $products->total() }} sản phẩm</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="products-table">
                    <thead>
                        <tr>
                            <th class="col-id">ID</th>
                            <th class="col-image">Hình ảnh</th>
                            <th class="col-name">Tên sản phẩm</th>
                            <th class="col-category">Danh mục</th>
                            <th class="col-price">Giá</th>
                            <th class="col-stock">Tồn kho</th>
                            <th class="col-status">Trạng thái</th>
                            <th class="col-actions">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td class="col-id">{{ $product->id }}</td>
                                <td class="col-image">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="product-image">
                                    @else
                                        <img src="{{ asset('images/default-product.jpg') }}" alt="{{ $product->name }}" class="product-image">
                                    @endif
                                </td>
                                <td class="col-name">
                                    <a href="{{ route('admin.products.show', $product->id) }}" class="fw-bold text-decoration-none">
                                        {{ $product->name }}
                                    </a>
                                </td>
                                <td class="col-category">
                                    @if($product->category)
                                        <span class="badge bg-light text-dark border">
                                            {{ $product->category->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">Không có</span>
                                    @endif
                                </td>
                                <td class="col-price">{{ number_format($product->price) }} VNĐ</td>
                                <td class="col-stock">{{ $product->stock ?? 0 }}</td>
                                <td class="col-status">
                                    <span class="status-badge bg-{{ $product->status ? 'success' : 'danger' }}">
                                        {{ $product->status ? 'Hoạt động' : 'Không hoạt động' }}
                                    </span>
                                </td>
                                <td class="col-actions">
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-info btn-sm" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary btn-sm" title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">Không có sản phẩm nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $products->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection