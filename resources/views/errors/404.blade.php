@extends('layouts.frontend')

@section('title', 'Không tìm thấy trang')

@section('content')
<section class="error-page bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <div class="error-content p-5 bg-white shadow rounded">
                    <h1 class="display-1 text-danger">404</h1>
                    <h2 class="mb-4">Không tìm thấy trang</h2>
                    <p class="lead mb-4">Xin lỗi, trang bạn đang tìm kiếm không tồn tại hoặc không có sẵn.</p>
                    <p class="mb-4">Hãy kiểm tra lại đường dẫn hoặc quay lại trang chủ để tiếp tục.</p>
                    <div class="buttons">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại trang trước
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i>Trang chủ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection 