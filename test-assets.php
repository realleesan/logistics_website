<?php
// Simple diagnostic page to verify static asset URLs and file existence
// You can delete this file after testing

require_once __DIR__ . '/database/config.php';

header('Content-Type: text/html; charset=UTF-8');

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// Build absolute URL via asset_url (defined in includes/functions.php via config)
function abs_url($path) {
    return asset_url($path);
}

// Map a web path to local filesystem path
function to_local_path($urlOrPath) {
    // Strip domain if present
    $u = $urlOrPath;
    if (defined('APP_URL') && APP_URL) {
        $u = preg_replace('#^' . preg_quote(rtrim(APP_URL, '/'), '#') . '#i', '', $u);
    }
    // Ensure leading slash removed for filesystem join
    $u = ltrim($u, '/');
    return __DIR__ . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $u);
}

function check_item($label, $path) {
    $url = abs_url($path);
    $local = to_local_path($url);
    $exists = file_exists($local);
    return [
        'label' => $label,
        'path' => $path,
        'url' => $url,
        'local' => $local,
        'exists' => $exists,
    ];
}

$items = [];

// Core assets
$items[] = check_item('Main JS', 'assets/js/main.js');
$items[] = check_item('Main CSS', 'assets/css/style.css');
$items[] = check_item('Logo (root)', '/logo.jpg');
$items[] = check_item('Logo removebg (root)', '/logo-removebg.png');
$items[] = check_item('QR Code (root)', '/qr_code.jpg');

// Image set index1..14
for ($i = 1; $i <= 14; $i++) {
    $items[] = check_item("Image index$i.png", "assets/images/index$i.png");
}

// News images from DB
$newsRows = [];
try {
    $stmt = $pdo->prepare("SELECT slug, title, image FROM news WHERE status = 'published' ORDER BY created_at DESC LIMIT 100");
    $stmt->execute();
    $newsRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $newsRows = [];
}

$newsChecks = [];
foreach ($newsRows as $row) {
    $imgPath = $row['image'] ?? '';
    if (!$imgPath) continue;
    $url = abs_url($imgPath);
    $local = to_local_path($url);
    $exists = (stripos($url, 'http') === 0) ? true : file_exists($local);
    $newsChecks[] = [
        'slug' => $row['slug'],
        'title' => $row['title'],
        'image' => $imgPath,
        'url' => $url,
        'local' => $local,
        'exists' => $exists,
    ];
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Test Assets</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    table { border-collapse: collapse; width: 100%; margin-bottom: 30px; }
    th, td { border: 1px solid #ddd; padding: 8px; }
    th { background: #f5f5f5; text-align: left; }
    .ok { color: #2e7d32; font-weight: 600; }
    .fail { color: #c62828; font-weight: 600; }
    code { background: #f9f9f9; padding: 2px 4px; border-radius: 4px; }
  </style>
  <base href="<?php echo h(rtrim(APP_URL, '/')); ?>/">
</head>
<body>
  <h1>Kiểm tra tài nguyên (Assets)</h1>
  <p>APP_URL: <code><?php echo h(APP_URL); ?></code></p>

  <h2>Tệp tĩnh cốt lõi</h2>
  <table>
    <tr><th>Label</th><th>Configured Path</th><th>URL</th><th>Local Path</th><th>Status</th></tr>
    <?php foreach ($items as $it): ?>
      <tr>
        <td><?php echo h($it['label']); ?></td>
        <td><code><?php echo h($it['path']); ?></code></td>
        <td><a href="<?php echo h($it['url']); ?>" target="_blank"><?php echo h($it['url']); ?></a></td>
        <td><code><?php echo h($it['local']); ?></code></td>
        <td class="<?php echo $it['exists'] ? 'ok' : 'fail'; ?>"><?php echo $it['exists'] ? 'OK' : 'NOT FOUND'; ?></td>
      </tr>
    <?php endforeach; ?>
  </table>

  <h2>Ảnh tin tức từ DB (tối đa 100 bài)</h2>
  <table>
    <tr><th>Slug</th><th>Title</th><th>DB Image</th><th>Resolved URL</th><th>Local Path</th><th>Status</th></tr>
    <?php foreach ($newsChecks as $nc): ?>
      <tr>
        <td><code><?php echo h($nc['slug']); ?></code></td>
        <td><?php echo h($nc['title']); ?></td>
        <td><code><?php echo h($nc['image']); ?></code></td>
        <td><a href="<?php echo h($nc['url']); ?>" target="_blank"><?php echo h($nc['url']); ?></a></td>
        <td><code><?php echo h($nc['local']); ?></code></td>
        <td class="<?php echo $nc['exists'] ? 'ok' : 'fail'; ?>"><?php echo $nc['exists'] ? 'OK' : 'NOT FOUND'; ?></td>
      </tr>
    <?php endforeach; ?>
  </table>

  <p>Nếu có mục NOT FOUND, cần tải đúng tệp lên máy chủ theo cột Local Path hoặc cập nhật đường dẫn trong DB về đúng vị trí.</p>
</body>
</html>


