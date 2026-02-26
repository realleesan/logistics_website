<?php
require_once 'config.php';
requireAdminLogin();

$page_title = 'Dashboard';

// Lấy thống kê từ database
$stats = getDashboardStats();

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-2">Dashboard</h1>
                <p class="text-muted mb-0">Tổng quan hệ thống quản trị nội dung</p>
            </div>
            <div class="text-muted">
                <i class="fas fa-calendar me-2"></i>
                <?php echo date('d/m/Y H:i'); ?>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <!-- Total News -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="admin-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-grow-1">
                    <div class="row">
                        <div class="col">
                            <h5 class="text-uppercase text-muted mb-2" style="font-size: 0.8rem;">Tin tức</h5>
                            <span class="h2 font-weight-bold mb-0"><?php echo number_format($stats['total_news']); ?></span>
                        </div>
                        <div class="col-auto">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 60px; height: 60px; background: linear-gradient(135deg, #8bc34a, #689f38);">
                                <i class="fas fa-newspaper fa-2x" style="color: #2c3e50;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light">
                <a href="news.php" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-eye me-1"></i>Xem chi tiết
                </a>
            </div>
        </div>
    </div>

    <!-- Total Services -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="admin-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-grow-1">
                    <div class="row">
                        <div class="col">
                            <h5 class="text-uppercase text-muted mb-2" style="font-size: 0.8rem;">Dịch vụ</h5>
                            <span class="h2 font-weight-bold mb-0"><?php echo number_format($stats['total_services']); ?></span>
                        </div>
                        <div class="col-auto">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 60px; height: 60px; background: linear-gradient(135deg, #17a2b8, #138496);">
                                <i class="fas fa-cogs fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light">
                <a href="services.php" class="btn btn-outline-info btn-sm">
                    <i class="fas fa-eye me-1"></i>Xem chi tiết
                </a>
            </div>
        </div>
    </div>

    <!-- Total Contacts -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="admin-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-grow-1">
                    <div class="row">
                        <div class="col">
                            <h5 class="text-uppercase text-muted mb-2" style="font-size: 0.8rem;">Liên hệ</h5>
                            <span class="h2 font-weight-bold mb-0"><?php echo number_format($stats['total_contacts']); ?></span>
                        </div>
                        <div class="col-auto">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 60px; height: 60px; background: linear-gradient(135deg, #ffc107, #e0a800);">
                                <i class="fas fa-envelope fa-2x" style="color: #2c3e50;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light">
                <a href="contacts.php" class="btn btn-outline-warning btn-sm">
                    <i class="fas fa-eye me-1"></i>Xem chi tiết
                </a>
            </div>
        </div>
    </div>

    <!-- New Contacts Today -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="admin-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-grow-1">
                    <div class="row">
                        <div class="col">
                            <h5 class="text-uppercase text-muted mb-2" style="font-size: 0.8rem;">Liên hệ hôm nay</h5>
                            <span class="h2 font-weight-bold mb-0"><?php echo number_format($stats['new_contacts']); ?></span>
                        </div>
                        <div class="col-auto">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 60px; height: 60px; background: linear-gradient(135deg, #28a745, #20c997);">
                                <i class="fas fa-bell fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light">
                <small class="text-muted">
                    <i class="fas fa-calendar me-1"></i>
                    Cập nhật: <?php echo date('H:i'); ?>
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="admin-card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    Thao tác nhanh
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="news-add.php" class="btn btn-admin-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4">
                            <i class="fas fa-plus-circle fa-2x mb-2"></i>
                            <span>Thêm tin tức mới</span>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="services-add.php" class="btn btn-admin-secondary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4">
                            <i class="fas fa-cog fa-2x mb-2"></i>
                            <span>Thêm dịch vụ mới</span>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="categories-add.php" class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4">
                            <i class="fas fa-tags fa-2x mb-2"></i>
                            <span>Thêm danh mục</span>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="../" target="_blank" class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4">
                            <i class="fas fa-external-link-alt fa-2x mb-2"></i>
                            <span>Xem website</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Content -->
<div class="row">
    <!-- Recent News -->
    <div class="col-xl-6 mb-4">
        <div class="admin-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-newspaper me-2"></i>
                    Tin tức gần đây
                </h5>
                <a href="news.php" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
            </div>
            <div class="card-body">
                <?php if (!empty($stats['recent_news'])): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($stats['recent_news'] as $news): ?>
                            <div class="list-group-item border-0 px-0">
                                <div class="d-flex align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($news['title']); ?></h6>
                                        <p class="mb-1 text-muted small">
                                            <?php echo htmlspecialchars(substr(strip_tags($news['excerpt']), 0, 100)); ?>...
                                        </p>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            <?php echo date('d/m/Y H:i', strtotime($news['created_at'])); ?>
                                        </small>
                                    </div>
                                    <span class="status-badge status-<?php echo $news['status']; ?>">
                                        <?php echo $news['status'] === 'published' ? 'Đã xuất bản' : 'Nháp'; ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Chưa có tin tức nào</p>
                        <a href="news-add.php" class="btn btn-admin-primary">Thêm tin tức đầu tiên</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Contacts -->
    <div class="col-xl-6 mb-4">
        <div class="admin-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-envelope me-2"></i>
                    Liên hệ gần đây
                </h5>
                <a href="contacts.php" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
            </div>
            <div class="card-body">
                <?php if (!empty($stats['recent_contacts'])): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($stats['recent_contacts'] as $contact): ?>
                            <div class="list-group-item border-0 px-0">
                                <div class="d-flex align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($contact['name']); ?></h6>
                                        <p class="mb-1 text-muted small">
                                            <?php echo htmlspecialchars($contact['email']); ?>
                                        </p>
                                        <p class="mb-1">
                                            <?php echo htmlspecialchars(substr($contact['message'], 0, 80)); ?>...
                                        </p>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            <?php echo date('d/m/Y H:i', strtotime($contact['created_at'])); ?>
                                        </small>
                                    </div>
                                    <span class="status-badge status-<?php echo $contact['status'] === 'new' ? 'active' : 'inactive'; ?>">
                                        <?php 
                                        switch($contact['status']) {
                                            case 'new': echo 'Mới'; break;
                                            case 'read': echo 'Đã đọc'; break;
                                            case 'replied': echo 'Đã trả lời'; break;
                                        }
                                        ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-envelope fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Chưa có liên hệ nào</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- System Info -->
<div class="row">
    <div class="col-12">
        <div class="admin-card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Thông tin hệ thống
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Phiên bản PHP:</strong><br>
                        <span class="text-muted"><?php echo PHP_VERSION; ?></span>
                    </div>
                    <div class="col-md-3">
                        <strong>Database:</strong><br>
                        <span class="text-muted">MySQL <?php echo $pdo->getAttribute(PDO::ATTR_SERVER_VERSION); ?></span>
                    </div>
                    <div class="col-md-3">
                        <strong>Đăng nhập lúc:</strong><br>
                        <span class="text-muted"><?php echo date('d/m/Y H:i:s', $_SESSION['admin_login_time']); ?></span>
                    </div>
                    <div class="col-md-3">
                        <strong>Bảo mật:</strong><br>
                        <span class="text-success">
                            <i class="fas fa-shield-alt me-1"></i>
                            SSL Enabled
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 