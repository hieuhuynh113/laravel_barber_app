@extends('layouts.admin')

@section('title', 'Chỉnh sửa đánh giá')

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
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chỉnh sửa đánh giá #{{ $review->id }}</h1>
        <a href="{{ route('admin.reviews.show', $review->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin đánh giá</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.reviews.update', $review->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label class="form-label">Khách hàng</label>
                            <input type="text" class="form-control" value="{{ $review->user->name }}" disabled>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Dịch vụ</label>
                            <input type="text" class="form-control" value="{{ $review->service->name }}" disabled>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Thợ cắt tóc</label>
                            <input type="text" class="form-control" value="{{ $review->barber->user->name }}" disabled>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Đánh giá</label>
                            <div class="star-rating">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star"></i>
                                @endfor
                                ({{ $review->rating }} sao)
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Nhận xét</label>
                            <textarea class="form-control" rows="4" disabled>{{ $review->comment }}</textarea>
                        </div>
                        
                        @if($review->images)
                            <div class="mb-4">
                                <label class="form-label">Hình ảnh</label>
                                <div class="review-images">
                                    @foreach(json_decode($review->images) as $image)
                                        <a href="{{ asset($image) }}" target="_blank">
                                            <img src="{{ asset($image) }}" alt="Review image" class="review-image">
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        <div class="mb-4">
                            <label for="admin_response" class="form-label">Phản hồi của admin</label>
                            <textarea class="form-control @error('admin_response') is-invalid @enderror" id="admin_response" name="admin_response" rows="4">{{ old('admin_response', $review->admin_response) }}</textarea>
                            @error('admin_response')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Phản hồi này sẽ được hiển thị cho khách hàng.</small>
                        </div>
                        
                        <div class="mb-4">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="1" {{ old('status', $review->status) == 1 ? 'selected' : '' }}>Hiển thị</option>
                                <option value="0" {{ old('status', $review->status) == 0 ? 'selected' : '' }}>Ẩn</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu thay đổi
                            </button>
                            
                            <a href="{{ route('admin.reviews.show', $review->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin bổ sung</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Ngày tạo:</strong> {{ $review->created_at->format('d/m/Y H:i') }}
                    </div>
                    
                    <div class="mb-3">
                        <strong>Cập nhật lần cuối:</strong> {{ $review->updated_at->format('d/m/Y H:i') }}
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Lưu ý: Admin chỉ có thể thay đổi trạng thái hiển thị và thêm phản hồi cho đánh giá. Nội dung đánh giá của khách hàng không thể chỉnh sửa.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
