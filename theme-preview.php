<?php
/**
 * HIEU CEO - Live Device Preview Simulator
 * Responsive Viewport Testing (Desktop iMac, iPad Pro, iPhone 15 Pro)
 */

require_once __DIR__ . '/config/helper.php';

$themeId = (int)($_GET['theme_id'] ?? 1);
$theme = getThemeById($themeId);

if (!$theme) {
    header('Location: index.php');
    exit;
}

$allThemes = getAllThemes();
?>
<!DOCTYPE html>
<html lang="vi" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mô Phỏng Giao Diện: <?= e($theme['name']) ?> - HIEU CEO</title>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="assets/css/ceo-core.css">
  <link rel="stylesheet" href="assets/css/animations.css">
  <link rel="stylesheet" href="assets/css/preview.css">
</head>
<body class="preview-workspace">

  <!-- 1. Simulator Top Toolbar -->
  <header class="preview-toolbar">
    <div style="display:flex;align-items:center;gap:16px;">
      <a href="index.php" class="btn-icon" title="Quay lại Trung Tâm Điều Hành">
        <i class="fa-solid fa-arrow-left"></i>
      </a>

      <div>
        <div style="display:flex;align-items:center;gap:8px;">
          <h2 style="font-size:1.1rem;font-weight:700;margin:0;"><?= e($theme['name']) ?></h2>
          <span class="badge-ceo <?= $theme['status'] === 'active' ? 'badge-active' : 'badge-ready' ?>" style="font-size:0.7rem;padding:3px 8px;">
            <?= $theme['status'] === 'active' ? 'Đang Vận Hành' : 'Sẵn Sàng' ?>
          </span>
        </div>
        <div style="font-size:0.75rem;color:var(--text-muted);">
          Code: <strong><?= e($theme['code_name']) ?></strong> • Danh mục: <strong><?= e($theme['category_name']) ?></strong>
        </div>
      </div>
    </div>

    <!-- Center: Device Mode Switcher -->
    <div class="device-selector">
      <button class="device-btn active" data-device="desktop" title="Màn hình Máy tính (Desktop 1280px)">
        <i class="fa-solid fa-desktop"></i> Desktop
      </button>
      <button class="device-btn" data-device="tablet" title="Màn hình Máy tính bảng (iPad Pro 768px)">
        <i class="fa-solid fa-tablet-screen-button"></i> Tablet
      </button>
      <button class="device-btn" data-device="mobile" title="Màn hình Điện thoại (iPhone 15 Pro 390px)">
        <i class="fa-solid fa-mobile-screen"></i> Mobile
      </button>
    </div>

    <!-- Right: Actions & Tools -->
    <div style="display:flex;align-items:center;gap:10px;">
      <select id="select-preview-zoom" class="glass-input" style="width:auto;padding:6px 12px;font-size:0.82rem;">
        <option value="1">Zoom: 100%</option>
        <option value="0.9">Zoom: 90%</option>
        <option value="0.8">Zoom: 80%</option>
        <option value="0.7">Zoom: 70%</option>
      </select>

      <button id="btn-reload-preview" class="btn-icon" title="Tải lại trang xem thử">
        <i class="fa-solid fa-rotate-right"></i>
      </button>

      <a href="customizer.php?theme_id=<?= $theme['id'] ?>" class="btn-ceo-secondary" style="padding:8px 16px;font-size:0.85rem;">
        <i class="fa-solid fa-wand-magic-sparkles mr-1" style="color:var(--ceo-gold);"></i> Tùy Biến
      </a>

      <?php if ($theme['status'] !== 'active'): ?>
        <button class="btn-ceo-primary btn-activate-theme" data-theme-id="<?= $theme['id'] ?>" data-theme-name="<?= e($theme['name']) ?>" style="padding:8px 18px;font-size:0.85rem;">
          <i class="fa-solid fa-power-off mr-1"></i> Kích Hoạt
        </button>
      <?php endif; ?>

      <a href="<?= e($theme['preview_url']) ?>" target="_blank" class="btn-icon" title="Mở trong tab mới">
        <i class="fa-solid fa-arrow-up-right-from-square"></i>
      </a>
    </div>
  </header>

  <!-- 2. Simulator Stage Area -->
  <main class="preview-stage">
    <div id="simulator-device-frame" class="device-frame desktop animate-fade-scale">
      <!-- Browser Top bar (Desktop mode) -->
      <div class="frame-browser-bar">
        <div class="browser-dots">
          <span class="dot-red"></span>
          <span class="dot-yellow"></span>
          <span class="dot-green"></span>
        </div>
        <div class="browser-address">
          <i class="fa-solid fa-lock" style="color:#10b981;"></i>
          <span>https://hieu.ceo/projects/<?= e($theme['folder_path']) ?>/</span>
        </div>
      </div>

      <!-- Authentic iOS Mobile / Tablet Status Bar -->
      <div class="mobile-status-bar">
        <span>9:41</span>
        <div class="dynamic-island">
          <span class="island-camera"></span>
          <span style="font-size:10px;color:#fff;font-weight:700;letter-spacing:0.5px;">HIEU</span>
          <span class="island-sensor"></span>
        </div>
        <div style="display:flex;align-items:center;gap:6px;font-size:0.75rem;">
          <i class="fa-solid fa-signal"></i>
          <i class="fa-solid fa-wifi"></i>
          <i class="fa-solid fa-battery-full"></i>
        </div>
      </div>

      <!-- Live Screen Container & iframe -->
      <div class="screen-container">
        <iframe id="simulator-iframe" class="preview-iframe" src="<?= e($theme['preview_url']) ?>" title="Theme Preview"></iframe>
      </div>

      <!-- Authentic Home Indicator Bar -->
      <div class="mobile-home-indicator">
        <span></span>
      </div>
    </div>
  </main>

  <script src="assets/js/ceo-app.js"></script>
  <script src="assets/js/preview-simulator.js"></script>
</body>
</html>
