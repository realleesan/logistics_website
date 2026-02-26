<?php
require_once 'config.php';
requireAdminLogin();

$page_title = 'Chỉnh sửa Dịch vụ';

// Get service ID
$id = $_GET['id'] ?? '';
if (empty($id)) {
    header('Location: services.php');
    exit();
}

// Get service details
try {
    $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->execute([$id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$service) {
        setMessage('Dịch vụ không tồn tại!', 'error');
        header('Location: services.php');
        exit();
    }
} catch (PDOException $e) {
    setMessage('Lỗi database: ' . $e->getMessage(), 'error');
    header('Location: services.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $short_description = trim($_POST['short_description'] ?? '');
    $content = $_POST['content'] ?? '';
    $status = $_POST['status'] ?? 'active';
    $sort_order = intval($_POST['sort_order'] ?? ($service['sort_order'] ?? 0));
    $featured = isset($_POST['featured']) ? 1 : 0;
    
    $errors = [];
    
    // Validation
    if (empty($title)) {
        $errors[] = 'Tiêu đề không được để trống';
    }
    if (empty($description)) {
        $errors[] = 'Mô tả không được để trống';
    }
    if (empty($content)) {
        $errors[] = 'Nội dung không được để trống';
    }
    
    // Generate slug from title
    $slug = generateSlug($title);
    
    // Check if slug already exists (exclude current service)
    if (!empty($slug)) {
        $stmt = $pdo->prepare("SELECT id FROM services WHERE slug = ? AND id != ?");
        $stmt->execute([$slug, $id]);
        if ($stmt->fetch()) {
            $errors[] = 'Tiêu đề đã tồn tại (slug trùng lặp)';
        }
    }
    
    // Handle image upload (uploadImage returns relative path string or false)
    $image_name = $service['image']; // Keep existing image by default
    if (!empty($_FILES['image']['name'])) {
        $uploaded_path = uploadImage($_FILES['image'], 'service_');
        if ($uploaded_path) {
            // Delete old image if exists
            if (!empty($service['image']) && file_exists('../' . $service['image'])) {
                unlink('../' . $service['image']);
            }
            $image_name = $uploaded_path; // e.g., assets/images/...
        } else {
            $errors[] = 'Lỗi khi upload hình ảnh. Vui lòng kiểm tra định dạng và kích thước file.';
        }
    }
    
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();
            // Shift sort_order for services with sort_order >= new_sort_order and not current service
            $shiftStmt = $pdo->prepare("UPDATE services SET sort_order = sort_order + 1 WHERE sort_order >= ? AND id <> ?");
            $shiftStmt->execute([$sort_order, $id]);
            
            $stmt = $pdo->prepare("
                UPDATE services 
                SET title = ?, slug = ?, description = ?, short_description = ?, content = ?, 
                    image = ?, status = ?, featured = ?, sort_order = ?, updated_at = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([
                $title, $slug, $description, $short_description, $content, 
                $image_name, $status, $featured, $sort_order, $id
            ]);
            $pdo->commit();
            
            setMessage('Cập nhật dịch vụ thành công!', 'success');
            header('Location: services.php');
            exit();
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
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
                <h1 class="h3 mb-2">Chỉnh sửa Dịch vụ</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="services.php">Dịch vụ</a></li>
                        <li class="breadcrumb-item active">Chỉnh sửa</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="services.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Service Edit Form -->
<div class="row">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Thông tin dịch vụ
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="title" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" 
                               value="<?php echo htmlspecialchars($service['title']); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="short_description" class="form-label">Mô tả ngắn</label>
                        <textarea class="form-control" id="short_description" name="short_description" rows="3" placeholder="Mỗi dòng tương ứng một gạch đầu dòng hiển thị ngoài website. Bạn cũng có thể ngăn cách bằng dấu ; hoặc |"><?php echo htmlspecialchars($service['short_description'] ?? ''); ?></textarea>
                        <div class="form-text">Nhập tối đa 4 ý chính. Ví dụ: "Thời gian: 3-5 ngày\nGiá cạnh tranh\nGiao tận nơi"</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($service['description']); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Hình ảnh</label>
                        <?php if (!empty($service['image'])): ?>
                            <div class="mb-2">
                                <img src="../<?php echo htmlspecialchars($service['image']); ?>" 
                                     alt="Current image" class="img-thumbnail" style="max-height: 150px;">
                                <div class="form-text">Hình ảnh hiện tại</div>
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <div class="form-text">Chọn file mới để thay đổi hình ảnh (JPG, PNG, tối đa 5MB)</div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="content" class="form-label">Nội dung chi tiết <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="content" name="content" rows="15"><?php echo htmlspecialchars($service['content']); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="sort_order" class="form-label">Thứ tự hiển thị</label>
                        <input type="number" class="form-control" id="sort_order" name="sort_order" value="<?php echo (int)($service['sort_order'] ?? 0); ?>" min="0">
                        <div class="form-text">Số nhỏ hiển thị trước.</div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-admin-primary">
                            <i class="fas fa-save me-2"></i>Cập nhật dịch vụ
                        </button>
                        <a href="services.php" class="btn btn-outline-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Status & Settings -->
        <div class="admin-card">
            <div class="card-header">
                <h6 class="mb-0">Cài đặt</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select class="form-select" id="status" name="status" form="serviceForm">
                        <option value="active" <?php echo $service['status'] === 'active' ? 'selected' : ''; ?>>Hoạt động</option>
                        <option value="inactive" <?php echo $service['status'] === 'inactive' ? 'selected' : ''; ?>>Không hoạt động</option>
                    </select>
                </div>
                
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="featured" name="featured" 
                           <?php echo $service['featured'] ? 'checked' : ''; ?> form="serviceForm">
                    <label class="form-check-label" for="featured">
                        Dịch vụ nổi bật
                    </label>
                    <div class="form-text">Hiển thị trong danh sách dịch vụ nổi bật</div>
                </div>
            </div>
        </div>
        
        <!-- Service Info -->
        <div class="admin-card">
            <div class="card-header">
                <h6 class="mb-0">Thông tin</h6>
            </div>
            <div class="card-body">
                <small class="text-muted">
                    <div><strong>ID:</strong> <?php echo $service['id']; ?></div>
                    <div><strong>Slug:</strong> <?php echo htmlspecialchars($service['slug']); ?></div>
                    <div><strong>Tạo:</strong> <?php echo date('d/m/Y H:i', strtotime($service['created_at'])); ?></div>
                    <div><strong>Cập nhật:</strong> <?php echo date('d/m/Y H:i', strtotime($service['updated_at'])); ?></div>
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
                    <a href="services.php" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-list me-1"></i>Danh sách dịch vụ
                    </a>
                    <a href="services-add.php" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-plus me-1"></i>Thêm dịch vụ mới
                    </a>
                    <?php if (!empty($service['slug'])): ?>
                        <a href="../services/<?php echo $service['slug']; ?>" target="_blank" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-external-link-alt me-1"></i>Xem trên website
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add form ID to main form -->
<script>
document.querySelector('form').id = 'serviceForm';

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
        .catch(error => {
            console.error(error);
        });
}
</script>

<?php include 'includes/footer.php'; ?> 