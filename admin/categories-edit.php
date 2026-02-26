<?php
require_once 'config.php';
requireAdminLogin();

$page_title = 'Chỉnh sửa danh mục tin tức';

// Lấy ID danh mục
$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    setMessage('ID danh mục không hợp lệ!', 'error');
    header('Location: categories.php');
    exit();
}

// Lấy thông tin danh mục
$stmt = $pdo->prepare("SELECT * FROM news_categories WHERE id = ?");
$stmt->execute([$id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    setMessage('Không tìm thấy danh mục!', 'error');
    header('Location: categories.php');
    exit();
}

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
    
    // Kiểm tra slug trùng lặp (trừ chính nó)
    if (!empty($slug)) {
        $stmt = $pdo->prepare("SELECT id FROM news_categories WHERE slug = ? AND id != ?");
        $stmt->execute([$slug, $id]);
        if ($stmt->fetchColumn()) {
            $errors[] = 'Slug này đã tồn tại, vui lòng chọn slug khác';
        }
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE news_categories SET name = ?, slug = ?, description = ?, sort_order = ?, status = ? WHERE id = ?");
            $stmt->execute([$name, $slug, $description, $sort_order, $status, $id]);
            
            setMessage('Cập nhật danh mục thành công!');
            header('Location: categories.php');
            exit();
        } catch (PDOException $e) {
            $errors[] = 'Lỗi database: ' . $e->getMessage();
        }
    }
} else {
    // Pre-fill form với dữ liệu hiện tại
    $_POST = $category;
}

// Lấy số lượng tin tức trong danh mục
$stmt = $pdo->prepare("SELECT COUNT(*) FROM news WHERE category_id = ?");
$stmt->execute([$id]);
$newsCount = $stmt->fetchColumn();

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-2">Chỉnh sửa danh mục tin tức</h1>
                <p class="text-muted mb-0">ID: #<?php echo $category['id']; ?> - <?php echo htmlspecialchars($category['name']); ?></p>
            </div>
            <div class="btn-group">
                <a href="categories.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                </a>
                <?php if ($newsCount > 0): ?>
                    <a href="news.php?category=<?php echo $id; ?>" class="btn btn-outline-info">
                        <i class="fas fa-newspaper me-2"></i>Xem tin tức (<?php echo $newsCount; ?>)
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Form -->
<div class="row">
    <div class="col-xl-8">
        <div class="admin-card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
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
                                   value="<?php echo htmlspecialchars($_POST['sort_order'] ?? ''); ?>" 
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
                            <option value="active" <?php echo ($_POST['status'] ?? '') === 'active' ? 'selected' : ''; ?>>
                                Hoạt động
                            </option>
                            <option value="inactive" <?php echo ($_POST['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>
                                Ngưng hoạt động
                            </option>
                        </select>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-admin-primary">
                            <i class="fas fa-save me-2"></i>Cập nhật danh mục
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
                    Thông tin danh mục
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <strong>ID:</strong><br>
                        <span class="text-muted">#<?php echo $category['id']; ?></span>
                    </div>
                    <div class="col-6">
                        <strong>Tin tức:</strong><br>
                        <span class="text-muted"><?php echo number_format($newsCount); ?> bài</span>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-6">
                        <strong>Tạo lúc:</strong><br>
                        <span class="text-muted small"><?php echo date('d/m/Y H:i', strtotime($category['created_at'])); ?></span>
                    </div>
                    <div class="col-6">
                        <strong>Trạng thái:</strong><br>
                        <span class="status-badge status-<?php echo $category['status']; ?>">
                            <?php echo $category['status'] === 'active' ? 'Hoạt động' : 'Ngưng'; ?>
                        </span>
                    </div>
                </div>
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
                        <?php echo $_SERVER['HTTP_HOST']; ?>/news.php?category=<span id="slug-preview"><?php echo htmlspecialchars($category['slug']); ?></span>
                    </code>
                </div>
            </div>
        </div>
        
        <?php if ($newsCount > 0): ?>
        <div class="admin-card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Lưu ý khi xóa
                </h6>
            </div>
            <div class="card-body">
                <p class="small text-warning mb-0">
                    Danh mục này có <?php echo $newsCount; ?> tin tức đang sử dụng. 
                    Bạn cần chuyển tất cả tin tức sang danh mục khác trước khi xóa.
                </p>
            </div>
        </div>
        <?php endif; ?>
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