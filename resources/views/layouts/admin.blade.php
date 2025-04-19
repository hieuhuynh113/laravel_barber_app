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

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    <!-- Admin Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin-custom.css') }}">

    <!-- User Table Fix CSS -->
    <link rel="stylesheet" href="{{ asset('css/user-table-fix.css') }}">

    <style>
        /* Notification Styles */
        .notification-item {
            padding: 10px;
            border-bottom: 1px solid #e3e6f0;
            transition: all 0.3s;
        }
        .notification-item:hover {
            background-color: #f8f9fc;
        }
        .notification-dropdown .dropdown-item:active {
            background-color: transparent;
        }
        .nav-item .badge.bg-danger {
            position: absolute;
            top: 0;
            right: 0;
            font-size: 0.6rem;
        }
    </style>

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
                <!-- Nhóm 1: Tổng quan và Thông báo -->
                <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i> Tổng quan
                    </a>
                </li>

                <li class="{{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.notifications.index') }}">
                        <i class="fas fa-bell me-2"></i> Thông báo
                        @if(Auth::user()->unreadNotifications->count() > 0)
                            <span class="badge bg-danger rounded-pill ms-2">{{ Auth::user()->unreadNotifications->count() }}</span>
                        @endif
                    </a>
                </li>

                <!-- Nhóm 2: Quản lý kinh doanh chính -->
                <li class="{{ request()->routeIs('admin.appointments.*') ? 'active' : '' }}">
                    <a href="#appointmentSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="fas fa-calendar-alt me-2"></i> Quản lý lịch hẹn
                    </a>
                    <ul class="collapse list-unstyled {{ request()->routeIs('admin.appointments.*') ? 'show' : '' }}" id="appointmentSubmenu">
                        <li>
                            <a href="{{ route('admin.appointments.index') }}">Danh sách lịch hẹn</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.appointments.create') }}">Thêm lịch hẹn</a>
                        </li>
                    </ul>
                </li>

                <li class="{{ request()->routeIs('admin.invoices.*') || request()->routeIs('admin.payment-receipts.*') ? 'active' : '' }}">
                    <a href="#invoiceSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="fas fa-file-invoice-dollar me-2"></i> Thanh toán & Hóa đơn
                    </a>
                    <ul class="collapse list-unstyled {{ request()->routeIs('admin.invoices.*') || request()->routeIs('admin.payment-receipts.*') ? 'show' : '' }}" id="invoiceSubmenu">
                        <li>
                            <a href="{{ route('admin.invoices.index') }}">Danh sách hóa đơn</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.invoices.create') }}">Tạo hóa đơn</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.payment-receipts.index') }}">Biên lai chuyển khoản
                                @php
                                    $pendingReceipts = \App\Models\PaymentReceipt::where('status', 'pending')->count();
                                @endphp
                                @if($pendingReceipts > 0)
                                    <span class="badge bg-danger rounded-pill ms-2">{{ $pendingReceipts }}</span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.invoices.statistics') }}">Thống kê doanh thu</a>
                        </li>
                    </ul>
                </li>

                <li class="{{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                    <a href="#reviewSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="fas fa-star me-2"></i> Đánh giá
                    </a>
                    <ul class="collapse list-unstyled {{ request()->routeIs('admin.reviews.*') ? 'show' : '' }}" id="reviewSubmenu">
                        <li>
                            <a href="{{ route('admin.reviews.index') }}">Danh sách đánh giá</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.reviews.statistics') }}">Thống kê đánh giá</a>
                        </li>
                    </ul>
                </li>

                <!-- Nhóm 3: Quản lý nhân sự -->
                <li class="{{ request()->routeIs('admin.barbers.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.barbers.index') }}">
                        <i class="fas fa-cut me-2"></i> Thợ cắt tóc
                    </a>
                </li>

                <li class="{{ request()->routeIs('admin.schedules.*') || request()->routeIs('admin.time-slots.*') ? 'active' : '' }}">
                    <a href="#scheduleSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="fas fa-calendar-alt me-2"></i> Quản lý lịch
                    </a>
                    <ul class="collapse list-unstyled {{ request()->routeIs('admin.schedules.*') || request()->routeIs('admin.time-slots.*') ? 'show' : '' }}" id="scheduleSubmenu">
                        <li>
                            <a href="{{ route('admin.schedules.index') }}">Lịch làm việc</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.time-slots.index') }}">Khung giờ đặt lịch</a>
                        </li>
                    </ul>
                </li>

                <li class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.users.index') }}">
                        <i class="fas fa-users me-2"></i> Người dùng
                    </a>
                </li>

                <!-- Nhóm 4: Quản lý nội dung -->
                <li class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.categories.index') }}">
                        <i class="fas fa-tags me-2"></i> Quản lý danh mục
                    </a>
                </li>

                <li class="{{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                    <a href="#serviceSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="fas fa-concierge-bell me-2"></i> Dịch vụ
                    </a>
                    <ul class="collapse list-unstyled {{ request()->routeIs('admin.services.*') ? 'show' : '' }}" id="serviceSubmenu">
                        <li>
                            <a href="{{ route('admin.services.index') }}">Danh sách dịch vụ</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.services.create') }}">Thêm dịch vụ</a>
                        </li>
                    </ul>
                </li>

                <li class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <a href="#productSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="fas fa-shopping-bag me-2"></i> Sản phẩm
                    </a>
                    <ul class="collapse list-unstyled {{ request()->routeIs('admin.products.*') ? 'show' : '' }}" id="productSubmenu">
                        <li>
                            <a href="{{ route('admin.products.index') }}">Danh sách sản phẩm</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.products.create') }}">Thêm sản phẩm</a>
                        </li>
                    </ul>
                </li>

                <li class="{{ request()->routeIs('admin.news.*') ? 'active' : '' }}">
                    <a href="#newsSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="fas fa-newspaper me-2"></i> Tin tức
                    </a>
                    <ul class="collapse list-unstyled {{ request()->routeIs('admin.news.*') ? 'show' : '' }}" id="newsSubmenu">
                        <li>
                            <a href="{{ route('admin.news.index') }}">Danh sách tin tức</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.news.create') }}">Thêm tin tức</a>
                        </li>
                    </ul>
                </li>

                <!-- Nhóm 5: Tương tác khách hàng -->
                <li class="{{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.contacts.index') }}">
                        <i class="fas fa-envelope me-2"></i> Liên hệ
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
                            <!-- Notifications Dropdown -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-bell me-2"></i>
                                    @if(Auth::user()->unreadNotifications->count() > 0)
                                        <span class="badge rounded-pill bg-danger">{{ Auth::user()->unreadNotifications->count() }}</span>
                                    @endif
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationsDropdown" style="width: 300px; max-height: 400px; overflow-y: auto;">
                                    <li>
                                        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                                            <h6 class="mb-0">Thông báo</h6>
                                            <a href="{{ route('admin.notifications.index') }}" class="text-decoration-none small">Xem tất cả</a>
                                        </div>
                                    </li>
                                    @forelse(Auth::user()->unreadNotifications->take(5) as $notification)
                                        <li>
                                            <div class="dropdown-item notification-item" style="white-space: normal;">
                                                @if($notification->type == 'App\\Notifications\\NewReviewNotification')
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="small text-muted">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</span>
                                                        @if($notification->data['is_low_rating'])
                                                            <span class="badge bg-danger">Cần chú ý</span>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <strong>{{ $notification->data['user_name'] }}</strong> đã đánh giá
                                                        <strong>{{ $notification->data['rating'] }}</strong> sao cho dịch vụ
                                                        <strong>{{ $notification->data['service_name'] }}</strong>
                                                    </div>
                                                    <div class="mt-1">
                                                        <a href="{{ route('admin.reviews.show', $notification->data['review_id']) }}" class="btn btn-sm btn-primary">
                                                            Xem chi tiết
                                                        </a>
                                                        <form action="{{ route('admin.notifications.markAsRead', $notification->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-secondary">
                                                                Đánh dấu đã đọc
                                                            </button>
                                                        </form>
                                                    </div>
                                                @else
                                                    <div class="small text-muted">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</div>
                                                    <div>{{ json_encode($notification->data) }}</div>
                                                @endif
                                            </div>
                                        </li>
                                    @empty
                                        <li><div class="dropdown-item text-center">Không có thông báo mới</div></li>
                                    @endforelse
                                    @if(Auth::user()->unreadNotifications->count() > 0)
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <div class="text-center py-2">
                                                <form action="{{ route('admin.notifications.markAllAsRead') }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-primary">
                                                        Đánh dấu tất cả đã đọc
                                                    </button>
                                                </form>
                                            </div>
                                        </li>
                                    @endif
                                </ul>
                            </li>

                            <!-- User Dropdown -->
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
                $('#content').toggleClass('sidebar-active');

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