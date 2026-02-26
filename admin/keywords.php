<?php
require_once 'config.php';
requireAdminLogin();

$page_title = 'Quản lý Từ khóa';

// Runtime safeguard: ensure table `keywords` has required columns used by this page
try {
    $existing = [];
    $colsStmt = $pdo->query("SHOW COLUMNS FROM keywords");
    foreach ($colsStmt->fetchAll(PDO::FETCH_ASSOC) as $col) {
        $existing[strtolower($col['Field'])] = true;
    }
    if (!isset($existing['search_volume'])) {
        $pdo->exec("ALTER TABLE keywords ADD COLUMN search_volume int(11) DEFAULT 0 AFTER keyword");
    }
    if (!isset($existing['difficulty'])) {
        $pdo->exec("ALTER TABLE keywords ADD COLUMN difficulty enum('easy','medium','hard') DEFAULT 'medium' AFTER search_volume");
    }
    if (!isset($existing['status'])) {
        $pdo->exec("ALTER TABLE keywords ADD COLUMN status enum('active','inactive') DEFAULT 'active' AFTER difficulty");
    }
    if (!isset($existing['notes'])) {
        $pdo->exec("ALTER TABLE keywords ADD COLUMN notes text AFTER status");
    }
    if (!isset($existing['created_at'])) {
        $pdo->exec("ALTER TABLE keywords ADD COLUMN created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP");
    }
    if (!isset($existing['updated_at'])) {
        $pdo->exec("ALTER TABLE keywords ADD COLUMN updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
    }
} catch (Throwable $___e) {
    // ignore, fallback on insert/update catch below
}

// Handle actions
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $keyword = trim($_POST['keyword'] ?? '');
        $search_volume = intval($_POST['search_volume'] ?? 0);
        $difficulty = $_POST['difficulty'] ?? 'medium';
        $status = $_POST['status'] ?? 'active';
        $notes = trim($_POST['notes'] ?? '');
        
        $errors = [];
        
        if (empty($keyword)) {
            $errors[] = 'Từ khóa không được để trống';
        }
        
        // Check if keyword already exists
        if (!empty($keyword)) {
            $stmt = $pdo->prepare("SELECT id FROM keywords WHERE keyword = ?");
            $stmt->execute([$keyword]);
            if ($stmt->fetch()) {
                $errors[] = 'Từ khóa đã tồn tại';
            }
        }
        
        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO keywords (keyword, search_volume, difficulty, status, notes, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, NOW(), NOW())
                ");
                $stmt->execute([$keyword, $search_volume, $difficulty, $status, $notes]);
                
                setMessage('Thêm từ khóa thành công!', 'success');
            } catch (PDOException $e) {
                setMessage('Lỗi database: ' . $e->getMessage(), 'error');
            }
        } else {
            setMessage(implode('<br>', $errors), 'error');
        }
        
        header('Location: keywords.php');
        exit();
    }
    
    if ($action === 'edit' && !empty($_POST['id'])) {
        $keyword = trim($_POST['keyword'] ?? '');
        $search_volume = intval($_POST['search_volume'] ?? 0);
        $difficulty = $_POST['difficulty'] ?? 'medium';
        $status = $_POST['status'] ?? 'active';
        $notes = trim($_POST['notes'] ?? '');
        
        $errors = [];
        
        if (empty($keyword)) {
            $errors[] = 'Từ khóa không được để trống';
        }
        
        // Check if keyword already exists (exclude current)
        if (!empty($keyword)) {
            $stmt = $pdo->prepare("SELECT id FROM keywords WHERE keyword = ? AND id != ?");
            $stmt->execute([$keyword, $_POST['id']]);
            if ($stmt->fetch()) {
                $errors[] = 'Từ khóa đã tồn tại';
            }
        }
        
        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare("
                    UPDATE keywords 
                    SET keyword = ?, search_volume = ?, difficulty = ?, status = ?, notes = ?, updated_at = NOW() 
                    WHERE id = ?
                ");
                $stmt->execute([$keyword, $search_volume, $difficulty, $status, $notes, $_POST['id']]);
                
                setMessage('Cập nhật từ khóa thành công!', 'success');
            } catch (PDOException $e) {
                setMessage('Lỗi database: ' . $e->getMessage(), 'error');
            }
        } else {
            setMessage(implode('<br>', $errors), 'error');
        }
        
        header('Location: keywords.php');
        exit();
    }
    
    if ($action === 'delete' && !empty($_POST['id'])) {
        try {
            $stmt = $pdo->prepare("DELETE FROM keywords WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            
            setMessage('Xóa từ khóa thành công!', 'success');
        } catch (PDOException $e) {
            setMessage('Lỗi khi xóa từ khóa: ' . $e->getMessage(), 'error');
        }
        
        header('Location: keywords.php');
        exit();
    }
}

