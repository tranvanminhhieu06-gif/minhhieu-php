<?php
/**
 * ====================================================================
 * HIEU CEO - INTERACTIVE MULTI-DEVICE LIVE PROJECT VIEWER & WORKSPACE
 * ====================================================================
 * Trình xem Live đa thiết bị mô phỏng chân thực:
 * - Desktop Full Canvas (100%) & iMac Studio (1280px)
 * - iPad Pro Tablet (768x1024 / 1024x768 xoay ngang dọc)
 * - iPhone 15 Pro (390x844 với Dynamic Island & Bezel)
 * Tích hợp chuyển đổi dự án thời gian thực, quét mã QR, thả tim yêu thích và đánh giá.
 */

require_once __DIR__ . '/config/auth_user.php';

$allThemes = getAllThemes();
$allProjects = scanProjectsDirectory();
$currentUser = getUserProfile();

// Xác định dự án đang được chọn xem
$selectedThemeId = (int)($_GET['theme_id'] ?? 0);
$selectedFolder = sanitize($_GET['project'] ?? '');

$currentTheme = null;
$currentProjectMeta = null;

if ($selectedThemeId > 0) {
    $currentTheme = getThemeById($selectedThemeId);
    if ($currentTheme) {
        $selectedFolder = basename($currentTheme['folder_path']);
    }
}

if (!$currentTheme && !empty($selectedFolder)) {
    foreach ($allThemes as $t) {
        if (strcasecmp(basename($t['folder_path']), $selectedFolder) === 0 || $t['code_name'] === $selectedFolder) {
            $currentTheme = $t;
            break;
        }
    }
}

// Nếu vẫn chưa có theme, mặc định lấy active theme hoặc theme đầu tiên
if (!$currentTheme) {
    $currentTheme = getActiveTheme() ?: ($allThemes[0] ?? null);
    if ($currentTheme) {
        $selectedFolder = basename($currentTheme['folder_path']);
    }
}

// Xác định đường dẫn Live URL của dự án
$liveUrl = 'projects/HieuWeb01/index.php'; // Mặc định fallback
if ($currentTheme) {
    $liveUrl = $currentTheme['preview_url'];
    if (!str_contains($liveUrl, '://') && !str_starts_with($liveUrl, 'projects/') && !str_starts_with($liveUrl, 'index.php')) {
        $liveUrl = 'projects/' . $liveUrl;
    }
} elseif (!empty($selectedFolder)) {
    $liveUrl = 'projects/' . $selectedFolder . '/index.php';
}

// Lấy thông tin tài khoản demo & cấu hình
$projectName = $currentTheme ? $currentTheme['name'] : $selectedFolder;
$projectTagline = $currentTheme ? ($currentTheme['tagline'] ?: $currentTheme['description']) : "Dự án website trong thư mục projects/{$selectedFolder}";
$projectCode = $currentTheme ? $currentTheme['code_name'] : $selectedFolder;
$primaryColor = $currentTheme ? ($currentTheme['primary_color'] ?: '#6366f1') : '#6366f1';
$demoCreds = getDemoCredentialsForProject($selectedFolder ?: $projectCode);

// Kiểm tra trạng thái yêu thích
$isFavorited = $currentTheme ? isThemeFavoritedByUser((int)$currentTheme['id']) : false;

// Ghi nhận lịch sử xem của người dùng
recordProjectView($selectedFolder ?: ($currentTheme ? $currentTheme['code_name'] : 'live'), $projectName, $liveUrl);

// Chuẩn bị URL tuyệt đối để sinh mã QR
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? '127.0.0.1:8099';
$fullAbsoluteUrl = $protocol . $host . '/' . ltrim($liveUrl, '/');
$qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($fullAbsoluteUrl);

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="vi" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Live Preview: <?= e($projectName) ?> - HIEU CEO Portal</title>
  <meta name="description" content="Trải nghiệm trực tiếp và mô phỏng giao diện website <?= e($projectName) ?> trên đa thiết bị Desktop, Tablet iPad và Mobile iPhone 15 Pro.">

  <!-- Font Awesome 6.5 Pro Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  <!-- CSS Core & Live View Styles -->
  <link rel="stylesheet" href="assets/css/ceo-core.css">
  <link rel="stylesheet" href="assets/css/animations.css">
  <link rel="stylesheet" href="assets/css/live-view.css">

  <style>
    :root {
      --current-project-color: <?= e($primaryColor) ?>;
    }
  </style>
