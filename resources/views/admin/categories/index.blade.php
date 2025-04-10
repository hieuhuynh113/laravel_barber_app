@extends('layouts.admin')

@section('title', 'Quản lý danh mục')

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý danh mục {{ ucfirst($type) }}</h1>
        <a href="{{ route('admin.categories.create', ['type' => $type]) }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Thêm danh mục mới
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Nav tabs -->
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link {{ $type === 'service' ? 'active' : '' }}" href="{{ route('admin.categories.index', ['type' => 'service']) }}">
                <i class="fas fa-cut mr-1"></i> Dịch vụ
                <span class="badge badge-pill badge-info">{{ \App\Models\Category::where('type', 'service')->count() }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $type === 'product' ? 'active' : '' }}" href="{{ route('admin.categories.index', ['type' => 'product']) }}">
                <i class="fas fa-shopping-bag mr-1"></i> Sản phẩm
                <span class="badge badge-pill badge-info">{{ \App\Models\Category::where('type', 'product')->count() }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $type === 'news' ? 'active' : '' }}" href="{{ route('admin.categories.index', ['type' => 'news']) }}">
                <i class="fas fa-newspaper mr-1"></i> Tin tức
                <span class="badge badge-pill badge-info">{{ \App\Models\Category::where('type', 'news')->count() }}</span>
            </a>
        </li>
    </ul>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách danh mục</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width: 60px">ID</th>
                            <th>Tên danh mục</th>
                            <th>Slug</th>
                            <th>Mô tả</th>
                            <th>Trạng thái</th>
                            <th style="width: 150px">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td>{{ $category->name }}</td>
                                <td>{{ $category->slug }}</td>
                                <td>{{ Str::limit($category->description, 50) }}</td>
                                <td>
                                    <span class="badge bg-{{ $category->status ? 'success' : 'danger' }}">
                                        {{ $category->status ? 'Hoạt động' : 'Không hoạt động' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Không có danh mục nào.</td>
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