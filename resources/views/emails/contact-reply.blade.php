<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Phản hồi từ Barber Shop</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
        }
        .header {
            background-color: #333;
            color: #fff;
            padding: 10px 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
            margin: -20px -20px 20px;
        }
        .content {
            padding: 20px 0;
        }
        .message-container {
            background-color: #f9f9f9;
            border-left: 3px solid #333;
            padding: 15px;
            margin: 15px 0;
        }
        .reply-container {
            background-color: #f0f7ff;
            border-left: 3px solid #0066cc;
            padding: 15px;
            margin: 15px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Barber Shop - Phản hồi liên hệ</h1>
        </div>
        <div class="content">
            <h2>Xin chào {{ $contact->name }},</h2>
            <p>Cảm ơn bạn đã liên hệ với Barber Shop. Chúng tôi đã nhận được tin nhắn của bạn và xin gửi phản hồi như sau:</p>

            <div class="message-container">
                <p><strong>Tiêu đề:</strong> {{ $contact->subject }}</p>
                <p><strong>Tin nhắn của bạn:</strong></p>
                <p>{{ $contact->message }}</p>
                <p><strong>Ngày gửi:</strong> {{ $contact->created_at->format('d/m/Y H:i') }}</p>
            </div>

            <div class="reply-container">
                <p><strong>Phản hồi của chúng tôi:</strong></p>
                <p>{!! nl2br(e($contact->reply)) !!}</p>
            </div>

            <p>Nếu bạn có bất kỳ câu hỏi nào khác, vui lòng liên hệ với chúng tôi qua email hoặc số điện thoại dưới đây.</p>

            <div class="contact-info">
                <p><strong>Barber Shop</strong><br>
                Quốc lộ 1A, Diên Toàn, Diên Khánh, Khánh Hòa<br>
                Điện thoại: 0559764554<br>
                Email: hieu.ht.63cntt@ntu.edu.vn</p>
            </div>

            <p>Trân trọng,<br><strong>Đội ngũ Barber Shop</strong></p>
        </div>
        <div class="footer">
            <p>Email này được gửi tự động, vui lòng không trả lời.</p>
            <p>&copy; {{ date('Y') }} Barber Shop. Tất cả các quyền được bảo lưu.</p>
        </div>
    </div>
</body>
</html>
