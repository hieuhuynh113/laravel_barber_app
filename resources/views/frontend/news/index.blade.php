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
        <h1 class="text-center mb-5">Tin tức & Sự kiện</h1>

        @if($categories->count() > 0)
        <div class="mb-4">
            <div class="d-flex justify-content-center flex-wrap">
                <a href="{{ route('news.index') }}" class="btn {{ !$categoryId ? 'btn-primary' : 'btn-outline-primary' }} me-2 mb-2">
                    Tất cả
                </a>
                @foreach($categories as $category)
                <a href="{{ route('news.index', ['category_id' => $category->id]) }}" class="btn {{ $categoryId == $category->id ? 'btn-primary' : 'btn-outline-primary' }} me-2 mb-2">
                    {{ $category->name }}
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <div class="row">
            @forelse($news as $item)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 news-card">
                    <img src="{{ asset('storage/' . $item->image) }}" class="card-img-top" alt="{{ $item->title }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-primary">{{ $item->category->name }}</span>
                            <small class="text-muted">{{ $item->created_at->format('d/m/Y') }}</small>
                        </div>
                        <h5 class="card-title">{{ $item->title }}</h5>
                        <p class="card-text">{{ Str::limit($item->excerpt, 100) }}</p>
                    </div>
                    <div class="card-footer bg-white border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('storage/' . $item->user->avatar) }}" alt="{{ $item->user->name }}" class="rounded-circle me-2" width="25" height="25">
                                <small>{{ $item->user->name }}</small>
                            </div>
                            <a href="{{ route('news.show', $item->slug) }}" class="btn btn-sm btn-outline-primary">Đọc tiếp</a>
                        </div>
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

        <div class="mt-4">
            {{ $news->links() }}
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