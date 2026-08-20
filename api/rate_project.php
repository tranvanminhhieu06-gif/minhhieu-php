<?php
/**
 * HIEU CEO API - Rate & Review Project
 * Xử lý gửi đánh giá sao và bình luận của người dùng
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
$rating = (float)($input['rating'] ?? 5);
$comment = sanitize($input['comment'] ?? '');

if ($themeId <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID dự án không hợp lệ']);
    exit;
}

$rating = max(1.0, min(5.0, $rating));
$user = getUserProfile();
$userName = $user ? $user['full_name'] : 'Khách Ẩn Danh';

try {
    $db = getDb();
    
    // Cập nhật rating trung bình trong bảng themes
    $stmt = $db->prepare("SELECT rating, downloads_count FROM `themes` WHERE `id` = :id LIMIT 1");
    $stmt->execute([':id' => $themeId]);
    $theme = $stmt->fetch();
    
    if ($theme) {
        $currentRating = (float)$theme['rating'];
        // Tính rating mới làm mượt
        $newRating = round(($currentRating * 9 + $rating) / 10, 2);
        
        $update = $db->prepare("UPDATE `themes` SET `rating` = :r, `downloads_count` = `downloads_count` + 1 WHERE `id` = :id");
        $update->execute([':r' => $newRating, ':id' => $themeId]);
    }
    
    logUserAction('RATING', "Người dùng {$userName} đánh giá {$rating}⭐ cho dự án ID {$themeId}. Nội dung: {$comment}");
    
    echo json_encode([
        'success' => true,
        'rating' => $rating,
        'message' => "Cảm ơn bạn đã gửi đánh giá {$rating} sao cho dự án!"
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi lưu đánh giá: ' . $e->getMessage()
    ]);
}
