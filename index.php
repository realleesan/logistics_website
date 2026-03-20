<?php
// Include database config
require_once 'database/config.php';
require_once 'includes/email-helper.php';

// Page meta information
$page_title = "Trang chủ";
$page_description = "VINA LOGISTICS - Dịch vụ vận chuyển hàng hóa uy tín, chuyên nghiệp từ Trung Quốc về Việt Nam. Nhập khẩu ủy thác, mua hàng Taobao, 1688 với chi phí thấp nhất.";
$page_keywords = getDynamicKeywords('general');

// Handle quick contact form submission
$contact_message = '';
$contact_message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['quick_contact'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $msg_content = trim($_POST['message'] ?? '');
    
    // Basic validation
    if (empty($name) || empty($email) || empty($msg_content)) {
        $contact_message = 'Vui lòng điền đầy đủ thông tin bắt buộc.';
        $contact_message_type = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $contact_message = 'Email không hợp lệ.';
        $contact_message_type = 'error';
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
                    error_log("Lỗi gửi email admin từ index: " . $e->getMessage());
                }
                
                // Send thank you email to customer
                try {
                    $customerEmailSent = $emailHelper->sendThankYouToCustomer($contactData);
                } catch(Exception $e) {
                    error_log("Lỗi gửi email khách hàng từ index: " . $e->getMessage());
                }
                
                // Set success message
                if ($adminEmailSent && $customerEmailSent) {
                    $contact_message = 'Cảm ơn bạn đã liên hệ! Chúng tôi đã gửi email xác nhận và sẽ phản hồi trong thời gian sớm nhất.';
                } elseif ($adminEmailSent || $customerEmailSent) {
                    $contact_message = 'Cảm ơn bạn đã liên hệ! Thông tin đã được lưu, chúng tôi sẽ phản hồi sớm nhất có thể.';
                } else {
                    $contact_message = 'Thông tin đã được lưu thành công. Chúng tôi sẽ liên hệ với bạn qua điện thoại trong thời gian sớm nhất.';
                }
                
                $contact_message_type = 'success';
                
                // Clear form data after successful submission
                $name = $email = $phone = $subject = $msg_content = '';
                
            } else {
                $contact_message = 'Có lỗi xảy ra khi lưu thông tin. Vui lòng thử lại sau.';
                $contact_message_type = 'error';
            }
        } catch(PDOException $e) {
            $contact_message = 'Có lỗi xảy ra. Vui lòng thử lại sau.';
            $contact_message_type = 'error';
            error_log("Database error in index: " . $e->getMessage());
        }
    }
}

// Get latest services (6 services) - ordered by configurable sort_order
try {
    $services_stmt = $pdo->prepare("SELECT * FROM services WHERE status = 'active' ORDER BY sort_order ASC, created_at DESC LIMIT 6");
    $services_stmt->execute();
    $services = $services_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $services = [];
}

// Get latest news (3 articles)
try {
    $news_stmt = $pdo->prepare("SELECT * FROM news WHERE status = 'published' ORDER BY created_at DESC LIMIT 3");
    $news_stmt->execute();
    $news_articles = $news_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $news_articles = [];
}

// Include header
include 'includes/header.php';
?>

<!-- Show contact message if any -->
<?php if (!empty($contact_message)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    showNotification('<?php echo addslashes($contact_message); ?>', '<?php echo $contact_message_type; ?>');
});
</script>
<?php endif; ?>

