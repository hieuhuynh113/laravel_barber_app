@extends('layouts.admin')

@section('title', 'Chi tiết sản phẩm')

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết sản phẩm</h1>
        <div>
            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Chỉnh sửa
            </a>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Hình ảnh sản phẩm</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid rounded">
                        @else
                            <img src="{{ asset('images/default-product.jpg') }}" alt="{{ $product->name }}" class="img-fluid rounded">
                        @endif
                    </div>

                    @if($product->images && count(json_decode($product->images)) > 0)
                        <h6 class="font-weight-bold">Thư viện ảnh</h6>
                        <div class="row">
                            @foreach(json_decode($product->images) as $image)
                                <div class="col-md-4 mb-3">
                                    <a href="{{ asset('storage/' . $image) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $image) }}" alt="Gallery image" class="img-thumbnail" style="height: 100px; width: 100%; object-fit: cover;">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin sản phẩm</h6>
                </div>
                <div class="card-body">
                    <h3 class="font-weight-bold mb-3">{{ $product->name }}</h3>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-0">
                                <strong class="text-primary">Giá:</strong> 
                                <span class="h5 text-danger font-weight-bold">{{ number_format($product->price) }} VNĐ</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-0">
                                <strong class="text-primary">Trạng thái:</strong> 
                                <span class="badge bg-{{ $product->status ? 'success' : 'danger' }}">
                                    {{ $product->status ? 'Hoạt động' : 'Không hoạt động' }}
                                </span>
                                @if($product->featured)
                                    <span class="badge bg-warning">Sản phẩm nổi bật</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-0">
                                <strong class="text-primary">ID:</strong> {{ $product->id }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-0">
                                <strong class="text-primary">Slug:</strong> {{ $product->slug }}
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-0">
                                <strong class="text-primary">Danh mục:</strong> 
                                @if($product->category)
                                    <a href="{{ route('admin.categories.show', $product->category->id) }}">
                                        {{ $product->category->name }}
                                    </a>
                                @else
                                    <span class="text-muted">Không có</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-0">
                                <strong class="text-primary">Tồn kho:</strong> 
                                <span class="badge bg-{{ $product->stock > 0 ? 'info' : 'secondary' }}">
                                    {{ $product->stock ?? 0 }}
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-0">
                                <strong class="text-primary">Ngày tạo:</strong> 
                                {{ $product->created_at->format('d/m/Y H:i:s') }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-0">
                                <strong class="text-primary">Cập nhật lần cuối:</strong> 
                                {{ $product->updated_at->format('d/m/Y H:i:s') }}
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h5 class="font-weight-bold">Mô tả ngắn</h5>
                        <p>{{ $product->description ?? 'Không có mô tả' }}</p>
                    </div>

                    <div class="mb-4">
                        <h5 class="font-weight-bold">Nội dung chi tiết</h5>
                        <div>
                            {!! $product->content ?? 'Không có nội dung chi tiết' !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 