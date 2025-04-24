<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Đăng nhập thợ cắt tóc - {{ config('app.name', 'Barber Shop') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #9e8a78;
            --secondary-color: #6c757d;
            --dark-color: #343a40;
            --light-color: #f8f9fa;
        }
        
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: url('{{ asset('images/barber-bg.jpg') }}');
            background-size: cover;
            background-position: center;
            position: relative;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: 1;
        }
        
        .login-container {
            max-width: 450px;
            width: 100%;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 2;
        }
        
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-logo img {
            max-width: 150px;
        }
        
        .login-title {
            text-align: center;
            margin-bottom: 30px;
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .form-control {
            height: 50px;
            border-radius: 5px;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            height: 50px;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            background-color: #8a7968;
            border-color: #8a7968;
        }
        
        .login-footer {
            text-align: center;
            margin-top: 20px;
            color: white;
            position: relative;
            z-index: 2;
        }
        
        .login-footer a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .login-footer a:hover {
            text-decoration: underline;
        }
        
        .input-group-text {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
            width: 50px;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-logo">
            <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name', 'Barber Shop') }}">
        </div>
        
        <h2 class="login-title">Đăng nhập thợ cắt tóc</h2>
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        <form method="POST" action="{{ route('barber.login.submit') }}">
            @csrf
            
            <div class="mb-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="Địa chỉ email" required autocomplete="email" autofocus>
                </div>
                @error('email')
                    <span class="text-danger mt-1 d-block">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            
            <div class="mb-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Mật khẩu" required autocomplete="current-password">
                </div>
                @error('password')
                    <span class="text-danger mt-1 d-block">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            
            <div class="mb-4 form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">
                    Ghi nhớ đăng nhập
                </label>
            </div>
            
            <div class="d-grid gap-2 mb-3">
                <button type="submit" class="btn btn-primary">
                    Đăng nhập
                </button>
            </div>
            
            <div class="text-center">
                @if (Route::has('password.request'))
                    <a class="text-decoration-none" href="{{ route('password.request') }}">
                        Quên mật khẩu?
                    </a>
                @endif
            </div>
        </form>
    </div>
    
    <div class="login-footer">
        <p>&copy; {{ date('Y') }} {{ config('app.name', 'Barber Shop') }}. Tất cả quyền được bảo lưu.</p>
        <p><a href="{{ route('home') }}">Quay lại trang chủ</a></p>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
