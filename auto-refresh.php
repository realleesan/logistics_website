<?php
/**
 * Auto-Refresh Page - Fixes hanging website issues
 * 
 * This page works as a "rescue" page that:
 * 1. Shows current website status
 * 2. Can automatically refresh the main page
 * 3. Provides one-click fix for hanging connections
 * 
 * Usage: Open this page when main site hangs, then click to refresh
 */

error_reporting(0);
ini_set('display_errors', 0);

// Check if this is an AJAX request (from auto-refresh)
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($isAjax) {
    // Return JSON status
    header('Content-Type: application/json');
    
    $result = [
        'status' => 'checking',
        'time' => date('Y-m-d H:i:s'),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ];
    
    try {
        // Quick DB check
        require_once __DIR__ . '/database/config.php';
        $pdo->query("SELECT 1");
        $result['db'] = 'ok';
        
        // Try to check main page
        $result['main_page_status'] = 'unknown';
        
    } catch (Exception $e) {
        $result['db'] = 'error: ' . $e->getMessage();
    }
    
    echo json_encode($result);
    exit;
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔄 Auto Refresh - VINA LOGISTICS</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
            font-size: 28px;
        }
        .icon {
            font-size: 80px;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        .status-box {
            background: #f5f5f5;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }
        .status-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }
        .status-item:last-child { border-bottom: none; }
        .status-label { color: #666; font-weight: 500; }
        .status-value { color: #333; font-weight: bold; }
        .status-ok { color: #4caf50; }
        .status-error { color: #f44336; }
        
        .btn {
            display: inline-block;
            padding: 15px 40px;
            margin: 10px 5px;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, #c3f725, #7cb342);
            color: #333;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(195, 247, 37, 0.4);
        }
        .btn-secondary {
            background: #fff;
            color: #333;
            border: 2px solid #667eea;
        }
        .btn-secondary:hover {
            background: #667eea;
            color: white;
        }
        
        .progress {
            width: 100%;
            height: 6px;
            background: #e0e0e0;
            border-radius: 3px;
            margin: 20px 0;
            overflow: hidden;
            display: none;
        }
        .progress.active { display: block; }
        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #c3f725, #7cb342);
            width: 0%;
            transition: width 0.3s;
        }
        
        .timer {
            font-size: 14px;
            color: #666;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🔄</div>
        <h1>Trang Khắc Phục Nhanh</h1>
        <p>Trang này giúp "reset" kết nối khi website chính bị treo</p>
        
        <div class="status-box">
            <div class="status-item">
                <span class="status-label">⏰ Thời gian:</span>
                <span class="status-value" id="currentTime"><?php echo date('H:i:s'); ?></span>
            </div>
            <div class="status-item">
                <span class="status-label">🗄️ Database:</span>
                <span class="status-value" id="dbStatus">⏳ Đang kiểm tra...</span>
            </div>
            <div class="status-item">
                <span class="status-label">🌐 Server:</span>
                <span class="status-value status-ok">✅ Hoạt động</span>
            </div>
        </div>
        
        <div class="progress" id="progressBar">
            <div class="progress-bar" id="progressFill"></div>
        </div>
        
        <button class="btn btn-primary" id="refreshBtn" onclick="refreshMainPage()">
            🔄 Truy cập Website
        </button>
        
        <button class="btn btn-secondary" onclick="testConnection()">
            🧪 Kiểm tra kết nối
        </button>
        
        <div class="timer" id="timerText">
            Click "Truy cập Website" để vào trang chủ
        </div>
    </div>

    <script>
        let countdown = 5;
        let countdownInterval;
        
        // Test database connection on load
        window.addEventListener('load', function() {
            testConnection();
            updateTime();
            setInterval(updateTime, 1000);
        });
        
        function updateTime() {
            document.getElementById('currentTime').textContent = new Date().toLocaleTimeString('vi-VN');
        }
        
        function testConnection() {
            const btn = document.querySelector('.btn-secondary');
            btn.disabled = true;
            btn.textContent = '⏳ Đang kiểm tra...';
            
            fetch('heartbeat.php', {
                method: 'GET',
                cache: 'no-cache'
            })
            .then(response => response.json())
            .then(data => {
                const dbStatus = document.getElementById('dbStatus');
                if (data.db === 'ok') {
                    dbStatus.innerHTML = '<span class="status-ok">✅ OK (' + data.db_time + ')</span>';
                } else {
                    dbStatus.innerHTML = '<span class="status-error">❌ Lỗi</span>';
                }
                btn.disabled = false;
                btn.textContent = '🧪 Kiểm tra kết nối';
            })
            .catch(error => {
                document.getElementById('dbStatus').innerHTML = '<span class="status-error">❌ Lỗi kết nối</span>';
                btn.disabled = false;
                btn.textContent = '🧪 Kiểm tra kết nối';
            });
        }
        
        function refreshMainPage() {
            const btn = document.getElementById('refreshBtn');
            const progress = document.getElementById('progressBar');
            const progressFill = document.getElementById('progressFill');
            const timerText = document.getElementById('timerText');
            
            btn.disabled = true;
            btn.textContent = '⏳ Đang kết nối...';
            progress.classList.add('active');
            
            // First, test the heartbeat to reset connection
            fetch('heartbeat.php', {
                method: 'GET',
                cache: 'no-cache'
            })
            .then(response => response.json())
            .then(data => {
                // Then redirect to main page
                countdown = 3;
                timerText.textContent = 'Đang chuyển đến trang chủ...';
                
                let width = 0;
                countdownInterval = setInterval(() => {
                    width += 33.33;
                    progressFill.style.width = width + '%';
                    
                    countdown--;
                    if (countdown <= 0) {
                        clearInterval(countdownInterval);
                        // Redirect to main page with timestamp to prevent cache
                        window.location.href = '/?t=' + Date.now();
                    }
                }, 1000);
            })
            .catch(error => {
                btn.disabled = false;
                btn.textContent = '❌ Lỗi kết nối';
                progress.classList.remove('active');
                timerText.textContent = 'Không thể kết nối. Vui lòng thử lại sau.';
            });
        }
        
        // Auto-refresh every 30 seconds (optional)
        // setInterval(testConnection, 30000);
    </script>
</body>
</html>
