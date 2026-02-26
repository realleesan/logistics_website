        </div>
    </div>

    <!-- JavaScript Files -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Admin JavaScript -->
    <script>
        // Confirm delete actions
        function confirmDelete(message = 'Bạn có chắc chắn muốn xóa mục này?') {
            return confirm(message);
        }
        
        // Auto hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    if (alert.classList.contains('show')) {
                        alert.classList.remove('show');
                        alert.classList.add('fade');
                        setTimeout(function() {
                            alert.remove();
                        }, 300);
                    }
                }, 5000);
            });
        });
        
        // Image preview
        function previewImage(input, previewId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(previewId).src = e.target.result;
                    document.getElementById(previewId).style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        // Auto-generate slug from title
        function generateSlug(titleInput, slugInput) {
            const title = titleInput.value;
            let slug = title.toLowerCase();
            
            // Vietnamese character mapping
            const vietnamese = {
                'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ': 'a',
                'đ': 'd',
                'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ': 'e',
                'í|ì|ỉ|ĩ|ị': 'i',
                'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ': 'o',
                'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự': 'u',
                'ý|ỳ|ỷ|ỹ|ỵ': 'y'
            };
            
            for (let key in vietnamese) {
                const regex = new RegExp(key, 'gi');
                slug = slug.replace(regex, vietnamese[key]);
            }
            
            slug = slug.replace(/[^a-z0-9\s]/gi, '')
                       .replace(/\s+/g, '-')
                       .replace(/-+/g, '-')
                       .replace(/^-|-$/g, '');
            
            slugInput.value = slug;
        }
        
        // Table search functionality
        function searchTable(searchInput, tableId) {
            const searchTerm = searchInput.value.toLowerCase();
            const table = document.getElementById(tableId);
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        }
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
</body>
</html> 