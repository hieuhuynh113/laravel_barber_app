@extends('layouts.admin')

@section('title', 'Chi tiết đánh giá')

@section('styles')
<style>
    .star-rating {
        color: #ffc107;
        font-size: 1.2rem;
    }
    .review-images {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 15px;
    }
    .review-image {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 5px;
        cursor: pointer;
    }
    .review-status {
        width: 12px;
        height: 12px;
        display: inline-block;
        border-radius: 50%;
        margin-right: 5px;
    }
    .status-active {
        background-color: #28a745;
    }
    .status-inactive {
        background-color: #dc3545;
    }
    .admin-response {
        background-color: #f8f9fa;
        border-left: 4px solid #0d6efd;
        padding: 15px;
        margin-top: 20px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết đánh giá #{{ $review->id }}</h1>
        <div>
            <a href="{{ route('admin.reviews.edit', $review->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Chỉnh sửa
            </a>
            <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin đánh giá</h6>
                    <div>
                        <span class="review-status {{ $review->status ? 'status-active' : 'status-inactive' }}"></span>
                        <span class="badge {{ $review->status ? 'bg-success' : 'bg-danger' }}">
                            {{ $review->status ? 'Hiển thị' : 'Ẩn' }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="star-rating me-3">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star"></i>
                                @endfor
                            </div>
                            <span class="text-muted">{{ $review->created_at->format('d/m/Y H:i') }}</span>
                        </div>

                        <h5 class="card-title">Nhận xét</h5>
                        <p class="card-text">{{ $review->comment }}</p>

                        @if($review->images)
                            <h5 class="card-title mt-4">Hình ảnh</h5>
                            <div class="review-images">
                                @foreach(json_decode($review->images) as $image)
                                    <a href="{{ asset($image) }}" target="_blank">
                                        <img src="{{ asset($image) }}" alt="Review image" class="review-image">
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        @if($review->admin_response)
                            <div class="admin-response">
                                <h5 class="card-title">Phản hồi của admin</h5>
                                <p class="card-text">{{ $review->admin_response }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <form action="{{ route('admin.reviews.toggleStatus', $review->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-{{ $review->status ? 'warning' : 'success' }}">
                                <i class="fas {{ $review->status ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                {{ $review->status ? 'Ẩn đánh giá' : 'Hiển thị đánh giá' }}
                            </button>
                        </form>

                        <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đánh giá này?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Xóa đánh giá
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin khách hàng</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ get_user_avatar($review->user, 'small') }}" alt="{{ $review->user->name }}" class="rounded-circle me-3" width="50" height="50">
                        <div>
                            <h6 class="mb-0">{{ $review->user->name }}</h6>
                            <small class="text-muted">{{ $review->user->email }}</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Số điện thoại:</strong> {{ $review->user->phone ?? 'Chưa cập nhật' }}
                    </div>

                    <div class="mb-3">
                        <strong>Thành viên từ:</strong> {{ $review->user->created_at->format('d/m/Y') }}
                    </div>

                    <a href="{{ route('admin.users.show', $review->user->id) }}" class="btn btn-info btn-sm">
                        <i class="fas fa-user"></i> Xem hồ sơ
                    </a>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin dịch vụ</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Dịch vụ:</strong> {{ $review->service->name }}
                    </div>

                    <div class="mb-3">
                        <strong>Giá:</strong> {{ number_format($review->service->price) }} VNĐ
                    </div>

                    <div class="mb-3">
                        <strong>Thời gian:</strong> {{ $review->service->duration }} phút
                    </div>

                    <a href="{{ route('admin.services.edit', $review->service->id) }}" class="btn btn-info btn-sm">
                        <i class="fas fa-edit"></i> Xem dịch vụ
                    </a>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin thợ cắt tóc</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ get_user_avatar($review->barber->user, 'small') }}" alt="{{ $review->barber->user->name }}" class="rounded-circle me-3" width="50" height="50">
                        <div>
                            <h6 class="mb-0">{{ $review->barber->user->name }}</h6>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Kinh nghiệm:</strong> {{ $review->barber->experience }} năm
                    </div>

                    <a href="{{ route('admin.barbers.show', $review->barber->user->id) }}" class="btn btn-info btn-sm">
                        <i class="fas fa-user"></i> Xem hồ sơ
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
