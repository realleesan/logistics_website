<?php
// Death Protection System - Status Checker
// File này để kiểm tra trạng thái website từ bên ngoài

header('Content-Type: application/json');

// Kiểm tra trạng thái khóa
function isWebsiteLocked() {
    return file_exists('../logs/website_locked.txt');
}

// Kiểm tra log file
function getLogInfo() {
    $log_file = '../logs/death_protection.log';
    if (file_exists($log_file)) {
        $lines = file($log_file);
        $recent_lines = array_slice($lines, -5);
        return $recent_lines;
    }
    return [];
}

// Kiểm tra file bị disable
function getDisabledFiles() {
    $files_to_check = [
        '../admin/bulk_upload.php',
        '../admin/delete_product_image.php',
        '../admin/update_prices.php',
        '../admin/settings.php',
        '../admin/categories.php',
        '../admin/materials.php',
        '../admin/tags.php'
    ];
    
    $disabled_files = [];
    foreach ($files_to_check as $file) {
        if (file_exists($file . '.disabled')) {
            $disabled_files[] = basename($file);
        }
    }
    
    return $disabled_files;
}

$response = [
    'timestamp' => date('Y-m-d H:i:s'),
    'website_locked' => isWebsiteLocked(),
    'lock_time' => isWebsiteLocked() ? file_get_contents('../logs/website_locked.txt') : null,
    'disabled_files' => getDisabledFiles(),
    'recent_logs' => getLogInfo()
];

echo json_encode($response, JSON_PRETTY_PRINT);
?> 