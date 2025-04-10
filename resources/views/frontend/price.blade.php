@extends('layouts.frontend')

@section('title', 'Bảng giá dịch vụ')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <h1 class="text-center mb-5">Bảng giá dịch vụ</h1>
        
        <div class="row justify-content-center">
            <div class="col-lg-10">
                @forelse($categories as $category)
                    <div class="card shadow mb-5">
                        <div class="card-header bg-primary py-3">
                            <h3 class="text-white mb-0">{{ $category->name }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Dịch vụ</th>
                                            <th>Mô tả</th>
                                            <th>Thời gian</th>
                                            <th class="text-end">Giá</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($category->services as $service)
                                            <tr>
                                                <td class="fw-bold">{{ $service->name }}</td>
                                                <td>{{ Str::limit($service->description, 100) }}</td>
                                                <td>{{ $service->duration }} phút</td>
                                                <td class="text-end fw-bold text-primary">{{ number_format($service->price) }} VNĐ</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-info text-center">
                        Hiện tại chưa có dịch vụ nào. Vui lòng quay lại sau.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="mb-4">Vì sao chọn Barber Shop?</h2>
                <p class="mb-5">Chúng tôi tự hào cung cấp dịch vụ cắt tóc chất lượng cao với giá cả hợp lý</p>
                
                <div class="row mt-5">
                    <div class="col-md-4 mb-4">
                        <div class="feature-item text-center">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-cut text-primary" style="font-size: 48px;"></i>
                            </div>
                            <h4>Thợ cắt tóc chuyên nghiệp</h4>
                            <p class="text-muted">Đội ngũ thợ cắt tóc có nhiều năm kinh nghiệm và được đào tạo bài bản.</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="feature-item text-center">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-gem text-primary" style="font-size: 48px;"></i>
                            </div>
                            <h4>Sản phẩm cao cấp</h4>
                            <p class="text-muted">Sử dụng các sản phẩm chăm sóc tóc chất lượng cao và nhập khẩu chính hãng.</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="feature-item text-center">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-smile text-primary" style="font-size: 48px;"></i>
                            </div>
                            <h4>Dịch vụ tận tâm</h4>
                            <p class="text-muted">Cam kết mang đến trải nghiệm thoải mái và hài lòng cho mọi khách hàng.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h2>Ưu đãi đặc biệt</h2>
                <p class="lead mb-4">Đăng ký thành viên để nhận nhiều ưu đãi hấp dẫn</p>
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <i class="fas fa-check-circle text-primary me-2"></i> Giảm 10% cho lần đầu sử dụng dịch vụ
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-check-circle text-primary me-2"></i> Tích điểm đổi quà với mỗi dịch vụ sử dụng
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-check-circle text-primary me-2"></i> Các chương trình khuyến mãi hàng tháng
                    </li>
                    <li>
                        <i class="fas fa-check-circle text-primary me-2"></i> Ưu đãi đặc biệt vào dịp sinh nhật
                    </li>
                </ul>
                <a href="{{ route('appointment.step1') }}" class="btn btn-primary mt-4">Đặt lịch ngay</a>
            </div>
            <div class="col-lg-6">
                <div class="ratio ratio-16x9 rounded overflow-hidden shadow">
                    <img src="{{ asset('images/barber-special.jpg') }}" alt="Ưu đãi đặc biệt" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mb-4 mb-lg-0">
                <h2 class="h1 mb-3">Sẵn sàng trải nghiệm dịch vụ của chúng tôi?</h2>
                <p class="lead mb-0">Đặt lịch ngay hôm nay và trải nghiệm dịch vụ cắt tóc chuyên nghiệp tại Barber Shop.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="{{ route('appointment.step1') }}" class="btn btn-light btn-lg">Đặt lịch ngay</a>
            </div>
        </div>
    </div>
</section>
@endsection 