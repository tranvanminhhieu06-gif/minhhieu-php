<?php
$projects = ['HieuWeb01', 'HieuWeb02', 'HieuWeb03', 'DatCyber', 'HieuWeb05'];

foreach ($projects as $proj) {
    echo "=== PROJECT: $proj ===\n";
    $dir = __DIR__ . "/../projects/$proj/assets/images";
    if (!is_dir($dir)) {
        echo "No assets/images directory!\n\n";
        continue;
    }
    
    $files = [];
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($it as $file) {
        if ($file->isFile() && in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp', 'svg'])) {
            $files[] = [
                'path' => substr($file->getPathname(), strlen(realpath(__DIR__ . "/../projects/$proj")) + 1),
                'size' => $file->getSize()
            ];
        }
    }
    echo "Total images: " . count($files) . "\n";
    foreach (array_slice($files, 0, 10) as $f) {
        echo "  - " . $f['path'] . " (" . round($f['size'] / 1024, 1) . " KB)\n";
    }
    if (count($files) > 10) {
        echo "  ... and " . (count($files) - 10) . " more.\n";
    }
    echo "\n";
}