<!-- Hero Section with Slider -->
<section class="hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1>
                    <span class="hero-highlight"><?php echo COMPANY_SHORT_NAME; ?></span><br>
                    Vận chuyển hàng hóa <br>
                    <span class="hero-highlight">Mua Niềm Tin - Nhận Sự Hài Lòng</span>
                </h1>
            <p>
                    Chúng tôi cung cấp dịch vụ vận chuyển hàng hóa chuyên nghiệp từ Trung Quốc về Việt Nam với 
                    chi phí tối ưu, thời gian nhanh chóng và đảm bảo an toàn 100%.
                </p>
                <div style="margin-top: 30px;">
                    <a href="lien-he" class="btn btn-primary" style="margin-right: 15px;">
                        <i class="fas fa-phone"></i> Liên hệ ngay
                    </a>
                    <a href="dich-vu" class="btn btn-outline">
                        <i class="fas fa-truck"></i> Xem dịch vụ
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div style="text-align: center; position: relative;">
                    <!-- Hero Image -->
                    <div style="position: relative; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                        <img src="<?php echo asset_url('assets/images/index1.jpg'); ?>" alt="Vận chuyển hàng hóa" 
                             style="width: 100%; height: 400px; object-fit: cover;">
                    </div>
                    
                    <!-- Floating service icons -->
                    <div style="position: absolute; top: 20px; right: 20px; display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
                        <div style="background: rgba(255,255,255,0.9); padding: 15px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                            <i class="fas fa-plane" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                        </div>
                        <div style="background: rgba(255,255,255,0.9); padding: 15px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                            <i class="fas fa-ship" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                        </div>
                        <div style="background: rgba(255,255,255,0.9); padding: 15px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                            <i class="fas fa-truck" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                        </div>
                        <div style="background: rgba(255,255,255,0.9); padding: 15px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                            <i class="fas fa-train" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Company Introduction -->
