@extends('layouts.admin')

@section('title', 'Chi tiết thợ cắt tóc')

@section('styles')
<style>
    .star-rating {
        color: #ffc107;
    }
    .rating-progress {
        height: 10px;
        margin-bottom: 10px;
    }
    .rating-count {
        min-width: 30px;
        text-align: right;
    }
    .rating-percentage {
        min-width: 50px;
        text-align: right;
    }
    .review-item {
        border-bottom: 1px solid #e3e6f0;
        padding-bottom: 15px;
        margin-bottom: 15px;
    }
    .review-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
        margin-bottom: 0;
    }
    .review-images {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
    }
    .review-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 5px;
        cursor: pointer;
    }
    .nav-tabs .nav-link {
        font-weight: 500;
    }
    .nav-tabs .nav-link.active {
        border-bottom: 3px solid #4e73df;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết thợ cắt tóc</h1>
        <div>
            <a href="{{ route('admin.barbers.edit', $barber->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Chỉnh sửa
            </a>
            <a href="{{ route('admin.barbers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin cá nhân</h6>
                </div>
                <div class="card-body text-center">
                    @if($barber->avatar)
                        <img src="{{ asset('storage/' . $barber->avatar) }}" alt="{{ $barber->name }}" class="img-profile rounded-circle mb-3" width="150" height="150">
                    @else
                        <img src="{{ asset('images/default-avatar.jpg') }}" alt="{{ $barber->name }}" class="img-profile rounded-circle mb-3" width="150" height="150">
                    @endif

                    <h4 class="mb-0">{{ $barber->name }}</h4>
                    <p class="text-muted mb-3">Thợ cắt tóc</p>

                    <div class="mb-2">
                        <span class="badge {{ $barber->status ? 'bg-success' : 'bg-danger' }}">
                            {{ $barber->status ? 'Hoạt động' : 'Không hoạt động' }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <i class="fas fa-envelope me-2"></i> {{ $barber->email }}<br>
                        <i class="fas fa-phone me-2"></i> {{ $barber->phone ?? 'Chưa cập nhật' }}<br>
                        <i class="fas fa-map-marker-alt me-2"></i> {{ $barber->address ?? 'Chưa cập nhật' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin chuyên môn</h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="mb-2">Kinh nghiệm</h5>
                        <p>{{ $barber->barber->experience ?? 0 }} năm</p>
                    </div>

                    <div class="mb-4">
                        <h5 class="mb-2">Chuyên môn</h5>
                        <p>{{ $barber->barber->specialties ?? 'Chưa cập nhật' }}</p>
                    </div>

                    <div class="mb-4">
                        <h5 class="mb-2">Tiểu sử</h5>
                        <p>{{ $barber->barber->bio ?? 'Chưa cập nhật' }}</p>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Lịch hẹn gần đây</h6>
                </div>
                <div class="card-body">
                    @if($barber->appointments && $barber->appointments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Khách hàng</th>
                                        <th>Ngày</th>
                                        <th>Giờ</th>
                                        <th>Dịch vụ</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($barber->appointments->take(5) as $appointment)
                                        <tr>
                                            <td>{{ $appointment->id }}</td>
                                            <td>{{ $appointment->user->name }}</td>
                                            <td>{{ date('d/m/Y', strtotime($appointment->appointment_date)) }}</td>
                                            <td>{{ $appointment->appointment_time }}</td>
                                            <td>
                                                @foreach($appointment->services as $service)
                                                    <span class="badge bg-secondary">{{ $service->name }}</span>
                                                @endforeach
                                            </td>
                                            <td>
                                                @if($appointment->status == 'pending')
                                                    <span class="badge bg-warning">Chờ xác nhận</span>
                                                @elseif($appointment->status == 'confirmed')
                                                    <span class="badge bg-primary">Đã xác nhận</span>
                                                @elseif($appointment->status == 'completed')
                                                    <span class="badge bg-success">Hoàn thành</span>
                                                @elseif($appointment->status == 'canceled')
                                                    <span class="badge bg-danger">Đã hủy</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end">
                            <a href="{{ route('admin.appointments.index', ['barber_id' => $barber->barber->id]) }}" class="btn btn-primary btn-sm">
                                Xem tất cả lịch hẹn
                            </a>
                        </div>
                    @else
                        <p>Không có lịch hẹn nào.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs for barber details and reviews -->
    <ul class="nav nav-tabs mt-4" id="barberTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="appointments-tab" data-bs-toggle="tab" data-bs-target="#appointments" type="button" role="tab" aria-controls="appointments" aria-selected="true">
                <i class="fas fa-calendar-check"></i> Lịch hẹn
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews" aria-selected="false">
                <i class="fas fa-star"></i> Đánh giá ({{ $reviewsCount }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="services-tab" data-bs-toggle="tab" data-bs-target="#services" type="button" role="tab" aria-controls="services" aria-selected="false">
                <i class="fas fa-concierge-bell"></i> Dịch vụ nổi bật
            </button>
        </li>
    </ul>

    <div class="tab-content" id="barberTabsContent">
        <!-- Appointments Tab -->
        <div class="tab-pane fade show active" id="appointments" role="tabpanel" aria-labelledby="appointments-tab">
            <div class="card shadow mb-4 mt-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Lịch hẹn gần đây</h6>
                    <a href="{{ route('admin.appointments.index', ['barber_id' => $barber->barber->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-list"></i> Xem tất cả lịch hẹn
                    </a>
                </div>
                <div class="card-body">
                    @if($barber->appointments && $barber->appointments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Khách hàng</th>
                                        <th>Ngày</th>
                                        <th>Giờ</th>
                                        <th>Dịch vụ</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($barber->appointments->take(5) as $appointment)
                                        <tr>
                                            <td>{{ $appointment->id }}</td>
                                            <td>{{ $appointment->user->name }}</td>
                                            <td>{{ date('d/m/Y', strtotime($appointment->appointment_date)) }}</td>
                                            <td>{{ $appointment->appointment_time }}</td>
                                            <td>
                                                @foreach($appointment->services as $service)
                                                    <span class="badge bg-secondary">{{ $service->name }}</span>
                                                @endforeach
                                            </td>
                                            <td>
                                                @if($appointment->status == 'pending')
                                                    <span class="badge bg-warning">Chờ xác nhận</span>
                                                @elseif($appointment->status == 'confirmed')
                                                    <span class="badge bg-primary">Đã xác nhận</span>
                                                @elseif($appointment->status == 'completed')
                                                    <span class="badge bg-success">Hoàn thành</span>
                                                @elseif($appointment->status == 'canceled')
                                                    <span class="badge bg-danger">Đã hủy</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.appointments.show', $appointment->id) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p>Không có lịch hẹn nào.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Reviews Tab -->
        <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
            <div class="card shadow mb-4 mt-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Đánh giá của khách hàng</h6>
                    <a href="{{ route('admin.reviews.index', ['barber_id' => $barber->barber->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-list"></i> Xem tất cả đánh giá
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Thống kê đánh giá -->
                        <div class="col-lg-4">
                            <div class="text-center mb-4">
                                <h1 class="display-4 font-weight-bold">{{ number_format($averageRating, 1) }}</h1>
                                <div class="star-rating mb-2" style="font-size: 1.5rem;">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= round($averageRating))
                                            <i class="fas fa-star"></i>
                                        @elseif($i - 0.5 <= $averageRating)
                                            <i class="fas fa-star-half-alt"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                                <p class="text-muted">{{ $reviewsCount }} đánh giá</p>
                            </div>

                            <!-- Phân bố đánh giá theo số sao -->
                            <div class="rating-distribution">
                                @foreach($ratingDistribution as $rating => $data)
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="rating-count me-2">{{ $rating }}</div>
                                        <div class="star-rating me-2">
                                            <i class="fas fa-star"></i>
                                        </div>
                                        <div class="flex-grow-1 mx-2">
                                            <div class="progress rating-progress">
                                                <div class="progress-bar bg-{{ $rating >= 4 ? 'success' : ($rating >= 3 ? 'info' : ($rating >= 2 ? 'warning' : 'danger')) }}"
                                                    role="progressbar" style="width: {{ $data['percentage'] }}%"
                                                    aria-valuenow="{{ $data['percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                        <div class="rating-count me-2">{{ $data['count'] }}</div>
                                        <div class="rating-percentage">({{ $data['percentage'] }}%)</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Danh sách đánh giá -->
                        <div class="col-lg-8">
                            @if($reviews->count() > 0)
                                @foreach($reviews as $review)
                                    <div class="review-item">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="d-flex align-items-center">
                                                @if($review->user->avatar)
                                                    <img src="{{ asset('storage/' . $review->user->avatar) }}" alt="{{ $review->user->name }}" class="rounded-circle me-2" width="40" height="40">
                                                @else
                                                    <img src="{{ asset('images/default-avatar.jpg') }}" alt="{{ $review->user->name }}" class="rounded-circle me-2" width="40" height="40">
                                                @endif
                                                <div>
                                                    <h6 class="mb-0">{{ $review->user->name }}</h6>
                                                    <small class="text-muted">{{ $review->created_at->format('d/m/Y H:i') }}</small>
                                                </div>
                                            </div>
                                            <div class="star-rating">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star"></i>
                                                @endfor
                                            </div>
                                        </div>

                                        <div class="mb-2">
                                            <strong>Dịch vụ:</strong> {{ $review->service->name }}
                                        </div>

                                        <p class="mb-2">{{ $review->comment }}</p>

                                        @if($review->images)
                                            <div class="review-images">
                                                @foreach(json_decode($review->images) as $image)
                                                    <a href="{{ asset($image) }}" target="_blank">
                                                        <img src="{{ asset($image) }}" alt="Review image" class="review-image">
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif

                                        @if($review->admin_response)
                                            <div class="alert alert-info mt-2 mb-0">
                                                <strong>Phản hồi của admin:</strong> {{ $review->admin_response }}
                                            </div>
                                        @endif

                                        <div class="mt-2">
                                            <a href="{{ route('admin.reviews.edit', $review->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> Chỉnh sửa
                                            </a>
                                            <a href="{{ route('admin.reviews.show', $review->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> Xem chi tiết
                                            </a>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="mt-3">
                                    {{ $reviews->appends(['reviews_page' => $reviews->currentPage()])->links() }}
                                </div>
                            @else
                                <div class="alert alert-info">
                                    Chưa có đánh giá nào cho thợ cắt tóc này.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services Tab -->
        <div class="tab-pane fade" id="services" role="tabpanel" aria-labelledby="services-tab">
            <div class="card shadow mb-4 mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Dịch vụ được đánh giá cao nhất</h6>
                </div>
                <div class="card-body">
                    @if($topServices->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Dịch vụ</th>
                                        <th>Số đánh giá</th>
                                        <th>Điểm trung bình</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topServices as $service)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.services.edit', $service->id) }}">
                                                    {{ $service->name }}
                                                </a>
                                            </td>
                                            <td>{{ $service->reviews_count }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="me-2">{{ number_format($service->reviews_avg_rating, 1) }}</span>
                                                    <div class="star-rating">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            @if($i <= round($service->reviews_avg_rating))
                                                                <i class="fas fa-star"></i>
                                                            @elseif($i - 0.5 <= $service->reviews_avg_rating)
                                                                <i class="fas fa-star-half-alt"></i>
                                                            @else
                                                                <i class="far fa-star"></i>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            Chưa có dịch vụ nào được đánh giá.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('admin.barbers.edit', $barber->id) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Chỉnh sửa
        </a>
        <a href="{{ route('admin.schedules.index', ['barber_id' => $barber->id]) }}" class="btn btn-info">
            <i class="fas fa-calendar-alt"></i> Quản lý lịch làm việc
        </a>
        <form action="{{ route('admin.barbers.destroy', $barber->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa thợ cắt tóc này?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Xóa
            </button>
        </form>
        <a href="{{ route('admin.barbers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Xử lý tabs
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab');

        if (activeTab === 'reviews') {
            $('#reviews-tab').tab('show');
        } else if (activeTab === 'services') {
            $('#services-tab').tab('show');
        }

        // Lưu tab đang active vào URL khi chuyển tab
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            const target = $(e.target).attr('id');
            if (target === 'reviews-tab') {
                history.replaceState(null, null, '?tab=reviews');
            } else if (target === 'services-tab') {
                history.replaceState(null, null, '?tab=services');
            } else {
                history.replaceState(null, null, window.location.pathname);
            }
        });
    });
</script>
@endsection