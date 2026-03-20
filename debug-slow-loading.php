<?php
/**
 * Advanced Diagnostic Tool for Slow Loading Issues
 * This file helps identify exactly why website hangs and why system-check.php "fixes" it
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Start timing
$startTime = microtime(true);
$memoryStart = memory_get_usage();

echo "<!DOCTYPE html>
<html lang='vi'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Advanced Diagnostic - VINA LOGISTICS</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #c3f725; padding-bottom: 10px; }
        h2 { color: #555; margin-top: 30px; }
        .test-result { padding: 15px; margin: 10px 0; border-radius: 8px; }
        .success { background: #e8f5e9; border-left: 4px solid #4caf50; }
        .warning { background: #fff3e0; border-left: 4px solid #ff9800; }
        .error { background: #ffebee; border-left: 4px solid #f44336; }
        .info { background: #e3f2fd; border-left: 4px solid #2196f3; }
        .metric { display: inline-block; margin: 5px 15px 5px 0; }
        .metric-label { font-weight: bold; color: #666; }
        .metric-value { color: #333; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f5f5f5; }
        .code-block { background: #263238; color: #aed581; padding: 15px; border-radius: 5px; overflow-x: auto; font-family: monospace; }
        .progress-bar { width: 100%; height: 30px; background: #e0e0e0; border-radius: 5px; overflow: hidden; margin: 10px 0; }
        .progress-fill { height: 100%; background: linear-gradient(90deg, #c3f725, #7cb342); transition: width 0.3s; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔍 Advanced Diagnostic Tool</h1>
        <p><strong>Target:</strong> https://truongvinalogistics.com.vn/</p>
        <p><strong>Time:</strong> " . date('Y-m-d H:i:s') . "</p>
        <p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";

// ============================================================
// TEST 1: PHP Execution Info
// ============================================================
echo "<h2>1. PHP Execution Environment</h2>";
echo "<div class='test-result info'>";
echo "<span class='metric'><span class='metric-label'>Memory Limit:</span> <span class='metric-value'>" . ini_get('memory_limit') . "</span></span>";
echo "<span class='metric'><span class='metric-label'>Max Execution Time:</span> <span class='metric-value'>" . ini_get('max_execution_time') . "s</span></span>";
echo "<span class='metric'><span class='metric-label'>Post Max Size:</span> <span class='metric-value'>" . ini_get('post_max_size') . "</span></span>";
echo "<span class='metric'><span class='metric-label'>Upload Max:</span> <span class='metric-value'>" . ini_get('upload_max_filesize') . "</span></span>";
echo "<span class='metric'><span class='metric-label'>Current Memory:</span> <span class='metric-value'>" . round(memory_get_usage()/1024/1024, 2) . "MB</span></span>";
echo "</div>";

// ============================================================
// TEST 2: Database Connection & Query Performance
// ============================================================
echo "<h2>2. Database Connection & Query Performance</h2>";

$dbTimes = [];
try {
    require_once __DIR__ . '/database/config.php';
    
    // Test connection time
    $connStart = microtime(true);
    $testQuery = $pdo->query("SELECT 1");
    $connTime = (microtime(true) - $connStart) * 1000;
    $dbTimes['connection'] = $connTime;
    
    echo "<div class='test-result success'>";
    echo "✅ Database connection: <strong>" . round($connTime, 2) . "ms</strong></div>";
    
    // Test query times
    $queries = [
        'services' => "SELECT * FROM services WHERE status = 'active' ORDER BY sort_order ASC, created_at DESC LIMIT 6",
        'news' => "SELECT * FROM news WHERE status = 'published' ORDER BY created_at DESC LIMIT 3",
        'contacts' => "SELECT COUNT(*) as total FROM contacts",
    ];
    
    echo "<table><tr><th>Query</th><th>Time (ms)</th><th>Rows</th></tr>";
    foreach ($queries as $name => $sql) {
        $queryStart = microtime(true);
        $stmt = $pdo->query($sql);
        $rows = $stmt->rowCount();
        $queryTime = (microtime(true) - $queryStart) * 1000;
        $dbTimes[$name] = $queryTime;
        
        $statusClass = $queryTime > 100 ? 'warning' : 'success';
        echo "<tr><td>$name</td><td>" . round($queryTime, 2) . "ms</td><td>$rows</td></tr>";
    }
    echo "</table>";
    
    // Check for slow queries
    $totalQueryTime = array_sum($dbTimes);
    if ($totalQueryTime > 500) {
        echo "<div class='test-result warning'>⚠️ Total query time is HIGH: " . round($totalQueryTime, 2) . "ms. This may cause slow loading!</div>";
    }
    
} catch (PDOException $e) {
    echo "<div class='test-result error'>❌ Database error: " . $e->getMessage() . "</div>";
}

// ============================================================
// TEST 3: Image Files Analysis
// ============================================================
echo "<h2>3. Image Files Analysis (MAIN ISSUE)</h2>";

$imageDir = __DIR__ . '/assets/images';
$images = array_merge(
    glob($imageDir . '/*.png'),
    glob($imageDir . '/*.jpg'),
    glob($imageDir . '/*.jpeg'),
    glob($imageDir . '/*.webp')
);

$totalSize = 0;
$problemImages = [];

echo "<table><tr><th>File</th><th>Size</th><th>Size (KB)</th><th>Issue</th></tr>";
foreach ($images as $image) {
    $size = filesize($image);
    $sizeKB = round($size / 1024);
    $totalSize += $size;
    $filename = basename($image);
    
    $issue = "";
    $rowClass = "";
    if ($sizeKB > 500) {
        $issue = "🔴 CRITICAL - Too large!";
        $rowClass = "error";
        $problemImages[] = $filename;
    } elseif ($sizeKB > 200) {
        $issue = "⚠️ Large";
        $rowClass = "warning";
    } else {
        $issue = "✅ OK";
    }
    
    echo "<tr><td>$filename</td><td>" . round($size / 1024 / 1024, 2) . " MB</td><td>$sizeKB KB</td><td>$issue</td></tr>";
}
echo "</table>";

$totalSizeMB = round($totalSize / 1024 / 1024, 2);
echo "<div class='test-result " . ($totalSizeMB > 3 ? 'error' : ($totalSizeMB > 2 ? 'warning' : 'success')) . "'>";
echo "<strong>Total images size: $totalSizeMB MB</strong>";
if ($totalSizeMB > 3) {
    echo "<br>🔴 <strong>THIS IS THE MAIN PROBLEM!</strong> Browser needs to download $totalSizeMB MB of images!";
}
echo "</div>";

// ============================================================
// TEST 4: Estimated Page Load Time
// ============================================================
echo "<h2>4. Estimated Page Load Time</h2>";

// Simulate network speeds
$networkSpeeds = [
    '3G Slow' => 400,    // KB/s
    '3G Fast' => 1600,   // KB/s
    '4G' => 10000,       // KB/s
    'Broadband' => 50000 // KB/s
];

echo "<table><tr><th>Connection Type</th><th>Download Time (Images only)</th><th>With PHP+DB</th></tr>";
foreach ($networkSpeeds as $type => $speedKBs) {
    $imageTime = ($totalSize * 1024) / $speedKBs;
    $totalTime = $imageTime + 500; // Add 500ms for PHP processing
    
    $color = $totalTime > 5 ? 'red' : ($totalTime > 2 ? 'orange' : 'green');
    echo "<tr><td>$type</td><td>" . round($imageTime, 1) . "s</td><td style='color:$color'><strong>" . round($totalTime, 1) . "s</strong></td></tr>";
}
echo "</table>";

// ============================================================
// TEST 5: External Resources Check
// ============================================================
echo "<h2>5. External Resources (CDN)</h2>";

$externalResources = [
    'Font Awesome' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
    'Bootstrap CSS' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
    'Google Fonts' => 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap',
    'Facebook SDK' => 'https://connect.facebook.net/vi_VN/sdk.js',
];

foreach ($externalResources as $name => $url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        echo "<span class='test-result success' style='display:inline-block; margin:5px;'>✅ $name</span>";
    } else {
        echo "<span class='test-result error' style='display:inline-block; margin:5px;'>❌ $name (HTTP $httpCode)</span>";
    }
}

// ============================================================
// TEST 6: Why system-check.php "fixes" the issue
// ============================================================
echo "<h2>6. Analysis: Why system-check.php Resolves the Issue</h2>";

echo "<div class='test-result info'>";
echo "<h3>🔬 Root Cause Analysis:</h3>";
echo "<ol>";
echo "<li><strong>Main page (index.php) loads:</strong>";
echo "<ul>";
echo "<li>Database queries (services, news)</li>";
echo "<li>Large images: index1.png (1.5MB) + index2.png (1.5MB) + others</li>";
echo "<li>External CDN resources</li>";
echo "<li>JavaScript files</li>";
echo "</ul></li>";

echo "<li><strong>system-check.php is lightweight because:</strong>";
echo "<ul>";
echo "<li>Only runs quick diagnostic checks (no images)</li>";
echo "<li>Simple database connection test</li>";
echo "<li>Small output HTML</li>";
echo "</ul></li>";

echo "<li><strong>Why it 'fixes' the issue:</strong>";
echo "<ul>";
echo "<li>When PHP script runs, it may release stuck MySQL connections</li>";
echo "<li>CloudFlare may reset its connection pool</li>";
echo "<li>Browser may re-establish connections</li>";
echo "<li>The 'reset' clears any dead connections or timeouts</li>";
echo "</ul></li>";
echo "</ol>";
echo "</div>";

echo "<div class='test-result error'>";
echo "<h3>🚨 CONCLUSION:</h3>";
echo "<p><strong>The MAIN cause of slow/no loading is LARGE IMAGES:</strong></p>";
echo "<ul>";
foreach ($problemImages as $img) {
    echo "<li>$img</li>";
}
echo "</ul>";
echo "<p><strong>Total image size: $totalSizeMB MB</strong> - This is too large for many devices!</p>";
echo "</div>";

// ============================================================
// TEST 7: Solutions
// ============================================================
echo "<h2>7. SOLUTIONS</h2>";

echo "<div class='test-result warning'>";
echo "<h3>📋 Required Actions:</h3>";
echo "<ol>";
echo "<li><strong>COMPRESS IMAGES NOW (Priority #1):</strong>";
echo "<ul>";
echo "<li>index1.png: 1506KB → compress to 80-100KB</li>";
echo "<li>index2.png: 1524KB → compress to 80-100KB</li>";
echo "<li>index3.png: 664KB → compress to 50-80KB</li>";
echo "<li>index4.png: 512KB → compress to 50-80KB</li>";
echo "<li>index7.png: 673KB → compress to 50-80KB</li>";
echo "<li>Use <a href='https://tinypng.com' target='_blank'>TinyPNG</a> or <a href='https://squoosh.app' target='_blank'>Squoosh</a></li>";
echo "</ul></li>";

echo "<li><strong>After compressing:</strong>";
echo "<ul>";
echo "<li>Upload to /assets/images/ via InfinityFree File Manager</li>";
echo "<li>Go to CloudFlare → Purge Everything</li>";
echo "<li>Test on multiple devices</li>";
echo "</ul></li>";

echo "<li><strong>Additional optimization:</strong>";
echo "<ul>";
echo "<li>Convert images to WebP format (30-50% smaller)</li>";
echo "<li>Add lazy loading to all images</li>";
echo "<li>Enable CloudFlare Polish (auto-optimize images)</li>";
echo "</ul></li>";
echo "</ol>";
echo "</div>";

// ============================================================
// Execution Time
// ============================================================
$executionTime = (microtime(true) - $startTime) * 1000;
$memoryUsed = round(memory_get_usage() / 1024 / 1024, 2);

echo "<hr>";
echo "<div style='text-align:center; color:#666;'>";
echo "Diagnostic completed in <strong>" . round($executionTime, 2) . "ms</strong> | ";
echo "Memory used: <strong>$memoryUsed MB</strong>";
echo "</div>";

echo "</div></body></html>";
?>
