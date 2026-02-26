<?php
// Include database config
require_once 'database/config.php';

// Page meta information
$page_title = "Giới thiệu";
$page_description = "Tìm hiểu về lịch sử, tầm nhìn và sứ mệnh của VINA LOGISTICS - đơn vị vận chuyển hàng hóa uy tín từ Trung Quốc về Việt Nam.";

// Include header
include 'includes/header.php';
?>

<!-- Breadcrumb -->
<section style="background: var(--bg-light); padding: 60px 0 40px; margin-top: 80px;">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 style="color: var(--text-dark); margin-bottom: 15px;">Giới thiệu về chúng tôi</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb" style="background: transparent; padding: 0; margin: 0;">
                        <li class="breadcrumb-item">
                                                            <a href="/" style="color: var(--text-light);">Trang chủ</a>
                        </li>
                        <li class="breadcrumb-item active" style="color: var(--primary-color);">Giới thiệu</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Company Overview -->
<section class="section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4">
                <div style="position: relative;">
                    <!-- Company Overview Image -->
                    <div style="position: relative; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin-bottom: 30px;">
                        <img src="<?php echo asset_url('assets/images/index14.png'); ?>" alt="Về công ty chúng tôi" 
                             style="width: 100%; height: 350px; object-fit: cover;">
                    </div>
                    
                    <div style="background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); 
                                border-radius: 20px; padding: 40px; text-align: center;">
                        <h3 style="color: var(--text-dark); margin-bottom: 20px;">
                            <strong><?php echo COMPANY_SHORT_NAME; ?></strong>
                        </h3>
                        <p style="color: var(--text-dark); font-size: 1.1rem; margin-bottom: 30px;">
                            Đối tác đáng tin cậy trong lĩnh vực logistics
                        </p>
                        
                        <!-- Company Stats -->
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                            <div style="background: rgba(255,255,255,0.9); padding: 20px; border-radius: 15px;">
                                <div style="font-size: 2rem; font-weight: 700; color: var(--text-dark); margin-bottom: 5px;">5+</div>
                                <div style="color: var(--text-dark); font-weight: 500;">Năm kinh nghiệm</div>
                            </div>
                            <div style="background: rgba(255,255,255,0.9); padding: 20px; border-radius: 15px;">
                                <div style="font-size: 2rem; font-weight: 700; color: var(--text-dark); margin-bottom: 5px;">15K+</div>
                                <div style="color: var(--text-dark); font-weight: 500;">Khách hàng</div>
                            </div>
                            <div style="background: rgba(255,255,255,0.9); padding: 20px; border-radius: 15px;">
                                <div style="font-size: 2rem; font-weight: 700; color: var(--text-dark); margin-bottom: 5px;">500K+</div>
                                <div style="color: var(--text-dark); font-weight: 500;">Đơn hàng</div>
                            </div>
                            <div style="background: rgba(255,255,255,0.9); padding: 20px; border-radius: 15px;">
                                <div style="font-size: 2rem; font-weight: 700; color: var(--text-dark); margin-bottom: 5px;">98%</div>
                                <div style="color: var(--text-dark); font-weight: 500;">Hài lòng</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <h2 style="color: var(--text-dark); margin-bottom: 25px;">Về chúng tôi</h2>
                <p style="color: var(--text-light); line-height: 1.8; margin-bottom: 20px;">
                    <strong style="color: var(--primary-color);"><?php echo COMPANY_NAME; ?></strong> 
                    là đơn vị chuyên cung cấp dịch vụ vận chuyển hàng hóa và logistics với hơn 5 năm kinh nghiệm 
                    trong lĩnh vực nhập khẩu từ Trung Quốc về Việt Nam.
                </p>
                <p style="color: var(--text-light); line-height: 1.8; margin-bottom: 20px;">
                    Chúng tôi tự hào là đối tác đáng tin cậy của hàng ngàn doanh nghiệp và cá nhân trong việc 
                    vận chuyển hàng hóa an toàn, nhanh chóng với chi phí tối ưu nhất.
                </p>
                <p style="color: var(--text-light); line-height: 1.8; margin-bottom: 25px;">
                    Với đội ngũ nhân viên chuyên nghiệp, giàu kinh nghiệm và hệ thống vận chuyển hiện đại, 
                    chúng tôi cam kết mang đến dịch vụ tốt nhất cho khách hàng.
                </p>
                
                <!-- Company License Info -->
                <div style="background: var(--bg-light); padding: 20px; border-radius: 15px; border-left: 4px solid var(--primary-color);">
                    <h6 style="color: var(--text-dark); margin-bottom: 10px;">
                        <i class="fas fa-certificate" style="color: var(--primary-color); margin-right: 10px;"></i>
                        Thông tin pháp lý
                    </h6>
                    <p style="margin-bottom: 8px; color: var(--text-light);">
                        <strong>Giấy phép kinh doanh:</strong> Số <?php echo COMPANY_LICENSE; ?>
                    </p>
                    <p style="margin-bottom: 8px; color: var(--text-light);">
                        <strong>Đăng ký lần đầu:</strong> 16/01/2024
                    </p>
                    <p style="margin: 0; color: var(--text-light);">
                        <strong>Nơi cấp:</strong> Sở Kế hoạch và Đầu tư TP. Hà Nội
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission, Vision, Values -->
<section class="section" style="background: var(--bg-light);">
    <div class="container">
        <div class="section-title">
            <h2>Tầm nhìn - Sứ mệnh - Giá trị cốt lõi</h2>
            <p>Những giá trị định hướng hoạt động của chúng tôi</p>
        </div>
        
        <div class="row">
            <!-- Vision -->
            <div class="col-lg-4 mb-4">
                <div style="background: white; padding: 40px 30px; border-radius: 15px; box-shadow: var(--shadow); height: 100%; text-align: center;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); 
                                border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px;">
                        <i class="fas fa-eye" style="font-size: 2rem; color: var(--text-dark);"></i>
                    </div>
                    <h4 style="color: var(--text-dark); margin-bottom: 20px;">Tầm nhìn</h4>
                    <p style="color: var(--text-light); line-height: 1.7;">
                        Trở thành đơn vị vận chuyển hàng hóa hàng đầu Việt Nam, là cầu nối tin cậy 
                        giữa các doanh nghiệp Việt Nam và thế giới.
                    </p>
                </div>
            </div>
            
            <!-- Mission -->
            <div class="col-lg-4 mb-4">
                <div style="background: white; padding: 40px 30px; border-radius: 15px; box-shadow: var(--shadow); height: 100%; text-align: center;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); 
                                border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px;">
                        <i class="fas fa-bullseye" style="font-size: 2rem; color: var(--text-dark);"></i>
                    </div>
                    <h4 style="color: var(--text-dark); margin-bottom: 20px;">Sứ mệnh</h4>
                    <p style="color: var(--text-light); line-height: 1.7;">
                        Cung cấp dịch vụ logistics chất lượng cao, giúp khách hàng tối ưu hóa chi phí 
                        và thời gian trong hoạt động kinh doanh.
                    </p>
                </div>
            </div>
            
            <!-- Values -->
            <div class="col-lg-4 mb-4">
                <div style="background: white; padding: 40px 30px; border-radius: 15px; box-shadow: var(--shadow); height: 100%; text-align: center;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); 
                                border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px;">
                        <i class="fas fa-heart" style="font-size: 2rem; color: var(--text-dark);"></i>
                    </div>
                    <h4 style="color: var(--text-dark); margin-bottom: 20px;">Giá trị cốt lõi</h4>
                    <p style="color: var(--text-light); line-height: 1.7;">
                        Uy tín, chất lượng, tận tâm. Chúng tôi đặt lợi ích khách hàng lên hàng đầu 
                        và không ngừng cải tiến dịch vụ.
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Team Image Section -->
        <div class="row mt-5">
            <div class="col-12">
                <div style="position: relative; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    <img src="<?php echo asset_url('assets/images/index13.png'); ?>" alt="Đội ngũ làm việc" 
                         style="width: 100%; height: 400px; object-fit: cover;">
                    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; display: flex; align-items: center; justify-content: center;">
                        <div style="text-align: center; color: white;">
                            <h3 style="margin-bottom: 15px; font-weight: 600;">Đội ngũ chuyên nghiệp</h3>
                            <p style="font-size: 1.1rem; margin: 0;">Với kinh nghiệm và tận tâm, chúng tôi cam kết mang đến dịch vụ tốt nhất</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Tại sao chọn <?php echo COMPANY_SHORT_NAME; ?>?</h2>
            <p>Những ưu điểm vượt trội giúp chúng tôi trở thành lựa chọn hàng đầu</p>
        </div>
        
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div style="display: flex; align-items: start; margin-bottom: 30px;">
                    <div style="width: 60px; height: 60px; background: var(--primary-color); border-radius: 50%; 
                                display: flex; align-items: center; justify-content: center; margin-right: 20px; flex-shrink: 0;">
                        <i class="fas fa-shipping-fast" style="color: var(--text-dark); font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h5 style="color: var(--text-dark); margin-bottom: 10px;">Vận chuyển nhanh chóng</h5>
                        <p style="color: var(--text-light); line-height: 1.6; margin: 0;">
                            Thời gian vận chuyển tối ưu với các tuyến đường được lựa chọn kỹ lưỡng. 
                            Hàng hóa được giao đúng thời gian cam kết.
                        </p>
                    </div>
                </div>
                
                <div style="display: flex; align-items: start; margin-bottom: 30px;">
                    <div style="width: 60px; height: 60px; background: var(--primary-color); border-radius: 50%; 
                                display: flex; align-items: center; justify-content: center; margin-right: 20px; flex-shrink: 0;">
                        <i class="fas fa-shield-alt" style="color: var(--text-dark); font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h5 style="color: var(--text-dark); margin-bottom: 10px;">An toàn tuyệt đối</h5>
                        <p style="color: var(--text-light); line-height: 1.6; margin: 0;">
                            Hàng hóa được bảo hiểm 100%, cam kết đền bù nếu có sự cố. 
                            Quy trình đóng gói và vận chuyển chuyên nghiệp.
                        </p>
                    </div>
                </div>
                
                <div style="display: flex; align-items: start; margin-bottom: 30px;">
                    <div style="width: 60px; height: 60px; background: var(--primary-color); border-radius: 50%; 
                                display: flex; align-items: center; justify-content: center; margin-right: 20px; flex-shrink: 0;">
                        <i class="fas fa-dollar-sign" style="color: var(--text-dark); font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h5 style="color: var(--text-dark); margin-bottom: 10px;">Chi phí tối ưu</h5>
                        <p style="color: var(--text-light); line-height: 1.6; margin: 0;">
                            Cước phí vận chuyển từ 1%, miễn phí tư vấn và đàm phán với đối tác. 
                            Không phát sinh chi phí ẩn.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div style="display: flex; align-items: start; margin-bottom: 30px;">
                    <div style="width: 60px; height: 60px; background: var(--primary-color); border-radius: 50%; 
                                display: flex; align-items: center; justify-content: center; margin-right: 20px; flex-shrink: 0;">
                        <i class="fas fa-headset" style="color: var(--text-dark); font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h5 style="color: var(--text-dark); margin-bottom: 10px;">Hỗ trợ 24/7</h5>
                        <p style="color: var(--text-light); line-height: 1.6; margin: 0;">
                            Đội ngũ tư vấn chăm sóc khách hàng chuyên nghiệp, sẵn sàng hỗ trợ 
                            mọi lúc mọi nơi khi bạn cần.
                        </p>
                    </div>
                </div>
                
                <div style="display: flex; align-items: start; margin-bottom: 30px;">
                    <div style="width: 60px; height: 60px; background: var(--primary-color); border-radius: 50%; 
                                display: flex; align-items: center; justify-content: center; margin-right: 20px; flex-shrink: 0;">
                        <i class="fas fa-search" style="color: var(--text-dark); font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h5 style="color: var(--text-dark); margin-bottom: 10px;">Hỗ trợ tìm nguồn hàng</h5>
                        <p style="color: var(--text-light); line-height: 1.6; margin: 0;">
                            Đội ngũ chuyên gia am hiểu thị trường, luôn sẵn sàng hỗ trợ miễn phí 
                            tìm nguồn hàng ưng ý cho khách hàng.
                        </p>
                    </div>
                </div>
                
                <div style="display: flex; align-items: start; margin-bottom: 30px;">
                    <div style="width: 60px; height: 60px; background: var(--primary-color); border-radius: 50%; 
                                display: flex; align-items: center; justify-content: center; margin-right: 20px; flex-shrink: 0;">
                        <i class="fas fa-laptop" style="color: var(--text-dark); font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h5 style="color: var(--text-dark); margin-bottom: 10px;">Theo dõi online</h5>
                        <p style="color: var(--text-light); line-height: 1.6; margin: 0;">
                            Hệ thống theo dõi đơn hàng trực tuyến, giúp khách hàng cập nhật 
                            tình trạng hàng hóa mọi lúc mọi nơi.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact CTA -->
<section class="section" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); text-align: center;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2 style="color: var(--text-dark); margin-bottom: 20px;">Sẵn sàng hợp tác cùng chúng tôi?</h2>
                <p style="color: var(--text-dark); font-size: 1.1rem; margin-bottom: 30px;">
                    Liên hệ ngay để nhận tư vấn miễn phí và báo giá tốt nhất cho nhu cầu vận chuyển của bạn
                </p>
                <div>
                    <a href="contact.php" class="btn" style="background: var(--text-dark); color: var(--primary-color); border: 2px solid var(--text-dark); margin-right: 15px;">
                        <i class="fas fa-phone"></i> Liên hệ ngay
                    </a>
                    <a href="services.php" class="btn" style="background: transparent; color: var(--text-dark); border: 2px solid var(--text-dark);">
                        <i class="fas fa-truck"></i> Xem dịch vụ
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?> 