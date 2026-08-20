<?php
/**
 * HIEU CEO - Aiven Cloud Database Migrator & Seeder
 */

$host = getenv('DB_HOST') ?: ($argv[1] ?? 'hieudoan-tranvanminhhieu06-342a.g.aivencloud.com');
$port = (int)(getenv('DB_PORT') ?: ($argv[2] ?? 19499));
$user = getenv('DB_USER') ?: ($argv[3] ?? 'avnadmin');
$pass = getenv('DB_PASS') ?: ($argv[4] ?? '');
$dbname = getenv('DB_NAME') ?: ($argv[5] ?? 'defaultdb');

if (empty($pass)) {
    echo "Vui long truyen mat khau: php database/cloud_seed_aiven.php [host] [port] [user] [pass] [dbname]\n";
    exit(1);
}

echo "=========================================================\n";
echo "  👑 HIEU CEO - AIVEN CLOUD DATABASE INITIALIZER 👑\n";
echo "=========================================================\n\n";

try {
    echo "[1/3] Connecting to Aiven Cloud MySQL ({$host}:{$port})... ";
    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false
    ];
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "SUCCESS!\n";
    $version = $pdo->query("SELECT VERSION()")->fetchColumn();
    echo "  - MySQL Cloud Version: {$version}\n\n";

    // 2. Import Schema
    echo "[2/3] Creating 10 core tables on Aiven Database... ";
    $schema = file_get_contents(__DIR__ . '/schema.sql');
    $schema = preg_replace('/CREATE DATABASE[^;]+;/i', '', $schema);
    $schema = preg_replace('/USE `[^`]+`;/i', '', $schema);
    $pdo->exec($schema);
    echo "SUCCESS!\n";

    // 3. Seed Master Data
    echo "[3/3] Seeding Master themes, UI components & CEO accounts... ";
    $seed = file_get_contents(__DIR__ . '/seed.sql');
    $seed = preg_replace('/USE `[^`]+`;/i', '', $seed);
    $pdo->exec($seed);

    // Update CEO admin password hash
    $pwdHash = password_hash('admin123', PASSWORD_BCRYPT);
    $pdo->prepare("UPDATE `users` SET `password_hash` = :p")->execute([':p' => $pwdHash]);
    echo "SUCCESS!\n\n";

    // Summary
    echo "=========================================================\n";
    echo "  KẾT QUẢ KHỞI TẠO AIVEN CLOUD: THÀNH CÔNG 100%\n";
    echo "=========================================================\n";
    $tables = ['users', 'theme_categories', 'themes', 'theme_sections', 'theme_tokens', 'ui_components', 'ceo_metrics', 'theme_analytics', 'system_logs', 'system_settings'];
    foreach ($tables as $tbl) {
        $count = $pdo->query("SELECT COUNT(*) FROM `{$tbl}`")->fetchColumn();
        echo "  - Table `{$tbl}`: {$count} records\n";
    }

} catch (Exception $e) {
    echo "FAILED: " . $e->getMessage() . "\n";
}
