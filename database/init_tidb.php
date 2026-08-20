<?php
/**
 * HIEU CEO - Initialize TiDB Cloud Database & Tables
 */

$host = 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com';
$port = '4000';
$user = 'VacZBGv6cSdbAZA.root';
$pass = 'mQTDiHQ2bnMq38y0';
$dbName = 'hieu_ceo_db';

echo "=========================================================\n";
echo "  🚀 INITIALIZING TIDB CLUSTER FOR RENDER DEPLOYMENT    \n";
echo "=========================================================\n\n";

try {
    echo "[1/4] Connecting to TiDB Cloud ({$host}:{$port})... ";
    $caPath = file_exists('C:/xampp/apache/bin/curl-ca-bundle.crt') ? 'C:/xampp/apache/bin/curl-ca-bundle.crt' : (file_exists('/etc/ssl/certs/ca-certificates.crt') ? '/etc/ssl/certs/ca-certificates.crt' : null);
    
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
        PDO::ATTR_TIMEOUT => 15
    ];
    if ($caPath) {
        $options[PDO::MYSQL_ATTR_SSL_CA] = $caPath;
    }

    $pdo = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $user, $pass, $options);
    echo "SUCCESS!\n";

    echo "[2/4] Creating database `{$dbName}` if not exists... ";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    $pdo->exec("USE `{$dbName}`;");
    echo "SUCCESS!\n";

    echo "[3/4] Executing schema.sql... ";
    $schemaSql = file_get_contents(__DIR__ . '/schema.sql');
    $pdo->exec($schemaSql);
    echo "SUCCESS!\n";

    echo "[4/4] Executing seed.sql... ";
    $seedSql = file_get_contents(__DIR__ . '/seed.sql');
    $pdo->exec($seedSql);
    echo "SUCCESS!\n\n";

    // Verify
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "=========================================================\n";
    echo "  🎉 TIDB DATABASE INITIALIZED WITH " . count($tables) . " TABLES! 🎉\n";
    echo "=========================================================\n";
    foreach ($tables as $tbl) {
        $count = $pdo->query("SELECT COUNT(*) FROM `{$tbl}`")->fetchColumn();
        echo "  - Table `{$tbl}`: {$count} records\n";
    }

} catch (Exception $e) {
    echo "FAILED!\n";
    echo "[ERROR] " . $e->getMessage() . "\n";
}
