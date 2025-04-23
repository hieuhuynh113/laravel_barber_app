@extends('layouts.frontend')

@section('title', $news->title)

@section('content')
<section class="py-5">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('news.index') }}">Tin tức</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $news->title }}</li>
            </ol>
        </nav>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <article class="blog-post">
                    <h1 class="blog-post-title mb-3">{{ $news->title }}</h1>

                    <div class="blog-post-meta d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex align-items-center">
                            <img src="{{ asset('storage/' . $news->user->avatar) }}" alt="{{ $news->user->name }}" class="rounded-circle me-2" width="32" height="32">
                            <span class="me-3">{{ $news->user->name }}</span>
                            <span class="me-3"><i class="far fa-calendar-alt me-1"></i> {{ $news->created_at->format('d/m/Y') }}</span>
                            <span><i class="far fa-eye me-1"></i> {{ $news->views }} lượt xem</span>
                        </div>
                        <span class="badge bg-primary">{{ $news->category->name }}</span>
                    </div>

                    @if($news->image)
                    <div class="blog-post-image mb-4">
                        <img src="{{ asset('storage/' . $news->image) }}" alt="{{ $news->title }}" class="img-fluid rounded">
                    </div>
                    @endif

                    <div class="blog-post-excerpt mb-4 lead">
                        {{ $news->excerpt }}
                    </div>

                    <div class="blog-post-content mb-5">
                        {!! $news->content !!}
                    </div>

                    <div class="blog-post-tags mb-5">
                        <span class="fw-bold me-2"><i class="fas fa-tags me-1"></i> Tags:</span>
                        <a href="#" class="badge bg-light text-dark text-decoration-none me-1">Barber</a>
                        <a href="#" class="badge bg-light text-dark text-decoration-none me-1">Tóc nam</a>
                        <a href="#" class="badge bg-light text-dark text-decoration-none me-1">Xu hướng</a>
                    </div>

                    <div class="blog-post-share d-flex align-items-center mb-5">
                        <span class="fw-bold me-3">Chia sẻ:</span>
                        <a href="#" class="btn btn-outline-primary btn-sm me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="btn btn-outline-info btn-sm me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="btn btn-outline-danger btn-sm"><i class="fab fa-pinterest"></i></a>
                    </div>
                </article>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">Bài viết liên quan</h2>

        <div class="row">
            @foreach($relatedNews as $item)
            <div class="col-md-4 mb-4">
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
                        <a href="{{ route('news.show', $item->slug) }}" class="btn btn-sm btn-outline-primary">Đọc tiếp</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-5 text-white text-center cta-appointment">
    <div class="container">
        <h2 class="h1 mb-4">Cần tư vấn thêm?</h2>
        <p class="lead mb-4">Liên hệ với chúng tôi để được tư vấn về các dịch vụ và sản phẩm.</p>
        <a href="{{ route('contact.index') }}" class="btn btn-light btn-lg appointment-btn">Liên hệ ngay</a>
    </div>
</section>
@endsection