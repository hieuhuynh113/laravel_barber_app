@extends('layouts.frontend')

@section('title', 'Tin tức')

@section('content')
@include('partials.page-header', [
    'title' => 'Tin tức & Sự kiện',
    'description' => 'Cập nhật những xu hướng tóc mới nhất và tin tức từ Barber Shop',
    'backgroundImage' => 'images/about-2.jpg'
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
                                CLEAR
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
                                <input type="text" id="searchInput" class="form-control" placeholder="Tìm kiếm tin tức..." value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary" type="button" id="searchButton">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Category Filter -->
                        <div class="filter-section mb-4">
                            <h6 class="filter-title">Danh mục tin tức</h6>
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

                        <!-- Time Filter -->
                        <div class="filter-section mb-4">
                            <h6 class="filter-title">Thời gian</h6>
                            <div class="filter-options">
                                <div class="form-check mb-2">
                                    <input class="form-check-input auto-filter" type="radio" name="time" id="timeAll" value="" {{ !$timeFilter ? 'checked' : '' }}>
                                    <label class="form-check-label" for="timeAll">
                                        Tất cả thời gian
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input auto-filter" type="radio" name="time" id="timeToday" value="today" {{ $timeFilter == 'today' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="timeToday">
                                        Hôm nay
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input auto-filter" type="radio" name="time" id="timeWeek" value="week" {{ $timeFilter == 'week' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="timeWeek">
                                        Tuần này
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input auto-filter" type="radio" name="time" id="timeMonth" value="month" {{ $timeFilter == 'month' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="timeMonth">
                                        Tháng này
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Sort By -->
                        <div class="filter-section mb-4">
                            <h6 class="filter-title">Sắp xếp theo</h6>
                            <div class="filter-options">
                                <div class="form-check mb-2">
                                    <input class="form-check-input auto-filter" type="radio" name="sort" id="sortAll" value="" {{ !$sort ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sortAll">
                                        Mặc định
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input auto-filter" type="radio" name="sort" id="sortPopular" value="popular" {{ $sort == 'popular' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sortPopular">
                                        Phổ biến nhất
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input auto-filter" type="radio" name="sort" id="sortNewest" value="newest" {{ $sort == 'newest' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sortNewest">
                                        Mới nhất
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input auto-filter" type="radio" name="sort" id="sortRecommended" value="recommended" {{ $sort == 'recommended' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sortRecommended">
                                        Đề xuất
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- News List - Moved to right -->
            <div class="col-lg-8">
                <!-- News List Header -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 text-muted">Danh sách tin tức</h5>
                    <div class="filter-count text-muted">
                        Hiển thị {{ $news->count() }} / {{ $news->total() }} bài viết
                    </div>
                </div>

                <!-- Active Filters -->
                <div id="active-filters" class="mb-3">
                    <!-- Sẽ được điền bởi JavaScript -->
                </div>

                <!-- News List -->
                <div id="news-container" class="row">
                    @forelse($news as $item)
                    <div class="col-md-6 col-lg-6 mb-4">
                        <div class="card h-100 news-card">
                            <div class="card-img-container position-relative">
                                <img src="{{ asset('storage/' . $item->image) }}" class="card-img-top" alt="{{ $item->title }}">
                                <div class="news-date position-absolute">
                                    <span><i class="far fa-calendar-alt me-1"></i>{{ $item->created_at->format('d/m/Y') }}</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="news-category mb-2">
                                    <span class="badge bg-light text-dark">{{ $item->category->name }}</span>
                                </div>
                                <h5 class="card-title">{{ $item->title }}</h5>
                                <p class="card-text">{{ Str::limit($item->excerpt, 100) }}</p>
                            </div>
                            <div class="card-footer bg-white border-top-0 d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('storage/' . $item->user->avatar) }}" alt="{{ $item->user->name }}" class="rounded-circle me-2" width="25" height="25">
                                    <small>{{ $item->user->name }}</small>
                                </div>
                                <a href="{{ route('news.show', $item->slug) }}" class="btn btn-sm btn-outline-primary">Đọc tiếp <i class="fas fa-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="alert alert-info text-center p-5">
                            <i class="fas fa-info-circle fa-3x mb-3"></i>
                            <h4>Không tìm thấy bài viết nào</h4>
                            <p>Hiện tại không có bài viết nào phù hợp với bộ lọc. Vui lòng thử lại với bộ lọc khác.</p>
                        </div>
                    </div>
                    @endforelse
                </div>

                <div class="mt-4 pagination-container">
                    {{ $news->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h2>Đăng ký nhận thông tin</h2>
                <p class="lead mb-4">Nhận thông tin mới nhất về xu hướng tóc, khuyến mãi và sự kiện đặc biệt.</p>

                <form action="#" method="POST" class="subscription-form">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" placeholder="Nhập email của bạn" required>
                        <button class="btn btn-primary" type="submit">Đăng ký</button>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agree" required>
                        <label class="form-check-label" for="agree">
                            Tôi đồng ý nhận email thông tin từ Barber Shop
                        </label>
                    </div>
                </form>
            </div>

            <div class="col-lg-6">
                <div class="position-relative">
                    <img src="{{ asset('images/newsletter.jpg') }}" alt="Đăng ký nhận thông tin" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 text-white text-center cta-appointment">
    <div class="container">
        <h2 class="h1 mb-4">Cập nhật tin tức mới nhất</h2>
        <p class="lead mb-4">Đặt lịch ngay hôm nay để trải nghiệm dịch vụ tuyệt vời tại Barber Shop.</p>
        <a href="{{ route('appointment.step1') }}" class="btn btn-light btn-lg appointment-btn">Đặt lịch ngay</a>
    </div>
</section>

<!-- Mobile Filter Button -->
<button id="showFilterSidebar" class="mobile-filter-btn">
    <i class="fas fa-filter"></i>
</button>

<!-- Filter Backdrop -->
<div class="filter-backdrop"></div>
@endsection