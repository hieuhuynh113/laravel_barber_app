@extends('layouts.frontend')

@section('title', $news->title)

@section('styles')
<style>
    /* Mục lục */
    .table-of-contents {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 1.5rem;
        margin: 0 0 2rem 0;
        border-left: 4px solid #0d6efd;
        width: 100%;
        float: none;
        clear: both;
    }

    .table-of-contents h5 {
        margin-bottom: 1rem;
        color: #0d6efd;
    }

    .table-of-contents ul {
        padding-left: 1.5rem;
        margin-bottom: 0;
    }

    .table-of-contents li {
        margin-bottom: 0.5rem;
    }

    .table-of-contents a {
        color: #495057;
        text-decoration: none;
    }

    .table-of-contents a:hover {
        color: #0d6efd;
        text-decoration: underline;
    }

    /* Cải thiện trải nghiệm đọc */
    .blog-post-content {
        font-family: 'Georgia', serif;
        line-height: 1.8;
        font-size: 1.1rem;
        color: #333;
    }

    .blog-post-content h2, .blog-post-content h3, .blog-post-content h4 {
        margin-top: 2rem;
        margin-bottom: 1rem;
        font-weight: 600;
    }

    .blog-post-content p {
        margin-bottom: 1.5rem;
    }

    .blog-post-content img {
        max-width: 100%;
        height: auto;
        margin: 1.5rem 0;
        border-radius: 8px;
    }

    /* Bài viết phổ biến */
    .popular-posts .post-item {
        display: flex;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #eee;
    }

    .popular-posts .post-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .popular-posts .post-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 4px;
        margin-right: 1rem;
    }

    .popular-posts .post-title {
        font-size: 0.95rem;
        margin-bottom: 0.25rem;
        font-weight: 500;
    }

    .popular-posts .post-meta {
        font-size: 0.8rem;
        color: #6c757d;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tạo mục lục tự động
        function generateTableOfContents() {
            const content = document.getElementById('blogPostContent');
            // Chỉ tìm các heading trong nội dung bài viết, không bao gồm heading của mục lục
            const contentHTML = content.innerHTML;
            const contentWithoutTOC = contentHTML.replace(/<div class="table-of-contents.*?<\/div>/s, '');
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = contentWithoutTOC;

            const headings = tempDiv.querySelectorAll('h2, h3, h4');
            const tocList = document.getElementById('tocList');
            const tableOfContents = document.getElementById('tableOfContents');

            if (headings.length === 0) {
                tableOfContents.style.display = 'none';
                return;
            }

            // Tìm các heading tương ứng trong nội dung thực tế
            const actualHeadings = content.querySelectorAll('h2, h3, h4');

            headings.forEach((heading, index) => {
                // Tìm heading tương ứng trong DOM thực tế
                const actualHeading = actualHeadings[index];

                // Thêm ID cho heading thực tế nếu chưa có
                if (actualHeading && !actualHeading.id) {
                    actualHeading.id = `heading-${index}`;
                }

                const listItem = document.createElement('li');
                const link = document.createElement('a');
                link.href = `#heading-${index}`;
                link.textContent = heading.textContent;

                // Thêm padding cho các heading cấp thấp hơn
                if (heading.tagName === 'H3') {
                    listItem.style.paddingLeft = '1rem';
                } else if (heading.tagName === 'H4') {
                    listItem.style.paddingLeft = '2rem';
                }

                listItem.appendChild(link);
                tocList.appendChild(listItem);
            });
        }

        generateTableOfContents();
    });
</script>
@endsection

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

        <div class="row">
            <div class="col-lg-8">
                <article class="blog-post">
                    <h1 class="blog-post-title mb-3">{{ $news->title }}</h1>

                    <div class="blog-post-meta mb-2">
                        <div class="d-flex align-items-center mb-2">
                            <img src="{{ asset('storage/' . $news->user->avatar) }}" alt="{{ $news->user->name }}" class="rounded-circle me-2" width="32" height="32">
                            <span class="me-3">{{ $news->user->name }}</span>
                            <span class="me-3"><i class="far fa-calendar-alt me-1"></i> {{ $news->created_at->format('d/m/Y') }}</span>
                            <span><i class="far fa-eye me-1"></i> {{ $news->view_count }} lượt xem</span>
                        </div>
                        <div>
                            <a href="{{ route('news.index', ['category_id' => $news->category_id]) }}" class="btn btn-sm btn-primary rounded-pill px-3">
                                <i class="fas fa-tag me-1"></i> {{ $news->category->name }}
                            </a>
                        </div>
                    </div>

                    @if($news->image)
                    <div class="blog-post-image mb-4">
                        <img src="{{ asset('storage/' . $news->image) }}" alt="{{ $news->title }}" class="img-fluid rounded">
                    </div>
                    @endif

                    <div class="blog-post-excerpt mb-4 lead">
                        {{ $news->excerpt }}
                    </div>

                    <div class="blog-post-content mb-5" id="blogPostContent">
                        <!-- Mục lục tự động -->
                        <div class="table-of-contents mb-4" id="tableOfContents">
                            <h5><i class="fas fa-list me-2"></i>Mục lục</h5>
                            <ul id="tocList">
                                <!-- Sẽ được điền bởi JavaScript -->
                            </ul>
                        </div>

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

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Bài viết phổ biến</h5>
                    </div>
                    <div class="card-body popular-posts">
                        @foreach($popularNews as $item)
                        <div class="post-item">
                            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}" class="post-image">
                            <div>
                                <h6 class="post-title">
                                    <a href="{{ route('news.show', $item->slug) }}" class="text-decoration-none">{{ Str::limit($item->title, 60) }}</a>
                                </h6>
                                <div class="post-meta">
                                    <i class="far fa-calendar-alt me-1"></i> {{ $item->created_at->format('d/m/Y') }}
                                    <i class="far fa-eye ms-2 me-1"></i> {{ $item->view_count }} lượt xem
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
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