<?php
require_once 'config.php';
requireAdminLogin();

$page_title = 'Quản lý tin tức';

// Xử lý actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        
        try {
            // Lấy thông tin ảnh để xóa file
            $stmt = $pdo->prepare("SELECT image FROM news WHERE id = ?");
            $stmt->execute([$id]);
            $image = $stmt->fetchColumn();
            
            // Xóa tin tức
            $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
            $stmt->execute([$id]);
            
            // Xóa file ảnh nếu có
            if ($image && file_exists('../' . $image)) {
                unlink('../' . $image);
            }
            
            setMessage('Xóa tin tức thành công!');
        } catch (PDOException $e) {
            setMessage('Lỗi: ' . $e->getMessage(), 'error');
        }
    }
    
    if ($action === 'toggle_status') {
        $id = (int)$_POST['id'];
        $newStatus = $_POST['status'] === 'published' ? 'draft' : 'published';
        
        try {
            $stmt = $pdo->prepare("UPDATE news SET status = ? WHERE id = ?");
            $stmt->execute([$newStatus, $id]);
            
            setMessage('Cập nhật trạng thái thành công!');
        } catch (PDOException $e) {
            setMessage('Lỗi: ' . $e->getMessage(), 'error');
        }
    }
}

// Phân trang và tìm kiếm
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * ITEMS_PER_PAGE;

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$status = $_GET['status'] ?? '';

$whereClause = '';
$params = [];

$conditions = [];
if ($search) {
    $conditions[] = "(title LIKE ? OR content LIKE ? OR excerpt LIKE ?)";
    $params = array_merge($params, ["%$search%", "%$search%", "%$search%"]);
}
if ($category) {
    $conditions[] = "category_id = ?";
    $params[] = $category;
}
if ($status) {
    $conditions[] = "status = ?";
    $params[] = $status;
}

if ($conditions) {
    $whereClause = "WHERE " . implode(" AND ", $conditions);
}

// Lấy tổng số bản ghi
$countSql = "SELECT COUNT(*) FROM news $whereClause";
$stmt = $pdo->prepare($countSql);
$stmt->execute($params);
$totalItems = $stmt->fetchColumn();
$totalPages = ceil($totalItems / ITEMS_PER_PAGE);

// Lấy danh sách tin tức
$sql = "SELECT n.*, nc.name as category_name 
        FROM news n 
        LEFT JOIN news_categories nc ON n.category_id = nc.id 
        $whereClause 
        ORDER BY n.created_at DESC 
        LIMIT " . ITEMS_PER_PAGE . " OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$newsList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy danh sách danh mục cho filter
$categoriesStmt = $pdo->query("SELECT * FROM news_categories WHERE status = 'active' ORDER BY name");
$categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-2">Quản lý tin tức</h1>
                <p class="text-muted mb-0">Quản lý tất cả tin tức và bài viết trên website</p>
            </div>
            <a href="news-add.php" class="btn btn-admin-primary">
                <i class="fas fa-plus me-2"></i>Thêm tin tức mới
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
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="search" 
                               placeholder="Tìm kiếm theo tiêu đề, nội dung..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-3">
                        <select name="category" class="form-control">
                            <option value="">Tất cả danh mục</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" 
                                        <?php echo $category == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-control">
                            <option value="">Tất cả trạng thái</option>
                            <option value="published" <?php echo $status === 'published' ? 'selected' : ''; ?>>Đã xuất bản</option>
                            <option value="draft" <?php echo $status === 'draft' ? 'selected' : ''; ?>>Nháp</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="btn-group w-100">
                            <button type="submit" class="btn btn-admin-primary">
                                <i class="fas fa-search me-2"></i>Tìm kiếm
                            </button>
                            <a href="news.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- News List -->
