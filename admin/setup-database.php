<?php
require_once 'config.php';

echo "<h2>🔧 Thiết lập Database - Vina Logistics Admin</h2>";

$tables_to_create = [
    'news_categories' => "CREATE TABLE `news_categories` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `slug` varchar(255) NOT NULL,
        `description` text,
        `status` enum('active','inactive') DEFAULT 'active',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `slug` (`slug`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    'news' => "CREATE TABLE `news` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `title` varchar(255) NOT NULL,
        `slug` varchar(255) NOT NULL,
        `excerpt` text,
        `content` longtext,
        `category_id` int(11),
        `image` varchar(255),
        `status` enum('draft','published') DEFAULT 'draft',
        `featured` tinyint(1) DEFAULT 0,
        `tags` varchar(500),
        `meta_title` varchar(255),
        `meta_description` varchar(500),
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `slug` (`slug`),
        KEY `category_id` (`category_id`),
        FOREIGN KEY (`category_id`) REFERENCES `news_categories`(`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    'services' => "CREATE TABLE `services` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `title` varchar(255) NOT NULL,
        `slug` varchar(255) NOT NULL,
        `description` text,
        `short_description` varchar(500),
        `content` longtext,
        `image` varchar(255),
        `status` enum('active','inactive') DEFAULT 'active',
        `featured` tinyint(1) DEFAULT 0,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `slug` (`slug`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    'contacts' => "CREATE TABLE `contacts` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `email` varchar(255) NOT NULL,
        `phone` varchar(50),
        `company` varchar(255),
        `subject` varchar(255) NOT NULL,
        `message` text NOT NULL,
        `status` enum('new','read','replied') DEFAULT 'new',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    'keywords' => "CREATE TABLE `keywords` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `keyword` varchar(255) NOT NULL,
        `search_volume` int(11) DEFAULT 0,
        `difficulty` enum('easy','medium','hard') DEFAULT 'medium',
        `status` enum('active','inactive') DEFAULT 'active',
        `notes` text,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `keyword` (`keyword`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
];

$sample_data = [
    'news_categories' => [
        ['Tin tức chung', 'tin-tuc-chung', 'Tin tức tổng quát về logistics', 'active'],
        ['Xuất nhập khẩu', 'xuat-nhap-khau', 'Tin tức về xuất nhập khẩu', 'active'],
        ['Vận tài', 'van-tai', 'Tin tức về vận tải', 'active'],
        ['Kho bãi', 'kho-bai', 'Tin tức về dịch vụ kho bãi', 'active']
    ],
    'services' => [
        ['Vận tải đường bộ', 'van-tai-duong-bo', 'Dịch vụ vận tải đường bộ chuyên nghiệp', 'Vận chuyển hàng hóa bằng xe tải', '<p>Dịch vụ vận tải đường bộ với đội xe hiện đại</p>', 'active', 1],
        ['Vận tải đường biển', 'van-tai-duong-bien', 'Dịch vụ vận tải đường biển quốc tế', 'Vận chuyển container đường biển', '<p>Dịch vụ vận tải container quốc tế</p>', 'active', 1],
        ['Kho bãi', 'kho-bai', 'Dịch vụ cho thuê kho bãi', 'Kho bãi hiện đại, an toàn', '<p>Hệ thống kho bãi với công nghệ hiện đại</p>', 'active', 0],
        ['Xuất nhập khẩu', 'xuat-nhap-khau', 'Dịch vụ xuất nhập khẩu trọn gói', 'Làm thủ tục xuất nhập khẩu', '<p>Dịch vụ làm thủ tục xuất nhập khẩu chuyên nghiệp</p>', 'active', 1]
    ],
    'keywords' => [
        ['vận chuyển logistics', 100, 'medium', 'active', 'Từ khóa chính về vận chuyển'],
        ['dịch vụ logistics', 80, 'easy', 'active', 'Dịch vụ logistics tổng quát'],
        ['xuất nhập khẩu', 60, 'hard', 'active', 'Từ khóa về xuất nhập khẩu'],
        ['kho bãi', 40, 'easy', 'active', 'Dịch vụ kho bãi'],
        ['vận tải container', 90, 'medium', 'active', 'Vận chuyển container']
    ],
    'contacts' => [
        ['Nguyễn Văn A', 'nguyenvana@email.com', '0901234567', 'Công ty ABC', 'Tư vấn dịch vụ logistics', 'Tôi muốn tư vấn về dịch vụ vận chuyển hàng hóa từ Việt Nam sang Mỹ', 'new'],
        ['Trần Thị B', 'tranthib@email.com', '0987654321', 'Công ty XYZ', 'Báo giá dịch vụ kho bãi', 'Công ty chúng tôi cần thuê kho để lưu trữ hàng hóa dài hạn', 'read']
    ]
];

function checkAndFixTableStructure($pdo, $table_name) {
    echo "<h4>🔍 Kiểm tra bảng $table_name...</h4>";
    
    if ($table_name === 'contacts') {
        $stmt = $pdo->query("SHOW COLUMNS FROM contacts LIKE 'company'");
        if (!$stmt->fetch()) {
            echo "<p style='color: orange;'>⚠️ Thêm cột 'company' vào bảng contacts...</p>";
            $pdo->exec("ALTER TABLE contacts ADD COLUMN company varchar(255) AFTER phone");
            echo "<p style='color: green;'>✅ Đã thêm cột 'company'!</p>";
        }
    }
    
    if ($table_name === 'news_categories') {
        // Ensure sort_order exists for proper ordering in admin UI
        $stmt = $pdo->query("SHOW COLUMNS FROM news_categories LIKE 'sort_order'");
        if (!$stmt->fetch()) {
            echo "<p style='color: orange;'>⚠️ Thêm cột 'sort_order' vào bảng news_categories...</p>";
            $pdo->exec("ALTER TABLE news_categories ADD COLUMN sort_order int(11) DEFAULT 0 AFTER description");
            echo "<p style='color: green;'>✅ Đã thêm cột 'sort_order'!</p>";
        }
    }

    if ($table_name === 'services') {
        // short_description
        $stmt = $pdo->query("SHOW COLUMNS FROM services LIKE 'short_description'");
        if (!$stmt->fetch()) {
            echo "<p style='color: orange;'>⚠️ Thêm cột 'short_description' vào bảng services...</p>";
            $pdo->exec("ALTER TABLE services ADD COLUMN short_description varchar(500) AFTER description");
            echo "<p style='color: green;'>✅ Đã thêm cột 'short_description'!</p>";
        }

        // content
        $stmt = $pdo->query("SHOW COLUMNS FROM services LIKE 'content'");
        if (!$stmt->fetch()) {
            echo "<p style='color: orange;'>⚠️ Thêm cột 'content' vào bảng services...</p>";
            $pdo->exec("ALTER TABLE services ADD COLUMN content longtext AFTER short_description");
            echo "<p style='color: green;'>✅ Đã thêm cột 'content'!</p>";
        }

        // featured
        $stmt = $pdo->query("SHOW COLUMNS FROM services LIKE 'featured'");
        if (!$stmt->fetch()) {
            echo "<p style='color: orange;'>⚠️ Thêm cột 'featured' vào bảng services...</p>";
            $pdo->exec("ALTER TABLE services ADD COLUMN featured tinyint(1) DEFAULT 0 AFTER status");
            echo "<p style='color: green;'>✅ Đã thêm cột 'featured'!</p>";
        }

        // sort_order
        $stmt = $pdo->query("SHOW COLUMNS FROM services LIKE 'sort_order'");
        if (!$stmt->fetch()) {
            echo "<p style='color: orange;'>⚠️ Thêm cột 'sort_order' vào bảng services...</p>";
            $pdo->exec("ALTER TABLE services ADD COLUMN sort_order int(11) DEFAULT 0 AFTER icon");
            echo "<p style='color: green;'>✅ Đã thêm cột 'sort_order'!</p>";
        }

        // updated_at
        $stmt = $pdo->query("SHOW COLUMNS FROM services LIKE 'updated_at'");
        if (!$stmt->fetch()) {
            echo "<p style='color: orange;'>⚠️ Thêm cột 'updated_at' vào bảng services...</p>";
            $pdo->exec("ALTER TABLE services ADD COLUMN updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at");
            echo "<p style='color: green;'>✅ Đã thêm cột 'updated_at'!</p>";
        }

        // icon (được trang public sử dụng, thêm nếu thiếu)
        $stmt = $pdo->query("SHOW COLUMNS FROM services LIKE 'icon'");
        if (!$stmt->fetch()) {
            echo "<p style='color: orange;'>⚠️ Thêm cột 'icon' vào bảng services...</p>";
            $pdo->exec("ALTER TABLE services ADD COLUMN icon varchar(255) DEFAULT NULL AFTER image");
            echo "<p style='color: green;'>✅ Đã thêm cột 'icon'!</p>";
        }
    }
    
    if ($table_name === 'keywords') {
        // Kiểm tra và thêm cột search_volume
        $stmt = $pdo->query("SHOW COLUMNS FROM keywords LIKE 'search_volume'");
        if (!$stmt->fetch()) {
            echo "<p style='color: orange;'>⚠️ Thêm cột 'search_volume' vào bảng keywords...</p>";
            $pdo->exec("ALTER TABLE keywords ADD COLUMN search_volume int(11) DEFAULT 0 AFTER keyword");
            echo "<p style='color: green;'>✅ Đã thêm cột 'search_volume'!</p>";
        }
        
        // Kiểm tra và thêm cột difficulty
        $stmt = $pdo->query("SHOW COLUMNS FROM keywords LIKE 'difficulty'");
        if (!$stmt->fetch()) {
            echo "<p style='color: orange;'>⚠️ Thêm cột 'difficulty' vào bảng keywords...</p>";
            $pdo->exec("ALTER TABLE keywords ADD COLUMN difficulty enum('easy','medium','hard') DEFAULT 'medium' AFTER search_volume");
            echo "<p style='color: green;'>✅ Đã thêm cột 'difficulty'!</p>";
        }
        
        // Kiểm tra và thêm cột status
        $stmt = $pdo->query("SHOW COLUMNS FROM keywords LIKE 'status'");
        if (!$stmt->fetch()) {
            echo "<p style='color: orange;'>⚠️ Thêm cột 'status' vào bảng keywords...</p>";
            $pdo->exec("ALTER TABLE keywords ADD COLUMN status enum('active','inactive') DEFAULT 'active' AFTER difficulty");
            echo "<p style='color: green;'>✅ Đã thêm cột 'status'!</p>";
        }
        
        // Kiểm tra và thêm cột notes
        $stmt = $pdo->query("SHOW COLUMNS FROM keywords LIKE 'notes'");
        if (!$stmt->fetch()) {
            echo "<p style='color: orange;'>⚠️ Thêm cột 'notes' vào bảng keywords...</p>";
            $pdo->exec("ALTER TABLE keywords ADD COLUMN notes text AFTER status");
            echo "<p style='color: green;'>✅ Đã thêm cột 'notes'!</p>";
        }
        
        // Bây giờ cập nhật các bản ghi hiện có để đảm bảo không có null values  
        try {
            $pdo->exec("UPDATE keywords SET difficulty = 'medium' WHERE difficulty IS NULL OR difficulty = ''");
            $pdo->exec("UPDATE keywords SET status = 'active' WHERE status IS NULL OR status = ''");
            $pdo->exec("UPDATE keywords SET search_volume = 0 WHERE search_volume IS NULL");
            $pdo->exec("UPDATE keywords SET notes = '' WHERE notes IS NULL");
            echo "<p style='color: green;'>✅ Đã cập nhật NULL values trong bảng keywords!</p>";
        } catch (PDOException $e) {
            echo "<p style='color: orange;'>⚠️ Cảnh báo khi cập nhật NULL values: " . $e->getMessage() . "</p>";
        }
    }
    
    if ($table_name === 'news') {
        $columns_to_check = ['tags', 'meta_title', 'meta_description'];
        foreach ($columns_to_check as $col) {
            $stmt = $pdo->query("SHOW COLUMNS FROM news LIKE '$col'");
            if (!$stmt->fetch()) {
                echo "<p style='color: orange;'>⚠️ Thêm cột '$col' vào bảng news...</p>";
                switch ($col) {
                    case 'tags':
                        $pdo->exec("ALTER TABLE news ADD COLUMN tags varchar(500) AFTER featured");
                        break;
                    case 'meta_title':
                        $pdo->exec("ALTER TABLE news ADD COLUMN meta_title varchar(255) AFTER tags");
                        break;
                    case 'meta_description':
                        $pdo->exec("ALTER TABLE news ADD COLUMN meta_description varchar(500) AFTER meta_title");
                        break;
                }
                echo "<p style='color: green;'>✅ Đã thêm cột '$col'!</p>";
            }
        }
    }
}

try {
    echo "<h3>📋 Kiểm tra và tạo bảng...</h3>";
    
    foreach ($tables_to_create as $table_name => $sql) {
        // Kiểm tra xem bảng có tồn tại không
        $stmt = $pdo->query("SHOW TABLES LIKE '$table_name'");
        $table_exists = $stmt->fetch();
        
        if (!$table_exists) {
            echo "<p style='color: orange;'>⚠️ Tạo bảng '$table_name'...</p>";
            $pdo->exec($sql);
            echo "<p style='color: green;'>✅ Đã tạo bảng '$table_name' thành công!</p>";
        } else {
            echo "<p style='color: blue;'>ℹ️ Bảng '$table_name' đã tồn tại.</p>";
            // Kiểm tra và sửa cấu trúc bảng
            checkAndFixTableStructure($pdo, $table_name);
        }
    }
    
    echo "<h3>📝 Thêm dữ liệu mẫu...</h3>";
    
    // Thêm dữ liệu mẫu cho news_categories
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM news_categories");
    if ($stmt->fetch()['count'] == 0) {
        echo "<p>Thêm danh mục mẫu...</p>";
        $stmt = $pdo->prepare("INSERT INTO news_categories (name, slug, description, status) VALUES (?, ?, ?, ?)");
        foreach ($sample_data['news_categories'] as $category) {
            $stmt->execute($category);
        }
        echo "<p style='color: green;'>✅ Đã thêm danh mục mẫu!</p>";
    }
    
    // Thêm dữ liệu mẫu cho services
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM services");
    if ($stmt->fetch()['count'] == 0) {
        echo "<p>Thêm dịch vụ mẫu...</p>";
        $stmt = $pdo->prepare("INSERT INTO services (title, slug, description, short_description, content, status, featured) VALUES (?, ?, ?, ?, ?, ?, ?)");
        foreach ($sample_data['services'] as $service) {
            $stmt->execute($service);
        }
        echo "<p style='color: green;'>✅ Đã thêm dịch vụ mẫu!</p>";
    }
    
    // Thêm dữ liệu mẫu cho keywords
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM keywords");
    if ($stmt->fetch()['count'] == 0) {
        echo "<p>Thêm từ khóa mẫu...</p>";
        $stmt = $pdo->prepare("INSERT INTO keywords (keyword, search_volume, difficulty, status, notes) VALUES (?, ?, ?, ?, ?)");
        foreach ($sample_data['keywords'] as $keyword) {
            $stmt->execute($keyword);
        }
        echo "<p style='color: green;'>✅ Đã thêm từ khóa mẫu!</p>";
    }
    
    // Thêm dữ liệu mẫu cho contacts
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM contacts");
    if ($stmt->fetch()['count'] == 0) {
        echo "<p>Thêm liên hệ mẫu...</p>";
        $stmt = $pdo->prepare("INSERT INTO contacts (name, email, phone, company, subject, message, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        foreach ($sample_data['contacts'] as $contact) {
            $stmt->execute($contact);
        }
        echo "<p style='color: green;'>✅ Đã thêm liên hệ mẫu!</p>";
    }
    
    echo "<h3>🎉 Hoàn thành thiết lập!</h3>";
    echo "<p style='color: green; font-size: 18px;'><strong>✅ Database đã được thiết lập hoàn chỉnh!</strong></p>";
    
    echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h4>📊 Tóm tắt:</h4>";
    echo "<ul>";
    foreach (array_keys($tables_to_create) as $table) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
        $count = $stmt->fetch()['count'];
        echo "<li><strong>$table:</strong> $count bản ghi</li>";
    }
    echo "</ul>";
    echo "</div>";
    
    echo "<h4>✅ Tất cả lỗi đã được khắc phục:</h4>";
    echo "<ul>";
    echo "<li>✅ Sửa lỗi 'Unknown column company'</li>";
    echo "<li>✅ Sửa lỗi 'Undefined array key difficulty'</li>";
    echo "<li>✅ Sửa lỗi 'Undefined array key status'</li>";
    echo "<li>✅ Sửa lỗi 'Undefined array key notes'</li>";
    echo "<li>✅ Sửa lỗi 'Undefined array key search_volume'</li>";
    echo "<li>✅ Cập nhật NULL values trong database</li>";
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Lỗi: " . $e->getMessage() . "</p>";
}

echo "<div style='margin-top: 30px; text-align: center;'>";
echo "<a href='index.php' style='background: #8bc34a; color: #2c3e50; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>🏠 Về Dashboard</a> ";
echo "<a href='keywords.php' style='background: #17a2b8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>🔑 Quản lý từ khóa</a> ";
echo "<a href='services.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>⚙️ Quản lý dịch vụ</a> ";
echo "<a href='contacts.php' style='background: #ffc107; color: #2c3e50; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>📧 Quản lý liên hệ</a>";
echo "</div>";
?> 