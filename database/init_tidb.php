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

    echo "[4/5] Executing seed.sql... ";
    $seedSql = file_get_contents(__DIR__ . '/seed.sql');
    $pdo->exec($seedSql);
    echo "SUCCESS!\n";

    echo "[5/5] Executing projects/HieuWeb01/database/hieumini_db.sql... ";
    // Ensure compatibility with existing users table
    $pdo->exec("ALTER TABLE `users` MODIFY COLUMN `username` VARCHAR(50) NULL;");
    $pdo->exec("ALTER TABLE `users` MODIFY COLUMN `password_hash` VARCHAR(255) NULL;");
    $pdo->exec("ALTER TABLE `users` MODIFY COLUMN `role` VARCHAR(50) DEFAULT 'viewer';");
    $pdo->exec("ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `password` VARCHAR(255) NULL;");
    $pdo->exec("ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `phone` VARCHAR(20) NULL;");
    $pdo->exec("ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `address` TEXT NULL;");

    $web01Sql = file_get_contents(__DIR__ . '/../projects/HieuWeb01/database/hieumini_db.sql');
    $web01Sql = preg_replace('/CREATE DATABASE.*?;/is', '', $web01Sql);
    $web01Sql = preg_replace('/USE `hieumini_db`;/is', '', $web01Sql);
    $pdo->exec($web01Sql);

    // Run products seeder from HieuWeb01
    $initWeb01 = file_get_contents(__DIR__ . '/../projects/HieuWeb01/database/init_database.php');
    if (preg_match('/\$products\s*=\s*\[(.*?)\];\s*\/\/ Xóa sản phẩm/s', $initWeb01, $m)) {
        eval('$products = [' . $m[1] . '];');
        if (!empty($products)) {
            $pdo->exec("DELETE FROM `products`;");
            $stmt = $pdo->prepare("INSERT INTO `products` (`category_id`, `name`, `slug`, `sku`, `price`, `discount_price`, `stock`, `sizes`, `colors`, `description`, `content`, `image`, `featured`, `status`, `view_count`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?)");
            foreach ($products as $p) {
                $stmt->execute([
                    $p['category_id'], $p['name'], $p['slug'], $p['sku'], $p['price'],
                    $p['discount_price'], $p['stock'], $p['sizes'], $p['colors'],
                    $p['description'], $p['content'], $p['image'], $p['featured'], $p['view_count']
                ]);
            }
        }
    }

    // Add missing columns for HieuWeb02-05 compatibility
    $alterCols = [
        // HieuWeb03 columns
        "ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `sale_price` DECIMAL(12,2) NULL;",
        "ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `specification` TEXT NULL;",
        "ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `stock_quantity` INT DEFAULT 100;",
        "ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `is_featured` TINYINT(1) DEFAULT 0;",
        "ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `is_hot` TINYINT(1) DEFAULT 0;",
        "ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `is_new` TINYINT(1) DEFAULT 1;",
        "ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `rating` DECIMAL(3,2) DEFAULT 5.0;",
        "ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `review_count` INT DEFAULT 0;",
        // HieuWeb04 columns
        "ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `old_price` DECIMAL(12,0) NULL;",
        "ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `short_description` TEXT NULL;",
        "ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `specs` TEXT NULL;",
        "ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `is_best_seller` TINYINT(1) DEFAULT 0;",
        "ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `is_flash_sale` TINYINT(1) DEFAULT 0;",
        "ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `discount_percent` INT DEFAULT 0;",
        // Categories extra columns (HieuWeb02/03)
        "ALTER TABLE `categories` ADD COLUMN IF NOT EXISTS `icon` VARCHAR(50) DEFAULT 'fa-tag';",
        "ALTER TABLE `categories` ADD COLUMN IF NOT EXISTS `badge` VARCHAR(50) DEFAULT 'Phổ biến';",
        // Set is_featured from existing featured column
        "UPDATE `products` SET `is_featured` = `featured` WHERE `featured` IS NOT NULL;",
        "UPDATE `products` SET `is_hot` = 1 WHERE `is_featured` = 1;",
    ];
    foreach ($alterCols as $sql) {
        try { $pdo->exec($sql); } catch (Exception $e) { /* skip if exists */ }
    }
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
