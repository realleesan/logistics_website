<?php
// One-off script to update service sort_order safely
// Usage: place in admin/, access once while logged in as admin: /admin/update-service-order.php
// It will show a dry-run preview and a confirm link with a token.

require_once 'config.php';
requireAdminLogin();

$page_title = 'Cập nhật thứ tự dịch vụ (một lần)';

// Desired order mapping
$desiredOrder = [
    'van-chuyen-duong-bo'    => 1,
    'mua-hang-trung-quoc'    => 2,
    'van-chuyen-hang-khong'  => 3,
    'nhap-khau-uy-thac'      => 4,
    'van-chuyen-duong-bien'  => 5,
];

// CSRF-like token
$token = hash('sha256', 'update-service-order' . session_id());
$do = isset($_GET['do']) && hash_equals($token, ($_GET['token'] ?? ''));

if ($do) {
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("UPDATE services SET sort_order = :sort WHERE slug = :slug");
        $updated = 0;
        foreach ($desiredOrder as $slug => $sort) {
            $stmt->execute([':sort' => $sort, ':slug' => $slug]);
            $updated += $stmt->rowCount();
        }
        $pdo->commit();
        setMessage('Đã cập nhật thứ tự cho ' . $updated . ' dịch vụ.', 'success');
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        setMessage('Lỗi cập nhật: ' . $e->getMessage(), 'error');
    }
    header('Location: services.php');
    exit();
}

include 'includes/header.php';
?>

<div class="row">
  <div class="col-lg-8">
    <div class="admin-card">
      <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-sort me-2"></i>Cập nhật thứ tự dịch vụ</h5>
      </div>
      <div class="card-body">
        <p>Script này sẽ thiết lập thứ tự hiển thị theo cấu hình sau:</p>
        <ul>
          <li>1: Vận chuyển đường bộ</li>
          <li>2: Đặt hàng Trung Quốc và Thanh toán cho Nhà cung cấp</li>
          <li>3: Vận chuyển xách tay nhanh 2 chiều Trung Quốc - Việt Nam</li>
          <li>4: Nhập khẩu ủy thác</li>
          <li>5: Vận chuyển đường biển</li>
        </ul>

        <p>Bấm nút dưới để xác nhận cập nhật. Có thể chạy lại an toàn.</p>
        <a class="btn btn-admin-primary" href="update-service-order.php?do=1&token=<?php echo $token; ?>">
          <i class="fas fa-check"></i> Xác nhận cập nhật
        </a>
        <a href="services.php" class="btn btn-outline-secondary ms-2">
          Quay lại danh sách
        </a>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

