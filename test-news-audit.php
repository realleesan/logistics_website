<?php
// News and assets deep audit
// This script will:
// - Crawl all published news detail pages
// - Extract <img>, <script>, <link rel="stylesheet"> URLs
// - Normalize to absolute URLs
// - HTTP-check each asset (status + content-type) to detect 404/MIME errors
// - Summarize failing assets per page
// You can delete this file after testing.

require_once __DIR__ . '/database/config.php';

header('Content-Type: text/html; charset=UTF-8');

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$BASE = rtrim(defined('APP_URL') ? APP_URL : ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']), '/');

function curl_fetch($url, $method = 'GET', $headers = [], $nobody = false) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 5,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_USERAGENT => 'VinaLogistics-TestBot/1.0',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);
    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    if ($method === 'HEAD' || $nobody) {
        curl_setopt($ch, CURLOPT_NOBODY, true);
    }
    $resp = curl_exec($ch);
    if ($resp === false) {
        $err = curl_error($ch);
        $code = curl_errno($ch);
        curl_close($ch);
        return ['ok' => false, 'error' => $err, 'errno' => $code];
    }
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    $headersRaw = substr($resp, 0, $headerSize);
    $body = substr($resp, $headerSize);
    curl_close($ch);
    return [
        'ok' => true,
        'status' => $statusCode,
        'content_type' => $contentType,
        'headers' => $headersRaw,
        'body' => $nobody ? '' : $body,
    ];
}

function normalize_url($url, $base) {
    $u = trim($url);
    if ($u === '') return '';
    if (preg_match('#^https?://#i', $u)) return $u;
    if (strpos($u, '//') === 0) { return 'https:' . $u; }
    // strip ./
    if (strpos($u, './') === 0) { $u = substr($u, 2); }
    // fix common wrong prefix /tin-tuc/assets/...
    if (preg_match('#^/tin-tuc/(assets/.*)$#i', $u, $m)) {
        $u = '/' . $m[1];
    }
    if ($u[0] !== '/') { $u = '/' . $u; }
    return rtrim($base, '/') . $u;
}

function extract_assets($html) {
    $assets = [];
    // scripts
    if (preg_match_all('#<script[^>]+src\s*=\s*([\"\'])([^\"\'>]+)\1#i', $html, $m)) {
        foreach ($m[2] as $src) { $assets[] = ['type' => 'js', 'url' => $src]; }
    }
    // stylesheets
    if (preg_match_all('#<link[^>]+rel\s*=\s*([\"\'])stylesheet\1[^>]+href\s*=\s*([\"\'])([^\"\'>]+)\2#i', $html, $m)) {
        foreach ($m[3] as $href) { $assets[] = ['type' => 'css', 'url' => $href]; }
    }
    // images
    if (preg_match_all('#<img[^>]+src\s*=\s*([\"\'])([^\"\'>]+)\1#i', $html, $m)) {
        foreach ($m[2] as $src) { $assets[] = ['type' => 'img', 'url' => $src]; }
    }
    return $assets;
}

function is_mime_ok($type, $contentType) {
    if (!$contentType) return false;
    $ct = strtolower($contentType);
    if ($type === 'js') return (strpos($ct, 'javascript') !== false) || (strpos($ct, 'application/x-javascript') !== false) || (strpos($ct, 'text/plain') === false && strpos($ct, 'text/html') === false);
    if ($type === 'css') return strpos($ct, 'text/css') !== false;
    if ($type === 'img') return strpos($ct, 'image/') !== false;
    return true;
}

