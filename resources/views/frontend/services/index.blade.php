@extends('layouts.frontend')

@section('title', 'Dịch vụ')

@section('content')
@include('partials.page-header', [
    'title' => 'Dịch vụ của chúng tôi',
    'description' => 'Khám phá các dịch vụ chất lượng cao và chuyên nghiệp tại Barber Shop',
    'backgroundImage' => 'images/hero-bg-2.jpg'
])

<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <!-- Services List Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0 text-muted">Danh sách dịch vụ</h5>
                    <div class="filter-count text-muted">
                        Hiển thị {{ $services->count() }} / {{ $services->total() }} dịch vụ
                    </div>
                </div>

                <!-- Services List -->
                <div id="services-container">
                    @if($services->count() > 0)
                        @foreach($services as $service)
                        <div class="service-list-item mb-4">
                            <div class="card service-card horizontal-card">
                                <div class="row g-0">
                                    <div class="col-md-4">
                                        <div class="service-image-container h-100">
                                            <img src="{{ asset('storage/' . $service->image) }}" class="img-fluid rounded-start h-100 w-100" alt="{{ $service->name }}" style="object-fit: cover;">
                                            <div class="service-duration position-absolute">
                                                <span>{{ $service->duration }} phút</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="card-body d-flex flex-column h-100">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div class="service-category">
                                                    <span class="badge bg-light text-dark">{{ $service->category->name }}</span>
                                                </div>
                                                <div class="service-rating">
                                                    @php
                                                        $avgRating = $service->reviews_avg_rating ?? 0;
                                                    @endphp
                                                    <div class="stars">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            @if($i <= round($avgRating))
                                                                <i class="fas fa-star text-warning small"></i>
                                                            @elseif($i - 0.5 <= $avgRating)
                                                                <i class="fas fa-star-half-alt text-warning small"></i>
                                                            @else
                                                                <i class="far fa-star text-warning small"></i>
                                                            @endif
                                                        @endfor
                                                        <span class="ms-1 small text-muted">{{ number_format($avgRating, 1) }} ({{ $service->reviews_count }})</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <h5 class="card-title">{{ $service->name }}</h5>
                                            <p class="card-text flex-grow-1">{{ Str::limit($service->description, 150) }}</p>

                                            <div class="service-features mb-3">
                                                <div class="d-flex flex-wrap">
                                                    <div class="service-feature me-3 mb-2">
                                                        <i class="fas fa-clock text-muted me-1"></i>
                                                        <span class="small">{{ $service->duration }} phút</span>
                                                    </div>
                                                    @if($service->level)
                                                    <div class="service-feature me-3 mb-2">
                                                        <i class="fas fa-signal text-muted me-1"></i>
                                                        <span class="small">{{ ucfirst($service->level) }}</span>
                                                    </div>
                                                    @endif
                                                    <div class="service-feature mb-2">
                                                        <i class="fas fa-user-tie text-muted me-1"></i>
                                                        <span class="small">Thợ chuyên nghiệp</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                                <span class="price text-primary fw-bold">{{ number_format($service->price) }} VNĐ</span>
                                                <div>
                                                    <a href="{{ route('appointment.step1', ['service_id' => $service->id]) }}" class="btn btn-sm btn-primary me-1 appointment-btn">Đặt lịch</a>
                                                    <a href="{{ route('services.show', $service->slug) }}" class="btn btn-sm btn-outline-primary">Chi tiết <i class="fas fa-arrow-right ms-1"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="alert alert-info text-center p-5">
                            <i class="fas fa-info-circle fa-3x mb-3"></i>
                            <h4>Không tìm thấy dịch vụ nào</h4>
                            <p>Hiện tại không có dịch vụ nào phù hợp với bộ lọc. Vui lòng thử lại với bộ lọc khác.</p>
                        </div>
                    @endif
                </div>

                <div class="mt-4 pagination-container">
                    {{ $services->appends(request()->query())->links() }}
                </div>
            </div>

            <!-- Sidebar Filters -->
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 d-flex align-items-center">
                            <i class="fas fa-filter me-2 text-primary"></i>
                            BỘ LỌC
                        </h5>
                        <button id="clearAllFilters" class="btn btn-sm text-primary border-0">
                            CLEAR
                        </button>
                    </div>
                    <div class="card-body">


                        <!-- Category Filter -->
                        <div class="filter-section mb-4">
                            <h6 class="filter-title">Danh mục dịch vụ</h6>
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
                                    <input class="form-check-input auto-filter" type="checkbox" name="price[]" id="price1" value="0-100000" {{ (is_array(request('price')) && in_array('0-100000', request('price'))) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="price1">
                                        Dưới 100.000 VNĐ
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input auto-filter" type="checkbox" name="price[]" id="price2" value="100000-200000" {{ (is_array(request('price')) && in_array('100000-200000', request('price'))) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="price2">
                                        100.000 - 200.000 VNĐ
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input auto-filter" type="checkbox" name="price[]" id="price3" value="200000-300000" {{ (is_array(request('price')) && in_array('200000-300000', request('price'))) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="price3">
                                        200.000 - 300.000 VNĐ
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input auto-filter" type="checkbox" name="price[]" id="price4" value="300000-1000000" {{ (is_array(request('price')) && in_array('300000-1000000', request('price'))) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="price4">
                                        Trên 300.000 VNĐ
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
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <h4 class="mb-3">Tại sao chọn dịch vụ của chúng tôi?</h4>
                                <div class="row">
                                    <div class="col-md-4 mb-3 mb-md-0">
                                        <div class="text-center p-3 h-100 border rounded">
                                            <i class="fas fa-user-tie text-primary mb-3" style="font-size: 28px;"></i>
                                            <h6>Thợ cắt tóc chuyên nghiệp</h6>
                                            <p class="small text-muted mb-0">Đội ngũ được đào tạo bài bản và có nhiều năm kinh nghiệm</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3 mb-md-0">
                                        <div class="text-center p-3 h-100 border rounded">
                                            <i class="fas fa-cut text-primary mb-3" style="font-size: 28px;"></i>
                                            <h6>Dụng cụ hiện đại</h6>
                                            <p class="small text-muted mb-0">Sử dụng các dụng cụ và sản phẩm chất lượng cao</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center p-3 h-100 border rounded">
                                            <i class="fas fa-gem text-primary mb-3" style="font-size: 28px;"></i>
                                            <h6>Không gian sang trọng</h6>
                                            <p class="small text-muted mb-0">Môi trường thoải mái, sang trọng và thư giãn</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 mt-4 mt-lg-0 text-center">
                                <a href="{{ route('appointment.step1') }}" class="btn btn-primary btn-lg appointment-btn">
                                    <i class="fas fa-calendar-alt me-2"></i>Đặt lịch ngay
                                </a>
                                <p class="small text-muted mt-2">Đặt lịch trước để được phục vụ tốt nhất</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection