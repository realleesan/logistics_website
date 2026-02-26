<?php
// Debug file để kiểm tra lỗi
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug Information</h1>";

// Check PHP version
echo "<h2>PHP Version</h2>";
echo "PHP Version: " . phpversion() . "<br>";

// Check if vendor directory exists
echo "<h2>Composer Dependencies</h2>";
if (file_exists('vendor/autoload.php')) {
    echo "✅ vendor/autoload.php exists<br>";
} else {
    echo "❌ vendor/autoload.php not found<br>";
}

// Check if .env file exists
echo "<h2>Environment File</h2>";
if (file_exists('.env')) {
    echo "✅ .env file exists<br>";
} else {
    echo "❌ .env file not found<br>";
}

// Check database connection
echo "<h2>Database Connection</h2>";
try {
    require_once 'database/config.php';
    echo "✅ Database connection successful<br>";
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Check file permissions
echo "<h2>File Permissions</h2>";
$files_to_check = [
    'index.php',
    'database/config.php',
    'includes/functions.php',
    'includes/header.php',
    'includes/footer.php'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file not found<br>";
    }
}

// Check logs directory
echo "<h2>Logs Directory</h2>";
if (is_dir('logs')) {
    echo "✅ logs directory exists<br>";
    if (is_writable('logs')) {
        echo "✅ logs directory is writable<br>";
    } else {
        echo "❌ logs directory is not writable<br>";
    }
} else {
    echo "❌ logs directory not found<br>";
}

// Check .htaccess
echo "<h2>.htaccess</h2>";
if (file_exists('.htaccess')) {
    echo "✅ .htaccess exists<br>";
} else {
    echo "❌ .htaccess not found<br>";
}

echo "<h2>Environment Variables</h2>";
echo "APP_ENV: " . ($_ENV['APP_ENV'] ?? 'not set') . "<br>";
echo "APP_DEBUG: " . ($_ENV['APP_DEBUG'] ?? 'not set') . "<br>";
echo "APP_URL: " . ($_ENV['APP_URL'] ?? 'not set') . "<br>";

echo "<h2>Server Information</h2>";
echo "Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'unknown') . "<br>";
echo "Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'unknown') . "<br>";
echo "Script Name: " . ($_SERVER['SCRIPT_NAME'] ?? 'unknown') . "<br>";
?> 