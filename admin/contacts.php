<?php
require_once 'config.php';
requireAdminLogin();

$page_title = 'Quản lý Liên hệ';

// Handle actions
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'delete' && !empty($_POST['id'])) {
        try {
            // Delete contact
            $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            
            setMessage('Xóa liên hệ thành công!', 'success');
        } catch (PDOException $e) {
            setMessage('Lỗi khi xóa liên hệ: ' . $e->getMessage(), 'error');
        }
        
        header('Location: contacts.php');
        exit();
    }
    
    if ($action === 'update_status' && !empty($_POST['id']) && !empty($_POST['status'])) {
        try {
            // Update status
            $stmt = $pdo->prepare("UPDATE contacts SET status = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$_POST['status'], $_POST['id']]);
            
            setMessage('Cập nhật trạng thái thành công!', 'success');
        } catch (PDOException $e) {
            setMessage('Lỗi khi cập nhật: ' . $e->getMessage(), 'error');
        }
        
        header('Location: contacts.php');
        exit();
    }
    
    if ($action === 'bulk_action' && !empty($_POST['contact_ids']) && !empty($_POST['bulk_action'])) {
        try {
            $contact_ids = $_POST['contact_ids'];
            $bulk_action = $_POST['bulk_action'];
            
            if ($bulk_action === 'delete') {
                $placeholders = str_repeat('?,', count($contact_ids) - 1) . '?';
                $stmt = $pdo->prepare("DELETE FROM contacts WHERE id IN ($placeholders)");
                $stmt->execute($contact_ids);
                setMessage('Xóa ' . count($contact_ids) . ' liên hệ thành công!', 'success');
            } else {
                // Update status for multiple contacts
                $placeholders = str_repeat('?,', count($contact_ids) - 1) . '?';
                $stmt = $pdo->prepare("UPDATE contacts SET status = ?, updated_at = NOW() WHERE id IN ($placeholders)");
                $params = array_merge([$bulk_action], $contact_ids);
                $stmt->execute($params);
                setMessage('Cập nhật trạng thái cho ' . count($contact_ids) . ' liên hệ thành công!', 'success');
            }
        } catch (PDOException $e) {
            setMessage('Lỗi khi thực hiện: ' . $e->getMessage(), 'error');
        }
        
        header('Location: contacts.php');
        exit();
    }
}

// Pagination
$page = max(1, intval($_GET['page'] ?? 1));
$limit = ITEMS_PER_PAGE;
$offset = ($page - 1) * $limit;

// Search and filter
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

// Build query
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(name LIKE ? OR email LIKE ? OR phone LIKE ? OR subject LIKE ? OR message LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($status_filter)) {
    $where_conditions[] = "status = ?";
    $params[] = $status_filter;
}

if (!empty($date_from)) {
    $where_conditions[] = "DATE(created_at) >= ?";
    $params[] = $date_from;
}