// 1) Fetch news list
$news = [];
try {
    $stmt = $pdo->prepare("SELECT slug, title FROM news WHERE status = 'published' ORDER BY created_at DESC LIMIT 200");
    $stmt->execute();
    $news = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

$report = [];
$summary = [
    'pages_total' => 0,
    'pages_ok' => 0,
    'pages_with_errors' => 0,
    'assets_checked' => 0,
    'assets_errors' => 0,
];

foreach ($news as $row) {
    $slug = $row['slug'];
    $pageUrl = $BASE . '/tin-tuc/' . $slug;
    $pageRes = curl_fetch($pageUrl, 'GET');

    $pageEntry = [
        'slug' => $slug,
        'title' => $row['title'],
        'url' => $pageUrl,
        'status' => $pageRes['ok'] ? ($pageRes['status'] ?? 0) : 0,
        'content_type' => $pageRes['ok'] ? ($pageRes['content_type'] ?? '') : '',
        'errors' => [],
        'assets' => [],
        'hasTinTucAssets' => false,
        'hasBaseTag' => (bool)preg_match('#<base\s+href=#i', $pageRes['ok'] ? $pageRes['body'] : ''),
    ];

    $summary['pages_total']++;

    if (!$pageRes['ok'] || $pageEntry['status'] >= 400) {
        $pageEntry['errors'][] = 'Page load error: ' . (!$pageRes['ok'] ? ($pageRes['error'] ?? 'unknown') : ('HTTP ' . $pageEntry['status']));
        $summary['pages_with_errors']++;
        $report[] = $pageEntry;
        continue;
    }

    $html = $pageRes['body'];
    if (strpos($html, '/tin-tuc/assets/') !== false) {
        $pageEntry['hasTinTucAssets'] = true;
    }

    // Extract assets and test them
    $assets = extract_assets($html);
    foreach ($assets as $a) {
        $abs = normalize_url($a['url'], $BASE);
        // Ignore mailto/tel/data
        if (preg_match('#^(mailto:|tel:|data:)#i', $a['url'])) continue;
        $ar = curl_fetch($abs, 'HEAD', [], true);
        if (!$ar['ok']) { // retry GET tiny
            $ar = curl_fetch($abs, 'GET');
        }
        $st = $ar['ok'] ? ($ar['status'] ?? 0) : 0;
        $ct = $ar['ok'] ? ($ar['content_type'] ?? '') : '';
        $ok = $ar['ok'] && $st >= 200 && $st < 400 && is_mime_ok($a['type'], $ct);
        $summary['assets_checked']++;
        if (!$ok) $summary['assets_errors']++;
        $pageEntry['assets'][] = [
            'type' => $a['type'],
            'raw' => $a['url'],
            'url' => $abs,
            'status' => $st,
            'content_type' => $ct,
            'ok' => $ok,
        ];
    }

    $hasErrors = count(array_filter($pageEntry['assets'], function($x){ return !$x['ok']; })) > 0;
    if ($hasErrors || $pageEntry['hasTinTucAssets'] === true) {
        $summary['pages_with_errors']++;
    } else {
        $summary['pages_ok']++;
    }

    $report[] = $pageEntry;
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>News Audit</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    .grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
    .card { border: 1px solid #ddd; padding: 12px; border-radius: 6px; }
    .ok { color: #2e7d32; font-weight: 600; }
    .fail { color: #c62828; font-weight: 600; }
    table { border-collapse: collapse; width: 100%; margin-top: 10px; }
    th, td { border: 1px solid #ddd; padding: 6px; font-size: 13px; }
    th { background: #f5f5f5; text-align: left; }
    code { background: #f9f9f9; padding: 1px 3px; border-radius: 3px; }
  </style>
</head>
<body>
  <h1>Kiểm tra trang tin và tài nguyên</h1>
  <p>Base: <code><?php echo h($BASE); ?></code></p>
  <div class="grid">
    <div class="card"><div>Tổng số trang:</div><div><strong><?php echo (int)$summary['pages_total']; ?></strong></div></div>
    <div class="card"><div>Trang OK:</div><div class="ok"><strong><?php echo (int)$summary['pages_ok']; ?></strong></div></div>
    <div class="card"><div>Trang có lỗi:</div><div class="fail"><strong><?php echo (int)$summary['pages_with_errors']; ?></strong></div></div>
    <div class="card"><div>Asset lỗi / tổng:</div><div><strong class="fail"><?php echo (int)$summary['assets_errors']; ?></strong> / <strong><?php echo (int)$summary['assets_checked']; ?></strong></div></div>
  </div>

  <?php foreach ($report as $page): ?>
    <div class="card">
      <div><strong><?php echo h($page['title']); ?></strong> — <code><?php echo h($page['slug']); ?></code></div>
      <div>URL: <a href="<?php echo h($page['url']); ?>" target="_blank"><?php echo h($page['url']); ?></a> — Status: <strong><?php echo (int)$page['status']; ?></strong> — Content-Type: <code><?php echo h($page['content_type']); ?></code> — Base tag: <?php echo $page['hasBaseTag'] ? '<span class="ok">YES</span>' : '<span class="fail">NO</span>'; ?> — '/tin-tuc/assets/' trong HTML: <?php echo $page['hasTinTucAssets'] ? '<span class="fail">CÓ</span>' : '<span class="ok">KHÔNG</span>'; ?></div>

      <?php if (!empty($page['assets'])): ?>
      <table>
        <tr><th>#</th><th>Type</th><th>Raw</th><th>Resolved URL</th><th>Status</th><th>Content-Type</th><th>OK</th></tr>
        <?php $i=1; foreach ($page['assets'] as $a): ?>
          <tr>
            <td><?php echo $i++; ?></td>
            <td><?php echo h($a['type']); ?></td>
            <td><code><?php echo h($a['raw']); ?></code></td>
            <td><a href="<?php echo h($a['url']); ?>" target="_blank"><?php echo h($a['url']); ?></a></td>
            <td><?php echo (int)$a['status']; ?></td>
            <td><code><?php echo h($a['content_type']); ?></code></td>
            <td><?php echo $a['ok'] ? '<span class="ok">OK</span>' : '<span class="fail">FAIL</span>'; ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>

  <p>Lưu ý: FAIL cho JS thường do trả về HTML 404 nên Content-Type là text/html. Khi asset tồn tại đúng, lỗi MIME sẽ hết.</p>
</body>
</html>


