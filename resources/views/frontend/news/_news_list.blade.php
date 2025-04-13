@forelse($news as $item)
<div class="col-md-6 col-lg-4 mb-4">
    <div class="card h-100 news-card">
        <div class="card-img-container position-relative">
            <img src="{{ asset('storage/' . $item->image) }}" class="card-img-top" alt="{{ $item->title }}">
            <div class="news-date position-absolute">
                <span><i class="far fa-calendar-alt me-1"></i>{{ $item->created_at->format('d/m/Y') }}</span>
            </div>
        </div>
        <div class="card-body">
            <div class="news-category mb-2">
                <span class="badge bg-light text-dark">{{ $item->category->name }}</span>
            </div>
            <h5 class="card-title">{{ $item->title }}</h5>
            <p class="card-text">{{ Str::limit($item->excerpt, 100) }}</p>
        </div>
        <div class="card-footer bg-white border-top-0 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <img src="{{ asset('storage/' . $item->user->avatar) }}" alt="{{ $item->user->name }}" class="rounded-circle me-2" width="25" height="25">
                <small>{{ $item->user->name }}</small>
            </div>
            <a href="{{ route('news.show', $item->slug) }}" class="btn btn-sm btn-outline-primary">Đọc tiếp <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
    </div>
</div>
@empty
<div class="col-12">
    <div class="alert alert-info text-center p-5">
        <i class="fas fa-info-circle fa-3x mb-3"></i>
        <h4>Không tìm thấy bài viết nào</h4>
        <p>Hiện tại không có bài viết nào phù hợp với bộ lọc. Vui lòng thử lại với bộ lọc khác.</p>
    </div>
</div>
@endforelse