<section class="section" style="background: var(--bg-light);">
    <div class="container">
        <div class="section-title">
            <h2>Giới thiệu <?php echo COMPANY_SHORT_NAME; ?></h2>
            <p>Với hơn 5 năm kinh nghiệm trong lĩnh vực logistics, chúng tôi tự hào là đối tác đáng tin cậy</p>
        </div>
        
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4">
                <div style="background: white; padding: 40px; border-radius: 15px; box-shadow: var(--shadow);">
                    <h3 style="color: var(--text-dark); margin-bottom: 20px;">
                        <i class="fas fa-award" style="color: var(--primary-color); margin-right: 10px;"></i>
                        Tại sao chọn chúng tôi?
                    </h3>
                    
                    <div style="margin-bottom: 20px;">
                        <div style="display: flex; align-items: center; margin-bottom: 15px;">
                            <div style="width: 50px; height: 50px; background: var(--primary-color); border-radius: 50%; 
                                        display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                <i class="fas fa-clock" style="color: var(--text-dark); font-size: 1.2rem;"></i>
                            </div>
                            <div>
                                <h6 style="margin: 0; color: var(--text-dark);">Giao hàng nhanh chóng</h6>
                                <p style="margin: 0; color: var(--text-light); font-size: 0.9rem;">Thời gian vận chuyển tối ưu</p>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: center; margin-bottom: 15px;">
                            <div style="width: 50px; height: 50px; background: var(--primary-color); border-radius: 50%; 
                                        display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                <i class="fas fa-shield-alt" style="color: var(--text-dark); font-size: 1.2rem;"></i>
                            </div>
                            <div>
                                <h6 style="margin: 0; color: var(--text-dark);">Chính sách bảo hiểm</h6>
                                <p style="margin: 0; color: var(--text-light); font-size: 0.9rem;">Đền bù khi hàng hóa hư hỏng + thất lạc</p>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: center; margin-bottom: 15px;">
                            <div style="width: 50px; height: 50px; background: var(--primary-color); border-radius: 50%; 
                                        display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                <i class="fas fa-dollar-sign" style="color: var(--text-dark); font-size: 1.2rem;"></i>
                            </div>
                            <div>
                                <h6 style="margin: 0; color: var(--text-dark);">Chi phí tối ưu nhất</h6>
                                <p style="margin: 0; color: var(--text-light); font-size: 0.9rem;">Phí dịch vụ từ 1%, hỗ trợ đàm phán</p>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: center; margin-bottom: 15px;">
                            <div style="width: 50px; height: 50px; background: var(--primary-color); border-radius: 50%; 
                                        display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                <i class="fas fa-headset" style="color: var(--text-dark); font-size: 1.2rem;"></i>
                            </div>
                            <div>
                                <h6 style="margin: 0; color: var(--text-dark);">Hỗ trợ 24/7</h6>
                                <p style="margin: 0; color: var(--text-light); font-size: 0.9rem;">Đội ngũ chăm sóc khách hàng tận tâm</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <!-- Company Image -->
                <div style="position: relative; border-radius: 15px; overflow: hidden; box-shadow: var(--shadow); margin-bottom: 30px;">
                    <img src="<?php echo asset_url('assets/images/index2.png'); ?>" alt="Kho hàng công ty" 
                         style="width: 100%; height: 300px; object-fit: cover;">
                </div>
                
                <!-- Statistics -->
                <div class="row">
                    <div class="col-6 mb-3">
                        <div style="background: white; padding: 30px 20px; border-radius: 15px; box-shadow: var(--shadow); text-align: center;">
                            <div style="font-size: 2.5rem; font-weight: 700; color: var(--primary-color); margin-bottom: 10px;">
                                <span class="counter" data-target="5">0</span>+
                            </div>
                            <p style="margin: 0; color: var(--text-dark); font-weight: 600;">Năm kinh nghiệm</p>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div style="background: white; padding: 30px 20px; border-radius: 15px; box-shadow: var(--shadow); text-align: center;">
                            <div style="font-size: 2.5rem; font-weight: 700; color: var(--primary-color); margin-bottom: 10px;">
                                <span class="counter" data-target="1500">0</span>+
                            </div>
                            <p style="margin: 0; color: var(--text-dark); font-weight: 600;">Khách hàng</p>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div style="background: white; padding: 30px 20px; border-radius: 15px; box-shadow: var(--shadow); text-align: center;">
                            <div style="font-size: 2.5rem; font-weight: 700; color: var(--primary-color); margin-bottom: 10px;">
                                <span class="counter" data-target="20000">0</span>+
                            </div>
                            <p style="margin: 0; color: var(--text-dark); font-weight: 600;">Đơn hàng</p>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div style="background: white; padding: 30px 20px; border-radius: 15px; box-shadow: var(--shadow); text-align: center;">
                            <div style="font-size: 2.5rem; font-weight: 700; color: var(--primary-color); margin-bottom: 10px;">
                                <span class="counter" data-target="98">0</span>%
                            </div>
                            <p style="margin: 0; color: var(--text-dark); font-weight: 600;">Hài lòng</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Dịch vụ của chúng tôi</h2>
            <p>Giải pháp vận chuyển hàng hóa chuyên nghiệp và uy tín với chi phí tối ưu</p>
        </div>
        
        <div class="row">
            <?php if (!empty($services)): ?>
                <?php foreach ($services as $service): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="service-card">
                        <div class="service-image">
                            <img src="<?php echo !empty($service['image']) ? $service['image'] : 'assets/images/service-' . strtolower(str_replace(' ', '-', $service['title'])) . '.jpg'; ?>" 
                                 alt="<?php echo htmlspecialchars($service['title']); ?>">
                        </div>
                        <div class="service-icon">
                            <i class="<?php echo htmlspecialchars($service['icon']); ?>"></i>
                        </div>
                        <h4><?php echo htmlspecialchars($service['title']); ?></h4>
                        <p><?php echo htmlspecialchars($service['description']); ?></p>
                        <a href="dich-vu" class="btn btn-outline btn-sm">
                            <i class="fas fa-arrow-right"></i> Xem chi tiết
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="dich-vu" class="btn btn-primary">
                <i class="fas fa-list"></i> Xem tất cả dịch vụ
            </a>
        </div>
    </div>
</section>

