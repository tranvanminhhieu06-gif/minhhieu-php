<?php
/**
 * HIEU CEO - Master Helper Functions & Security Suite
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/database.php';

function getDb(): PDO {
    return Database::getConnection();
}

// ==================== SECURITY & SANITIZATION ====================

function e(?string $string): string {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return is_string($data) ? trim(strip_tags($data)) : $data;
}

function generateCsrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(?string $token): bool {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

// ==================== AUTHENTICATION & RBAC ====================

function isLoggedIn(): bool {
    return !empty($_SESSION['user_id']) && !empty($_SESSION['user_role']);
}

function getCurrentUser(): ?array {
    if (!isLoggedIn()) {
        return null;
    }
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['user_name'] ?? '',
        'email' => $_SESSION['user_email'] ?? '',
        'full_name' => $_SESSION['user_fullname'] ?? '',
        'role' => $_SESSION['user_role'] ?? 'viewer',
        'title' => $_SESSION['user_title'] ?? 'Executive Officer',
        'avatar' => $_SESSION['user_avatar'] ?? 'assets/images/ceo-avatar.png'
    ];
}

function requireLogin(string $redirect = 'login.php'): void {
    if (!isLoggedIn()) {
        $_SESSION['flash_error'] = 'Vui lòng đăng nhập quyền điều hành để truy cập khu vực này.';
        header("Location: {$redirect}");
        exit;
    }
}

function requireRole(array $allowedRoles = ['ceo', 'cdo']): void {
    requireLogin();
    $currentRole = $_SESSION['user_role'] ?? 'viewer';
    if (!in_array($currentRole, $allowedRoles, true)) {
        http_response_code(403);
        die("<div style='font-family:sans-serif;padding:40px;background:#101010;color:#f87171;text-align:center;'>
            <h2>🚫 Truy Cập Bị Từ Chối (403 Forbidden)</h2>
            <p style='color:#a3a3a3;'>Tài khoản của bạn với vai trò <strong>" . strtoupper($currentRole) . "</strong> không có thẩm quyền thực hiện thao tác này.</p>
            <a href='index.php' style='color:#89f5ff;text-decoration:none;'>← Quay lại Trang Chủ</a>
        </div>");
    }
}

// ==================== FLASH MESSAGES & RESPONSES ====================

function setFlash(string $type, string $message): void {
    $_SESSION["flash_{$type}"] = $message;
}

function getFlash(): array {
    $flash = [];
    $types = ['success', 'error', 'warning', 'info'];
    foreach ($types as $t) {
        if (isset($_SESSION["flash_{$t}"])) {
            $flash[$t] = $_SESSION["flash_{$t}"];
            unset($_SESSION["flash_{$t}"]);
        }
    }
    return $flash;
}

function jsonResponse(bool $success, $data = null, string $message = '', int $statusCode = 200): void {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code($statusCode);
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
        'timestamp' => time()
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// ==================== SYSTEM AUDIT LOGGING ====================

function logSystemAction(?int $userId, string $actionType, string $description): void {
    try {
        $db = getDb();
        $stmt = $db->prepare("INSERT INTO `system_logs` (`user_id`, `action_type`, `description`, `ip_address`) VALUES (:uid, :act, :desc, :ip)");
        $stmt->execute([
            ':uid' => $userId,
            ':act' => $actionType,
            ':desc' => $description,
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ]);
    } catch (Exception $e) {
        // Silently skip log error to prevent breaking flow
    }
}

// ==================== THEME & SETTINGS HELPERS ====================

function getSystemSetting(string $key, string $default = ''): string {
    try {
        $db = getDb();
        $stmt = $db->prepare("SELECT `setting_value` FROM `system_settings` WHERE `setting_key` = :k LIMIT 1");
        $stmt->execute([':k' => $key]);
        $val = $stmt->fetchColumn();
        return $val !== false ? $val : $default;
    } catch (Exception $e) {
        return $default;
    }
}

function updateSystemSetting(string $key, string $value): bool {
    try {
        $db = getDb();
        $stmt = $db->prepare("INSERT INTO `system_settings` (`setting_key`, `setting_value`) VALUES (:k, :v) ON DUPLICATE KEY UPDATE `setting_value` = :v2");
        return $stmt->execute([':k' => $key, ':v' => $value, ':v2' => $value]);
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Chuẩn hóa đường dẫn truy cập Live của Theme
 */
