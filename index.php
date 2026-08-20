<?php
/**
 * HIEU CEO - Master Website Interface & Theme Management Portal
 * Executive Standard: Ultra-Luxury Glassmorphism & High-Performance Theme Hub
 */

require_once __DIR__ . '/config/helper.php';

$activeCategory = sanitize($_GET['category'] ?? 'all');
$searchQuery = sanitize($_GET['search'] ?? '');
$allCategories = getAllCategories();
$themes = getAllThemes($activeCategory, $searchQuery);
$activeTheme = getActiveTheme();
$currentUser = getCurrentUser();
$projects = scanProjectsDirectory();
?>
<!DOCTYPE html>
<html lang="vi" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HIEU CEO - Hệ Thống Quản Lý Giao Diện Website Chuẩn Điều Hành</title>
  <meta name="description" content="Nền tảng quản lý và tùy biến giao diện website đẳng cấp CEO, tích hợp mô phỏng đa thiết bị và chuyển động mượt mà.">
  
  <!-- Font Awesome 6.5 Pro Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  <!-- Core Design System & Motion Styles -->
  <link rel="stylesheet" href="assets/css/ceo-core.css">
  <link rel="stylesheet" href="assets/css/animations.css">
</head>
<body>
  <div class="ceo-mesh-bg"></div>

  <!-- 1. Executive Master Navbar -->
  <header class="ceo-container">
    <nav class="ceo-navbar ceo-flex-between animate-fade-up">
      <a href="index.php" class="ceo-logo">
        <div class="logo-icon">
          <i class="fa-solid fa-crown"></i>
        </div>
        <div>
          <span>HIEU<span class="text-gold-gradient">.CEO</span></span>
          <span style="display:block;font-size:0.68rem;letter-spacing:0.12em;color:var(--text-accent);font-weight:600;">THEME MANAGER PRO</span>
        </div>
      </a>

      <!-- Quick Search Form -->
      <form action="index.php" method="GET" style="position:relative;width:320px;" class="hide-mobile">
        <input type="text" name="search" value="<?= e($searchQuery) ?>" placeholder="Tìm giao diện (VD: thời trang, gym, tech...)" class="glass-input" style="padding-left:38px;padding-top:8px;padding-bottom:8px;font-size:0.88rem;">
        <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:0.85rem;"></i>
        <?php if (!empty($activeCategory) && $activeCategory !== 'all'): ?>
          <input type="hidden" name="category" value="<?= e($activeCategory) ?>">
        <?php endif; ?>
      </form>

      <!-- Action Navigation -->
      <div style="display:flex;align-items:center;gap:12px;">
        <a href="explore.php" class="btn-ceo-primary btn-ripple" style="padding:8px 18px;font-size:0.85rem;" title="Xem tất cả website đã đăng tải">
          <i class="fa-solid fa-compass mr-1"></i> Khám Phá Website
        </a>

        <a href="admin/project-upload.php" class="btn-ceo-secondary" style="padding:8px 16px;font-size:0.85rem;" title="Tải lên thư mục dự án ZIP">
          <i class="fa-solid fa-cloud-arrow-up mr-1" style="color:#38bdf8;"></i> Tải Lên Dự Án
        </a>

        <button id="btn-theme-mode" class="btn-icon" title="Chuyển chế độ Sáng / Tối">
          <i class="fa-solid fa-circle-half-stroke"></i>
        </button>

        <button id="btn-clear-cache" class="btn-icon" title="Xóa Cache & Tối ưu CSS">
          <i class="fa-solid fa-arrows-rotate"></i>
        </button>

        <?php if ($currentUser): ?>
          <a href="admin/index.php" class="btn-ceo-primary btn-ripple" style="padding:8px 18px;font-size:0.88rem;">
            <i class="fa-solid fa-chart-pie mr-2"></i> Bảng Điều Khiển CEO
          </a>
          <a href="logout.php" class="btn-ceo-secondary" style="padding:8px 14px;font-size:0.85rem;" title="Đăng xuất">
            <i class="fa-solid fa-right-from-bracket"></i>
          </a>
        <?php else: ?>
          <a href="login.php" class="btn-ceo-primary btn-ripple" style="padding:8px 20px;font-size:0.88rem;">
            <i class="fa-solid fa-shield-halved mr-2"></i> Đăng Nhập CEO
          </a>
        <?php endif; ?>
      </div>
    </nav>
  </header>

  <main class="ceo-container">
    <!-- 2. Hero Section -->
    <section style="padding:30px 0 50px 0;text-align:center;" class="animate-fade-up">
      <div style="display:inline-flex;align-items:center;gap:8px;padding:6px 18px;background:rgba(99,102,241,0.12);border:1px solid rgba(99,102,241,0.3);border-radius:9999px;margin-bottom:20px;">
        <span class="animate-beacon" style="width:8px;height:8px;background:#10b981;border-radius:50%;display:inline-block;"></span>
        <span style="font-size:0.82rem;font-weight:700;letter-spacing:0.08em;color:#818cf8;text-transform:uppercase;">
          CÔNG NGHỆ QUẢN LÝ GIAO DIỆN CHUẨN CEO 2026
        </span>
      </div>

      <h1 style="font-size: clamp(2.2rem, 5vw, 3.8rem);font-weight:900;line-height:1.15;letter-spacing:-0.03em;margin-bottom:20px;">
        Trung Tâm Điều Hành <span class="text-shimmer">Giao Diện Website</span><br>
        <span class="text-gold-gradient">Đẳng Cấp & Vượt Trội</span>
      </h1>

      <p style="max-width:760px;margin:0 auto 36px auto;color:var(--text-secondary);font-size:1.1rem;line-height:1.7;">
        Hệ sinh thái quản trị giao diện website toàn diện viết bằng <strong>PHP & MySQL</strong>, lưu trữ tập trung tại thư mục <code>projects/</code>, cho phép tải lên tệp <strong>.ZIP</strong> dự án, kích hoạt thời gian thực và giả lập hiển thị trên mọi thiết bị.
      </p>

      <div style="display:flex;align-items:center;justify-content:center;gap:16px;flex-wrap:wrap;margin-bottom:48px;">
        <a href="explore.php" class="btn-ceo-primary btn-ripple animate-pulse-glow" style="padding:14px 32px;font-size:1.05rem;">
          <i class="fa-solid fa-compass mr-1"></i> Khám Phá <?= count($themes) ?> Website Đã Đăng Tải
        </a>
        <a href="admin/project-upload.php" class="btn-ceo-gold btn-ripple" style="padding:14px 28px;font-size:1.05rem;">
          <i class="fa-solid fa-cloud-arrow-up mr-1"></i> Tải Lên Dự Án (.ZIP)
        </a>
        <a href="admin/projects.php" class="btn-ceo-secondary btn-ripple" style="padding:14px 26px;font-size:1.05rem;">
          <i class="fa-solid fa-folder-tree mr-1" style="color:#38bdf8;"></i> Kho Thư Mục Dự Án
        </a>
        <a href="customizer.php?theme_id=<?= $activeTheme['id'] ?? 1 ?>" class="btn-ceo-secondary btn-ripple" style="padding:14px 26px;font-size:1.05rem;">
          <i class="fa-solid fa-wand-magic-sparkles" style="color:var(--ceo-gold);"></i> Trình Tùy Biến
        </a>
        <a href="test_system.php" class="btn-ceo-secondary" style="padding:14px 24px;font-size:1.05rem;" target="_blank" title="Chạy bộ kiểm thử tự động">
          <i class="fa-solid fa-vial-circle-check" style="color:#10b981;"></i> Kiểm Thử
        </a>
      </div>

      <!-- 3. Key Performance Indicators Bar -->
      <div class="glass-panel" style="padding:28px;display:grid;grid-template-columns:repeat(4, 1fr);gap:20px;text-align:center;">
        <div>
          <div style="color:var(--text-muted);font-size:0.85rem;font-weight:600;text-transform:uppercase;margin-bottom:6px;">
            <i class="fa-solid fa-cube mr-2" style="color:#6366f1;"></i> Giao Diện Đang Chạy
          </div>
          <div style="font-size:2rem;font-weight:800;color:var(--text-primary);">
            <span class="counter-val" data-target="<?= count($themes) ?>" data-current="0">0</span> Mẫu
          </div>
        </div>
        <div>
          <div style="color:var(--text-muted);font-size:0.85rem;font-weight:600;text-transform:uppercase;margin-bottom:6px;">
            <i class="fa-solid fa-folder-tree mr-2" style="color:#38bdf8;"></i> Thư Mục projects/
          </div>
          <div style="font-size:2rem;font-weight:800;color:var(--text-primary);">
            <span class="counter-val" data-target="<?= count($projects) ?>" data-current="0">0</span> Thư Mục
          </div>
        </div>
        <div>
          <div style="color:var(--text-muted);font-size:0.85rem;font-weight:600;text-transform:uppercase;margin-bottom:6px;">
            <i class="fa-solid fa-bolt mr-2" style="color:#10b981;"></i> Tốc Độ Tải (LCP)
          </div>
          <div style="font-size:2rem;font-weight:800;color:#34d399;">
            0.38s (Ultra Fast)
          </div>
        </div>
        <div>
          <div style="color:var(--text-muted);font-size:0.85rem;font-weight:600;text-transform:uppercase;margin-bottom:6px;">
            <i class="fa-solid fa-shield-check mr-2" style="color:#f59e0b;"></i> Độ Ổn Định
          </div>
          <div style="font-size:2rem;font-weight:800;color:#fbbf24;">
            99.99% Uptime
          </div>
        </div>
      </div>
    </section>

    <!-- 4. Active Theme Live Spotlight -->
    <?php if ($activeTheme): ?>
    <section style="margin-bottom:60px;" class="animate-fade-up">
      <div class="glass-panel" style="padding:32px;border:1px solid rgba(16,185,129,0.35);background:linear-gradient(135deg, rgba(15,23,42,0.85), rgba(6,78,59,0.15));position:relative;overflow:hidden;">
        <div style="position:absolute;top:0;right:0;background:linear-gradient(135deg, #10b981, #059669);color:#fff;padding:6px 24px;border-bottom-left-radius:18px;font-size:0.78rem;font-weight:800;letter-spacing:0.08em;">
          <i class="fa-solid fa-circle-dot animate-beacon mr-1"></i> GIAO DIỆN ĐANG VẬN HÀNH CHÍNH
        </div>

        <div style="display:grid;grid-template-columns:1.2fr 0.8fr;gap:36px;align-items:center;">
          <div>
            <span class="badge-ceo badge-active" style="margin-bottom:12px;">
              <i class="fa-solid fa-star"></i> <?= e($activeTheme['category_name']) ?> • Version <?= e($activeTheme['version']) ?>
            </span>
            <h2 style="font-size:1.85rem;font-weight:800;margin-bottom:10px;">
              <?= e($activeTheme['name']) ?>
            </h2>
            <p style="color:var(--text-secondary);margin-bottom:20px;font-size:0.98rem;line-height:1.6;">
              <?= e($activeTheme['description']) ?>
            </p>

            <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
              <a href="theme-preview.php?theme_id=<?= $activeTheme['id'] ?>" class="btn-ceo-primary btn-ripple">
                <i class="fa-solid fa-laptop-code"></i> Xem Trước Đa Thiết Bị
              </a>
              <a href="customizer.php?theme_id=<?= $activeTheme['id'] ?>" class="btn-ceo-secondary">
                <i class="fa-solid fa-sliders"></i> Tùy Biến Màu & Font
              </a>
              <a href="<?= e($activeTheme['preview_url']) ?>" target="_blank" class="btn-ceo-secondary" title="Mở trang trực tiếp">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Mở Trực Tiếp
              </a>
            </div>
          </div>

          <div style="text-align:center;">
            <div class="glass-card" style="padding:16px;background:rgba(0,0,0,0.4);border-radius:16px;">
              <div style="font-size:0.85rem;color:var(--text-muted);margin-bottom:8px;">Bảng màu & Định danh:</div>
              <div style="display:flex;justify-content:center;gap:10px;margin-bottom:14px;">
                <span style="width:24px;height:24px;border-radius:50%;background:<?= e($activeTheme['primary_color']) ?>;display:inline-block;box-shadow:0 0 10px <?= e($activeTheme['primary_color']) ?>;" title="Primary Color"></span>
                <span style="width:24px;height:24px;border-radius:50%;background:<?= e($activeTheme['secondary_color']) ?>;display:inline-block;box-shadow:0 0 10px <?= e($activeTheme['secondary_color']) ?>;" title="Secondary Color"></span>
                <span style="width:24px;height:24px;border-radius:50%;background:<?= e($activeTheme['accent_color']) ?>;display:inline-block;box-shadow:0 0 10px <?= e($activeTheme['accent_color']) ?>;" title="Accent Color"></span>
              </div>
              <div style="font-size:0.88rem;color:var(--text-primary);font-family:var(--font-mono);">
                Code: <strong><?= e($activeTheme['code_name']) ?></strong> | Thư mục: <strong><?= e($activeTheme['folder_path']) ?></strong>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <!-- 5. Theme Catalog & Category Switcher -->
    <section id="themes-catalog" style="margin-bottom:80px;">
      <div class="ceo-flex-between" style="margin-bottom:28px;flex-wrap:wrap;gap:16px;">
        <div>
          <h2 style="font-size:1.9rem;font-weight:800;margin-bottom:6px;">
            Kho Giao Diện Website <span class="text-cyan-gradient">Cao Cấp</span>
          </h2>
          <p style="color:var(--text-secondary);font-size:0.95rem;">
            Chọn và kích hoạt tức thì các bộ giao diện chuyên biệt cho từng mô hình kinh doanh.
          </p>
        </div>

        <!-- Category Tabs -->
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
          <a href="index.php?category=all#themes-catalog" class="badge-ceo <?= $activeCategory === 'all' ? 'badge-active' : 'badge-ready' ?>" style="text-decoration:none;padding:8px 16px;font-size:0.82rem;cursor:pointer;">
            Tất Cả (<?= count($themes) ?>)
          </a>
          <?php foreach ($allCategories as $cat): ?>
            <a href="index.php?category=<?= e($cat['slug']) ?>#themes-catalog" class="badge-ceo <?= $activeCategory === $cat['slug'] ? 'badge-active' : 'badge-ready' ?>" style="text-decoration:none;padding:8px 16px;font-size:0.82rem;cursor:pointer;">
              <i class="fa-solid <?= e($cat['icon']) ?> mr-1"></i> <?= e($cat['name']) ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Theme Cards Grid -->
      <div class="ceo-grid-3">
        <?php foreach ($themes as $t): ?>
          <div class="glass-card ceo-card-tilt animate-fade-up" style="display:flex;flex-direction:column;position:relative;overflow:hidden;">
            <!-- Status Badge Top -->
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
              <span class="badge-ceo <?= $t['status'] === 'active' ? 'badge-active' : 'badge-ready' ?>">
                <i class="fa-solid <?= $t['status'] === 'active' ? 'fa-circle-check' : 'fa-layer-group' ?>"></i>
                <?= $t['status'] === 'active' ? 'Đang Kích Hoạt' : 'Sẵn Sàng' ?>
              </span>
              <span style="font-size:0.85rem;color:var(--text-muted);font-weight:600;">
                <i class="fa-solid fa-star" style="color:#f59e0b;"></i> <?= number_format($t['rating'], 1) ?>
              </span>
            </div>

            <!-- Card Body -->
            <h3 style="font-size:1.25rem;font-weight:700;margin-bottom:8px;color:var(--text-primary);">
              <?= e($t['name']) ?>
            </h3>
            
            <p style="color:var(--text-secondary);font-size:0.88rem;line-height:1.55;margin-bottom:18px;flex:1;">
              <?= e($t['tagline'] ?: substr($t['description'], 0, 110) . '...') ?>
            </p>

            <!-- Specs Row -->
            <div style="background:rgba(0,0,0,0.25);border-radius:10px;padding:10px 14px;display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;font-size:0.8rem;">
              <div style="color:var(--text-secondary);">
                Danh mục: <strong style="color:var(--text-primary);"><?= e($t['category_name']) ?></strong>
              </div>
              <div style="color:var(--text-secondary);">
                Font: <strong style="color:var(--text-primary);"><?= e($t['font_family']) ?></strong>
              </div>
            </div>

            <!-- Actions Row -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
              <a href="theme-preview.php?theme_id=<?= $t['id'] ?>" class="btn-ceo-secondary" style="padding:10px;font-size:0.85rem;text-align:center;" title="Mô phỏng đa thiết bị">
                <i class="fa-solid fa-eye mr-1"></i> Xem Thử
              </a>
              <a href="<?= e($t['preview_url']) ?>" target="_blank" class="btn-ceo-secondary" style="padding:10px;font-size:0.85rem;text-align:center;" title="Mở trang web trực tiếp trong tab mới">
                <i class="fa-solid fa-arrow-up-right-from-square mr-1" style="color:#38bdf8;"></i> Mở Web
              </a>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
              <a href="customizer.php?theme_id=<?= $t['id'] ?>" class="btn-ceo-secondary" style="padding:10px;font-size:0.85rem;text-align:center;" title="Tùy biến bảng màu và typography">
                <i class="fa-solid fa-wand-magic-sparkles mr-1" style="color:var(--ceo-gold);"></i> Tùy Biến
              </a>
              <?php if ($t['status'] === 'active'): ?>
                <button class="btn-ceo-primary" style="padding:10px;font-size:0.85rem;background:#10b981;cursor:default;" disabled>
                  <i class="fa-solid fa-circle-check mr-1"></i> Đang Chạy
                </button>
              <?php else: ?>
                <button class="btn-ceo-primary btn-activate-theme" data-theme-id="<?= $t['id'] ?>" data-theme-name="<?= e($t['name']) ?>" style="padding:10px;font-size:0.85rem;">
                  <i class="fa-solid fa-power-off mr-1"></i> Kích Hoạt
                </button>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  </main>

  <!-- 6. Master Footer -->
  <footer style="border-top:1px solid var(--border-glass);padding:40px 0;background:rgba(7,9,19,0.85);margin-top:60px;">
    <div class="ceo-container ceo-flex-between" style="flex-wrap:wrap;gap:20px;">
      <div>
        <div class="ceo-logo" style="margin-bottom:8px;">
          <div class="logo-icon" style="width:32px;height:32px;font-size:0.9rem;">
            <i class="fa-solid fa-crown"></i>
          </div>
          <span>HIEU<span class="text-gold-gradient">.CEO</span></span>
        </div>
        <p style="color:var(--text-muted);font-size:0.85rem;">
          © <?= date('Y') ?> HIEU CEO Architecture. All Rights Reserved. Built with PHP & MySQL.
        </p>
      </div>

      <div style="display:flex;gap:20px;font-size:0.9rem;">
        <a href="admin/project-upload.php" style="color:var(--text-secondary);text-decoration:none;">Tải Lên Dự Án</a>
        <a href="admin/projects.php" style="color:var(--text-secondary);text-decoration:none;">Kho projects/</a>
        <a href="test_system.php" style="color:var(--text-secondary);text-decoration:none;" target="_blank">Kiểm Thử Hệ Thống</a>
        <a href="admin/index.php" style="color:var(--text-secondary);text-decoration:none;">Quản Trị Admin</a>
        <a href="README.md" style="color:var(--text-secondary);text-decoration:none;" target="_blank">Tài Liệu Hướng Dẫn</a>
      </div>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="assets/js/ceo-app.js"></script>
</body>
</html>
