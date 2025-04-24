@extends('layouts.frontend')

@section('title', 'Lịch hẹn của tôi')

@section('styles')
<style>
    /* Cải thiện giao diện tab */
    #appointmentTabs .nav-link {
        color: #666;
        border-radius: 0;
        padding: 10px 15px;
        font-weight: 500;
        border: 1px solid #dee2e6;
        border-bottom: none;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }

    #appointmentTabs .nav-link.active {
        color: #9E8A78;
        background-color: #fff;
        border-top: 3px solid #9E8A78;
        font-weight: 600;
    }

    #appointmentTabs .nav-link:hover:not(.active) {
        background-color: #f1f1f1;
        border-color: #dee2e6;
    }

    /* Cải thiện giao diện bảng */
    .table {
        box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }

    .table thead th {
        font-weight: 600;
        color: #555;
        border-bottom: 2px solid #9E8A78;
        vertical-align: middle;
    }

    .table tbody td {
        vertical-align: middle;
        padding: 12px 8px;
    }

    /* Cải thiện giao diện badge */
    .badge {
        font-weight: 500;
        padding: 5px 8px;
        border-radius: 4px;
    }

    /* Cải thiện dropdown menu */
    .dropdown-menu {
        border-radius: 4px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border: none;
        padding: 8px 0;
    }

    .dropdown-item {
        padding: 8px 15px;
        transition: background-color 0.2s;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
    }

    .dropdown-item i {
        width: 20px;
        text-align: center;
    }

    /* Cải thiện nút thao tác */
    .btn-outline-secondary {
        border-color: #ced4da;
        color: #6c757d;
    }

    .btn-outline-secondary:hover {
        background-color: #f8f9fa;
        color: #495057;
        border-color: #ced4da;
    }

    /* Hiệu ứng đánh giá sao */
    .rating-stars {
        cursor: pointer;
    }

    .rating-star {
        transition: transform 0.2s, color 0.2s;
        margin-right: 5px;
    }

    .rating-star:hover {
        transform: scale(1.2);
    }

    @keyframes star-pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.3); }
        100% { transform: scale(1); }
    }

    .animate-star {
        animation: star-pulse 0.3s ease-in-out;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-body text-center">
                <img src="{{ get_user_avatar($user, 'large') }}" alt="{{ $user->name }}" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                <h5 class="card-title">{{ $user->name }}</h5>
                <p class="text-muted">Thành viên từ {{ $user->created_at->format('d/m/Y') }}</p>
                <a href="{{ route('profile.edit') }}" class="btn mt-2" style="background-color: #9E8A78; color: white;">Chỉnh sửa hồ sơ</a>
            </div>
        </div>

        <div class="list-group mb-4">
            <a href="{{ route('profile.index') }}" class="list-group-item list-group-item-action">
                <i class="fas fa-user me-2"></i> Hồ sơ của tôi
            </a>
            <a href="{{ route('profile.appointments') }}" class="list-group-item list-group-item-action active">
                <i class="fas fa-calendar me-2"></i> Lịch hẹn của tôi
            </a>
            <a href="{{ route('profile.reviews') }}" class="list-group-item list-group-item-action">
                <i class="fas fa-star me-2"></i> Đánh giá của tôi
            </a>
        </div>
    </div>

    <div class="col-md-9">
        <div class="card">
            <div class="card-header text-white" style="background-color: #9E8A78;">
                <h5 class="card-title mb-0">Lịch hẹn của tôi</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif

                <!-- Tab navigation -->
                <ul class="nav nav-tabs mb-4" id="appointmentTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button" role="tab" aria-controls="upcoming" aria-selected="true">
                            <i class="fas fa-calendar-day me-2"></i>Sắp tới
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab" aria-controls="completed" aria-selected="false">
                            <i class="fas fa-check-circle me-2"></i>Đã hoàn thành
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="cancelled-tab" data-bs-toggle="tab" data-bs-target="#cancelled" type="button" role="tab" aria-controls="cancelled" aria-selected="false">
                            <i class="fas fa-times-circle me-2"></i>Đã hủy
                        </button>
                    </li>
                </ul>

                <!-- Tab content -->
                <div class="tab-content" id="appointmentTabsContent">
                    <!-- Upcoming appointments tab -->
                    <div class="tab-pane fade show active" id="upcoming" role="tabpanel" aria-labelledby="upcoming-tab">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 8%">Mã</th>
                                        <th style="width: 12%">Ngày</th>
                                        <th style="width: 8%">Giờ</th>
                                        <th style="width: 12%">Barber</th>
                                        <th style="width: 20%">Dịch vụ</th>
                                        <th style="width: 12%">Trạng thái</th>
                                        <th style="width: 15%">Thanh toán</th>
                                        <th style="width: 13%">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $upcomingAppointments = $appointments->filter(function($appointment) {
                                            return in_array($appointment->status, ['pending', 'confirmed']);
                                        });
                                    @endphp

                                    @forelse($upcomingAppointments as $appointment)
                                    <tr>
                                        <td>#{{ $appointment->id }}</td>
                                        <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}</td>
                                        <td>{{ $appointment->barber->user->name }}</td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($appointment->services as $service)
                                                <span class="badge bg-info text-white">{{ $service->name }}</span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            @if($appointment->status == 'pending')
                                            <span class="badge bg-warning text-dark d-flex align-items-center">
                                                <i class="fas fa-clock me-1"></i> Chờ xác nhận
                                            </span>
                                            @elseif($appointment->status == 'confirmed')
                                            <span class="badge bg-success d-flex align-items-center">
                                                <i class="fas fa-check me-1"></i> Đã xác nhận
                                            </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center mb-1">
                                                @if($appointment->payment_status == 'paid')
                                                <span class="badge bg-success d-flex align-items-center">
                                                    <i class="fas fa-check-circle me-1"></i> Đã thanh toán
                                                </span>
                                                @else
                                                <span class="badge bg-warning text-dark d-flex align-items-center">
                                                    <i class="fas fa-exclamation-circle me-1"></i> Chưa thanh toán
                                                </span>
                                                @endif
                                            </div>
                                            <div>
                                                @if($appointment->payment_method == 'cash')
                                                <small class="d-flex align-items-center text-muted">
                                                    <i class="fas fa-money-bill-wave text-success me-1"></i> Tiền mặt
                                                </small>
                                                @elseif($appointment->payment_method == 'bank_transfer')
                                                <small class="d-flex align-items-center text-muted">
                                                    <i class="fas fa-university text-primary me-1"></i> Chuyển khoản
                                                </small>
                                                @if($appointment->paymentReceipt)
                                                    <div class="mt-1">
                                                    @if($appointment->paymentReceipt->status == 'pending')
                                                    <span class="badge bg-info d-inline-flex align-items-center">
                                                        <i class="fas fa-clock me-1"></i> Chờ xác nhận
                                                    </span>
                                                    @elseif($appointment->paymentReceipt->status == 'approved')
                                                    <span class="badge bg-success d-inline-flex align-items-center">
                                                        <i class="fas fa-check me-1"></i> Đã xác nhận
                                                    </span>
                                                    @elseif($appointment->paymentReceipt->status == 'rejected')
                                                    <span class="badge bg-danger d-inline-flex align-items-center">
                                                        <i class="fas fa-times me-1"></i> Đã từ chối
                                                    </span>
                                                    <a href="{{ route('appointment.payment.confirmation', $appointment->id) }}" class="d-block mt-1 small text-primary">
                                                        <i class="fas fa-upload me-1"></i> Gửi lại
                                                    </a>
                                                    @endif
                                                    </div>
                                                @else
                                                <a href="{{ route('appointment.payment.confirmation', $appointment->id) }}" class="d-block mt-1 small text-primary">
                                                    <i class="fas fa-upload me-1"></i> Gửi biên lai
                                                </a>
                                                @endif
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton{{ $appointment->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i> Thao tác
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $appointment->id }}">
                                                    <li>
                                                        <a href="{{ route('profile.appointment.detail', $appointment->id) }}" class="dropdown-item">
                                                            <i class="fas fa-eye text-info me-2"></i> Xem chi tiết
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <button class="dropdown-item text-danger" onclick="confirmCancel({{ $appointment->id }})">
                                                            <i class="fas fa-times-circle text-danger me-2"></i> Hủy lịch hẹn
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Bạn không có lịch hẹn sắp tới nào.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Completed appointments tab -->
                    <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 8%">Mã</th>
                                        <th style="width: 12%">Ngày</th>
                                        <th style="width: 8%">Giờ</th>
                                        <th style="width: 12%">Barber</th>
                                        <th style="width: 45%">Dịch vụ</th>
                                        <th style="width: 15%">Đánh giá</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $completedAppointments = $appointments->filter(function($appointment) {
                                            return $appointment->status == 'completed';
                                        });
                                    @endphp

                                    @forelse($completedAppointments as $appointment)
                                    <tr>
                                        <td>#{{ $appointment->id }}</td>
                                        <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}</td>
                                        <td>{{ $appointment->barber->user->name }}</td>
                                        <td>
                                            <div class="d-flex flex-column gap-2">
                                                @foreach($appointment->services as $service)
                                                <div class="d-flex align-items-center justify-content-between p-2 border rounded" style="background-color: #f8f9fa;">
                                                    <span class="badge bg-info text-white me-2">{{ $service->name }}</span>
                                                    @php
                                                        // Kiểm tra xem người dùng đã đánh giá dịch vụ này trong lịch hẹn này chưa
                                                        // Sử dụng appointment_id để đảm bảo đánh giá thuộc về lịch hẹn hiện tại
                                                        $hasReview = App\Models\Review::where('user_id', Auth::id())
                                                            ->where('service_id', $service->id)
                                                            ->where('barber_id', $appointment->barber_id)
                                                            ->where('appointment_id', $appointment->id)
                                                            ->exists();
                                                    @endphp

                                                    @if($hasReview)
                                                        <span class="badge bg-success d-flex align-items-center">
                                                            <i class="fas fa-check-circle me-1"></i> Đã đánh giá
                                                        </span>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#reviewModal"
                                                            data-service-id="{{ $service->id }}"
                                                            data-service-name="{{ $service->name }}"
                                                            data-barber-id="{{ $appointment->barber_id }}"
                                                            data-barber-name="{{ $appointment->barber->user->name }}"
                                                            data-appointment-id="{{ $appointment->id }}">
                                                            <i class="fas fa-star me-1"></i> Đánh giá
                                                        </button>
                                                    @endif
                                                </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('profile.reviews') }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-list me-1"></i> Xem đánh giá
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Bạn không có lịch hẹn đã hoàn thành nào.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Cancelled appointments tab -->
                    <div class="tab-pane fade" id="cancelled" role="tabpanel" aria-labelledby="cancelled-tab">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 10%">Mã</th>
                                        <th style="width: 15%">Ngày</th>
                                        <th style="width: 10%">Giờ</th>
                                        <th style="width: 20%">Barber</th>
                                        <th style="width: 45%">Dịch vụ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $cancelledAppointments = $appointments->filter(function($appointment) {
                                            return $appointment->status == 'canceled';
                                        });
                                    @endphp

                                    @forelse($cancelledAppointments as $appointment)
                                    <tr>
                                        <td>#{{ $appointment->id }}</td>
                                        <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}</td>
                                        <td>{{ $appointment->barber->user->name }}</td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($appointment->services as $service)
                                                <span class="badge bg-info text-white">{{ $service->name }}</span>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Bạn không có lịch hẹn đã hủy nào.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('appointment.step1') }}" class="btn btn-primary">
                        <i class="fas fa-calendar-plus me-2"></i> Đặt lịch mới
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #9E8A78; color: white;">
                <h5 class="modal-title" id="reviewModalLabel">
                    <i class="fas fa-star me-2"></i> Đánh giá dịch vụ
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="reviewForm" action="{{ route('profile.reviews.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="service_id" id="service_id">
                    <input type="hidden" name="barber_id" id="barber_id">
                    <input type="hidden" name="appointment_id" id="appointment_id">

                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-cut text-primary me-2"></i>
                            <label class="form-label mb-0 fw-bold">Dịch vụ</label>
                        </div>
                        <p id="service_name" class="form-control-static ms-4 fw-bold text-primary"></p>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-user text-secondary me-2"></i>
                            <label class="form-label mb-0 fw-bold">Thợ cắt tóc</label>
                        </div>
                        <p id="barber_name" class="form-control-static ms-4"></p>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-star text-warning me-2"></i>
                            <label class="form-label mb-0 fw-bold">Đánh giá của bạn</label>
                        </div>
                        <div class="rating-stars mb-2 ms-4" style="font-size: 1.5rem;">
                            <input type="hidden" name="rating" id="rating" value="5">
                            <i class="fas fa-star text-warning rating-star" data-value="1"></i>
                            <i class="fas fa-star text-warning rating-star" data-value="2"></i>
                            <i class="fas fa-star text-warning rating-star" data-value="3"></i>
                            <i class="fas fa-star text-warning rating-star" data-value="4"></i>
                            <i class="fas fa-star text-warning rating-star" data-value="5"></i>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-comment text-info me-2"></i>
                            <label for="comment" class="form-label mb-0 fw-bold">Nhận xét của bạn</label>
                            <small class="text-muted ms-2">(không bắt buộc)</small>
                        </div>
                        <textarea class="form-control mt-2" id="comment" name="comment" rows="3" placeholder="Nhập nhận xét của bạn về dịch vụ này..."></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-image text-success me-2"></i>
                            <label for="review_images" class="form-label mb-0 fw-bold">Ảnh</label>
                            <small class="text-muted ms-2">(tùy chọn)</small>
                        </div>
                        <input class="form-control mt-2" type="file" id="review_images" name="review_images[]" multiple accept="image/*">
                        <small class="text-muted d-block mt-1">Bạn có thể tải lên nhiều ảnh (tối đa 5 ảnh)</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Hủy
                </button>
                <button type="button" class="btn btn-primary" id="submitReview">
                    <i class="fas fa-paper-plane me-1"></i> Gửi đánh giá
                </button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    function confirmCancel(appointmentId) {
        // Tạo modal xác nhận hủy lịch hẹn
        const modalHtml = `
            <div class="modal fade" id="cancelConfirmModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-exclamation-triangle me-2"></i> Xác nhận hủy lịch hẹn
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-2">Bạn có chắc chắn muốn hủy lịch hẹn này không?</p>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <strong>Lưu ý:</strong> Nếu bạn hủy lịch hẹn trong vòng 24 giờ trước thời gian hẹn, bạn có thể sẽ bị tính phí hủy.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i> Đóng
                            </button>
                            <form action="/appointment/cancel/${appointmentId}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-check me-1"></i> Xác nhận hủy
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Thêm modal vào body
        const modalContainer = document.createElement('div');
        modalContainer.innerHTML = modalHtml;
        document.body.appendChild(modalContainer);

        // Hiển thị modal
        const modal = new bootstrap.Modal(document.getElementById('cancelConfirmModal'));
        modal.show();

        // Xóa modal khi đóng
        document.getElementById('cancelConfirmModal').addEventListener('hidden.bs.modal', function () {
            document.body.removeChild(modalContainer);
        });
    }

    // Xử lý modal đánh giá
    document.addEventListener('DOMContentLoaded', function() {
        // Lấy thông tin khi mở modal
        const reviewModal = document.getElementById('reviewModal');
        if (reviewModal) {
            reviewModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const serviceId = button.getAttribute('data-service-id');
                const serviceName = button.getAttribute('data-service-name');
                const barberId = button.getAttribute('data-barber-id');
                const barberName = button.getAttribute('data-barber-name');
                const appointmentId = button.getAttribute('data-appointment-id');

                document.getElementById('service_id').value = serviceId;
                document.getElementById('barber_id').value = barberId;
                document.getElementById('appointment_id').value = appointmentId;
                document.getElementById('service_name').textContent = serviceName;
                document.getElementById('barber_name').textContent = barberName;
            });
        }

        // Xử lý đánh giá sao
        const ratingStars = document.querySelectorAll('.rating-star');
        const ratingInput = document.getElementById('rating');

        // Thêm hiệu ứng hover cho sao
        ratingStars.forEach(star => {
            // Hover vào sao
            star.addEventListener('mouseover', function() {
                const hoverValue = this.getAttribute('data-value');
                highlightStars(hoverValue);
            });

            // Rời chuột khỏi khu vực sao
            star.parentElement.addEventListener('mouseleave', function() {
                const currentValue = ratingInput.value;
                highlightStars(currentValue);
            });

            // Click vào sao
            star.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                ratingInput.value = value;
                highlightStars(value);

                // Thêm hiệu ứng nhấp nháy khi chọn sao
                ratingStars.forEach(s => {
                    if (s.getAttribute('data-value') <= value) {
                        s.classList.add('animate-star');
                        setTimeout(() => {
                            s.classList.remove('animate-star');
                        }, 300);
                    }
                });
            });
        });

        // Hàm highlight các sao
        function highlightStars(value) {
            ratingStars.forEach(s => {
                if (s.getAttribute('data-value') <= value) {
                    s.classList.remove('far');
                    s.classList.add('fas');
                    s.classList.add('text-warning');
                } else {
                    s.classList.remove('fas');
                    s.classList.add('far');
                    s.classList.add('text-warning');
                }
            });
        }

        // Xử lý gửi form đánh giá
        const submitReviewBtn = document.getElementById('submitReview');
        if (submitReviewBtn) {
            submitReviewBtn.addEventListener('click', function() {
                document.getElementById('reviewForm').submit();
            });
        }
    });
</script>
@endsection