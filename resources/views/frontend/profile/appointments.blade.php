@extends('layouts.frontend')

@section('title', 'Lịch hẹn của tôi')

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-body text-center">
                <img src="{{ $user->avatar ? asset($user->avatar) : asset('images/avatar-placeholder.jpg') }}" alt="{{ $user->name }}" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                <h5 class="card-title">{{ $user->name }}</h5>
                <p class="text-muted">Thành viên từ {{ $user->created_at->format('d/m/Y') }}</p>
                <a href="{{ route('profile.edit') }}" class="btn btn-primary mt-2">Chỉnh sửa hồ sơ</a>
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
            <div class="card-header bg-primary text-white">
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
                        <button class="nav-link active" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button" role="tab" aria-controls="upcoming" aria-selected="true">Sắp tới</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab" aria-controls="completed" aria-selected="false">Đã hoàn thành</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="cancelled-tab" data-bs-toggle="tab" data-bs-target="#cancelled" type="button" role="tab" aria-controls="cancelled" aria-selected="false">Đã hủy</button>
                    </li>
                </ul>

                <!-- Tab content -->
                <div class="tab-content" id="appointmentTabsContent">
                    <!-- Upcoming appointments tab -->
                    <div class="tab-pane fade show active" id="upcoming" role="tabpanel" aria-labelledby="upcoming-tab">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Mã lịch hẹn</th>
                                        <th>Ngày</th>
                                        <th>Giờ</th>
                                        <th>Barber</th>
                                        <th>Dịch vụ</th>
                                        <th>Trạng thái</th>
                                        <th>Thanh toán</th>
                                        <th>Thao tác</th>
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
                                            @foreach($appointment->services as $service)
                                            <span class="badge bg-info">{{ $service->name }}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            @if($appointment->status == 'pending')
                                            <span class="badge bg-warning">Chờ xác nhận</span>
                                            @elseif($appointment->status == 'confirmed')
                                            <span class="badge bg-success">Đã xác nhận</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                @if($appointment->payment_status == 'paid')
                                                <span class="badge bg-success">Đã thanh toán</span>
                                                @else
                                                <span class="badge bg-warning">Chưa thanh toán</span>
                                                @endif
                                            </div>
                                            <div class="mt-1">
                                                @if($appointment->payment_method == 'cash')
                                                <small><i class="fas fa-money-bill-wave text-success"></i> Tiền mặt</small>
                                                @elseif($appointment->payment_method == 'bank_transfer')
                                                <small><i class="fas fa-university text-primary"></i> Chuyển khoản</small>
                                                @if($appointment->paymentReceipt)
                                                    @if($appointment->paymentReceipt->status == 'pending')
                                                    <span class="badge bg-info">Chờ xác nhận</span>
                                                    @elseif($appointment->paymentReceipt->status == 'approved')
                                                    <span class="badge bg-success">Đã xác nhận</span>
                                                    @elseif($appointment->paymentReceipt->status == 'rejected')
                                                    <span class="badge bg-danger">Đã từ chối</span>
                                                    <a href="{{ route('appointment.payment.confirmation', $appointment->id) }}" class="d-block mt-1 small">Gửi lại</a>
                                                    @endif
                                                @else
                                                <a href="{{ route('appointment.payment.confirmation', $appointment->id) }}" class="d-block mt-1 small">Gửi biên lai</a>
                                                @endif
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-danger" onclick="confirmCancel({{ $appointment->id }})">Hủy</button>
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
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Mã lịch hẹn</th>
                                        <th>Ngày</th>
                                        <th>Giờ</th>
                                        <th>Barber</th>
                                        <th>Dịch vụ</th>
                                        <th>Đánh giá</th>
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
                                            @foreach($appointment->services as $service)
                                            <div class="mb-1">
                                                <span class="badge bg-info">{{ $service->name }}</span>
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
                                                    <span class="badge bg-success">Đã đánh giá</span>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-outline-primary ms-1"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#reviewModal"
                                                        data-service-id="{{ $service->id }}"
                                                        data-service-name="{{ $service->name }}"
                                                        data-barber-id="{{ $appointment->barber_id }}"
                                                        data-barber-name="{{ $appointment->barber->user->name }}"
                                                        data-appointment-id="{{ $appointment->id }}">
                                                        Đánh giá
                                                    </button>
                                                @endif
                                            </div>
                                            @endforeach
                                        </td>
                                        <td>
                                            <a href="{{ route('profile.reviews') }}" class="btn btn-sm btn-outline-secondary">Xem đánh giá</a>
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
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Mã lịch hẹn</th>
                                        <th>Ngày</th>
                                        <th>Giờ</th>
                                        <th>Barber</th>
                                        <th>Dịch vụ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $cancelledAppointments = $appointments->filter(function($appointment) {
                                            return $appointment->status == 'cancelled';
                                        });
                                    @endphp

                                    @forelse($cancelledAppointments as $appointment)
                                    <tr>
                                        <td>#{{ $appointment->id }}</td>
                                        <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}</td>
                                        <td>{{ $appointment->barber->user->name }}</td>
                                        <td>
                                            @foreach($appointment->services as $service)
                                            <span class="badge bg-info">{{ $service->name }}</span>
                                            @endforeach
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
                    <a href="{{ route('appointment.step1') }}" class="btn btn-primary">Đặt lịch mới</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewModalLabel">Đánh giá dịch vụ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="reviewForm" action="{{ route('profile.reviews.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="service_id" id="service_id">
                    <input type="hidden" name="barber_id" id="barber_id">
                    <input type="hidden" name="appointment_id" id="appointment_id">

                    <div class="mb-3">
                        <label class="form-label">Dịch vụ</label>
                        <p id="service_name" class="form-control-static fw-bold"></p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Thợ cắt tóc</label>
                        <p id="barber_name" class="form-control-static"></p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Đánh giá của bạn</label>
                        <div class="rating-stars mb-2">
                            <input type="hidden" name="rating" id="rating" value="5">
                            <i class="fas fa-star text-warning rating-star" data-value="1"></i>
                            <i class="fas fa-star text-warning rating-star" data-value="2"></i>
                            <i class="fas fa-star text-warning rating-star" data-value="3"></i>
                            <i class="fas fa-star text-warning rating-star" data-value="4"></i>
                            <i class="fas fa-star text-warning rating-star" data-value="5"></i>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="comment" class="form-label">Nhận xét của bạn (không bắt buộc)</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3" placeholder="Nhập nhận xét của bạn (không bắt buộc)"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="review_images" class="form-label">Ảnh (tùy chọn)</label>
                        <input class="form-control" type="file" id="review_images" name="review_images[]" multiple accept="image/*">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="submitReview">Gửi đánh giá</button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    function confirmCancel(appointmentId) {
        if (confirm('Bạn có chắc chắn muốn hủy lịch hẹn này không?')) {
            // Nếu xác nhận, gửi request để hủy lịch hẹn
            // Phần này sẽ được hoàn thiện khi tạo route và controller để hủy lịch hẹn
            alert('Chức năng đang được phát triển');
        }
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

        ratingStars.forEach(star => {
            star.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                ratingInput.value = value;

                // Cập nhật hiển thị sao
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
            });
        });

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