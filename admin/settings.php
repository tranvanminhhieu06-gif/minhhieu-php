<?php
/**
 * HIEU CEO - Global Settings & System Administration
 */

require_once __DIR__ . '/../config/auth_admin.php';
requireAdminAuth(['ceo', 'cdo']);

$currentUser = getAdminUser();

$db = getDb();
$error = '';
$flash = getFlash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $siteName = sanitize($_POST['site_name'] ?? 'HIEU CEO');
    $siteTagline = sanitize($_POST['site_tagline'] ?? '');
    $defaultFont = sanitize($_POST['default_font'] ?? 'Outfit');
    $maintenanceMode = isset($_POST['maintenance_mode']) ? '1' : '0';
    $animationLevel = sanitize($_POST['animation_level'] ?? 'smooth_ultra');

    updateSystemSetting('site_name', $siteName);
    updateSystemSetting('site_tagline', $siteTagline);
    updateSystemSetting('default_font', $defaultFont);
    updateSystemSetting('maintenance_mode', $maintenanceMode);
    updateSystemSetting('animation_level', $animationLevel);

    logSystemAction($_SESSION['user_id'] ?? 1, 'SETTINGS_UPDATE', 'Cập nhật cấu hình hệ thống toàn cục.');
    setFlash('success', 'Đã lưu cấu hình hệ thống thành công!');
    header('Location: settings.php');
    exit;
}

