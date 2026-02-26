<?php
require_once 'config.php';

// Nếu đã đăng nhập, chuyển hướng về dashboard
if (isAdminLoggedIn()) {
    header('Location: index.php');
    exit();
}

$error = '';

// Xử lý đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        $_SESSION['admin_login_time'] = time();
        
        header('Location: index.php');
        exit();
    } else {
        $error = 'Tên đăng nhập hoặc mật khẩu không chính xác!';
    }
}

$page_title = 'Đăng nhập Admin';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Vina Logistics</title>
    
    <!-- CSS Files -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #8bc34a;
            --primary-dark: #689f38;
            --text-dark: #2c3e50;
            --bg-light: #f8f9fa;
        }
        
        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Poppins', sans-serif;
        }
        
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 900px;
            margin: 0 auto;
        }
        
        .login-left {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            padding: 60px 40px;
            color: white;
            text-align: center;
        }
        
        .login-right {
            padding: 60px 40px;
        }
        
        .logo-section img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 20px;
        }
        
        .form-control {
            padding: 15px 20px;
            border-radius: 10px;
            border: 2px solid #e9ecef;
            font-size: 16px;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(139, 195, 74, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border: none;
            padding: 15px 30px;
            border-radius: 10px;
            color: var(--text-dark);
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(139, 195, 74, 0.3);
            color: var(--text-dark);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .input-group-text {
            background: var(--bg-light);
            border: 2px solid #e9ecef;
            border-right: none;
            border-radius: 10px 0 0 10px;
        }
        
        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
        
        .input-group:focus-within .input-group-text {
            border-color: var(--primary-color);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container row no-gutters">
            <!-- Left side - Branding -->
            <div class="col-md-6 login-left d-flex flex-column justify-content-center">
                <div class="logo-section">
                    <img src="../logo.jpg" alt="Vina Logistics Logo">
                    <h2 class="mb-3 font-weight-bold">Vina Logistics</h2>
                    <h4 class="mb-4">Admin Panel</h4>
                </div>
                
                <div class="mb-4">
                    <h5><i class="fas fa-shield-alt me-2"></i>Hệ thống quản trị</h5>
                    <p class="mb-0">Quản lý nội dung website một cách dễ dàng và hiệu quả</p>
                </div>
                
                <div class="feature-list">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-newspaper me-3 fa-lg"></i>
                        <span>Quản lý tin tức & bài viết</span>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-cogs me-3 fa-lg"></i>
                        <span>Quản lý dịch vụ</span>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-envelope me-3 fa-lg"></i>
                        <span>Quản lý liên hệ</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-chart-bar me-3 fa-lg"></i>
                        <span>Thống kê & báo cáo</span>
                    </div>
                </div>
            </div>
            
            <!-- Right side - Login form -->
            <div class="col-md-6 login-right">
                <div class="text-center mb-4">
                    <h3 class="text-dark">Đăng nhập</h3>
                    <p class="text-muted">Nhập thông tin để truy cập hệ thống</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-4">
                        <label for="username" class="form-label">Tên đăng nhập</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text" class="form-control" id="username" name="username" 
                                   placeholder="Nhập tên đăng nhập" required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Nhập mật khẩu" required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember">
                            <label class="form-check-label" for="remember">
                                Ghi nhớ đăng nhập
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Đăng nhập
                    </button>
                </form>
                
                <div class="text-center mt-4">
                    <small class="text-muted">
                        <i class="fas fa-lock me-1"></i>
                        Kết nối được bảo mật bằng SSL
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 