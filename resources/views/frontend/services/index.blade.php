@extends('layouts.frontend')

@section('title', 'Dịch vụ')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <h1 class="text-center mb-5">Dịch vụ của chúng tôi</h1>
        
        <div class="row">
            @foreach($services as $service)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 service-card">
                    <img src="{{ asset('storage/' . $service->image) }}" class="card-img-top" alt="{{ $service->name }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $service->name }}</h5>
                        <p class="card-text">{{ Str::limit($service->description, 150) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="price text-primary fw-bold">{{ number_format($service->price) }} VNĐ</span>
                            <a href="{{ route('services.show', $service->slug) }}" class="btn btn-outline-primary">Chi tiết</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="mt-4">
            {{ $services->links() }}
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h2>Tại sao chọn dịch vụ của chúng tôi?</h2>
                <p>Barber Shop tự hào cung cấp các dịch vụ cắt tóc và chăm sóc tóc chất lượng cao. Chúng tôi cam kết mang đến trải nghiệm tuyệt vời nhất cho quý khách hàng.</p>
                
                <div class="mt-4">
                    <div class="d-flex mb-3">
                        <div class="feature-icon me-3">
                            <i class="fas fa-user-tie text-primary" style="font-size: 24px;"></i>
                        </div>
                        <div>
                            <h5>Thợ cắt tóc chuyên nghiệp</h5>
                            <p class="text-muted">Đội ngũ thợ cắt tóc của chúng tôi đều được đào tạo bài bản và có nhiều năm kinh nghiệm.</p>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-3">
                        <div class="feature-icon me-3">
                            <i class="fas fa-cut text-primary" style="font-size: 24px;"></i>
                        </div>
                        <div>
                            <h5>Dụng cụ hiện đại</h5>
                            <p class="text-muted">Sử dụng các dụng cụ và sản phẩm chất lượng cao để đảm bảo kết quả tốt nhất.</p>
                        </div>
                    </div>
                    
                    <div class="d-flex">
                        <div class="feature-icon me-3">
                            <i class="fas fa-gem text-primary" style="font-size: 24px;"></i>
                        </div>
                        <div>
                            <h5>Không gian sang trọng</h5>
                            <p class="text-muted">Môi trường thoải mái, sang trọng giúp quý khách có trải nghiệm thư giãn nhất.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="position-relative">
                    <img src="{{ asset('images/services-main.jpg') }}" alt="Dịch vụ cắt tóc" class="img-fluid rounded shadow">
                    <div class="position-absolute top-0 start-0 bg-primary text-white p-3 rounded-end" style="transform: translateY(30px);">
                        <h4 class="mb-0">Đặt lịch ngay</h4>
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
                <p class="mb-5">Một số câu hỏi khách hàng thường hỏi về dịch vụ của chúng tôi</p>
            </div>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Tôi có cần đặt lịch trước không?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Chúng tôi khuyến khích quý khách đặt lịch trước để đảm bảo được phục vụ đúng giờ và không phải chờ đợi. Tuy nhiên, chúng tôi vẫn phục vụ khách hàng đến trực tiếp nếu có chỗ trống.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Các phương thức thanh toán được chấp nhận?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Chúng tôi chấp nhận thanh toán bằng tiền mặt, thẻ tín dụng/ghi nợ và các ví điện tử phổ biến như MoMo, ZaloPay, VNPay.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                Tôi có thể hủy hoặc đổi lịch hẹn không?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Quý khách có thể hủy hoặc đổi lịch hẹn trước ít nhất 2 giờ. Việc hủy lịch sau thời gian này có thể phát sinh phí.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                Có cần tư vấn trước khi sử dụng dịch vụ không?
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Chúng tôi khuyên quý khách nên tham khảo ý kiến của thợ cắt tóc trước khi quyết định kiểu tóc. Các thợ cắt tóc của chúng tôi sẽ tư vấn kiểu tóc phù hợp nhất với khuôn mặt và phong cách của quý khách.
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
                <h2 class="h1 mb-3">Sẵn sàng trải nghiệm dịch vụ?</h2>
                <p class="lead mb-0">Đặt lịch ngay hôm nay và trải nghiệm dịch vụ cắt tóc chuyên nghiệp tại Barber Shop.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="{{ route('appointment.step1') }}" class="btn btn-light btn-lg">Đặt lịch ngay</a>
            </div>
        </div>
    </div>
</section>
@endsection 