<!-- News Section -->
<section class="section" style="background: var(--bg-light);">
    <div class="container">
        <div class="section-title">
            <h2>Tin tức mới</h2>
            <p>Cập nhật những thông tin mới nhất về dịch vụ và chính sách vận chuyển</p>
        </div>
        
        <div class="row">
            <?php if (!empty($news_articles)): ?>
                <?php foreach ($news_articles as $article): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="news-card">
                        <div class="news-image">
                            <?php
                                $candidateImage = !empty($article['image']) ? $article['image'] : '';
                                $serverPath = $candidateImage ? __DIR__ . '/' . ltrim($candidateImage, '/\\') : '';
                                $resolvedImage = ($candidateImage && file_exists($serverPath)) ? $candidateImage : 'assets/images/index9.png';
                            ?>
                            <img src="<?php echo asset_url($resolvedImage); ?>" 
                                 alt="<?php echo htmlspecialchars($article['title']); ?>">
                        </div>
                        <div class="news-content">
                            <div class="news-date">
                                <i class="fas fa-calendar"></i>
                                <?php echo date('d/m/Y', strtotime($article['created_at'])); ?>
                            </div>
                            <h5 class="news-title">
                                <a href="/tin-tuc/<?php echo htmlspecialchars($article['slug']); ?>" style="color: inherit; text-decoration: none;">
                                    <?php echo htmlspecialchars($article['title']); ?>
                                </a>
                            </h5>
                            <p class="news-excerpt">
                                <?php echo htmlspecialchars(substr($article['excerpt'] ?: strip_tags($article['content']), 0, 120)) . '...'; ?>
                            </p>
                            <a href="/tin-tuc/<?php echo htmlspecialchars($article['slug']); ?>" class="btn btn-outline btn-sm">
                                <i class="fas fa-arrow-right"></i> Đọc thêm
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Default news nếu database chưa có dữ liệu -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="news-card">
                        <div class="news-image">
                            <img src="<?php echo asset_url('assets/images/news-default.jpg'); ?>" alt="Tin tức mặc định">
                        </div>
                        <div class="news-content">
                            <div class="news-date">
                                <i class="fas fa-calendar"></i>
                                <?php echo date('d/m/Y'); ?>
                            </div>
                            <h5 class="news-title">Ưu đãi đặc biệt cuối năm</h5>
                            <p class="news-excerpt">
                                Chương trình ưu đãi với mức giảm giá lên đến 20% phí vận chuyển cho tất cả dịch vụ...
                            </p>
                            <a href="tin-tuc" class="btn btn-outline btn-sm">
                                <i class="fas fa-arrow-right"></i> Đọc thêm
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-4">
                                <a href="tin-tuc" class="btn btn-primary">
                        <i class="fas fa-newspaper"></i> Xem tất cả tin tức
                    </a>
        </div>
    </div>
</section>

<!-- Quick Contact Form -->
<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Liên hệ nhanh</h2>
            <p>Để lại thông tin để nhận tư vấn miễn phí từ chuyên gia của chúng tôi</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <form id="quick-contact-form" class="contact-form" method="POST" action="">
                    <input type="hidden" name="quick_contact" value="1">
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
                                    <option value="Tư vấn dịch vụ" <?php echo (($subject ?? '') == 'Tư vấn dịch vụ') ? 'selected' : ''; ?>>Tư vấn dịch vụ</option>
                                    <option value="Báo giá vận chuyển" <?php echo (($subject ?? '') == 'Báo giá vận chuyển') ? 'selected' : ''; ?>>Báo giá vận chuyển</option>
                                    <option value="Nhập khẩu ủy thác" <?php echo (($subject ?? '') == 'Nhập khẩu ủy thác') ? 'selected' : ''; ?>>Nhập khẩu ủy thác</option>
                                    <option value="Mua hàng Trung Quốc" <?php echo (($subject ?? '') == 'Mua hàng Trung Quốc') ? 'selected' : ''; ?>>Mua hàng Trung Quốc</option>
                                    <option value="Khác" <?php echo (($subject ?? '') == 'Khác') ? 'selected' : ''; ?>>Khác</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Nội dung *</label>
                        <textarea id="message" name="message" class="form-control" rows="5" 
                                  placeholder="Mô tả chi tiết nhu cầu của bạn..." required><?php echo htmlspecialchars($msg_content ?? ''); ?></textarea>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Gửi yêu cầu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?> 