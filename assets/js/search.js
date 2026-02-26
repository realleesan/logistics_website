// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    // Tìm tất cả search forms
    const searchForms = document.querySelectorAll('.search-form');
    
    searchForms.forEach(function(form) {
        // Đảm bảo form có thể submit
        form.addEventListener('submit', function(e) {
            const input = form.querySelector('input[name="q"]');
            const query = input.value.trim();
            
            // Kiểm tra input có giá trị không
            if (query.length === 0) {
                e.preventDefault();
                input.focus();
                alert('Vui lòng nhập từ khóa tìm kiếm!');
                return false;
            }
            
            // Kiểm tra độ dài tối thiểu
            if (query.length < 2) {
                e.preventDefault();
                input.focus();
                alert('Từ khóa tối thiểu 2 ký tự!');
                return false;
            }
            
            // Log để debug
            console.log('🔍 Searching for:', query);
            
            // Form sẽ submit bình thường
            return true;
        });
        
        // Xử lý click button
        const button = form.querySelector('button[type="submit"]');
        if (button) {
            button.addEventListener('click', function(e) {
                console.log('🔧 Search button clicked!');
                // Không preventDefault, để form submit tự nhiên
            });
        }
        
        // Xử lý Enter key
        const input = form.querySelector('input[name="q"]');
        if (input) {
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    console.log('⌨️ Enter key pressed!');
                    form.dispatchEvent(new Event('submit'));
                }
            });
            
            // Fix input properties
            input.style.pointerEvents = 'auto';
            input.style.userSelect = 'auto';
            input.removeAttribute('readonly');
            input.removeAttribute('disabled');
        }
    });
    
    console.log('✅ Search functionality loaded!');
}); 