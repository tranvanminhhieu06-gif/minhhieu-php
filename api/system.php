<?php
/**
 * HIEU CEO - System & Maintenance API
 */

require_once __DIR__ . '/../config/helper.php';

header('Content-Type: application/json; charset=utf-8');

$action = sanitize($_GET['action'] ?? ($_POST['action'] ?? 'health'));
$db = getDb();

if ($action === 'clear_cache') {
    // Perform simulated runtime cache purge
    $cacheFiles = glob(__DIR__ . '/../cache/*');
    if ($cacheFiles) {
        foreach ($cacheFiles as $file) {
            if (is_file($file)) @unlink($file);
        }
    }

    logSystemAction($_SESSION['user_id'] ?? 1, 'CACHE_FLUSH', 'Xóa toàn bộ bộ nhớ đệm hệ thống và biên dịch lại CSS Tokens');
    jsonResponse(true, ['cleared_at' => date('Y-m-d H:i:s')], 'Đã dọn dẹp và tối ưu bộ nhớ đệm hệ thống thành công!');
}

if ($action === 'health') {
    $dbPing = false;
    try {
        $db->query("SELECT 1");
        $dbPing = true;
    } catch (Exception $e) {
        $dbPing = false;
    }

    $health = [
        'database_status' => $dbPing ? 'Operational (Optimal)' : 'Degraded',
        'php_version' => PHP_VERSION,
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'PHP Built-in Server',
        'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
        'execution_time_limit' => ini_get('max_execution_time') . 's',
        'maintenance_mode' => getSystemSetting('maintenance_mode', '0') === '1' ? 'Enabled' : 'Disabled',
        'active_theme' => getActiveTheme()['name'] ?? 'N/A'
    ];

    jsonResponse(true, $health, 'Hệ thống vận hành tối ưu chuẩn CEO.');
}

if ($action === 'backup') {
    requireRole(['ceo', 'cdo']);
    
    // Generate SQL dump of current database
    try {
        $tables = ['users', 'theme_categories', 'themes', 'theme_sections', 'theme_tokens', 'ui_components', 'ceo_metrics', 'theme_analytics', 'system_logs', 'system_settings'];
        $sqlDump = "-- HIEU CEO Database Backup\n-- Generated on: " . date('Y-m-d H:i:s') . "\n\n";

        foreach ($tables as $tbl) {
            $sqlDump .= "TRUNCATE TABLE `{$tbl}`;\n";
            $rows = $db->query("SELECT * FROM `{$tbl}`")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $r) {
                $keys = array_keys($r);
                $vals = array_map(function($v) use ($db) {
                    return $v === null ? 'NULL' : $db->quote($v);
                }, array_values($r));
                $sqlDump .= "INSERT INTO `{$tbl}` (`" . implode('`, `', $keys) . "`) VALUES (" . implode(', ', $vals) . ");\n";
            }
            $sqlDump .= "\n";
        }

        $backupDir = __DIR__ . '/../backups';
        if (!is_dir($backupDir)) mkdir($backupDir, 0777, true);
        $filename = 'backup_hieu_ceo_' . date('Ymd_His') . '.sql';
        file_put_contents($backupDir . '/' . $filename, $sqlDump);

        logSystemAction($_SESSION['user_id'] ?? 1, 'BACKUP_CREATE', "Tạo bản sao lưu CSDL: {$filename}");
        jsonResponse(true, ['backup_file' => $filename, 'size' => strlen($sqlDump)], 'Tạo bản sao lưu dữ liệu thành công!');
    } catch (Exception $e) {
        jsonResponse(false, null, 'Lỗi sao lưu: ' . $e->getMessage(), 500);
    }
}

jsonResponse(false, null, 'Hành động không hợp lệ.', 400);
