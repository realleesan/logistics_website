<?php
session_start();

// Cấu hình bảo mật
define('DEATH_PASSWORD', 'deathdeathdeath');
define('DEATH_LOG_FILE', '../logs/death_protection.log');

// Tạo thư mục logs nếu chưa có
if (!file_exists('../logs')) {
    mkdir('../logs', 0755, true);
}

// Hàm ghi log
function writeDeathLog($action, $details = '') {
    $log_entry = date('Y-m-d H:i:s') . " | " . $_SERVER['REMOTE_ADDR'] . " | " . $action . " | " . $details . "\n";
    file_put_contents(DEATH_LOG_FILE, $log_entry, FILE_APPEND | LOCK_EX);
}

// Hàm kiểm tra trạng thái khóa
function isWebsiteLocked() {
    return file_exists('../logs/website_locked.txt');
}

// Hàm khóa website
function lockWebsite() {
    file_put_contents('../logs/website_locked.txt', date('Y-m-d H:i:s'));
    writeDeathLog('WEBSITE_LOCKED', 'Website has been locked');
}

// Hàm mở khóa website
function unlockWebsite() {
    if (file_exists('../logs/website_locked.txt')) {
        unlink('../logs/website_locked.txt');
        writeDeathLog('WEBSITE_UNLOCKED', 'Website has been unlocked');
        return true;
    }
    return false;
}

// Xử lý AJAX requests
if (isset($_POST['action'])) {
    $response = ['success' => false, 'message' => ''];
    
    switch ($_POST['action']) {
        case 'check_lock':
            $response['locked'] = isWebsiteLocked();
            $response['success'] = true;
            break;
            
        case 'lock_website':
            if (isset($_POST['password']) && $_POST['password'] === DEATH_PASSWORD) {
                lockWebsite();
                $response['success'] = true;
                $response['message'] = 'Website has been locked successfully';
            } else {
                $response['message'] = 'Invalid password';
            }
            break;
            
        case 'unlock_website':
            if (isset($_POST['password']) && $_POST['password'] === DEATH_PASSWORD) {
                if (unlockWebsite()) {
                    $response['success'] = true;
                    $response['message'] = 'Website has been unlocked successfully';
                } else {
                    $response['message'] = 'Website is not locked';
                }
            } else {
                $response['message'] = 'Invalid password';
            }
            break;
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Death Protection System</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            background: #000;
            color: #00ff00;
            margin: 0;
            padding: 20px;
            overflow: hidden;
        }
        
        .death-panel {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #111;
            border: 2px solid #ff0000;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 50px #ff0000;
            z-index: 9999;
            min-width: 400px;
        }
        
        .death-title {
            text-align: center;
            font-size: 24px;
            margin-bottom: 30px;
            color: #ff0000;
            text-shadow: 0 0 10px #ff0000;
        }
        
        .death-button {
            background: #ff0000;
            color: #000;
            border: none;
            padding: 15px 25px;
            margin: 10px 5px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .death-button:hover {
            background: #cc0000;
            box-shadow: 0 0 20px #ff0000;
        }
        
        .death-input {
            background: #000;
            border: 1px solid #ff0000;
            color: #00ff00;
            padding: 10px;
            margin: 10px 0;
            width: 100%;
            box-sizing: border-box;
        }
        
        .death-message {
            margin: 15px 0;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }
        
        .death-message.success {
            background: #004400;
            border: 1px solid #00ff00;
        }
        
        .death-message.error {
            background: #440000;
            border: 1px solid #ff0000;
        }
        
        .matrix-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            opacity: 0.1;
        }
        
        .matrix-char {
            position: absolute;
            color: #00ff00;
            font-size: 20px;
            animation: fall 3s linear infinite;
        }
        
        @keyframes fall {
            0% { transform: translateY(-100vh); }
            100% { transform: translateY(100vh); }
        }
    </style>
</head>
<body>
    <div class="matrix-bg" id="matrixBg"></div>
    
    <div class="death-panel" id="deathPanel">
        <div class="death-title">☠️ DEATH PROTECTION SYSTEM ☠️</div>
        
        <div id="lockStatus"></div>
        
        <div style="margin: 20px 0;">
            <input type="password" id="deathPassword" class="death-input" placeholder="Enter death password">
        </div>
        
        <div style="text-align: center;">
            <button class="death-button" onclick="lockWebsite()">🔒 LOCK WEBSITE</button>
            <button class="death-button" onclick="unlockWebsite()">🔓 UNLOCK WEBSITE</button>
        </div>
        
        <div id="deathMessage"></div>
    </div>

    <script>
        // Tạo hiệu ứng Matrix
        function createMatrixEffect() {
            const bg = document.getElementById('matrixBg');
            const chars = '01アイウエオカキクケコサシスセソタチツテトナニヌネノハヒフヘホマミムメモヤユヨラリルレロワヲン';
            
            setInterval(() => {
                const char = document.createElement('div');
                char.className = 'matrix-char';
                char.textContent = chars[Math.floor(Math.random() * chars.length)];
                char.style.left = Math.random() * 100 + '%';
                char.style.animationDelay = Math.random() * 3 + 's';
                bg.appendChild(char);
                
                setTimeout(() => {
                    if (char.parentNode) {
                        char.parentNode.removeChild(char);
                    }
                }, 3000);
            }, 100);
        }
        
        function checkLockStatus() {
            fetch('index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=check_lock'
            })
            .then(response => response.json())
            .then(data => {
                const statusDiv = document.getElementById('lockStatus');
                if (data.locked) {
                    statusDiv.innerHTML = '<div class="death-message error">🔒 WEBSITE IS LOCKED</div>';
                } else {
                    statusDiv.innerHTML = '<div class="death-message success">🔓 WEBSITE IS UNLOCKED</div>';
                }
            });
        }
        
        function lockWebsite() {
            const password = document.getElementById('deathPassword').value;
            if (!password) {
                showMessage('Please enter password', 'error');
                return;
            }
            
            fetch('index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=lock_website&password=${encodeURIComponent(password)}`
            })
            .then(response => response.json())
            .then(data => {
                showMessage(data.message, data.success ? 'success' : 'error');
                if (data.success) {
                    checkLockStatus();
                }
            });
        }
        
        function unlockWebsite() {
            const password = document.getElementById('deathPassword').value;
            if (!password) {
                showMessage('Please enter password', 'error');
                return;
            }
            
            fetch('index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=unlock_website&password=${encodeURIComponent(password)}`
            })
            .then(response => response.json())
            .then(data => {
                showMessage(data.message, data.success ? 'success' : 'error');
                if (data.success) {
                    checkLockStatus();
                }
            });
        }
        
        function showMessage(message, type) {
            const messageDiv = document.getElementById('deathMessage');
            messageDiv.innerHTML = `<div class="death-message ${type}">${message}</div>`;
            
            setTimeout(() => {
                messageDiv.innerHTML = '';
            }, 5000);
        }
        
        // Khởi tạo hiệu ứng Matrix và kiểm tra trạng thái
        createMatrixEffect();
        checkLockStatus();
    </script>
</body>
</html> 