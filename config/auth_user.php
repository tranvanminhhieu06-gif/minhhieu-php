<?php
/**
 * ====================================================================
 * HIEU CEO - USER & CLIENT ACCESS GUARD (QUYỀN TRUY CẬP NGƯỜI DÙNG)
 * ====================================================================
 * Tệp này chuyên biệt quản lý và kiểm soát toàn bộ quyền truy cập dành
 * cho Người Dùng, Khách Hàng, Độc Giả và Thành Viên Trải Nghiệm.
 * Cho phép xem website, lưu danh sách giao diện yêu thích, tùy biến nháp,
 * gửi đánh giá và quản lý tài khoản cá nhân.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/helper.php';

/**
 * Khởi tạo giỏ yêu thích & lưu vết người dùng nếu chưa có
 */
if (!isset($_SESSION['user_favorites'])) {
    $_SESSION['user_favorites'] = [];
}

/**
 * Kiểm tra xem có người dùng (User hoặc Khách đã đăng nhập) hay không
 */
function isUserLoggedIn(): bool {
    return !empty($_SESSION['user_id']);
}

/**
 * Lấy thông tin hồ sơ của Người Dùng / Khách Hàng hiện tại
 */
function getUserProfile(): ?array {
    if (!isUserLoggedIn()) {
        return null;
    }
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['user_name'] ?? 'Khách Trải Nghiệm',
        'email' => $_SESSION['user_email'] ?? '',
        'full_name' => $_SESSION['user_fullname'] ?? 'Khách Thăm Quan',
        'role' => $_SESSION['user_role'] ?? 'customer',
        'title' => $_SESSION['user_title'] ?? 'VIP Member',
        'avatar' => $_SESSION['user_avatar'] ?? 'assets/images/user-avatar.png',
        'favorites' => $_SESSION['user_favorites'] ?? []
    ];
}

/**
 * Bắt buộc người dùng phải đăng nhập tài khoản Thành Viên trước khi thực hiện
 * các hành động như: Đánh giá sao, Lưu giao diện yêu thích, Tải bản thiết kế
 * 
 * @param string $loginRedirect Trang chuyển hướng khi chưa đăng nhập
 */
function requireUserAuth(string $loginRedirect = 'user-login.php'): void {
    if (!isUserLoggedIn()) {
        $_SESSION['flash_warning'] = 'Vui lòng đăng nhập tài khoản Thành Viên để sử dụng tính năng này.';
        header("Location: {$loginRedirect}");
        exit;
    }
}

/**
 * Thêm hoặc gỡ một giao diện khỏi danh sách yêu thích của người dùng
 */
function toggleUserFavoriteTheme(int $themeId): bool {
    if (!isset($_SESSION['user_favorites'])) {
        $_SESSION['user_favorites'] = [];
    }
    $key = array_search($themeId, $_SESSION['user_favorites'], true);
    if ($key !== false) {
        unset($_SESSION['user_favorites'][$key]);
        $_SESSION['user_favorites'] = array_values($_SESSION['user_favorites']);
        return false; // Removed
    } else {
        $_SESSION['user_favorites'][] = $themeId;
        return true; // Added
    }
}

/**
 * Kiểm tra xem giao diện có nằm trong danh sách yêu thích của người dùng không
 */
function isThemeFavoritedByUser(int $themeId): bool {
    return in_array($themeId, $_SESSION['user_favorites'] ?? [], true);
}

/**
 * Ghi nhận nhật ký tương tác của người dùng
 */
function logUserAction(string $action, string $details): void {
    $userId = $_SESSION['user_id'] ?? null;
    try {
        $db = getDb();
        $stmt = $db->prepare("INSERT INTO `system_logs` (`user_id`, `action`, `details`, `ip_address`, `user_agent`, `created_at`) VALUES (:user_id, :action, :details, :ip, :ua, NOW())");
        $stmt->execute([
            ':user_id' => $userId,
            ':action' => 'USER_' . $action,
            ':details' => $details,
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            ':ua' => substr($_SERVER['HTTP_USER_AGENT'] ?? 'User Browser', 0, 255)
        ]);
    } catch (Exception $e) {
        // Silently continue
    }
}
