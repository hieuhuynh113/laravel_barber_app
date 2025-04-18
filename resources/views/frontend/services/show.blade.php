@extends('layouts.frontend')

@section('title', $service->name)

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('services.index') }}">Dịch vụ</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $service->name }}</li>
            </ol>
        </nav>

        <div class="row mt-4">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="position-relative">
                    <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->name }}" class="img-fluid rounded shadow">
                    <div class="position-absolute top-0 end-0 bg-primary text-white p-3 rounded-start" style="transform: translateY(30px);">
                        <h4 class="mb-0">{{ number_format($service->price) }} VNĐ</h4>
                    </div>
                </div>

                <div class="row mt-3">
                    @if($service->gallery_images)
                        @foreach(json_decode($service->gallery_images) as $galleryImage)
                            <div class="col-3 mt-2">
                                <img src="{{ asset('storage/' . $galleryImage) }}" alt="{{ $service->name }}" class="img-fluid rounded cursor-pointer gallery-image" data-src="{{ asset('storage/' . $galleryImage) }}">
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-lg-6">
                <h1 class="mb-3">{{ $service->name }}</h1>

                <div class="mb-4">
                    <div class="stars">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $service->rating)
                                <i class="fas fa-star text-warning"></i>
                            @else
                                <i class="far fa-star text-warning"></i>
                            @endif
                        @endfor
                        <span class="ms-2">({{ $service->reviews_count }} đánh giá)</span>
                    </div>
                </div>

                <div class="mb-4">
                    <h5 class="mb-3">Mô tả dịch vụ</h5>
                    <div class="service-description">
                        {!! $service->description !!}
                    </div>
                </div>

                <div class="mb-4">
                    <h5 class="mb-3">Thời gian thực hiện</h5>
                    <div class="d-flex align-items-center mb-2">
                        <i class="far fa-clock text-primary me-2"></i>
                        <span>{{ $service->duration }} phút</span>
                    </div>
                </div>

                @if($service->includes)
                <div class="mb-4">
                    <h5 class="mb-3">Bao gồm</h5>
                    <ul class="list-group list-group-flush">
                        @foreach(json_decode($service->includes) as $include)
                        <li class="list-group-item bg-transparent px-0">
                            <i class="fas fa-check text-success me-2"></i> {{ $include }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="d-grid gap-2">
                    <a href="{{ route('appointment.step1', ['service_id' => $service->id]) }}" class="btn btn-primary btn-lg appointment-btn">Đặt lịch ngay</a>
                    <a href="{{ route('services.index') }}" class="btn btn-outline-secondary">Xem các dịch vụ khác</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <ul class="nav nav-tabs mb-4" id="serviceTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews" aria-selected="true">Đánh giá ({{ $service->reviews_count }})</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="faq-tab" data-bs-toggle="tab" data-bs-target="#faq" type="button" role="tab" aria-controls="faq" aria-selected="false">Câu hỏi thường gặp</button>
            </li>
        </ul>

        <div class="tab-content" id="serviceTabContent">
            <div class="tab-pane fade show active" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                <div class="row">
                    <div class="col-lg-8">
                        @if($reviews->count() > 0)
                            @foreach($reviews as $review)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex mb-3">
                                        <img src="{{ $review->user->avatar ? asset('storage/' . $review->user->avatar) : asset('images/default-avatar.jpg') }}" class="rounded-circle me-3" width="50" height="50" alt="{{ $review->user->name }}">
                                        <div>
                                            <h6 class="mb-0">{{ $review->user->name }}</h6>
                                            <div class="small text-muted">{{ $review->created_at->format('d/m/Y') }}</div>
                                        </div>
                                    </div>

                                    <div class="stars mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <i class="fas fa-star text-warning"></i>
                                            @else
                                                <i class="far fa-star text-warning"></i>
                                            @endif
                                        @endfor
                                    </div>

                                    <p class="card-text">{{ $review->comment }}</p>

                                    @if($review->images)
                                    <div class="review-images mt-3">
                                        <div class="row">
                                            @foreach(json_decode($review->images) as $image)
                                            <div class="col-2">
                                                <img src="{{ asset($image) }}" class="img-fluid rounded cursor-pointer" alt="Review image">
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach

                            <div class="mt-4">
                                {{ $reviews->links() }}
                            </div>
                        @else
                            <div class="alert alert-info">
                                Chưa có đánh giá nào cho dịch vụ này. Hãy là người đầu tiên đánh giá!
                            </div>
                        @endif
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Đánh giá dịch vụ</h5>
                                <p class="card-text">Hãy chia sẻ trải nghiệm của bạn về dịch vụ này.</p>

                                @auth
                                    @php
                                        // Kiểm tra xem người dùng đã sử dụng dịch vụ này chưa
                                        $hasUsedService = \App\Models\Appointment::where('user_id', Auth::id())
                                            ->where('status', 'completed')
                                            ->whereHas('services', function($query) use ($service) {
                                                $query->where('services.id', $service->id);
                                            })
                                            ->exists();
                                    @endphp

                                    @if($hasUsedService)
                                        <div class="alert alert-info">
                                            Bạn đã sử dụng dịch vụ này. Để đánh giá, vui lòng vào <a href="{{ route('profile.appointments') }}" class="alert-link">trang lịch hẹn</a> và chọn tab "Đã hoàn thành" để đánh giá dịch vụ.
                                        </div>
                                    @else
                                        <div class="alert alert-warning">
                                            Bạn chỉ có thể đánh giá dịch vụ sau khi đã sử dụng. Vui lòng <a href="{{ route('appointment.step1', ['service_id' => $service->id]) }}" class="alert-link">đặt lịch</a> và sử dụng dịch vụ trước.
                                        </div>
                                    @endif
                                @else
                                <div class="alert alert-info">
                                    Vui lòng <a href="{{ route('login') }}">đăng nhập</a> để đánh giá dịch vụ này.
                                </div>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="faq" role="tabpanel" aria-labelledby="faq-tab">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseOne" aria-expanded="true" aria-controls="faqCollapseOne">
                                Dịch vụ này có phù hợp với tóc của tôi không?
                            </button>
                        </h2>
                        <div id="faqCollapseOne" class="accordion-collapse collapse show" aria-labelledby="faqOne" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Các thợ cắt tóc của chúng tôi đều có kinh nghiệm làm việc với nhiều loại tóc khác nhau. Khi bạn đến, chúng tôi sẽ đánh giá tình trạng tóc của bạn và tư vấn liệu dịch vụ này có phù hợp không. Nếu không, chúng tôi sẽ đề xuất các lựa chọn thay thế tốt hơn.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseTwo" aria-expanded="false" aria-controls="faqCollapseTwo">
                                Tôi cần chuẩn bị gì trước khi đến sử dụng dịch vụ?
                            </button>
                        </h2>
                        <div id="faqCollapseTwo" class="accordion-collapse collapse" aria-labelledby="faqTwo" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Bạn không cần chuẩn bị gì đặc biệt. Tuy nhiên, nếu có thể, hãy mang theo hình ảnh kiểu tóc mong muốn để thợ cắt tóc có thể hiểu rõ hơn về mong muốn của bạn. Đồng thời, đến đúng giờ hẹn sẽ giúp chúng tôi phục vụ bạn tốt nhất.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseThree" aria-expanded="false" aria-controls="faqCollapseThree">
                                Có cần đặt lịch trước khi sử dụng dịch vụ này không?
                            </button>
                        </h2>
                        <div id="faqCollapseThree" class="accordion-collapse collapse" aria-labelledby="faqThree" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Chúng tôi khuyến khích đặt lịch trước để đảm bảo bạn được phục vụ đúng thời gian mong muốn và không phải chờ đợi. Việc đặt lịch cũng giúp chúng tôi sắp xếp thợ cắt tóc phù hợp nhất với yêu cầu của bạn.
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
        <h2 class="text-center mb-4">Dịch vụ tương tự</h2>

        <div class="row">
            @foreach($relatedServices as $relatedService)
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card h-100 service-card">
                    <img src="{{ asset('storage/' . $relatedService->image) }}" class="card-img-top" alt="{{ $relatedService->name }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $relatedService->name }}</h5>
                        <p class="card-text">{{ Str::limit($relatedService->description, 80) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="price text-primary fw-bold">{{ number_format($relatedService->price) }} VNĐ</span>
                            <a href="{{ route('services.show', $relatedService->slug) }}" class="btn btn-sm btn-outline-primary">Chi tiết</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý đánh giá sao
        const ratingStars = document.querySelectorAll('.rating-star');
        const ratingInput = document.getElementById('rating');

        ratingStars.forEach(star => {
            star.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                ratingInput.value = value;

                // Cập nhật hiển thị sao
                ratingStars.forEach(s => {
                    if (s.getAttribute('data-value') <= value) {
                        s.classList.remove('far');
                        s.classList.add('fas');
                    } else {
                        s.classList.remove('fas');
                        s.classList.add('far');
                    }
                });
            });
        });

        // Xử lý gallery ảnh
        const galleryImages = document.querySelectorAll('.gallery-image');
        const mainImage = document.querySelector('.position-relative img');

        galleryImages.forEach(img => {
            img.addEventListener('click', function() {
                const src = this.getAttribute('data-src');
                mainImage.src = src;
            });
        });
    });
</script>
@endsection