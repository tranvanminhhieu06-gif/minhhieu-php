<?php
/**
 * Initialize local MySQL databases
 */

$host = '127.0.0.1';
$port = '3306';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $dbs = [
        'hieu_ceo_db'            => [__DIR__ . '/schema.sql', __DIR__ . '/seed.sql'],
        'hieumini_db'            => [__DIR__ . '/../projects/HieuWeb01/database/hieumini_db.sql'],
        'hieumini_bookstore_db'  => [__DIR__ . '/../projects/HieuWeb02/database/hieumini_db.sql'],
        'hieumini_furniture_db'  => [__DIR__ . '/../projects/HieuWeb03/database.sql'],
        'hieumini_appliances_db' => [__DIR__ . '/../projects/HieuWeb04/database.sql'],
        'hieumini_gym_db'        => [__DIR__ . '/../projects/HieuWeb05/database.sql'],
    ];

    foreach ($dbs as $db => $sqlFiles) {
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
        $pdo->exec("USE `$db`;");
        foreach ($sqlFiles as $file) {
            if (file_exists($file)) {
                $sql = file_get_contents($file);
                $pdo->exec($sql);
            }
        }
        echo "Local DB `$db`: Initialized successfully!\n";
    }

} catch (Exception $e) {
    echo "Local MySQL init error: " . $e->getMessage() . "\n";
}
