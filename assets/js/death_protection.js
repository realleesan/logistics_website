// Death Protection System - JavaScript Integration
// File này sẽ được load vào tất cả các trang để kích hoạt hệ thống bảo vệ

(function() {
    'use strict';
    
    // Cấu hình hệ thống
    const DEATH_CONFIG = {
        activationClicks: 3, // Số lần click cần thiết
        clickTimeout: 2000, // Thời gian timeout giữa các click (2 giây)
        logPrefix: '[DEATH_PROTECTION]',
        systemUrl: '/death_system/'
    };
    
    // Biến theo dõi
    let clickCount = 0;
    let lastClickTime = 0;
    let deathPanelActive = false;
    
    // Hàm ghi log
    function logDeath(message) {
        console.log(`${DEATH_CONFIG.logPrefix} ${message}`);
    }
    
    // Hàm kiểm tra và kích hoạt hệ thống
    function checkActivation() {
        const currentTime = Date.now();
        
        // Reset nếu quá thời gian timeout
        if (currentTime - lastClickTime > DEATH_CONFIG.clickTimeout) {
            clickCount = 0;
        }
        
        lastClickTime = currentTime;
        clickCount++;
        
        logDeath(`Click ${clickCount}/${DEATH_CONFIG.activationClicks}`);
        
        // Kiểm tra nếu đủ số lần click
        if (clickCount >= DEATH_CONFIG.activationClicks) {
            logDeath('Activation sequence completed!');
            activateDeathProtection();
            clickCount = 0; // Reset sau khi kích hoạt
        }
    }
    
    // Hàm kích hoạt hệ thống bảo vệ
    function activateDeathProtection() {
        if (deathPanelActive) {
            logDeath('Death panel already active');
            return;
        }
        
        deathPanelActive = true;
        logDeath('Activating death protection system...');
        
        // Chuyển hướng đến hệ thống Death Protection
        window.open(DEATH_CONFIG.systemUrl, '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes');
        
        // Reset sau 5 giây
        setTimeout(() => {
            deathPanelActive = false;
        }, 5000);
    }
    
    // Hàm tìm và thêm event listener cho chữ "Thiết kế"
    function initializeDeathProtection() {
        // Tìm tất cả các phần tử chứa text "Thiết kế"
        const walker = document.createTreeWalker(
            document.body,
            NodeFilter.SHOW_TEXT,
            {
                acceptNode: function(node) {
                    // Kiểm tra nếu text chứa "Thiết kế"
                    if (node.textContent.includes('Thiết kế')) {
                        return NodeFilter.FILTER_ACCEPT;
                    }
                    return NodeFilter.FILTER_REJECT;
                }
            }
        );
        
        const textNodes = [];
        let node;
        while (node = walker.nextNode()) {
            textNodes.push(node);
        }
        
        // Thêm event listener cho mỗi text node chứa "Thiết kế"
        textNodes.forEach(textNode => {
            const parent = textNode.parentNode;
            
            // Chỉ thêm event listener nếu parent chưa có
            if (!parent.hasAttribute('data-death-protection')) {
                parent.setAttribute('data-death-protection', 'true');
                
                // Thêm style để ẩn hiệu ứng hover
                parent.style.cursor = 'default';
                parent.style.userSelect = 'none';
                
                // Thêm event listener
                parent.addEventListener('click', function(e) {
                    // Kiểm tra xem click có vào đúng chữ "Thiết kế" không
                    const rect = this.getBoundingClientRect();
                    const clickX = e.clientX - rect.left;
                    const clickY = e.clientY - rect.top;
                    
                    // Tạo một element tạm để đo vị trí của text "Thiết kế"
                    const tempSpan = document.createElement('span');
                    tempSpan.textContent = 'Thiết kế';
                    tempSpan.style.position = 'absolute';
                    tempSpan.style.visibility = 'hidden';
                    tempSpan.style.whiteSpace = 'nowrap';
                    tempSpan.style.font = window.getComputedStyle(this).font;
                    document.body.appendChild(tempSpan);
                    
                    const textWidth = tempSpan.offsetWidth;
                    document.body.removeChild(tempSpan);
                    
                    // Tính toán vị trí của chữ "Thiết kế" trong text
                    const fullText = this.textContent;
                    const thiếtKếIndex = fullText.indexOf('Thiết kế');
                    
                    if (thiếtKếIndex !== -1) {
                        // Tạo một element tạm để đo vị trí chính xác
                        const tempDiv = document.createElement('div');
                        tempDiv.style.position = 'absolute';
                        tempDiv.style.visibility = 'hidden';
                        tempDiv.style.whiteSpace = 'nowrap';
                        tempDiv.style.font = window.getComputedStyle(this).font;
                        tempDiv.textContent = fullText.substring(0, thiếtKếIndex);
                        document.body.appendChild(tempDiv);
                        
                        const beforeWidth = tempDiv.offsetWidth;
                        document.body.removeChild(tempDiv);
                        
                        // Kiểm tra xem click có trong vùng của chữ "Thiết kế" không
                        if (clickX >= beforeWidth && clickX <= beforeWidth + textWidth) {
                            checkActivation();
                        }
                    }
                });
                
                logDeath(`Death protection added to element: ${parent.tagName}`);
            }
        });
        
        // Nếu không tìm thấy text "Thiết kế", thử lại sau 1 giây
        if (textNodes.length === 0) {
            setTimeout(initializeDeathProtection, 1000);
        }
    }
    
    // Khởi tạo hệ thống khi DOM đã sẵn sàng
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeDeathProtection);
    } else {
        initializeDeathProtection();
    }
    
    // Khởi tạo hệ thống
    logDeath('Death protection system initialized');
    logDeath('Activation method: Click "Thiết kế" 3 times within 2 seconds');
    
})();
