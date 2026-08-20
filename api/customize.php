<?php
/**
 * HIEU CEO - Live Customizer Save API
 */

require_once __DIR__ . '/../config/helper.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, null, 'Method Not Allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$themeId = (int)($input['theme_id'] ?? 0);

if (!$themeId) {
    jsonResponse(false, null, 'ID giao diện không hợp lệ.', 400);
}

$db = getDb();

try {
    $db->beginTransaction();

    $primary = sanitize($input['primary_color'] ?? '#6366f1');
    $secondary = sanitize($input['secondary_color'] ?? '#ec4899');
    $accent = sanitize($input['accent_color'] ?? '#06b6d4');
    $bg = sanitize($input['bg_color'] ?? '#0f172a');
    $font = sanitize($input['font_family'] ?? 'Outfit');
    $customCss = $input['custom_css'] ?? '';

    // Update Theme Record
    $stmt = $db->prepare("UPDATE `themes` SET 
        `primary_color` = :c1,
        `secondary_color` = :c2,
        `accent_color` = :c3,
        `bg_color` = :bg,
        `font_family` = :font,
        `custom_css` = :css,
        `updated_at` = NOW()
        WHERE `id` = :id");

    $stmt->execute([
        ':c1' => $primary,
        ':c2' => $secondary,
        ':c3' => $accent,
        ':bg' => $bg,
        ':font' => $font,
        ':css' => $customCss,
        ':id' => $themeId
    ]);

    // Update Theme Tokens
    $tokens = [
        '--ceo-primary' => $primary,
        '--ceo-secondary' => $secondary,
        '--ceo-accent' => $accent,
        '--ceo-bg-dark' => $bg,
        '--ceo-font' => "'{$font}', sans-serif"
    ];

    $tokStmt = $db->prepare("INSERT INTO `theme_tokens` (`theme_id`, `token_key`, `token_value`, `token_type`)
        VALUES (:tid, :k, :v, 'color')
        ON DUPLICATE KEY UPDATE `token_value` = :v2");

    foreach ($tokens as $k => $v) {
        $tokStmt->execute([':tid' => $themeId, ':k' => $k, ':v' => $v, ':v2' => $v]);
    }

    // Update Sections
    if (!empty($input['sections']) && is_array($input['sections'])) {
        $secStmt = $db->prepare("UPDATE `theme_sections` SET `is_enabled` = :enb WHERE `theme_id` = :tid AND `section_key` = :key");
        foreach ($input['sections'] as $sKey => $isEnabled) {
            $secStmt->execute([
                ':enb' => $isEnabled ? 1 : 0,
                ':tid' => $themeId,
                ':key' => sanitize($sKey)
            ]);
        }
    }

    logSystemAction($_SESSION['user_id'] ?? 1, 'THEME_CUSTOMIZE', "Cập nhật tùy biến cho giao diện ID #{$themeId}");

    $db->commit();
    jsonResponse(true, null, 'Đã lưu cấu hình tùy biến thành công!');
} catch (Exception $e) {
    $db->rollBack();
    jsonResponse(false, null, 'Lỗi khi lưu cấu hình: ' . $e->getMessage(), 500);
}