// Pagination
$page = max(1, intval($_GET['page'] ?? 1));
$limit = ITEMS_PER_PAGE;
$offset = ($page - 1) * $limit;

// Search and filter
$search = $_GET['search'] ?? '';
$difficulty_filter = $_GET['difficulty'] ?? '';
$status_filter = $_GET['status'] ?? '';

// Build query
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(keyword LIKE ? OR notes LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($difficulty_filter)) {
    $where_conditions[] = "difficulty = ?";
    $params[] = $difficulty_filter;
}

if (!empty($status_filter)) {
    $where_conditions[] = "status = ?";
    $params[] = $status_filter;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total count
$count_sql = "SELECT COUNT(*) FROM keywords $where_clause";
$stmt = $pdo->prepare($count_sql);
$stmt->execute($params);
$total_items = $stmt->fetchColumn();
$total_pages = ceil($total_items / $limit);

// Get keywords
$sql = "SELECT * FROM keywords $where_clause ORDER BY created_at DESC LIMIT " . intval($limit) . " OFFSET " . intval($offset);
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$keywords = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-2">Quản lý Từ khóa</h1>
                <p class="text-muted mb-0">Quản lý từ khóa SEO cho website</p>
            </div>
            <div>
                <button type="button" class="btn btn-admin-primary" data-bs-toggle="modal" data-bs-target="#keywordModal">
                    <i class="fas fa-plus me-2"></i>Thêm từ khóa mới
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter -->
<div class="row mb-4">
    <div class="col-12">
        <div class="admin-card">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Tìm kiếm</label>
                        <input type="text" class="form-control" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Tìm theo từ khóa hoặc ghi chú...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Độ khó</label>
                        <select class="form-select" name="difficulty">
                            <option value="">Tất cả</option>
                            <option value="easy" <?php echo $difficulty_filter === 'easy' ? 'selected' : ''; ?>>Dễ</option>
                            <option value="medium" <?php echo $difficulty_filter === 'medium' ? 'selected' : ''; ?>>Trung bình</option>
                            <option value="hard" <?php echo $difficulty_filter === 'hard' ? 'selected' : ''; ?>>Khó</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Trạng thái</label>
                        <select class="form-select" name="status">
                            <option value="">Tất cả</option>
                            <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Hoạt động</option>
                            <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>Không hoạt động</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search me-1"></i>Tìm kiếm
                        </button>
                        <a href="keywords.php" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Keywords Table -->
<div class="row">
    <div class="col-12">
        <div class="admin-card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    Danh sách từ khóa (<?php echo number_format($total_items); ?>)
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($keywords)): ?>
                    <div class="table-responsive">
                        <table class="table table-admin mb-0">
                            <thead>
                                <tr>
                                    <th width="60">ID</th>
                                    <th>Từ khóa</th>
                                    <th width="120">Lượng tìm kiếm</th>
                                    <th width="100">Độ khó</th>
                                    <th width="100">Trạng thái</th>
                                    <th width="200">Ghi chú</th>
                                    <th width="120">Ngày tạo</th>
                                    <th width="120">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($keywords as $keyword): ?>
                                    <tr>
                                        <td><?php echo $keyword['id']; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($keyword['keyword']); ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?php echo number_format($keyword['search_volume'] ?? 0); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            $difficulty_colors = [
                                                'easy' => 'success',
                                                'medium' => 'warning', 
                                                'hard' => 'danger'
                                            ];
                                            $difficulty_labels = [
                                                'easy' => 'Dễ',
                                                'medium' => 'Trung bình',
                                                'hard' => 'Khó'
                                            ];
                                            $difficulty = $keyword['difficulty'] ?? 'medium';
                                            ?>
                                            <span class="badge bg-<?php echo $difficulty_colors[$difficulty]; ?>">
                                                <?php echo $difficulty_labels[$difficulty]; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php $status = $keyword['status'] ?? 'active'; ?>
                                            <span class="status-badge status-<?php echo $status; ?>">
                                                <?php echo $status === 'active' ? 'Hoạt động' : 'Không hoạt động'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php $notes = $keyword['notes'] ?? ''; ?>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars(substr($notes, 0, 50)); ?>
                                                <?php if (strlen($notes) > 50): ?>...<?php endif; ?>
                                            </small>
                                        </td>
                                        <td>
                                            <small><?php echo date('d/m/Y', strtotime($keyword['created_at'])); ?></small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-action btn-action-edit" 
                                                        onclick="editKeyword(<?php echo htmlspecialchars(json_encode($keyword)); ?>)" title="Sửa">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                
                                                <form method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa từ khóa này?')">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo $keyword['id']; ?>">
                                                    <button type="submit" class="btn btn-action btn-action-delete" title="Xóa">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <div class="card-footer">
                            <nav aria-label="Phân trang">
                                <ul class="pagination pagination-sm mb-0 justify-content-center">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo ($page - 1); ?>&search=<?php echo urlencode($search); ?>&difficulty=<?php echo urlencode($difficulty_filter); ?>&status=<?php echo urlencode($status_filter); ?>">
                                                <i class="fas fa-chevron-left"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&difficulty=<?php echo urlencode($difficulty_filter); ?>&status=<?php echo urlencode($status_filter); ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo ($page + 1); ?>&search=<?php echo urlencode($search); ?>&difficulty=<?php echo urlencode($difficulty_filter); ?>&status=<?php echo urlencode($status_filter); ?>">
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        </div>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-key fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Không có từ khóa nào</h5>
                        <p class="text-muted mb-4">Hãy thêm từ khóa SEO đầu tiên của bạn</p>
                        <button type="button" class="btn btn-admin-primary" data-bs-toggle="modal" data-bs-target="#keywordModal">
                            <i class="fas fa-plus me-2"></i>Thêm từ khóa mới
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Keyword Modal -->
<div class="modal fade" id="keywordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="keywordModalTitle">Thêm từ khóa mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="keywordForm">
                <div class="modal-body">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="id" id="keywordId">
                    
                    <div class="mb-3">
                        <label for="keyword" class="form-label">Từ khóa <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="keyword" name="keyword" required placeholder="Nhập từ khóa...">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="search_volume" class="form-label">Lượng tìm kiếm</label>
                            <input type="number" class="form-control" id="search_volume" name="search_volume" value="0" min="0" placeholder="0">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="difficulty" class="form-label">Độ khó</label>
                            <select class="form-select" id="difficulty" name="difficulty">
                                <option value="easy">Dễ</option>
                                <option value="medium" selected>Trung bình</option>
                                <option value="hard">Khó</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active" selected>Hoạt động</option>
                            <option value="inactive">Không hoạt động</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Ghi chú</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Ghi chú về từ khóa..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-admin-primary">
                        <i class="fas fa-save me-2"></i>Lưu từ khóa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Reset form when modal is closed
document.getElementById('keywordModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('keywordForm').reset();
    document.getElementById('formAction').value = 'add';
    document.getElementById('keywordId').value = '';
    document.getElementById('keywordModalTitle').textContent = 'Thêm từ khóa mới';
});

// Edit keyword function
function editKeyword(keyword) {
    document.getElementById('formAction').value = 'edit';
    document.getElementById('keywordId').value = keyword.id || '';
    document.getElementById('keyword').value = keyword.keyword || '';
    document.getElementById('search_volume').value = keyword.search_volume || 0;
    document.getElementById('difficulty').value = keyword.difficulty || 'medium';
    document.getElementById('status').value = keyword.status || 'active';
    document.getElementById('notes').value = keyword.notes || '';
    document.getElementById('keywordModalTitle').textContent = 'Chỉnh sửa từ khóa';
    
    new bootstrap.Modal(document.getElementById('keywordModal')).show();
}
</script>

<?php include 'includes/footer.php'; ?> 