<!DOCTYPE html>
<html>
<head>
    <title>Xác nhận đặt lịch</title>
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
        .booking-code {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            color: #007bff;
            padding: 10px;
            border: 2px dashed #007bff;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Xác nhận đặt lịch tại Barbershop</h2>
        </div>
        
        <p>Chào {{ $appointment->customer->name }},</p>
        
        <p>Cảm ơn bạn đã đặt lịch tại Barbershop. Dưới đây là thông tin chi tiết về lịch hẹn của bạn:</p>
        
        <div class="booking-info">
            <p><strong>Mã đặt lịch:</strong></p>
            <div class="booking-code">{{ $appointment->booking_code }}</div>
            
            <p><strong>Ngày hẹn:</strong> {{ date('d/m/Y', strtotime($appointment->appointment_date)) }}</p>
            <p><strong>Giờ hẹn:</strong> {{ date('H:i', strtotime($appointment->appointment_time)) }}</p>
            <p><strong>Thợ cắt tóc:</strong> {{ $appointment->barber->name }}</p>
            
            <p><strong>Dịch vụ:</strong></p>
            <ul>
                @foreach($appointment->services as $service)
                <li>{{ $service->name }} - {{ number_format($service->price) }}đ</li>
                @endforeach
            </ul>
            
            <p><strong>Tổng giá tiền:</strong> {{ number_format($appointment->total_price) }}đ</p>
            <p><strong>Phương thức thanh toán:</strong> {{ $appointment->payment_method == 'bank' ? 'Chuyển khoản ngân hàng' : 'Thanh toán tại tiệm' }}</p>
        </div>
        
        @if($appointment->payment_method == 'bank')
        <div class="payment-info">
            <p><strong>Thông tin thanh toán:</strong></p>
            <p>Ngân hàng: VCB - Vietcombank</p>
            <p>Số tài khoản: 1234567890</p>
            <p>Chủ tài khoản: CÔNG TY TNHH BARBERSHOP</p>
            <p>Nội dung chuyển khoản: {{ $appointment->booking_code }}</p>
        </div>
        @endif
        
        <p>Vui lòng đến đúng giờ hẹn. Nếu bạn cần thay đổi hoặc hủy lịch hẹn, vui lòng liên hệ với chúng tôi ít nhất 2 giờ trước giờ hẹn.</p>
        
        <p>Trân trọng,<br>Đội ngũ Barbershop</p>
        
        <div class="footer">
            <p>Địa chỉ: 123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh</p>
            <p>Điện thoại: 028 1234 5678 | Email: info@barbershop.com</p>
            <p>© 2025 Barbershop. Đã đăng ký Bản quyền.</p>
        </div>
    </div>
</body>
</html>