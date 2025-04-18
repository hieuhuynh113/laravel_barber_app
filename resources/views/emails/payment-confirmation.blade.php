<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Xác nhận thanh toán</title>
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
            background-color: #28a745;
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
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Xác nhận thanh toán</h1>
        </div>
        
        <div class="content">
            <p>Xin chào {{ $appointment->customer_name }},</p>
            
            <p>Chúng tôi xin thông báo rằng thanh toán của bạn cho lịch hẹn đã được xác nhận thành công. Dưới đây là thông tin chi tiết về lịch hẹn của bạn:</p>
            
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
                    <td><strong style="color: #28a745;">Đã thanh toán</strong></td>
                </tr>
            </table>
            
            <h3>Dịch vụ đã chọn:</h3>
            <table>
                <thead>
                    <tr>
                        <th>Dịch vụ</th>
                        <th>Giá</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($appointment->services as $service)
                        <tr>
                            <td>{{ $service->name }}</td>
                            <td>{{ number_format($service->pivot->price) }} VNĐ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <p>Lịch hẹn của bạn đã được xác nhận. Vui lòng đến đúng giờ để được phục vụ tốt nhất.</p>
            
            <p>Nếu bạn cần thay đổi hoặc hủy lịch hẹn, vui lòng liên hệ với chúng tôi ít nhất 2 giờ trước giờ hẹn.</p>
            
            <p>Cảm ơn bạn đã chọn dịch vụ của chúng tôi!</p>
            
            <a href="{{ route('profile.appointments') }}" class="btn">Xem lịch hẹn của tôi</a>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} Barber Shop. Tất cả các quyền được bảo lưu.</p>
            <p>Địa chỉ: 123 Đường ABC, Quận XYZ, Thành phố HCM</p>
            <p>Điện thoại: 0123 456 789 | Email: info@barbershop.com</p>
        </div>
    </div>
</body>
</html>
