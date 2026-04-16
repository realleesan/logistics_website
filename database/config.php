<?php
// Load environment variables
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Log all errors to file
function logError($message, $file = '', $line = '') {
    $logMessage = date('Y-m-d H:i:s') . " - ERROR: " . $message;
    if ($file) $logMessage .= " in $file";
    if ($line) $logMessage .= " on line $line";
    $logMessage .= "\n";
    error_log($logMessage, 3, __DIR__ . '/../logs/error.log');
}

// Set error handler
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    logError($errstr, $errfile, $errline);
    return false;
});

// Set exception handler
set_exception_handler(function($exception) {
    logError($exception->getMessage(), $exception->getFile(), $exception->getLine());
    if (defined('APP_DEBUG') && APP_DEBUG) {
        echo "Error: " . $exception->getMessage();
    } else {
        echo "Có lỗi xảy ra. Vui lòng thử lại sau.";
    }
});

// Load .env file if it exists
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    try {
        if (class_exists('Dotenv\Dotenv')) {
            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
            $dotenv->load();
        } else {
            // Manual .env parsing if dotenv not available
            $envContent = file_get_contents($envFile);
            $lines = explode("\n", $envContent);
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line) && strpos($line, '#') !== 0) {
                    $parts = explode('=', $line, 2);
                    if (count($parts) == 2) {
                        $key = trim($parts[0]);
                        $value = trim($parts[1], '"\'');
                        $_ENV[$key] = $value;
                    }
                }
            }
        }
    } catch (Exception $e) {
        // Fallback if dotenv fails
        logError("Dotenv error: " . $e->getMessage());
    }
}

// Set default values if .env variables are not set
$_ENV['DB_HOST'] = $_ENV['DB_HOST'] ?? 'localhost';
$_ENV['DB_NAME'] = $_ENV['DB_NAME'] ?? 'tru98691_db';
$_ENV['DB_USER'] = $_ENV['DB_USER'] ?? 'tru98691_db';
$_ENV['DB_PASS'] = $_ENV['DB_PASS'] ?? '21042005nhat';
$_ENV['SMTP_HOST'] = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com';
$_ENV['SMTP_PORT'] = $_ENV['SMTP_PORT'] ?? '587';
$_ENV['SMTP_USERNAME'] = $_ENV['SMTP_USERNAME'] ?? 'baominhkpkp@gmail.com';
$_ENV['SMTP_PASSWORD'] = $_ENV['SMTP_PASSWORD'] ?? 'gjvz qdrq pogq sheb';
$_ENV['COMPANY_NAME'] = $_ENV['COMPANY_NAME'] ?? 'Công ty TNHH Thương mại và Dịch vụ XNK Trường Vina';
$_ENV['COMPANY_SHORT_NAME'] = $_ENV['COMPANY_SHORT_NAME'] ?? 'VINA LOGISTICS';
$_ENV['COMPANY_ADDRESS'] = $_ENV['COMPANY_ADDRESS'] ?? 'Số nhà 28 phố Lê Trọng Tấn, Phường La Khê, Quận Hà Đông, Thành phố Hà Nội';
$_ENV['COMPANY_PHONE'] = $_ENV['COMPANY_PHONE'] ?? '0587.363636';
$_ENV['COMPANY_EMAIL'] = $_ENV['COMPANY_EMAIL'] ?? 'baominhkpkp@gmail.com';
$_ENV['COMPANY_LICENSE'] = $_ENV['COMPANY_LICENSE'] ?? '0110603970';
$_ENV['APP_ENV'] = $_ENV['APP_ENV'] ?? 'production';
$_ENV['APP_DEBUG'] = $_ENV['APP_DEBUG'] ?? 'true'; // Enable debug for troubleshooting
$_ENV['APP_URL'] = $_ENV['APP_URL'] ?? 'https://truongvinalogistics.com';
$_ENV['ADMIN_USERNAME'] = $_ENV['ADMIN_USERNAME'] ?? 'admin';
$_ENV['ADMIN_PASSWORD'] = $_ENV['ADMIN_PASSWORD'] ?? 'vinalogistics2024';
$_ENV['UPLOAD_PATH'] = $_ENV['UPLOAD_PATH'] ?? 'assets/images/';
$_ENV['UPLOAD_URL'] = $_ENV['UPLOAD_URL'] ?? 'assets/images/';
$_ENV['ITEMS_PER_PAGE'] = $_ENV['ITEMS_PER_PAGE'] ?? '10';

// Cấu hình kết nối database cho Vina Logistics
define('DB_HOST', $_ENV['DB_HOST']);
define('DB_NAME', $_ENV['DB_NAME']);
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASS', $_ENV['DB_PASS']);

// Thông tin email
define('SMTP_HOST', $_ENV['SMTP_HOST']);
define('SMTP_PORT', $_ENV['SMTP_PORT']);
define('SMTP_USERNAME', $_ENV['SMTP_USERNAME']);
define('FROM_EMAIL', $_ENV['SMTP_USERNAME']);
define('FROM_NAME', $_ENV['COMPANY_SHORT_NAME']);
define('SMTP_PASSWORD', $_ENV['SMTP_PASSWORD']);

// Thông tin công ty
define('COMPANY_NAME', $_ENV['COMPANY_NAME']);
define('COMPANY_SHORT_NAME', $_ENV['COMPANY_SHORT_NAME']);
define('COMPANY_ADDRESS', $_ENV['COMPANY_ADDRESS']);
define('COMPANY_PHONE', $_ENV['COMPANY_PHONE']);
define('COMPANY_EMAIL', $_ENV['COMPANY_EMAIL']);
define('COMPANY_LICENSE', $_ENV['COMPANY_LICENSE']);

// Application settings
define('APP_ENV', $_ENV['APP_ENV']);
define('APP_DEBUG', $_ENV['APP_DEBUG'] === 'true');
define('APP_URL', $_ENV['APP_URL']);

// Admin settings
define('ADMIN_USERNAME', $_ENV['ADMIN_USERNAME']);
define('ADMIN_PASSWORD', $_ENV['ADMIN_PASSWORD']);

// Upload settings
define('UPLOAD_PATH', $_ENV['UPLOAD_PATH']);
define('UPLOAD_URL', $_ENV['UPLOAD_URL']);
define('ITEMS_PER_PAGE', (int)$_ENV['ITEMS_PER_PAGE']);

// Error reporting based on environment
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/error.log');
}

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_TIMEOUT, 10); // Set connection timeout
    $pdo->setAttribute(PDO::ATTR_PERSISTENT, false); // Disable persistent connections
    logError("Database connection successful");
} catch(PDOException $e) {
    logError("Database connection error: " . $e->getMessage());
    if (APP_DEBUG) {
        die("Lỗi kết nối database: " . $e->getMessage());
    } else {
        die("Có lỗi xảy ra. Vui lòng thử lại sau.");
    }
}

// Include helper functions
require_once __DIR__ . '/../includes/functions.php';
?> 