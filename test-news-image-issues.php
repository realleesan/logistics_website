<?php
/**
 * Test file để kiểm tra các vấn đề về ảnh tin tức
 * Chạy file này để xác định nguyên nhân của các lỗi:
 * 1. Admin panel hiển thị "Current image" thay vì ảnh thực tế
 * 2. Frontend hiển thị ảnh sai vị trí
 */

// Include database config
require_once 'database/config.php';
require_once 'includes/functions.php';

echo "<h1>Test Kiểm Tra Vấn Đề Ảnh Tin Tức</h1>\n";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
    .success { background-color: #d4edda; border-color: #c3e6cb; }
    .error { background-color: #f8d7da; border-color: #f5c6cb; }
    .warning { background-color: #fff3cd; border-color: #ffeaa7; }
    .info { background-color: #d1ecf1; border-color: #bee5eb; }
    pre { background: #f8f9fa; padding: 10px; border-radius: 3px; overflow-x: auto; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>\n";

// Test 1: Kiểm tra cấu hình upload
echo "<div class='test-section info'>\n";
echo "<h2>1. Kiểm Tra Cấu Hình Upload</h2>\n";

// Kiểm tra UPLOAD_PATH
if (defined('UPLOAD_PATH')) {
    echo "<p><strong>UPLOAD_PATH:</strong> " . UPLOAD_PATH . "</p>\n";
    echo "<p><strong>UPLOAD_PATH tồn tại:</strong> " . (is_dir(UPLOAD_PATH) ? 'CÓ' : 'KHÔNG') . "</p>\n";
    if (is_dir(UPLOAD_PATH)) {
        echo "<p><strong>UPLOAD_PATH có thể ghi:</strong> " . (is_writable(UPLOAD_PATH) ? 'CÓ' : 'KHÔNG') . "</p>\n";
    }
} else {
    echo "<p class='error'>UPLOAD_PATH không được định nghĩa!</p>\n";
}

// Kiểm tra thư mục assets/images
$assetsPath = 'assets/images/';
echo "<p><strong>Assets path:</strong> " . $assetsPath . "</p>\n";
echo "<p><strong>Assets path tồn tại:</strong> " . (is_dir($assetsPath) ? 'CÓ' : 'KHÔNG') . "</p>\n";
if (is_dir($assetsPath)) {
    echo "<p><strong>Assets path có thể ghi:</strong> " . (is_writable($assetsPath) ? 'CÓ' : 'KHÔNG') . "</p>\n";
}

echo "</div>\n";

// Test 2: Kiểm tra dữ liệu tin tức trong database
echo "<div class='test-section info'>\n";
echo "<h2>2. Kiểm Tra Dữ Liệu Tin Tức Trong Database</h2>\n";

try {
    $stmt = $pdo->query("SELECT id, title, image, featured, status, created_at FROM news ORDER BY created_at DESC LIMIT 10");
    $news = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($news)) {
        echo "<table>\n";
        echo "<tr><th>ID</th><th>Tiêu đề</th><th>Ảnh</th><th>Nổi bật</th><th>Trạng thái</th><th>Ngày tạo</th></tr>\n";
        
        foreach ($news as $article) {
            $imageExists = !empty($article['image']) && file_exists($article['image']);
            $imageStatus = $imageExists ? 'CÓ' : 'KHÔNG';
            $imageClass = $imageExists ? 'success' : 'error';
            
            echo "<tr>\n";
            echo "<td>{$article['id']}</td>\n";
            echo "<td>" . htmlspecialchars($article['title']) . "</td>\n";
            echo "<td class='{$imageClass}'>{$article['image']} ({$imageStatus})</td>\n";
            echo "<td>" . ($article['featured'] ? 'CÓ' : 'KHÔNG') . "</td>\n";
            echo "<td>{$article['status']}</td>\n";
            echo "<td>{$article['created_at']}</td>\n";
            echo "</tr>\n";
        }
        echo "</table>\n";
    } else {
        echo "<p class='warning'>Không có tin tức nào trong database!</p>\n";
    }
} catch (PDOException $e) {
    echo "<p class='error'>Lỗi database: " . $e->getMessage() . "</p>\n";
}

echo "</div>\n";

// Test 3: Kiểm tra function uploadImage
echo "<div class='test-section info'>\n";
echo "<h2>3. Kiểm Tra Function uploadImage</h2>\n";

// Kiểm tra function có tồn tại không
if (function_exists('uploadImage')) {
    echo "<p class='success'>Function uploadImage tồn tại</p>\n";
    
    // Test với file giả
    $testFile = [
        'name' => 'test.jpg',
        'type' => 'image/jpeg',
        'tmp_name' => '/tmp/test',
        'error' => UPLOAD_ERR_OK,
        'size' => 1024
    ];
    
    $result = uploadImage($testFile, 'test_');
    echo "<p><strong>Test uploadImage với file giả:</strong> " . ($result ? $result : 'FALSE') . "</p>\n";
} else {
    echo "<p class='error'>Function uploadImage không tồn tại!</p>\n";
}

echo "</div>\n";

// Test 4: Kiểm tra function asset_url
echo "<div class='test-section info'>\n";
echo "<h2>4. Kiểm Tra Function asset_url</h2>\n";

if (function_exists('asset_url')) {
    echo "<p class='success'>Function asset_url tồn tại</p>\n";
    
    // Test các trường hợp
    $testCases = [
        'assets/images/test.jpg',
        'test.jpg',
        '/assets/images/test.jpg',
        'assets/css/style.css',
        'assets/js/main.js'
    ];
    
    echo "<table>\n";
    echo "<tr><th>Input</th><th>Output</th></tr>\n";
    
    foreach ($testCases as $testCase) {
        $result = asset_url($testCase);
        echo "<tr><td>{$testCase}</td><td>{$result}</td></tr>\n";
    }
    echo "</table>\n";
} else {
    echo "<p class='error'>Function asset_url không tồn tại!</p>\n";
}

echo "</div>\n";

// Test 5: Kiểm tra logic hiển thị ảnh trong frontend
echo "<div class='test-section info'>\n";
echo "<h2>5. Kiểm Tra Logic Hiển Thị Ảnh Frontend</h2>\n";

try {
    // Lấy tin tức nổi bật cho homepage
    $featured_stmt = $pdo->query("
        SELECT n.*, nc.name as category_name 
        FROM news n 
        LEFT JOIN news_categories nc ON n.category_id = nc.id 
        WHERE n.status = 'published' AND n.featured = 1 
        ORDER BY n.created_at DESC 
        LIMIT 3
    ");
    $featured_news = $featured_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Tin tức nổi bật (Homepage):</h3>\n";
    if (!empty($featured_news)) {
        echo "<table>\n";
        echo "<tr><th>ID</th><th>Tiêu đề</th><th>Ảnh gốc</th><th>Ảnh hiển thị</th><th>File tồn tại</th></tr>\n";
        
        foreach ($featured_news as $article) {
            $originalImage = $article['image'];
            $candidateImage = !empty($originalImage) ? $originalImage : '';
            $serverPath = $candidateImage ? __DIR__ . '/' . ltrim($candidateImage, '/\\') : '';
            $resolvedImage = ($candidateImage && file_exists($serverPath)) ? $candidateImage : 'assets/images/index9.png';
            $fileExists = file_exists($serverPath);
            
            echo "<tr>\n";
            echo "<td>{$article['id']}</td>\n";
            echo "<td>" . htmlspecialchars($article['title']) . "</td>\n";
            echo "<td>{$originalImage}</td>\n";
            echo "<td>{$resolvedImage}</td>\n";
            echo "<td class='" . ($fileExists ? 'success' : 'error') . "'>" . ($fileExists ? 'CÓ' : 'KHÔNG') . "</td>\n";
            echo "</tr>\n";
        }
        echo "</table>\n";
    } else {
        echo "<p class='warning'>Không có tin tức nổi bật!</p>\n";
    }
    
    // Lấy tin tức thường cho trang tin tức
    $regular_stmt = $pdo->query("
        SELECT n.*, nc.name as category_name 
        FROM news n 
        LEFT JOIN news_categories nc ON n.category_id = nc.id 
        WHERE n.status = 'published'
        ORDER BY n.created_at DESC 
        LIMIT 5
    ");
    $regular_news = $regular_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Tin tức thường (Trang tin tức):</h3>\n";
    if (!empty($regular_news)) {
        echo "<table>\n";
        echo "<tr><th>ID</th><th>Tiêu đề</th><th>Ảnh gốc</th><th>Ảnh hiển thị</th><th>File tồn tại</th></tr>\n";
        
        foreach ($regular_news as $article) {
            $originalImage = $article['image'];
            $candidateImage = !empty($originalImage) ? $originalImage : '';
            $serverPath = $candidateImage ? __DIR__ . '/' . ltrim($candidateImage, '/\\') : '';
            $resolvedImage = ($candidateImage && file_exists($serverPath)) ? $candidateImage : 'assets/images/index9.png';
            $fileExists = file_exists($serverPath);
            
            echo "<tr>\n";
            echo "<td>{$article['id']}</td>\n";
            echo "<td>" . htmlspecialchars($article['title']) . "</td>\n";
            echo "<td>{$originalImage}</td>\n";
            echo "<td>{$resolvedImage}</td>\n";
            echo "<td class='" . ($fileExists ? 'success' : 'error') . "'>" . ($fileExists ? 'CÓ' : 'KHÔNG') . "</td>\n";
            echo "</tr>\n";
        }
        echo "</table>\n";
    } else {
        echo "<p class='warning'>Không có tin tức thường!</p>\n";
    }
    
} catch (PDOException $e) {
    echo "<p class='error'>Lỗi database: " . $e->getMessage() . "</p>\n";
}

echo "</div>\n";

// Test 6: Kiểm tra các file ảnh mặc định
echo "<div class='test-section info'>\n";
echo "<h2>6. Kiểm Tra File Ảnh Mặc Định</h2>\n";

$defaultImages = [
    'assets/images/index9.png',
    'assets/images/news-default.jpg',
    'assets/images/index1.png',
    'assets/images/index2.png',
    'assets/images/index3.png'
];

echo "<table>\n";
echo "<tr><th>File</th><th>Tồn tại</th><th>Kích thước</th></tr>\n";

foreach ($defaultImages as $image) {
    $exists = file_exists($image);
    $size = $exists ? filesize($image) : 0;
    $sizeFormatted = $exists ? number_format($size) . ' bytes' : 'N/A';
    
    echo "<tr>\n";
    echo "<td>{$image}</td>\n";
    echo "<td class='" . ($exists ? 'success' : 'error') . "'>" . ($exists ? 'CÓ' : 'KHÔNG') . "</td>\n";
    echo "<td>{$sizeFormatted}</td>\n";
    echo "</tr>\n";
}
echo "</table>\n";

echo "</div>\n";

// Test 7: Kiểm tra quyền truy cập file
echo "<div class='test-section info'>\n";
echo "<h2>7. Kiểm Tra Quyền Truy Cập File</h2>\n";

$testFiles = [
    'assets/images/',
    'admin/config.php',
    'includes/functions.php',
    'database/config.php'
];

echo "<table>\n";
echo "<tr><th>File/Thư mục</th><th>Tồn tại</th><th>Đọc được</th><th>Ghi được</th></tr>\n";

foreach ($testFiles as $file) {
    $exists = file_exists($file);
    $readable = $exists ? is_readable($file) : false;
    $writable = $exists ? is_writable($file) : false;
    
    echo "<tr>\n";
    echo "<td>{$file}</td>\n";
    echo "<td class='" . ($exists ? 'success' : 'error') . "'>" . ($exists ? 'CÓ' : 'KHÔNG') . "</td>\n";
    echo "<td class='" . ($readable ? 'success' : 'error') . "'>" . ($readable ? 'CÓ' : 'KHÔNG') . "</td>\n";
    echo "<td class='" . ($writable ? 'success' : 'error') . "'>" . ($writable ? 'CÓ' : 'KHÔNG') . "</td>\n";
    echo "</tr>\n";
}
echo "</table>\n";

echo "</div>\n";

// Test 8: Kiểm tra cấu hình database
echo "<div class='test-section info'>\n";
echo "<h2>8. Kiểm Tra Cấu Hình Database</h2>\n";

try {
    // Kiểm tra bảng news
    $stmt = $pdo->query("DESCRIBE news");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Cấu trúc bảng news:</h3>\n";
    echo "<table>\n";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>\n";
    
    foreach ($columns as $column) {
        echo "<tr>\n";
        echo "<td>{$column['Field']}</td>\n";
        echo "<td>{$column['Type']}</td>\n";
        echo "<td>{$column['Null']}</td>\n";
        echo "<td>{$column['Key']}</td>\n";
        echo "<td>{$column['Default']}</td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";
    
    // Kiểm tra số lượng tin tức
    $count_stmt = $pdo->query("SELECT COUNT(*) as total FROM news");
    $total = $count_stmt->fetchColumn();
    echo "<p><strong>Tổng số tin tức:</strong> {$total}</p>\n";
    
    $published_stmt = $pdo->query("SELECT COUNT(*) as published FROM news WHERE status = 'published'");
    $published = $published_stmt->fetchColumn();
    echo "<p><strong>Tin tức đã xuất bản:</strong> {$published}</p>\n";
    
    $featured_stmt = $pdo->query("SELECT COUNT(*) as featured FROM news WHERE featured = 1");
    $featured = $featured_stmt->fetchColumn();
    echo "<p><strong>Tin tức nổi bật:</strong> {$featured}</p>\n";
    
} catch (PDOException $e) {
    echo "<p class='error'>Lỗi database: " . $e->getMessage() . "</p>\n";
}

echo "</div>\n";

// Kết luận
echo "<div class='test-section'>\n";
echo "<h2>Kết Luận</h2>\n";
echo "<p>File test này đã kiểm tra:</p>\n";
echo "<ul>\n";
echo "<li>Cấu hình upload và quyền truy cập</li>\n";
echo "<li>Dữ liệu tin tức trong database</li>\n";
echo "<li>Function uploadImage và asset_url</li>\n";
echo "<li>Logic hiển thị ảnh trong frontend</li>\n";
echo "<li>File ảnh mặc định</li>\n";
echo "<li>Quyền truy cập file</li>\n";
echo "<li>Cấu hình database</li>\n";
echo "</ul>\n";
echo "<p><strong>Hãy chạy file này và gửi kết quả để tôi có thể phân tích nguyên nhân chính xác của vấn đề.</strong></p>\n";
echo "</div>\n";

?>
