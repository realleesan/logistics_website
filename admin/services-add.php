<?php
require_once 'config.php';
requireAdminLogin();

$page_title = 'Thêm Dịch vụ mới';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $short_description = trim($_POST['short_description'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $status = $_POST['status'] ?? 'active';
    $sort_order = intval($_POST['sort_order'] ?? 0);
    $featured = isset($_POST['featured']) ? 1 : 0;
    
    $errors = [];
    
    // Validation
    if (empty($title)) {
        $errors[] = 'Tên dịch vụ không được để trống';
    }
    
    if (empty($description)) {
        $errors[] = 'Mô tả dịch vụ không được để trống';
    }
    
    if (empty($content)) {
        $errors[] = 'Nội dung chi tiết không được để trống';
    }
    
    // Check if title already exists
    if (!empty($title)) {
        $stmt = $pdo->prepare("SELECT id FROM services WHERE title = ?");
        $stmt->execute([$title]);
        if ($stmt->fetch()) {
            $errors[] = 'Tên dịch vụ đã tồn tại';
        }
    }
    
    if (empty($errors)) {
        try {
            // Handle image upload
            $image_path = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $image_path = uploadImage($_FILES['image'], 'service_');
                if (!$image_path) {
                    $errors[] = 'Lỗi khi upload hình ảnh. Vui lòng kiểm tra định dạng và kích thước file.';
                }
            }
            
            if (empty($errors)) {
                // Generate slug
                $slug = generateSlug($title);
                
                // Ensure slug is unique
                $original_slug = $slug;
                $counter = 1;
                while (true) {
                    $stmt = $pdo->prepare("SELECT id FROM services WHERE slug = ?");
                    $stmt->execute([$slug]);
                    if (!$stmt->fetch()) {
                        break;
                    }
                    $slug = $original_slug . '-' . $counter;
                    $counter++;
                }
                
                // Atomic insert + sort shifting to avoid race
                $pdo->beginTransaction();
                if ($sort_order <= 0) {
                    $maxStmt = $pdo->query("SELECT COALESCE(MAX(sort_order), 0) FROM services");
                    $sort_order = (int)$maxStmt->fetchColumn() + 1;
                } else {
                    $shiftStmt = $pdo->prepare("UPDATE services SET sort_order = sort_order + 1 WHERE sort_order >= ?");
                    $shiftStmt->execute([$sort_order]);
                }

                // Insert service
                $stmt = $pdo->prepare("
                    INSERT INTO services (title, slug, description, short_description, content, image, status, featured, sort_order, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                
                $stmt->execute([
                    $title,
                    $slug,
                    $description,
                    $short_description,
                    $content,
                    $image_path,
                    $status,
                    $featured,
                    $sort_order
                ]);
                $pdo->commit();
                
                setMessage('Thêm dịch vụ thành công!', 'success');
                header('Location: services.php');
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
                <h1 class="h3 mb-2">Thêm Dịch vụ mới</h1>
                <p class="text-muted mb-0">Thêm dịch vụ logistics mới cho công ty</p>
            </div>
            <div>
                <a href="services.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Service Form -->
<div class="row">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-plus me-2"></i>
                    Thông tin dịch vụ
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data" class="form-admin">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="title" class="form-label">Tên dịch vụ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" 
                                   placeholder="Nhập tên dịch vụ..." required>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="short_description" class="form-label">Mô tả ngắn</label>
                            <textarea class="form-control" id="short_description" name="short_description" 
                                      rows="3" placeholder="Mỗi dòng tương ứng một gạch đầu dòng hiển thị ngoài website. Bạn cũng có thể ngăn cách bằng dấu ; hoặc |"><?php echo htmlspecialchars($_POST['short_description'] ?? ''); ?></textarea>
                            <small class="form-text text-muted">Ví dụ: "Thời gian: 3-5 ngày\nGiá cạnh tranh\nGiao tận nơi"</small>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="description" class="form-label">Mô tả dịch vụ <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" 
                                      rows="4" placeholder="Mô tả chi tiết về dịch vụ..." required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="content" class="form-label">Nội dung chi tiết <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="content" name="content" 
                                      rows="8" placeholder="Nội dung chi tiết về dịch vụ..."><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
                            <small class="form-text text-muted">Có thể sử dụng HTML để định dạng nội dung</small>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="image" class="form-label">Hình ảnh dịch vụ</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <small class="form-text text-muted">
                                Chọn hình ảnh đại diện cho dịch vụ. Định dạng: JPG, PNG, GIF. Kích thước tối đa: 5MB
                            </small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" <?php echo ($_POST['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Hoạt động</option>
                                <option value="inactive" <?php echo ($_POST['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Không hoạt động</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="sort_order" class="form-label">Thứ tự hiển thị</label>
                            <input type="number" class="form-control" id="sort_order" name="sort_order" value="<?php echo htmlspecialchars($_POST['sort_order'] ?? '0'); ?>" min="0">
                            <small class="form-text text-muted">Số nhỏ hiển thị trước. Bạn có thể thay đổi sau trong danh sách.</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tùy chọn</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="featured" name="featured" value="1"
                                       <?php echo isset($_POST['featured']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="featured">
                                    Dịch vụ nổi bật
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-admin-primary">
                            <i class="fas fa-save me-2"></i>Lưu dịch vụ
                        </button>
                        <a href="services.php" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Hủy bỏ
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Help Panel -->
        <div class="admin-card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-question-circle me-2"></i>
                    Hướng dẫn
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6><i class="fas fa-lightbulb text-warning me-2"></i>Lưu ý quan trọng</h6>
                    <ul class="small mb-0">
                        <li>Tên dịch vụ phải là duy nhất</li>
                        <li>Mỗi dòng trong <strong>Mô tả ngắn</strong> sẽ hiển thị thành 1 gạch đầu dòng ở trang dịch vụ</li>
                        <li>Nội dung chi tiết hỗ trợ HTML</li>
                        <li>Hình ảnh nên có tỷ lệ 16:9 để hiển thị tốt nhất</li>
                    </ul>
                </div>
                
                <div class="mb-3">
                    <h6><i class="fas fa-image text-info me-2"></i>Về hình ảnh</h6>
                    <ul class="small mb-0">
                        <li>Định dạng hỗ trợ: JPG, PNG, GIF</li>
                        <li>Kích thước tối đa: 5MB</li>
                        <li>Kích thước khuyến nghị: 800x450px</li>
                    </ul>
                </div>
                
                <div>
                    <h6><i class="fas fa-star text-success me-2"></i>Dịch vụ nổi bật</h6>
                    <p class="small mb-0">Dịch vụ nổi bật sẽ được ưu tiên hiển thị trên trang chủ và có thể có vị trí đặc biệt trong giao diện.</p>
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
                    <a href="services.php" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-list me-2"></i>Xem tất cả dịch vụ
                    </a>
                    <a href="../services.php" target="_blank" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-external-link-alt me-2"></i>Xem trang dịch vụ
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

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
                        alert('Vui lòng nhập nội dung chi tiết dịch vụ.');
                        editor.editing.view.focus();
                    }
                });
            }
        })
        .catch(error => {
            console.error(error);
        });
});
</script>

<?php include 'includes/footer.php'; ?> 