@extends('layouts.frontend')

@section('title', 'Sản phẩm')

@section('content')
@include('partials.page-header', [
    'title' => 'Sản phẩm của chúng tôi',
    'description' => 'Khám phá các sản phẩm chăm sóc tóc chất lượng cao từ những thương hiệu nổi tiếng',
    'backgroundImage' => 'images/hero-bg-3.jpg'
])

<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <!-- Sidebar Filters - Moved to left -->
            <div class="col-lg-4">
                <div class="card shadow-sm filter-sidebar">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 d-flex align-items-center">
                            <i class="fas fa-filter me-2 text-primary"></i>
                            BỘ LỌC
                        </h5>
                        <div>
                            <button id="clearAllFilters" class="btn btn-sm text-primary border-0">
                                XÓA
                            </button>
                            <button id="closeFilterSidebar" class="btn btn-sm text-primary border-0 d-lg-none">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Search Filter -->
                        <div class="filter-section mb-4">
                            <h6 class="filter-title">Tìm kiếm</h6>
                            <div class="input-group">
                                <input type="text" id="searchInput" class="form-control" placeholder="Tên sản phẩm..." value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary" type="button" id="searchButton">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Category Filter -->
                        <div class="filter-section mb-4">
                            <h6 class="filter-title">Danh mục sản phẩm</h6>
                            <div class="filter-options">
                                @foreach($categories as $category)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input auto-filter" type="checkbox" name="category_id[]" id="category{{ $category->id }}" value="{{ $category->id }}" {{ (is_array(request('category_id')) && in_array($category->id, request('category_id'))) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="category{{ $category->id }}">
                                            {{ $category->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>



                        <!-- Price Range Filter -->
                        <div class="filter-section mb-4">
                            <h6 class="filter-title">Khoảng giá</h6>
                            <div class="filter-options">
                                <div class="form-check mb-2">
                                    <input class="form-check-input auto-filter" type="checkbox" name="price[]" id="price1" value="0-200000" {{ (is_array(request('price')) && in_array('0-200000', request('price'))) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="price1">
                                        Dưới 200.000 VNĐ
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input auto-filter" type="checkbox" name="price[]" id="price2" value="200000-500000" {{ (is_array(request('price')) && in_array('200000-500000', request('price'))) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="price2">
                                        200.000 - 500.000 VNĐ
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input auto-filter" type="checkbox" name="price[]" id="price3" value="500000-1000000" {{ (is_array(request('price')) && in_array('500000-1000000', request('price'))) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="price3">
                                        Trên 500.000 VNĐ
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Sort By -->
                        <div class="filter-section mb-4">
                            <h6 class="filter-title">Sắp xếp theo</h6>
                            <div class="filter-options">
                                <div class="form-check mb-2">
                                    <input class="form-check-input auto-filter" type="radio" name="sort" id="sortPriceLow" value="price_low" {{ $sort == 'price_low' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sortPriceLow">
                                        Giá thấp đến cao
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input auto-filter" type="radio" name="sort" id="sortPriceHigh" value="price_high" {{ $sort == 'price_high' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sortPriceHigh">
                                        Giá cao đến thấp
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input auto-filter" type="radio" name="sort" id="sortPopular" value="popular" {{ $sort == 'popular' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sortPopular">
                                        Phổ biến nhất
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input auto-filter" type="radio" name="sort" id="sortNewest" value="newest" {{ $sort == 'newest' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sortNewest">
                                        Mới nhất
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products List - Moved to right -->
            <div class="col-lg-8">
                <!-- Products List Header -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 text-muted">Danh sách sản phẩm</h5>
                    <div class="filter-count text-muted">
                        Hiển thị {{ $products->count() }} / {{ $products->total() }} sản phẩm
                    </div>
                </div>

                <!-- Active Filters -->
                <div id="active-filters" class="mb-3">
                    <!-- Sẽ được điền bởi JavaScript -->
                </div>

                <!-- Products List -->
                <div id="products-container" class="row">
                    @forelse($products as $product)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 product-card">
                            <div class="card-img-container position-relative">
                                <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                                <div class="product-status position-absolute">
                                    <span class="{{ $product->stock > 0 ? 'in-stock' : 'out-of-stock' }}">{{ $product->stock > 0 ? 'Còn hàng' : 'Hết hàng' }}</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="product-category mb-2">
                                    <span class="badge bg-light text-dark">{{ $product->category->name }}</span>
                                </div>
                                <h5 class="card-title">{{ $product->name }}</h5>
                                <p class="card-text">{{ Str::limit($product->description, 100) }}</p>
                            </div>
                            <div class="card-footer bg-white border-top-0 d-flex justify-content-between align-items-center">
                                <span class="price text-primary fw-bold">{{ number_format($product->price) }} VNĐ</span>
                                <a href="{{ route('products.show', $product->slug) }}" class="btn btn-sm btn-outline-primary">Chi tiết <i class="fas fa-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            Hiện tại chưa có sản phẩm nào. Vui lòng quay lại sau.
                        </div>
                    </div>
                    @endforelse
                </div>

                <div class="mt-4 pagination-container">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 text-white text-center cta-appointment">
    <div class="container">
        <h2 class="h1 mb-4">Sản phẩm chăm sóc tóc chất lượng cao</h2>
        <p class="lead mb-4">Chúng tôi cung cấp các sản phẩm chăm sóc tóc chính hãng, giúp bạn duy trì mái tóc khỏe đẹp sau khi cắt tóc tại Barber Shop.</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="{{ route('contact.index') }}" class="btn btn-light btn-lg">Liên hệ tư vấn</a>
            <a href="#products-container" class="btn btn-outline-light btn-lg">Xem sản phẩm</a>
        </div>
    </div>
</section>

<!-- Mobile Filter Button -->
<button id="showFilterSidebar" class="mobile-filter-btn">
    <i class="fas fa-filter"></i>
</button>

<!-- Filter Backdrop -->
<div class="filter-backdrop"></div>
@endsection