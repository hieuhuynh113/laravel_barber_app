<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Thanh toán không được xác nhận</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #dc3545;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
            background-color: #f9f9f9;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #777;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #0d6efd;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Thanh toán không được xác nhận</h1>
        </div>
        
        <div class="content">
            <p>Xin chào {{ $appointment->customer_name }},</p>
            
            <div class="alert">
                <p>Chúng tôi rất tiếc phải thông báo rằng biên lai thanh toán của bạn cho lịch hẹn không được xác nhận.</p>
            </div>
            
            <h3>Thông tin lịch hẹn:</h3>
            <table>
                <tr>
                    <th>Mã đặt lịch:</th>
                    <td>{{ $appointment->booking_code }}</td>
                </tr>
                <tr>
                    <th>Ngày hẹn:</th>
                    <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <th>Giờ hẹn:</th>
                    <td>{{ $appointment->start_time }} - {{ $appointment->end_time }}</td>
                </tr>
                <tr>
                    <th>Thợ cắt tóc:</th>
                    <td>{{ $appointment->barber->user->name }}</td>
                </tr>
                <tr>
                    <th>Tổng tiền:</th>
                    <td>{{ number_format($appointment->services->sum('pivot.price')) }} VNĐ</td>
                </tr>
                <tr>
                    <th>Trạng thái thanh toán:</th>
                    <td><strong style="color: #dc3545;">Chưa thanh toán</strong></td>
                </tr>
            </table>
            
            @if($receipt->admin_notes)
            <h3>Lý do không xác nhận:</h3>
            <p>{{ $receipt->admin_notes }}</p>
            @endif
            
            <p>Vui lòng thực hiện lại thanh toán hoặc liên hệ với chúng tôi để được hỗ trợ.</p>
            
            <h3>Hướng dẫn thanh toán:</h3>
            <table>
                <tr>
                    <th>Ngân hàng:</th>
                    <td>Vietcombank</td>
                </tr>
                <tr>
                    <th>Số tài khoản:</th>
                    <td>1234567890</td>
                </tr>
                <tr>
                    <th>Chủ tài khoản:</th>
                    <td>CÔNG TY TNHH BARBER SHOP</td>
                </tr>
                <tr>
                    <th>Số tiền:</th>
                    <td>{{ number_format($appointment->services->sum('pivot.price')) }} VNĐ</td>
                </tr>
                <tr>
                    <th>Nội dung chuyển khoản:</th>
                    <td>{{ $appointment->booking_code }}</td>
                </tr>
            </table>
            
            <p>Sau khi chuyển khoản, vui lòng tải lên biên lai mới hoặc gửi biên lai qua email <a href="mailto:info@barbershop.com">info@barbershop.com</a>.</p>
            
            <p>Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi qua số điện thoại 0123 456 789.</p>
            
            <a href="{{ route('appointment.payment.confirmation', $appointment->id) }}" class="btn">Tải lên biên lai mới</a>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} Barber Shop. Tất cả các quyền được bảo lưu.</p>
            <p>Địa chỉ: 123 Đường ABC, Quận XYZ, Thành phố HCM</p>
            <p>Điện thoại: 0123 456 789 | Email: info@barbershop.com</p>
        </div>
    </div>
</body>
</html>
