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
        <!-- New Filter UI based on reference image -->
        <div class="filter-header mb-4">
            <div class="d-flex align-items-center justify-content-between">
                <div class="filter-toggle d-flex align-items-center">
                    <i class="fas fa-filter me-2"></i>
                    <span>Filters:</span>
                </div>
                <div class="filter-count">
                    Hiển thị {{ $news->count() }} / {{ $news->total() }} bài viết
                </div>
            </div>
        </div>

        <div class="filter-options mb-4">
            <div class="row">
                <div class="col-md-6 mb-3 mb-md-0">
                    <div class="filter-group">
                        <label for="categoryFilter">Danh mục:</label>
                        <select class="form-select" id="categoryFilter">
                            <option value="" {{ !$categoryId ? 'selected' : '' }}>Tất cả danh mục</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="filter-group">
                        <label for="timeFilter">Thời gian:</label>
                        <select class="form-select" id="timeFilter">
                            <option value="">Tất cả thời gian</option>
                            <option value="today" {{ $timeFilter == 'today' ? 'selected' : '' }}>Hôm nay</option>
                            <option value="week" {{ $timeFilter == 'week' ? 'selected' : '' }}>Tuần này</option>
                            <option value="month" {{ $timeFilter == 'month' ? 'selected' : '' }}>Tháng này</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="filter-tabs mb-4">
            <div class="filter-tab {{ !$sort ? 'active' : '' }}" data-sort="">Tất cả bài viết</div>
            <div class="filter-tab {{ $sort == 'popular' ? 'active' : '' }}" data-sort="popular">Phổ biến nhất</div>
            <div class="filter-tab {{ $sort == 'newest' ? 'active' : '' }}" data-sort="newest">Mới nhất</div>
            <div class="filter-tab {{ $sort == 'recommended' ? 'active' : '' }}" data-sort="recommended">Đề xuất</div>
        </div>

        <div class="row" id="news-container">
            @forelse($news as $item)
            <div class="col-md-6 col-lg-4 mb-4">
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
                <div class="alert alert-info text-center">
                    Hiện tại chưa có bài viết nào. Vui lòng quay lại sau.
                </div>
            </div>
            @endforelse
        </div>

        <div class="mt-4 pagination-container">
            {{ $news->appends(request()->query())->links() }}
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

<section class="py-5 bg-primary text-white text-center cta-appointment">
    <div class="container">
        <h2 class="h1 mb-4">Cập nhật tin tức mới nhất</h2>
        <p class="lead mb-4">Đặt lịch ngay hôm nay để trải nghiệm dịch vụ tuyệt vời tại Barber Shop.</p>
        <a href="{{ route('appointment.step1') }}" class="btn btn-light btn-lg appointment-btn">Đặt lịch ngay</a>
    </div>
</section>
@endsection