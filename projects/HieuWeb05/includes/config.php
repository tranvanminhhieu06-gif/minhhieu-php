<?php
/**
 * HieuMini Luxury Fitness Club - Configuration & Database Connection
 * Standard: CEO Executive Edition
 */

// Bắt đầu Session an toàn
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Thiết lập múi giờ Việt Nam & Báo lỗi
date_default_timezone_set('Asia/Ho_Chi_Minh');
error_reporting(E_ALL);
ini_set('display_errors', 0); // Đảm bảo an toàn trên production

// Thiết lập mã hóa UTF-8 toàn diện cho Tiếng Việt
ini_set('default_charset', 'UTF-8');
mb_internal_encoding('UTF-8');
if (!headers_sent() && php_sapi_name() !== 'cli') {
    header('Content-Type: text/html; charset=UTF-8');
}

// Cấu hình kết nối MySQL
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_NAME', getenv('DB_NAME_WEB05') ?: (getenv('DB_NAME') ?: 'hieumini_gym'));
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
define('DB_CHARSET', 'utf8mb4');

// Cấu hình URL & Đường dẫn ứng dụng
define('SITE_NAME', 'HieuMini Luxury Fitness Club');
define('SITE_TAGLINE', 'Đẳng Cấp Thể Hình Thượng Lưu Chuẩn CEO');
define('SITE_HOTLINE', '1900 8899 - 0988 889 999');
define('SITE_EMAIL', 'vip@hieumini.com');
define('SITE_ADDRESS', 'Tòa nhà HieuMini Tower, 88 Nguyễn Huệ, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh');

// Xác định Base URL tự động theo đường dẫn thực tế của ứng dụng
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
if (preg_match('#^(.*/HieuWeb05)#i', $scriptName, $matches)) {
    $app_path = $matches[1];
} else {
    $scriptDir = dirname($scriptName);
    $cleanDir = preg_replace('#/(admin|api|includes)(/.*)?$#i', '', $scriptDir);
    $app_path = rtrim($cleanDir, '/');
}
define('BASE_URL', $protocol . $host . $app_path);

// Kết nối Cơ sở dữ liệu bằng PDO
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    die("Lỗi kết nối Cơ sở dữ liệu: " . $e->getMessage());
}

// Khởi tạo Giỏ hàng trong Session nếu chưa có
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// ==================== CÁC HÀM TIỆN ÍCH (HELPER FUNCTIONS) ====================

/**
 * Định dạng tiền tệ VNĐ chuẩn CEO
 */
function format_currency($amount) {
    return number_format($amount, 0, ',', '.') . ' ₫';
}

/**
 * Tính phần trăm giảm giá
 */
function get_discount_percent($price, $original_price) {
    if ($original_price && $original_price > $price) {
        return round((($original_price - $price) / $original_price) * 100);
    }
    return 0;
}

/**
 * Lọc và làm sạch dữ liệu đầu vào (Anti XSS)
 */
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Lấy tổng số lượng sản phẩm trong giỏ hàng
 */
function get_cart_count() {
    $count = 0;
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $count += (int)$item['quantity'];
        }
    }
    return $count;
}

/**
 * Lấy tổng tiền tạm tính trong giỏ hàng
 */
function get_cart_subtotal() {
    $subtotal = 0;
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $subtotal += (float)$item['price'] * (int)$item['quantity'];
        }
    }
    return $subtotal;
}

/**
 * Lấy số tiền giảm giá theo voucher
 */
function get_cart_discount() {
    $subtotal = get_cart_subtotal();
    $discount = 0;
    if (isset($_SESSION['applied_coupon'])) {
        $coupon = strtoupper($_SESSION['applied_coupon']);
        if ($coupon === 'CEOFIT20') {
            $discount = $subtotal * 0.20; // Giảm 20%
        } elseif ($coupon === 'HIEUMINI10') {
            $discount = $subtotal * 0.10; // Giảm 10%
        } elseif ($coupon === 'VIPMEMBER') {
            $discount = $subtotal * 0.15; // Giảm 15%
        }
    }
    return $discount;
}

/**
 * Lấy tổng tiền thanh toán cuối cùng
 */
function get_cart_total() {
    $subtotal = get_cart_subtotal();
    $discount = get_cart_discount();
    $total = $subtotal - $discount;
    return $total > 0 ? $total : 0;
}

/**
 * Kiểm tra trạng thái đăng nhập Quản trị viên
 */
function is_admin_logged_in() {
    return isset($_SESSION['admin_user']) && $_SESSION['admin_user']['role'] === 'admin';
}

/**
 * Thông báo Flash Toast
 */
function set_flash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, // success, error, warning, info
        'message' => $message
    ];
}

function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
