<?php
// Include database config
require_once 'database/config.php';

// Page meta information
$page_title = "Dịch vụ";
$page_description = "Các dịch vụ vận chuyển hàng hóa chuyên nghiệp của VINA LOGISTICS: vận chuyển hàng không, đường biển, đường bộ, nhập khẩu ủy thác, mua hàng Trung Quốc.";
$page_keywords = getDynamicKeywords('services');

// Get all services - ordered by configurable sort_order
try {
    $services_stmt = $pdo->prepare("SELECT * FROM services WHERE status = 'active' ORDER BY sort_order ASC, created_at DESC");
    $services_stmt->execute();
    $services = $services_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $services = [];
}

// Include header
include 'includes/header.php';
?>

<!-- Breadcrumb -->
<section style="background: var(--bg-light); padding: 60px 0 40px; margin-top: 80px;">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 style="color: var(--text-dark); margin-bottom: 15px;">Dịch vụ của chúng tôi</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb" style="background: transparent; padding: 0; margin: 0;">
                        <li class="breadcrumb-item">
                                                            <a href="/" style="color: var(--text-light);">Trang chủ</a>
                        </li>
                        <li class="breadcrumb-item active" style="color: var(--primary-color);">Dịch vụ</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Services Overview -->
<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Giải pháp vận chuyển toàn diện</h2>
            <p>Chúng tôi cung cấp đa dạng các dịch vụ logistics chuyên nghiệp với chi phí tối ưu và thời gian nhanh chóng</p>
        </div>
        
        <div class="row">
            <?php if (!empty($services)): ?>
                <?php foreach ($services as $index => $service): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="service-card">
                        <div class="service-image">
                            <img src="<?php echo asset_url(!empty($service['image']) ? $service['image'] : 'assets/images/service-' . strtolower(str_replace(' ', '-', $service['title'])) . '.jpg'); ?>" 
                                 alt="<?php echo htmlspecialchars($service['title']); ?>"
                                  onerror="this.src='<?php echo asset_url('assets/images/service-default.svg'); ?>'">
                        </div>
                        <div class="service-icon">
                            <i class="<?php echo htmlspecialchars($service['icon']); ?>"></i>
                        </div>
                        <h4><?php echo htmlspecialchars($service['title']); ?></h4>
                        <p><?php echo htmlspecialchars($service['description']); ?></p>
                        
                        <!-- Service features list -->
                        <div style="text-align: left; margin: 20px 0;">
                            <?php
                            // Build dynamic features for each service
                            $features = [];
                            $title_lower = strtolower($service['title']);

                            // 1) Prefer features written in short_description (each line or separated by ;, |, •)
                            if (!empty($service['short_description'])) {
                                $raw_short = strip_tags($service['short_description']);
                                // Split by new lines, semicolon, bullet char (•) or pipe
                                $parts = preg_split("/\r\n|\r|\n|;|•|\\|/u", $raw_short);
                                if (is_array($parts)) {
                                    foreach ($parts as $p) {
                                        $p = trim($p);
                                        if ($p !== '') { $features[] = $p; }
                                    }
                                }
                            }

                            // 2) If still empty, try extracting <li> items from rich content
                            if (count($features) === 0 && !empty($service['content'])) {
                                if (preg_match_all('/<li[^>]*>(.*?)<\/li>/si', $service['content'], $m)) {
                                    foreach ($m[1] as $li) {
                                        $text = trim(strip_tags($li));
                                        if ($text !== '') { $features[] = $text; }
                                    }
                                }
                            }

                            // 3) Fallback: previous static defaults based on title keywords
                            if (count($features) === 0) {
                                if (strpos($title_lower, 'xách tay') !== false) {
                                    $features = [
                                        'Thời gian nhanh nhất: 24 - 36h sau khi ký nhận',
                                        'Phù hợp hàng nhỏ, giá trị cao',
                                        'Quy trình xử lý nhanh gọn, chính xác và cấp thiết',
                                        'Chính sách bảo hiểm 100% giá trị hàng hoá'
                                    ];
                                } elseif (strpos($title_lower, 'đường biển') !== false) {
                                    $features = [
                                        'Chi phí tối ưu nhất',
                                        'Phù hợp hàng khối lượng lớn',
                                        'Thời gian: 15-25 ngày',
                                        'Dịch vụ FCL và LCL'
                                    ];
                                } elseif (strpos($title_lower, 'đường bộ') !== false) {
                                    $features = [
                                        'Thời gian vận chuyển: linh hoạt từ 3 - 5 ngày',
                                        'Phù hợp mọi loại hàng hóa',
                                        'Giá cạnh tranh',
                                        'Vận chuyển tận nơi, giao tận nhà'
                                    ];
                                } elseif (strpos($title_lower, 'đường sắt') !== false) {
                                    $features = [
                                        'Cân bằng thời gian và chi phí',
                                        'Thân thiện môi trường',
                                        'An toàn cao',
                                        'Phù hợp hàng nặng'
                                    ];
                                } elseif (strpos($title_lower, 'nhập khẩu') !== false) {
                                    $features = [
                                        'Trọn gói từ A-Z',
                                        'Hoá đơn VAT + giấy tờ đầy đủ',
                                        'Tư vấn thuế nhập khẩu',
                                        'Hỗ trợ pháp lý'
                                    ];
                                } else {
                                    $features = [
                                        'Lên đơn và mua hộ qua các sàn thương mại 1688, taobao, Tmall, Alibaba…',
                                        'Thanh toán trực tiếp qua các kênh như Wechat, Alipay, Tài khoản ngân hàng, Mã QR, Thanh toán uỷ quyền',
                                        'Thanh toán an toàn',
                                        'Kiểm tra chất lượng',
                                        'Đàm phán giá tốt nhất'
                                    ];
                                }
                            }

                            // Limit number of items to keep card layout consistent
                            $features = array_slice($features, 0, 4);
                            ?>

                            <?php foreach ($features as $feature): ?>
                            <div style="display: flex; align-items: center; margin-bottom: 8px; font-size: 0.9rem;">
                                <i class="fas fa-check-circle" style="color: var(--primary-color); margin-right: 10px; font-size: 0.8rem;"></i>
                                <span style="color: var(--text-light);"><?php echo $feature; ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <a href="contact.php" class="btn btn-outline btn-sm">
                            <i class="fas fa-quote-left"></i> Báo giá ngay
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Process Section -->
<section class="section" style="background: var(--bg-light);">
    <div class="container">
        <div class="section-title">
            <h2>Quy trình làm việc</h2>
            <p>Quy trình đơn giản, minh bạch để đảm bảo dịch vụ tốt nhất cho khách hàng</p>
        </div>
        
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div style="background: white; padding: 40px 20px; border-radius: 15px; box-shadow: var(--shadow); text-align: center; position: relative;">
                    <div style="position: absolute; top: -20px; right: 20px; width: 40px; height: 40px; 
                                background: var(--primary-color); border-radius: 50%; display: flex; align-items: center; 
                                justify-content: center; font-weight: 700; color: var(--text-dark);">1</div>
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); 
                                border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fas fa-phone-alt" style="color: var(--text-dark); font-size: 1.5rem;"></i>
                    </div>
                    <h5 style="color: var(--text-dark); margin-bottom: 15px;">Liên hệ tư vấn</h5>
                    <p style="color: var(--text-light); font-size: 0.9rem; line-height: 1.6;">
                        Liên hệ với chúng tôi để được tư vấn, giải đáp mọi vấn đề liên quan tới đơn hàng với mức chi phí thấp nhất
                    </p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div style="background: white; padding: 40px 20px; border-radius: 15px; box-shadow: var(--shadow); text-align: center; position: relative;">
                    <div style="position: absolute; top: -20px; right: 20px; width: 40px; height: 40px; 
                                background: var(--primary-color); border-radius: 50%; display: flex; align-items: center; 
                                justify-content: center; font-weight: 700; color: var(--text-dark);">2</div>
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); 
                                border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fas fa-file-contract" style="color: var(--text-dark); font-size: 1.5rem;"></i>
                    </div>
                    <h5 style="color: var(--text-dark); margin-bottom: 15px;">Ký kết hợp đồng</h5>
                    <p style="color: var(--text-light); font-size: 0.9rem; line-height: 1.6;">
                        Ký kết hợp đồng theo nội dung và thỏa thuận giữ 2 bên về đơn hàng với các điều khoản dịch vụ 1 cách minh bạch và chi tiết
                    </p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div style="background: white; padding: 40px 20px; border-radius: 15px; box-shadow: var(--shadow); text-align: center; position: relative;">
                    <div style="position: absolute; top: -20px; right: 20px; width: 40px; height: 40px; 
                                background: var(--primary-color); border-radius: 50%; display: flex; align-items: center; 
                                justify-content: center; font-weight: 700; color: var(--text-dark);">3</div>
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); 
                                border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fas fa-shipping-fast" style="color: var(--text-dark); font-size: 1.5rem;"></i>
                    </div>
                    <h5 style="color: var(--text-dark); margin-bottom: 15px;">Xử lý đơn đặt hàng và Vận chuyển</h5>
                    <p style="color: var(--text-light); font-size: 0.9rem; line-height: 1.6;">
                        Thực hiện quy trình đặt hàng theo hợp đồng ký kết và vận chuyển hàng hoá về cho khách hàng theo đúng thời gian đã ký kết
                    </p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div style="background: white; padding: 40px 20px; border-radius: 15px; box-shadow: var(--shadow); text-align: center; position: relative;">
                    <div style="position: absolute; top: -20px; right: 20px; width: 40px; height: 40px; 
                                background: var(--primary-color); border-radius: 50%; display: flex; align-items: center; 
                                justify-content: center; font-weight: 700; color: var(--text-dark);">4</div>
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); 
                                border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fas fa-check-circle" style="color: var(--text-dark); font-size: 1.5rem;"></i>
                    </div>
                    <h5 style="color: var(--text-dark); margin-bottom: 15px;">Giao hàng</h5>
                    <p style="color: var(--text-light); font-size: 0.9rem; line-height: 1.6;">
                        Giao hàng tận nơi và hoàn tất thủ tục thanh toán một cách thuận tiện
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Process Image -->
        <div class="row mt-5">
            <div class="col-12">
                <div style="position: relative; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    <img src="<?php echo asset_url('assets/images/index12.png'); ?>" alt="Quy trình logistics" 
                         style="width: 100%; height: 300px; object-fit: cover;">
                    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; display: flex; align-items: center; justify-content: center;">
                        <div style="text-align: center; color: white;">
                            <h3 style="margin-bottom: 15px; font-weight: 600;">Quy trình chuyên nghiệp</h3>
                            <p style="font-size: 1.1rem; margin: 0;">Đảm bảo hàng hóa được vận chuyển an toàn và đúng thời gian</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Our Services -->
