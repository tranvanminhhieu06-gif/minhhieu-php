<?php
/**
 * HIEU CEO - Initialize TiDB Cloud Databases & Tables for All 5 Sub-Projects
 */

$host = 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com';
$port = '4000';
$user = 'VacZBGv6cSdbAZA.root';
$pass = 'mQTDiHQ2bnMq38y0';

echo "=========================================================\n";
echo "  🚀 INITIALIZING FULL MULTI-PROJECT TIDB CLUSTER        \n";
echo "=========================================================\n\n";

try {
    echo "[1/6] Connecting to TiDB Cloud ({$host}:{$port})... ";
    $caPath = file_exists('C:/xampp/apache/bin/curl-ca-bundle.crt') ? 'C:/xampp/apache/bin/curl-ca-bundle.crt' : (file_exists('/etc/ssl/certs/ca-certificates.crt') ? '/etc/ssl/certs/ca-certificates.crt' : null);
    
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
        PDO::ATTR_TIMEOUT => 30
    ];
    if ($caPath) {
        $options[PDO::MYSQL_ATTR_SSL_CA] = $caPath;
    }

    $pdo = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $user, $pass, $options);
    echo "SUCCESS!\n";

    // ---------------------------------------------------------
    // 0. Main CEO Portal Database (hieu_ceo_db)
    // ---------------------------------------------------------
    echo "[2/6] Setting up `hieu_ceo_db` (Main Theme Hub & User Portal)... ";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `hieu_ceo_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    $pdo->exec("USE `hieu_ceo_db`;");
    $schemaSql = file_get_contents(__DIR__ . '/schema.sql');
    $pdo->exec($schemaSql);
    $seedSql = file_get_contents(__DIR__ . '/seed.sql');
    $pdo->exec($seedSql);
    echo "SUCCESS!\n";

    // ---------------------------------------------------------
    // 1. Project 1: HieuWeb01 (Fashion Studio) -> hieumini_db
    // ---------------------------------------------------------
    echo "[3/6] Setting up `hieumini_db` (HieuWeb01 - Fashion Studio)... ";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `hieumini_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    $pdo->exec("USE `hieumini_db`;");
    $web01Sql = file_get_contents(__DIR__ . '/../projects/HieuWeb01/database/hieumini_db.sql');
    $pdo->exec($web01Sql);
    
    // Seed HieuWeb01 products
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
    echo "SUCCESS!\n";

    // ---------------------------------------------------------
    // 2. Project 2: HieuWeb02 (Tech Store) -> hieumini_bookstore_db
    // ---------------------------------------------------------
    echo "[4/6] Setting up `hieumini_bookstore_db` (HieuWeb02 - Tech Gadgets)... ";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `hieumini_bookstore_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    $pdo->exec("USE `hieumini_bookstore_db`;");
    $web02Sql = file_get_contents(__DIR__ . '/../projects/HieuWeb02/database/hieumini_db.sql');
    $pdo->exec($web02Sql);
    echo "SUCCESS!\n";

    // ---------------------------------------------------------
    // 3. Project 3: HieuWeb03 (Stationery) -> hieumini_furniture_db
    // ---------------------------------------------------------
    echo "[5/6] Setting up `hieumini_furniture_db` (HieuWeb03 - Stationery & Lifestyle)... ";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `hieumini_furniture_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    $pdo->exec("USE `hieumini_furniture_db`;");
    $web03Sql = file_get_contents(__DIR__ . '/../projects/HieuWeb03/database.sql');
    $pdo->exec($web03Sql);
    echo "SUCCESS!\n";

    // ---------------------------------------------------------
    // 4. Project 4: DatCyber (Smart Appliances) -> hieumini_appliances_db / datcyber_appliances_db
    // ---------------------------------------------------------
    echo "[6/6] Setting up `datcyber_appliances_db` (DatCyber - Smart Appliances)... ";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `datcyber_appliances_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    $pdo->exec("USE `datcyber_appliances_db`;");
    $web04Sql = file_get_contents(__DIR__ . '/../projects/DatCyber/database.sql');
    $pdo->exec($web04Sql);
    echo "SUCCESS!\n";

    // ---------------------------------------------------------
    // 5. Project 5: HieuWeb05 (Luxury Fitness) -> hieumini_gym_db
    // ---------------------------------------------------------
    echo "[*] Setting up `hieumini_gym_db` (HieuWeb05 - Luxury Fitness Club)... ";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `hieumini_gym_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    $pdo->exec("USE `hieumini_gym_db`;");
    $web05Sql = file_get_contents(__DIR__ . '/../projects/HieuWeb05/database.sql');
    $pdo->exec($web05Sql);
    echo "SUCCESS!\n\n";

    echo "=========================================================\n";
    echo "  🎉 ALL 6 TIDB DATABASES SUCCESSFULLY INITIALIZED! 🎉   \n";
    echo "=========================================================\n";

} catch (Exception $e) {
    echo "\n❌ Database Initialization Failed: " . $e->getMessage() . "\n";
    exit(1);
}
