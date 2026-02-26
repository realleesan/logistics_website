<?php
session_start();

// Include database config từ thư mục cha
require_once '../database/config.php';

// Admin authentication check
function requireAdminLogin() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: login.php');
        exit();
    }
}

// Check if user is admin
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Admin credentials (sử dụng từ .env)
define('ADMIN_USERNAME', $_ENV['ADMIN_USERNAME'] ?? 'admin');
define('ADMIN_PASSWORD', $_ENV['ADMIN_PASSWORD'] ?? 'vinalogistics2024');

// Admin page title prefix
define('ADMIN_PREFIX', 'Admin Panel - ');

// Upload directories (sử dụng từ .env)
// Tránh định nghĩa lại nếu đã có từ database/config.php và giữ đường dẫn thống nhất ở project root
if (!defined('UPLOAD_PATH')) {
    define('UPLOAD_PATH', $_ENV['UPLOAD_PATH'] ?? 'assets/images/');
}
if (!defined('UPLOAD_URL')) {
    define('UPLOAD_URL', $_ENV['UPLOAD_URL'] ?? 'assets/images/');
}

// Pagination (sử dụng từ .env)
define('ITEMS_PER_PAGE', (int)($_ENV['ITEMS_PER_PAGE'] ?? 10));

// Success/Error messages
function setMessage($message, $type = 'success') {
    $_SESSION['admin_message'] = $message;
    $_SESSION['admin_message_type'] = $type;
}

function getMessage() {
    if (isset($_SESSION['admin_message'])) {
        $message = $_SESSION['admin_message'];
        $type = $_SESSION['admin_message_type'] ?? 'success';
        unset($_SESSION['admin_message'], $_SESSION['admin_message_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

// Helper functions
function generateSlug($text) {
    // Chuyển đổi tiếng Việt không dấu
    $unicode = array(
        'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
        'd' => 'đ',
        'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
        'i' => 'í|ì|ỉ|ĩ|ị',
        'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
        'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
        'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
        'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
        'D' => 'Đ',
        'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
        'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
        'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
        'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
        'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
    );

    foreach($unicode as $nonUnicode => $uni) {
        $text = preg_replace("/($uni)/i", $nonUnicode, $text);
    }

    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s]/', '', $text);
    $text = preg_replace('/\s+/', '-', $text);
    $text = trim($text, '-');
    
    return $text;
}

function uploadImage($file, $prefix = '') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    // Ensure upload directory exists (absolute path at project root)
    $absoluteUploadDir = rtrim(dirname(__DIR__), '/\\') . '/' . trim(UPLOAD_PATH, '/\\');
    if (!is_dir($absoluteUploadDir)) {
        @mkdir($absoluteUploadDir, 0755, true);
    }

    // Detect MIME type using finfo (more reliable than $_FILES['type'])
    $finfo = function_exists('finfo_open') ? new finfo(FILEINFO_MIME_TYPE) : null;
    $detectedMime = $finfo ? $finfo->file($file['tmp_name']) : ($file['type'] ?? '');

    $allowedMimes = [
        'image/jpeg' => 'jpg',
        'image/jpg'  => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp'
    ];

    if (!array_key_exists($detectedMime, $allowedMimes)) {
        return false; // invalid format
    }

    // Size limit 5MB
    $maxSize = 5 * 1024 * 1024; // 5MB
    if ((int)$file['size'] > $maxSize) {
        return false;
    }

    // Determine extension from MIME to prevent spoofing
    $extension = $allowedMimes[$detectedMime];
    $filename = $prefix . uniqid('', true) . '.' . $extension;
    $uploadPath = rtrim($absoluteUploadDir, '/\\') . '/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        // Return relative public path used on frontend
        return 'assets/images/' . $filename;
    }

    return false;
}

// Get statistics for dashboard
function getDashboardStats() {
    global $pdo;
    
    $stats = [];
    
    // Total news
    $stmt = $pdo->query("SELECT COUNT(*) FROM news WHERE status = 'published'");
    $stats['total_news'] = $stmt->fetchColumn();
    
    // Total services
    $stmt = $pdo->query("SELECT COUNT(*) FROM services WHERE status = 'active'");
    $stats['total_services'] = $stmt->fetchColumn();
    
    // Total contacts
    $stmt = $pdo->query("SELECT COUNT(*) FROM contacts");
    $stats['total_contacts'] = $stmt->fetchColumn();
    
    // New contacts today
    $stmt = $pdo->query("SELECT COUNT(*) FROM contacts WHERE DATE(created_at) = CURDATE()");
    $stats['new_contacts'] = $stmt->fetchColumn();
    
    // Total categories
    $stmt = $pdo->query("SELECT COUNT(*) FROM news_categories WHERE status = 'active'");
    $stats['total_categories'] = $stmt->fetchColumn();
    
    // Recent news
    $stmt = $pdo->query("SELECT * FROM news ORDER BY created_at DESC LIMIT 5");
    $stats['recent_news'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Recent contacts
    $stmt = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC LIMIT 5");
    $stats['recent_contacts'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return $stats;
}
?> 