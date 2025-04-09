@extends('layouts.frontend')

@section('title', 'Truy cập bị từ chối')

@section('content')
<section class="error-page bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <div class="error-content p-5 bg-white shadow rounded">
                    <h1 class="display-1 text-warning">403</h1>
                    <h2 class="mb-4">Truy cập bị từ chối</h2>
                    <p class="lead mb-4">Rất tiếc, bạn không có quyền truy cập vào trang này.</p>
                    <p class="mb-4">Vui lòng đăng nhập với tài khoản có quyền phù hợp hoặc quay lại trang chủ.</p>
                    <div class="buttons">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại trang trước
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i>Trang chủ
                        </a>
                        @guest
                        <a href="{{ route('login') }}" class="btn btn-info">
                            <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                        </a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection 