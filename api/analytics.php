<?php
/**
 * HIEU CEO - Analytics API Feed
 */

require_once __DIR__ . '/../config/helper.php';

header('Content-Type: application/json; charset=utf-8');

$db = getDb();
$themeId = (int)($_GET['theme_id'] ?? 1);

try {
    // 1. Fetch KPI Metrics
    $metrics = $db->query("SELECT * FROM `ceo_metrics`")->fetchAll();

    // 2. Fetch 7-Day Performance for Active Theme
    $stmt = $db->prepare("SELECT report_date, pageviews, unique_visitors, bounce_rate, avg_load_time_ms, conversion_rate 
                          FROM `theme_analytics` 
                          WHERE `theme_id` = :tid 
                          ORDER BY `report_date` ASC LIMIT 7");
    $stmt->execute([':tid' => $themeId]);
    $analytics = $stmt->fetchAll();

    // 3. Device Breakdown (Calculated / Estimated)
    $devices = [
        ['name' => 'Desktop / Laptop', 'percent' => 58.4, 'color' => '#6366f1'],
        ['name' => 'Mobile Devices', 'percent' => 34.2, 'color' => '#ec4899'],
        ['name' => 'Tablet / iPad', 'percent' => 7.4, 'color' => '#06b6d4']
    ];

    jsonResponse(true, [
        'metrics' => $metrics,
        'analytics' => $analytics,
        'devices' => $devices
    ], 'Lấy dữ liệu phân tích CEO thành công.');
} catch (Exception $e) {
    jsonResponse(false, null, 'Lỗi phân tích: ' . $e->getMessage(), 500);
}