<div class="row">
    <div class="col-12">
        <div class="admin-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Danh sách tin tức (<?php echo number_format($totalItems); ?>)</h5>
                <small class="text-muted">Trang <?php echo $page; ?> / <?php echo $totalPages; ?></small>
            </div>
            <div class="card-body p-0">
                <?php if ($newsList): ?>
                    <div class="table-responsive">
                        <table class="table table-admin mb-0">
                            <thead>
                                <tr>
                                    <th width="50">ID</th>
                                    <th width="80">Ảnh</th>
                                    <th>Tiêu đề</th>
                                    <th width="120">Danh mục</th>
                                    <th width="80">Lượt xem</th>
                                    <th width="100">Trạng thái</th>
                                    <th width="120">Ngày tạo</th>
                                    <th width="150">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($newsList as $news): ?>
                                    <tr>
                                        <td><strong>#<?php echo $news['id']; ?></strong></td>
                                        <td>
                                            <?php if ($news['image'] && file_exists('../' . $news['image'])): ?>
                                                <img src="../<?php echo htmlspecialchars($news['image']); ?>" 
                                                     alt="News image" class="img-thumbnail" 
                                                     style="width: 60px; height: 40px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                     style="width: 60px; height: 40px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($news['title']); ?></strong>
                                            <?php if ($news['featured']): ?>
                                                <span class="badge bg-warning text-dark ms-2">Nổi bật</span>
                                            <?php endif; ?>
                                            <br>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars(substr(strip_tags($news['excerpt']), 0, 80)); ?>...
                                            </small>
                                        </td>
                                        <td>
                                            <?php if ($news['category_name']): ?>
                                                <span class="badge bg-info"><?php echo htmlspecialchars($news['category_name']); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">Không có</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary"><?php echo number_format($news['views']); ?></span>
                                        </td>
                                        <td>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="toggle_status">
                                                <input type="hidden" name="id" value="<?php echo $news['id']; ?>">
                                                <input type="hidden" name="status" value="<?php echo $news['status']; ?>">
                                                <button type="submit" class="btn btn-sm status-badge status-<?php echo $news['status']; ?> border-0" 
                                                        title="Click để thay đổi trạng thái">
                                                    <?php echo $news['status'] === 'published' ? 'Đã xuất bản' : 'Nháp'; ?>
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo date('d/m/Y', strtotime($news['created_at'])); ?><br>
                                                <?php echo date('H:i', strtotime($news['created_at'])); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="../news-detail.php?slug=<?php echo htmlspecialchars($news['slug']); ?>" 
                                                   target="_blank" class="btn btn-action btn-action-view" title="Xem">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="news-edit.php?id=<?php echo $news['id']; ?>" 
                                                   class="btn btn-action btn-action-edit" title="Sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" class="d-inline" onsubmit="return confirmDelete('Bạn có chắc muốn xóa tin tức này?')">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo $news['id']; ?>">
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
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Không tìm thấy tin tức nào</h5>
                        <?php if ($search || $category || $status): ?>
                            <p class="text-muted">Thử thay đổi bộ lọc hoặc <a href="news.php">xem tất cả tin tức</a></p>
                        <?php else: ?>
                            <p class="text-muted">Hãy tạo tin tức đầu tiên để bắt đầu</p>
                            <a href="news-add.php" class="btn btn-admin-primary">
                                <i class="fas fa-plus me-2"></i>Thêm tin tức đầu tiên
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
            <nav aria-label="Phân trang tin tức">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo ($page - 1); ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $category ? '&category=' . $category : ''; ?><?php echo $status ? '&status=' . $status : ''; ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php
                    $start = max(1, $page - 2);
                    $end = min($totalPages, $page + 2);
                    
                    if ($start > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=1<?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $category ? '&category=' . $category : ''; ?><?php echo $status ? '&status=' . $status : ''; ?>">1</a>
                        </li>
                        <?php if ($start > 2): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $start; $i <= $end; $i++): ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $category ? '&category=' . $category : ''; ?><?php echo $status ? '&status=' . $status : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($end < $totalPages): ?>
                        <?php if ($end < $totalPages - 1): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $totalPages; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $category ? '&category=' . $category : ''; ?><?php echo $status ? '&status=' . $status : ''; ?>">
                                <?php echo $totalPages; ?>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo ($page + 1); ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $category ? '&category=' . $category : ''; ?><?php echo $status ? '&status=' . $status : ''; ?>">
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