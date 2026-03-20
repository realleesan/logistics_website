<?php
/**
 * Heartbeat Script - Keeps database connections alive
 * This lightweight script can be called periodically to prevent connection timeouts
 * 
 * Usage:
 * - Call via JavaScript: fetch('heartbeat.php')
 * - Call via cron job
 * - Call manually when website hangs
 */

// Prevent caching
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Lightweight response - minimal processing
header('Content-Type: application/json');

$startTime = microtime(true);
$status = ['ok' => true, 'time' => date('Y-m-d H:i:s')];

try {
    // Quick database check
    require_once __DIR__ . '/database/config.php';
    
    // Simple lightweight query
    $pdo->query("SELECT 1");
    
    $status['db'] = 'connected';
    $status['db_time'] = round((microtime(true) - $startTime) * 1000, 2) . 'ms';
    
} catch (Exception $e) {
    $status['ok'] = false;
    $status['error'] = $e->getMessage();
}

// Add server info (optional, can be removed for privacy)
$status['execution_time'] = round((microtime(true) - $startTime) * 1000, 2) . 'ms';

// Output minimal JSON
echo json_encode($status);
?>
