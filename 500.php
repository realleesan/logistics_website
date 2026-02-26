<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lỗi Server - VINA LOGISTICS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-align: center;
            max-width: 500px;
            width: 90%;
        }
        .error-code {
            font-size: 72px;
            font-weight: bold;
            color: #e74c3c;
            margin: 0;
        }
        .error-title {
            font-size: 24px;
            color: #2c3e50;
            margin: 20px 0;
        }
        .error-message {
            color: #7f8c8d;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 25px;
            display: inline-block;
            margin: 10px;
            transition: transform 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .contact-info {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        .contact-info h3 {
            color: #2c3e50;
            margin-bottom: 15px;
        }
        .contact-info p {
            margin: 5px 0;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">500</div>
        <h1 class="error-title">Lỗi Server</h1>
        <p class="error-message">
            Rất tiếc, đã xảy ra lỗi trên máy chủ. Chúng tôi đang khắc phục vấn đề này.
            Vui lòng thử lại sau vài phút.
        </p>
        
        <a href="/" class="btn">Về Trang Chủ</a>
        <a href="javascript:history.back()" class="btn">Quay Lại</a>
        
        <div class="contact-info">
            <h3>Liên hệ hỗ trợ</h3>
            <p>📞 Hotline: 0587.363636</p>
            <p>📧 Email: baominhkpkp@gmail.com</p>
            <p>🕐 Thời gian: 8:00 - 17:00 (Thứ 2 - Thứ 7)</p>
        </div>
    </div>
    
    <script>
        // Auto refresh after 30 seconds
        setTimeout(function() {
            window.location.reload();
        }, 30000);
        
        // Log error for debugging
        console.log('500 Error occurred at: ' + new Date().toISOString());
    </script>
</body>
</html> 