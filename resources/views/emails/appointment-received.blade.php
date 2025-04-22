<!DOCTYPE html>
<html>
<head>
    <title>Đã nhận yêu cầu đặt lịch</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .booking-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #777;
        }
        .highlight {
            background-color: #fffacd;
            padding: 10px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #ffd700;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Đã nhận yêu cầu đặt lịch tại Barber Shop</h2>
        </div>

        <p>Chào {{ $appointment->customer_name }},</p>

        <p>Cảm ơn bạn đã đặt lịch tại Barber Shop. Chúng tôi đã nhận được yêu cầu đặt lịch của bạn và đang xử lý.</p>

        <div class="highlight">
            <p><strong>Lưu ý quan trọng:</strong> Lịch hẹn của bạn đang chờ xác nhận từ nhân viên của chúng tôi. Bạn sẽ nhận được email xác nhận kèm theo mã đặt chỗ sau khi lịch hẹn được xác nhận.</p>
        </div>

        <div class="booking-info">
            <p><strong>Thông tin lịch hẹn:</strong></p>
            <p><strong>Ngày hẹn:</strong> {{ date('d/m/Y', strtotime($appointment->appointment_date)) }}</p>
            <p><strong>Giờ hẹn:</strong> {{ $appointment->start_time }} - {{ $appointment->end_time }}</p>
            <p><strong>Thợ cắt tóc:</strong> {{ $appointment->barber->user->name }}</p>

            <p><strong>Dịch vụ:</strong></p>
            <ul>
                @foreach($appointment->services as $service)
                <li>{{ $service->name }} - {{ number_format($service->price) }}đ</li>
                @endforeach
            </ul>

            <p><strong>Phương thức thanh toán:</strong> 
                @if($appointment->payment_method == 'cash')
                    Tiền mặt
                @elseif($appointment->payment_method == 'bank')
                    Chuyển khoản ngân hàng
                @endif
            </p>
        </div>

        @if($appointment->payment_method == 'bank')
        <div class="payment-info">
            <p><strong>Thông tin thanh toán:</strong></p>
            <p>Ngân hàng: VCB - Vietcombank</p>
            <p>Số tài khoản: 1234567890</p>
            <p>Chủ tài khoản: CÔNG TY TNHH BARBER SHOP</p>
            <p>Nội dung chuyển khoản: {{ $appointment->booking_code }}</p>
        </div>
        @endif

        <p>Nếu bạn cần thay đổi hoặc hủy lịch hẹn, vui lòng liên hệ với chúng tôi qua số điện thoại hoặc email dưới đây.</p>

        <p>Trân trọng,<br>Đội ngũ Barber Shop</p>

        <div class="footer">
            <p>Địa chỉ: 123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh</p>
            <p>Điện thoại: 028 1234 5678 | Email: info@barbershop.vn</p>
            <p>© 2025 Barber Shop. Đã đăng ký Bản quyền.</p>
        </div>
    </div>
</body>
</html>
