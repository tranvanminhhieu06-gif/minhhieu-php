<?php
/**
 * HIEU CEO - Edit Existing Theme
 */

require_once __DIR__ . '/../config/auth_admin.php';

$currentUser = getAdminUser();

$themeId = (int)($_GET['id'] ?? 0);
$theme = getThemeById($themeId);

if (!$theme) {
    header('Location: themes.php');
    exit;
}

$categories = getAllCategories();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryId = (int)($_POST['category_id'] ?? 1);
    $name = sanitize($_POST['name'] ?? '');
    $codeName = strtoupper(sanitize($_POST['code_name'] ?? ''));
    $tagline = sanitize($_POST['tagline'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $previewUrl = sanitize($_POST['preview_url'] ?? 'index.php');
    $folderPath = sanitize($_POST['folder_path'] ?? 'HieuCustom');
    $version = sanitize($_POST['version'] ?? '1.0.0');
    $author = sanitize($_POST['author'] ?? 'HIEU CEO Studio');
    $status = sanitize($_POST['status'] ?? 'ready');
    $rating = (float)($_POST['rating'] ?? 5.0);
    $primaryColor = sanitize($_POST['primary_color'] ?? '#6366f1');
    $secondaryColor = sanitize($_POST['secondary_color'] ?? '#ec4899');
    $accentColor = sanitize($_POST['accent_color'] ?? '#06b6d4');
    $bgColor = sanitize($_POST['bg_color'] ?? '#0f172a');
    $fontFamily = sanitize($_POST['font_family'] ?? 'Outfit');
    $customCss = $_POST['custom_css'] ?? '';

    if (empty($name) || empty($codeName)) {
        $error = 'Vui lòng nhập đầy đủ tên và mã định danh.';
    } else {
        try {
            $db = getDb();
            $stmt = $db->prepare("UPDATE `themes` SET
                `category_id` = :cat,
                `name` = :name,
                `code_name` = :code,
                `tagline` = :tag,
                `description` = :desc,
                `preview_url` = :preview,
                `folder_path` = :folder,
                `version` = :ver,
                `author` = :auth,
                `status` = :st,
                `rating` = :rat,
                `primary_color` = :c1,
                `secondary_color` = :c2,
                `accent_color` = :c3,
                `bg_color` = :bg,
                `font_family` = :font,
                `custom_css` = :css,
                `updated_at` = NOW()
                WHERE `id` = :id");

            $stmt->execute([
                ':cat' => $categoryId,
                ':name' => $name,
                ':code' => $codeName,
                ':tag' => $tagline,
                ':desc' => $description,
                ':preview' => $previewUrl,
                ':folder' => $folderPath,
                ':ver' => $version,
                ':auth' => $author,
                ':st' => $status,
                ':rat' => $rating,
                ':c1' => $primaryColor,
                ':c2' => $secondaryColor,
                ':c3' => $accentColor,
                ':bg' => $bgColor,
                ':font' => $fontFamily,
                ':css' => $customCss,
                ':id' => $themeId
            ]);

            logSystemAction($_SESSION['user_id'] ?? 1, 'THEME_UPDATE', "Cập nhật thông số giao diện: {$name} (ID: {$themeId})");
            setFlash('success', "Đã cập nhật giao diện {$name} thành công!");
            header('Location: themes.php');
            exit;
        } catch (Exception $e) {
            $error = 'Lỗi cập nhật: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chỉnh Sửa Giao Diện: <?= e($theme['name']) ?> - HIEU CEO</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/ceo-core.css">
  <link rel="stylesheet" href="../assets/css/animations.css">
  <style>
    .admin-layout { display: grid; grid-template-columns: 260px 1fr; min-height: 100vh; }
    .admin-sidebar { background: #090d16; border-right: 1px solid var(--border-glass); padding: 24px 18px; display: flex; flex-direction: column; }
    .sidebar-menu { list-style: none; margin-top: 30px; display: flex; flex-direction: column; gap: 6px; flex: 1; }
    .sidebar-menu a { display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: var(--text-secondary); text-decoration: none; border-radius: var(--radius-md); font-weight: 600; font-size: 0.92rem; }
    .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(99, 102, 241, 0.15); color: #818cf8; border-left: 3px solid #6366f1; }
    .admin-main { padding: 30px 40px; overflow-y: auto; }
  </style>
</head>
<body>
  <div class="ceo-mesh-bg"></div>
  <div class="admin-layout">
    <aside class="admin-sidebar">
      <a href="../index.php" class="ceo-logo" style="margin-bottom:10px;">
        <div class="logo-icon"><i class="fa-solid fa-crown"></i></div>
        <span>HIEU<span class="text-gold-gradient">.CEO</span></span>
      </a>
      <ul class="sidebar-menu">
        <li><a href="index.php"><i class="fa-solid fa-chart-pie"></i> Bảng Điều Khiển CEO</a></li>
        <li><a href="themes.php" class="active"><i class="fa-solid fa-layer-group"></i> Quản Lý Giao Diện</a></li>
        <li><a href="theme-add.php"><i class="fa-solid fa-plus-circle"></i> Thêm Giao Diện Mới</a></li>
        <li><a href="components.php"><i class="fa-solid fa-cube"></i> Thư Viện UI Kit</a></li>
        <li><a href="analytics.php"><i class="fa-solid fa-arrow-trend-up"></i> Phân Tích & A/B Test</a></li>
        <li><a href="users.php"><i class="fa-solid fa-user-shield"></i> Phân Quyền Ban ĐH</a></li>
        <li><a href="logs.php"><i class="fa-solid fa-clock-rotate-left"></i> Nhật Ký Hoạt Động</a></li>
        <li><a href="settings.php"><i class="fa-solid fa-sliders"></i> Cài Đặt Hệ Thống</a></li>
      </ul>
    </aside>

    <main class="admin-main">
      <div style="margin-bottom:28px;">
        <a href="themes.php" style="color:var(--text-accent);text-decoration:none;font-size:0.85rem;margin-bottom:8px;display:inline-block;">
          <i class="fa-solid fa-arrow-left mr-1"></i> Quay lại danh sách
        </a>
        <h1 style="font-size:1.8rem;font-weight:800;">Chỉnh Sửa Giao Diện: <?= e($theme['name']) ?></h1>
      </div>

      <?php if (!empty($error)): ?>
        <div style="background:rgba(244,63,94,0.15);border:1px solid rgba(244,63,94,0.35);color:#fda4af;padding:12px 16px;border-radius:var(--radius-md);margin-bottom:20px;">
          <?= e($error) ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="theme-edit.php?id=<?= $theme['id'] ?>" class="glass-panel" style="padding:32px;">
        <div class="ceo-grid-2">
          <div class="form-group">
            <label class="form-label">Tên Giao Diện: *</label>
            <input type="text" name="name" value="<?= e($theme['name']) ?>" required class="glass-input">
          </div>

          <div class="form-group">
            <label class="form-label">Mã Code Định Danh: *</label>
            <input type="text" name="code_name" value="<?= e($theme['code_name']) ?>" required class="glass-input">
          </div>
        </div>

        <div class="ceo-grid-3">
          <div class="form-group">
            <label class="form-label">Danh Mục:</label>
            <select name="category_id" class="glass-input">
              <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $theme['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Trạng Thái:</label>
            <select name="status" class="glass-input">
              <option value="active" <?= $theme['status'] === 'active' ? 'selected' : '' ?>>active (Đang Vận Hành)</option>
              <option value="ready" <?= $theme['status'] === 'ready' ? 'selected' : '' ?>>ready (Sẵn Sàng)</option>
              <option value="beta" <?= $theme['status'] === 'beta' ? 'selected' : '' ?>>beta (Thử Nghiệm)</option>
              <option value="archived" <?= $theme['status'] === 'archived' ? 'selected' : '' ?>>archived (Lưu Trữ)</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Đánh Giá (Rating):</label>
            <input type="number" step="0.01" min="1" max="5" name="rating" value="<?= e($theme['rating']) ?>" class="glass-input">
          </div>
        </div>

        <div class="ceo-grid-2">
          <div class="form-group">
            <label class="form-label">Preview URL:</label>
            <input type="text" name="preview_url" value="<?= e($theme['preview_url']) ?>" required class="glass-input">
          </div>

          <div class="form-group">
            <label class="form-label">Folder Path:</label>
            <input type="text" name="folder_path" value="<?= e($theme['folder_path']) ?>" class="glass-input">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Khẩu Hiệu (Tagline):</label>
          <input type="text" name="tagline" value="<?= e($theme['tagline']) ?>" class="glass-input">
        </div>

        <div class="ceo-grid-4">
          <div class="form-group">
            <label class="form-label">Màu Chủ Đạo:</label>
            <input type="color" name="primary_color" value="<?= e($theme['primary_color'] ?: '#6366f1') ?>" class="glass-input" style="height:44px;padding:4px;">
          </div>
          <div class="form-group">
            <label class="form-label">Màu Phụ:</label>
            <input type="color" name="secondary_color" value="<?= e($theme['secondary_color'] ?: '#ec4899') ?>" class="glass-input" style="height:44px;padding:4px;">
          </div>
          <div class="form-group">
            <label class="form-label">Màu Nhấn:</label>
            <input type="color" name="accent_color" value="<?= e($theme['accent_color'] ?: '#06b6d4') ?>" class="glass-input" style="height:44px;padding:4px;">
          </div>
          <div class="form-group">
            <label class="form-label">Phông Chữ:</label>
            <select name="font_family" class="glass-input">
              <option value="Outfit" <?= $theme['font_family'] === 'Outfit' ? 'selected' : '' ?>>Outfit</option>
              <option value="Plus Jakarta Sans" <?= $theme['font_family'] === 'Plus Jakarta Sans' ? 'selected' : '' ?>>Plus Jakarta Sans</option>
              <option value="Montserrat" <?= $theme['font_family'] === 'Montserrat' ? 'selected' : '' ?>>Montserrat</option>
              <option value="Cinzel" <?= $theme['font_family'] === 'Cinzel' ? 'selected' : '' ?>>Cinzel</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Mô Tả:</label>
          <textarea name="description" rows="3" class="glass-input"><?= e($theme['description']) ?></textarea>
        </div>

        <div class="form-group">
          <label class="form-label">Custom CSS:</label>
          <textarea name="custom_css" rows="4" class="glass-input" style="font-family:var(--font-mono);font-size:0.85rem;"><?= e($theme['custom_css']) ?></textarea>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:14px;margin-top:20px;">
          <a href="themes.php" class="btn-ceo-secondary">Hủy Bỏ</a>
          <button type="submit" class="btn-ceo-primary btn-ripple">
            <i class="fa-solid fa-floppy-disk mr-1"></i> Cập Nhật Thông Số
          </button>
        </div>
      </form>
    </main>
  </div>
  <script src="../assets/js/ceo-app.js"></script>
</body>
</html>
