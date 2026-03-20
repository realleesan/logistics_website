<?php
// Death Protection System Integration
require_once __DIR__ . '/death_integration.php';

// Lấy tên trang hiện tại để highlight menu active
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo isset($page_description) ? $page_description : 'VINA LOGISTICS - Dịch vụ vận chuyển hàng hóa uy tín, chuyên nghiệp từ Trung Quốc về Việt Nam'; ?>">
    <meta name="keywords" content="<?php echo isset($page_keywords) ? $page_keywords : getDynamicKeywords('general'); ?>">
    <meta name="author" content="VINA LOGISTICS">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo isset($page_title) ? $page_title : 'VINA LOGISTICS - Uy tín - Niềm tin'; ?>">
    <meta property="og:description" content="<?php echo isset($page_description) ? $page_description : 'Dịch vụ vận chuyển hàng hóa uy tín, chuyên nghiệp'; ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    <meta property="og:image" content="<?php echo asset_url('/logo.jpg'); ?>">
    
    <title><?php echo isset($page_title) ? $page_title . ' - VINA LOGISTICS' : 'VINA LOGISTICS - Uy tín - Niềm tin'; ?></title>
    <base href="<?php echo rtrim(defined('APP_URL') ? APP_URL : ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']), '/'); ?>/">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo asset_url('/logo.jpg'); ?>">
    
    <!-- CSS Files -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo asset_url('assets/css/style.css'); ?>" rel="stylesheet">
    
    <!-- Image Loading Script -->
    <script src="<?php echo asset_url('assets/js/image-loader.js'); ?>" defer></script>
    
    <!-- Search Script -->
    <script src="<?php echo asset_url('assets/js/search.js'); ?>" defer></script>
    
    <!-- Main Script -->
    <script src="<?php echo asset_url('assets/js/main.js'); ?>" defer></script>
    
    <!-- Additional CSS for specific pages -->
    <?php if (isset($additional_css)): ?>
        <?php foreach ($additional_css as $css): ?>
            <link href="<?php echo $css; ?>" rel="stylesheet">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Facebook SDK -->
    <div id="fb-root"></div>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v23.0"></script>