<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Tại sao chọn dịch vụ của chúng tôi?</h2>
            <p>Những cam kết và ưu điểm vượt trội mà chúng tôi mang lại</p>
        </div>
        
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div style="display: flex; align-items: start; margin-bottom: 30px;">
                    <div style="width: 60px; height: 60px; background: var(--primary-color); border-radius: 50%; 
                                display: flex; align-items: center; justify-content: center; margin-right: 20px; flex-shrink: 0;">
                        <i class="fas fa-money-bill-wave" style="color: var(--text-dark); font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h5 style="color: var(--text-dark); margin-bottom: 10px;">Chi phí cạnh tranh</h5>
                        <p style="color: var(--text-light); line-height: 1.6; margin: 0;">
                            Cước phí vận chuyển từ 1%, miễn phí tư vấn và đàm phán. Không phát sinh chi phí ẩn.
                        </p>
                    </div>
                </div>
                
                <div style="display: flex; align-items: start; margin-bottom: 30px;">
                    <div style="width: 60px; height: 60px; background: var(--primary-color); border-radius: 50%; 
                                display: flex; align-items: center; justify-content: center; margin-right: 20px; flex-shrink: 0;">
                        <i class="fas fa-clock" style="color: var(--text-dark); font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h5 style="color: var(--text-dark); margin-bottom: 10px;">Giao hàng đúng hạn</h5>
                        <p style="color: var(--text-light); line-height: 1.6; margin: 0;">
                            Cam kết giao hàng đúng thời gian đã thỏa thuận với hệ thống theo dõi real-time.
                        </p>
                    </div>
                </div>
                
                <div style="display: flex; align-items: start; margin-bottom: 30px;">
                    <div style="width: 60px; height: 60px; background: var(--primary-color); border-radius: 50%; 
                                display: flex; align-items: center; justify-content: center; margin-right: 20px; flex-shrink: 0;">
                        <i class="fas fa-shield-alt" style="color: var(--text-dark); font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h5 style="color: var(--text-dark); margin-bottom: 10px;">Bảo hiểm toàn diện</h5>
                        <p style="color: var(--text-light); line-height: 1.6; margin: 0;">
                            Bảo hiểm 100% giá trị hàng hóa, đền bù nếu có sự cố trong quá trình vận chuyển.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div style="display: flex; align-items: start; margin-bottom: 30px;">
                    <div style="width: 60px; height: 60px; background: var(--primary-color); border-radius: 50%; 
                                display: flex; align-items: center; justify-content: center; margin-right: 20px; flex-shrink: 0;">
                        <i class="fas fa-users" style="color: var(--text-dark); font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h5 style="color: var(--text-dark); margin-bottom: 10px;">Đội ngũ chuyên nghiệp</h5>
                        <p style="color: var(--text-light); line-height: 1.6; margin: 0;">
                            Đội ngũ nhân viên giàu kinh nghiệm, được đào tạo chuyên sâu về logistics.
                        </p>
                    </div>
                </div>
                
                <div style="display: flex; align-items: start; margin-bottom: 30px;">
                    <div style="width: 60px; height: 60px; background: var(--primary-color); border-radius: 50%; 
                                display: flex; align-items: center; justify-content: center; margin-right: 20px; flex-shrink: 0;">
                        <i class="fas fa-headset" style="color: var(--text-dark); font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h5 style="color: var(--text-dark); margin-bottom: 10px;">Hỗ trợ 24/7</h5>
                        <p style="color: var(--text-light); line-height: 1.6; margin: 0;">
                            Tư vấn và hỗ trợ khách hàng 24/7 qua hotline, email và các kênh trực tuyến.
                        </p>
                    </div>
                </div>
                
                <div style="display: flex; align-items: start; margin-bottom: 30px;">
                    <div style="width: 60px; height: 60px; background: var(--primary-color); border-radius: 50%; 
                                display: flex; align-items: center; justify-content: center; margin-right: 20px; flex-shrink: 0;">
                        <i class="fas fa-globe-asia" style="color: var(--text-dark); font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h5 style="color: var(--text-dark); margin-bottom: 10px;">Mạng lưới rộng khắp</h5>
                        <p style="color: var(--text-light); line-height: 1.6; margin: 0;">
                            Hệ thống đại lý và đối tác rộng khắp tại Trung Quốc và Việt Nam.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="section" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-light));">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h2 style="color: var(--text-dark); margin-bottom: 20px;">Cần tư vấn dịch vụ?</h2>
                <p style="color: var(--text-dark); font-size: 1.1rem; margin-bottom: 30px;">
                    Liên hệ ngay với chúng tôi để nhận tư vấn miễn phí và báo giá tốt nhất cho nhu cầu vận chuyển của bạn
                </p>
                <div>
                    <a href="contact.php" class="btn" style="background: var(--text-dark); color: var(--primary-color); border: none; margin-right: 15px;">
                        <i class="fas fa-phone"></i> Liên hệ ngay
                    </a>
                    <a href="tel:<?php echo str_replace([' ', '.', '-'], '', COMPANY_PHONE); ?>" class="btn" style="background: transparent; color: var(--text-dark); border: 2px solid var(--text-dark);">
                        <i class="fas fa-phone-alt"></i> <?php echo COMPANY_PHONE; ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?> 