<?php
/**
 * HIEU CEO - Database Initializer & Migration Script
 * CLI Tool to setup and seed the hieu_ceo_db MySQL database
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = '127.0.0.1';
$port = '3306';
$username = 'root';
$password = '';
$database = 'hieu_ceo_db';

echo "\033[1;36m=========================================================\033[0m\n";
echo "\033[1;33m  👑 HIEU CEO - DATABASE INITIALIZER & SEEDER TOOL 👑   \033[0m\n";
echo "\033[1;36m=========================================================\033[0m\n\n";

try {
    // 1. Connect to MySQL server without DB selected
    echo "[1/4] Connecting to MySQL Server at {$host}:{$port}... ";
    $pdo = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    echo "\033[1;32mSUCCESS\033[0m\n";

    // 2. Execute Schema
    echo "[2/4] Executing database schema (database/schema.sql)... ";
    $schemaFile = __DIR__ . '/schema.sql';
    if (!file_exists($schemaFile)) {
        throw new Exception("Schema file not found: {$schemaFile}");
    }
    $schemaSql = file_get_contents($schemaFile);
    $pdo->exec($schemaSql);
    echo "\033[1;32mSUCCESS (10 Tables Created)\033[0m\n";

    // 3. Select database and Execute Seed
    echo "[3/4] Seeding initial master data (database/seed.sql)... ";
    $pdo->exec("USE `{$database}`");
    $seedFile = __DIR__ . '/seed.sql';
    if (!file_exists($seedFile)) {
        throw new Exception("Seed file not found: {$seedFile}");
    }
    $seedSql = file_get_contents($seedFile);
    $pdo->exec($seedSql);

    // Update with guaranteed password hash for 'admin123'
    $hashedPassword = password_hash('admin123', PASSWORD_BCRYPT);
    $updateStmt = $pdo->prepare("UPDATE `users` SET `password_hash` = :pwd");
    $updateStmt->execute([':pwd' => $hashedPassword]);
    echo "\033[1;32mSUCCESS\033[0m\n";

    // 4. Verify Tables & Count Records
    echo "[4/4] Verifying database integrity & counting records...\n";
    $tables = [
        'users' => 'Executive Users',
        'theme_categories' => 'Theme Categories',
        'themes' => 'Master Themes',
        'theme_sections' => 'Theme Sections',
        'theme_tokens' => 'Custom Tokens',
        'ui_components' => 'UI Components',
        'ceo_metrics' => 'CEO Metrics',
        'theme_analytics' => 'Analytics Data',
        'system_logs' => 'Audit Logs',
        'system_settings' => 'System Settings'
    ];

    foreach ($tables as $tbl => $name) {
        $cnt = $pdo->query("SELECT COUNT(*) FROM `{$tbl}`")->fetchColumn();
        echo "  - Table \033[1;37m`{$tbl}`\033[0m ({$name}): \033[1;32m{$cnt} records\033[0m\n";
    }

    echo "\n\033[1;32m=========================================================\033[0m\n";
    echo "\033[1;32m  DATABASE INITIALIZATION COMPLETED SUCCESSFULLY!        \033[0m\n";
    echo "\033[1;32m=========================================================\033[0m\n";
    echo "  - Database: \033[1;33m{$database}\033[0m\n";
    echo "  - Default Admin Account: \033[1;33mceo@hieu.vn\033[0m | Password: \033[1;33madmin123\033[0m\n";
    echo "  - CDO Account: \033[1;33mcdo@hieu.vn\033[0m | Password: \033[1;33madmin123\033[0m\n";
    echo "  - Dev Account: \033[1;33mdev@hieu.vn\033[0m | Password: \033[1;33madmin123\033[0m\n\n";

} catch (Exception $e) {
    echo "\033[1;31mFAILED\033[0m\n";
    echo "\033[1;31m[ERROR] " . $e->getMessage() . "\033[0m\n";
    exit(1);
}
