<?php
/**
 * HIEU CEO - Add New Theme
 */

require_once __DIR__ . '/../config/auth_admin.php';

$currentUser = getAdminUser();

$categories = getAllCategories();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryId = (int)($_POST['category_id'] ?? 1);
    $name = sanitize($_POST['name'] ?? '');
    $codeName = strtoupper(sanitize($_POST['code_name'] ?? ''));
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    $tagline = sanitize($_POST['tagline'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $previewUrl = sanitize($_POST['preview_url'] ?? 'index.php');
    $folderPath = sanitize($_POST['folder_path'] ?? 'HieuCustom');
    $version = sanitize($_POST['version'] ?? '1.0.0');
    $author = sanitize($_POST['author'] ?? 'HIEU CEO Studio');
    $primaryColor = sanitize($_POST['primary_color'] ?? '#ffb8e7');
    $secondaryColor = sanitize($_POST['secondary_color'] ?? '#89f5ff');
    $accentColor = sanitize($_POST['accent_color'] ?? '#89f5ff');
    $bgColor = sanitize($_POST['bg_color'] ?? '#101010');
    $fontFamily = sanitize($_POST['font_family'] ?? 'Outfit');
    $customCss = $_POST['custom_css'] ?? '';

    if (empty($name) || empty($codeName)) {
        $error = 'Vui lòng nhập tên giao diện và mã code định danh.';
    } else {
        try {
            $db = getDb();
            // Check unique slug/code
            $chk = $db->prepare("SELECT id FROM `themes` WHERE `slug` = :s OR `code_name` = :c LIMIT 1");
            $chk->execute([':s' => $slug, ':c' => $codeName]);
            if ($chk->fetch()) {
                $slug .= '-' . substr(uniqid(), -3);
            }

            $stmt = $db->prepare("INSERT INTO `themes` 
                (`category_id`, `name`, `slug`, `code_name`, `tagline`, `description`, `thumbnail`, `preview_url`, `folder_path`, `version`, `author`, `status`, `primary_color`, `secondary_color`, `accent_color`, `bg_color`, `font_family`, `custom_css`)
                VALUES 
                (:cat, :name, :slug, :code, :tag, :desc, 'assets/images/themes/custom-preview.png', :preview, :folder, :ver, :auth, 'ready', :c1, :c2, :c3, :bg, :font, :css)");

            $stmt->execute([
                ':cat' => $categoryId,
                ':name' => $name,
                ':slug' => $slug,
                ':code' => $codeName,
                ':tag' => $tagline,
                ':desc' => $description,
                ':preview' => $previewUrl,
                ':folder' => $folderPath,
                ':ver' => $version,
                ':auth' => $author,
                ':c1' => $primaryColor,
                ':c2' => $secondaryColor,
                ':c3' => $accentColor,
                ':bg' => $bgColor,
                ':font' => $fontFamily,
                ':css' => $customCss
            ]);

            $newId = $db->lastInsertId();
            logSystemAction($_SESSION['user_id'] ?? 1, 'THEME_CREATE', "Tạo mới giao diện: {$name} ({$codeName})");
            setFlash('success', "Đã tạo mới giao diện {$name} thành công!");
            header('Location: themes.php');
            exit;
        } catch (Exception $e) {
            $error = 'Lỗi lưu giao diện: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thêm Giao Diện Mới - HIEU CEO</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/ceo-core.css">
  <link rel="stylesheet" href="../assets/css/animations.css">
  <style>
    .admin-layout { display: grid; grid-template-columns: 260px 1fr; min-height: 100vh; }
    .admin-sidebar { background: #0a0a0a; border-right: 1px solid var(--border-glass); padding: 24px 18px; display: flex; flex-direction: column; }
    .sidebar-menu { list-style: none; margin-top: 30px; display: flex; flex-direction: column; gap: 6px; flex: 1; }
    .sidebar-menu a { display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: var(--text-secondary); text-decoration: none; border-radius: var(--radius-md); font-weight: 600; font-size: 0.92rem; }
    .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(255, 184, 231, 0.15); color: #ffd4f0; border-left: 3px solid #ffb8e7; }
    .admin-main { padding: 30px 40px; overflow-y: auto; }
  </style>
</head>
<body>
  <div class="ceo-mesh-bg"></div>
  <div class="admin-layout">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
      <a href="../index.php" class="ceo-logo" style="margin-bottom:10px;">
        <div class="logo-icon"><i class="fa-solid fa-crown"></i></div>
        <span>HIEU<span class="text-gold-gradient">.CEO</span></span>
      </a>
      <ul class="sidebar-menu">
        <li><a href="index.php"><i class="fa-solid fa-chart-pie"></i> Bảng Điều Khiển CEO</a></li>
        <li><a href="themes.php"><i class="fa-solid fa-layer-group"></i> Quản Lý Giao Diện</a></li>
        <li><a href="theme-add.php" class="active"><i class="fa-solid fa-plus-circle"></i> Thêm Giao Diện Mới</a></li>
        <li><a href="components.php"><i class="fa-solid fa-cube"></i> Thư Viện UI Kit</a></li>
        <li><a href="analytics.php"><i class="fa-solid fa-arrow-trend-up"></i> Phân Tích & A/B Test</a></li>
        <li><a href="users.php"><i class="fa-solid fa-user-shield"></i> Phân Quyền Ban ĐH</a></li>
        <li><a href="logs.php"><i class="fa-solid fa-clock-rotate-left"></i> Nhật Ký Hoạt Động</a></li>
        <li><a href="settings.php"><i class="fa-solid fa-sliders"></i> Cài Đặt Hệ Thống</a></li>
      </ul>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
      <div style="margin-bottom:28px;">
        <a href="themes.php" style="color:var(--text-accent);text-decoration:none;font-size:0.85rem;margin-bottom:8px;display:inline-block;">
          <i class="fa-solid fa-arrow-left mr-1"></i> Quay lại danh sách
        </a>
        <h1 style="font-size:1.8rem;font-weight:800;">Tạo Mới Giao Diện Website</h1>
      </div>

      <?php if (!empty($error)): ?>
        <div style="background:rgba(244,63,94,0.15);border:1px solid rgba(244,63,94,0.35);color:#fda4af;padding:12px 16px;border-radius:var(--radius-md);margin-bottom:20px;">
          <?= e($error) ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="theme-add.php" class="glass-panel" style="padding:32px;">
        <div class="ceo-grid-2">
          <div class="form-group">
            <label class="form-label">Tên Giao Diện (Theme Name): *</label>
            <input type="text" name="name" required class="glass-input" placeholder="VD: Hieu Diamond Fintech Pro">
          </div>

          <div class="form-group">
            <label class="form-label">Mã Định Danh (Code Name): *</label>
            <input type="text" name="code_name" required class="glass-input" placeholder="VD: HIEU_WEB_08">
          </div>
        </div>

        <div class="ceo-grid-2">
          <div class="form-group">
            <label class="form-label">Danh Mục Phân Loại:</label>
            <select name="category_id" class="glass-input">
              <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Khẩu Hiệu Ngắn (Tagline):</label>
            <input type="text" name="tagline" class="glass-input" placeholder="VD: Đỉnh cao công nghệ tài chính ngân hàng số">
          </div>
        </div>

        <div class="ceo-grid-2">
          <div class="form-group">
            <label class="form-label">Đường Dẫn Xem Thử (Preview URL): *</label>
            <input type="text" name="preview_url" required class="glass-input" value="index.php" placeholder="VD: HieuWeb01/index.php">
          </div>

          <div class="form-group">
            <label class="form-label">Thư Mục Mã Nguồn (Folder Path):</label>
            <input type="text" name="folder_path" class="glass-input" value="HieuCustom">
          </div>
        </div>

        <div class="ceo-grid-4">
          <div class="form-group">
            <label class="form-label">Màu Chủ Đạo:</label>
            <input type="color" name="primary_color" value="#ffb8e7" class="glass-input" style="height:44px;padding:4px;">
          </div>
          <div class="form-group">
            <label class="form-label">Màu Phụ:</label>
            <input type="color" name="secondary_color" value="#89f5ff" class="glass-input" style="height:44px;padding:4px;">
          </div>
          <div class="form-group">
            <label class="form-label">Màu Nhấn:</label>
            <input type="color" name="accent_color" value="#89f5ff" class="glass-input" style="height:44px;padding:4px;">
          </div>
          <div class="form-group">
            <label class="form-label">Phông Chữ:</label>
            <select name="font_family" class="glass-input">
              <option value="Outfit">Outfit</option>
              <option value="Plus Jakarta Sans">Plus Jakarta Sans</option>
              <option value="Montserrat">Montserrat</option>
              <option value="Cinzel">Cinzel</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Mô Tả Chi Tiết Giao Diện:</label>
          <textarea name="description" rows="3" class="glass-input" placeholder="Mô tả các tính năng cốt lõi, công nghệ và đối tượng khách hàng phù hợp..."></textarea>
        </div>

        <div class="form-group">
          <label class="form-label">Custom CSS Tùy Biến:</label>
          <textarea name="custom_css" rows="3" class="glass-input" style="font-family:var(--font-mono);font-size:0.85rem;" placeholder="/* Thêm các quy tắc CSS riêng */"></textarea>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:14px;margin-top:20px;">
          <a href="themes.php" class="btn-ceo-secondary">Hủy Bỏ</a>
          <button type="submit" class="btn-ceo-primary btn-ripple">
            <i class="fa-solid fa-plus mr-1"></i> Lưu & Khởi Tạo Giao Diện
          </button>
        </div>
      </form>
    </main>
  </div>

  <script src="../assets/js/ceo-app.js"></script>
</body>
</html>