$siteName = getSystemSetting('site_name', 'HIEU CEO - Master Website Interface & Theme Hub');
$siteTagline = getSystemSetting('site_tagline', 'Hệ Thống Quản Lý Giao Diện Website & Trình Tùy Biến Trực Quan Chuẩn CEO');
$defaultFont = getSystemSetting('default_font', 'Outfit');
$maintenanceMode = getSystemSetting('maintenance_mode', '0');
$animationLevel = getSystemSetting('animation_level', 'smooth_ultra');
?>
<!DOCTYPE html>
<html lang="vi" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cài Đặt Hệ Thống - HIEU CEO</title>
  
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
    <aside class="admin-sidebar">
      <a href="../index.php" class="ceo-logo" style="margin-bottom:10px;">
        <div class="logo-icon"><i class="fa-solid fa-crown"></i></div>
        <span>HIEU<span class="text-gold-gradient">.CEO</span></span>
      </a>
      <ul class="sidebar-menu">
        <li><a href="index.php"><i class="fa-solid fa-chart-pie"></i> Bảng Điều Khiển CEO</a></li>
        <li><a href="themes.php"><i class="fa-solid fa-layer-group"></i> Quản Lý Giao Diện</a></li>
        <li><a href="theme-add.php"><i class="fa-solid fa-plus-circle"></i> Thêm Giao Diện Mới</a></li>
        <li><a href="components.php"><i class="fa-solid fa-cube"></i> Thư Viện UI Kit</a></li>
        <li><a href="analytics.php"><i class="fa-solid fa-arrow-trend-up"></i> Phân Tích & A/B Test</a></li>
        <li><a href="users.php"><i class="fa-solid fa-user-shield"></i> Phân Quyền Ban ĐH</a></li>
        <li><a href="logs.php"><i class="fa-solid fa-clock-rotate-left"></i> Nhật Ký Hoạt Động</a></li>
        <li><a href="settings.php" class="active"><i class="fa-solid fa-sliders"></i> Cài Đặt Hệ Thống</a></li>
      </ul>
    </aside>

    <main class="admin-main">
      <div style="margin-bottom:30px;">
        <h1 style="font-size:1.8rem;font-weight:800;margin-bottom:4px;">Cài Đặt Hệ Thống & Bảo Trì</h1>
        <p style="color:var(--text-secondary);font-size:0.92rem;">Cấu hình thương hiệu, bộ nhớ đệm và sao lưu cơ sở dữ liệu an toàn</p>
      </div>

      <?php if (!empty($flash['success'])): ?>
        <div style="background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.35);color:#34d399;padding:12px 16px;border-radius:var(--radius-md);margin-bottom:20px;">
          <?= e($flash['success']) ?>
        </div>
      <?php endif; ?>

      <div style="display:grid;grid-template-columns:1.5fr 1fr;gap:28px;">
        <!-- General Settings Form -->
        <form method="POST" action="settings.php" class="glass-panel" style="padding:32px;">
          <h3 style="font-size:1.15rem;font-weight:700;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
            <i class="fa-solid fa-gears" style="color:#ffb8e7;"></i> Cấu Hình Toàn Cục
          </h3>

          <div class="form-group">
            <label class="form-label">Tên Thương Hiệu Hệ Thống:</label>
            <input type="text" name="site_name" value="<?= e($siteName) ?>" required class="glass-input">
          </div>

          <div class="form-group">
            <label class="form-label">Khẩu Hiệu Hệ Thống (Tagline):</label>
            <input type="text" name="site_tagline" value="<?= e($siteTagline) ?>" class="glass-input">
          </div>

          <div class="ceo-grid-2">
            <div class="form-group">
              <label class="form-label">Phông Chữ Mặc Định:</label>
              <select name="default_font" class="glass-input">
                <option value="Outfit" <?= $defaultFont === 'Outfit' ? 'selected' : '' ?>>Outfit</option>
                <option value="Plus Jakarta Sans" <?= $defaultFont === 'Plus Jakarta Sans' ? 'selected' : '' ?>>Plus Jakarta Sans</option>
                <option value="Montserrat" <?= $defaultFont === 'Montserrat' ? 'selected' : '' ?>>Montserrat</option>
                <option value="Cinzel" <?= $defaultFont === 'Cinzel' ? 'selected' : '' ?>>Cinzel</option>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label">Cấp Độ Chuyển Động (Animation):</label>
              <select name="animation_level" class="glass-input">
                <option value="smooth_ultra" <?= $animationLevel === 'smooth_ultra' ? 'selected' : '' ?>>Siêu Mượt 60FPS (Ultra)</option>
                <option value="balanced" <?= $animationLevel === 'balanced' ? 'selected' : '' ?>>Cân Bằng (Balanced)</option>
                <option value="minimal" <?= $animationLevel === 'minimal' ? 'selected' : '' ?>>Tối Giản (Minimal)</option>
              </select>
            </div>
          </div>

          <div class="form-group" style="margin-top:10px;">
            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
              <input type="checkbox" name="maintenance_mode" value="1" <?= $maintenanceMode === '1' ? 'checked' : '' ?> style="width:20px;height:20px;accent-color:#ef4444;">
              <span style="font-weight:600;color:var(--text-primary);">Bật chế độ bảo trì hệ thống (Maintenance Mode)</span>
            </label>
          </div>

          <button type="submit" class="btn-ceo-primary btn-ripple" style="margin-top:14px;">
            <i class="fa-solid fa-floppy-disk mr-1"></i> Lưu Cấu Hình Hệ Thống
          </button>
        </form>

        <!-- System Maintenance & Backup Tools -->
        <div style="display:flex;flex-direction:column;gap:24px;">
          <!-- Cache Flush Card -->
          <div class="glass-panel" style="padding:28px;">
            <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:10px;color:var(--text-primary);display:flex;align-items:center;gap:8px;">
              <i class="fa-solid fa-arrows-rotate" style="color:#10b981;"></i> Quản Lý Bộ Nhớ Đệm
            </h3>
            <p style="color:var(--text-secondary);font-size:0.85rem;margin-bottom:18px;line-height:1.55;">
              Xóa sạch các tệp cache và render lại toàn bộ CSS Tokens của tất cả giao diện.
            </p>
            <button id="btn-admin-clear-cache" class="btn-ceo-secondary" style="width:100%;">
              <i class="fa-solid fa-broom mr-1"></i> Dọn Dẹp Cache Ngay
            </button>
          </div>

          <!-- Backup Card -->
          <div class="glass-panel" style="padding:28px;">
            <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:10px;color:var(--text-primary);display:flex;align-items:center;gap:8px;">
              <i class="fa-solid fa-database" style="color:#f59e0b;"></i> Sao Lưu Dữ Liệu MySQL
            </h3>
            <p style="color:var(--text-secondary);font-size:0.85rem;margin-bottom:18px;line-height:1.55;">
              Tạo bản sao lưu toàn bộ 10 bảng CSDL <code>hieu_ceo_db</code> với 1 click.
            </p>
            <button id="btn-create-backup" class="btn-ceo-gold btn-ripple" style="width:100%;">
              <i class="fa-solid fa-download mr-1"></i> Xuất File SQL Backup
            </button>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script src="../assets/js/ceo-app.js"></script>
  <script>
    document.getElementById('btn-admin-clear-cache').addEventListener('click', async () => {
      try {
        const res = await fetch('../api/system.php?action=clear_cache');
        const d = await res.json();
        if (d.success) showToast(d.message, 'success');
      } catch (e) {
        showToast('Lỗi dọn dẹp cache.', 'error');
      }
    });

    document.getElementById('btn-create-backup').addEventListener('click', async () => {
      try {
        const res = await fetch('../api/system.php?action=backup');
        const d = await res.json();
        if (d.success) {
          showToast(`Sao lưu thành công tệp: ${d.data.backup_file} (${d.data.size} bytes)`, 'success');
        } else {
          showToast(d.message, 'error');
        }
      } catch (e) {
        showToast('Lỗi tạo sao lưu CSDL.', 'error');
      }
    });
  </script>
</body>
</html>
