@extends('layouts.frontend')

@section('title', 'Sản phẩm')

@section('content')
@include('partials.page-header', [
    'title' => 'Sản phẩm của chúng tôi',
    'description' => 'Khám phá các sản phẩm chăm sóc tóc chất lượng cao từ những thương hiệu nổi tiếng',
    'backgroundImage' => 'images/hero-bg-3.jpg'
])

<section class="py-5 bg-light">
    <div class="container">
        <!-- New Filter UI based on reference image -->
        <div class="filter-header mb-4">
            <div class="d-flex align-items-center justify-content-between">
                <div class="filter-toggle d-flex align-items-center">
                    <i class="fas fa-filter me-2"></i>
                    <span>Filters:</span>
                </div>
                <div class="filter-count">
                    Hiển thị {{ $products->count() }} / {{ $products->total() }} sản phẩm
                </div>
            </div>
        </div>

        <div class="filter-options mb-4">
            <div class="row">
                <div class="col-md-6 mb-3 mb-md-0">
                    <div class="filter-group">
                        <label for="categoryFilter">Danh mục:</label>
                        <select class="form-select" id="categoryFilter">
                            <option value="" {{ !$categoryId ? 'selected' : '' }}>Tất cả danh mục</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="filter-group">
                        <label for="priceFilter">Giá:</label>
                        <select class="form-select" id="priceFilter">
                            <option value="">Tất cả mức giá</option>
                            <option value="low">Dưới 200.000 VNĐ</option>
                            <option value="medium">200.000 - 500.000 VNĐ</option>
                            <option value="high">Trên 500.000 VNĐ</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="filter-tabs mb-4">
            <div class="filter-tab {{ !$sort ? 'active' : '' }}" data-sort="">Tất cả sản phẩm</div>
            <div class="filter-tab {{ $sort == 'popular' ? 'active' : '' }}" data-sort="popular">Phổ biến nhất</div>
            <div class="filter-tab {{ $sort == 'newest' ? 'active' : '' }}" data-sort="newest">Mới nhất</div>
            <div class="filter-tab {{ $sort == 'recommended' ? 'active' : '' }}" data-sort="recommended">Đề xuất</div>
        </div>

        <div class="row" id="products-container">
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
                <div class="alert alert-info text-center">
                    Hiện tại chưa có sản phẩm nào. Vui lòng quay lại sau.
                </div>
            </div>
            @endforelse
        </div>

        <div class="mt-4 pagination-container">
            {{ $products->appends(request()->query())->links() }}
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h2>Tại sao chọn sản phẩm của chúng tôi?</h2>
                <p>Barber Shop tự hào cung cấp các sản phẩm chăm sóc tóc và da đầu chất lượng cao. Chúng tôi cam kết chỉ bán những sản phẩm tốt nhất để quý khách có mái tóc khỏe mạnh và đẹp.</p>

                <div class="mt-4">
                    <div class="d-flex mb-3">
                        <div class="feature-icon me-3">
                            <i class="fas fa-check-circle text-primary" style="font-size: 24px;"></i>
                        </div>
                        <div>
                            <h5>Chất lượng đảm bảo</h5>
                            <p class="text-muted">Sản phẩm được nhập khẩu chính hãng, có giấy chứng nhận rõ ràng.</p>
                        </div>
                    </div>

                    <div class="d-flex mb-3">
                        <div class="feature-icon me-3">
                            <i class="fas fa-leaf text-primary" style="font-size: 24px;"></i>
                        </div>
                        <div>
                            <h5>An toàn cho sức khỏe</h5>
                            <p class="text-muted">Nhiều sản phẩm có thành phần thiên nhiên, an toàn và lành tính.</p>
                        </div>
                    </div>

                    <div class="d-flex">
                        <div class="feature-icon me-3">
                            <i class="fas fa-star text-primary" style="font-size: 24px;"></i>
                        </div>
                        <div>
                            <h5>Hiệu quả cao</h5>
                            <p class="text-muted">Sản phẩm được thử nghiệm và đánh giá cao bởi khách hàng và chuyên gia.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="position-relative">
                    <img src="{{ asset('images/products-main.jpg') }}" alt="Sản phẩm chăm sóc tóc" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="mb-4">Câu hỏi thường gặp</h2>
                <p class="mb-5">Một số câu hỏi khách hàng thường hỏi về sản phẩm của chúng tôi</p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Sản phẩm có phù hợp với mọi loại tóc không?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Chúng tôi có nhiều dòng sản phẩm khác nhau cho từng loại tóc. Quý khách có thể tham khảo thông tin chi tiết trên trang sản phẩm hoặc tư vấn trực tiếp với nhân viên để chọn sản phẩm phù hợp nhất.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Chính sách đổi trả sản phẩm như thế nào?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Chúng tôi áp dụng chính sách đổi trả trong vòng 7 ngày đối với sản phẩm chưa qua sử dụng và còn nguyên seal. Trường hợp sản phẩm bị lỗi do nhà sản xuất, chúng tôi sẽ đổi sản phẩm mới cho quý khách.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                Cách sử dụng sản phẩm hiệu quả nhất?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Mỗi sản phẩm sẽ có hướng dẫn sử dụng riêng được ghi rõ trên bao bì. Nếu quý khách có bất kỳ thắc mắc nào, hãy liên hệ với nhân viên của chúng tôi để được tư vấn chi tiết và cụ thể cho từng loại sản phẩm.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <p class="mb-3">Còn câu hỏi khác? Liên hệ với chúng tôi</p>
            <a href="{{ route('contact.index') }}" class="btn btn-primary">Liên hệ ngay</a>
        </div>
    </div>
</section>

<section class="py-5 bg-primary text-white text-center cta-appointment">
    <div class="container">
        <h2 class="h1 mb-4">Chăm sóc tóc tại nhà cùng sản phẩm chính hãng</h2>
        <p class="lead mb-4">Đặt lịch ngay hôm nay và trải nghiệm dịch vụ cắt tóc chuyên nghiệp tại Barber Shop.</p>
        <a href="{{ route('appointment.step1') }}" class="btn btn-light btn-lg appointment-btn">Đặt lịch ngay</a>
    </div>
</section>
@endsection