<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รหัส OTP - JJ Apartment</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #4A90E2;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        .content p {
            color: #666;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .otp-code {
            background-color: #f8f9fa;
            border: 2px dashed #4A90E2;
            border-radius: 10px;
            padding: 20px;
            margin: 30px 0;
        }
        .otp-code h2 {
            color: #4A90E2;
            font-size: 36px;
            letter-spacing: 8px;
            margin: 0;
            font-family: monospace;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            text-align: left;
            border-radius: 4px;
        }
        .warning p {
            color: #856404;
            font-size: 14px;
            margin: 0;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #eee;
        }
        .footer p {
            color: #999;
            font-size: 12px;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>JJ Apartment</h1>
        </div>

        <div class="content">
            <p>สวัสดีคุณ {{ $user->username ?? 'ผู้ใช้' }},</p>
            <p>เราได้รับคำขอรีเซ็ตรหัสผ่านสำหรับบัญชีของคุณ กรุณาใช้รหัส OTP ด้านล่างนี้เพื่อยืนยันตัวตน:</p>

            <div class="otp-code">
                <h2>{{ $otp }}</h2>
            </div>

            <p>รหัส OTP นี้จะหมดอายุภายใน <strong>10 นาที</strong></p>

            <div class="warning">
                <p><strong>ข้อควรระวัง:</strong> อย่าแชร์รหัสนี้กับผู้อื่น หากคุณไม่ได้ขอรีเซ็ตรหัสผ่าน กรุณาเพิกเฉยอีเมลฉบับนี้</p>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} JJ Apartment Management System</p>
            <p>อีเมลนี้ถูกส่งโดยอัตโนมัติ กรุณาอย่าตอบกลับ</p>
        </div>
    </div>
</body>
</html>
