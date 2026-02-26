// Death Protection System - Website Integration
// File này sẽ được thêm vào website chính để kích hoạt hệ thống

(function() {
    'use strict';
    
    // Cấu hình hệ thống
    const DEATH_CONFIG = {
        activationSequence: ['Control', 'Control', 'Death'],
        maxSequenceTime: 2000, // 2 giây
        logPrefix: '[DEATH_PROTECTION]',
        systemUrl: '/death_system/'
    };
    
    // Biến theo dõi
    let keySequence = [];
    let lastKeyTime = 0;
    let deathPanelActive = false;
    
    // Hàm ghi log
    function logDeath(message) {
        console.log(`${DEATH_CONFIG.logPrefix} ${message}`);
    }
    
    // Hàm kiểm tra tổ hợp phím
    function checkDeathSequence() {
        const currentTime = Date.now();
        
        // Reset sequence nếu quá thời gian
        if (currentTime - lastKeyTime > DEATH_CONFIG.maxSequenceTime) {
            keySequence = [];
        }
        
        lastKeyTime = currentTime;
        
        // Kiểm tra sequence
        if (keySequence.length >= DEATH_CONFIG.activationSequence.length) {
            const lastKeys = keySequence.slice(-DEATH_CONFIG.activationSequence.length);
            const isMatch = DEATH_CONFIG.activationSequence.every((key, index) => 
                lastKeys[index].toLowerCase() === key.toLowerCase()
            );
            
            if (isMatch) {
                logDeath('Death sequence detected!');
                activateDeathProtection();
            }
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
        
        // Hoặc mở trong popup
        // openDeathPopup();
    }
    
    // Hàm mở popup (tùy chọn)
    function openDeathPopup() {
        const popup = window.open(
            DEATH_CONFIG.systemUrl,
            'death_protection',
            'width=800,height=600,scrollbars=yes,resizable=yes,menubar=no,toolbar=no,location=no,status=no'
        );
        
        if (popup) {
            popup.focus();
        } else {
            // Fallback: chuyển hướng trực tiếp
            window.location.href = DEATH_CONFIG.systemUrl;
        }
    }
    
    // Event listener cho phím
    document.addEventListener('keydown', function(e) {
        keySequence.push(e.key);
        checkDeathSequence();
    });
    
    // Khởi tạo hệ thống
    logDeath('Death protection system initialized');
    
})(); 