<?php
/**
 * HIEU CEO - Project Upload & Management REST API
 * Handles ZIP extraction, folder scanning, and auto theme registration
 */

require_once __DIR__ . '/../config/helper.php';

header('Content-Type: application/json; charset=utf-8');

$action = sanitize($_GET['action'] ?? ($_POST['action'] ?? 'upload_zip'));
$db = getDb();
$projectsDir = getProjectsDirectory();

// 1. Scan Projects Directory
if ($action === 'scan') {
    try {
        $projects = scanProjectsDirectory();
        jsonResponse(true, $projects, 'Quét thư mục projects/ thành công.');
    } catch (Exception $e) {
        jsonResponse(false, null, 'Lỗi khi quét thư mục: ' . $e->getMessage(), 500);
    }
}

// 2. Auto-register an existing project folder
if ($action === 'register_folder') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $folderName = sanitize($input['folder_name'] ?? '');
    $categoryId = (int)($input['category_id'] ?? 1);

    if (empty($folderName)) {
        jsonResponse(false, null, 'Tên thư mục không hợp lệ.', 400);
    }

    try {
        $themeId = autoRegisterProjectTheme($folderName, ['category_id' => $categoryId]);
        $theme = getThemeById($themeId);
        jsonResponse(true, $theme, "Đã đăng ký hiển thị dự án {$folderName} lên hệ thống!");
    } catch (Exception $e) {
        jsonResponse(false, null, 'Lỗi đăng ký: ' . $e->getMessage(), 500);
    }
}

// 3. Upload ZIP Archive
if ($action === 'upload_zip') {
    if (empty($_FILES['project_zip']['tmp_name'])) {
        jsonResponse(false, null, 'Vui lòng chọn tệp .ZIP dự án để tải lên.', 400);
    }

    $file = $_FILES['project_zip'];
    $originalName = pathinfo($file['name'], PATHINFO_FILENAME);
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if ($ext !== 'zip') {
        jsonResponse(false, null, 'Định dạng tệp không được hỗ trợ. Vui lòng tải lên tệp đuôi .ZIP.', 400);
    }

    // Custom folder name or sanitize from zip filename
    $folderName = sanitize($_POST['folder_name'] ?? $originalName);
    $folderName = preg_replace('/[^A-Za-z0-9_-]/', '', $folderName);
    if (empty($folderName)) {
        $folderName = 'Project_' . date('Ymd_His');
    }

    $targetDir = $projectsDir . '/' . $folderName;
    if (is_dir($targetDir)) {
        $folderName .= '_' . substr(uniqid(), -4);
        $targetDir = $projectsDir . '/' . $folderName;
    }

    // Extract ZIP
    $extracted = extractZipArchive($file['tmp_name'], $targetDir);

    if (!$extracted) {
        jsonResponse(false, null, 'Không thể giải nén tệp ZIP. Vui lòng kiểm tra lại file nén.', 500);
    }

    // Check if zip contains a single nested root folder
    $innerFiles = array_diff(scandir($targetDir), ['.', '..']);
    if (count($innerFiles) === 1) {
        $firstChild = $targetDir . '/' . reset($innerFiles);
        if (is_dir($firstChild)) {
            // Move contents up one level
            $nestedItems = array_diff(scandir($firstChild), ['.', '..']);
            foreach ($nestedItems as $nItem) {
                rename($firstChild . '/' . $nItem, $targetDir . '/' . $nItem);
            }
            @rmdir($firstChild);
        }
    }

    // Auto-Register into themes database
    try {
        $themeName = sanitize($_POST['theme_name'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 1);
        $primaryColor = sanitize($_POST['primary_color'] ?? '#6366f1');
        $fontFamily = sanitize($_POST['font_family'] ?? 'Outfit');

        $themeId = autoRegisterProjectTheme($folderName, [
            'name' => $themeName,
            'category_id' => $categoryId,
            'primary_color' => $primaryColor,
            'font_family' => $fontFamily
        ]);

        $theme = getThemeById($themeId);
        jsonResponse(true, [
            'theme' => $theme,
            'folder' => $folderName,
            'preview_url' => "projects/{$folderName}/index.php"
        ], "Tải lên và giải nén thành công dự án {$folderName}!");
    } catch (Exception $e) {
        jsonResponse(true, [
            'folder' => $folderName,
            'warning' => 'Đã giải nén vào projects/' . $folderName . ' nhưng chưa đăng ký DB: ' . $e->getMessage()
        ], "Đã tải lên thư mục dự án {$folderName}!");
    }
}

// 4. Delete Project Folder & Unlink DB
if ($action === 'delete_project') {
    requireRole(['ceo', 'cdo']);
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $folderName = sanitize($input['folder_name'] ?? '');

    if (empty($folderName) || $folderName === '.' || $folderName === '..') {
        jsonResponse(false, null, 'Tên thư mục không hợp lệ.', 400);
    }

    $targetDir = $projectsDir . '/' . $folderName;
    if (!is_dir($targetDir)) {
        jsonResponse(false, null, 'Thư mục không tồn tại.', 404);
    }

    // Remove DB theme record if exists
    $db->prepare("DELETE FROM `themes` WHERE `folder_path` = :p1 OR `folder_path` = :p2")->execute([
        ':p1' => $folderName,
        ':p2' => 'projects/' . $folderName
    ]);

    // Recursively delete directory
    $it = new RecursiveDirectoryIterator($targetDir, RecursiveDirectoryIterator::SKIP_DOTS);
    $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($files as $file) {
        if ($file->isDir()){
            rmdir($file->getRealPath());
        } else {
            unlink($file->getRealPath());
        }
    }
    rmdir($targetDir);

    logSystemAction($_SESSION['user_id'] ?? 1, 'PROJECT_DELETE', "Xóa toàn bộ thư mục dự án: projects/{$folderName}");
    jsonResponse(true, null, "Đã xóa thư mục dự án {$folderName} thành công!");
}

jsonResponse(false, null, 'Hành động không hợp lệ.', 400);
