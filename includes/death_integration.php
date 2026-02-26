<?php
// Death Protection Integration
// File này sẽ được include vào tất cả các trang để kiểm tra trạng thái khóa

// Kiểm tra xem website có bị khóa không
function checkWebsiteLock() {
    if (file_exists('logs/website_locked.txt')) {
        // Website đang bị khóa
        http_response_code(503); // Service Unavailable
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Website Temporarily Unavailable</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    margin: 0;
                    padding: 0;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                    color: #333;
                }
                
                .maintenance-container {
                    background: white;
                    padding: 40px;
                    border-radius: 15px;
                    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
                    text-align: center;
                    max-width: 500px;
                    width: 90%;
                }
                
                .maintenance-icon {
                    font-size: 80px;
                    margin-bottom: 20px;
                }
                
                .maintenance-title {
                    font-size: 28px;
                    font-weight: bold;
                    margin-bottom: 15px;
                    color: #333;
                }
                
                .maintenance-message {
                    font-size: 16px;
                    color: #666;
                    line-height: 1.6;
                    margin-bottom: 30px;
                }
                
                .maintenance-time {
                    background: #f8f9fa;
                    padding: 15px;
                    border-radius: 8px;
                    font-size: 14px;
                    color: #555;
                }
                
                .progress-bar {
                    width: 100%;
                    height: 6px;
                    background: #e9ecef;
                    border-radius: 3px;
                    margin: 20px 0;
                    overflow: hidden;
                }
                
                .progress-fill {
                    height: 100%;
                    background: linear-gradient(90deg, #667eea, #764ba2);
                    width: 0%;
                    animation: progress 2s ease-in-out infinite;
                }
                
                @keyframes progress {
                    0% { width: 0%; }
                    50% { width: 70%; }
                    100% { width: 100%; }
                }
            </style>
        </head>
        <body>
            <div class="maintenance-container">
                <div class="maintenance-icon">🔧</div>
                <div class="maintenance-title">Website Temporarily Unavailable</div>
                <div class="maintenance-message">
                    We're currently performing maintenance on our website. 
                    Please check back later. Thank you for your patience.
                </div>
                <div class="progress-bar">
                    <div class="progress-fill"></div>
                </div>
                <div class="maintenance-time">
                    Maintenance started: <?php echo file_get_contents('logs/website_locked.txt'); ?>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

// Tự động kiểm tra khi include file này
checkWebsiteLock();
?>
