<?php
/**
 * System Check for Vina Logistics Website
 * File này kiểm tra tất cả các thành phần cần thiết cho website
 */

echo "<h1>🔍 System Check - Vina Logistics</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .success { color: green; }
    .error { color: red; }
    .warning { color: orange; }
    .info { color: blue; }
    .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
</style>";

// ===========================================
// 1. PHP Version Check
// ===========================================
echo "<div class='section'>";
echo "<h2>📋 PHP Version Check</h2>";
$php_version = phpversion();
echo "<p>PHP Version: <strong>$php_version</strong></p>";

if (version_compare($php_version, '7.4.0', '>=')) {
    echo "<p class='success'>✅ PHP version is compatible (>= 7.4.0)</p>";
} else {
    echo "<p class='error'>❌ PHP version is too old. Required: >= 7.4.0</p>";
}
echo "</div>";

// ===========================================
// 2. Required Extensions Check
// ===========================================
echo "<div class='section'>";
echo "<h2>🔧 Required Extensions Check</h2>";

$required_extensions = [
    'pdo',
    'pdo_mysql',
    'mbstring',
    'openssl',
    'curl',
    'gd',
    'json'
];

foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p class='success'>✅ $ext extension is loaded</p>";
    } else {
        echo "<p class='error'>❌ $ext extension is missing</p>";
    }
}
echo "</div>";

// ===========================================
// 3. Database Connection Check
// ===========================================
echo "<div class='section'>";
echo "<h2>🗄️ Database Connection Check</h2>";

try {
    require_once 'database/config.php';
    
    if (isset($pdo)) {
        echo "<p class='success'>✅ Database connection successful</p>";
        
        // Test query
        $stmt = $pdo->query("SELECT COUNT(*) FROM information_schema.tables");
        $table_count = $stmt->fetchColumn();
        echo "<p class='info'>📊 Total tables in database: $table_count</p>";
        
        // Check required tables
        $required_tables = ['news', 'services', 'contacts', 'news_categories'];
        foreach ($required_tables as $table) {
            try {
                $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
                $count = $stmt->fetchColumn();
                echo "<p class='success'>✅ Table '$table' exists with $count records</p>";
            } catch (Exception $e) {
                echo "<p class='error'>❌ Table '$table' is missing</p>";
            }
        }
    } else {
        echo "<p class='error'>❌ Database connection failed</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Database error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// ===========================================
// 4. File Permissions Check
// ===========================================
echo "<div class='section'>";
echo "<h2>📁 File Permissions Check</h2>";

$files_to_check = [
    'database/config.php' => 'readable',
    'database/smtp-config.php' => 'readable',
    'includes/header.php' => 'readable',
    'includes/footer.php' => 'readable',
    'assets/css/style.css' => 'readable',
    'assets/js/main.js' => 'readable',
    'logo.jpg' => 'readable',
    'logo-removebg.png' => 'readable'
];

foreach ($files_to_check as $file => $permission) {
    if (file_exists($file)) {
        if ($permission == 'readable' && is_readable($file)) {
            echo "<p class='success'>✅ $file is readable</p>";
        } elseif ($permission == 'writable' && is_writable($file)) {
            echo "<p class='success'>✅ $file is writable</p>";
        } else {
            echo "<p class='error'>❌ $file permission issue</p>";
        }
    } else {
        echo "<p class='error'>❌ $file does not exist</p>";
    }
}
echo "</div>";

// ===========================================
// 5. Email Configuration Check
// ===========================================
echo "<div class='section'>";
echo "<h2>📧 Email Configuration Check</h2>";

if (defined('SMTP_HOST') && defined('SMTP_USERNAME')) {
    echo "<p class='success'>✅ SMTP configuration is set</p>";
    echo "<p class='info'>📧 SMTP Host: " . SMTP_HOST . "</p>";
    echo "<p class='info'>📧 SMTP Username: " . SMTP_USERNAME . "</p>";
    
    if (defined('SMTP_PASSWORD') && !empty(SMTP_PASSWORD)) {
        echo "<p class='success'>✅ SMTP Password is configured</p>";
    } else {
        echo "<p class='warning'>⚠️ SMTP Password is not configured</p>";
    }
} else {
    echo "<p class='error'>❌ SMTP configuration is missing</p>";
}
echo "</div>";

// ===========================================
// 6. URL Rewriting Check
// ===========================================
echo "<div class='section'>";
echo "<h2>🔗 URL Rewriting Check</h2>";

if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    if (in_array('mod_rewrite', $modules)) {
        echo "<p class='success'>✅ mod_rewrite is enabled</p>";
    } else {
        echo "<p class='warning'>⚠️ mod_rewrite may not be enabled</p>";
    }
} else {
    echo "<p class='info'>ℹ️ Cannot check mod_rewrite status (not Apache)</p>";
}

// Check if .htaccess exists
if (file_exists('.htaccess')) {
    echo "<p class='success'>✅ .htaccess file exists</p>";
} else {
    echo "<p class='error'>❌ .htaccess file is missing</p>";
}
echo "</div>";

// ===========================================
// 7. Security Check
// ===========================================
echo "<div class='section'>";
echo "<h2>🔒 Security Check</h2>";

// Check if sensitive files are accessible
$sensitive_files = [
    'database/smtp-config.php',
    'admin/config.php',
    'database/production-config.php'
];

foreach ($sensitive_files as $file) {
    if (file_exists($file)) {
        echo "<p class='success'>✅ $file exists</p>";
    } else {
        echo "<p class='warning'>⚠️ $file is missing</p>";
    }
}

// Check SSL
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    echo "<p class='success'>✅ SSL is enabled</p>";
} else {
    echo "<p class='warning'>⚠️ SSL is not enabled (recommended for production)</p>";
}
echo "</div>";

