@extends('layouts.admin')

@section('title', 'Chi tiết danh mục')

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết danh mục</h1>
        <div>
            <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Chỉnh sửa
            </a>
            <a href="{{ route('admin.categories.index', ['type' => $category->type]) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Hình ảnh</h6>
                </div>
                <div class="card-body text-center">
                    @if($category->image)
                        <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="img-fluid rounded">
                    @else
                        <img src="{{ asset('images/default-category.jpg') }}" alt="{{ $category->name }}" class="img-fluid rounded">
                    @endif

                    <h4 class="mt-3">{{ $category->name }}</h4>
                    <p class="text-muted">
                        <span class="badge bg-{{ $category->status ? 'success' : 'danger' }}">
                            {{ $category->status ? 'Hoạt động' : 'Không hoạt động' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin chi tiết</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>ID:</strong>
                        </div>
                        <div class="col-sm-9">
                            {{ $category->id }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>Tên:</strong>
                        </div>
                        <div class="col-sm-9">
                            {{ $category->name }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>Slug:</strong>
                        </div>
                        <div class="col-sm-9">
                            {{ $category->slug }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>Loại:</strong>
                        </div>
                        <div class="col-sm-9">
                            @if($category->type == 'service')
                                <span class="badge bg-primary">Dịch vụ</span>
                            @elseif($category->type == 'product')
                                <span class="badge bg-info">Sản phẩm</span>
                            @elseif($category->type == 'news')
                                <span class="badge bg-warning">Tin tức</span>
                            @endif
                        </div>
                    </div>



                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>Trạng thái:</strong>
                        </div>
                        <div class="col-sm-9">
                            <span class="badge bg-{{ $category->status ? 'success' : 'danger' }}">
                                {{ $category->status ? 'Hoạt động' : 'Không hoạt động' }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>Ngày tạo:</strong>
                        </div>
                        <div class="col-sm-9">
                            {{ $category->created_at->format('d/m/Y H:i:s') }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-3">
                            <strong>Cập nhật lần cuối:</strong>
                        </div>
                        <div class="col-sm-9">
                            {{ $category->updated_at->format('d/m/Y H:i:s') }}
                        </div>
                    </div>
                </div>
            </div>

            @if($category->type == 'service' && isset($services) && $services->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Dịch vụ trong danh mục này</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên dịch vụ</th>
                                    <th>Giá</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($services as $service)
                                <tr>
                                    <td>{{ $service->id }}</td>
                                    <td>{{ $service->name }}</td>
                                    <td>{{ number_format($service->price) }} VNĐ</td>
                                    <td>
                                        <span class="badge bg-{{ $service->status ? 'success' : 'danger' }}">
                                            {{ $service->status ? 'Hoạt động' : 'Không hoạt động' }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            @if($category->type == 'product' && isset($products) && $products->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Sản phẩm trong danh mục này</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Giá</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                <tr>
                                    <td>{{ $product->id }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ number_format($product->price) }} VNĐ</td>
                                    <td>
                                        <span class="badge bg-{{ $product->status ? 'success' : 'danger' }}">
                                            {{ $product->status ? 'Hoạt động' : 'Không hoạt động' }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection