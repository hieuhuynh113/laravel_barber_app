@extends('layouts.app')

@section('title', 'Quản lý lịch hẹn')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<link rel="stylesheet" href="{{ asset('css/barber-dashboard.css') }}">
<style>
    .filter-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        margin-bottom: 1.5rem;
        padding: 1.25rem;
    }

    .appointments-table th, .appointments-table td {
        vertical-align: middle;
    }

    .status-badge {
        padding: 0.5rem 0.75rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.8rem;
    }

    .status-pending {
        background-color: #f8f9fa;
        color: #f39c12;
        border: 1px solid #f39c12;
    }

    .status-confirmed {
        background-color: #e8f4fd;
        color: #3498db;
        border: 1px solid #3498db;
    }

    .status-completed {
        background-color: #e8f8f5;
        color: #2ecc71;
        border: 1px solid #2ecc71;
    }

    .status-canceled {
        background-color: #fdeeee;
        color: #e74c3c;
        border: 1px solid #e74c3c;
    }

    .action-btn {
        padding: 0.5rem 0.75rem;
        border-radius: 5px;
        transition: all 0.3s;
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .action-btn i {
        font-size: 0.9rem;
    }

    .service-badge {
        background-color: #f8f9fa;
        color: #2c3e50;
        border: 1px solid #ddd;
        padding: 0.25rem 0.5rem;
        border-radius: 50px;
        font-size: 0.75rem;
        margin-right: 0.25rem;
        margin-bottom: 0.25rem;
        display: inline-block;
    }

    /* Tối ưu hiệu suất modal */
    .modal-content {
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        will-change: transform;
    }

    .modal-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1rem 1.5rem;
    }

    .modal-footer {
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1rem 1.5rem;
    }

    .modal-body {
        padding: 1.5rem;
    }

    /* Tối ưu hiệu suất tooltip */
    .tooltip {
        pointer-events: none;
    }
</style>
@endsection

