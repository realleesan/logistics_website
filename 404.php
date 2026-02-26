<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Không Tìm Thấy Trang - VINA LOGISTICS</title>
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
            color: #f39c12;
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
        .search-box {
            margin: 20px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        .search-input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 25px;
            font-size: 16px;
            margin-bottom: 10px;
        }
        .search-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
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
        <div class="error-code">404</div>
        <h1 class="error-title">Không Tìm Thấy Trang</h1>
        <p class="error-message">
            Trang bạn đang tìm kiếm không tồn tại hoặc đã được di chuyển.
            Vui lòng kiểm tra lại đường dẫn hoặc sử dụng tìm kiếm bên dưới.
        </p>
        
        <div class="search-box">
            <h3>Tìm kiếm nội dung</h3>
            <form action="/search.php" method="GET">
                <input type="text" name="q" placeholder="Nhập từ khóa tìm kiếm..." class="search-input" required>
                <button type="submit" class="search-btn">Tìm Kiếm</button>
            </form>
        </div>
        
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
        // Focus on search input
        document.querySelector('.search-input').focus();
        
        // Log 404 error for analytics
        console.log('404 Error occurred at: ' + new Date().toISOString() + ' for URL: ' + window.location.href);
    </script>
</body>
</html> 