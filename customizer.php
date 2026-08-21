<?php
/**
 * HIEU CEO - Live Visual Theme Customizer
 * Real-time Theme Token Editor & Section Manager
 */

require_once __DIR__ . '/config/helper.php';

$themeId = (int)($_GET['theme_id'] ?? 1);
$theme = getThemeById($themeId);

if (!$theme) {
    header('Location: index.php');
    exit;
}

$sections = getThemeSections($themeId);
$tokens = getThemeTokens($themeId);
?>
<!DOCTYPE html>
<html lang="vi" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tùy Biến Giao Diện: <?= e($theme['name']) ?> - HIEU CEO</title>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="assets/css/ceo-core.css">
  <link rel="stylesheet" href="assets/css/animations.css">
  
  <style>
    .customizer-layout {
      display: grid;
      grid-template-columns: 380px 1fr;
      height: 100vh;
      overflow: hidden;
    }
    .customizer-sidebar {
      background: #050505;
      border-right: 1px solid var(--border-glass);
      display: flex;
      flex-direction: column;
      height: 100vh;
      overflow-y: auto;
    }
    .customizer-header {
      padding: 20px 24px;
      border-bottom: 1px solid var(--border-glass);
      background: rgba(16, 16, 16, 0.8);
      position: sticky;
      top: 0;
      z-index: 10;
    }
    .customizer-body {
      padding: 24px;
      flex: 1;
    }
    .customizer-section-card {
      background: rgba(28, 28, 28, 0.4);
      border: 1px solid var(--border-glass);
      border-radius: var(--radius-md);
      padding: 16px;
      margin-bottom: 20px;
    }
    .color-picker-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 12px;
    }
    .color-input-badge {
      width: 38px;
      height: 38px;
      padding: 0;
      border: 2px solid var(--border-glass);
      border-radius: 8px;
      cursor: pointer;
      background: transparent;
    }
    .customizer-preview-container {
      background: #000000;
      display: flex;
      flex-direction: column;
      height: 100vh;
    }
    .preview-iframe-box {
      flex: 1;
      width: 100%;
      height: 100%;
      border: none;
      background: #ffffff;
    }
  </style>
