<?php
require_once 'config.php';
requireAdminLogin();

$page_title = 'Thêm danh mục tin tức';

// Xử lý form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $status = $_POST['status'] ?? 'active';
    
    $errors = [];
    
    // Validation
    if (empty($name)) {
        $errors[] = 'Tên danh mục không được để trống';
    }
    
    if (empty($slug)) {
        $slug = generateSlug($name);
    }
    
    // Kiểm tra slug trùng lặp
    if (!empty($slug)) {
        $stmt = $pdo->prepare("SELECT id FROM news_categories WHERE slug = ?");
        $stmt->execute([$slug]);
        if ($stmt->fetchColumn()) {
            $errors[] = 'Slug này đã tồn tại, vui lòng chọn slug khác';
        }
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO news_categories (name, slug, description, sort_order, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $slug, $description, $sort_order, $status]);
            
            setMessage('Thêm danh mục thành công!');
            header('Location: categories.php');
            exit();
        } catch (PDOException $e) {
            $errors[] = 'Lỗi database: ' . $e->getMessage();
        }
    }
}

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-2">Thêm danh mục tin tức</h1>
                <p class="text-muted mb-0">Tạo danh mục mới cho tin tức và bài viết</p>
            </div>
            <a href="categories.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
        </div>
    </div>
</div>

<!-- Form -->
<div class="row">
    <div class="col-xl-8">
        <div class="admin-card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-plus me-2"></i>
                    Thông tin danh mục
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="form-admin">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="name" class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" 
                                   placeholder="Nhập tên danh mục..."
                                   onkeyup="generateSlug(this, document.getElementById('slug'))" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="sort_order" class="form-label">Thứ tự sắp xếp</label>
                            <input type="number" class="form-control" id="sort_order" name="sort_order" 
                                   value="<?php echo htmlspecialchars($_POST['sort_order'] ?? '0'); ?>" 
                                   min="0" max="999">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug (URL thân thiện)</label>
                        <input type="text" class="form-control" id="slug" name="slug" 
                               value="<?php echo htmlspecialchars($_POST['slug'] ?? ''); ?>" 
                               placeholder="tu-dong-tao-tu-ten-danh-muc">
                        <div class="form-text">
                            Slug sẽ được tạo tự động từ tên danh mục. Bạn có thể chỉnh sửa nếu cần.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="description" name="description" rows="4" 
                                  placeholder="Nhập mô tả cho danh mục..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-control" id="status" name="status">
                            <option value="active" <?php echo ($_POST['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>
                                Hoạt động
                            </option>
                            <option value="inactive" <?php echo ($_POST['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>
                                Ngưng hoạt động
                            </option>
                        </select>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-admin-primary">
                            <i class="fas fa-save me-2"></i>Lưu danh mục
                        </button>
                        <a href="categories.php" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Hủy bỏ
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="col-xl-4">
        <div class="admin-card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Hướng dẫn
                </h6>
            </div>
            <div class="card-body">
                <h6>Tên danh mục</h6>
                <p class="small text-muted">Tên hiển thị của danh mục tin tức. Nên ngắn gọn và dễ hiểu.</p>
                
                <h6>Slug</h6>
                <p class="small text-muted">URL thân thiện cho danh mục. Sẽ được tạo tự động từ tên danh mục.</p>
                
                <h6>Thứ tự sắp xếp</h6>
                <p class="small text-muted">Số càng nhỏ sẽ hiển thị trước. Mặc định là 0.</p>
                
                <h6>Mô tả</h6>
                <p class="small text-muted">Mô tả ngắn về danh mục này. Có thể để trống.</p>
            </div>
        </div>
        
        <div class="admin-card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-eye me-2"></i>
                    Preview URL
                </h6>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-2">URL sẽ hiển thị như:</p>
                <div class="bg-light p-2 rounded">
                    <code id="preview-url">
                        <?php echo $_SERVER['HTTP_HOST']; ?>/news.php?category=<span id="slug-preview">slug-danh-muc</span>
                    </code>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Update preview URL when slug changes
document.getElementById('slug').addEventListener('input', function() {
    const slugPreview = document.getElementById('slug-preview');
    slugPreview.textContent = this.value || 'slug-danh-muc';
});

// Auto-generate slug and update preview
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
    
    // Update preview
    const slugPreview = document.getElementById('slug-preview');
    slugPreview.textContent = slug || 'slug-danh-muc';
}
</script>

<?php include 'includes/footer.php'; ?> 