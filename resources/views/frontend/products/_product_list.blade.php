@forelse($products as $product)
<div class="col-md-6 col-lg-4 mb-4">
    <div class="card h-100 product-card">
        <div class="card-img-container position-relative">
            <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}">
            <div class="product-status position-absolute">
                <span class="{{ $product->stock > 0 ? 'in-stock' : 'out-of-stock' }}">{{ $product->stock > 0 ? 'Còn hàng' : 'Hết hàng' }}</span>
            </div>
        </div>
        <div class="card-body">
            <div class="product-category mb-2">
                <span class="badge bg-light text-dark">{{ $product->category->name }}</span>
            </div>
            <h5 class="card-title">{{ $product->name }}</h5>
            <p class="card-text">{{ Str::limit($product->description, 100) }}</p>
        </div>
        <div class="card-footer bg-white border-top-0 d-flex justify-content-between align-items-center">
            <span class="price text-primary fw-bold">{{ number_format($product->price) }} VNĐ</span>
            <a href="{{ route('products.show', $product->slug) }}" class="btn btn-sm btn-outline-primary">Chi tiết <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
    </div>
</div>
@empty
<div class="col-12">
    <div class="alert alert-info text-center p-5">
        <i class="fas fa-info-circle fa-3x mb-3"></i>
        <h4>Không tìm thấy sản phẩm nào</h4>
        <p>Hiện tại không có sản phẩm nào phù hợp với bộ lọc. Vui lòng thử lại với bộ lọc khác.</p>
    </div>
</div>
@endforelse
