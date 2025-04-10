@extends('layouts.frontend')

@section('title', 'Sản phẩm')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <h1 class="text-center mb-5">Sản phẩm của chúng tôi</h1>
        
        @if($categories->count() > 0)
        <div class="mb-4">
            <div class="d-flex justify-content-center flex-wrap">
                <a href="{{ route('products.index') }}" class="btn {{ !$categoryId ? 'btn-primary' : 'btn-outline-primary' }} me-2 mb-2">
                    Tất cả
                </a>
                @foreach($categories as $category)
                <a href="{{ route('products.index', ['category_id' => $category->id]) }}" class="btn {{ $categoryId == $category->id ? 'btn-primary' : 'btn-outline-primary' }} me-2 mb-2">
                    {{ $category->name }}
                </a>
                @endforeach
            </div>
        </div>
        @endif
        
        <div class="row">
            @forelse($products as $product)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 product-card">
                    <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text">{{ Str::limit($product->description, 150) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="price text-primary fw-bold">{{ number_format($product->price) }} VNĐ</span>
                            <a href="{{ route('products.show', $product->slug) }}" class="btn btn-outline-primary">Chi tiết</a>
                        </div>
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
        
        <div class="mt-4">
            {{ $products->links() }}
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
                    <div class="position-absolute top-0 start-0 bg-primary text-white p-3 rounded-end" style="transform: translateY(30px);">
                        <h4 class="mb-0">Chất lượng hàng đầu</h4>
                    </div>
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

<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mb-4 mb-lg-0">
                <h2 class="h1 mb-3">Chăm sóc tóc tại nhà cùng sản phẩm chính hãng</h2>
                <p class="lead mb-0">Đặt lịch ngay hôm nay và trải nghiệm dịch vụ cắt tóc chuyên nghiệp tại Barber Shop.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="{{ route('appointment.step1') }}" class="btn btn-light btn-lg">Đặt lịch ngay</a>
            </div>
        </div>
    </div>
</section>
@endsection 