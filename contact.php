<?php
// Include database config
require_once 'database/config.php';
require_once 'includes/email-helper.php';

// Page meta information
$page_title = "Liên hệ";
$page_description = "Liên hệ với VINA LOGISTICS để được tư vấn miễn phí về dịch vụ vận chuyển hàng hóa từ Trung Quốc về Việt Nam.";
$page_keywords = getDynamicKeywords('general') . ', liên hệ, tư vấn, hotline';

// Handle form submission
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $msg_content = trim($_POST['message'] ?? '');
    
    // Basic validation
    if (empty($name) || empty($email) || empty($msg_content)) {
        $message = 'Vui lòng điền đầy đủ thông tin bắt buộc.';
        $message_type = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Email không hợp lệ.';
        $message_type = 'error';
    } else {
        // Save to database
        try {
            $contact_stmt = $pdo->prepare("INSERT INTO contacts (name, email, phone, subject, message) VALUES (:name, :email, :phone, :subject, :message)");
            $contact_stmt->bindParam(':name', $name);
            $contact_stmt->bindParam(':email', $email);
            $contact_stmt->bindParam(':phone', $phone);
            $contact_stmt->bindParam(':subject', $subject);
            $contact_stmt->bindParam(':message', $msg_content);
            
            if ($contact_stmt->execute()) {
                // Prepare contact data for emails
                $contactData = [
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'subject' => $subject,
                    'message' => $msg_content
                ];
                
                // Initialize email helper
                $emailHelper = new EmailHelper();
                
                // Send emails
                $adminEmailSent = false;
                $customerEmailSent = false;
                
                // Send notification to admin
                try {
                    $adminEmailSent = $emailHelper->sendContactNotificationToAdmin($contactData);
                } catch(Exception $e) {
                    error_log("Lỗi gửi email admin: " . $e->getMessage());
                }
                
                // Send thank you email to customer
                try {
                    $customerEmailSent = $emailHelper->sendThankYouToCustomer($contactData);
                } catch(Exception $e) {
                    error_log("Lỗi gửi email khách hàng: " . $e->getMessage());
                }
                
                // Set success message
                if ($adminEmailSent && $customerEmailSent) {
                    $message = 'Cảm ơn bạn đã liên hệ! Chúng tôi đã gửi email xác nhận và sẽ phản hồi trong thời gian sớm nhất.';
                } elseif ($adminEmailSent || $customerEmailSent) {
                    $message = 'Cảm ơn bạn đã liên hệ! Thông tin đã được lưu, chúng tôi sẽ phản hồi sớm nhất có thể.';
                } else {
                    $message = 'Thông tin đã được lưu thành công. Chúng tôi sẽ liên hệ với bạn qua điện thoại trong thời gian sớm nhất.';
                }
                
                $message_type = 'success';
                
                // Clear form data after successful submission
                $name = $email = $phone = $subject = $msg_content = '';
                
            } else {
                $message = 'Có lỗi xảy ra khi lưu thông tin. Vui lòng thử lại sau.';
                $message_type = 'error';
            }
        } catch(PDOException $e) {
            $message = 'Có lỗi xảy ra. Vui lòng thử lại sau.';
            $message_type = 'error';
            error_log("Database error: " . $e->getMessage());
        }
    }
}

// Include header
include 'includes/header.php';
?>

<!-- Show message if any -->
<?php if (!empty($message)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    showNotification('<?php echo addslashes($message); ?>', '<?php echo $message_type; ?>');
});
</script>
<?php endif; ?>

<!-- Breadcrumb -->
<section style="background: var(--bg-light); padding: 60px 0 40px; margin-top: 80px;">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 style="color: var(--text-dark); margin-bottom: 15px;">Liên hệ với chúng tôi</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb" style="background: transparent; padding: 0; margin: 0;">
                        <li class="breadcrumb-item">
                                                            <a href="/" style="color: var(--text-light);">Trang chủ</a>
                        </li>
                        <li class="breadcrumb-item active" style="color: var(--primary-color);">Liên hệ</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>



