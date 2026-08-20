<?php
/**
 * HIEU CEO - Themes Management REST API
 */

require_once __DIR__ . '/../config/helper.php';

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$db = getDb();

if ($method === 'GET') {
    $cat = sanitize($_GET['category'] ?? null);
    $search = sanitize($_GET['search'] ?? null);
    $themes = getAllThemes($cat, $search);
    jsonResponse(true, $themes, 'Lấy danh sách giao diện thành công.');
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $action = sanitize($input['action'] ?? '');
    $themeId = (int)($input['theme_id'] ?? 0);

    if (!$themeId) {
        jsonResponse(false, null, 'ID giao diện không hợp lệ.', 400);
    }

    if ($action === 'activate') {
        try {
            $db->beginTransaction();

            // Set all themes to ready if currently active
            $db->exec("UPDATE `themes` SET `status` = 'ready' WHERE `status` = 'active'");

            // Set target theme to active
            $stmt = $db->prepare("UPDATE `themes` SET `status` = 'active' WHERE `id` = :id");
            $stmt->execute([':id' => $themeId]);

            // Update system settings
            updateSystemSetting('active_theme_id', (string)$themeId);

            // Audit Log
            $theme = getThemeById($themeId);
            $themeName = $theme['name'] ?? "Theme #{$themeId}";
            logSystemAction($_SESSION['user_id'] ?? 1, 'THEME_ACTIVATE', "Kích hoạt giao diện chính: {$themeName}");

            $db->commit();
            jsonResponse(true, ['active_theme_id' => $themeId], "Đã kích hoạt giao diện {$themeName} thành công!");
        } catch (Exception $e) {
            $db->rollBack();
            jsonResponse(false, null, 'Lỗi khi kích hoạt: ' . $e->getMessage(), 500);
        }
    }

    if ($action === 'duplicate') {
        try {
            $theme = getThemeById($themeId);
            if (!$theme) {
                jsonResponse(false, null, 'Không tìm thấy giao diện gốc.', 404);
            }

            $newSlug = $theme['slug'] . '-clone-' . substr(uniqid(), -4);
            $newName = $theme['name'] . ' (Bản Sao)';
            $newCode = $theme['code_name'] . '_COPY';

            $stmt = $db->prepare("INSERT INTO `themes` 
                (`category_id`, `name`, `slug`, `code_name`, `tagline`, `description`, `thumbnail`, `preview_url`, `folder_path`, `version`, `author`, `status`, `primary_color`, `secondary_color`, `accent_color`, `bg_color`, `font_family`, `layout_type`, `custom_css`, `custom_js`)
                VALUES
                (:cat, :name, :slug, :code, :tag, :desc, :thumb, :preview, :folder, :ver, :auth, 'ready', :c1, :c2, :c3, :bg, :font, :layout, :css, :js)");

            $stmt->execute([
                ':cat' => $theme['category_id'],
                ':name' => $newName,
                ':slug' => $newSlug,
                ':code' => $newCode,
                ':tag' => $theme['tagline'],
                ':desc' => $theme['description'],
                ':thumb' => $theme['thumbnail'],
                ':preview' => $theme['preview_url'],
                ':folder' => $theme['folder_path'],
                ':ver' => $theme['version'],
                ':auth' => $theme['author'],
                ':c1' => $theme['primary_color'],
                ':c2' => $theme['secondary_color'],
                ':c3' => $theme['accent_color'],
                ':bg' => $theme['bg_color'],
                ':font' => $theme['font_family'],
                ':layout' => $theme['layout_type'],
                ':css' => $theme['custom_css'],
                ':js' => $theme['custom_js']
            ]);

            $newId = $db->lastInsertId();

            // Clone sections
            $sections = getThemeSections($themeId);
            $secStmt = $db->prepare("INSERT INTO `theme_sections` (`theme_id`, `section_key`, `section_name`, `is_enabled`, `sort_order`, `config_json`) VALUES (:tid, :key, :name, :enb, :sort, :cfg)");
            foreach ($sections as $s) {
                $secStmt->execute([
                    ':tid' => $newId,
                    ':key' => $s['section_key'],
                    ':name' => $s['section_name'],
                    ':enb' => $s['is_enabled'],
                    ':sort' => $s['sort_order'],
                    ':cfg' => $s['config_json']
                ]);
            }

            logSystemAction($_SESSION['user_id'] ?? 1, 'THEME_DUPLICATE', "Tạo bản sao giao diện: {$newName}");
            jsonResponse(true, ['new_theme_id' => $newId], "Đã nhân bản giao diện thành công!");
        } catch (Exception $e) {
            jsonResponse(false, null, 'Lỗi khi nhân bản: ' . $e->getMessage(), 500);
        }
    }

    if ($action === 'delete') {
        try {
            $theme = getThemeById($themeId);
            if (!$theme) {
                jsonResponse(false, null, 'Không tìm thấy giao diện.', 404);
            }
            if ($theme['status'] === 'active') {
                jsonResponse(false, null, 'Không thể xóa giao diện đang được kích hoạt vận hành.', 400);
            }

            $stmt = $db->prepare("DELETE FROM `themes` WHERE `id` = :id");
            $stmt->execute([':id' => $themeId]);

            logSystemAction($_SESSION['user_id'] ?? 1, 'THEME_DELETE', "Xóa giao diện: {$theme['name']}");
            jsonResponse(true, null, "Đã xóa giao diện {$theme['name']} thành công!");
        } catch (Exception $e) {
            jsonResponse(false, null, 'Lỗi khi xóa: ' . $e->getMessage(), 500);
        }
    }

    jsonResponse(false, null, 'Thao tác không hỗ trợ.', 400);
}