// ===========================================
// 8. Performance Check
// ===========================================
echo "<div class='section'>";
echo "<h2>⚡ Performance Check</h2>";

// Check memory limit
$memory_limit = ini_get('memory_limit');
echo "<p class='info'>💾 Memory Limit: $memory_limit</p>";

// Check max execution time
$max_execution_time = ini_get('max_execution_time');
echo "<p class='info'>⏱️ Max Execution Time: $max_execution_time seconds</p>";

// Check upload max filesize
$upload_max_filesize = ini_get('upload_max_filesize');
echo "<p class='info'>📤 Upload Max Filesize: $upload_max_filesize</p>";
echo "</div>";

// ===========================================
// 9. Recommendations
// ===========================================
echo "<div class='section'>";
echo "<h2>💡 Recommendations</h2>";

echo "<ul>";
echo "<li>🔒 Enable SSL certificate for production</li>";
echo "<li>📧 Configure SMTP password in database/smtp-config.php</li>";
echo "<li>🔐 Change admin password in admin/config.php</li>";
echo "<li>🌐 Update domain in robots.txt and sitemap.xml</li>";
echo "<li>📊 Set up Google Analytics (optional)</li>";
echo "<li>🔄 Configure backup system</li>";
echo "<li>📱 Test mobile responsiveness</li>";
echo "<li>🚀 Optimize images for web</li>";
echo "</ul>";
echo "</div>";

// ===========================================
// 10. Summary
// ===========================================
echo "<div class='section'>";
echo "<h2>📋 Summary</h2>";
echo "<p>System check completed. Please review all sections above.</p>";
echo "<p><strong>Next steps:</strong></p>";
echo "<ol>";
echo "<li>Fix any errors shown above</li>";
echo "<li>Configure email settings</li>";
echo "<li>Update domain information</li>";
echo "<li>Test all functionality</li>";
echo "<li>Deploy to production</li>";
echo "</ol>";
echo "</div>";

echo "<hr>";
echo "<p><em>System check completed at: " . date('Y-m-d H:i:s') . "</em></p>";
?> 