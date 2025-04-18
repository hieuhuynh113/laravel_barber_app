@extends('layouts.admin')

@section('title', 'Chỉnh sửa dịch vụ')

@section('styles')
<style>
    .star-rating {
        color: #ffc107;
    }
    .rating-progress {
        height: 10px;
        margin-bottom: 10px;
    }
    .rating-count {
        min-width: 30px;
        text-align: right;
    }
    .rating-percentage {
        min-width: 50px;
        text-align: right;
    }
    .review-item {
        border-bottom: 1px solid #e3e6f0;
        padding-bottom: 15px;
        margin-bottom: 15px;
    }
    .review-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
        margin-bottom: 0;
    }
    .review-images {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
    }
    .review-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 5px;
        cursor: pointer;
    }
    .nav-tabs .nav-link {
        font-weight: 500;
    }
    .nav-tabs .nav-link.active {
        border-bottom: 3px solid #4e73df;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chỉnh sửa dịch vụ</h1>
        <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Thông tin dịch vụ</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.services.update', $service->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên dịch vụ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $service->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Danh mục <span class="text-danger">*</span></label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                <option value="">-- Chọn danh mục --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ (old('category_id', $service->category_id) == $category->id) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="price" class="form-label">Giá (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $service->price) }}" min="0" required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="duration" class="form-label">Thời gian (phút) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('duration') is-invalid @enderror" id="duration" name="duration" value="{{ old('duration', $service->duration) }}" min="1" required>
                            @error('duration')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Mô tả</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $service->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Hình ảnh</label>
                    @if($service->image)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->name }}" class="img-thumbnail" style="max-height: 150px;">
                        </div>
                    @endif
                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">
                    <small class="text-muted">Tải lên hình ảnh mới để thay đổi (để trống nếu giữ nguyên hình ảnh hiện tại)</small>
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                        <option value="1" {{ old('status', $service->status) == 1 ? 'selected' : '' }}>Hoạt động</option>
                        <option value="0" {{ old('status', $service->status) == 0 ? 'selected' : '' }}>Không hoạt động</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Cập nhật dịch vụ
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabs for service details and reviews -->
    <ul class="nav nav-tabs mt-4" id="serviceTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab" aria-controls="details" aria-selected="true">
                <i class="fas fa-info-circle"></i> Thông tin chi tiết
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews" aria-selected="false">
                <i class="fas fa-star"></i> Đánh giá ({{ $reviewsCount }})
            </button>
        </li>
    </ul>

    <div class="tab-content" id="serviceTabsContent">
        <!-- Details Tab -->
        <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
            <!-- Thông tin chi tiết dịch vụ -->
        </div>

        <!-- Reviews Tab -->
        <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
            <div class="card shadow mb-4 mt-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Đánh giá dịch vụ</h6>
                    <a href="{{ route('admin.reviews.index', ['service_id' => $service->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-list"></i> Xem tất cả đánh giá
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Thống kê đánh giá -->
                        <div class="col-lg-4">
                            <div class="text-center mb-4">
                                <h1 class="display-4 font-weight-bold">{{ number_format($averageRating, 1) }}</h1>
                                <div class="star-rating mb-2" style="font-size: 1.5rem;">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= round($averageRating))
                                            <i class="fas fa-star"></i>
                                        @elseif($i - 0.5 <= $averageRating)
                                            <i class="fas fa-star-half-alt"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                                <p class="text-muted">{{ $reviewsCount }} đánh giá</p>
                            </div>

                            <!-- Phân bố đánh giá theo số sao -->
                            <div class="rating-distribution">
                                @foreach($ratingDistribution as $rating => $data)
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="rating-count me-2">{{ $rating }}</div>
                                        <div class="star-rating me-2">
                                            <i class="fas fa-star"></i>
                                        </div>
                                        <div class="flex-grow-1 mx-2">
                                            <div class="progress rating-progress">
                                                <div class="progress-bar bg-{{ $rating >= 4 ? 'success' : ($rating >= 3 ? 'info' : ($rating >= 2 ? 'warning' : 'danger')) }}"
                                                    role="progressbar" style="width: {{ $data['percentage'] }}%"
                                                    aria-valuenow="{{ $data['percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                        <div class="rating-count me-2">{{ $data['count'] }}</div>
                                        <div class="rating-percentage">({{ $data['percentage'] }}%)</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Danh sách đánh giá -->
                        <div class="col-lg-8">
                            @if($reviews->count() > 0)
                                @foreach($reviews as $review)
                                    <div class="review-item">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="d-flex align-items-center">
                                                @if($review->user->avatar)
                                                    <img src="{{ asset('storage/' . $review->user->avatar) }}" alt="{{ $review->user->name }}" class="rounded-circle me-2" width="40" height="40">
                                                @else
                                                    <img src="{{ asset('images/default-avatar.jpg') }}" alt="{{ $review->user->name }}" class="rounded-circle me-2" width="40" height="40">
                                                @endif
                                                <div>
                                                    <h6 class="mb-0">{{ $review->user->name }}</h6>
                                                    <small class="text-muted">{{ $review->created_at->format('d/m/Y H:i') }}</small>
                                                </div>
                                            </div>
                                            <div class="star-rating">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star"></i>
                                                @endfor
                                            </div>
                                        </div>

                                        <div class="mb-2">
                                            <strong>Thợ cắt tóc:</strong> {{ $review->barber->user->name }}
                                        </div>

                                        <p class="mb-2">{{ $review->comment }}</p>

                                        @if($review->images)
                                            <div class="review-images">
                                                @foreach(json_decode($review->images) as $image)
                                                    <a href="{{ asset($image) }}" target="_blank">
                                                        <img src="{{ asset($image) }}" alt="Review image" class="review-image">
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif

                                        @if($review->admin_response)
                                            <div class="alert alert-info mt-2 mb-0">
                                                <strong>Phản hồi của admin:</strong> {{ $review->admin_response }}
                                            </div>
                                        @endif

                                        <div class="mt-2">
                                            <a href="{{ route('admin.reviews.edit', $review->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> Chỉnh sửa
                                            </a>
                                            <a href="{{ route('admin.reviews.show', $review->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> Xem chi tiết
                                            </a>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="mt-3">
                                    {{ $reviews->appends(['reviews_page' => $reviews->currentPage()])->links() }}
                                </div>
                            @else
                                <div class="alert alert-info">
                                    Chưa có đánh giá nào cho dịch vụ này.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Xử lý tabs
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab');

        if (activeTab === 'reviews') {
            $('#reviews-tab').tab('show');
        }

        // Lưu tab đang active vào URL khi chuyển tab
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            const target = $(e.target).attr('id');
            if (target === 'reviews-tab') {
                history.replaceState(null, null, '?tab=reviews');
            } else {
                history.replaceState(null, null, window.location.pathname);
            }
        });

        // Mô tả sử dụng WYSIWYG editor nếu cần
        /*
        ClassicEditor
            .create(document.querySelector('#description'))
            .catch(error => {
                console.error(error);
            });
        */
    });
</script>
@endsection