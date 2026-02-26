<?php
/**
 * Email Helper cho Vina Logistics
 * Sử dụng PHPMailer để gửi email
 */

require_once __DIR__ . '/../lib/src/PHPMailer.php';
require_once __DIR__ . '/../lib/src/SMTP.php';
require_once __DIR__ . '/../lib/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailHelper {
    
    private $mailer;
    
    public function __construct() {
        $this->mailer = new PHPMailer(true);
        $this->setupSMTP();
    }
    
    /**
     * Cấu hình SMTP
     */
    private function setupSMTP() {
        try {
            $this->mailer->isSMTP();
            $this->mailer->Host = SMTP_HOST;
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = SMTP_USERNAME;
            $this->mailer->Password = SMTP_PASSWORD;
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = SMTP_PORT;
            $this->mailer->CharSet = 'UTF-8';
            
            // Cấu hình người gửi
            $this->mailer->setFrom(FROM_EMAIL, FROM_NAME);
        } catch (Exception $e) {
            error_log("Lỗi cấu hình SMTP: " . $e->getMessage());
        }
    }
    
    /**
     * Gửi email thông báo liên hệ mới cho admin
     */
    public function sendContactNotificationToAdmin($contactData) {
        try {
            $this->mailer->clearAllRecipients();
            $this->mailer->clearAttachments();
            
            // Người nhận
            $this->mailer->addAddress(COMPANY_EMAIL);
            $this->mailer->addAddress('truongvinagroup@gmail.com'); // Email giám đốc
            $this->mailer->addAddress('tongthitrang.vinalogistics@gmail.com'); // Email trưởng phòng KD
            
            // Cấu hình email
            $this->mailer->isHTML(true);
            $this->mailer->Subject = '[' . COMPANY_SHORT_NAME . '] Liên hệ mới từ khách hàng';
            
            // Nội dung email
            $body = $this->getAdminNotificationTemplate($contactData);
            $this->mailer->Body = $body;
            
            return $this->mailer->send();
            
        } catch (Exception $e) {
            error_log("Lỗi gửi email cho admin: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Gửi email cảm ơn cho khách hàng
     */
    public function sendThankYouToCustomer($contactData) {
        try {
            $this->mailer->clearAllRecipients();
            $this->mailer->clearAttachments();
            
            // Người nhận
            $this->mailer->addAddress($contactData['email'], $contactData['name']);
            
            // Cấu hình email
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Cảm ơn bạn đã liên hệ với ' . COMPANY_SHORT_NAME;
            
            // Nội dung email
            $body = $this->getThankYouTemplate($contactData);
            $this->mailer->Body = $body;
            
            return $this->mailer->send();
            
        } catch (Exception $e) {
            error_log("Lỗi gửi email cảm ơn: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Template email thông báo cho admin
     */
    private function getAdminNotificationTemplate($data) {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Liên hệ mới từ khách hàng</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #FDD835, #FFEB3B); padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #fff; padding: 20px; border: 1px solid #ddd; }
                .info-row { margin-bottom: 15px; }
                .label { font-weight: bold; color: #333; }
                .value { color: #666; }
                .footer { background: #f8f9fa; padding: 15px; text-align: center; border-radius: 0 0 8px 8px; font-size: 12px; color: #666; }
                .priority { background: #ff4444; color: white; padding: 5px 10px; border-radius: 15px; font-size: 12px; display: inline-block; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2 style="margin: 0; color: #333;">🔔 Liên hệ mới từ khách hàng</h2>
                    <span class="priority">ƯU TIÊN CAO</span>
                </div>
                
                <div class="content">
                    <div class="info-row">
                        <span class="label">👤 Họ và tên:</span>
                        <span class="value">' . htmlspecialchars($data['name']) . '</span>
                    </div>
                    
                    <div class="info-row">
                        <span class="label">📧 Email:</span>
                        <span class="value"><a href="mailto:' . htmlspecialchars($data['email']) . '">' . htmlspecialchars($data['email']) . '</a></span>
                    </div>
                    
                    ' . (!empty($data['phone']) ? '
                    <div class="info-row">
                        <span class="label">📞 Số điện thoại:</span>
                        <span class="value"><a href="tel:' . htmlspecialchars($data['phone']) . '">' . htmlspecialchars($data['phone']) . '</a></span>
                    </div>
                    ' : '') . '
                    
                    ' . (!empty($data['subject']) ? '
                    <div class="info-row">
                        <span class="label">🏷️ Chủ đề:</span>
                        <span class="value">' . htmlspecialchars($data['subject']) . '</span>
                    </div>
                    ' : '') . '
                    
                    <div class="info-row">
                        <span class="label">💬 Nội dung:</span>
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 10px;">
                            ' . nl2br(htmlspecialchars($data['message'])) . '
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <span class="label">🕐 Thời gian:</span>
                        <span class="value">' . date('d/m/Y H:i:s') . '</span>
                    </div>
                </div>
                
                <div class="footer">
                    <p style="margin: 0;">
                        <strong>' . COMPANY_NAME . '</strong><br>
                        📍 ' . COMPANY_ADDRESS . '<br>
                        📞 ' . COMPANY_PHONE . ' | 📧 ' . COMPANY_EMAIL . '
                    </p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    /**
     * Template email cảm ơn cho khách hàng
     */
    private function getThankYouTemplate($data) {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Cảm ơn bạn đã liên hệ</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #FDD835, #FFEB3B); padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #fff; padding: 30px; border: 1px solid #ddd; }
                .footer { background: #f8f9fa; padding: 20px; text-align: center; border-radius: 0 0 8px 8px; }
                .contact-info { background: #e3f2fd; padding: 20px; border-radius: 8px; margin: 20px 0; }
                .btn { background: #FDD835; color: #333; padding: 12px 25px; text-decoration: none; border-radius: 25px; display: inline-block; font-weight: bold; margin: 10px 5px; }
                .btn:hover { background: #FFEB3B; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1 style="margin: 0; color: #333;">✨ Cảm ơn bạn đã liên hệ!</h1>
                    <p style="margin: 10px 0 0; color: #666;">Chúng tôi đã nhận được tin nhắn của bạn</p>
                </div>
                
                <div class="content">
                    <p><strong>Xin chào ' . htmlspecialchars($data['name']) . ',</strong></p>
                    
                    <p>Cảm ơn bạn đã quan tâm và gửi thông tin liên hệ đến <strong>' . COMPANY_SHORT_NAME . '</strong>. Chúng tôi đã nhận được tin nhắn của bạn và sẽ phản hồi trong thời gian sớm nhất có thể.</p>
                    
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
                        <h3 style="color: #333; margin-top: 0;">📝 Thông tin bạn đã gửi:</h3>
                        ' . (!empty($data['subject']) ? '<p><strong>Chủ đề:</strong> ' . htmlspecialchars($data['subject']) . '</p>' : '') . '
                        <p><strong>Nội dung:</strong></p>
                        <div style="background: white; padding: 15px; border-radius: 5px; border-left: 4px solid #FDD835;">
                            ' . nl2br(htmlspecialchars($data['message'])) . '
                        </div>
                    </div>
                    
                    <div class="contact-info">
                        <h3 style="color: #333; margin-top: 0;">🚀 Liên hệ nhanh với chúng tôi:</h3>
                        <p style="margin-bottom: 15px;">Để được hỗ trợ nhanh chóng, bạn có thể liên hệ trực tiếp:</p>
                        
                        <div style="text-align: center;">
                            <a href="tel:0971160197" class="btn">📞 Gọi Giám đốc</a>
                            <a href="tel:0379265520" class="btn">📞 Gọi Kinh doanh</a>
                            <a href="https://zalo.me/0971160197" class="btn" target="_blank">💬 Chat Zalo</a>
                        </div>
                    </div>
                    
                    <div style="background: linear-gradient(90deg, #e3f2fd, #f3e5f5); padding: 20px; border-radius: 8px; margin: 20px 0;">
                        <h3 style="color: #333; margin-top: 0;">🎯 Cam kết của chúng tôi:</h3>
                        <ul style="margin: 0; padding-left: 20px;">
                            <li>✅ Phản hồi trong vòng 2 giờ (giờ hành chính)</li>
                            <li>✅ Tư vấn miễn phí và báo giá chi tiết</li>
                            <li>✅ Dịch vụ chuyên nghiệp, uy tín</li>
                            <li>✅ Hỗ trợ 24/7 qua hotline</li>
                        </ul>
                    </div>
                    
                    <p style="text-align: center; font-style: italic; color: #666;">
                        Cảm ơn bạn đã tin tưởng và lựa chọn ' . COMPANY_SHORT_NAME . '!<br>
                        Chúng tôi sẽ liên hệ với bạn sớm nhất có thể.
                    </p>
                </div>
                
                <div class="footer">
                    <p style="margin: 0; font-weight: bold; color: #333;">' . COMPANY_NAME . '</p>
                    <p style="margin: 5px 0;">📍 ' . COMPANY_ADDRESS . '</p>
                    <p style="margin: 5px 0;">📞 ' . COMPANY_PHONE . ' | 📧 ' . COMPANY_EMAIL . '</p>
                    <p style="margin: 10px 0 0; font-size: 12px; color: #999;">
                        Email này được gửi tự động, vui lòng không trả lời email này.
                    </p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
}
?> 