if (!empty($date_to)) {
    $where_conditions[] = "DATE(created_at) <= ?";
    $params[] = $date_to;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total count
$count_sql = "SELECT COUNT(*) FROM contacts $where_clause";
$stmt = $pdo->prepare($count_sql);
$stmt->execute($params);
$total_items = $stmt->fetchColumn();
$total_pages = ceil($total_items / $limit);

// Get contacts
$sql = "SELECT * FROM contacts $where_clause ORDER BY created_at DESC LIMIT " . intval($limit) . " OFFSET " . intval($offset);
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-2">Quản lý Liên hệ</h1>
                <p class="text-muted mb-0">Quản lý các tin nhắn liên hệ từ khách hàng</p>
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
                    <div class="col-md-3">
                        <label class="form-label">Tìm kiếm</label>
                        <input type="text" class="form-control" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Tìm theo tên, email, điện thoại...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Trạng thái</label>
                        <select class="form-select" name="status">
                            <option value="">Tất cả</option>
                            <option value="new" <?php echo $status_filter === 'new' ? 'selected' : ''; ?>>Mới</option>
                            <option value="read" <?php echo $status_filter === 'read' ? 'selected' : ''; ?>>Đã đọc</option>
                            <option value="replied" <?php echo $status_filter === 'replied' ? 'selected' : ''; ?>>Đã trả lời</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Từ ngày</label>
                        <input type="date" class="form-control" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Đến ngày</label>
                        <input type="date" class="form-control" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>">
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search me-1"></i>Tìm
                        </button>
                        <a href="contacts.php" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Reset
                        </a>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <small class="text-muted">
                            Tổng: <?php echo number_format($total_items); ?>
                        </small>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Actions -->
<?php if (!empty($contacts)): ?>
<div class="row mb-3">
    <div class="col-12">
        <form method="POST" id="bulkForm">
            <input type="hidden" name="action" value="bulk_action">
            <div class="d-flex align-items-center gap-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="selectAll">
                    <label class="form-check-label" for="selectAll">Chọn tất cả</label>
                </div>
                <select class="form-select" name="bulk_action" style="width: auto;">
                    <option value="">Chọn thao tác...</option>
                    <option value="new">Đánh dấu mới</option>
                    <option value="read">Đánh dấu đã đọc</option>
                    <option value="replied">Đánh dấu đã trả lời</option>
                    <option value="delete">Xóa</option>
                </select>
                <button type="submit" class="btn btn-outline-primary btn-sm" onclick="return confirmBulkAction()">
                    <i class="fas fa-check me-1"></i>Thực hiện
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Contacts Table -->
<div class="row">
    <div class="col-12">
        <div class="admin-card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    Danh sách liên hệ
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($contacts)): ?>
                    <div class="table-responsive">
                        <table class="table table-admin mb-0">
                            <thead>
                                <tr>
                                    <th width="40">
                                        <input type="checkbox" id="selectAllTable">
                                    </th>
                                    <th width="60">ID</th>
                                    <th width="150">Thông tin</th>
                                    <th width="120">Liên hệ</th>
                                    <th width="150">Chủ đề</th>
                                    <th>Nội dung</th>
                                    <th width="100">Trạng thái</th>
                                    <th width="120">Ngày gửi</th>
                                    <th width="120">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($contacts as $contact): ?>
                                    <tr class="<?php echo $contact['status'] === 'new' ? 'table-warning' : ''; ?>">
                                        <td>
                                            <input type="checkbox" name="contact_ids[]" value="<?php echo $contact['id']; ?>" class="contact-checkbox">
                                        </td>
                                        <td><?php echo $contact['id']; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($contact['name']); ?></strong>
                                            <?php if (!empty($contact['company'])): ?>
                                                <br><small class="text-muted"><?php echo htmlspecialchars($contact['company']); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small>
                                                <i class="fas fa-envelope me-1"></i><?php echo htmlspecialchars($contact['email']); ?><br>
                                                <?php if (!empty($contact['phone'])): ?>
                                                    <i class="fas fa-phone me-1"></i><?php echo htmlspecialchars($contact['phone']); ?>
                                                <?php endif; ?>
                                            </small>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($contact['subject']); ?></strong>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars(substr($contact['message'], 0, 100)); ?>
                                                <?php if (strlen($contact['message']) > 100): ?>...<?php endif; ?>
                                            </small>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo $contact['status'] === 'new' ? 'active' : ($contact['status'] === 'replied' ? 'published' : 'inactive'); ?>">
                                                <?php 
                                                switch($contact['status']) {
                                                    case 'new': echo 'Mới'; break;
                                                    case 'read': echo 'Đã đọc'; break;
                                                    case 'replied': echo 'Đã trả lời'; break;
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small><?php echo date('d/m/Y H:i', strtotime($contact['created_at'])); ?></small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-action btn-action-view" 
                                                        onclick="viewContact(<?php echo $contact['id']; ?>)" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                
                                                <div class="dropdown">
                                                    <button class="btn btn-action btn-action-edit dropdown-toggle" 
                                                            type="button" data-bs-toggle="dropdown" title="Trạng thái">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <?php foreach (['new' => 'Mới', 'read' => 'Đã đọc', 'replied' => 'Đã trả lời'] as $status => $label): ?>
                                                            <?php if ($status !== $contact['status']): ?>
                                                                <li>
                                                                    <form method="POST" class="d-inline">
                                                                        <input type="hidden" name="action" value="update_status">
                                                                        <input type="hidden" name="id" value="<?php echo $contact['id']; ?>">
                                                                        <input type="hidden" name="status" value="<?php echo $status; ?>">
                                                                        <button type="submit" class="dropdown-item"><?php echo $label; ?></button>
                                                                    </form>
                                                                </li>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                                
                                                <form method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa liên hệ này?')">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo $contact['id']; ?>">
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
                                            <a class="page-link" href="?page=<?php echo ($page - 1); ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&date_from=<?php echo urlencode($date_from); ?>&date_to=<?php echo urlencode($date_to); ?>">
                                                <i class="fas fa-chevron-left"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&date_from=<?php echo urlencode($date_from); ?>&date_to=<?php echo urlencode($date_to); ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo ($page + 1); ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&date_from=<?php echo urlencode($date_from); ?>&date_to=<?php echo urlencode($date_to); ?>">
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
                        <i class="fas fa-envelope fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Không có liên hệ nào</h5>
                        <p class="text-muted mb-4">Chưa có khách hàng nào gửi liên hệ</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Contact Detail Modal -->
<div class="modal fade" id="contactModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết liên hệ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contactDetails">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Select all functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.contact-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

document.getElementById('selectAllTable').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.contact-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Bulk action confirmation
function confirmBulkAction() {
    const selected = document.querySelectorAll('.contact-checkbox:checked');
    const action = document.querySelector('select[name="bulk_action"]').value;
    
    if (selected.length === 0) {
        alert('Vui lòng chọn ít nhất một liên hệ');
        return false;
    }
    
    if (!action) {
        alert('Vui lòng chọn thao tác');
        return false;
    }
    
    const actionText = action === 'delete' ? 'xóa' : 'cập nhật trạng thái cho';
    return confirm(`Bạn có chắc muốn ${actionText} ${selected.length} liên hệ đã chọn?`);
}

// View contact details
function viewContact(id) {
    fetch('contact-detail.php?id=' + id)
        .then(response => response.text())
        .then(data => {
            document.getElementById('contactDetails').innerHTML = data;
            new bootstrap.Modal(document.getElementById('contactModal')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Lỗi khi tải chi tiết liên hệ');
        });
}
</script>

<?php include 'includes/footer.php'; ?> 