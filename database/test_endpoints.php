<?php
/**
 * Test all subprojects endpoints
 */

$urls = [
    'Main Portal' => 'http://127.0.0.1:8099/index.php',
    'User Login'  => 'http://127.0.0.1:8099/user/login.php',
    'HieuWeb01'   => 'http://127.0.0.1:8099/projects/HieuWeb01/index.php',
    'HieuWeb02'   => 'http://127.0.0.1:8099/projects/HieuWeb02/index.php',
    'HieuWeb03'   => 'http://127.0.0.1:8099/projects/HieuWeb03/index.php',
    'DatCyber'    => 'http://127.0.0.1:8099/projects/DatCyber/index.php',
    'HieuWeb05'   => 'http://127.0.0.1:8099/projects/HieuWeb05/index.php',
];

echo "========================================\n";
echo "  TESTING ALL SUB-PROJECT ENDPOINTS\n";
echo "========================================\n\n";

foreach ($urls as $name => $url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $html = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $hasFatal = stripos($html, 'Fatal error') !== false || stripos($html, 'Uncaught PDOException') !== false;
    $hasWarning = stripos($html, 'Warning:') !== false;
    
    if ($code === 200 && !$hasFatal) {
        echo "✅ [$code] $name: OK (" . strlen($html) . " bytes)\n";
        if ($hasWarning) {
            echo "   ⚠️ Notice/Warning found in page output\n";
        }
    } else {
        echo "❌ [$code] $name: FAILED (Fatal Error or bad status)\n";
        if ($hasFatal) {
            preg_match('/(Fatal error.*?)(\n|<br)/is', $html, $m);
            echo "   " . ($m[1] ?? 'Fatal error details') . "\n";
        }
    }
}