function getThemeLiveUrl(array|string|null $themeOrUrl): string {
    if (!$themeOrUrl) return 'live-view.php';
    $url = is_array($themeOrUrl) ? ($themeOrUrl['preview_url'] ?? '') : $themeOrUrl;
    if (empty($url)) {
        if (is_array($themeOrUrl) && !empty($themeOrUrl['folder_path'])) {
            $folder = $themeOrUrl['folder_path'];
            if (is_dir(__DIR__ . '/../projects/' . $folder)) {
                return "projects/{$folder}/index.php";
            }
        }
        return 'live-view.php';
    }
    // If it's already full URL or starts with projects/ or special demo query
    if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://') || str_starts_with($url, 'projects/') || str_starts_with($url, 'index.php') || str_starts_with($url, 'explore.php') || str_starts_with($url, 'live-view.php')) {
        return $url;
    }
    // Check if the folder exists inside projects/
    $firstFolder = explode('/', trim($url, '/'))[0] ?? '';
    if (is_dir(__DIR__ . '/../projects/' . $firstFolder)) {
        return 'projects/' . ltrim($url, '/');
    }
    return $url;
}

function getActiveTheme(): ?array {
    try {
        $db = getDb();
        $stmt = $db->query("SELECT t.*, c.name AS category_name, c.slug AS category_slug 
                            FROM `themes` t 
                            JOIN `theme_categories` c ON t.category_id = c.id 
                            WHERE t.status = 'active' LIMIT 1");
        $theme = $stmt->fetch();
        if (!$theme) {
            // Fallback to first theme if none marked active
            $stmt = $db->query("SELECT t.*, c.name AS category_name, c.slug AS category_slug 
                                FROM `themes` t 
                                JOIN `theme_categories` c ON t.category_id = c.id 
                                ORDER BY t.id ASC LIMIT 1");
            $theme = $stmt->fetch();
        }
        if ($theme) {
            $theme['preview_url'] = getThemeLiveUrl($theme['preview_url']);
        }
        return $theme ?: null;
    } catch (Exception $e) {
        return null;
    }
}

function getThemeById(int $id): ?array {
    try {
        $db = getDb();
        $stmt = $db->prepare("SELECT t.*, c.name AS category_name, c.slug AS category_slug 
                              FROM `themes` t 
                              JOIN `theme_categories` c ON t.category_id = c.id 
                              WHERE t.id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $theme = $stmt->fetch();
        if ($theme) {
            $theme['preview_url'] = getThemeLiveUrl($theme['preview_url']);
        }
        return $theme ?: null;
    } catch (Exception $e) {
        return null;
    }
}

function getThemeBySlug(string $slug): ?array {
    try {
        $db = getDb();
        $stmt = $db->prepare("SELECT t.*, c.name AS category_name, c.slug AS category_slug 
                              FROM `themes` t 
                              JOIN `theme_categories` c ON t.category_id = c.id 
                              WHERE t.slug = :slug LIMIT 1");
        $stmt->execute([':slug' => $slug]);
        $theme = $stmt->fetch();
        if ($theme) {
            $theme['preview_url'] = getThemeLiveUrl($theme['preview_url']);
        }
        return $theme ?: null;
    } catch (Exception $e) {
        return null;
    }
}

function getAllThemes(?string $categorySlug = null, ?string $search = null): array {
    try {
        $db = getDb();
        $sql = "SELECT t.*, c.name AS category_name, c.slug AS category_slug, c.icon AS category_icon 
                FROM `themes` t 
                JOIN `theme_categories` c ON t.category_id = c.id 
                WHERE 1=1";
        $params = [];

        if (!empty($categorySlug) && $categorySlug !== 'all') {
            $sql .= " AND c.slug = :cat";
            $params[':cat'] = $categorySlug;
        }

        if (!empty($search)) {
            $sql .= " AND (t.name LIKE :s OR t.description LIKE :s2 OR t.code_name LIKE :s3)";
            $params[':s'] = "%{$search}%";
            $params[':s2'] = "%{$search}%";
            $params[':s3'] = "%{$search}%";
        }

        $sql .= " ORDER BY (t.status = 'active') DESC, t.is_featured DESC, t.downloads_count DESC, t.id ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $themes = $stmt->fetchAll();
        foreach ($themes as &$t) {
            $t['preview_url'] = getThemeLiveUrl($t['preview_url']);
        }
        return $themes;
    } catch (Exception $e) {
        return [];
    }
}

function getAllCategories(): array {
    try {
        $db = getDb();
        $stmt = $db->query("SELECT c.*, COUNT(t.id) as themes_count 
                            FROM `theme_categories` c 
                            LEFT JOIN `themes` t ON c.id = t.category_id 
                            GROUP BY c.id 
                            ORDER BY c.sort_order ASC, c.id ASC");
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

function getThemeSections(int $themeId): array {
    try {
        $db = getDb();
        $stmt = $db->prepare("SELECT * FROM `theme_sections` WHERE `theme_id` = :tid ORDER BY `sort_order` ASC");
        $stmt->execute([':tid' => $themeId]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

function getThemeTokens(int $themeId): array {
    try {
        $db = getDb();
        $stmt = $db->prepare("SELECT * FROM `theme_tokens` WHERE `theme_id` = :tid");
        $stmt->execute([':tid' => $themeId]);
        $tokens = [];
        while ($row = $stmt->fetch()) {
            $tokens[$row['token_key']] = $row['token_value'];
        }
        return $tokens;
    } catch (Exception $e) {
        return [];
    }
}

// ==================== PROJECT STORAGE & ZIP EXTRACTOR ====================

function getProjectsDirectory(): string {
    $dir = dirname(__DIR__) . '/projects';
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    return $dir;
}

function extractZipArchive(string $zipFilePath, string $destinationDir): bool {
    if (!file_exists($zipFilePath)) return false;
    if (!is_dir($destinationDir)) {
        mkdir($destinationDir, 0777, true);
    }

    // Method 1: PHP ZipArchive
    if (class_exists('ZipArchive')) {
        $zip = new ZipArchive();
        if ($zip->open($zipFilePath) === true) {
            $zip->extractTo($destinationDir);
            $zip->close();
            return true;
        }
    }

    // Method 2: PowerShell fallback on Windows
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $cmd = 'powershell -NoProfile -Command "Expand-Archive -Path ' . escapeshellarg($zipFilePath) . ' -DestinationPath ' . escapeshellarg($destinationDir) . ' -Force"';
        exec($cmd, $output, $returnCode);
        return $returnCode === 0;
    }

    return false;
}

function scanProjectsDirectory(): array {
    $projectsDir = getProjectsDirectory();
    $items = scandir($projectsDir);
    $projects = [];
    $registeredMap = [];
    try {
        $db = getDb();
        $registeredThemes = $db->query("SELECT id, name, code_name, folder_path, status, preview_url FROM `themes`")->fetchAll();
        foreach ($registeredThemes as $rt) {
            $normPath = str_replace('\\', '/', $rt['folder_path']);
            $baseName = basename($normPath);
            $registeredMap[$baseName] = $rt;
            $registeredMap[$normPath] = $rt;
        }
    } catch (Exception $e) {
        // Fallback when DB is offline
    }

    foreach ($items as $item) {
        if ($item === '.' || $item === '..' || !is_dir($projectsDir . '/' . $item)) continue;

        $projectPath = $projectsDir . '/' . $item;
        $hasIndexPhp = file_exists($projectPath . '/index.php');
        $hasIndexHtml = file_exists($projectPath . '/index.html');
        $hasReadme = file_exists($projectPath . '/README.md');

        // Calculate size & file count safely
        $fileCount = 0;
        $totalSize = 0;
        try {
            if (is_dir($projectPath) && is_readable($projectPath)) {
                $dirIt = new RecursiveDirectoryIterator(
                    $projectPath,
                    FilesystemIterator::SKIP_DOTS | FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::CURRENT_AS_FILEINFO
                );
                $iterator = new RecursiveIteratorIterator(
                    $dirIt,
                    RecursiveIteratorIterator::LEAVES_ONLY,
                    RecursiveIteratorIterator::CATCH_GET_CHILD
                );
                foreach ($iterator as $file) {
                    try {
                        if ($file->isFile()) {
                            $fileCount++;
                            $size = @$file->getSize();
                            if ($size !== false && $size > 0) {
                                $totalSize += $size;
                            }
                        }
                    } catch (Throwable $t) {
                        // Skip inaccessible file or broken symlink stat
                    }
                }
            }
        } catch (Throwable $e) {
            // Graceful fallback if directory traversal encounters an issue
        }

        $sizeFormatted = $totalSize > 1048576 
            ? round($totalSize / 1048576, 2) . ' MB' 
            : round($totalSize / 1024, 2) . ' KB';

        $isReg = isset($registeredMap[$item]) || isset($registeredMap['projects/' . $item]);
        $regInfo = $isReg ? ($registeredMap[$item] ?? $registeredMap['projects/' . $item]) : null;

        $projects[] = [
            'folder_name' => $item,
            'folder_path' => 'projects/' . $item,
            'full_path' => $projectPath,
            'has_index' => $hasIndexPhp || $hasIndexHtml,
            'entry_file' => $hasIndexPhp ? 'index.php' : ($hasIndexHtml ? 'index.html' : 'N/A'),
            'preview_url' => $hasIndexPhp ? "projects/{$item}/index.php" : ($hasIndexHtml ? "projects/{$item}/index.html" : "projects/{$item}/"),
            'has_readme' => $hasReadme,
            'file_count' => $fileCount,
            'size' => $sizeFormatted,
            'size_formatted' => $sizeFormatted,
            'is_registered' => $isReg,
            'theme_id' => $regInfo['id'] ?? null,
            'theme_name' => $regInfo['name'] ?? null,
            'theme_code' => $regInfo['code_name'] ?? null,
            'theme_status' => $regInfo['status'] ?? null
        ];
    }

    return $projects;
}

function autoRegisterProjectTheme(string $folderName, array $options = []): int {
    $db = getDb();
    $projectsDir = getProjectsDirectory();
    $projectPath = $projectsDir . '/' . $folderName;

    if (!is_dir($projectPath)) {
        throw new Exception("Thư mục dự án không tồn tại: {$folderName}");
    }

    $name = $options['name'] ?? ucwords(str_replace(['_', '-'], ' ', $folderName));
    $codeName = $options['code_name'] ?? strtoupper('HIEU_' . preg_replace('/[^A-Za-z0-9]/', '', $folderName));
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name))) . '-' . substr(uniqid(), -4);
    $categoryId = (int)($options['category_id'] ?? 1);
    $tagline = $options['tagline'] ?? "Giao diện website tải lên tự động: {$name}";
    $description = $options['description'] ?? "Bộ giao diện hoàn chỉnh được quản lý trong thư mục projects/{$folderName}";
    $primaryColor = $options['primary_color'] ?? '#ffb8e7';
    $secondaryColor = $options['secondary_color'] ?? '#89f5ff';
    $accentColor = $options['accent_color'] ?? '#89f5ff';
    $fontFamily = $options['font_family'] ?? 'Outfit';

    $hasIndexPhp = file_exists($projectPath . '/index.php');
    $previewUrl = $hasIndexPhp ? "projects/{$folderName}/index.php" : "projects/{$folderName}/index.html";

    // Read README if exists
    if (file_exists($projectPath . '/README.md')) {
        $readme = file_get_contents($projectPath . '/README.md');
        $lines = explode("\n", $readme);
        if (!empty($lines[0])) {
            $rawTitle = trim(str_replace(['#', '*', '='], '', $lines[0]));
            if (!empty($rawTitle) && empty($options['name'])) {
                $name = $rawTitle;
            }
        }
    }

    $stmt = $db->prepare("INSERT INTO `themes` 
        (`category_id`, `name`, `slug`, `code_name`, `tagline`, `description`, `thumbnail`, `preview_url`, `folder_path`, `version`, `author`, `status`, `primary_color`, `secondary_color`, `accent_color`, `bg_color`, `font_family`, `layout_type`)
        VALUES
        (:cat, :name, :slug, :code, :tag, :desc, 'assets/images/themes/custom-preview.png', :preview, :folder, '1.0.0', 'HIEU CEO Upload Hub', 'ready', :c1, :c2, :c3, '#101010', :font, 'executive_glass')");

    $stmt->execute([
        ':cat' => $categoryId,
        ':name' => $name,
        ':slug' => $slug,
        ':code' => $codeName,
        ':tag' => $tagline,
        ':desc' => $description,
        ':preview' => $previewUrl,
        ':folder' => "projects/{$folderName}",
        ':c1' => $primaryColor,
        ':c2' => $secondaryColor,
        ':c3' => $accentColor,
        ':font' => $fontFamily
    ]);

    $newId = (int)$db->lastInsertId();

    // Default Section
    $db->prepare("INSERT INTO `theme_sections` (`theme_id`, `section_key`, `section_name`, `is_enabled`, `sort_order`) VALUES (:tid, 'hero_main', 'Hero Section & Banners', 1, 1)")->execute([':tid' => $newId]);

    logSystemAction($_SESSION['user_id'] ?? 1, 'PROJECT_UPLOAD', "Tải lên và đăng ký thành công dự án: {$name} (projects/{$folderName})");

    return $newId;
}

// ==================== FORMATTERS ====================

function timeAgo(?string $datetime): string {
    if (empty($datetime)) return 'Chưa cập nhật';
    $timestamp = strtotime($datetime);
    $difference = time() - $timestamp;

    if ($difference < 60) return 'Vừa xong';
    if ($difference < 3600) return floor($difference / 60) . ' phút trước';
    if ($difference < 86400) return floor($difference / 3600) . ' giờ trước';
    if ($difference < 2592000) return floor($difference / 86400) . ' ngày trước';
    return date('d/m/Y H:i', $timestamp);
}
