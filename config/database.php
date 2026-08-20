<?php
/**
 * HIEU CEO - Database Configuration & PDO Factory
 */

if (!defined('DB_HOST')) define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
if (!defined('DB_PORT')) define('DB_PORT', getenv('DB_PORT') ?: '3306');
if (!defined('DB_USER')) define('DB_USER', getenv('DB_USER') ?: 'root');
if (!defined('DB_PASS')) define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
if (!defined('DB_NAME')) define('DB_NAME', getenv('DB_NAME') ?: 'hieu_ceo_db');

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ];
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                // If in production/json mode vs web view
                if (str_contains($_SERVER['REQUEST_URI'] ?? '', '/api/')) {
                    header('Content-Type: application/json; charset=utf-8');
                    http_response_code(500);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Database Connection Failed: ' . $e->getMessage()
                    ]);
                    exit;
                }
                die("<div style='font-family:sans-serif;padding:30px;background:#1e1e2d;color:#ff6b6b;border-radius:12px;margin:50px auto;max-width:600px;box-shadow:0 10px 30px rgba(0,0,0,0.5);'>
                    <h2 style='margin-top:0;'>⚠️ HIEU CEO Database Error</h2>
                    <p style='color:#cbd5e1;'>Không thể kết nối đến cơ sở dữ liệu MySQL <strong>" . DB_NAME . "</strong>.</p>
                    <p style='background:#111827;padding:12px;border-radius:8px;font-family:monospace;color:#f87171;'>Chi tiết: " . htmlspecialchars($e->getMessage()) . "</p>
                    <p style='color:#94a3b8;font-size:14px;'>Hãy đảm bảo MySQL trong XAMPP đang chạy và chạy lệnh: <code>php database/init_database.php</code></p>
                </div>");
            }
        }
        return self::$instance;
    }
}
