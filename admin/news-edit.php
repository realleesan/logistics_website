<?php
require_once 'config.php';
requireAdminLogin();

$page_title = 'Chỉnh sửa Tin tức';

// Get news ID
$id = $_GET['id'] ?? '';
if (empty($id)) {
    header('Location: news.php');
    exit();
}

// Get news details
try {
    $stmt = $pdo->prepare("
        SELECT n.*, c.name as category_name 
        FROM news n 
        LEFT JOIN news_categories c ON n.category_id = c.id 
        WHERE n.id = ?
    ");
    $stmt->execute([$id]);
    $news = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$news) {
        setMessage('Tin tức không tồn tại!', 'error');
        header('Location: news.php');
        exit();
    }
    
    // Kiểm tra ảnh hiện tại có tồn tại không (không tự động chỉnh sửa DB)
    if (!empty($news['image'])) {
        $absPath = __DIR__ . '/../' . ltrim($news['image'], '/\\');
        if (!file_exists($absPath)) {
            // Để nguyên dữ liệu DB, chỉ cảnh báo trong giao diện khi render bên dưới
        }
    }
} catch (PDOException $e) {
    setMessage('Lỗi database: ' . $e->getMessage(), 'error');
    header('Location: news.php');
    exit();
}

// Get categories for select
try {
    $categories_stmt = $pdo->query("SELECT * FROM news_categories WHERE status = 'active' ORDER BY name");
    $categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categories = [];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = $_POST['content'] ?? '';
    $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
    $status = $_POST['status'] ?? 'draft';
    $featured = isset($_POST['featured']) ? 1 : 0;
    $tags = trim($_POST['tags'] ?? '');
    $meta_title = trim($_POST['meta_title'] ?? '');
    $meta_description = trim($_POST['meta_description'] ?? '');
    
    $errors = [];
    
    // Validation
    if (empty($title)) {
        $errors[] = 'Tiêu đề không được để trống';
    }
    if (empty($content)) {
        $errors[] = 'Nội dung không được để trống';
    }
    
    // Generate slug from title
    $slug = generateSlug($title);
    
    // Check if slug already exists (exclude current news)
    if (!empty($slug)) {
        $stmt = $pdo->prepare("SELECT id FROM news WHERE slug = ? AND id != ?");
        $stmt->execute([$slug, $id]);
        if ($stmt->fetch()) {
            $errors[] = 'Tiêu đề đã tồn tại (slug trùng lặp)';
        }
    }
    
// Handle image upload (uploadImage returns relative path string or false)
$image_name = $news['image']; // Keep existing image by default
if (!empty($_FILES['image']['name'])) {
    // Kiểm tra function uploadImage có tồn tại không
    if (function_exists('uploadImage')) {
        $uploaded_path = uploadImage($_FILES['image'], 'news_');
        if ($uploaded_path) {
            // Delete old image if exists
            if (!empty($news['image']) && file_exists('../' . $news['image'])) {
                unlink('../' . $news['image']);
            }
            $image_name = $uploaded_path; // e.g., assets/images/...
        } else {
            $errors[] = 'Lỗi khi upload hình ảnh. Vui lòng kiểm tra định dạng và kích thước file.';
        }
    } else {
        $errors[] = 'Function uploadImage không tồn tại. Vui lòng kiểm tra cấu hình.';
    }
}
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                UPDATE news 
                SET title = ?, slug = ?, excerpt = ?, content = ?, category_id = ?, 
                    image = ?, status = ?, featured = ?, tags = ?, meta_title = ?, 
                    meta_description = ?, updated_at = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([
                $title, $slug, $excerpt, $content, $category_id, 
                $image_name, $status, $featured, $tags, $meta_title, 
                $meta_description, $id
            ]);
            
            setMessage('Cập nhật tin tức thành công!', 'success');
            header('Location: news.php');
            exit();
        } catch (PDOException $e) {
            setMessage('Lỗi database: ' . $e->getMessage(), 'error');
        }
    } else {
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
                <h1 class="h3 mb-2">Chỉnh sửa Tin tức</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="news.php">Tin tức</a></li>
                        <li class="breadcrumb-item active">Chỉnh sửa</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="news.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                </a>
            </div>
        </div>
    </div>
</div>

<!-- News Edit Form -->
<div class="row">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Thông tin tin tức
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data" id="newsForm">
                    <div class="mb-3">
                        <label for="title" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" 
                               value="<?php echo htmlspecialchars($news['title']); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="excerpt" class="form-label">Tóm tắt</label>
                        <textarea class="form-control" id="excerpt" name="excerpt" rows="3"
                                  placeholder="Tóm tắt ngắn gọn về nội dung tin tức..."><?php echo htmlspecialchars($news['excerpt'] ?? ''); ?></textarea>
                        <div class="form-text">Tóm tắt sẽ hiển thị trong danh sách tin tức</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Danh mục</label>
                        <select class="form-select" id="category_id" name="category_id">
                            <option value="">Chọn danh mục</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" 
                                        <?php echo $news['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (empty($categories)): ?>
                            <div class="form-text text-warning">
                                Chưa có danh mục nào. <a href="categories-add.php">Tạo danh mục mới</a>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Hình ảnh đại diện</label>
                        <?php if (!empty($news['image']) && file_exists('../' . $news['image'])): ?>
                            <div class="mb-2">
                                <img src="../<?php echo htmlspecialchars($news['image']); ?>" 
                                     alt="Current image" class="img-thumbnail" style="max-height: 150px;">
                                <div class="form-text">Hình ảnh hiện tại</div>
                            </div>
                        <?php elseif (!empty($news['image'])): ?>
                            <div class="mb-2">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Ảnh hiện tại không tồn tại: <?php echo htmlspecialchars($news['image']); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <div class="form-text">Chọn file mới để thay đổi hình ảnh (JPG, PNG, tối đa 5MB)</div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="content" class="form-label">Nội dung <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="content" name="content" rows="15"><?php echo htmlspecialchars($news['content']); ?></textarea>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-admin-primary">
                            <i class="fas fa-save me-2"></i>Cập nhật tin tức
                        </button>
                        <a href="news.php" class="btn btn-outline-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- SEO Settings -->
        <div class="admin-card">
            <div class="card-header">
                <h6 class="mb-0">Cài đặt SEO</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="meta_title" class="form-label">Meta Title</label>
                    <input type="text" class="form-control" id="meta_title" name="meta_title" 
                           value="<?php echo htmlspecialchars($news['meta_title'] ?? ''); ?>" 
                           maxlength="60" form="newsForm">
                    <div class="form-text">Tối đa 60 ký tự. Để trống để sử dụng tiêu đề chính</div>
                </div>
                
                <div class="mb-3">
                    <label for="meta_description" class="form-label">Meta Description</label>
                    <textarea class="form-control" id="meta_description" name="meta_description" 
                              rows="3" maxlength="160" form="newsForm"><?php echo htmlspecialchars($news['meta_description'] ?? ''); ?></textarea>
                    <div class="form-text">Tối đa 160 ký tự. Mô tả này sẽ hiển thị trên kết quả tìm kiếm</div>
                </div>
                
                <div class="mb-3">
                    <label for="tags" class="form-label">Tags</label>
                    <input type="text" class="form-control" id="tags" name="tags" 
                           value="<?php echo htmlspecialchars($news['tags'] ?? ''); ?>" 
                           form="newsForm" placeholder="tag1, tag2, tag3">
                    <div class="form-text">Phân cách các tag bằng dấu phẩy</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Status & Settings -->
        <div class="admin-card">
            <div class="card-header">
                <h6 class="mb-0">Cài đặt xuất bản</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select class="form-select" id="status" name="status" form="newsForm">
                        <option value="draft" <?php echo $news['status'] === 'draft' ? 'selected' : ''; ?>>Bản nháp</option>
                        <option value="published" <?php echo $news['status'] === 'published' ? 'selected' : ''; ?>>Đã xuất bản</option>
                    </select>
                </div>
                
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="featured" name="featured" 
                           <?php echo $news['featured'] ? 'checked' : ''; ?> form="newsForm">
                    <label class="form-check-label" for="featured">
                        Tin tức nổi bật
                    </label>
                    <div class="form-text">Hiển thị trong danh sách tin tức nổi bật</div>
                </div>
            </div>
        </div>
        
        <!-- News Info -->
        <div class="admin-card">
            <div class="card-header">
                <h6 class="mb-0">Thông tin</h6>
            </div>
            <div class="card-body">
                <small class="text-muted">
                    <div><strong>ID:</strong> <?php echo $news['id']; ?></div>
                    <div><strong>Slug:</strong> <?php echo htmlspecialchars($news['slug']); ?></div>
                    <div><strong>Danh mục:</strong> <?php echo htmlspecialchars($news['category_name'] ?? 'Không có'); ?></div>
                    <div><strong>Tạo:</strong> <?php echo date('d/m/Y H:i', strtotime($news['created_at'])); ?></div>
                    <div><strong>Cập nhật:</strong> <?php echo date('d/m/Y H:i', strtotime($news['updated_at'])); ?></div>
                </small>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="admin-card">
            <div class="card-header">
                <h6 class="mb-0">Thao tác nhanh</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="news.php" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-list me-1"></i>Danh sách tin tức
                    </a>
                    <a href="news-add.php" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-plus me-1"></i>Thêm tin tức mới
                    </a>
                    <a href="categories.php" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-folder me-1"></i>Quản lý danh mục
                    </a>
                    <?php if (!empty($news['slug']) && $news['status'] === 'published'): ?>
                        <a href="../news/<?php echo $news['slug']; ?>" target="_blank" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-external-link-alt me-1"></i>Xem trên website
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Image Preview -->
        <?php if (!empty($news['image']) && file_exists('../' . $news['image'])): ?>
            <div class="admin-card">
                <div class="card-header">
                    <h6 class="mb-0">Hình ảnh đại diện</h6>
                </div>
                <div class="card-body text-center">
                    <img src="../<?php echo htmlspecialchars($news['image']); ?>" 
                         alt="News image" class="img-fluid rounded" style="max-height: 200px;">
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Initialize CKEditor
if (typeof ClassicEditor !== 'undefined') {
    ClassicEditor
        .create(document.querySelector('#content'), {
            toolbar: {
                items: [
                    'heading', '|',
                    'bold', 'italic', 'link', '|',
                    'bulletedList', 'numberedList', '|',
                    'outdent', 'indent', '|',
                    'imageUpload', 'blockQuote', 'insertTable', '|',
                    'undo', 'redo'
                ]
            },
            language: 'vi',
            image: {
                toolbar: [
                    'imageTextAlternative',
                    'imageStyle:full',
                    'imageStyle:side'
                ]
            },
            table: {
                contentToolbar: [
                    'tableColumn',
                    'tableRow',
                    'mergeTableCells'
                ]
            }
        })
        .then(editor => {
            const form = document.getElementById('newsForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const textarea = document.getElementById('content');
                    textarea.value = editor.getData().trim();
                    if (!textarea.value) {
                        e.preventDefault();
                        alert('Vui lòng nhập nội dung.');
                        editor.editing.view.focus();
                    }
                });
            }
        })
        .catch(error => {
            console.error(error);
        });
}

// Character counters for SEO fields
document.getElementById('meta_title').addEventListener('input', function() {
    updateCharCounter(this, 60);
});

document.getElementById('meta_description').addEventListener('input', function() {
    updateCharCounter(this, 160);
});

function updateCharCounter(element, maxLength) {
    const current = element.value.length;
    const remaining = maxLength - current;
    
    // Find or create counter element
    let counter = element.parentNode.querySelector('.char-counter');
    if (!counter) {
        counter = document.createElement('div');
        counter.className = 'char-counter form-text';
        element.parentNode.appendChild(counter);
    }
    
    counter.textContent = `${current}/${maxLength} ký tự`;
    counter.className = remaining < 0 ? 'char-counter form-text text-danger' : 'char-counter form-text';
}
</script>

<?php include 'includes/footer.php'; ?> 