</head>
<body>
    <!-- Preloader with AUTO-HIDE timeout -->
    <div class="preloader" id="mainPreloader" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #fff; display: flex; align-items: center; justify-content: center; z-index: 9999;">
        <div style="text-align: center;">
            <div style="width: 50px; height: 50px; border: 4px solid #f3f3f3; border-top: 4px solid #c3f725; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 20px;"></div>
            <p style="color: #333; font-weight: 600;">Đang tải...</p>
        </div>
    </div>
    
    <!-- Auto-hide preloader using CSS animation (no JavaScript needed) -->
    <style>
        #mainPreloader {
            animation: preloaderTimeout 3s forwards;
        }
        @keyframes preloaderTimeout {
            0% { opacity: 1; visibility: visible; }
            95% { opacity: 1; visibility: visible; }
            100% { opacity: 0; visibility: hidden; display: none; }
        }
    </style>
    


    <style>
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    
    <!-- Auto-refresh mechanism to fix hanging issues -->
    <script>
    (function() {
        var loadStartTime = Date.now();
        var retryCount = 0;
        var maxRetries = 2;
        
        // Check if page is hanging after 10 seconds
        setTimeout(function() {
            var elapsed = Date.now() - loadStartTime;
            var preloader = document.querySelector('.preloader');
            
            // If preloader is still visible after 10 seconds, try to fix
            if (preloader && preloader.style.display !== 'none') {
                console.log('⚠️ Page loading slow, attempting auto-fix...');
                autoFixHanging();
            }
        }, 10000);
        
        function autoFixHanging() {
            if (retryCount >= maxRetries) {
                console.log('❌ Auto-fix failed after ' + maxRetries + ' attempts');
                return;
            }
            
            retryCount++;
            console.log('🔄 Auto-fix attempt #' + retryCount);
            
            // Fetch heartbeat to reset connection
            fetch('heartbeat.php?t=' + Date.now(), {
                method: 'GET',
                cache: 'no-cache',
                mode: 'cors'
            })
            .then(function(response) {
                if (response.ok) {
                    console.log('✅ Heartbeat OK, reloading page...');
                    // Small delay then reload
                    setTimeout(function() {
                        window.location.reload(true);
                    }, 1500);
                } else {
                    throw new Error('Heartbeat failed');
                }
            })
            .catch(function(err) {
                console.log('⚠️ Heartbeat error:', err.message);
                // Try again after delay
                setTimeout(function() {
                    autoFixHanging();
                }, 3000);
            });
        }
        
        // Also listen for page load errors
        window.addEventListener('error', function(e) {
            if (e.target.tagName === 'IMG' || e.target.tagName === 'SCRIPT') {
                console.log('⚠️ Resource load error:', e.target.src || e.target.href);
            }
        });
    })();
    </script>

    <!-- Top Contact Bar -->
    <div class="top-contact-bar">
        <div class="container">
            <div class="top-contact-content">
                <!-- Search Bar -->
                <div class="top-contact-left">
                    <form class="search-form" action="search.php" method="GET">
                        <div class="search-input-group">
                            <input type="text" 
                                   name="q" 
                                   placeholder="Tìm kiếm tin tức, dịch vụ..." 
                                   value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>"
                                   autocomplete="off"
                                   style="pointer-events: auto !important; z-index: 100 !important;"
                                   required>
                            <button type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Contact Info -->
                <div class="top-contact-right">
                    <div class="contact-item qr-payment" onclick="openQRModal()">
                        <i class="fas fa-qrcode"></i>
                        <span>QR Thanh toán</span>
                    </div>
                    <div class="contact-item phone" onclick="callPhone('<?php echo COMPANY_PHONE; ?>')">
                        <i class="fas fa-phone"></i>
                        <span><?php echo COMPANY_PHONE; ?></span>
                    </div>
                    <div class="contact-item email" onclick="sendEmail('<?php echo COMPANY_EMAIL; ?>')">
                        <i class="fas fa-envelope"></i>
                        <span><?php echo COMPANY_EMAIL; ?></span>
                    </div>
                    <div class="contact-item clock">
                        <i class="fas fa-clock"></i>
                        <span>8:00 - 17:00<br>Thứ 2 - Thứ 7</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <!-- Logo -->
                <div class="logo">
                    <a href="/">
                        <img src="<?php echo asset_url('/logo-removebg.png'); ?>" alt="VINA LOGISTICS Logo">
                        <span><?php echo COMPANY_SHORT_NAME; ?></span>
                    </a>
                </div>

                <!-- Main Navigation -->
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="/" class="nav-link <?php echo ($current_page == 'index') ? 'active' : ''; ?>">
                            <i class="fas fa-home"></i> Trang chủ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="gioi-thieu" class="nav-link <?php echo ($current_page == 'about') ? 'active' : ''; ?>">
                            <i class="fas fa-info-circle"></i> Giới thiệu
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="dich-vu" class="nav-link <?php echo ($current_page == 'services') ? 'active' : ''; ?>">
                            <i class="fas fa-truck"></i> Dịch vụ
                        </a>
                        <!-- Dropdown menu có thể thêm sau -->
                    </li>
                    <li class="nav-item">
                        <a href="tin-tuc" class="nav-link <?php echo ($current_page == 'news' || $current_page == 'news-detail') ? 'active' : ''; ?>">
                            <i class="fas fa-newspaper"></i> Tin tức
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="lien-he" class="nav-link <?php echo ($current_page == 'contact') ? 'active' : ''; ?>">
                            <i class="fas fa-envelope"></i> Liên hệ
                        </a>
                    </li>
                </ul>

                <!-- Mobile Menu Toggle -->
                <div class="mobile-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </nav>
        </div>
    </header>

    <!-- Top Info Bar (Optional - chỉ hiển thị trên trang chủ) -->
    <?php if ($current_page == 'index'): ?>
    <?php
    // Lấy danh sách tiêu đề tin tức cho thông báo động
    $newsNotifications = getNewsNotifications(10);
    ?>
    <div class="top-info-notification" style="background: var(--primary-color); color: var(--text-dark); padding: 8px 0; font-size: 14px;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <i class="fas fa-bullhorn"></i>
                    <strong>Thông báo:</strong> 
                    <span id="notification-text">
                        <?php echo !empty($newsNotifications) ? htmlspecialchars($newsNotifications[0]) : 'Chào mừng bạn đến với VINA LOGISTICS!'; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript cho hiệu ứng chuyển đổi thông báo -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const notifications = <?php echo json_encode($newsNotifications); ?>;
        const notificationElement = document.getElementById('notification-text');
        let currentIndex = 0;
        
        if (notifications && notifications.length > 1) {
            setInterval(function() {
                currentIndex = (currentIndex + 1) % notifications.length;
                
                // Fade out
                notificationElement.style.opacity = '0';
                notificationElement.style.transition = 'opacity 0.5s ease-in-out';
                
                setTimeout(function() {
                    // Change text
                    notificationElement.textContent = notifications[currentIndex];
                    
                    // Fade in
                    notificationElement.style.opacity = '1';
                }, 500);
                
            }, 5000); // Chuyển đổi mỗi 5 giây
        }
    });
    </script>
    <?php endif; ?>

    <!-- QR Modal -->
    <div class="modal-overlay" id="qrModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">QR Code Thanh Toán</h3>
                <button class="modal-close" onclick="closeQRModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <img src="<?php echo asset_url('/qr_code.jpg'); ?>" alt="QR Code Thanh Toán" class="qr-image" id="qrImage">
                <p class="qr-description">
                    Quét mã QR này để thanh toán nhanh chóng và an toàn.<br>
                    Hỗ trợ: VietQR, ZaloPay, Momo, VNPay và các ví điện tử khác.
                </p>
            </div>
        </div>
    </div>

    <!-- Contact Interaction Scripts -->
    <script>
        // QR Modal Functions
        function openQRModal() {
            const modal = document.getElementById('qrModal');
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeQRModal() {
            const modal = document.getElementById('qrModal');
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // Phone Call Function
        function callPhone(phoneNumber) {
            // Remove any non-digit characters
            const cleanNumber = phoneNumber.replace(/\D/g, '');
            window.open(`tel:${cleanNumber}`, '_self');
        }

        // Email Function
        function sendEmail(emailAddress) {
            window.open(`mailto:${emailAddress}?subject=Liên hệ từ website VINA LOGISTICS`, '_self');
        }

        // Close modal when clicking outside
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('qrModal');
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeQRModal();
                }
            });

            // Close modal with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeQRModal();
                }
            });
        });
    </script>

    <!-- Main Content Container -->
    <main class="main-content"><?php if ($current_page != 'index'): echo '<div class="content-spacer"></div>'; endif; ?> 