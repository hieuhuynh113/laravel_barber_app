<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thông báo hủy lịch hẹn tại Barbershop</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #dc3545;
            color: #ffffff;
            padding: 15px;
            text-align: center;
            border-radius: 5px 5px 0 0;
            margin-bottom: 20px;
        }
        .highlight {
            background-color: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #dc3545;
            margin: 20px 0;
        }
        .appointment-details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            text-align: center;
            font-size: 0.9em;
            color: #777;
        }
        .btn {
            display: inline-block;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin-top: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Thông báo hủy lịch hẹn tại Barber Shop</h2>
        </div>

        <p>Chào {{ $appointment->customer_name }},</p>

        <p>Chúng tôi xác nhận rằng lịch hẹn của bạn tại Barber Shop đã được hủy thành công.</p>

        <div class="appointment-details">
            <h3>Chi tiết lịch hẹn đã hủy:</h3>
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
                    <td>{{ $appointment->appointment_time }}</td>
                </tr>
                <tr>
                    <th>Thợ cắt tóc:</th>
                    <td>{{ $appointment->barber->user->name }}</td>
                </tr>
                <tr>
                    <th>Dịch vụ:</th>
                    <td>
                        @foreach($appointment->services as $service)
                            {{ $service->name }} ({{ number_format($service->price) }} VNĐ)<br>
                        @endforeach
                    </td>
                </tr>
            </table>
        </div>

        <p>Nếu bạn muốn đặt lịch hẹn mới, vui lòng truy cập trang web của chúng tôi hoặc liên hệ với chúng tôi qua số điện thoại hoặc email dưới đây.</p>

        <div class="highlight">
            <p>Lưu ý: Nếu bạn đã thanh toán trước cho lịch hẹn này, vui lòng liên hệ với chúng tôi để được hỗ trợ hoàn tiền.</p>
        </div>

        <p>Trân trọng,<br>Đội ngũ Barber Shop</p>

        <div class="footer">
            <p>Địa chỉ: 123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh</p>
            <p>Điện thoại: 028 1234 5678 | Email: info@barbershop.vn</p>
            <p>© {{ date('Y') }} Barber Shop. Đã đăng ký Bản quyền.</p>
        </div>
    </div>
</body>
</html>
