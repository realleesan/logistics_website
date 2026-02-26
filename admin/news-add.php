<?php
require_once 'config.php';
requireAdminLogin();

$page_title = 'Thêm Tin tức mới';

// Get categories for dropdown
$stmt = $pdo->query("SELECT * FROM news_categories WHERE status = 'active' ORDER BY name");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    $status = $_POST['status'] ?? 'draft';
    $featured = isset($_POST['featured']) ? 1 : 0;
    $tags = trim($_POST['tags'] ?? '');
    $meta_title = trim($_POST['meta_title'] ?? '');
    $meta_description = trim($_POST['meta_description'] ?? '');
    
    $errors = [];
    
    // Validation
    if (empty($title)) {
        $errors[] = 'Tiêu đề tin tức không được để trống';
    }
    
    if (empty($excerpt)) {
        $errors[] = 'Tóm tắt tin tức không được để trống';
    }
    
    if (empty($content)) {
        $errors[] = 'Nội dung tin tức không được để trống';
    }
    
    if ($category_id <= 0) {
        $errors[] = 'Vui lòng chọn danh mục';
    }
    
    // Check if title already exists
    if (!empty($title)) {
        $stmt = $pdo->prepare("SELECT id FROM news WHERE title = ?");
        $stmt->execute([$title]);
        if ($stmt->fetch()) {
            $errors[] = 'Tiêu đề tin tức đã tồn tại';
        }
    }
    
    if (empty($errors)) {
        try {
            // Handle image upload
            $image_path = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                if (function_exists('uploadImage')) {
                    $uploaded_path = uploadImage($_FILES['image'], 'news_');
                    if ($uploaded_path) {
                        $image_path = $uploaded_path; // e.g., assets/images/...
                    } else {
                        $errors[] = 'Lỗi khi upload hình ảnh. Vui lòng kiểm tra định dạng và kích thước file.';
                    }
                } else {
                    $errors[] = 'Function uploadImage không tồn tại. Vui lòng kiểm tra cấu hình.';
                }
            }
            
            if (empty($errors)) {
                // Generate slug
                $slug = generateSlug($title);
                
                // Ensure slug is unique
                $original_slug = $slug;
                $counter = 1;
                while (true) {
                    $stmt = $pdo->prepare("SELECT id FROM news WHERE slug = ?");
                    $stmt->execute([$slug]);
                    if (!$stmt->fetch()) {
                        break;
                    }
                    $slug = $original_slug . '-' . $counter;
                    $counter++;
                }
                
                // Set meta title if empty
                if (empty($meta_title)) {
                    $meta_title = $title;
                }
                
                // Set meta description if empty
                if (empty($meta_description)) {
                    $meta_description = substr(strip_tags($excerpt), 0, 160);
                }
                
                // Insert news
                $stmt = $pdo->prepare("
                    INSERT INTO news (title, slug, excerpt, content, category_id, image, status, featured, tags, meta_title, meta_description, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                
                $stmt->execute([
                    $title,
                    $slug,
                    $excerpt,
                    $content,
                    $category_id,
                    $image_path,
                    $status,
                    $featured,
                    $tags,
                    $meta_title,
                    $meta_description
                ]);
                
                setMessage('Thêm tin tức thành công!', 'success');
                header('Location: news.php');
                exit();
            }
        } catch (PDOException $e) {
            $errors[] = 'Lỗi database: ' . $e->getMessage();
        }
    }
    
    if (!empty($errors)) {
        setMessage(implode('<br>', $errors), 'error');
    }
}

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-2">Thêm Tin tức mới</h1>
                <p class="text-muted mb-0">Tạo tin tức mới cho trang web</p>
            </div>
            <div>
                <a href="news.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
                </a>
            </div>
        </div>
    </div>
</div>

