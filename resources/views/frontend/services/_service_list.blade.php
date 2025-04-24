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
