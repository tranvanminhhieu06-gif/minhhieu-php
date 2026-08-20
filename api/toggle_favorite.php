<?php
/**
 * HIEU CEO API - Toggle Favorite Theme/Project
 * Xử lý thêm hoặc gỡ giao diện khỏi danh sách yêu thích
 */

require_once __DIR__ . '/../config/auth_user.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Chỉ chấp nhận phương thức POST']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$themeId = (int)($input['theme_id'] ?? 0);

if ($themeId <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID giao diện không hợp lệ']);
    exit;
}

$theme = getThemeById($themeId);
$themeName = $theme ? $theme['name'] : "Dự án #{$themeId}";

$isFavorited = toggleUserFavoriteTheme($themeId);
$totalFavorites = count($_SESSION['user_favorites'] ?? []);

logUserAction('FAVORITE', ($isFavorited ? 'Yêu thích' : 'Bỏ yêu thích') . " dự án {$themeName} (ID: {$themeId})");

echo json_encode([
    'success' => true,
    'favorited' => $isFavorited,
    'total_favorites' => $totalFavorites,
    'theme_id' => $themeId,
    'theme_name' => $themeName,
    'message' => $isFavorited ? "Đã lưu \"{$themeName}\" vào danh sách yêu thích!" : "Đã gỡ \"{$themeName}\" khỏi danh sách yêu thích."
], JSON_UNESCAPED_UNICODE);
