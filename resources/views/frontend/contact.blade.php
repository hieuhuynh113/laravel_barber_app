@extends('layouts.frontend')

@section('title', 'Liên hệ')

@section('content')
@include('partials.page-header', [
    'title' => 'Liên hệ',
    'description' => 'Luôn lắng nghe và sẵn sàng hỗ trợ quý khách mọi lúc mọi nơi',
    'backgroundImage' => 'images/about-1.jpg'
])

<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-6 mb-4 mb-md-0">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <h3 class="mb-4">Gửi tin nhắn cho chúng tôi</h3>

                        @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        <form action="{{ route('contact.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Họ tên</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Số điện thoại</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="subject" class="form-label">Tiêu đề</label>
                                <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject') }}" required>
                                @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="message" class="form-label">Nội dung</label>
                                <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
                                @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Gửi tin nhắn</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <h3 class="mb-4">Thông tin liên hệ</h3>

                        <div class="mb-4">
                            <h5><i class="fas fa-map-marker-alt text-primary me-2"></i> Địa chỉ</h5>
                            <p class="ms-4">123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh</p>
                        </div>

                        <div class="mb-4">
                            <h5><i class="fas fa-phone-alt text-primary me-2"></i> Điện thoại</h5>
                            <p class="ms-4">0123456789</p>
                        </div>

                        <div class="mb-4">
                            <h5><i class="fas fa-envelope text-primary me-2"></i> Email</h5>
                            <p class="ms-4">hieu0559764554@gmail.com</p>
                        </div>

                        <div class="mb-4">
                            <h5><i class="fas fa-clock text-primary me-2"></i> Giờ làm việc</h5>
                            <p class="ms-4">Thứ 2 - Chủ nhật: 8:00 - 20:00</p>
                        </div>

                        <div class="mb-4">
                            <h5><i class="fas fa-share-alt text-primary me-2"></i> Mạng xã hội</h5>
                            <div class="ms-4 social-icons">
                                <a href="#" class="text-decoration-none me-2"><i class="fab fa-facebook-f fa-lg"></i></a>
                                <a href="#" class="text-decoration-none me-2"><i class="fab fa-instagram fa-lg"></i></a>
                                <a href="#" class="text-decoration-none me-2"><i class="fab fa-twitter fa-lg"></i></a>
                                <a href="#" class="text-decoration-none"><i class="fab fa-youtube fa-lg"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <h3 class="text-center mb-5">Bản đồ cửa hàng</h3>
        <div class="ratio ratio-16x9">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.5177580926147!2d106.69892827465639!3d10.771608989387898!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f40a3b49e59%3A0xa1bd15b1ead7b0df!2sCentral%20Post%20Office!5e0!3m2!1sen!2s!4v1709878254594!5m2!1sen!2s"
                    width="600" height="450" style="border:0;" allowfullscreen=""
                    loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</section>

<section class="py-5 bg-primary text-white text-center cta-appointment">
    <div class="container">
        <h2 class="h1 mb-4">Bạn cần đặt lịch?</h2>
        <p class="lead mb-4">Hãy đặt lịch ngay hôm nay để trải nghiệm dịch vụ tuyệt vời tại Barber Shop.</p>
        <a href="{{ route('appointment.step1') }}" class="btn btn-light btn-lg appointment-btn">Đặt lịch ngay</a>
    </div>
</section>
@endsection