</head>
<body>

  <div class="customizer-layout">
    <!-- Left Sidebar: Controls -->
    <aside class="customizer-sidebar">
      <div class="customizer-header ceo-flex-between">
        <div style="display:flex;align-items:center;gap:12px;">
          <a href="index.php" class="btn-icon" style="width:34px;height:34px;" title="Quay lại">
            <i class="fa-solid fa-arrow-left"></i>
          </a>
          <div>
            <h2 style="font-size:1rem;font-weight:700;margin:0;">Tùy Biến Trực Quan</h2>
            <span style="font-size:0.75rem;color:var(--text-accent);"><?= e($theme['name']) ?></span>
          </div>
        </div>

        <button id="btn-save-customizer" class="btn-ceo-primary" style="padding:7px 16px;font-size:0.82rem;">
          <i class="fa-solid fa-floppy-disk mr-1"></i> Lưu Lại
        </button>
      </div>

      <div class="customizer-body">
        <input type="hidden" id="customizer-theme-id" value="<?= $theme['id'] ?>">

        <!-- 1. Brand Color Palette -->
        <div class="customizer-section-card">
          <h3 style="font-size:0.9rem;font-weight:700;margin-bottom:14px;color:var(--text-primary);display:flex;align-items:center;gap:8px;">
            <i class="fa-solid fa-palette" style="color:#ffb8e7;"></i> Bảng Màu Thương Hiệu
          </h3>

          <div class="color-picker-row">
            <label class="form-label" style="margin:0;">Màu Chủ Đạo (Primary):</label>
            <input type="color" id="cust-primary-color" value="<?= e($theme['primary_color'] ?: '#ffb8e7') ?>" class="color-input-badge">
          </div>

          <div class="color-picker-row">
            <label class="form-label" style="margin:0;">Màu Phụ (Secondary):</label>
            <input type="color" id="cust-secondary-color" value="<?= e($theme['secondary_color'] ?: '#89f5ff') ?>" class="color-input-badge">
          </div>

          <div class="color-picker-row">
            <label class="form-label" style="margin:0;">Màu Nhấn (Accent):</label>
            <input type="color" id="cust-accent-color" value="<?= e($theme['accent_color'] ?: '#89f5ff') ?>" class="color-input-badge">
          </div>

          <div class="color-picker-row">
            <label class="form-label" style="margin:0;">Màu Nền Nền (Background):</label>
            <input type="color" id="cust-bg-color" value="<?= e($theme['bg_color'] ?: '#101010') ?>" class="color-input-badge">
          </div>
        </div>

        <!-- 2. Typography & Radius -->
        <div class="customizer-section-card">
          <h3 style="font-size:0.9rem;font-weight:700;margin-bottom:14px;color:var(--text-primary);display:flex;align-items:center;gap:8px;">
            <i class="fa-solid fa-font" style="color:#f59e0b;"></i> Font Chữ & Kiểu Dáng
          </h3>

          <div class="form-group">
            <label class="form-label">Phông Chữ Toàn Trang:</label>
            <select id="cust-font-family" class="glass-input">
              <option value="Outfit" <?= $theme['font_family'] === 'Outfit' ? 'selected' : '' ?>>Outfit (Hiện Đại - Luxury)</option>
              <option value="Plus Jakarta Sans" <?= $theme['font_family'] === 'Plus Jakarta Sans' ? 'selected' : '' ?>>Plus Jakarta Sans (Sắc Nét)</option>
              <option value="Montserrat" <?= $theme['font_family'] === 'Montserrat' ? 'selected' : '' ?>>Montserrat (Mạnh Mẽ - Thể Thao)</option>
              <option value="Cinzel" <?= $theme['font_family'] === 'Cinzel' ? 'selected' : '' ?>>Cinzel (Hoàng Gia - Thượng Lưu)</option>
            </select>
          </div>

          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label">Độ Bo Góc Viền (Border Radius):</label>
            <input type="range" id="cust-border-radius" min="0" max="32" value="14" style="width:100%;accent-color:var(--ceo-primary);">
          </div>
        </div>

        <!-- 3. Section Manager -->
        <?php if (!empty($sections)): ?>
        <div class="customizer-section-card">
          <h3 style="font-size:0.9rem;font-weight:700;margin-bottom:14px;color:var(--text-primary);display:flex;align-items:center;gap:8px;">
            <i class="fa-solid fa-table-cells-large" style="color:#10b981;"></i> Quản Lý Bật/Tắt Section
          </h3>

          <?php foreach ($sections as $sec): ?>
            <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.05);">
              <span style="font-size:0.85rem;color:var(--text-primary);"><?= e($sec['section_name']) ?></span>
              <input type="checkbox" class="section-toggle-cb" data-section-key="<?= e($sec['section_key']) ?>" <?= $sec['is_enabled'] ? 'checked' : '' ?> style="width:18px;height:18px;accent-color:#10b981;cursor:pointer;">
            </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- 4. Custom CSS Injection -->
        <div class="customizer-section-card">
          <h3 style="font-size:0.9rem;font-weight:700;margin-bottom:12px;color:var(--text-primary);display:flex;align-items:center;gap:8px;">
            <i class="fa-solid fa-code" style="color:#89f5ff;"></i> Tùy Biến Custom CSS
          </h3>
          <textarea id="cust-custom-css" rows="4" class="glass-input" style="font-family:var(--font-mono);font-size:0.8rem;" placeholder="/* Thêm CSS tùy biến tại đây */"><?= e($theme['custom_css']) ?></textarea>
        </div>
      </div>
    </aside>

    <!-- Right: Real-time Live Preview Stage -->
    <main class="customizer-preview-container">
      <div style="height:44px;background:#0a0a0a;border-bottom:1px solid var(--border-glass);display:flex;align-items:center;justify-content:space-between;padding:0 20px;">
        <span style="font-size:0.8rem;color:var(--text-muted);">
          <i class="fa-solid fa-circle-dot animate-beacon mr-1" style="color:#10b981;"></i>
          Khung Xem Trước Trực Quan (Real-time Live Sync)
        </span>
        <a href="<?= e($theme['preview_url']) ?>" target="_blank" style="color:var(--text-accent);font-size:0.8rem;text-decoration:none;">
          <i class="fa-solid fa-up-right-from-square mr-1"></i> Mở Cửa Sổ Mới
        </a>
      </div>
      <iframe id="customizer-preview-frame" class="preview-iframe-box" src="<?= e($theme['preview_url']) ?>" title="Live Customizer Preview"></iframe>
    </main>
  </div>

  <script src="assets/js/ceo-app.js"></script>
  <script src="assets/js/customizer.js"></script>
</body>
</html>
