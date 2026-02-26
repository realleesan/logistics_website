    </main>

    <!-- Rainbow Text Animation CSS -->
    <style>
        .rainbow-text {
            background: linear-gradient(45deg, #ff0000, #ff8000, #ffff00, #80ff00, #00ff00, #00ff80, #00ffff, #0080ff, #0000ff, #8000ff, #ff00ff, #ff0080);
            background-size: 400% 400%;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: rainbow-flow 3s ease-in-out infinite;
            text-decoration: none;
            font-weight: 600;
        }

        .rainbow-text:hover {
            animation-duration: 1s;
        }

        @keyframes rainbow-flow {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        /* Fallback for browsers that don't support background-clip: text */
        @supports not (-webkit-background-clip: text) {
            .rainbow-text {
                background: none;
                -webkit-text-fill-color: initial;
                color: var(--primary-color);
                animation: hue-rotate 3s linear infinite;
            }

            @keyframes hue-rotate {
                0% {
                    filter: hue-rotate(0deg);
                }
                100% {
                    filter: hue-rotate(360deg);
                }
            }
        }
    </style>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <!-- First Row: Company Info, Quick Links, Services, Contact Info -->
            <div class="row">
                <!-- Company Info -->
                <div class="col-md-4 mb-4">
                    <div class="logo mb-3">
                        <img src="<?php echo defined('APP_URL') ? APP_URL : ''; ?>/logo.jpg" alt="Vina Logistics Logo" style="height: 60px;">
                        <span style="color: var(--primary-color); font-size: 1.5rem; font-weight: 700; margin-left: 10px;">
                            <?php echo COMPANY_SHORT_NAME; ?>
                        </span>
                    </div>
                    <p style="color: #bdc3c7; line-height: 1.6; margin-bottom: 20px;">
                        Chúng tôi cung cấp dịch vụ vận chuyển hàng hóa chuyên nghiệp, uy tín từ Trung Quốc về Việt Nam với chi phí tối ưu và thời gian nhanh chóng.
                    </p>
                    
                    <!-- Giấy phép kinh doanh -->
                    <div style="background: rgba(195, 247, 37, 0.1); padding: 15px; border-radius: 8px; border-left: 4px solid var(--primary-color);">
                        <strong style="color: var(--primary-color);">Giấy phép kinh doanh:</strong><br>
                        <span style="color: #bdc3c7;">Số <?php echo COMPANY_LICENSE; ?></span><br>
                        <span style="color: #95a5a6; font-size: 0.9rem;">Đăng ký lần đầu: 16/01/2024</span>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-md-2 mb-4">
                    <h5>Liên kết nhanh</h5>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 10px;">
                            <a href="/" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s ease;">
                                <i class="fas fa-angle-right" style="color: var(--primary-color); margin-right: 8px;"></i>
                                Trang chủ
                            </a>
                        </li>
                        <li style="margin-bottom: 10px;">
                            <a href="gioi-thieu" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s ease;">
                                <i class="fas fa-angle-right" style="color: var(--primary-color); margin-right: 8px;"></i>
                                Giới thiệu
                            </a>
                        </li>
                        <li style="margin-bottom: 10px;">
                            <a href="dich-vu" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s ease;">
                                <i class="fas fa-angle-right" style="color: var(--primary-color); margin-right: 8px;"></i>
                                Dịch vụ
                            </a>
                        </li>
                        <li style="margin-bottom: 10px;">
                            <a href="tin-tuc" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s ease;">
                                <i class="fas fa-angle-right" style="color: var(--primary-color); margin-right: 8px;"></i>
                                Tin tức
                            </a>
                        </li>
                        <li style="margin-bottom: 10px;">
                            <a href="lien-he" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s ease;">
                                <i class="fas fa-angle-right" style="color: var(--primary-color); margin-right: 8px;"></i>
                                Liên hệ
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Services -->
                <div class="col-md-3 mb-4">
                    <h5>Dịch vụ chính</h5>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 10px;">
                            <a href="dich-vu" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s ease;">
                                <i class="fas fa-truck" style="color: var(--primary-color); margin-right: 8px;"></i>
                                Vận chuyển đường bộ
                            </a>
                        </li>
                        <li style="margin-bottom: 10px;">
                            <a href="dich-vu" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s ease;">
                                <i class="fas fa-file-import" style="color: var(--primary-color); margin-right: 8px;"></i>
                                Nhập khẩu ủy thác
                            </a>
                        </li>
                        <li style="margin-bottom: 10px;">
                            <a href="dich-vu" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s ease;">
                                <i class="fas fa-ship" style="color: var(--primary-color); margin-right: 8px;"></i>
                                Vận chuyển đường biển
                            </a>
                        </li>
                        <li style="margin-bottom: 10px;">
                            <a href="dich-vu" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s ease;">
                                <i class="fas fa-suitcase-rolling" style="color: var(--primary-color); margin-right: 8px;"></i>
                                Vận chuyển xách tay nhanh
                            </a>
                        </li>
                        <li style="margin-bottom: 10px;">
                            <a href="dich-vu" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s ease;">
                                <i class="fas fa-shopping-cart" style="color: var(--primary-color); margin-right: 8px;"></i>
                                Đặt hàng Trung Quốc
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="col-md-3 mb-4">
                    <h5>Thông tin liên hệ</h5>
                    <div style="margin-bottom: 15px;">
                        <i class="fas fa-map-marker-alt" style="color: var(--primary-color); margin-right: 10px; width: 20px;"></i>
                        <span style="color: #bdc3c7;"><?php echo COMPANY_ADDRESS; ?></span>
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <i class="fas fa-phone" style="color: var(--primary-color); margin-right: 10px; width: 20px;"></i>
                        <a href="tel:<?php echo str_replace([' ', '.', '-'], '', COMPANY_PHONE); ?>" style="color: #bdc3c7; text-decoration: none;">
                            <?php echo COMPANY_PHONE; ?>
                        </a>
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <i class="fas fa-envelope" style="color: var(--primary-color); margin-right: 10px; width: 20px;"></i>
                        <a href="mailto:<?php echo COMPANY_EMAIL; ?>" style="color: #bdc3c7; text-decoration: none;">
                            <?php echo COMPANY_EMAIL; ?>
                        </a>
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <i class="fas fa-clock" style="color: var(--primary-color); margin-right: 10px; width: 20px;"></i>
                        <span style="color: #bdc3c7;">Thứ 2 - Thứ 7: 8:00 - 17:00</span>
                    </div>
                </div>
            </div>

            <!-- Connect With Us Section -->
            <div style="border-top: 1px solid #34495e; padding-top: 40px; margin-top: 30px;">
                <!-- Section Title -->
                <div class="row mb-4">
                    <div class="col-12 text-center">
                        <h3 style="color: var(--primary-color); font-weight: 700; margin-bottom: 30px;">
                            <i class="fas fa-handshake" style="margin-right: 10px;"></i>
                            Kết nối với chúng tôi
                        </h3>
                    </div>
                </div>

                <!-- Content Row -->
                <div class="row">
                    <!-- Left Column: Facebook Embed -->
                    <div class="col-md-6 mb-4">
                        <div style="background: rgba(255, 255, 255, 0.05); padding: 20px; border-radius: 10px; min-height: 280px; display: flex; align-items: center; justify-content: center;">
                            <div class="fb-page" 
                                 data-href="https://www.facebook.com/truongvinalogistics" 
                                 data-tabs="" 
                                 data-width="400" 
                                 data-height="280" 
                                 data-small-header="false" 
                                 data-adapt-container-width="true" 
                                 data-hide-cover="false" 
                                 data-show-facepile="false"
                                 style="display: flex; justify-content: center; align-items: center;">
                                <blockquote cite="https://www.facebook.com/truongvinalogistics" class="fb-xfbml-parse-ignore">
                                    <a href="https://www.facebook.com/truongvinalogistics">Trường VINA Logistics</a>
                                </blockquote>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Contact Info -->
                    <div class="col-md-6 mb-4">
                        <div class="row">
                            <!-- Director Section -->
                            <div class="col-md-6 mb-4">
                                <h5 id="director-name" style="color: var(--primary-color); margin-bottom: 15px; font-weight: 600; cursor: pointer; user-select: none;">
                                    <i class="fas fa-user-tie" style="margin-right: 8px;"></i>
                                    Giám đốc: Nguyễn Thế Trường
                                </h5>
                                <ul style="list-style: none; padding: 0;">
                                    <li style="margin-bottom: 10px;">
                                        <a href="tel:0971160197" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s ease;">
                                            <i class="fas fa-phone" style="color: var(--primary-color); margin-right: 8px;"></i>
                                            0971.160.197
                                        </a>
                                    </li>
                                    <li style="margin-bottom: 10px;">
                                        <a href="https://zalo.me/0971160197" target="_blank" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s ease;">
                                            <i class="fas fa-comment" style="color: var(--primary-color); margin-right: 8px;"></i>
                                            Zalo
                                        </a>
                                    </li>
                                    <li style="margin-bottom: 10px;">
                                        <a href="mailto:truongvinagroup@gmail.com" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s ease;">
                                            <i class="fas fa-envelope" style="color: var(--primary-color); margin-right: 8px;"></i>
                                            Gmail
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <!-- Sales Manager Section -->
                            <div class="col-md-6 mb-4">
                                <h5 style="color: var(--primary-color); margin-bottom: 15px; font-weight: 600;">
                                    <i class="fas fa-user-tie" style="margin-right: 8px;"></i>
                                    Trưởng phòng kinh doanh: Tống Thị Trang
                                </h5>
                                <ul style="list-style: none; padding: 0;">
                                    <li style="margin-bottom: 10px;">
                                        <a href="tel:0379265520" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s ease;">
                                            <i class="fas fa-phone" style="color: var(--primary-color); margin-right: 8px;"></i>
                                            0379.265.520
                                        </a>
                                    </li>
                                    <li style="margin-bottom: 10px;">
                                        <a href="https://zalo.me/0379265520" target="_blank" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s ease;">
                                            <i class="fas fa-comment" style="color: var(--primary-color); margin-right: 8px;"></i>
                                            Zalo
                                        </a>
                                    </li>
                                    <li style="margin-bottom: 10px;">
                                        <a href="mailto:tongthitrang.vinalogistics@gmail.com" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s ease;">
                                            <i class="fas fa-envelope" style="color: var(--primary-color); margin-right: 8px;"></i>
                                            Gmail
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p style="margin: 0; color: #95a5a6;">
                            &copy; <?php echo date('Y'); ?> <?php echo COMPANY_SHORT_NAME; ?>. Tất cả quyền được bảo lưu.
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p style="margin: 0; color: #95a5a6;">
                            Thiết kế bởi <a href="https://zalo.me/0914960029" target="_blank" class="rainbow-text">Misty Team</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Fixed Contact Buttons -->
    <div style="position: fixed; right: 20px; top: 50%; transform: translateY(-50%); z-index: 1000;">
        <!-- Hotline Button -->
        <a href="tel:<?php echo str_replace([' ', '.', '-'], '', COMPANY_PHONE); ?>" 
           style="display: block; width: 60px; height: 60px; background: linear-gradient(135deg, #25d366, #128c7e); 
                  border-radius: 50%; margin-bottom: 15px; box-shadow: 0 4px 15px rgba(37, 211, 102, 0.4); 
                  display: flex; align-items: center; justify-content: center; text-decoration: none; 
                  transition: all 0.3s ease;"
           onmouseover="this.style.transform='scale(1.1)'"
           onmouseout="this.style.transform='scale(1)'"
           title="Gọi hotline: <?php echo COMPANY_PHONE; ?>">
            <i class="fas fa-phone" style="color: white; font-size: 24px;"></i>
        </a>

        <!-- Zalo Button -->
        <a href="https://zalo.me/0971160197" target="_blank"
           style="display: block; width: 60px; height: 60px; background: linear-gradient(135deg, #0068ff, #0052cc); 
                  border-radius: 50%; margin-bottom: 15px; box-shadow: 0 4px 15px rgba(0, 104, 255, 0.4); 
                  display: flex; align-items: center; justify-content: center; text-decoration: none; 
                  transition: all 0.3s ease;"
           onmouseover="this.style.transform='scale(1.1)'"
           onmouseout="this.style.transform='scale(1)'"
           title="Chat Zalo">
            <i class="fas fa-comment" style="color: white; font-size: 24px;"></i>
        </a>

        <!-- Email Button -->
        <a href="mailto:<?php echo COMPANY_EMAIL; ?>"
           style="display: block; width: 60px; height: 60px; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); 
                  border-radius: 50%; box-shadow: 0 4px 15px rgba(195, 247, 37, 0.4); 
                  display: flex; align-items: center; justify-content: center; text-decoration: none; 
                  transition: all 0.3s ease;"
           onmouseover="this.style.transform='scale(1.1)'"
           onmouseout="this.style.transform='scale(1)'"
           title="Gửi email">
            <i class="fas fa-envelope" style="color: var(--text-dark); font-size: 24px;"></i>
        </a>
    </div>

    <!-- Admin Access Modal -->
    <div class="modal fade" id="adminAccessModal" tabindex="-1" aria-labelledby="adminAccessModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; border: none;">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title" id="adminAccessModalLabel" style="color: var(--text-dark); font-weight: 600;">
                        <i class="fas fa-shield-alt me-2"></i>
                        Truy cập quản trị
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding: 30px;">
                    <div class="text-center mb-4">
                        <i class="fas fa-lock fa-3x" style="color: var(--primary-color); margin-bottom: 15px;"></i>
                        <h6 style="color: var(--text-dark);">Nhập mã bí mật để truy cập</h6>
                        <p class="text-muted small">Chỉ dành cho quản trị viên hệ thống</p>
                    </div>
                    
                    <form id="adminSecretForm">
                        <div class="mb-3">
                            <input type="password" class="form-control" id="secretCode" 
                                   placeholder="Nhập mã bí mật (5 ký tự)" 
                                   maxlength="5" 
                                   style="text-align: center; font-size: 18px; letter-spacing: 3px; padding: 15px; border-radius: 10px; border: 2px solid #e9ecef;">
                        </div>
                        <div id="secretError" class="alert alert-danger d-none" style="border-radius: 8px;">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Mã bí mật không chính xác!
                        </div>
                        <button type="submit" class="btn w-100" 
                                style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); 
                                       color: var(--text-dark); font-weight: 600; padding: 12px; border-radius: 10px; border: none;">
                            <i class="fas fa-key me-2"></i>
                            Xác nhận
                        </button>
                    </form>
                </div>
                <div class="modal-footer" style="border: none; padding: 0 30px 30px;">
                    <small class="text-muted mx-auto">
                        <i class="fas fa-info-circle me-1"></i>
                        Liên hệ quản trị viên nếu bạn quên mã bí mật
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Files -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo asset_url('assets/js/main.js'); ?>"></script>
    
    <!-- Admin Access Secret Script -->
    <script>
        let clickCount = 0;
        let clickTimer = null;
        const secretCode = 'zxc34';
        
        document.addEventListener('DOMContentLoaded', function() {
            const directorName = document.getElementById('director-name');
            const adminModal = new bootstrap.Modal(document.getElementById('adminAccessModal'));
            const secretForm = document.getElementById('adminSecretForm');
            const secretInput = document.getElementById('secretCode');
            const secretError = document.getElementById('secretError');
            
            // Director name click handler
            directorName.addEventListener('click', function() {
                clickCount++;
                
                // Reset counter after 3 seconds of no clicks
                if (clickTimer) {
                    clearTimeout(clickTimer);
                }
                clickTimer = setTimeout(() => {
                    clickCount = 0;
                }, 3000);
                
                // Add click effect
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 100);
                
                // Show modal after 5 clicks
                if (clickCount >= 5) {
                    clickCount = 0;
                    clearTimeout(clickTimer);
                    adminModal.show();
                    setTimeout(() => {
                        secretInput.focus();
                    }, 500);
                }
            });
            
            // Secret form submission
            secretForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const inputCode = secretInput.value.trim();
                
                if (inputCode === secretCode) {
                    // Correct code - redirect to admin
                    adminModal.hide();
                    
                    // Show success message briefly
                    const successAlert = document.createElement('div');
                    successAlert.className = 'alert alert-success position-fixed';
                    successAlert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; border-radius: 10px;';
                    successAlert.innerHTML = '<i class="fas fa-check-circle me-2"></i>Đang chuyển hướng...';
                    document.body.appendChild(successAlert);
                    
                    setTimeout(() => {
                        window.location.href = 'admin/';
                    }, 1000);
                } else {
                    // Wrong code - show error
                    secretError.classList.remove('d-none');
                    secretInput.value = '';
                    secretInput.focus();
                    
                    // Shake effect
                    secretInput.style.animation = 'shake 0.5s';
                    setTimeout(() => {
                        secretInput.style.animation = '';
                    }, 500);
                    
                    // Hide error after 3 seconds
                    setTimeout(() => {
                        secretError.classList.add('d-none');
                    }, 3000);
                }
            });
            
            // Reset form when modal is hidden
            document.getElementById('adminAccessModal').addEventListener('hidden.bs.modal', function() {
                secretInput.value = '';
                secretError.classList.add('d-none');
            });
            
            // Input formatting
            secretInput.addEventListener('input', function() {
                this.value = this.value.toLowerCase();
                secretError.classList.add('d-none');
            });
        });
        
        // Add shake animation
        const shakeStyle = document.createElement('style');
        shakeStyle.textContent = `
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
                20%, 40%, 60%, 80% { transform: translateX(5px); }
            }
            
            #director-name {
                transition: transform 0.1s ease;
            }
            
            #director-name:hover {
                color: #a5d6a7 !important;
            }
        `;
        document.head.appendChild(shakeStyle);
    </script>
    
    <!-- Additional JavaScript for specific pages -->
    <?php if (isset($additional_js)): ?>
        <?php foreach ($additional_js as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Page-specific JavaScript -->
    <?php if (isset($page_script)): ?>
        <script><?php echo $page_script; ?></script>
    <?php endif; ?>

    <!-- Death Protection System -->
    <script src="<?php echo defined('APP_URL') ? APP_URL : ''; ?>/assets/js/death_protection.js"></script>
</body>
</html> 