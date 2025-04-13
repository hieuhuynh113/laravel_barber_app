@if($services->count() > 0)
    @foreach($services as $service)
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100 service-card">
            <div class="card-img-container position-relative">
                <img src="{{ asset('storage/' . $service->image) }}" class="card-img-top" alt="{{ $service->name }}">
                <div class="service-duration position-absolute">
                    <span>{{ $service->duration }} phút</span>
                </div>
            </div>
            <div class="card-body">
                <div class="service-category mb-2">
                    <span class="badge bg-light text-dark">{{ $service->category->name }}</span>
                </div>
                <h5 class="card-title">{{ $service->name }}</h5>
                <p class="card-text">{{ Str::limit($service->description, 100) }}</p>
            </div>
            <div class="card-footer bg-white border-top-0 d-flex justify-content-between align-items-center">
                <span class="price text-primary fw-bold">{{ number_format($service->price) }} VNĐ</span>
                <a href="{{ route('services.show', $service->slug) }}" class="btn btn-sm btn-outline-primary">Chi tiết <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
    @endforeach
@else
    <div class="col-12">
        <div class="alert alert-info text-center p-5">
            <i class="fas fa-info-circle fa-3x mb-3"></i>
            <h4>Không tìm thấy dịch vụ nào</h4>
            <p>Hiện tại không có dịch vụ nào phù hợp với bộ lọc. Vui lòng thử lại với bộ lọc khác.</p>
        </div>
    </div>
@endif
