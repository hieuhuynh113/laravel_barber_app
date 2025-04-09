@extends('layouts.frontend')

@section('title', 'Đánh giá của tôi')

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-body text-center">
                <img src="{{ $user->avatar ? asset($user->avatar) : asset('images/avatar-placeholder.jpg') }}" alt="{{ Auth::user()->name }}" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                <h5 class="card-title">{{ Auth::user()->name }}</h5>
                <p class="text-muted">Thành viên từ {{ Auth::user()->created_at->format('d/m/Y') }}</p>
                <a href="{{ route('profile.edit') }}" class="btn btn-primary mt-2">Chỉnh sửa hồ sơ</a>
            </div>
        </div>
        
        <div class="list-group mb-4">
            <a href="{{ route('profile.index') }}" class="list-group-item list-group-item-action">
                <i class="fas fa-user me-2"></i> Hồ sơ của tôi
            </a>
            <a href="{{ route('profile.appointments') }}" class="list-group-item list-group-item-action">
                <i class="fas fa-calendar me-2"></i> Lịch hẹn của tôi
            </a>
            <a href="{{ route('profile.reviews') }}" class="list-group-item list-group-item-action active">
                <i class="fas fa-star me-2"></i> Đánh giá của tôi
            </a>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Đánh giá của tôi</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif
                
                @if($reviews->count() > 0)
                    @foreach($reviews as $review)
                    <div class="review-card mb-4 p-3 border rounded">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5>{{ $review->service->name }}</h5>
                                <p class="text-muted mb-2">Thợ cắt: {{ $review->barber->name }}</p>
                                <div class="mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            <i class="fas fa-star text-warning"></i>
                                        @else
                                            <i class="far fa-star text-warning"></i>
                                        @endif
                                    @endfor
                                    <span class="ms-2 text-muted">{{ $review->created_at->format('d/m/Y') }}</span>
                                </div>
                                <p>{{ $review->comment }}</p>
                                
                                @if($review->images)
                                <div class="review-images mt-2">
                                    <div class="row">
                                        @foreach(json_decode($review->images) as $image)
                                        <div class="col-md-3 col-6 mb-2">
                                            <a href="{{ asset($image) }}" data-lightbox="review-{{ $review->id }}">
                                                <img src="{{ asset($image) }}" class="img-thumbnail" alt="Review image">
                                            </a>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div>
                                <form action="{{ route('profile.reviews.delete', $review->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đánh giá này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    
                    <div class="d-flex justify-content-center mt-4">
                        {{ $reviews->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-comment-slash fa-4x text-muted mb-3"></i>
                        <h5>Bạn chưa có đánh giá nào</h5>
                        <p class="text-muted">Hãy sử dụng dịch vụ và để lại đánh giá của bạn</p>
                        <a href="{{ route('services.index') }}" class="btn btn-primary mt-3">Xem các dịch vụ</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 