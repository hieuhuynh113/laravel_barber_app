<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title') - Quản trị Barber Shop</title>
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Admin CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    
    @yield('styles')
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="bg-dark">
            <div class="sidebar-header">
                <h3 class="text-light">Barber Shop Admin</h3>
            </div>

            <ul class="list-unstyled components">
                <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i> Tổng quan
                    </a>
                </li>
                
                <li class="{{ request()->routeIs('admin.appointments.*') ? 'active' : '' }}">
                    <a href="#appointmentSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="fas fa-calendar-alt me-2"></i> Quản lý lịch hẹn
                    </a>
                    <ul class="collapse list-unstyled {{ request()->routeIs('admin.appointments.*') ? 'show' : '' }}" id="appointmentSubmenu">
                        <li>
                            <a href="{{ route('admin.appointments.index') }}">Danh sách lịch hẹn</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.appointments.calendar') }}">Lịch hẹn theo lịch</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.appointments.create') }}">Thêm lịch hẹn</a>
                        </li>
                    </ul>
                </li>
                
                <li class="{{ request()->routeIs('admin.barbers.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.barbers.index') }}">
                        <i class="fas fa-cut me-2"></i> Thợ cắt tóc
                    </a>
                </li>
                
                <li class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.users.index') }}">
                        <i class="fas fa-users me-2"></i> Người dùng
                    </a>
                </li>
                
                <li class="{{ request()->routeIs('admin.services.*') || request()->routeIs('admin.categories.*') && request()->input('type') == 'service' ? 'active' : '' }}">
                    <a href="#serviceSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="fas fa-concierge-bell me-2"></i> Dịch vụ
                    </a>
                    <ul class="collapse list-unstyled {{ request()->routeIs('admin.services.*') || request()->routeIs('admin.categories.*') && request()->input('type') == 'service' ? 'show' : '' }}" id="serviceSubmenu">
                        <li>
                            <a href="{{ route('admin.services.index') }}">Danh sách dịch vụ</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.categories.index', ['type' => 'service']) }}">Danh mục dịch vụ</a>
                        </li>
                    </ul>
                </li>
                
                <li class="{{ request()->routeIs('admin.products.*') || request()->routeIs('admin.categories.*') && request()->input('type') == 'product' ? 'active' : '' }}">
                    <a href="#productSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="fas fa-shopping-bag me-2"></i> Sản phẩm
                    </a>
                    <ul class="collapse list-unstyled {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.categories.*') && request()->input('type') == 'product' ? 'show' : '' }}" id="productSubmenu">
                        <li>
                            <a href="{{ route('admin.products.index') }}">Danh sách sản phẩm</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.categories.index', ['type' => 'product']) }}">Danh mục sản phẩm</a>
                        </li>
                    </ul>
                </li>
                
                <li class="{{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}">
                    <a href="#invoiceSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="fas fa-file-invoice-dollar me-2"></i> Hóa đơn
                    </a>
                    <ul class="collapse list-unstyled {{ request()->routeIs('admin.invoices.*') ? 'show' : '' }}" id="invoiceSubmenu">
                        <li>
                            <a href="{{ route('admin.invoices.index') }}">Danh sách hóa đơn</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.invoices.create') }}">Tạo hóa đơn</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.invoices.statistics') }}">Thống kê doanh thu</a>
                        </li>
                    </ul>
                </li>
                
                <li class="{{ request()->routeIs('admin.news.*') || request()->routeIs('admin.categories.*') && request()->input('type') == 'news' ? 'active' : '' }}">
                    <a href="#newsSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="fas fa-newspaper me-2"></i> Tin tức
                    </a>
                    <ul class="collapse list-unstyled {{ request()->routeIs('admin.news.*') || request()->routeIs('admin.categories.*') && request()->input('type') == 'news' ? 'show' : '' }}" id="newsSubmenu">
                        <li>
                            <a href="{{ route('admin.news.index') }}">Danh sách tin tức</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.news.create') }}">Thêm tin tức</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.categories.index', ['type' => 'news']) }}">Danh mục tin tức</a>
                        </li>
                    </ul>
                </li>
                
                <li class="{{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.contacts.index') }}">
                        <i class="fas fa-envelope me-2"></i> Liên hệ
                    </a>
                </li>
                
                <li class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.settings.index') }}">
                        <i class="fas fa-cog me-2"></i> Cài đặt
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-info">
                        <i class="fas fa-align-left"></i>
                        <span>Thu gọn menu</span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="nav navbar-nav ms-auto">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user me-2"></i>{{ Auth::user()->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="{{ route('home') }}">Về trang chủ</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            Đăng xuất
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    
    <!-- Admin Script -->
    <script>
        $(document).ready(function () {
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
                
                // Thay đổi text của nút
                if ($('#sidebar').hasClass('active')) {
                    $(this).find('span').text('Mở rộng menu');
                } else {
                    $(this).find('span').text('Thu gọn menu');
                }
            });
        });
    </script>
    
    @yield('scripts')
</body>
</html> 