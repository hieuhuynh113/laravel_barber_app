@extends('layouts.frontend')

@section('title', $product->name)

@section('content')
<section class="py-5">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Sản phẩm</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="product-image-container">
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid rounded shadow">
                </div>
            </div>
            <div class="col-lg-6">
                <h1 class="mb-3">{{ $product->name }}</h1>
                <div class="mb-3">
                    <span class="badge bg-info">{{ $product->category->name }}</span>
                </div>
                <div class="product-price mb-4">
                    <span class="h3 text-primary">{{ number_format($product->price) }} VNĐ</span>
                </div>
                <div class="product-description mb-4">
                    <h5>Mô tả sản phẩm</h5>
                    <p>{{ $product->description }}</p>
                </div>

                <div class="product-features mb-4">
                    <h5>Đặc điểm nổi bật</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item bg-transparent ps-0">
                            <i class="fas fa-check-circle text-success me-2"></i> Chất lượng hàng đầu
                        </li>
                        <li class="list-group-item bg-transparent ps-0">
                            <i class="fas fa-check-circle text-success me-2"></i> An toàn cho mọi loại tóc
                        </li>
                        <li class="list-group-item bg-transparent ps-0">
                            <i class="fas fa-check-circle text-success me-2"></i> Được các chuyên gia khuyên dùng
                        </li>
                    </ul>
                </div>

                <div class="product-action mb-4">
                    <div class="d-grid gap-2 d-md-block">
                        <a href="{{ route('contact.index') }}" class="btn btn-primary">
                            <i class="fas fa-phone me-2"></i> Liên hệ để đặt hàng
                        </a>
                        <a href="{{ route('appointment.step1') }}" class="btn btn-outline-primary">
                            <i class="fas fa-calendar-alt me-2"></i> Đặt lịch dùng thử
                        </a>
                    </div>
                </div>

                <div class="product-info">
                    <h5>Thông tin thêm</h5>
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-2">
                                <strong>Xuất xứ:</strong> Chính hãng
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-2">
                                <strong>Bảo hành:</strong> 30 ngày
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-2">
                                <strong>Đơn vị:</strong> Sản phẩm
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-2">
                                <strong>Tình trạng:</strong> <span class="{{ $product->stock > 0 ? 'text-success' : 'text-danger' }}">{{ $product->stock > 0 ? 'Còn hàng' : 'Hết hàng' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">Sản phẩm liên quan</h2>

        <div class="row">
            @foreach($relatedProducts as $relatedProduct)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 product-card">
                    <img src="{{ asset('storage/' . $relatedProduct->image) }}" class="card-img-top" alt="{{ $relatedProduct->name }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $relatedProduct->name }}</h5>
                        <p class="card-text">{{ Str::limit($relatedProduct->description, 100) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="price text-primary fw-bold">{{ number_format($relatedProduct->price) }} VNĐ</span>
                            <a href="{{ route('products.show', $relatedProduct->slug) }}" class="btn btn-outline-primary">Chi tiết</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-5 bg-primary text-white text-center cta-appointment">
    <div class="container">
        <h2 class="h1 mb-4">Cần tư vấn thêm về sản phẩm?</h2>
        <p class="lead mb-4">Đừng ngần ngại liên hệ hoặc ghé cửa hàng để được tư vấn chi tiết về sản phẩm phù hợp.</p>
        <a href="{{ route('contact.index') }}" class="btn btn-light btn-lg appointment-btn">Liên hệ ngay</a>
    </div>
</section>
@endsection