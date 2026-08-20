<?php
/**
 * ==========================================================
 * HIEUMINI TECH STORE - CẤU HÌNH KẾT NỐI CƠ SỞ DỮ LIỆU (PDO)
 * ==========================================================
 */

define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME_WEB02') ?: (getenv('DB_NAME') ?: 'hieumini_bookstore_db'));
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
define('DB_CHARSET', 'utf8mb4');

// URL gốc của ứng dụng (Tự động nhận diện hoặc chỉ định)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
$scriptDir = isset($_SERVER['SCRIPT_NAME']) ? dirname($_SERVER['SCRIPT_NAME']) : '';
$base_url = rtrim($protocol . "://" . $host . str_replace('/admin', '', $scriptDir), '/');
define('BASE_URL', $base_url);
define('SITE_NAME', 'HieuMini - Siêu Thị Công Nghệ Đỉnh Cao');

class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
        ];

        try {
            $this->conn = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Trường hợp chưa import database hoặc MySQL chưa bật
            $this->conn = null;
            // Lưu thông báo lỗi để tiện debug
            $this->error = $e->getMessage();
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    public function getError() {
        return isset($this->error) ? $this->error : null;
    }
}

// Khởi tạo biến $pdo toàn cục
$db = Database::getInstance();
$pdo = $db->getConnection();
