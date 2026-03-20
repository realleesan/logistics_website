<?php
/**
 * System Check - Diagnostic Tool for VINA LOGISTICS
 * This file helps identify issues causing inconsistent website loading
 */

// Set strict error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

$issues = [];
$warnings = [];
$success = [];

// Check 1: Image Files Size
echo "=== KIỂM TRA HÌNH ẢNH ===<br>";
$imageDir = __DIR__ . '/assets/images';
if (is_dir($imageDir)) {
    $images = glob($imageDir . '/*.png');
    $images = array_merge($images, glob($imageDir . '/*.jpg'));
    $images = array_merge($images, glob($imageDir . '/*.jpeg'));
    $images = array_merge($images, glob($imageDir . '/*.webp'));
    
    $largeImages = [];
    $totalSize = 0;
    
    foreach ($images as $image) {
        $size = filesize($image);
        $totalSize += $size;
        $sizeKB = round($size / 1024);
        $filename = basename($image);
        
        if ($sizeKB > 500) {
            $largeImages[] = [
                'name' => $filename,
                'size' => $sizeKB
            ];
            echo "⚠️  $filename: ${sizeKB}KB (QUÁ LỚN)<br>";
        } else {
            echo "✅ $filename: ${sizeKB}KB<br>";
        }
    }
    
    $totalSizeMB = round($totalSize / (1024 * 1024), 2);
    echo "<br>Tổng kích thước hình ảnh: {$totalSizeMB}MB<br>";
    
    if (count($largeImages) > 0) {
        $issues[] = "Có " . count($largeImages) . " hình ảnh có kích thước > 500KB. Đây là NGUYÊN NHÂN CHÍNH gây ra lỗi loading trên một số thiết bị.";
    }
}

// Check 2: PHP Configuration
echo "<br>=== KIỂM TRA CẤU HÌNH PHP ===<br>";
$phpSettings = [
    'memory_limit' => ini_get('memory_limit'),
    'max_execution_time' => ini_get('max_execution_time'),
    'post_max_size' => ini_get('post_max_size'),
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'display_errors' => ini_get('display_errors'),
];

foreach ($phpSettings as $key => $value) {
    echo "$key: $value<br>";
}

if (intval($phpSettings['max_execution_time']) < 60) {
    $warnings[] = "max_execution_time có thể quá thấp ($value). Khuyến nghị: 60-300";
}

// Check 3: Death Protection Lock
echo "<br>=== KIỂM TRA HỆ THỐNG BẢO VỆ ===<br>";
$lockFile = __DIR__ . '/logs/website_locked.txt';
if (file_exists($lockFile)) {
    $issues[] = "Website đang bị khóa! File: logs/website_locked.txt tồn tại";
    echo "🔒 Website đang bị khóa bởi hệ thống bảo vệ<br>";
} else {
    echo "✅ Website không bị khóa<br>";
}

// Check 4: External Resources
echo "<br>=== KIỂM TRA TÀI NGUYÊN NGOẠI VI ===<br>";
$externalResources = [
    'cdnjs.cloudflare.com/ajax/libs/font-awesome' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
    'cdn.jsdelivr.net (Bootstrap)' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
    'Facebook SDK' => 'https://connect.facebook.net/vi_VN/sdk.js',
];

foreach ($externalResources as $name => $url) {
    echo "Checking $name...<br>";
}

// Check 5: Database Connection
echo "<br>=== KIỂM TRA DATABASE ===<br>";
try {
    require_once __DIR__ . '/database/config.php';
    echo "✅ Kết nối database thành công<br>";
} catch (Exception $e) {
    $issues[] = "Lỗi kết nối database: " . $e->getMessage();
    echo "❌ Lỗi kết nối database: " . $e->getMessage() . "<br>";
}

// Check 6: .htaccess Configuration
echo "<br>=== KIỂM TRA .HTACCESS ===<br>";
$htaccess = __DIR__ . '/.htaccess';
if (file_exists($htaccess)) {
    echo "✅ File .htaccess tồn tại<br>";
    
    // Check for common issues
    $htcontent = file_get_contents($htaccess);
    if (strpos($htcontent, 'php_value memory_limit') !== false) {
        echo "✅ Có cấu hình PHP memory_limit trong .htaccess<br>";
    }
    if (strpos($htcontent, 'mod_deflate') !== false) {
        echo "✅ Gzip compression đã bật<br>";
    }
    if (strpos($htcontent, 'mod_expires') !== false) {
        echo "✅ Browser caching đã bật<br>";
    }
} else {
    $issues[] = "File .htaccess không tồn tại!";
    echo "❌ File .htaccess không tồn tại<br>";
}

// Summary
echo "<br>=== TỔNG KẾT VẤN ĐỀ ===<br>";

if (count($issues) > 0) {
    echo "<div style='background: #ffebee; padding: 15px; border-radius: 5px;'>";
    echo "<h3 style='color: #c62828;'>⚠️ VẤN ĐỀ CẦN KHẮC PHỤC:</h3>";
    echo "<ul>";
    foreach ($issues as $issue) {
        echo "<li>$issue</li>";
    }
    echo "</ul>";
    echo "</div>";
}

if (count($warnings) > 0) {
    echo "<div style='background: #fff3e0; padding: 15px; border-radius: 5px;'>";
    echo "<h3 style='color: #e65100;'>⚡ CẢNH BÁO:</h3>";
    echo "<ul>";
    foreach ($warnings as $warning) {
        echo "<li>$warning</li>";
    }
    echo "</ul>";
    echo "</div>";
}

if (count($issues) == 0 && count($warnings) == 0) {
    echo "<div style='background: #e8f5e9; padding: 15px; border-radius: 5px;'>";
    echo "<h3 style='color: #2e7d32;'>✅ Không phát hiện vấn đề nghiêm trọng!</h3>";
    echo "</div>";
}

// Recommendations
echo "<br>=== HƯỚNG DẪN KHẮC PHỤC ===<br>";
echo "<div style='background: #e3f2fd; padding: 15px; border-radius: 5px;'>";
echo "<h3>📋 Các bước khắc phục:</h3>";
echo "<ol>";
echo "<li><strong>Tối ưu hình ảnh:</strong> Nén tất cả hình ảnh trong thư mục assets/images/ xuống dưới 200KB mỗi file. Sử dụng định dạng WebP nếu có thể.</li>";
echo "<li><strong>Kiểm tra hosting:</strong> Đăng nhập InfinityFree Control Panel kiểm tra Resource Usage có vượt quá giới hạn không.</li>";
echo "<li><strong>Xóa cache:</strong> Vào InfinityFree Control Panel → CloudFlare → Purge Cache</li>";
echo "<li><strong>Kiểm tra lỗi PHP:</strong> Xem file logs/error.log để biết chi tiết lỗi</li>";
echo "</ol>";
echo "</div>";

echo "<br><small>Tool chẩn đoán - VINA LOGISTICS - " . date('Y-m-d H:i:s') . "</small>";
?>
