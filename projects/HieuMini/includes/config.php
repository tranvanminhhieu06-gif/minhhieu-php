<?php
/**
 * HieuMini - Cấu hình hệ thống
 * -----------------------------------------------------------------
 * Nơi duy nhất khai báo kết nối CSDL, phiên làm việc và hằng số.
 * Mọi tệp PHP khác đều nạp tệp này đầu tiên.
 */

declare(strict_types=1);

// ---------- 1. Thông số kết nối cơ sở dữ liệu ----------
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'hieumini_market_db');
define('DB_USER', 'root');
define('DB_PASS', '');          // XAMPP mặc định để trống
define('DB_CHARSET', 'utf8mb4');

// ---------- 2. Thông tin thương hiệu ----------
define('SITE_NAME', 'HieuMini');
define('SITE_SLOGAN', 'Chợ mã nguồn website chuẩn SEO cho người Việt');

// ---------- 3. Đường dẫn gốc tự phát hiện ----------
// Ví dụ: /DoAnWebsite/projects/HieuMini
$__docRoot = str_replace('\\', '/', rtrim((string)($_SERVER['DOCUMENT_ROOT'] ?? ''), '/'));
$__baseDir = str_replace('\\', '/', dirname(__DIR__));
$__basePath = ($__docRoot !== '' && str_starts_with($__baseDir, $__docRoot))
    ? substr($__baseDir, strlen($__docRoot))
    : '';
define('BASE_PATH', rtrim($__basePath, '/'));

$__scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
define('BASE_URL', $__scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . BASE_PATH);

define('ROOT_DIR', dirname(__DIR__));
define('UPLOAD_DIR', ROOT_DIR . '/uploads');

// ---------- 4. Phiên làm việc an toàn ----------
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_name('HIEUMINI_SESS');
    session_start();
}

// ---------- 5. Múi giờ & hiển thị lỗi ----------
date_default_timezone_set('Asia/Ho_Chi_Minh');
define('DEBUG_MODE', true);   // Đặt false khi đưa lên máy chủ thật
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// ---------- 6. Kết nối PDO ----------
try {
    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', DB_HOST, DB_PORT, DB_NAME, DB_CHARSET);
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_STRINGIFY_FETCHES  => false,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    $msg = DEBUG_MODE ? htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') : '';
    exit(
        '<!doctype html><html lang="vi"><meta charset="utf-8">'
        . '<title>Lỗi kết nối cơ sở dữ liệu</title>'
        . '<body style="font-family:system-ui;background:#0B0B1A;color:#E2E8F0;padding:48px;line-height:1.6">'
        . '<h1 style="color:#A78BFA">Không kết nối được cơ sở dữ liệu</h1>'
        . '<p>Hãy kiểm tra: MySQL trong XAMPP đã bật chưa, và đã nhập tệp <code>database.sql</code> chưa.</p>'
        . ($msg !== '' ? '<pre style="background:#1E1C35;padding:16px;border-radius:12px;overflow:auto">' . $msg . '</pre>' : '')
        . '</body></html>'
    );
}

// ---------- 7. Nạp bộ hàm dùng chung ----------
require_once __DIR__ . '/functions.php';

// ---------- 8. Nạp cấu hình động từ bảng settings ----------
$SETTINGS = load_settings($pdo);