<!-- Contact Information -->
<section class="section">
    <div class="container">
        <div class="row">
            <!-- Company Info -->
            <div class="col-lg-4 mb-4">
                <div style="background: white; padding: 40px; border-radius: 15px; box-shadow: var(--shadow); height: 100%;">
                    <h3 style="color: var(--text-dark); margin-bottom: 30px;">
                        <i class="fas fa-building" style="color: var(--primary-color); margin-right: 10px;"></i>
                        Thông tin công ty
                    </h3>
                    
                    <div style="margin-bottom: 25px;">
                        <h6 style="color: var(--text-dark); margin-bottom: 10px;">
                            <i class="fas fa-map-marker-alt" style="color: var(--primary-color); margin-right: 10px;"></i>
                            Địa chỉ
                        </h6>
                        <p style="color: var(--text-light); margin: 0; line-height: 1.6;">
                            <?php echo COMPANY_ADDRESS; ?>
                        </p>
                    </div>
                    
                    <div style="margin-bottom: 25px;">
                        <h6 style="color: var(--text-dark); margin-bottom: 10px;">
                            <i class="fas fa-phone" style="color: var(--primary-color); margin-right: 10px;"></i>
                            Hotline
                        </h6>
                        <p style="color: var(--text-light); margin: 0;">
                            <a href="tel:<?php echo COMPANY_PHONE; ?>" style="color: var(--primary-color); text-decoration: none; font-weight: 600;">
                                <?php echo COMPANY_PHONE; ?>
                            </a>
                        </p>
                    </div>
                    
                    <div style="margin-bottom: 25px;">
                        <h6 style="color: var(--text-dark); margin-bottom: 10px;">
                            <i class="fas fa-envelope" style="color: var(--primary-color); margin-right: 10px;"></i>
                            Email
                        </h6>
                        <p style="color: var(--text-light); margin: 0;">
                            <a href="mailto:<?php echo COMPANY_EMAIL; ?>" style="color: var(--primary-color); text-decoration: none;">
                                <?php echo COMPANY_EMAIL; ?>
                            </a>
                        </p>
                    </div>
                    
                                         <div style="margin-bottom: 25px;">
                         <h6 style="color: var(--text-dark); margin-bottom: 10px;">
                             <i class="fas fa-clock" style="color: var(--primary-color); margin-right: 10px;"></i>
                             Giờ làm việc
                         </h6>
                         <p style="color: var(--text-light); margin: 0; line-height: 1.6;">
                             Thứ 2 - Thứ 7: 8:00 - 17:00<br>
                             Chủ nhật: Nghỉ
                         </p>
                     </div>
                    
                    <div>
                        <h6 style="color: var(--text-dark); margin-bottom: 15px;">
                            <i class="fas fa-certificate" style="color: var(--primary-color); margin-right: 10px;"></i>
                            Giấy phép kinh doanh
                        </h6>
                        <p style="color: var(--text-light); margin: 0; line-height: 1.6;">
                            Số: <?php echo COMPANY_LICENSE; ?><br>
                            <small style="color: var(--text-light); opacity: 0.8;">Đăng ký lần đầu: 16/01/2024</small>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Director Contact -->
            <div class="col-lg-4 mb-4">
                <div style="background: white; padding: 40px; border-radius: 15px; box-shadow: var(--shadow); height: 100%;">
                    <h3 style="color: var(--text-dark); margin-bottom: 30px;">
                        <i class="fas fa-user-tie" style="color: var(--primary-color); margin-right: 10px;"></i>
                        Giám đốc
                    </h3>
                    
                    <div style="margin-bottom: 25px;">
                        <h6 style="color: var(--text-dark); margin-bottom: 10px;">
                            <i class="fas fa-user" style="color: var(--primary-color); margin-right: 10px;"></i>
                            Họ và tên
                        </h6>
                        <p style="color: var(--text-light); margin: 0; font-weight: 600;">
                            Nguyễn Thế Trường
                        </p>
                        <p style="color: var(--text-light); margin: 5px 0 0; font-size: 0.9rem;">
                            Giám đốc điều hành
                        </p>
                    </div>
                    
                    <div style="margin-bottom: 25px;">
                        <h6 style="color: var(--text-dark); margin-bottom: 10px;">
                            <i class="fas fa-phone" style="color: var(--primary-color); margin-right: 10px;"></i>
                            Điện thoại
                        </h6>
                        <p style="color: var(--text-light); margin: 0;">
                            <a href="tel:0971160197" style="color: var(--primary-color); text-decoration: none; font-weight: 600;">
                                0971.160.197
                            </a>
                        </p>
                    </div>
                    
                    <div style="margin-bottom: 25px;">
                        <h6 style="color: var(--text-dark); margin-bottom: 10px;">
                            <i class="fas fa-comment" style="color: var(--primary-color); margin-right: 10px;"></i>
                            Zalo
                        </h6>
                        <p style="color: var(--text-light); margin: 0;">
                            <a href="https://zalo.me/0971160197" target="_blank" style="color: var(--primary-color); text-decoration: none;">
                                Chat Zalo
                            </a>
                        </p>
                    </div>
                    
                    <div>
                        <h6 style="color: var(--text-dark); margin-bottom: 10px;">
                            <i class="fas fa-envelope" style="color: var(--primary-color); margin-right: 10px;"></i>
                            Email
                        </h6>
                        <p style="color: var(--text-light); margin: 0;">
                            <a href="mailto:truongvinagroup@gmail.com" style="color: var(--primary-color); text-decoration: none;">
                                truongvinagroup@gmail.com
                            </a>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Sales Manager Contact -->
            <div class="col-lg-4 mb-4">
                <div style="background: white; padding: 40px; border-radius: 15px; box-shadow: var(--shadow); height: 100%;">
                    <h3 style="color: var(--text-dark); margin-bottom: 30px;">
                        <i class="fas fa-user-tie" style="color: var(--primary-color); margin-right: 10px;"></i>
                        Trưởng phòng KD
                    </h3>
                    
                    <div style="margin-bottom: 25px;">
                        <h6 style="color: var(--text-dark); margin-bottom: 10px;">
                            <i class="fas fa-user" style="color: var(--primary-color); margin-right: 10px;"></i>
                            Họ và tên
                        </h6>
                        <p style="color: var(--text-light); margin: 0; font-weight: 600;">
                            Tống Thị Trang
                        </p>
                        <p style="color: var(--text-light); margin: 5px 0 0; font-size: 0.9rem;">
                            Trưởng phòng kinh doanh
                        </p>
                    </div>
                    
                    <div style="margin-bottom: 25px;">
                        <h6 style="color: var(--text-dark); margin-bottom: 10px;">
                            <i class="fas fa-phone" style="color: var(--primary-color); margin-right: 10px;"></i>
                            Điện thoại
                        </h6>
                        <p style="color: var(--text-light); margin: 0;">
                            <a href="tel:0379265520" style="color: var(--primary-color); text-decoration: none; font-weight: 600;">
                                0379.265.520
                            </a>
                        </p>
                    </div>
                    
                    <div style="margin-bottom: 25px;">
                        <h6 style="color: var(--text-dark); margin-bottom: 10px;">
                            <i class="fas fa-comment" style="color: var(--primary-color); margin-right: 10px;"></i>
                            Zalo
                        </h6>
                        <p style="color: var(--text-light); margin: 0;">
                            <a href="https://zalo.me/0379265520" target="_blank" style="color: var(--primary-color); text-decoration: none;">
                                Chat Zalo
                            </a>
                        </p>
                    </div>
                    
                    <div>
                        <h6 style="color: var(--text-dark); margin-bottom: 10px;">
                            <i class="fas fa-envelope" style="color: var(--primary-color); margin-right: 10px;"></i>
                            Email
                        </h6>
                        <p style="color: var(--text-light); margin: 0;">
                            <a href="mailto:tongthitrang.vinalogistics@gmail.com" style="color: var(--primary-color); text-decoration: none;">
                                tongthitrang.vinalogistics@gmail.com
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form & FAQ -->
<section class="section" style="background: var(--bg-light);">
    <div class="container">
        <div class="section-title">
            <h2>Liên hệ & Hỗ trợ</h2>
            <p>Gửi tin nhắn cho chúng tôi hoặc tìm câu trả lời nhanh chóng</p>
        </div>
        
        <div class="row">
            <!-- Contact Form -->
            <div class="col-lg-8 mb-4">
                <div style="background: white; padding: 40px; border-radius: 15px; box-shadow: var(--shadow);">
                    <h3 style="color: var(--text-dark); margin-bottom: 25px;">
                        <i class="fas fa-paper-plane" style="color: var(--primary-color); margin-right: 10px;"></i>
                        Gửi tin nhắn cho chúng tôi
                    </h3>
                    
                    <form id="contact-form" method="POST" action="">
                        <div class="row">
                                                    <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Họ và tên *</label>
                                <input type="text" id="name" name="name" class="form-control" 
                                       value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" class="form-control" 
                                       value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                            </div>
                        </div>
                        </div>
                        
                        <div class="row">
                                                    <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Số điện thoại</label>
                                <input type="tel" id="phone" name="phone" class="form-control" 
                                       value="<?php echo htmlspecialchars($phone ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="subject">Chủ đề</label>
                                <select id="subject" name="subject" class="form-control">
                                    <option value="">-- Chọn chủ đề --</option>
                                    <option value="Tư vấn dịch vụ" <?php echo ($subject == 'Tư vấn dịch vụ') ? 'selected' : ''; ?>>Tư vấn dịch vụ</option>
                                    <option value="Vận chuyển đường bộ" <?php echo ($subject == 'Vận chuyển đường bộ') ? 'selected' : ''; ?>>Vận chuyển đường bộ</option>
                                    <option value="Nhập khẩu ủy thác" <?php echo ($subject == 'Nhập khẩu ủy thác') ? 'selected' : ''; ?>>Nhập khẩu ủy thác</option>
                                    <option value="Vận chuyển đường biển" <?php echo ($subject == 'Vận chuyển đường biển') ? 'selected' : ''; ?>>Vận chuyển đường biển</option>
                                    <option value="Vận chuyển xách tay nhanh" <?php echo ($subject == 'Vận chuyển xách tay nhanh') ? 'selected' : ''; ?>>Vận chuyển xách tay nhanh</option>
                                    <option value="Đặt hàng Trung Quốc" <?php echo ($subject == 'Đặt hàng Trung Quốc') ? 'selected' : ''; ?>>Đặt hàng Trung Quốc</option>
                                    <option value="Khiếu nại" <?php echo ($subject == 'Khiếu nại') ? 'selected' : ''; ?>>Khiếu nại</option>
                                    <option value="Khác" <?php echo ($subject == 'Khác') ? 'selected' : ''; ?>>Khác</option>
                                </select>
                            </div>
                        </div>
                        </div>
                        
                                            <div class="form-group">
                        <label for="message">Nội dung *</label>
                        <textarea id="message" name="message" class="form-control" rows="6" 
                                  placeholder="Mô tả chi tiết nhu cầu của bạn..." required><?php echo htmlspecialchars($msg_content ?? ''); ?></textarea>
                    </div>
                        
                        <div style="text-align: center; margin-top: 30px;">
                            <button type="submit" class="btn btn-primary" style="padding: 15px 40px;">
                                <i class="fas fa-paper-plane"></i> Gửi tin nhắn
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- FAQ Sidebar -->
            <div class="col-lg-4 mb-4">
                <div style="background: white; border-radius: 15px; box-shadow: var(--shadow); overflow: hidden;">
                    <div style="background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); padding: 25px; text-align: center;">
                        <h4 style="color: var(--text-dark); margin: 0;">
                            <i class="fas fa-question-circle" style="margin-right: 10px;"></i>
                            Câu hỏi thường gặp
                        </h4>
                        <p style="color: var(--text-dark); margin: 10px 0 0; opacity: 0.8; font-size: 0.9rem;">
                            Tìm câu trả lời nhanh chóng
                        </p>
                    </div>
                    
                    <!-- FAQ Items -->
                    <div style="padding: 0;">
                                                 <!-- FAQ Item 1 -->
                         <div style="border-bottom: 1px solid var(--border-color);">
                             <div onclick="toggleFAQ(1)" 
                                  style="padding: 20px 25px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; background: white;">
                                 <h6 style="margin: 0; color: var(--text-dark); font-size: 0.9rem;">Thời gian vận chuyển từ TQ về VN?</h6>
                                 <i id="faq-icon-1" class="fas fa-plus" style="color: var(--primary-color); transition: transform 0.3s ease; font-size: 0.8rem;"></i>
                             </div>
                             <div id="faq-content-1" style="max-height: 0; overflow: hidden; transition: all 0.3s ease;">
                                 <div style="padding: 0 25px 20px 25px; color: var(--text-light); line-height: 1.6; font-size: 0.85rem;">
                                     <strong>Đường bộ:</strong> 3-5 ngày<br>
                                     <strong>Đường biển:</strong> 15-25 ngày<br>
                                     <strong>Xách tay nhanh:</strong> 2-3 ngày (linh hoạt theo chuyến bay)
                                 </div>
                             </div>
                         </div>
                         
                         <!-- FAQ Item 2 -->
                         <div style="border-bottom: 1px solid var(--border-color);">
                             <div onclick="toggleFAQ(2)" 
                                  style="padding: 20px 25px; cursor: pointer; display: flex; justify-content: space-between; align-items: center;">
                                 <h6 style="margin: 0; color: var(--text-dark); font-size: 0.9rem;">Chi phí vận chuyển từ 1%?</h6>
                                 <i id="faq-icon-2" class="fas fa-plus" style="color: var(--primary-color); transition: transform 0.3s ease; font-size: 0.8rem;"></i>
                             </div>
                             <div id="faq-content-2" style="max-height: 0; overflow: hidden; transition: all 0.3s ease;">
                                 <div style="padding: 0 25px 20px 25px; color: var(--text-light); line-height: 1.6; font-size: 0.85rem;">
                                     Chúng tôi cam kết cước phí từ 1% tính trên giá trị hàng hóa. Chi phí tùy thuộc: trọng lượng, kích thước, 
                                     phương thức vận chuyển. Miễn phí tư vấn và báo giá chi tiết.
                                 </div>
                             </div>
                         </div>
                         
                         <!-- FAQ Item 3 -->
                         <div style="border-bottom: 1px solid var(--border-color);">
                             <div onclick="toggleFAQ(3)" 
                                  style="padding: 20px 25px; cursor: pointer; display: flex; justify-content: space-between; align-items: center;">
                                 <h6 style="margin: 0; color: var(--text-dark); font-size: 0.9rem;">Dịch vụ xách tay và đặt hàng?</h6>
                                 <i id="faq-icon-3" class="fas fa-plus" style="color: var(--primary-color); transition: transform 0.3s ease; font-size: 0.8rem;"></i>
                             </div>
                             <div id="faq-content-3" style="max-height: 0; overflow: hidden; transition: all 0.3s ease;">
                                 <div style="padding: 0 25px 20px 25px; color: var(--text-light); line-height: 1.6; font-size: 0.85rem;">
                                     <strong>Xách tay nhanh:</strong> 2 chiều TQ-VN, vận chuyển cá nhân an toàn.<br>
                                     <strong>Đặt hàng:</strong> Hỗ trợ từ Taobao, 1688, Tmall, thanh toán Wechat/Alipay, kiểm tra chất lượng.
                                 </div>
                             </div>
                         </div>
                         
                         <!-- FAQ Item 4 -->
                         <div style="border-bottom: 1px solid var(--border-color);">
                             <div onclick="toggleFAQ(4)" 
                                  style="padding: 20px 25px; cursor: pointer; display: flex; justify-content: space-between; align-items: center;">
                                 <h6 style="margin: 0; color: var(--text-dark); font-size: 0.9rem;">Nhập khẩu ủy thác trọn gói?</h6>
                                 <i id="faq-icon-4" class="fas fa-plus" style="color: var(--primary-color); transition: transform 0.3s ease; font-size: 0.8rem;"></i>
                             </div>
                             <div id="faq-content-4" style="max-height: 0; overflow: hidden; transition: all 0.3s ease;">
                                 <div style="padding: 0 25px 20px 25px; color: var(--text-light); line-height: 1.6; font-size: 0.85rem;">
                                     Dịch vụ nhập khẩu A-Z: tư vấn thuế, giấy tờ pháp lý đầy đủ, hóa đơn VAT, 
                                     hỗ trợ thủ tục hải quan. Tiết kiệm thời gian và chi phí cho doanh nghiệp.
                                 </div>
                             </div>
                         </div>
                         
                         <!-- FAQ Item 5 -->
                         <div>
                             <div onclick="toggleFAQ(5)" 
                                  style="padding: 20px 25px; cursor: pointer; display: flex; justify-content: space-between; align-items: center;">
                                 <h6 style="margin: 0; color: var(--text-dark); font-size: 0.9rem;">Hàng có được bảo hiểm 100%?</h6>
                                 <i id="faq-icon-5" class="fas fa-plus" style="color: var(--primary-color); transition: transform 0.3s ease; font-size: 0.8rem;"></i>
                             </div>
                             <div id="faq-content-5" style="max-height: 0; overflow: hidden; transition: all 0.3s ease;">
                                 <div style="padding: 0 25px 20px 25px; color: var(--text-light); line-height: 1.6; font-size: 0.85rem;">
                                     Cam kết bảo hiểm 100% giá trị hàng hóa. Mã tracking theo dõi real-time, 
                                     cập nhật tình trạng qua Zalo/SMS. Đền bù đầy đủ nếu thất lạc/hư hỏng.
                                 </div>
                             </div>
                         </div>
                    </div>
                    
                    <!-- Contact CTA -->
                    <div style="padding: 25px; background: var(--bg-light); text-align: center;">
                        <p style="margin: 0 0 15px; color: var(--text-dark); font-weight: 600;">
                            Không tìm thấy câu trả lời?
                        </p>
                        <div style="display: flex; gap: 10px; justify-content: center;">
                            <a href="tel:<?php echo str_replace([' ', '.', '-'], '', COMPANY_PHONE); ?>" 
                               style="flex: 1; background: var(--primary-color); color: var(--text-dark); 
                                      padding: 8px 12px; border-radius: 8px; text-decoration: none; 
                                      font-size: 0.85rem; font-weight: 600; text-align: center;">
                                <i class="fas fa-phone" style="margin-right: 5px;"></i>
                                Gọi ngay
                            </a>
                            <a href="https://zalo.me/0971160197" target="_blank"
                               style="flex: 1; background: #0068ff; color: white; 
                                      padding: 8px 12px; border-radius: 8px; text-decoration: none; 
                                      font-size: 0.85rem; font-weight: 600; text-align: center;">
                                <i class="fas fa-comment" style="margin-right: 5px;"></i>
                                Chat Zalo
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



<script>
function toggleFAQ(index) {
    const content = document.getElementById(`faq-content-${index}`);
    const icon = document.getElementById(`faq-icon-${index}`);
    
    if (content.style.maxHeight === '0px' || content.style.maxHeight === '') {
        content.style.maxHeight = content.scrollHeight + 'px';
        icon.style.transform = 'rotate(45deg)';
        icon.className = 'fas fa-times';
    } else {
        content.style.maxHeight = '0px';
        icon.style.transform = 'rotate(0deg)';
        icon.className = 'fas fa-plus';
    }
}
</script>

<?php include 'includes/footer.php'; ?> 