<!-- News Form -->
<form method="POST" enctype="multipart/form-data" class="form-admin">
    <div class="row">
        <div class="col-lg-8">
            <!-- Main Content -->
            <div class="admin-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Nội dung tin tức
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" 
                               value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" 
                               placeholder="Nhập tiêu đề tin tức..." required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="excerpt" class="form-label">Tóm tắt <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="excerpt" name="excerpt" 
                                  rows="3" placeholder="Tóm tắt ngắn gọn về tin tức..." required><?php echo htmlspecialchars($_POST['excerpt'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">Nội dung <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="content" name="content" 
                                  rows="15" placeholder="Nội dung chi tiết tin tức..."><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>
            
            <!-- SEO Settings -->
            <div class="admin-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-search me-2"></i>
                        Tối ưu SEO
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="meta_title" class="form-label">Meta Title</label>
                        <input type="text" class="form-control" id="meta_title" name="meta_title" 
                               value="<?php echo htmlspecialchars($_POST['meta_title'] ?? ''); ?>" 
                               placeholder="Tiêu đề SEO (để trống sẽ dùng tiêu đề tin tức)">
                        <small class="form-text text-muted">Khuyến nghị: 50-60 ký tự</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="meta_description" class="form-label">Meta Description</label>
                        <textarea class="form-control" id="meta_description" name="meta_description" 
                                  rows="2" placeholder="Mô tả SEO (để trống sẽ dùng tóm tắt)"><?php echo htmlspecialchars($_POST['meta_description'] ?? ''); ?></textarea>
                        <small class="form-text text-muted">Khuyến nghị: 150-160 ký tự</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tags" class="form-label">Tags</label>
                        <input type="text" class="form-control" id="tags" name="tags" 
                               value="<?php echo htmlspecialchars($_POST['tags'] ?? ''); ?>" 
                               placeholder="Nhập các tag, cách nhau bằng dấu phẩy">
                        <small class="form-text text-muted">Ví dụ: logistics, vận chuyển, xuất khẩu</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Publish Settings -->
            <div class="admin-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-cog me-2"></i>
                        Cài đặt xuất bản
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-select" id="status" name="status">
                            <option value="draft" <?php echo ($_POST['status'] ?? 'draft') === 'draft' ? 'selected' : ''; ?>>Nháp</option>
                            <option value="published" <?php echo ($_POST['status'] ?? '') === 'published' ? 'selected' : ''; ?>>Đã xuất bản</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Danh mục <span class="text-danger">*</span></label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">Chọn danh mục...</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" 
                                        <?php echo ($_POST['category_id'] ?? '') == $category['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (empty($categories)): ?>
                            <small class="form-text text-warning">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Chưa có danh mục nào. <a href="categories-add.php">Tạo danh mục mới</a>
                            </small>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="featured" name="featured" value="1"
                                   <?php echo isset($_POST['featured']) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="featured">
                                Tin tức nổi bật
                            </label>
                        </div>
                        <small class="form-text text-muted">Tin tức nổi bật sẽ hiển thị ở vị trí đặc biệt</small>
                    </div>
                </div>
            </div>
            
            <!-- Featured Image -->
            <div class="admin-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-image me-2"></i>
                        Hình ảnh đại diện
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <small class="form-text text-muted">
                            Định dạng: JPG, PNG, GIF. Kích thước tối đa: 5MB<br>
                            Kích thước khuyến nghị: 800x600px
                        </small>
                    </div>
                    
                    <div id="imagePreview" class="text-center" style="display: none;">
                        <img id="previewImg" src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeImagePreview()">
                                <i class="fas fa-times me-1"></i>Xóa ảnh
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="admin-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        Thao tác nhanh
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-admin-primary">
                            <i class="fas fa-save me-2"></i>Lưu tin tức
                        </button>
                        <a href="news.php" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Hủy bỏ
                        </a>
                        <hr>
                        <a href="news.php" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-list me-2"></i>Xem tất cả tin tức
                        </a>
                        <a href="categories.php" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-tags me-2"></i>Quản lý danh mục
                        </a>
                        <a href="../news.php" target="_blank" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-external-link-alt me-2"></i>Xem trang tin tức
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Rich Text Editor -->
<script src="https://cdn.ckeditor.com/ckeditor5/35.0.1/classic/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize CKEditor for content field
ClassicEditor
        .create(document.querySelector('#content'), {
            toolbar: [
                'heading', '|',
                'bold', 'italic', 'link', '|',
                'bulletedList', 'numberedList', '|',
                'outdent', 'indent', '|',
                'blockQuote', 'insertTable', '|',
                'imageUpload', 'mediaEmbed', '|',
                'undo', 'redo'
            ],
            language: 'vi'
        })
        .then(editor => {
            const form = document.querySelector('form.form-admin');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const textarea = document.getElementById('content');
                    textarea.value = editor.getData().trim();
                    if (!textarea.value) {
                        e.preventDefault();
                        alert('Vui lòng nhập nội dung tin tức.');
                        editor.editing.view.focus();
                    }
                });
            }
        })
        .catch(error => {
            console.error(error);
        });
    
    // Image preview functionality
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImg').src = e.target.result;
                document.getElementById('imagePreview').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Auto-generate meta title from title
    document.getElementById('title').addEventListener('blur', function() {
        const metaTitle = document.getElementById('meta_title');
        if (!metaTitle.value.trim()) {
            metaTitle.value = this.value;
        }
    });
    
    // Auto-generate meta description from excerpt
    document.getElementById('excerpt').addEventListener('blur', function() {
        const metaDescription = document.getElementById('meta_description');
        if (!metaDescription.value.trim()) {
            metaDescription.value = this.value.substring(0, 160);
        }
    });
    
    // Character count for meta fields
    function updateCharCount(fieldId, countId) {
        const field = document.getElementById(fieldId);
        const count = document.getElementById(countId);
        if (field && count) {
            field.addEventListener('input', function() {
                count.textContent = this.value.length;
            });
        }
    }
    
    // Add character counters if elements exist
    const metaTitle = document.getElementById('meta_title');
    const metaDescription = document.getElementById('meta_description');
    
    if (metaTitle) {
        const titleCounter = document.createElement('small');
        titleCounter.className = 'form-text text-muted float-end';
        titleCounter.innerHTML = '<span id="metaTitleCount">0</span>/60 ký tự';
        metaTitle.parentNode.appendChild(titleCounter);
        updateCharCount('meta_title', 'metaTitleCount');
    }
    
    if (metaDescription) {
        const descCounter = document.createElement('small');
        descCounter.className = 'form-text text-muted float-end';
        descCounter.innerHTML = '<span id="metaDescCount">0</span>/160 ký tự';
        metaDescription.parentNode.appendChild(descCounter);
        updateCharCount('meta_description', 'metaDescCount');
    }
});

function removeImagePreview() {
    document.getElementById('image').value = '';
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('previewImg').src = '';
}
</script>

<?php include 'includes/footer.php'; ?> 