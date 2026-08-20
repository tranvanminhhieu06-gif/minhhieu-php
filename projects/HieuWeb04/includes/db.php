<?php
/**
 * Database Connection using PDO
 * HieuMini Smart Home Appliances
 */

$host = getenv('DB_HOST') ?: '127.0.0.1';
$dbname = getenv('DB_NAME_WEB04') ?: (getenv('DB_NAME') ?: 'hieumini_tech_db');
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    // If database connection fails, show user-friendly message
    die('<div style="font-family:sans-serif;padding:2rem;text-align:center;color:#e11d48;">
        <h2>Lỗi kết nối cơ sở dữ liệu!</h2>
        <p>Vui lòng đảm bảo MySQL trong XAMPP đang chạy và cơ sở dữ liệu `hieumini_db` đã được khởi tạo.</p>
        <p><small>' . htmlspecialchars($e->getMessage()) . '</small></p>
    </div>');
}

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
