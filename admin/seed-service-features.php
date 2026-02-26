<?php
require_once 'config.php';
requireAdminLogin();

// Page config
$page_title = 'Nhập dữ liệu động cho Dịch vụ (short_description + icon)';

// Controls
$applyChanges = isset($_GET['apply']) && $_GET['apply'] == '1';
$forceOverwrite = isset($_GET['force']) && $_GET['force'] == '1';

function mapFeaturesAndIconByTitle(string $title): array {
    $titleLower = mb_strtolower($title, 'UTF-8');
    $features = [];
    $icon = '';

    if (strpos($titleLower, 'xách tay') !== false) {
        $features = [
            'Thời gian nhanh nhất: 24 - 36h sau khi ký nhận',
            'Phù hợp hàng nhỏ, giá trị cao',
            'Quy trình xử lý nhanh gọn, chính xác và cấp thiết',
            'Chính sách bảo hiểm 100% giá trị hàng hoá'
        ];
        $icon = 'fas fa-plane';
    } elseif (strpos($titleLower, 'đường biển') !== false) {
        $features = [
            'Chi phí tối ưu nhất',
            'Phù hợp hàng khối lượng lớn',
            'Thời gian: 15-25 ngày',
            'Dịch vụ FCL và LCL'
        ];
        $icon = 'fas fa-ship';
    } elseif (strpos($titleLower, 'đường bộ') !== false) {
        $features = [
            'Thời gian vận chuyển: linh hoạt từ 3 - 5 ngày',
            'Phù hợp mọi loại hàng hóa',
            'Giá cạnh tranh',
            'Vận chuyển tận nơi, giao tận nhà'
        ];
        $icon = 'fas fa-truck';
    } elseif (strpos($titleLower, 'đường sắt') !== false) {
        $features = [
            'Cân bằng thời gian và chi phí',
            'Thân thiện môi trường',
            'An toàn cao',
            'Phù hợp hàng nặng'
        ];
        $icon = 'fas fa-train';
    } elseif (strpos($titleLower, 'nhập khẩu') !== false) {
        $features = [
            'Trọn gói từ A-Z',
            'Hoá đơn VAT + giấy tờ đầy đủ',
            'Tư vấn thuế nhập khẩu',
            'Hỗ trợ pháp lý'
        ];
        $icon = 'fas fa-file-invoice';
    } else {
        $features = [
            'Lên đơn và mua hộ qua các sàn thương mại 1688, Taobao, Tmall, Alibaba…',
            'Thanh toán qua Wechat, Alipay, tài khoản ngân hàng, mã QR, uỷ quyền',
            'Thanh toán an toàn, kiểm tra chất lượng',
            'Đàm phán giá tốt, hỗ trợ trọn gói'
        ];
        $icon = 'fas fa-shopping-cart';
    }

    return [
        'features' => array_slice($features, 0, 4),
        'icon' => $icon
    ];
}

// Fetch services
$services = [];
try {
    $stmt = $pdo->query("SELECT * FROM services ORDER BY sort_order ASC, created_at DESC");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    setMessage('Lỗi đọc dữ liệu: ' . $e->getMessage(), 'error');
}

// Apply updates if requested
$updated = 0; $skipped = 0; $iconUpdated = 0; $errors = 0;
if ($applyChanges && !empty($services)) {
    foreach ($services as $service) {
        $map = mapFeaturesAndIconByTitle($service['title']);
        $newShort = implode("\n", $map['features']);
        $needShort = $forceOverwrite || empty(trim((string)($service['short_description'] ?? '')));
        $needIcon = empty(trim((string)($service['icon'] ?? '')));

        if ($needShort || $needIcon) {
            try {
                $params = [];
                $setParts = [];
                if ($needShort) { $setParts[] = 'short_description = ?'; $params[] = $newShort; }
                if ($needIcon)  { $setParts[] = 'icon = ?'; $params[] = $map['icon']; }
                $setSql = implode(', ', $setParts);
                $params[] = $service['id'];

                $sql = "UPDATE services SET $setSql, updated_at = NOW() WHERE id = ?";
                $upd = $pdo->prepare($sql);
                $upd->execute($params);
                if ($needShort) { $updated++; }
                if ($needIcon)  { $iconUpdated++; }
            } catch (PDOException $e) {
                $errors++;
            }
        } else {
            $skipped++;
        }
    }
    setMessage("Cập nhật xong! short_description: $updated, icon: $iconUpdated, bỏ qua: $skipped, lỗi: $errors", 'success');
}

include 'includes/header.php';
?>

<div class="row mb-4">
  <div class="col-12 d-flex justify-content-between align-items-center">
    <div>
      <h1 class="h3 mb-2">Nhập dữ liệu động cho Dịch vụ</h1>
      <p class="text-muted mb-0">Script này sẽ tự động điền trường <code>short_description</code> (mỗi dòng là 1 bullet) và <code>icon</code> nếu đang để trống.</p>
    </div>
    <div class="d-flex gap-2">
      <a href="seed-service-features.php?apply=1" class="btn btn-admin-primary"><i class="fas fa-play me-2"></i>Chạy cập nhật</a>
      <a href="seed-service-features.php?apply=1&force=1" class="btn btn-danger" onclick="return confirm('Ghi đè tất cả short_description hiện có?');"><i class="fas fa-exclamation-triangle me-2"></i>Chạy và ghi đè</a>
    </div>
  </div>
</div>

<div class="admin-card">
  <div class="card-header">
    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Xem trước dữ liệu sẽ cập nhật</h5>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th style="width:60px">ID</th>
            <th>Tiêu đề</th>
            <th>short_description (hiện tại)</th>
            <th>short_description (mới)</th>
            <th>Icon hiện tại</th>
            <th>Icon mới</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($services as $sv): $map = mapFeaturesAndIconByTitle($sv['title']); ?>
            <tr>
              <td><?php echo (int)$sv['id']; ?></td>
              <td><?php echo htmlspecialchars($sv['title']); ?></td>
              <td><pre style="white-space:pre-wrap;margin:0"><?php echo htmlspecialchars($sv['short_description'] ?? ''); ?></pre></td>
              <td><pre style="white-space:pre-wrap;margin:0"><?php echo htmlspecialchars(implode("\n", $map['features'])); ?></pre></td>
              <td><i class="<?php echo htmlspecialchars($sv['icon'] ?: ''); ?>"></i> <code><?php echo htmlspecialchars($sv['icon'] ?: ''); ?></code></td>
              <td><i class="<?php echo htmlspecialchars($map['icon']); ?>"></i> <code><?php echo htmlspecialchars($map['icon']); ?></code></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="mt-3 text-muted small">
      Gợi ý: Nhấn "Chạy cập nhật" để chỉ điền các ô đang trống. Dùng "Chạy và ghi đè" nếu muốn thay toàn bộ theo gợi ý.
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>


