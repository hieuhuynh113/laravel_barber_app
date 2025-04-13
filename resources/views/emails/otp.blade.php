<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-disable-message-reformatting">
    <title>Mã xác thực đăng ký tài khoản Barber Shop</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }
        .header {
            background-color: #2c3e50;
            color: #fff;
            padding: 25px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px 25px;
        }
        .otp-container {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
            border: 1px solid #e9ecef;
        }
        .otp-code {
            font-size: 36px;
            font-weight: bold;
            letter-spacing: 8px;
            color: #2c3e50;
            margin: 10px 0;
            display: inline-block;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            font-size: 13px;
            color: #6c757d;
        }
        .contact-info {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            background-color: #2c3e50;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin-top: 15px;
            font-weight: 500;
        }
        @media only screen and (max-width: 600px) {
            .container {
                width: 100% !important;
                padding: 10px !important;
            }
            .content {
                padding: 20px 15px !important;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Barber Shop - Xác thực tài khoản</h1>
        </div>
        <div class="content">
            <h2>Xin chào quý khách,</h2>
            <p>Cảm ơn bạn đã đăng ký tài khoản tại <strong>Barber Shop</strong>. Để đảm bảo tính bảo mật và hoàn tất quá trình đăng ký, vui lòng sử dụng mã xác thực (OTP) dưới đây:</p>

            <div class="otp-container">
                <p>Mã xác thực của bạn là:</p>
                <div class="otp-code">{{ $otp }}</div>
                <p>Mã này sẽ hết hạn sau <strong>10 phút</strong>.</p>
            </div>

            <p>Sau khi xác thực thành công, bạn sẽ có thể truy cập và sử dụng đầy đủ các tính năng của Barber Shop, bao gồm:</p>
            <ul>
                <li>Đặt lịch cắt tóc trực tuyến</li>
                <li>Xem lịch sử đặt lịch và dịch vụ</li>
                <li>Nhận thông báo về các khuyến mãi đặc biệt</li>
            </ul>

            <p>Nếu bạn không thực hiện yêu cầu đăng ký này, vui lòng bỏ qua email này hoặc liên hệ với chúng tôi nếu bạn có bất kỳ câu hỏi nào.</p>

            <div class="contact-info">
                <p><strong>Barber Shop</strong><br>
                123 Đường ABC, Quận XYZ, Thành phố HCM<br>
                Điện thoại: 0123456789<br>
                Email: info@barbershop.vn</p>
            </div>

            <p>Trân trọng,<br><strong>Đội ngũ Barber Shop</strong></p>
        </div>
        <div class="footer">
            <p>Email này được gửi tự động, vui lòng không trả lời.</p>
            <p>&copy; {{ date('Y') }} Barber Shop. Tất cả các quyền được bảo lưu.</p>
            <p>Bạn nhận được email này vì bạn đã đăng ký tài khoản trên hệ thống của chúng tôi.</p>
        </div>
    </div>
</body>
</html>
