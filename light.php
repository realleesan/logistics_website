<?php
/**
 * Lightweight fallback page - Loads quickly even when main site hangs
 * This page serves as a backup when the main index.php is stuck
 * 
 * Usage: If main site hangs, redirect here
 * Can be used as meta refresh fallback
 */

// Minimal setup - no heavy includes
error_reporting(0);
ini_set('display_errors', 0);

// Quick DB check with very short timeout
$dbOk = false;
try {
    $pdo = new PDO("mysql:host=localhost;dbname=tru98691_db;charset=utf8", "tru98691_db", "21042005nhat");
    $pdo->setAttribute(PDO::ATTR_TIMEOUT, 3); // 3 second timeout
    $pdo->query("SELECT 1");
    $dbOk = true;
} catch (Exception $e) {
    $dbOk = false;
}

$services = [];
$news = [];

// If DB is fast, get content
if ($dbOk) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM services WHERE status = 'active' ORDER BY sort_order ASC LIMIT 3");
        $stmt->execute();
        $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stmt2 = $pdo->prepare("SELECT * FROM news WHERE status = 'published' ORDER BY created_at DESC LIMIT 2");
        $stmt2->execute();
        $news = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Silently fail - show minimal content
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VINA LOGISTICS - Trang chủ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; }
        .hero { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 60px 0; }
        .service-card { border: 1px solid #ddd; border-radius: 10px; padding: 20px; margin-bottom: 20px; }
        .footer { background: #333; color: white; padding: 30px 0; margin-top: 50px; }
    </style>
</head>
<body>
    <!-- Simple Header -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: #333;">
        <div class="container">
            <a class="navbar-brand" href="/">VINA LOGISTICS</a>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero">
        <div class="container text-center">
            <h1>VINA LOGISTICS</h1>
            <p>Dịch vụ vận chuyển hàng hóa uy tín từ Trung Quốc về Việt Nam</p>
            <a href="/lien-he" class="btn btn-light mt-3">Liên hệ ngay</a>
        </div>
    </section>

    <!-- Services -->
    <div class="container my-5">
        <h2 class="text-center mb-4">Dịch vụ của chúng tôi</h2>
        <div class="row">
            <?php if (count($services) > 0): ?>
                <?php foreach ($services as $service): ?>
                    <div class="col-md-4">
                        <div class="service-card">
                            <h5><?php echo htmlspecialchars($service['title'] ?? 'Dịch vụ'); ?></h5>
                            <p><?php echo htmlspecialchars(substr($service['description'] ?? '', 0, 100)); ?>...</p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p>📦 Vận chuyển hàng hóa</p>
                    <p>🏭 Nhập khẩu ủy thác</p>
                    <p>🛒 Order Taobao, 1688</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Contact -->
    <div class="bg-light py-5">
        <div class="container text-center">
            <h3>Liên hệ với chúng tôi</h3>
            <p>📞 0587.363636</p>
            <p>📧 baominhkpkp@gmail.com</p>
            <p>📍 Số nhà 28 phố Lê Trọng Tấn, Hà Đông, Hà Nội</p>
            <a href="/lien-he" class="btn btn-primary">Gửi liên hệ</a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer text-center">
        <div class="container">
            <p>&copy; 2024 VINA LOGISTICS. All rights reserved.</p>
            <p><small>Trang tải nhanh - Phiên bản dự phòng</small></p>
        </div>
    </footer>

    <!-- Auto-redirect to main site after 10 seconds -->
    <script>
        setTimeout(function() {
            // Try to load main site
            var img = new Image();
            img.onload = function() {
                window.location.href = '/?from_light=1';
            };
            img.onerror = function() {
                console.log('Main site still unavailable');
            };
            img.src = '/assets/images/index9.png?t=' + Date.now();
        }, 10000);
    </script>
</body>
</html>
