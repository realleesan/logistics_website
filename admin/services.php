<?php
require_once 'config.php';
requireAdminLogin();

$page_title = 'Quản lý Dịch vụ';

// Handle actions
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_sort' && !empty($_POST['id'])) {
        $serviceId = (int)$_POST['id'];
        $newOrder = max(0, (int)($_POST['sort_order'] ?? 0));
        try {
            $pdo->beginTransaction();
            if ($newOrder <= 0) {
                $maxStmt = $pdo->query("SELECT COALESCE(MAX(sort_order), 0) FROM services");
                $newOrder = (int)$maxStmt->fetchColumn() + 1;
            } else {
                $shiftStmt = $pdo->prepare("UPDATE services SET sort_order = sort_order + 1 WHERE sort_order >= ? AND id <> ?");
                $shiftStmt->execute([$newOrder, $serviceId]);
            }
            $updateStmt = $pdo->prepare("UPDATE services SET sort_order = ? WHERE id = ?");
            $updateStmt->execute([$newOrder, $serviceId]);
            $pdo->commit();
            setMessage('Cập nhật thứ tự hiển thị thành công!', 'success');
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            setMessage('Lỗi khi cập nhật thứ tự: ' . $e->getMessage(), 'error');
        }
        header('Location: services.php?page=' . intval($_GET['page'] ?? 1) . '&search=' . urlencode($_GET['search'] ?? '') . '&status=' . urlencode($_GET['status'] ?? ''));
        exit();
    }

    if ($action === 'delete' && !empty($_POST['id'])) {
        try {
            // Delete service
            $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            
            setMessage('Xóa dịch vụ thành công!', 'success');
        } catch (PDOException $e) {
            setMessage('Lỗi khi xóa dịch vụ: ' . $e->getMessage(), 'error');
        }
        
        header('Location: services.php');
        exit();
    }
    
    if ($action === 'toggle_status' && !empty($_POST['id'])) {
        try {
            // Toggle status
            $stmt = $pdo->prepare("UPDATE services SET status = CASE WHEN status = 'active' THEN 'inactive' ELSE 'active' END WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            
            setMessage('Cập nhật trạng thái thành công!', 'success');
        } catch (PDOException $e) {
            setMessage('Lỗi khi cập nhật: ' . $e->getMessage(), 'error');
        }
        
        header('Location: services.php');
        exit();
    }
}

// Pagination
$page = max(1, intval($_GET['page'] ?? 1));
$limit = ITEMS_PER_PAGE;
$offset = ($page - 1) * $limit;

// Search
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';

// Build query
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(title LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($status_filter)) {
    $where_conditions[] = "status = ?";
    $params[] = $status_filter;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total count
$count_sql = "SELECT COUNT(*) FROM services $where_clause";
$stmt = $pdo->prepare($count_sql);
$stmt->execute($params);
$total_items = $stmt->fetchColumn();
$total_pages = ceil($total_items / $limit);

// Get services
$sql = "SELECT * FROM services $where_clause ORDER BY sort_order ASC, created_at DESC LIMIT " . intval($limit) . " OFFSET " . intval($offset);
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-2">Quản lý Dịch vụ</h1>
                <p class="text-muted mb-0">Quản lý các dịch vụ logistics của công ty</p>
            </div>
            <div>
                <a href="services-add.php" class="btn btn-admin-primary">
                    <i class="fas fa-plus me-2"></i>Thêm dịch vụ mới
                </a>
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
                        <input type="text" class="form-control" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Tìm theo tên hoặc mô tả...">
                    </div>
                    <div class="col-md-3">
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
                        <a href="services.php" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Reset
                        </a>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <small class="text-muted">
                            Tổng: <?php echo number_format($total_items); ?> dịch vụ
                        </small>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Services Table -->
<div class="row">
    <div class="col-12">
        <div class="admin-card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    Danh sách dịch vụ
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($services)): ?>
                    <div class="table-responsive">
                        <table class="table table-admin mb-0">
                            <thead>
                                 <tr>
                                    <th width="60">ID</th>
                                    <th width="80">Hình ảnh</th>
                                    <th>Tên dịch vụ</th>
                                    <th width="150">Mô tả ngắn</th>
                                     <th width="90">Thứ tự</th>
                                    <th width="100">Trạng thái</th>
                                    <th width="120">Ngày tạo</th>
                                    <th width="120">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($services as $service): ?>
                                    <tr>
                                        <td><?php echo $service['id']; ?></td>
                                        <td>
                                            <?php if (!empty($service['image'])): ?>
                                                <img src="../<?php echo htmlspecialchars($service['image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($service['title']); ?>" 
                                                     class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($service['title']); ?></strong>
                                        </td>
                                         <td>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars(substr(strip_tags($service['description']), 0, 100)); ?>...
                                            </small>
                                        </td>
                                         <td>
                                             <form method="POST" action="services.php?page=<?php echo $page; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>" class="d-flex align-items-center gap-2">
                                                 <input type="hidden" name="action" value="update_sort">
                                                 <input type="hidden" name="id" value="<?php echo $service['id']; ?>">
                                                 <input type="number" name="sort_order" value="<?php echo (int)$service['sort_order']; ?>" class="form-control form-control-sm" style="width:80px" min="0">
                                                 <button class="btn btn-outline-secondary btn-sm" title="Lưu">
                                                     <i class="fas fa-save"></i>
                                                 </button>
                                             </form>
                                         </td>
                                        <td>
                                            <span class="status-badge status-<?php echo $service['status']; ?>">
                                                <?php echo $service['status'] === 'active' ? 'Hoạt động' : 'Không hoạt động'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small><?php echo date('d/m/Y', strtotime($service['created_at'])); ?></small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="services-edit.php?id=<?php echo $service['id']; ?>" 
                                                   class="btn btn-action btn-action-edit" title="Sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <form method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn thay đổi trạng thái?')">
                                                    <input type="hidden" name="action" value="toggle_status">
                                                    <input type="hidden" name="id" value="<?php echo $service['id']; ?>">
                                                    <button type="submit" class="btn btn-action btn-action-view" title="Đổi trạng thái">
                                                        <i class="fas fa-toggle-<?php echo $service['status'] === 'active' ? 'on' : 'off'; ?>"></i>
                                                    </button>
                                                </form>
                                                
                                                <form method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa dịch vụ này?')">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo $service['id']; ?>">
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
                                            <a class="page-link" href="?page=<?php echo ($page - 1); ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>">
                                                <i class="fas fa-chevron-left"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo ($page + 1); ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>">
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
                        <i class="fas fa-cogs fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Không có dịch vụ nào</h5>
                        <p class="text-muted mb-4">Hãy thêm dịch vụ đầu tiên của bạn</p>
                        <a href="services-add.php" class="btn btn-admin-primary">
                            <i class="fas fa-plus me-2"></i>Thêm dịch vụ mới
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 