<?php
/**
 * HIEU CEO - Executive Logout Handler
 */

require_once __DIR__ . '/config/helper.php';

if (isLoggedIn()) {
    logSystemAction($_SESSION['user_id'] ?? null, 'AUTH_LOGOUT', 'Đăng xuất khỏi hệ thống.');
}

$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

session_start();
setFlash('info', 'Bạn đã đăng xuất khỏi hệ thống điều hành an toàn.');
header('Location: index.php');
exit;
