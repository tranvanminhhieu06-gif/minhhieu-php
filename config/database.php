<?php
/**
 * HIEU CEO - Database Configuration & PDO Factory
 */

if (!defined('DB_HOST')) define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
if (!defined('DB_PORT')) define('DB_PORT', getenv('DB_PORT') ?: '3306');
if (!defined('DB_USER')) define('DB_USER', getenv('DB_USER') ?: 'root');
if (!defined('DB_PASS')) define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
if (!defined('DB_NAME')) define('DB_NAME', getenv('DB_NAME') ?: 'hieu_ceo_db');
if (!defined('DB_SSL')) define('DB_SSL', (getenv('DB_SSL') === 'true' || getenv('DB_SSL') === '1'));

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
                    PDO::ATTR_TIMEOUT => 10,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ];

                // Auto-enable SSL for Cloud MySQL providers (Aiven, TiDB, Clever Cloud)
                if (DB_SSL || str_contains(DB_HOST, 'aivencloud.com') || str_contains(DB_HOST, 'tidbcloud.com') || str_contains(DB_HOST, 'clever-cloud.com') || DB_PORT !== '3306') {
                    $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
                }

                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                // Throw exception so caller functions can catch and gracefully fallback
                throw $e;
            }
        }
        return self::$instance;
    }
}
