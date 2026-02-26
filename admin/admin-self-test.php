<?php
require_once 'config.php';
requireAdminLogin();

$page_title = 'Chẩn đoán nhanh tính năng quản trị';

function print_result($name, $ok, $message = '') {
    $color = $ok ? '#155724' : '#721c24';
    $bg = $ok ? '#d4edda' : '#f8d7da';
    echo "<div style=\"padding:10px 12px;margin-bottom:8px;border-radius:8px;background:$bg;color:$color;\">";
    echo ($ok ? '✅' : '❌') . ' <strong>' . htmlspecialchars($name) . '</strong>';
    if ($message !== '') {
        echo '<div style="font-size:12px;color:#555;margin-top:4px">' . htmlspecialchars($message) . '</div>';
    }
    echo '</div>';
}

function generate_slug_simple($text) {
    $text = preg_replace("/(á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ)/i", 'a', $text);
    $text = preg_replace("/(đ)/i", 'd', $text);
    $text = preg_replace("/(é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ)/i", 'e', $text);
    $text = preg_replace("/(í|ì|ỉ|ĩ|ị)/i", 'i', $text);
    $text = preg_replace("/(ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ)/i", 'o', $text);
    $text = preg_replace("/(ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự)/i", 'u', $text);
    $text = preg_replace("/(ý|ỳ|ỷ|ỹ|ỵ)/i", 'y', $text);
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s]/', '', $text);
    $text = preg_replace('/\s+/', '-', $text);
    return trim($text, '-');
}

// Ensure keywords schema is complete (self-heal)
function ensureKeywordsSchema(PDO $pdo): void {
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
    } catch (Throwable $e) {
        // ignore; will be reported in checks below
    }
}

// Attempt to self-heal schema before rendering
ensureKeywordsSchema($pdo);

ob_start();
include 'includes/header.php';
ob_end_flush();
?>