@section('content')
<div class="container dashboard-container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="dashboard-title">Quản lý lịch hẹn</h1>
                    <p class="dashboard-subtitle">Xem và quản lý tất cả các lịch hẹn của bạn</p>
                </div>
                <a href="{{ route('barber.dashboard') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại Dashboard
                </a>
            </div>

            <!-- Bộ lọc -->
            <div class="filter-card">
                <form action="{{ route('barber.appointments.index') }}" method="GET" class="row align-items-end">
                    <div class="col-md-4 mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Tất cả trạng thái</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                            <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Đã hủy</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="date" class="form-label">Ngày hẹn</label>
                        <input type="date" name="date" id="date" class="form-control" value="{{ request('date') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i>Lọc
                        </button>
                    </div>
                </form>
            </div>

            <!-- Danh sách lịch hẹn -->
            <div class="card shadow">
                <div class="card-body">
                    @if($appointments->count() > 0)
                        <div class="table-responsive">
                            <table class="table appointments-table">
                                <thead>
                                    <tr>
                                        <th>Mã đặt lịch</th>
                                        <th>Khách hàng</th>
                                        <th>Ngày</th>
                                        <th>Giờ</th>
                                        <th>Dịch vụ</th>
                                        <th>Trạng thái</th>
                                        <th style="width: 120px;">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($appointments as $appointment)
                                        <tr>
                                            <td>{{ $appointment->booking_code }}</td>
                                            <td>{{ $appointment->customer_name }}</td>
                                            <td>{{ $appointment->appointment_date->format('d/m/Y') }}</td>
                                            <td>{{ $appointment->time_slot }}</td>
                                            <td>
                                                @foreach($appointment->services as $service)
                                                    <span class="service-badge">{{ $service->name }}</span>
                                                @endforeach
                                            </td>
                                            <td>
                                                @if($appointment->status == 'pending')
                                                    <span class="status-badge status-pending">Chờ xác nhận</span>
                                                @elseif($appointment->status == 'confirmed')
                                                    <span class="status-badge status-confirmed">Đã xác nhận</span>
                                                @elseif($appointment->status == 'completed')
                                                    <span class="status-badge status-completed">Hoàn thành</span>
                                                @elseif($appointment->status == 'canceled')
                                                    <span class="status-badge status-canceled">Đã hủy</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <a href="{{ route('barber.appointments.show', $appointment->id) }}" class="btn btn-info action-btn me-1" title="Xem chi tiết">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($appointment->status == 'pending')
                                                        <a href="{{ route('barber.appointments.show', $appointment->id) }}" class="btn btn-primary action-btn me-1" title="Xác nhận lịch hẹn">
                                                            <i class="fas fa-check-circle"></i>
                                                        </a>
                                                    @elseif($appointment->status == 'confirmed')
                                                        <button type="button" class="btn btn-success action-btn"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#completeModal"
                                                            data-appointment-id="{{ $appointment->id }}"
                                                            data-appointment-route="{{ route('barber.appointments.complete', $appointment->id) }}"
                                                            title="Đánh dấu hoàn thành">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Phân trang -->
                        <div class="mt-4">
                            {{ $appointments->appends(request()->query())->links('barber.partials.pagination') }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                            <h4>Không có lịch hẹn nào</h4>
                            <p class="text-muted">Không tìm thấy lịch hẹn nào phù hợp với bộ lọc của bạn.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- Modal xác nhận hoàn thành chung -->
<div class="modal fade" id="completeModal" tabindex="-1" aria-labelledby="completeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered animate__animated animate__fadeInDown animate__faster">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="completeModalLabel">Xác nhận hoàn thành lịch hẹn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="completeForm" action="" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Bạn đang chuyển trạng thái lịch hẹn sang "Hoàn thành". Hệ thống sẽ tạo hóa đơn tự động.
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Trạng thái thanh toán:</label>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_status" id="payment-pending" value="pending" checked>
                            <label class="form-check-label" for="payment-pending">
                                <i class="fas fa-clock text-warning me-1"></i> Chưa thanh toán
                            </label>
                            <small class="text-muted d-block">Hóa đơn sẽ được tạo với trạng thái "Chưa thanh toán".</small>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_status" id="payment-paid" value="paid">
                            <label class="form-check-label" for="payment-paid">
                                <i class="fas fa-check-circle text-success me-1"></i> Đã thanh toán
                            </label>
                            <small class="text-muted d-block">Hóa đơn sẽ được tạo với trạng thái "Đã thanh toán".</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Hủy
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i> Xác nhận
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal xem hóa đơn -->
<div class="modal fade" id="invoiceModal" tabindex="-1" aria-labelledby="invoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="invoiceModalLabel">Chi tiết hóa đơn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Nội dung hóa đơn sẽ được tải bằng AJAX -->
                <div class="text-center py-5">
                    <i class="fas fa-spinner fa-spin fa-3x"></i>
                    <p class="mt-3">Đang tải hóa đơn...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Đóng
                </button>
                <a href="#" class="btn btn-success" id="printInvoiceBtn" target="_blank">
                    <i class="fas fa-print me-1"></i> In hóa đơn
                </a>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script src="{{ asset('js/invoice-modal.js') }}"></script>
<script>
    $(document).ready(function() {
        // Kiểm tra xem có thông tin hóa đơn vừa hoàn thành trong localStorage không
        const lastCompletedInvoiceId = localStorage.getItem('lastCompletedInvoiceId');
        const lastCompletedInvoiceUrl = localStorage.getItem('lastCompletedInvoiceUrl');
        const lastCompletedTimestamp = localStorage.getItem('lastCompletedTimestamp');

        // Chỉ hiển thị thông báo nếu hóa đơn được tạo trong vòng 5 giây trước đó
        if (lastCompletedInvoiceId && lastCompletedInvoiceUrl && lastCompletedTimestamp) {
            const now = new Date().getTime();
            const timeDiff = now - parseInt(lastCompletedTimestamp);

            // Nếu thời gian từ khi tạo hóa đơn đến hiện tại nhỏ hơn 5 giây
            if (timeDiff < 5000) {
                // Hiển thị thông báo với nút xem hóa đơn
                const alertHtml = `
                    <div class="alert alert-success alert-dismissible fade show animate__animated animate__fadeIn" role="alert">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-check-circle me-2"></i> Lịch hẹn đã được đánh dấu hoàn thành thành công.
                            </div>
                            <button type="button" class="btn btn-sm btn-primary animate__animated animate__fadeIn"
                                    onclick="window.open('${lastCompletedInvoiceUrl}', '_blank')">
                                <i class="fas fa-file-invoice me-1"></i> Xem hóa đơn
                            </button>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;

                // Thêm thông báo vào đầu trang
                $('.dashboard-container').prepend(alertHtml);

                // Xóa thông tin hóa đơn khỏi localStorage
                localStorage.removeItem('lastCompletedInvoiceId');
                localStorage.removeItem('lastCompletedInvoiceUrl');
                localStorage.removeItem('lastCompletedTimestamp');
            }
        }

        // Xử lý sự kiện khi modal được hiển thị
        $('#completeModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Nút được nhấn
            var appointmentId = button.data('appointment-id'); // Lấy thông tin từ data-* attributes
            var appointmentRoute = button.data('appointment-route');

            console.log("Modal opening for appointment ID:", appointmentId);
            console.log("Route:", appointmentRoute);

            // Cập nhật action của form
            $('#completeForm').attr('action', appointmentRoute);

            // Reset form
            $('#completeForm').find('input[name="payment_status"][value="pending"]').prop('checked', true);
        });

        // Xử lý form submit bằng AJAX
        $('#completeForm').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            const formData = form.serialize();
            const url = form.attr('action');

            // Hiển thị loading khi submit
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Đang xử lý...');

            // Thêm hiệu ứng mờ cho modal body
            form.closest('.modal-content').css('opacity', '0.7');

            // Gửi request AJAX
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    console.log('Response from server:', response);

                    // Đóng modal
                    $('#completeModal').modal('hide');

                    // Lấy thông tin hóa đơn từ phản hồi
                    const invoiceId = response.invoice ? response.invoice.id : null;
                    console.log('Invoice ID:', invoiceId);

                    // Tạo URL cho hóa đơn
                    let invoiceUrl = null;
                    if (invoiceId) {
                        invoiceUrl = "{{ url('barber/invoices') }}/" + invoiceId;
                        console.log('Invoice URL:', invoiceUrl);
                    }

                    // Hiển thị thông báo thành công với nút xem hóa đơn
                    let alertHtml = `
                        <div class="alert alert-success alert-dismissible fade show animate__animated animate__fadeIn" role="alert">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-check-circle me-2"></i> Lịch hẹn đã được đánh dấu hoàn thành thành công.
                                </div>
                    `;

                    if (invoiceId) {
                        alertHtml += `
                                <button type="button" class="btn btn-sm btn-primary animate__animated animate__fadeIn animate__delay-1s"
                                        onclick="showInvoiceModal(${invoiceId})">
                                    <i class="fas fa-file-invoice me-1"></i> Xem hóa đơn
                                </button>
                        `;
                    }

                    alertHtml += `
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;

                    // Thêm thông báo vào đầu trang
                    $('.dashboard-container').prepend(alertHtml);

                    // Lưu thông tin hóa đơn vào localStorage để sử dụng sau khi tải lại trang
                    if (invoiceId) {
                        // Hiển thị thông tin debug
                        console.log('Saving invoice info to localStorage:');
                        console.log('- Invoice ID:', invoiceId);
                        console.log('- Invoice URL:', invoiceUrl);

                        // Lưu vào localStorage
                        localStorage.setItem('lastCompletedInvoiceId', invoiceId);
                        localStorage.setItem('lastCompletedInvoiceUrl', invoiceUrl);
                        localStorage.setItem('lastCompletedTimestamp', new Date().getTime());

                        // Thêm debug để kiểm tra xem đã lưu thành công chưa
                        console.log('Saved to localStorage:', {
                            id: localStorage.getItem('lastCompletedInvoiceId'),
                            url: localStorage.getItem('lastCompletedInvoiceUrl'),
                            timestamp: localStorage.getItem('lastCompletedTimestamp')
                        });
                    }

                    // Nếu có hóa đơn, hiển thị modal hóa đơn
                    if (invoiceId) {
                        // Hiển thị modal hóa đơn sau 500ms để người dùng có thể thấy thông báo thành công
                        setTimeout(function() {
                            showInvoiceModal(invoiceId);
                        }, 500);
                    } else {
                        // Nếu không có hóa đơn, tải lại trang sau 1.5 giây
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    }
                },
                error: function(xhr) {
                    // Khôi phục nút submit
                    submitBtn.prop('disabled', false).html('<i class="fas fa-check me-1"></i> Xác nhận');
                    form.closest('.modal-content').css('opacity', '1');

                    // Hiển thị thông báo lỗi
                    let errorMessage = 'Đã xảy ra lỗi khi xử lý yêu cầu.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    const errorHtml = `
                        <div class="alert alert-danger mt-3">
                            <i class="fas fa-exclamation-circle me-2"></i> ${errorMessage}
                        </div>
                    `;

                    // Thêm thông báo lỗi vào modal
                    form.find('.modal-body').append(errorHtml);
                }
            });
        });

        // Khởi tạo tooltip cho các nút thao tác
        $('[title]').tooltip({
            placement: 'top',
            trigger: 'hover',
            delay: { show: 300, hide: 0 }
        });

        // Tắt tooltip khi click vào nút
        $('[title]').on('click', function() {
            $(this).tooltip('hide');
        });

        // Xóa thông báo lỗi khi modal đóng
        $('#completeModal').on('hidden.bs.modal', function() {
            $(this).find('.alert-danger').remove();
            $(this).find('.modal-content').css('opacity', '1');
            $(this).find('button[type="submit"]').prop('disabled', false).html('<i class="fas fa-check me-1"></i> Xác nhận');
        });

        // Xử lý sự kiện khi nhấn nút "Xem hóa đơn" trong thông báo
        $(document).on('click', '.view-invoice-btn', function(e) {
            e.preventDefault();
            const invoiceId = $(this).data('invoice-id');
            showInvoiceModal(invoiceId);
        });
    });

    /**
     * Hiển thị modal hóa đơn
     */
    function showInvoiceModal(invoiceId) {
        // Hiển thị loading
        $('#invoiceModal .modal-body').html(`
            <div class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-3x"></i>
                <p class="mt-3">Đang tải hóa đơn...</p>
            </div>
        `);

        // Cập nhật tiêu đề modal
        $('#invoiceModalLabel').text('Chi tiết hóa đơn #' + invoiceId);

        // Cập nhật URL cho các nút
        const viewUrl = "{{ url('barber/invoices') }}/" + invoiceId;
        const printUrl = viewUrl + "/print";

        $('#viewFullInvoiceBtn').attr('href', viewUrl);
        $('#printInvoiceBtn').attr('href', printUrl);

        // Hiển thị modal
        $('#invoiceModal').modal('show');

        // Tải dữ liệu hóa đơn bằng AJAX
        $.ajax({
            url: "{{ url('barber/invoices') }}/" + invoiceId + "/data",
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('Invoice data received:', response);

                // Tạo HTML cho modal trực tiếp thay vì tải template
                const modalHtml = generateInvoiceModalHtml(response);

                // Cập nhật nội dung modal
                $('#invoiceModal .modal-body').html(modalHtml);
            },
            error: function(xhr) {
                // Hiển thị thông báo lỗi
                let errorMessage = 'Đã xảy ra lỗi khi tải dữ liệu hóa đơn.';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }

                $('#invoiceModal .modal-body').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i> ${errorMessage}
                    </div>
                `);
            }
        });
    }

    // Hàm generateInvoiceModalHtml đã được di chuyển vào file js/invoice-modal.js

    // Hàm updateInvoiceModalData đã được loại bỏ vì không còn sử dụng
</script>
@endsection