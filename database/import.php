<?php
/*
 * Database Import Script cho Vina Logistics
 * Script này sẽ import toàn bộ database structure và sample data
 */

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'vina_logistics';

try {
    // Kết nối đến MySQL server
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>🚀 VINA LOGISTICS - DATABASE IMPORT</h2>";
    echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0; border-left: 4px solid #8bc34a;'>";
    echo "<h3>✅ Đã thêm dữ liệu tin tức mới:</h3>";
    echo "<ul>";
    echo "<li>📰 12 bài tin tức đa dạng (thay vì 4 bài cũ)</li>";
    echo "<li>🎯 Phân loại: Tin tức, Hướng dẫn, Chính sách, Khuyến mãi</li>";
    echo "<li>🏷️ Tags đầy đủ cho mỗi bài viết</li>";
    echo "<li>🔍 Tối ưu cho search và SEO</li>";
    echo "</ul>";
    echo "</div>";
    
    // Đọc file SQL
    $sql_file = __DIR__ . '/database.sql';
    if (!file_exists($sql_file)) {
        throw new Exception("File database.sql không tìm thấy!");
    }
    
    $sql_content = file_get_contents($sql_file);
    
    // Chia SQL thành các statements riêng biệt
    $statements = array_filter(
        array_map('trim', explode(';', $sql_content)),
        function($stmt) {
            return !empty($stmt) && !preg_match('/^\s*--/', $stmt);
        }
    );
    
    $success_count = 0;
    $warning_count = 0;
    $error_count = 0;
    
    echo "<h3>🔄 Đang import database...</h3>";
    echo "<div style='background: #fff; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>";
    
    foreach ($statements as $statement) {
        try {
            $pdo->exec($statement);
            
            // Kiểm tra loại statement
            if (stripos($statement, 'CREATE DATABASE') !== false) {
                echo "✅ Tạo database<br>";
            } elseif (stripos($statement, 'CREATE TABLE') !== false) {
                preg_match('/CREATE TABLE\s+(?:IF NOT EXISTS\s+)?`?([^`\s]+)`?/i', $statement, $matches);
                $table = $matches[1] ?? 'unknown';
                echo "✅ Tạo bảng: <strong>$table</strong><br>";
            } elseif (stripos($statement, 'INSERT INTO') !== false) {
                preg_match('/INSERT INTO\s+`?([^`\s]+)`?/i', $statement, $matches);
                $table = $matches[1] ?? 'unknown';
                echo "📝 Thêm dữ liệu vào: <strong>$table</strong><br>";
            }
            
            $success_count++;
        } catch (PDOException $e) {
            if (stripos($e->getMessage(), 'already exists') !== false) {
                echo "⚠️ " . $e->getMessage() . "<br>";
                $warning_count++;
            } else {
                echo "❌ Lỗi: " . $e->getMessage() . "<br>";
                $error_count++;
            }
        }
    }
    
    echo "</div>";
    
    // Kết nối đến database vừa tạo để kiểm tra
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h3>📊 Kết quả import:</h3>";
    echo "<div style='display: flex; gap: 20px; margin: 20px 0;'>";
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; flex: 1;'>";
    echo "<strong>✅ Thành công: $success_count</strong>";
    echo "</div>";
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; flex: 1;'>";
    echo "<strong>⚠️ Cảnh báo: $warning_count</strong>";
    echo "</div>";
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; flex: 1;'>";
    echo "<strong>❌ Lỗi: $error_count</strong>";
    echo "</div>";
    echo "</div>";
    
    // Kiểm tra các bảng đã được tạo
    echo "<h3>🗂️ Kiểm tra bảng trong database:</h3>";
    echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px;'>";
    
    $tables = ['news_categories', 'keywords', 'news', 'services', 'contacts'];
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM `$table`");
            $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            echo "📋 Bảng <strong>$table</strong>: $count records<br>";
        } catch (PDOException $e) {
            echo "❌ Bảng <strong>$table</strong>: Không tồn tại<br>";
        }
    }
    echo "</div>";
    
    // Kiểm tra đặc biệt cho tin tức
    try {
        $stmt = $pdo->query("
            SELECT nc.name as category, COUNT(n.id) as count 
            FROM news_categories nc 
            LEFT JOIN news n ON nc.id = n.category_id 
            GROUP BY nc.id, nc.name
        ");
        
        echo "<h3>📰 Thống kê tin tức theo danh mục:</h3>";
        echo "<div style='background: #e7f3ff; padding: 15px; border-radius: 5px;'>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "📂 <strong>{$row['category']}</strong>: {$row['count']} bài viết<br>";
        }
        echo "</div>";
    } catch (PDOException $e) {
        echo "❌ Không thể kiểm tra thống kê tin tức<br>";
    }
    
    echo "<div style='background: linear-gradient(135deg, #8bc34a 0%, #689f38 100%); color: white; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center;'>";
    echo "<h2>🎉 IMPORT HOÀN THÀNH!</h2>";
    echo "<p><strong>Website đã sẵn sàng với đầy đủ tính năng:</strong></p>";
    echo "<ul style='text-align: left; display: inline-block;'>";
    echo "<li>🔍 Search bar với thiết kế gradient đẹp</li>";
    echo "<li>📂 Phân loại tin tức theo danh mục</li>";
    echo "<li>🏷️ Từ khóa động từ database</li>";
    echo "<li>📰 12 bài tin tức mẫu đa dạng</li>";
    echo "</ul>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div style='background: #f8d7da; padding: 20px; border-radius: 5px; color: #721c24;'>";
    echo "<h3>❌ Lỗi kết nối database:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p><strong>Hãy kiểm tra:</strong></p>";
    echo "<ul>";
    echo "<li>XAMPP/WAMP đã khởi động chưa</li>";
    echo "<li>MySQL service đang chạy</li>";
    echo "<li>Thông tin kết nối database đúng chưa</li>";
    echo "</ul>";
    echo "</div>";
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 20px; border-radius: 5px; color: #721c24;'>";
    echo "<h3>❌ Lỗi:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}
?>

<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    background: #f8f9fa;
    color: #2c3e50;
}

h2 {
    color: #8bc34a;
    border-bottom: 2px solid #8bc34a;
    padding-bottom: 10px;
}

h3 {
    color: #2c3e50;
    margin-top: 25px;
}

p, li {
    line-height: 1.6;
}

ul {
    background: white;
    padding: 15px 30px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

hr {
    border: none;
    height: 1px;
    background: #ddd;
    margin: 30px 0;
}
</style> 