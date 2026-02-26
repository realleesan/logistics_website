<?php
require_once 'config.php';
requireAdminLogin();

$page_title = 'Quản lý danh mục tin tức';

// Xử lý actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        
        try {
            // Kiểm tra xem có tin tức nào đang sử dụng danh mục này không
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM news WHERE category_id = ?");
            $stmt->execute([$id]);
            $newsCount = $stmt->fetchColumn();
            
            if ($newsCount > 0) {
                setMessage("Không thể xóa danh mục này vì có {$newsCount} tin tức đang sử dụng!", 'error');
            } else {
                $stmt = $pdo->prepare("DELETE FROM news_categories WHERE id = ?");
                $stmt->execute([$id]);
                setMessage('Xóa danh mục thành công!');
            }
        } catch (PDOException $e) {
            setMessage('Lỗi: ' . $e->getMessage(), 'error');
        }
    }
}

// Phân trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * ITEMS_PER_PAGE;

// Search
$search = $_GET['search'] ?? '';
$whereClause = '';
$params = [];

if ($search) {
    $whereClause = "WHERE name LIKE ? OR description LIKE ?";
    $params = ["%$search%", "%$search%"];
}

// Lấy tổng số bản ghi
$countSql = "SELECT COUNT(*) FROM news_categories $whereClause";
$stmt = $pdo->prepare($countSql);
$stmt->execute($params);
$totalItems = $stmt->fetchColumn();
$totalPages = ceil($totalItems / ITEMS_PER_PAGE);

// Lấy danh sách danh mục
$sql = "SELECT nc.*, 
               (SELECT COUNT(*) FROM news WHERE category_id = nc.id) as news_count
        FROM news_categories nc 
        $whereClause 
        ORDER BY nc.sort_order ASC, nc.name ASC 
        LIMIT " . ITEMS_PER_PAGE . " OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-2">Quản lý danh mục tin tức</h1>
                <p class="text-muted mb-0">Quản lý các danh mục tin tức và bài viết</p>
            </div>
            <a href="categories-add.php" class="btn btn-admin-primary">
                <i class="fas fa-plus me-2"></i>Thêm danh mục mới
            </a>
        </div>
    </div>
</div>

<!-- Search and Filters -->
<div class="row mb-4">
    <div class="col-12">
        <div class="admin-card">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="search" 
                               placeholder="Tìm kiếm theo tên hoặc mô tả danh mục..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-4">
                        <div class="btn-group w-100">
                            <button type="submit" class="btn btn-admin-primary">
                                <i class="fas fa-search me-2"></i>Tìm kiếm
                            </button>
                            <a href="categories.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Categories List -->
<div class="row">
    <div class="col-12">
        <div class="admin-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Danh sách danh mục (<?php echo number_format($totalItems); ?>)</h5>
                <small class="text-muted">Trang <?php echo $page; ?> / <?php echo $totalPages; ?></small>
            </div>
            <div class="card-body p-0">
                <?php if ($categories): ?>
                    <div class="table-responsive">
                        <table class="table table-admin mb-0" id="categoriesTable">
                            <thead>
                                <tr>
                                    <th width="50">ID</th>
                                    <th>Tên danh mục</th>
                                    <th>Slug</th>
                                    <th>Mô tả</th>
                                    <th width="100">Thứ tự</th>
                                    <th width="100">Tin tức</th>
                                    <th width="100">Trạng thái</th>
                                    <th width="120">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $category): ?>
                                    <tr>
                                        <td><strong>#<?php echo $category['id']; ?></strong></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($category['name']); ?></strong>
                                        </td>
                                        <td>
                                            <code><?php echo htmlspecialchars($category['slug']); ?></code>
                                        </td>
                                        <td>
                                            <?php 
                                            $description = htmlspecialchars($category['description']);
                                            echo $description ? (strlen($description) > 80 ? substr($description, 0, 80) . '...' : $description) : '<em class="text-muted">Không có mô tả</em>';
                                            ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary"><?php echo $category['sort_order']; ?></span>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($category['news_count'] > 0): ?>
                                                <a href="news.php?category=<?php echo $category['id']; ?>" class="badge bg-info text-decoration-none">
                                                    <?php echo $category['news_count']; ?> tin
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">0 tin</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo $category['status']; ?>">
                                                <?php echo $category['status'] === 'active' ? 'Hoạt động' : 'Ngưng'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="categories-edit.php?id=<?php echo $category['id']; ?>" 
                                                   class="btn btn-action btn-action-edit" title="Sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <?php if ($category['news_count'] == 0): ?>
                                                    <form method="POST" class="d-inline" onsubmit="return confirmDelete('Bạn có chắc muốn xóa danh mục này?')">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                                                        <button type="submit" class="btn btn-action btn-action-delete" title="Xóa">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <button type="button" class="btn btn-action btn-secondary" 
                                                            title="Không thể xóa - có tin tức đang sử dụng" disabled>
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Không tìm thấy danh mục nào</h5>
                        <?php if ($search): ?>
                            <p class="text-muted">Thử thay đổi từ khóa tìm kiếm hoặc <a href="categories.php">xem tất cả danh mục</a></p>
                        <?php else: ?>
                            <p class="text-muted">Hãy tạo danh mục đầu tiên để bắt đầu</p>
                            <a href="categories-add.php" class="btn btn-admin-primary">
                                <i class="fas fa-plus me-2"></i>Thêm danh mục đầu tiên
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
    <div class="row mt-4">
        <div class="col-12">
            <nav aria-label="Phân trang danh mục">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo ($page - 1); ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php
                    $start = max(1, $page - 2);
                    $end = min($totalPages, $page + 2);
                    
                    if ($start > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=1<?php echo $search ? '&search=' . urlencode($search) : ''; ?>">1</a>
                        </li>
                        <?php if ($start > 2): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $start; $i <= $end; $i++): ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($end < $totalPages): ?>
                        <?php if ($end < $totalPages - 1): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $totalPages; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                                <?php echo $totalPages; ?>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo ($page + 1); ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?> 