</head>
<body class="live-workspace">

  <!-- Ambient Light Glow matching project theme color -->
  <div class="live-ambient-glow" id="ambient-glow" style="background: radial-gradient(circle, <?= e($primaryColor) ?>33 0%, rgba(6,182,212,0.08) 40%, transparent 70%);"></div>

  <!-- ==================== 1. TOP LIVE TOOLBAR ==================== -->
  <header class="live-toolbar animate-fade-down">
    <!-- Left: Brand & Project Switcher Dropdown -->
    <div class="live-toolbar-left">
      <a href="index.php" class="btn-icon" title="Quay lại Trang Chủ">
        <i class="fa-solid fa-house"></i>
      </a>

      <div class="project-selector-dropdown">
        <button class="project-select-btn" id="btn-toggle-project-dropdown" title="Bấm để chuyển đổi nhanh giữa các dự án">
          <span style="width:10px;height:10px;border-radius:50%;background:<?= e($primaryColor) ?>;box-shadow:0 0 10px <?= e($primaryColor) ?>;display:inline-block;"></span>
          <span style="max-width:210px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= e($projectName) ?></span>
          <i class="fa-solid fa-chevron-down" style="font-size:0.75rem;color:var(--text-muted);"></i>
        </button>

        <!-- Dropdown Menu -->
        <div class="project-dropdown-menu" id="project-dropdown-menu">
          <div style="padding:6px 10px 10px 10px;border-bottom:1px solid rgba(255,255,255,0.08);margin-bottom:6px;">
            <span style="font-size:0.72rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.06em;">Chọn Website Cần Xem Live:</span>
          </div>

          <?php if (!empty($allThemes)): ?>
            <?php foreach ($allThemes as $t): ?>
              <?php 
                $isCurrent = ($currentTheme && $currentTheme['id'] == $t['id']);
                $tColor = $t['primary_color'] ?: '#6366f1';
              ?>
              <a href="live-view.php?theme_id=<?= $t['id'] ?>" class="dropdown-item-project <?= $isCurrent ? 'active' : '' ?>">
                <span style="width:8px;height:8px;border-radius:50%;background:<?= e($tColor) ?>;"></span>
                <div style="flex:1;min-width:0;">
                  <div style="font-size:0.85rem;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= e($t['name']) ?></div>
                  <div style="font-size:0.72rem;color:var(--text-muted);"><?= e($t['folder_path']) ?> • <?= e($t['category_name']) ?></div>
                </div>
                <?php if ($isCurrent): ?>
                  <i class="fa-solid fa-circle-check text-success" style="font-size:0.8rem;"></i>
                <?php endif; ?>
              </a>
            <?php endforeach; ?>
          <?php else: ?>
            <?php foreach ($allProjects as $p): ?>
              <?php $isCurrent = ($selectedFolder === $p['folder_name']); ?>
              <a href="live-view.php?project=<?= urlencode($p['folder_name']) ?>" class="dropdown-item-project <?= $isCurrent ? 'active' : '' ?>">
                <span style="width:8px;height:8px;border-radius:50%;background:#38bdf8;"></span>
                <div style="flex:1;min-width:0;">
                  <div style="font-size:0.85rem;font-weight:700;"><?= e($p['folder_name']) ?></div>
                  <div style="font-size:0.72rem;color:var(--text-muted);">projects/<?= e($p['folder_name']) ?></div>
                </div>
                <?php if ($isCurrent): ?>
                  <i class="fa-solid fa-circle-check text-success" style="font-size:0.8rem;"></i>
                <?php endif; ?>
              </a>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Center: Multi-Device Viewport Switcher -->
    <div class="live-toolbar-center">
      <div class="device-viewport-bar">
        <button class="device-tab-btn" data-device="full-screen" title="Toàn Màn Hình Canvas (100% Responsive)">
          <i class="fa-solid fa-expand"></i> <span>Full Canvas</span>
        </button>
        <button class="device-tab-btn active" data-device="desktop" title="Màn hình Máy tính iMac Studio (1280px)">
          <i class="fa-solid fa-desktop"></i> <span>Desktop</span>
        </button>
        <button class="device-tab-btn" data-device="tablet" title="Màn hình Máy tính bảng iPad Pro (768px)">
          <i class="fa-solid fa-tablet-screen-button"></i> <span>Tablet</span>
        </button>
        <button class="device-tab-btn" data-device="mobile" title="Màn hình Điện thoại iPhone 15 Pro (390px)">
          <i class="fa-solid fa-mobile-screen"></i> <span>Mobile</span>
        </button>
      </div>

      <!-- Tablet Rotate Toggle (Visible in tablet mode) -->
      <button id="btn-rotate-device" class="btn-icon hide-desktop" style="display:none;" title="Xoay Ngang / Dọc Thiết Bị">
        <i class="fa-solid fa-arrows-rotate"></i>
      </button>
    </div>

    <!-- Right: Actions, Favoriting, QR Code, Tech Specs & User Profile -->
    <div class="live-toolbar-right">
      <!-- Heart Favorite Button -->
      <?php if ($currentTheme): ?>
        <button id="btn-favorite-action" class="btn-heart-favorite <?= $isFavorited ? 'favorited' : '' ?>" data-theme-id="<?= $currentTheme['id'] ?>" title="Lưu vào danh sách yêu thích">
          <i class="fa-<?= $isFavorited ? 'solid' : 'regular' ?> fa-heart"></i>
          <span id="favorite-btn-text"><?= $isFavorited ? 'Đã Thích' : 'Yêu Thích' ?></span>
        </button>
      <?php endif; ?>

      <!-- Tech Specs / Demo Account Button -->
      <button id="btn-open-specs-modal" class="btn-ceo-secondary" style="padding:7px 12px;font-size:0.82rem;" title="Xem thông tin chi tiết & tài khoản mẫu">
        <i class="fa-solid fa-circle-info mr-1" style="color:#38bdf8;"></i> <span class="hide-mobile">Thông Tin</span>
      </button>

      <!-- Mobile QR Code Button -->
      <button id="btn-open-qr-modal" class="btn-icon" title="Quét mã QR để xem live trên điện thoại cá nhân">
        <i class="fa-solid fa-qrcode"></i>
      </button>

      <!-- Rating & Feedback Button -->
      <button id="btn-open-rate-modal" class="btn-icon" title="Đánh giá chất lượng website">
        <i class="fa-solid fa-star" style="color:#f59e0b;"></i>
      </button>

      <!-- Reload Iframe Button -->
      <button id="btn-reload-frame" class="btn-icon" title="Tải lại nội dung trang">
        <i class="fa-solid fa-rotate-right"></i>
      </button>

      <!-- Direct Open in New Tab -->
      <a href="<?= e($liveUrl) ?>" target="_blank" class="btn-icon" title="Mở website trong thẻ trình duyệt mới">
        <i class="fa-solid fa-arrow-up-right-from-square"></i>
      </a>

      <!-- User Account / Dashboard Link -->
      <?php if ($currentUser): ?>
        <a href="user/dashboard.php" class="btn-ceo-secondary" style="padding:6px 12px;font-size:0.82rem;gap:6px;" title="Vào Bảng Điều Khiển Cá Nhân">
          <i class="fa-solid fa-user-circle" style="color:#34d399;"></i>
          <span class="hide-mobile"><?= e($currentUser['full_name']) ?></span>
        </a>
      <?php else: ?>
        <a href="user/login.php?redirect=<?= urlencode('../live-view.php') ?>" class="btn-ceo-primary btn-ripple" style="padding:6px 14px;font-size:0.82rem;" title="Đăng nhập tài khoản thành viên">
          <i class="fa-solid fa-user mr-1"></i> Đăng Nhập
        </a>
      <?php endif; ?>

      <!-- Sidebar Drawer Toggle -->
      <button id="btn-toggle-drawer" class="btn-icon" title="Mở danh sách các dự án">
        <i class="fa-solid fa-bars-staggered"></i>
      </button>
    </div>
  </header>

  <!-- ==================== 2. MAIN SIMULATOR STAGE ==================== -->
  <main class="live-stage" id="live-stage">
    
    <!-- Active Device Frame (Desktop by default) -->
    <div id="device-frame-container" class="device-frame desktop animate-fade-scale">
      
      <!-- Top Frame Bar with Traffic Lights & URL -->
      <div class="frame-top-bar" id="frame-top-bar">
        <div class="browser-traffic-lights">
          <span class="dot-red"></span>
          <span class="dot-yellow"></span>
          <span class="dot-green"></span>
        </div>

        <div class="frame-url-input">
          <i class="fa-solid fa-lock" style="color:#10b981;font-size:0.75rem;"></i>
          <span><?= e($fullAbsoluteUrl) ?></span>
        </div>

        <div style="font-size:0.72rem;color:var(--text-muted);font-family:var(--font-mono);">
          <span id="viewport-resolution-label">1280 × 800</span>
        </div>
      </div>

      <!-- iPhone Dynamic Island (Shown only in Mobile mode) -->
      <div class="dynamic-island-notch" id="mobile-dynamic-island" style="display:none;">
        <span class="island-camera-lens"></span>
        <span style="font-size:10px;color:#ffffff;font-weight:800;letter-spacing:0.5px;">HIEU LIVE</span>
        <span class="island-glow-dot"></span>
      </div>

      <!-- Live Web Iframe -->
      <iframe id="live-iframe-element" class="live-iframe" src="<?= e($liveUrl) ?>" title="Live Website View" allowfullscreen></iframe>

      <!-- iOS Home Bar Indicator (Shown only in Mobile mode) -->
      <div class="ios-home-bar" id="mobile-home-bar" style="display:none;"></div>
    </div>
  </main>

  <!-- ==================== 3. SLIDE-OUT PROJECTS DRAWER ==================== -->
  <aside class="projects-drawer-panel" id="projects-drawer">
    <div style="padding:18px 20px;border-bottom:1px solid rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:space-between;">
      <div style="display:flex;align-items:center;gap:8px;">
        <i class="fa-solid fa-folder-tree" style="color:#38bdf8;"></i>
        <h3 style="font-size:1.05rem;font-weight:700;margin:0;">Kho Dự Án Website</h3>
      </div>
      <button id="btn-close-drawer" class="btn-icon" style="width:32px;height:32px;"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <!-- Quick Search within Drawer -->
    <div style="padding:14px 20px;border-bottom:1px solid rgba(255,255,255,0.06);">
      <input type="text" id="drawer-search-input" class="glass-input" placeholder="Tìm kiếm nhanh website..." style="padding:8px 12px;font-size:0.82rem;">
    </div>

    <!-- Drawer Project List -->
    <div style="flex:1;overflow-y:auto;padding:12px 16px;" id="drawer-project-items">
      <?php if (!empty($allThemes)): ?>
        <?php foreach ($allThemes as $t): ?>
          <?php 
            $isActive = ($currentTheme && $currentTheme['id'] == $t['id']);
            $tColor = $t['primary_color'] ?: '#6366f1';
          ?>
          <div class="glass-card drawer-item" data-name="<?= strtolower(e($t['name'] . ' ' . $t['code_name'])) ?>" style="padding:14px;margin-bottom:10px;cursor:pointer;border-color:<?= $isActive ? 'rgba(99,102,241,0.6)' : 'rgba(255,255,255,0.08)' ?>;background:<?= $isActive ? 'rgba(99,102,241,0.12)' : 'rgba(15,23,42,0.6)' ?>;" onclick="window.location.href='live-view.php?theme_id=<?= $t['id'] ?>'">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
              <span class="badge-ceo" style="font-size:0.68rem;padding:2px 8px;border-color:<?= e($tColor) ?>66;color:<?= e($tColor) ?>;">
                <?= e($t['category_name']) ?>
              </span>
              <span style="font-size:0.75rem;color:var(--text-muted);"><i class="fa-solid fa-star text-gold mr-1"></i><?= number_format($t['rating'], 1) ?></span>
            </div>

            <h4 style="font-size:0.95rem;font-weight:700;margin:0 0 4px 0;color:var(--text-primary);"><?= e($t['name']) ?></h4>
            <p style="font-size:0.78rem;color:var(--text-secondary);margin:0 0 10px 0;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
              <?= e($t['tagline'] ?: $t['description']) ?>
            </p>

            <div style="display:flex;justify-content:space-between;align-items:center;font-size:0.74rem;">
              <span style="color:#38bdf8;font-family:var(--font-mono);"><i class="fa-solid fa-folder mr-1"></i><?= e($t['folder_path']) ?></span>
              <span style="color:var(--text-accent);font-weight:600;">Xem Ngay →</span>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <?php foreach ($allProjects as $p): ?>
          <?php $isActive = ($selectedFolder === $p['folder_name']); ?>
          <div class="glass-card drawer-item" data-name="<?= strtolower(e($p['folder_name'])) ?>" style="padding:14px;margin-bottom:10px;cursor:pointer;border-color:<?= $isActive ? 'rgba(99,102,241,0.6)' : 'rgba(255,255,255,0.08)' ?>;background:<?= $isActive ? 'rgba(99,102,241,0.12)' : 'rgba(15,23,42,0.6)' ?>;" onclick="window.location.href='live-view.php?project=<?= urlencode($p['folder_name']) ?>'">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
              <span class="badge-ceo" style="font-size:0.68rem;padding:2px 8px;border-color:#38bdf866;color:#38bdf8;">
                Dự Án projects/
              </span>
              <span style="font-size:0.75rem;color:var(--text-muted);"><i class="fa-solid fa-folder text-accent mr-1"></i><?= $p['file_count'] ?> files</span>
            </div>

            <h4 style="font-size:0.95rem;font-weight:700;margin:0 0 4px 0;color:var(--text-primary);"><?= e($p['folder_name']) ?></h4>
            <p style="font-size:0.78rem;color:var(--text-secondary);margin:0 0 10px 0;">
              Dung lượng: <?= e($p['size_formatted']) ?> • Khởi chạy: <?= e($p['entry_file']) ?>
            </p>

            <div style="display:flex;justify-content:space-between;align-items:center;font-size:0.74rem;">
              <span style="color:#38bdf8;font-family:var(--font-mono);">projects/<?= e($p['folder_name']) ?></span>
              <span style="color:var(--text-accent);font-weight:600;">Xem Ngay →</span>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </aside>

  <!-- ==================== 4. MODALS & POPUPS ==================== -->

  <!-- Modal 1: Tech Specs & Demo Credentials -->
  <div class="live-modal-backdrop" id="modal-specs">
    <div class="live-modal-content animate-scale-up">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
        <div style="display:flex;align-items:center;gap:10px;">
          <div style="width:36px;height:36px;border-radius:10px;background:rgba(56,189,248,0.2);display:flex;align-items:center;justify-content:center;color:#38bdf8;">
            <i class="fa-solid fa-laptop-code"></i>
          </div>
          <div>
            <h3 style="font-size:1.15rem;font-weight:800;margin:0;"><?= e($projectName) ?></h3>
            <span style="font-size:0.75rem;color:var(--text-muted);font-family:var(--font-mono);"><?= e($projectCode) ?> • <?= e($selectedFolder) ?></span>
          </div>
        </div>
        <button class="btn-icon btn-close-modal" style="width:32px;height:32px;"><i class="fa-solid fa-xmark"></i></button>
      </div>

      <p style="color:var(--text-secondary);font-size:0.88rem;line-height:1.6;margin-bottom:18px;">
        <?= e($projectTagline) ?>
      </p>

      <!-- Tech Stack Badges -->
      <div style="margin-bottom:20px;">
        <label style="display:block;font-size:0.75rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;margin-bottom:8px;">Công Nghệ Tích Hợp:</label>
        <div style="display:flex;gap:6px;flex-wrap:wrap;">
          <?php foreach ($demoCreds['tech'] as $tech): ?>
            <span class="badge-ceo" style="font-size:0.75rem;padding:4px 10px;background:rgba(255,255,255,0.06);border-color:rgba(255,255,255,0.12);">
              <i class="fa-solid fa-check-double text-success mr-1"></i> <?= e($tech) ?>
            </span>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Demo Accounts Box -->
      <div style="background:rgba(0,0,0,0.45);border:1px dashed rgba(255,255,255,0.15);border-radius:14px;padding:14px 16px;margin-bottom:20px;">
        <div style="color:var(--text-accent);font-weight:700;font-size:0.82rem;margin-bottom:10px;display:flex;align-items:center;gap:6px;">
          <i class="fa-solid fa-key text-warning"></i> Tài Khoản Đăng Nhập Thử Nghiệm:
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;font-size:0.8rem;">
          <div style="background:rgba(255,255,255,0.04);padding:10px;border-radius:8px;">
            <div style="color:var(--text-muted);font-size:0.72rem;margin-bottom:2px;">👑 Quản Trị Viên (Admin):</div>
            <div>Email: <strong style="color:var(--text-primary);"><?= e($demoCreds['admin_email']) ?></strong></div>
            <div>Pass: <code style="color:#34d399;"><?= e($demoCreds['admin_pass']) ?></code></div>
          </div>

          <div style="background:rgba(255,255,255,0.04);padding:10px;border-radius:8px;">
            <div style="color:var(--text-muted);font-size:0.72rem;margin-bottom:2px;">👤 Khách Hàng (Customer):</div>
            <div>Email: <strong style="color:var(--text-primary);"><?= e($demoCreds['customer_email']) ?></strong></div>
            <div>Pass: <code style="color:#34d399;"><?= e($demoCreds['customer_pass']) ?></code></div>
          </div>
        </div>
      </div>

      <!-- Features List -->
      <div style="margin-bottom:24px;">
        <label style="display:block;font-size:0.75rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;margin-bottom:8px;">Tính Năng Nổi Bật:</label>
        <ul style="margin:0;padding-left:18px;font-size:0.84rem;color:var(--text-secondary);line-height:1.7;">
          <?php foreach ($demoCreds['features'] as $feat): ?>
            <li><?= e($feat) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div style="display:flex;gap:10px;">
        <a href="<?= e($liveUrl) ?>" target="_blank" class="btn-ceo-primary btn-ripple" style="flex:1;padding:10px;font-size:0.88rem;justify-content:center;">
          <i class="fa-solid fa-arrow-up-right-from-square mr-2"></i> Mở Trực Tiếp Tab Mới
        </a>
        <button class="btn-ceo-secondary btn-close-modal" style="padding:10px 18px;font-size:0.88rem;">Đóng</button>
      </div>
    </div>
  </div>

  <!-- Modal 2: QR Code for Smartphone -->
  <div class="live-modal-backdrop" id="modal-qr">
    <div class="live-modal-content animate-scale-up" style="max-width:400px;text-align:center;">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <h3 style="font-size:1.15rem;font-weight:800;margin:0;"><i class="fa-solid fa-qrcode text-accent mr-2"></i> Quét Mã QR Trực Tiếp</h3>
        <button class="btn-icon btn-close-modal" style="width:32px;height:32px;"><i class="fa-solid fa-xmark"></i></button>
      </div>

      <p style="color:var(--text-secondary);font-size:0.85rem;margin-bottom:18px;">
        Mở camera trên điện thoại của bạn và hướng vào mã QR bên dưới để tải trang web trực tiếp:
      </p>

      <div style="background:#ffffff;padding:16px;border-radius:18px;display:inline-block;margin-bottom:16px;box-shadow:0 15px 35px rgba(0,0,0,0.5);">
        <img src="<?= e($qrCodeUrl) ?>" alt="QR Code Live Website" style="width:200px;height:200px;display:block;">
      </div>

      <div style="background:rgba(0,0,0,0.4);border-radius:10px;padding:8px 12px;font-size:0.75rem;color:var(--text-muted);font-family:var(--font-mono);word-break:break-all;margin-bottom:20px;">
        <?= e($fullAbsoluteUrl) ?>
      </div>

      <button class="btn-ceo-primary btn-close-modal" style="width:100%;padding:10px;justify-content:center;">
        Hoàn Tất
      </button>
    </div>
  </div>

  <!-- Modal 3: Rating & Feedback -->
  <div class="live-modal-backdrop" id="modal-rate">
    <div class="live-modal-content animate-scale-up" style="max-width:440px;">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <h3 style="font-size:1.15rem;font-weight:800;margin:0;"><i class="fa-solid fa-star text-gold mr-2"></i> Đánh Giá Giao Diện</h3>
        <button class="btn-icon btn-close-modal" style="width:32px;height:32px;"><i class="fa-solid fa-xmark"></i></button>
      </div>

      <p style="color:var(--text-secondary);font-size:0.85rem;margin-bottom:18px;">
        Chia sẻ cảm nhận của bạn về giao diện <strong><?= e($projectName) ?></strong>:
      </p>

      <form id="rating-form">
        <input type="hidden" name="theme_id" value="<?= $currentTheme ? $currentTheme['id'] : 1 ?>">

        <!-- Star selector -->
        <div style="display:flex;justify-content:center;gap:12px;font-size:1.8rem;margin-bottom:20px;color:#cbd5e1;cursor:pointer;" id="star-rating-container">
          <i class="fa-solid fa-star" data-rating="1"></i>
          <i class="fa-solid fa-star" data-rating="2"></i>
          <i class="fa-solid fa-star" data-rating="3"></i>
          <i class="fa-solid fa-star" data-rating="4"></i>
          <i class="fa-solid fa-star active text-gold" data-rating="5"></i>
        </div>
        <input type="hidden" name="rating" id="selected-rating-val" value="5">

        <div style="margin-bottom:20px;">
          <label style="display:block;font-size:0.8rem;font-weight:600;color:var(--text-secondary);margin-bottom:6px;">Ý kiến đóng góp (tùy chọn):</label>
          <textarea name="comment" rows="3" class="glass-input" placeholder="Giao diện rất mượt, màu sắc hiện đại, tính năng phong phú..."></textarea>
        </div>

        <button type="submit" class="btn-ceo-primary btn-ripple" style="width:100%;padding:11px;font-size:0.92rem;justify-content:center;font-weight:700;">
          <i class="fa-solid fa-paper-plane mr-2"></i> Gửi Đánh Giá Ngay
        </button>
      </form>
    </div>
  </div>

  <!-- Live Toast Element -->
  <div class="live-toast" id="live-toast">
    <i class="fa-solid fa-circle-check text-success" id="toast-icon"></i>
    <span id="toast-message">Thao tác thành công!</span>
  </div>

  <!-- ==================== 5. JAVASCRIPT LOGIC ==================== -->
  <script>
    // State management
    const currentThemeId = <?= $currentTheme ? $currentTheme['id'] : 0 ?>;
    const deviceFrame = document.getElementById('device-frame-container');
    const liveIframe = document.getElementById('live-iframe-element');
    const dynamicIsland = document.getElementById('mobile-dynamic-island');
    const homeBar = document.getElementById('mobile-home-bar');
    const frameTopBar = document.getElementById('frame-top-bar');
    const resLabel = document.getElementById('viewport-resolution-label');
    const rotateBtn = document.getElementById('btn-rotate-device');

    // Device Switcher
    const deviceButtons = document.querySelectorAll('.device-tab-btn');
    deviceButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        deviceButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const device = btn.dataset.device;
        deviceFrame.className = 'device-frame animate-fade-scale ' + device;

        if (device === 'full-screen') {
          frameTopBar.style.display = 'none';
          dynamicIsland.style.display = 'none';
          homeBar.style.display = 'none';
          rotateBtn.style.display = 'none';
        } else if (device === 'desktop') {
          frameTopBar.style.display = 'flex';
          dynamicIsland.style.display = 'none';
          homeBar.style.display = 'none';
          resLabel.textContent = '1280 × 800';
          rotateBtn.style.display = 'none';
        } else if (device === 'tablet') {
          frameTopBar.style.display = 'none';
          dynamicIsland.style.display = 'none';
          homeBar.style.display = 'none';
          rotateBtn.style.display = 'flex';
          resLabel.textContent = deviceFrame.classList.contains('landscape') ? '1024 × 768' : '768 × 1024';
        } else if (device === 'mobile') {
          frameTopBar.style.display = 'none';
          dynamicIsland.style.display = 'flex';
          homeBar.style.display = 'block';
          rotateBtn.style.display = 'none';
          resLabel.textContent = '393 × 852';
        }
      });
    });

    // Tablet Rotate
    rotateBtn.addEventListener('click', () => {
      deviceFrame.classList.toggle('landscape');
      resLabel.textContent = deviceFrame.classList.contains('landscape') ? '1024 × 768' : '768 × 1024';
    });

    // Reload Frame
    document.getElementById('btn-reload-frame').addEventListener('click', () => {
      liveIframe.classList.add('loading');
      liveIframe.src = liveIframe.src;
      setTimeout(() => liveIframe.classList.remove('loading'), 400);
      showToast('Đang tải lại giao diện...', 'fa-rotate-right');
    });

    // Toggle Project Dropdown
    const toggleDropdownBtn = document.getElementById('btn-toggle-project-dropdown');
    const projectDropdown = document.getElementById('project-dropdown-menu');
    toggleDropdownBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      projectDropdown.classList.toggle('show');
    });
    document.addEventListener('click', () => projectDropdown.classList.remove('show'));

    // Drawer Toggle
    const drawer = document.getElementById('projects-drawer');
    document.getElementById('btn-toggle-drawer').addEventListener('click', () => drawer.classList.add('open'));
    document.getElementById('btn-close-drawer').addEventListener('click', () => drawer.classList.remove('open'));

    // Drawer Live Search
    document.getElementById('drawer-search-input').addEventListener('input', (e) => {
      const q = e.target.value.toLowerCase().trim();
      document.querySelectorAll('.drawer-item').forEach(item => {
        const name = item.dataset.name;
        item.style.display = name.includes(q) ? 'block' : 'none';
      });
    });

    // Modals Handlers
    function setupModal(triggerId, modalId) {
      const trigger = document.getElementById(triggerId);
      const modal = document.getElementById(modalId);
      if (!trigger || !modal) return;

      trigger.addEventListener('click', () => modal.classList.add('show'));
      modal.querySelectorAll('.btn-close-modal').forEach(b => {
        b.addEventListener('click', () => modal.classList.remove('show'));
      });
      modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.classList.remove('show');
      });
    }

    setupModal('btn-open-specs-modal', 'modal-specs');
    setupModal('btn-open-qr-modal', 'modal-qr');
    setupModal('btn-open-rate-modal', 'modal-rate');

    // Heart Favorite Action
    const favBtn = document.getElementById('btn-favorite-action');
    if (favBtn) {
      favBtn.addEventListener('click', async () => {
        try {
          const res = await fetch('api/toggle_favorite.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ theme_id: currentThemeId })
          });
          const data = await res.json();
          if (data.success) {
            if (data.favorited) {
              favBtn.classList.add('favorited');
              favBtn.innerHTML = '<i class="fa-solid fa-heart"></i> <span id="favorite-btn-text">Đã Thích</span>';
              showToast(data.message, 'fa-heart text-danger');
            } else {
              favBtn.classList.remove('favorited');
              favBtn.innerHTML = '<i class="fa-regular fa-heart"></i> <span id="favorite-btn-text">Yêu Thích</span>';
              showToast(data.message, 'fa-circle-info');
            }
          }
        } catch (e) {
          showToast('Không thể kết nối đến máy chủ', 'fa-triangle-exclamation');
        }
      });
    }

    // Star Rating Interaction
    const starIcons = document.querySelectorAll('#star-rating-container i');
    const ratingInput = document.getElementById('selected-rating-val');
    starIcons.forEach(star => {
      star.addEventListener('click', () => {
        const val = parseInt(star.dataset.rating, 10);
        ratingInput.value = val;
        starIcons.forEach((s, idx) => {
          if (idx < val) {
            s.className = 'fa-solid fa-star text-gold';
          } else {
            s.className = 'fa-regular fa-star';
          }
        });
      });
    });

    // Rating Submit
    const ratingForm = document.getElementById('rating-form');
    if (ratingForm) {
      ratingForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(ratingForm);
        try {
          const res = await fetch('api/rate_project.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              theme_id: formData.get('theme_id'),
              rating: formData.get('rating'),
              comment: formData.get('comment')
            })
          });
          const data = await res.json();
          document.getElementById('modal-rate').classList.remove('show');
          showToast(data.message, 'fa-star text-gold');
        } catch (err) {
          showToast('Lỗi khi gửi đánh giá', 'fa-triangle-exclamation');
        }
      });
    }

    // Toast Utility
    function showToast(msg, icon = 'fa-circle-check') {
      const toast = document.getElementById('live-toast');
      const toastMsg = document.getElementById('toast-message');
      const toastIcon = document.getElementById('toast-icon');

      toastMsg.textContent = msg;
      toastIcon.className = 'fa-solid ' + icon;
      toast.classList.add('show');

      setTimeout(() => {
        toast.classList.remove('show');
      }, 3500);
    }
  </script>
</body>
</html>