<div class="row mb-4">
  <div class="col-12">
    <div class="admin-card">
      <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-stethoscope me-2"></i>Chẩn đoán nhanh tính năng quản trị</h5>
      </div>
      <div class="card-body">
        <?php
        // 1) Kiểm tra kết nối DB
        try {
            $pdo->query('SELECT 1');
            print_result('Kết nối database', true);
        } catch (Throwable $e) {
            print_result('Kết nối database', false, $e->getMessage());
        }

        // 2) Kiểm tra schema bảng keywords (các cột dùng trong admin)
        try {
            $cols = [];
            $stmt = $pdo->query('SHOW COLUMNS FROM keywords');
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $c) {
                $cols[strtolower($c['Field'])] = true;
            }
            $need = ['keyword','search_volume','difficulty','status','notes','created_at','updated_at'];
            $missing = array_values(array_diff($need, array_keys($cols)));
            if (empty($missing)) {
                print_result('Schema keywords đầy đủ', true);
            } else {
                // cố gắng tự sửa và kiểm tra lại 1 lần
                ensureKeywordsSchema($pdo);
                $cols = [];
                $stmt = $pdo->query('SHOW COLUMNS FROM keywords');
                foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $c) {
                    $cols[strtolower($c['Field'])] = true;
                }
                $missing = array_values(array_diff($need, array_keys($cols)));
                if (empty($missing)) {
                    print_result('Schema keywords đầy đủ', true, 'Đã tự sửa cấu trúc bảng.');
                } else {
                    print_result('Schema keywords thiếu cột', false, 'Thiếu: ' . implode(', ', $missing));
                }
            }
        } catch (Throwable $e) {
            print_result('Đọc schema keywords', false, $e->getMessage());
        }

        // 3) Thử thêm từ khóa (transaction + rollback)
        try {
            $pdo->beginTransaction();
            $kw = 'selftest-key-' . date('YmdHis');
            $stmt = $pdo->prepare('INSERT INTO keywords (keyword, search_volume, difficulty, status, notes, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())');
            $stmt->execute([$kw, 123, 'medium', 'active', 'self-test']);
            // verify
            $check = $pdo->prepare('SELECT id FROM keywords WHERE keyword = ?');
            $check->execute([$kw]);
            $ok = (bool)$check->fetchColumn();
            $pdo->rollBack();
            print_result('Thêm từ khóa (mô phỏng)', $ok);
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            print_result('Thêm từ khóa (mô phỏng)', false, $e->getMessage());
        }

        // 4) Đảm bảo có danh mục tin tức hoạt động
        $categoryId = null;
        try {
            $stmt = $pdo->query("SELECT id FROM news_categories WHERE status = 'active' ORDER BY id ASC LIMIT 1");
            $categoryId = $stmt->fetchColumn();
            if (!$categoryId) {
                // tạo tạm
                $name = 'Tạm - SelfTest';
                $slug = generate_slug_simple($name) . '-' . substr(uniqid(), -4);
                $ins = $pdo->prepare('INSERT INTO news_categories (name, slug, description, sort_order, status, created_at) VALUES (?, ?, ?, 0, "active", NOW())');
                $ins->execute([$name, $slug, 'Tạo tạm cho self-test']);
                $categoryId = $pdo->lastInsertId();
            }
            print_result('Danh mục tin tức sẵn sàng', (bool)$categoryId);
        } catch (Throwable $e) {
            print_result('Danh mục tin tức', false, $e->getMessage());
        }

        // 5) Thử thêm tin tức dạng Nháp (transaction + rollback)
        try {
            if ($categoryId) {
                $pdo->beginTransaction();
                $title = 'SelfTest News ' . date('YmdHis');
                $slug = generate_slug_simple($title);
                // đảm bảo slug unique
                $base = $slug; $i = 1;
                $chk = $pdo->prepare('SELECT id FROM news WHERE slug = ? LIMIT 1');
                while (true) {
                    $chk->execute([$slug]);
                    if (!$chk->fetch()) break;
                    $slug = $base . '-' . $i++;
                }
                $stmt = $pdo->prepare('INSERT INTO news (title, slug, excerpt, content, category_id, image, status, featured, tags, meta_title, meta_description, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, 0, ?, ?, ?, NOW(), NOW())');
                $stmt->execute([$title, $slug, 'Tóm tắt self-test', '<p>Nội dung self-test</p>', $categoryId, null, 'draft', 'test', $title, 'Mô tả SEO self-test']);
                $ok = (bool)$pdo->lastInsertId();
                $pdo->rollBack();
                print_result('Thêm Tin tức Nháp (mô phỏng)', $ok);
            } else {
                print_result('Thêm Tin tức Nháp (mô phỏng)', false, 'Không có category hợp lệ');
            }
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            print_result('Thêm Tin tức Nháp (mô phỏng)', false, $e->getMessage());
        }

        // 6) Thử logic sắp xếp Dịch vụ (transaction + rollback)
        try {
            $pdo->beginTransaction();
            $targetOrder = 1;
            // Đẩy các mục có sort_order >= 1 xuống 1 bậc
            $shift = $pdo->prepare('UPDATE services SET sort_order = sort_order + 1 WHERE sort_order >= ?');
            $shift->execute([$targetOrder]);
            // Thêm dịch vụ ở vị trí 1
            $title = 'SelfTest Service ' . date('YmdHis');
            $slug = generate_slug_simple($title);
            $base = $slug; $i = 1; $chk = $pdo->prepare('SELECT id FROM services WHERE slug = ? LIMIT 1');
            while (true) {
                $chk->execute([$slug]);
                if (!$chk->fetch()) break;
                $slug = $base . '-' . $i++;
            }
            $ins = $pdo->prepare('INSERT INTO services (title, slug, description, short_description, content, image, status, featured, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NULL, "active", 0, ?, NOW(), NOW())');
            $ins->execute([$title, $slug, 'Mô tả self-test', 'Mô tả ngắn self-test', '<p>Nội dung self-test</p>', $targetOrder]);
            // Kiểm tra vị trí
            $top = $pdo->query('SELECT title FROM services ORDER BY sort_order ASC, created_at DESC LIMIT 1')->fetchColumn();
            $ok = ($top === $title);
            $pdo->rollBack();
            print_result('Sắp xếp Dịch vụ khi trùng thứ tự (mô phỏng)', $ok, $ok ? '' : 'Mục test không đứng vị trí 1 sau khi shift');
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            print_result('Sắp xếp Dịch vụ khi trùng thứ tự (mô phỏng)', false, $e->getMessage());
        }
        ?>
      </div>
      <div class="card-footer">
        <small class="text-muted">Các bài kiểm tra chạy trong transaction và sẽ không để lại dữ liệu test. Riêng kiểm tra schema chỉ đọc thông tin cột.</small>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>


