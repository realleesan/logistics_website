<?php
/**
 * Cấu hình Production cho VINA LOGISTICS - InfinityFree Hosting
 * File này chứa thông tin cấu hình cho hosting thực tế
 */

 // ===========================================
// CẤU HÌNH DATABASE HOSTING - InfinityFree
// ===========================================

// Thông tin database hosting InfinityFree
define('PROD_DB_HOST', 'sql106.infinityfree.com');
define('PROD_DB_NAME', 'if0_41225297_truongvina');
define('PROD_DB_USER', 'if0_41225297');
define('PROD_DB_PASS', 'XxvGtV3rdx5S');

// ===========================================
// CẤU HÌNH EMAIL HOSTING
// ===========================================

// Sử dụng Gmail SMTP
define('PROD_SMTP_HOST', 'smtp.gmail.com');
define('PROD_SMTP_PORT', 587);
define('PROD_SMTP_USERNAME', 'baominhkpkp@gmail.com');
define('PROD_SMTP_PASSWORD', 'gjvz qdrq pogq sheb'); // App Password từ Gmail
define('PROD_FROM_EMAIL', 'baominhkpkp@gmail.com');
define('PROD_FROM_NAME', 'Trường VINA LOGISTICS');

// ===========================================
// CẤU HÌNH DOMAIN
// ===========================================

// Domain chính của website
define('PROD_DOMAIN', 'truongvinalogistics.com.vn');
define('PROD_BASE_URL', ''); // Để trống nếu website ở root domain

// ===========================================
// CẤU HÌNH BẢO MẬT
// ===========================================

// Admin credentials
define('PROD_ADMIN_USERNAME', 'admin');
define('PROD_ADMIN_PASSWORD', 'vinalogistics2024');

// ===========================================
// CẤU HÌNH SEO
// ===========================================

// Google Analytics ID (nếu có)
define('PROD_GA_ID', ''); // GA-XXXXXXXXX

// Facebook Pixel ID (nếu có)
define('PROD_FB_PIXEL_ID', ''); // XXXXXXXXXXX

?>
