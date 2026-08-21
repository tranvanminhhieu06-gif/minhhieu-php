<?php
/**
 * HIEU CEO - Public Website Directory & Live Experience Portal
 * Trang Giao Diện Người Dùng Khám Phá & Trải Nghiệm Các Trang Web Đã Đăng Tải
 */

require_once __DIR__ . '/config/auth_user.php';

// Ẩn trang explore và chuyển hướng trực tiếp sang live-view.php
header('Location: live-view.php');
exit;

$allCategories = getAllCategories();
$themes = getAllThemes($activeCategory, $searchQuery);
$activeTheme = getActiveTheme();
$currentUser = getUserProfile();
$projects = scanProjectsDirectory();

// Map project folder details to themes if available
$folderThemeMap = [];
foreach ($themes as $t) {
    $folderKey = basename($t['folder_path']);
    $folderThemeMap[$folderKey] = $t;
}

// Demo accounts database for each theme
$demoCredentials = [
    'HIEU_WEB_01' => [
        'title' => 'Thời Trang HieuMini Studio',
        'admin_email' => 'admin@hieumini.vn',
        'admin_pass' => 'admin123',
        'customer_email' => 'khachhang@gmail.com',
        'customer_pass' => 'admin123',
        'features' => 'Hero Slider, Flash Sale, Size Guide Modal, Giỏ hàng VietQR MBBank, Hóa đơn điện tử'
    ],
    'HIEU_WEB_02' => [
        'title' => 'Nhà Sách Hieu Bookstore Hub',
        'admin_email' => 'admin@hieubooks.vn',
        'admin_pass' => 'admin123',
        'customer_email' => 'docgia@gmail.com',
        'customer_pass' => 'admin123',
        'features' => 'Đọc thử trích đoạn, Đánh giá sao, Bộ lọc thể loại, Mã giảm giá FREESHIP'
    ],
    'HIEU_WEB_03' => [
        'title' => 'Nội Thất Hieu Living Decor',
        'admin_email' => 'admin@hieuliving.vn',
        'admin_pass' => 'admin123',
        'customer_email' => 'khachhang@gmail.com',
        'customer_pass' => 'admin123',
        'features' => 'Showroom 3D góc rộng, Thiết kế theo không gian phòng, Tư vấn nội thất cá nhân hóa'
    ],
    'HIEU_WEB_04' => [
        'title' => 'Công Nghệ Hieu CyberTech Innovation',
        'admin_email' => 'admin@hieutech.vn',
        'admin_pass' => 'admin123',
        'customer_email' => 'khachhang@gmail.com',
        'customer_pass' => 'admin123',
        'features' => 'So sánh thông số kỹ thuật, Trả góp 0%, Tra cứu bảo hành điện tử'
    ],
    'HIEU_WEB_05' => [
        'title' => 'Thể Hình & Dinh Dưỡng Hieu Pro Gym',
        'admin_email' => 'admin@hieugym.vn',
        'admin_pass' => 'admin123',
        'customer_email' => 'hoi_vien@gmail.com',
        'customer_pass' => 'admin123',
        'features' => 'Máy tính chỉ số BMI cơ thể, Bán thực phẩm bổ sung Whey Isolate, Đặt lịch huấn luyện viên PT'
    ],
    'HIEU_HIEUCYBERPORTFOLIO' => [
        'title' => 'Hieu Cyber Portfolio Pro',
        'admin_email' => 'ceo@hieu.vn',
        'admin_pass' => 'admin123',
        'customer_email' => 'guest@hieu.vn',
        'customer_pass' => 'admin123',
        'features' => 'Showcase dự án công nghệ cao cấp, Glassmorphism tối ưu, Tốc độ tải siêu mượt'
    ]
];
?>
<!DOCTYPE html>
<html lang="vi" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Khám Phá Dự Án Website - HIEU CEO Portal</title>
  <meta name="description" content="Danh mục các trang web và thư mục dự án đã đăng tải trong hệ sinh thái projects/. Truy cập trực tiếp, xem mô phỏng đa thiết bị và tùy biến trực quan.">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="assets/css/ceo-core.css">
  <link rel="stylesheet" href="assets/css/animations.css">
  
  <style>
    .showcase-header {
      padding: 40px 0 24px 0;
      text-align: center;
    }
    .website-card {
      background: rgba(16, 16, 16, 0.75);
      backdrop-filter: blur(20px);
      border: 1px solid var(--border-glass);
      border-radius: var(--radius-xl);
      overflow: hidden;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      transition: all var(--transition-smooth);
      position: relative;
    }
    .website-card:hover {
      border-color: var(--border-glass-hover);
      box-shadow: 0 20px 45px rgba(0, 0, 0, 0.6), var(--shadow-glow);
      transform: translateY(-6px);
    }
    .website-card-banner {
      height: 140px;
      position: relative;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, rgba(28,28,28,0.9), rgba(16,16,16,0.95));
      border-bottom: 1px solid var(--border-glass);
    }
    .website-card-banner .banner-icon {
      font-size: 3.5rem;
      opacity: 0.15;
      color: #ffffff;
      position: absolute;
      right: 15px;
      bottom: 5px;
      transform: rotate(-10deg);
    }
    .project-folder-card {
      background: rgba(16, 16, 16, 0.65);
      backdrop-filter: blur(16px);
      border: 1px solid var(--border-glass);
      border-radius: var(--radius-lg);
      padding: 24px;
      transition: all var(--transition-smooth);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .project-folder-card:hover {
      border-color: rgba(137, 245, 255, 0.5);
      background: rgba(16, 16, 16, 0.85);
      transform: translateY(-4px);
      box-shadow: 0 15px 35px rgba(0,0,0,0.5), 0 0 20px rgba(137, 245, 255, 0.15);
    }
    .tech-pill {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      background: rgba(255, 255, 255, 0.06);
      border: 1px solid rgba(255, 255, 255, 0.1);
      padding: 3px 10px;
      border-radius: 9999px;
      font-size: 0.72rem;
      color: var(--text-secondary);
      font-weight: 500;
    }
    .quick-preview-modal-box {
      width: 95vw;
      max-width: 1300px;
      height: 90vh;
      display: flex;
      flex-direction: column;
      padding: 0;
      overflow: hidden;
      border-radius: 24px;
    }
    .view-tab-btn {
      padding: 10px 24px;
      border-radius: var(--radius-full);
      font-size: 0.95rem;
      font-weight: 700;
      text-decoration: none;
      transition: all var(--transition-fast);
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }
    .view-tab-btn.active {
      background: linear-gradient(135deg, #ffb8e7, #d68cb8);
      color: #000000;
      box-shadow: 0 4px 20px rgba(255,184,231,0.4);
    }
    .view-tab-btn:not(.active) {
      background: rgba(255,255,255,0.05);
      color: var(--text-secondary);
      border: 1px solid var(--border-glass);
    }
    .view-tab-btn:not(.active):hover {
      background: rgba(255,255,255,0.1);
      color: var(--text-primary);
    }
  </style>
</head>
<body>
  <div class="ceo-mesh-bg"></div>

  <!-- 1. Executive Top Navbar -->
  <header class="ceo-container">
    <nav class="ceo-navbar ceo-flex-between animate-fade-up">
      <a href="index.php" class="ceo-logo">
        <div class="logo-icon">
          <i class="fa-solid fa-crown"></i>
        </div>
        <div>
          <span>HIEU<span class="text-gold-gradient">.CEO</span></span>
          <span style="display:block;font-size:0.68rem;letter-spacing:0.12em;color:var(--text-accent);font-weight:600;">PROJECTS EXPLORER</span>
        </div>
      </a>

      <!-- Instant Search Filter -->
      <form action="explore.php" method="GET" style="position:relative;width:340px;" class="hide-mobile">
        <input type="text" name="search" id="live-search-input" value="<?= e($searchQuery) ?>" placeholder="Tìm tên website, ngành nghề, thư mục..." class="glass-input" style="padding-left:38px;padding-top:8px;padding-bottom:8px;font-size:0.88rem;">
        <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:0.85rem;"></i>
        <?php if (!empty($activeCategory) && $activeCategory !== 'all'): ?>
          <input type="hidden" name="category" value="<?= e($activeCategory) ?>">
        <?php endif; ?>
        <?php if ($viewTab !== 'showcase'): ?>
          <input type="hidden" name="tab" value="<?= e($viewTab) ?>">
        <?php endif; ?>
      </form>

      <!-- Action Navigation -->
      <div style="display:flex;align-items:center;gap:12px;">
        <a href="live-view.php" class="btn-ceo-primary btn-ripple" style="padding:8px 18px;font-size:0.85rem;background:linear-gradient(135deg, #ffb8e7, #89f5ff);box-shadow:0 0 20px rgba(255,184,231,0.4);" title="Trình xem live đa thiết bị tương tác">
          <i class="fa-solid fa-play mr-1"></i> Xem Live Dự Án
        </a>

        <button id="btn-theme-mode" class="btn-icon" title="Chuyển chế độ Sáng / Tối">
          <i class="fa-solid fa-circle-half-stroke"></i>
        </button>

        <?php if ($currentUser): ?>
          <div style="display:flex;align-items:center;gap:8px;padding:6px 14px;background:rgba(255,255,255,0.06);border-radius:9999px;border:1px solid var(--border-glass);">
            <a href="user/dashboard.php" style="font-size:0.82rem;color:var(--text-primary);font-weight:600;text-decoration:none;">
              <i class="fa-solid fa-user-circle mr-1" style="color:#34d399;"></i> <?= e($currentUser['full_name']) ?>
            </a>
            <a href="logout.php" style="color:#f87171;font-size:0.78rem;margin-left:4px;" title="Đăng xuất"><i class="fa-solid fa-right-from-bracket"></i></a>
          </div>
        <?php else: ?>
          <a href="user/login.php" class="btn-ceo-secondary" style="padding:8px 16px;font-size:0.85rem;">
            <i class="fa-solid fa-user mr-1"></i> Đăng Nhập Thành Viên
          </a>
        <?php endif; ?>
      </div>
    </nav>
  </header>

  <main class="ceo-container">
    <!-- 2. Hero Showcase Header -->
    <section class="showcase-header animate-fade-up">
      <div style="display:inline-flex;align-items:center;gap:8px;padding:6px 18px;background:rgba(137,245,255,0.12);border:1px solid rgba(137,245,255,0.3);border-radius:9999px;margin-bottom:18px;">
        <span class="animate-beacon" style="width:8px;height:8px;background:#89f5ff;border-radius:50%;display:inline-block;"></span>
        <span style="font-size:0.82rem;font-weight:700;letter-spacing:0.08em;color:#89f5ff;text-transform:uppercase;">
          KHÁM PHÁ CÁC DỰ ÁN TRONG THƯ MỤC PROJECTS/
        </span>
      </div>

      <h1 style="font-size: clamp(2rem, 4.2vw, 3.2rem);font-weight:900;line-height:1.2;margin-bottom:16px;">
        Toàn Bộ <span class="text-shimmer">Dự Án Website Đã Đăng Tải</span><br>
        <span class="text-gold-gradient">Lưu Trữ Trong Thư Mục projects/</span>
      </h1>

      <p style="max-width:800px;margin:0 auto 28px auto;color:var(--text-secondary);font-size:1.05rem;line-height:1.65;">
        Hệ thống tự động quét và hiển thị trực quan toàn bộ các thư mục website thực tế có trong <code>projects/</code>. Bạn có thể mở trực tiếp, chạy thử mô phỏng đa thiết bị (iMac, iPad, iPhone), hoặc xem nhanh mã nguồn và tài khoản thử nghiệm.
      </p>

      <!-- View Switcher Tabs -->
      <div style="display:flex;justify-content:center;gap:12px;margin-bottom:34px;flex-wrap:wrap;">
        <a href="explore.php?tab=showcase<?= $searchQuery ? '&search=' . urlencode($searchQuery) : '' ?>" class="view-tab-btn <?= $viewTab === 'showcase' ? 'active' : '' ?>">
          <i class="fa-solid fa-palette"></i> Bộ Sưu Tập Giao Diện (<?= count($themes) ?> Website)
        </a>
        <a href="explore.php?tab=projects<?= $searchQuery ? '&search=' . urlencode($searchQuery) : '' ?>" class="view-tab-btn <?= $viewTab === 'projects' ? 'active' : '' ?>">
          <i class="fa-solid fa-folder-tree"></i> Kho Thư Mục projects/ (<?= count($projects) ?> Thư Mục Thực Tế)
        </a>
      </div>

      <!-- Quick Metrics Ribbon -->
      <div class="glass-panel" style="padding:16px 28px;max-width:980px;margin:0 auto 36px auto;display:grid;grid-template-columns:repeat(4, 1fr);gap:16px;text-align:center;">
        <div>
          <div style="font-size:0.75rem;color:var(--text-muted);font-weight:700;text-transform:uppercase;">Thư Mục Trong projects/</div>
          <div style="font-size:1.6rem;font-weight:800;color:#89f5ff;margin-top:2px;"><?= count($projects) ?> Dự Án</div>
        </div>
        <div>
          <div style="font-size:0.75rem;color:var(--text-muted);font-weight:700;text-transform:uppercase;">Website Đã Đăng Ký</div>
          <div style="font-size:1.6rem;font-weight:800;color:var(--text-primary);margin-top:2px;"><?= count($themes) ?> Giao Diện</div>
        </div>
        <div>
          <div style="font-size:0.75rem;color:var(--text-muted);font-weight:700;text-transform:uppercase;">Cơ Sở Dữ Liệu</div>
          <div style="font-size:1.6rem;font-weight:800;color:#ffd4f0;margin-top:2px;">Tách Biệt Độc Lập</div>
        </div>
        <div>
          <div style="font-size:0.75rem;color:var(--text-muted);font-weight:700;text-transform:uppercase;">Trạng Thái Vận Hành</div>
          <div style="font-size:1.6rem;font-weight:800;color:#34d399;margin-top:2px;">100% Sẵn Sàng</div>
        </div>
      </div>
    </section>

    <!-- ==================== TAB 1: SHOWCASE VIEW ==================== -->
    <?php if ($viewTab === 'showcase'): ?>
      <!-- Category Filter Tabs -->
      <div style="display:flex;justify-content:center;gap:8px;flex-wrap:wrap;margin-bottom:36px;">
        <a href="explore.php?tab=showcase&category=all" class="badge-ceo <?= $activeCategory === 'all' ? 'badge-active' : 'badge-ready' ?>" style="text-decoration:none;padding:8px 16px;font-size:0.82rem;">
          <i class="fa-solid fa-layer-group mr-1"></i> Tất Cả (<?= count($themes) ?>)
        </a>
        <?php foreach ($allCategories as $cat): ?>
          <a href="explore.php?tab=showcase&category=<?= e($cat['slug']) ?>" class="badge-ceo <?= $activeCategory === $cat['slug'] ? 'badge-active' : 'badge-ready' ?>" style="text-decoration:none;padding:8px 16px;font-size:0.82rem;">
            <i class="fa-solid <?= e($cat['icon']) ?> mr-1"></i> <?= e($cat['name']) ?>
          </a>
        <?php endforeach; ?>
      </div>

      <!-- Website Cards Showcase Grid -->
      <section style="margin-bottom:80px;">
        <div class="ceo-grid-3">
          <?php foreach ($themes as $t): ?>
            <?php $cred = $demoCredentials[$t['code_name']] ?? null; ?>
            <div class="website-card ceo-card-tilt animate-fade-up">
              <!-- Card Header Banner -->
              <div>
                <div class="website-card-banner" style="border-top: 4px solid <?= e($t['primary_color'] ?: '#ffb8e7') ?>;">
                  <div style="text-align:center;position:relative;z-index:2;padding:0 20px;">
                    <span class="badge-ceo badge-gold" style="font-size:0.7rem;margin-bottom:6px;">
                      <i class="fa-solid fa-star mr-1"></i> ĐÁNH GIÁ <?= number_format($t['rating'], 1) ?>/5.0
                    </span>
                    <h4 style="font-size:1.15rem;font-weight:800;color:#ffffff;margin:0;"><?= e($t['name']) ?></h4>
                    <div style="font-size:0.75rem;color:var(--text-accent);font-family:var(--font-mono);margin-top:4px;">
                      Mã: <?= e($t['code_name']) ?>
                    </div>
                  </div>
                  <i class="fa-solid fa-laptop-code banner-icon"></i>
                </div>

                <!-- Card Body Content -->
                <div style="padding:22px;">
                  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                    <span class="badge-ceo <?= $t['status'] === 'active' ? 'badge-active' : 'badge-ready' ?>" style="font-size:0.72rem;">
                      <i class="fa-solid <?= $t['status'] === 'active' ? 'fa-circle-check' : 'fa-circle-play' ?> mr-1"></i>
                      <?= $t['status'] === 'active' ? 'Đang Vận Hành Chính' : 'Sẵn Sàng Trực Tuyến' ?>
                    </span>
                    <span style="font-size:0.78rem;color:#89f5ff;font-weight:600;font-family:var(--font-mono);">
                      <i class="fa-solid fa-folder mr-1"></i> <?= e($t['folder_path']) ?>
                    </span>
                  </div>

                  <p style="color:var(--text-secondary);font-size:0.88rem;line-height:1.6;margin-bottom:18px;min-height:55px;">
                    <?= e($t['tagline'] ?: $t['description']) ?>
                  </p>

                  <!-- Tech Tags -->
                  <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:18px;">
                    <span class="tech-pill"><i class="fa-brands fa-php" style="color:#ffd4f0;"></i> PHP 8.2</span>
                    <span class="tech-pill"><i class="fa-solid fa-database" style="color:#f59e0b;"></i> MySQL</span>
                    <span class="tech-pill"><i class="fa-solid fa-font" style="color:#89f5ff;"></i> <?= e($t['font_family']) ?></span>
                    <span class="tech-pill"><i class="fa-solid fa-mobile-screen" style="color:#10b981;"></i> Responsive</span>
                  </div>

                  <!-- Demo Account Drawer Info if available -->
                  <?php if ($cred): ?>
                    <div style="background:rgba(0,0,0,0.35);border:1px dashed rgba(255,255,255,0.12);border-radius:10px;padding:10px 14px;margin-bottom:18px;font-size:0.78rem;">
                      <div style="color:var(--text-accent);font-weight:700;margin-bottom:4px;display:flex;align-items:center;gap:6px;">
                        <i class="fa-solid fa-key"></i> Tài Khoản Đăng Nhập Thử Nghiệm:
                      </div>
                      <div style="color:var(--text-secondary);">
                        Admin: <strong style="color:var(--text-primary);"><?= e($cred['admin_email']) ?></strong> | Pass: <code style="color:#34d399;"><?= e($cred['admin_pass']) ?></code>
                      </div>
                    </div>
                  <?php endif; ?>
                </div>
              </div>

              <!-- Card Action Footer -->
              <div style="padding:0 22px 22px 22px;">
                <!-- Main Live View Button -->
                <a href="live-view.php?theme_id=<?= $t['id'] ?>" class="btn-ceo-primary btn-ripple" style="width:100%;padding:11px;font-size:0.92rem;margin-bottom:10px;text-align:center;justify-content:center;background:linear-gradient(135deg, #ffb8e7, #d68cb8);box-shadow:0 4px 15px rgba(255,184,231,0.35);">
                  <i class="fa-solid fa-play mr-2"></i> Xem Live Đa Thiết Bị
                </a>

                <!-- Secondary Actions -->
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;">
                  <a href="<?= e($t['preview_url']) ?>" target="_blank" class="btn-ceo-secondary" style="padding:8px;font-size:0.8rem;text-align:center;" title="Mở trang web trực tiếp trong tab mới">
                    <i class="fa-solid fa-arrow-up-right-from-square mr-1" style="color:#89f5ff;"></i> Mở Web
                  </a>
                  <button onclick="openQuickPreview('<?= e($t['preview_url']) ?>', '<?= e($t['name']) ?>')" class="btn-ceo-secondary" style="padding:8px;font-size:0.8rem;text-align:center;" title="Xem nhanh tại trang">
                    <i class="fa-solid fa-eye mr-1"></i> Xem Nhanh
                  </button>
                  <a href="customizer.php?theme_id=<?= $t['id'] ?>" class="btn-ceo-secondary" style="padding:8px;font-size:0.8rem;text-align:center;" title="Tùy biến màu sắc và font">
                    <i class="fa-solid fa-palette mr-1" style="color:var(--ceo-gold);"></i> Tùy Biến
                  </a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </section>

    <!-- ==================== TAB 2: PROJECTS DIRECTORY VIEW ==================== -->
    <?php else: ?>
      <section style="margin-bottom:80px;">
        <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(350px, 1fr));gap:24px;">
          <?php foreach ($projects as $p): ?>
            <?php 
              $themeInfo = $folderThemeMap[$p['folder_name']] ?? null;
              $previewTarget = $p['has_index'] ? 'projects/' . $p['folder_name'] . '/' . $p['entry_file'] : '#';
            ?>
            <div class="project-folder-card animate-fade-up">
              <div>
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:14px;">
                  <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:48px;height:48px;border-radius:12px;background:linear-gradient(135deg, rgba(137,245,255,0.2), rgba(255,184,231,0.2));border:1px solid rgba(137,245,255,0.3);display:flex;align-items:center;justify-content:center;color:#89f5ff;font-size:1.4rem;">
                      <i class="fa-solid fa-folder-open"></i>
                    </div>
                    <div>
                      <h3 style="font-size:1.15rem;font-weight:700;color:var(--text-primary);margin:0;"><?= e($p['folder_name']) ?></h3>
                      <span style="font-size:0.75rem;color:var(--text-muted);font-family:var(--font-mono);">
                        projects/<?= e($p['folder_name']) ?>
                      </span>
                    </div>
                  </div>

                  <span class="badge-ceo <?= $p['is_registered'] ? 'badge-active' : 'badge-ready' ?>" style="font-size:0.7rem;">
                    <?= $p['is_registered'] ? '<i class="fa-solid fa-circle-check mr-1"></i> Đã Đăng Ký' : '<i class="fa-solid fa-circle-pause mr-1"></i> Chưa Đăng Ký' ?>
                  </span>
                </div>

                <!-- Folder Meta Specs -->
                <div style="background:rgba(0,0,0,0.3);border-radius:10px;padding:12px 14px;margin-bottom:16px;display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:0.82rem;">
                  <div>
                    <span style="color:var(--text-muted);display:block;font-size:0.72rem;">Dung Lượng:</span>
                    <strong style="color:var(--text-primary);"><?= e($p['size_formatted']) ?></strong>
                  </div>
                  <div>
                    <span style="color:var(--text-muted);display:block;font-size:0.72rem;">Số Lượng File:</span>
                    <strong style="color:var(--text-primary);"><?= $p['file_count'] ?> tập tin</strong>
                  </div>
                  <div>
                    <span style="color:var(--text-muted);display:block;font-size:0.72rem;">Tệp Khởi Chạy:</span>
                    <strong style="color:#34d399;font-family:var(--font-mono);"><?= e($p['entry_file'] ?: 'Không có') ?></strong>
                  </div>
                  <div>
                    <span style="color:var(--text-muted);display:block;font-size:0.72rem;">Loại Dự Án:</span>
                    <strong style="color:var(--text-accent);"><?= $p['has_php'] ? 'PHP & MySQL' : 'HTML5/JS' ?></strong>
                  </div>
                </div>

                <?php if (!empty($p['readme_title'])): ?>
                  <p style="color:var(--text-secondary);font-size:0.85rem;line-height:1.5;margin-bottom:18px;">
                    <i class="fa-solid fa-book-open mr-1" style="color:var(--text-accent);"></i> <?= e($p['readme_title']) ?>
                  </p>
                <?php endif; ?>
              </div>

              <!-- Folder Actions -->
              <div>
                <?php if ($p['has_index']): ?>
                  <a href="<?= e($previewTarget) ?>" target="_blank" class="btn-ceo-primary btn-ripple" style="width:100%;padding:10px;font-size:0.88rem;text-align:center;margin-bottom:8px;">
                    <i class="fa-solid fa-rocket mr-1"></i> Mở Trang Web Trực Tiếp
                  </a>

                  <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    <button onclick="openQuickPreview('<?= e($previewTarget) ?>', '<?= e($p['folder_name']) ?>')" class="btn-ceo-secondary" style="padding:8px;font-size:0.8rem;text-align:center;">
                      <i class="fa-solid fa-eye mr-1"></i> Xem Nhanh
                    </button>
                    <?php if ($themeInfo): ?>
                      <a href="theme-preview.php?theme_id=<?= $themeInfo['id'] ?>" class="btn-ceo-secondary" style="padding:8px;font-size:0.8rem;text-align:center;">
                        <i class="fa-solid fa-desktop mr-1"></i> Giả Lập
                      </a>
                    <?php else: ?>
                      <a href="admin/projects.php" class="btn-ceo-secondary" style="padding:8px;font-size:0.8rem;text-align:center;">
                        <i class="fa-solid fa-plus mr-1"></i> Đăng Ký
                      </a>
                    <?php endif; ?>
                  </div>
                <?php else: ?>
                  <button class="btn-ceo-secondary" style="width:100%;padding:10px;opacity:0.5;cursor:not-allowed;" disabled>
                    <i class="fa-solid fa-triangle-exclamation mr-1"></i> Thiếu tệp index.php / index.html
                  </button>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endif; ?>

    <!-- 5. Quick Upload Banner CTA -->
    <section style="margin-bottom:80px;">
      <div class="glass-panel" style="padding:40px;background:linear-gradient(135deg, rgba(28,28,28,0.8), rgba(16,16,16,0.95));border:1px solid var(--border-glass-hover);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:24px;">
        <div style="max-width:650px;">
          <span class="badge-ceo badge-gold" style="margin-bottom:12px;">MỞ RỘNG HỆ SINH THÁI</span>
          <h2 style="font-size:1.8rem;font-weight:800;margin-bottom:10px;">Bạn Muốn Tải Lên Thêm Dự Án Mới?</h2>
          <p style="color:var(--text-secondary);font-size:0.95rem;line-height:1.6;">
            Hệ thống hỗ trợ nén tệp <code>.ZIP</code> của bất kỳ website nào và tự động giải nén vào thư mục <code>projects/</code> để hiển thị trực tiếp lên cổng trải nghiệm này.
          </p>
        </div>

        <div style="display:flex;gap:14px;flex-wrap:wrap;">
          <a href="admin/project-upload.php" class="btn-ceo-primary btn-ripple" style="padding:14px 28px;font-size:1rem;">
            <i class="fa-solid fa-cloud-arrow-up mr-2"></i> Tải Lên Dự Án Ngay
          </a>
          <a href="admin/projects.php" class="btn-ceo-secondary" style="padding:14px 24px;font-size:1rem;">
            <i class="fa-solid fa-folder-tree mr-2"></i> Quản Lý Thư Mục projects/
          </a>
        </div>
      </div>
    </section>
  </main>

  <!-- 6. Quick Interactive Viewport Modal -->
  <div id="quick-preview-modal" class="modal-overlay">
    <div class="modal-box quick-preview-modal-box">
      <!-- Modal Header -->
      <div style="padding:14px 24px;background:#0a0a0a;border-bottom:1px solid var(--border-glass);display:flex;align-items:center;justify-content:space-between;">
        <div style="display:flex;align-items:center;gap:12px;">
          <span class="animate-beacon" style="width:10px;height:10px;background:#10b981;border-radius:50%;"></span>
          <strong id="modal-theme-title" style="font-size:1rem;color:var(--text-primary);">Xem Nhanh Website</strong>
        </div>

        <div style="display:flex;align-items:center;gap:10px;">
          <a id="modal-external-link" href="#" target="_blank" class="btn-ceo-secondary" style="padding:6px 14px;font-size:0.8rem;">
            <i class="fa-solid fa-up-right-from-square mr-1"></i> Mở Cửa Sổ Riêng
          </a>
          <button onclick="closeQuickPreview()" class="btn-icon" style="width:34px;height:34px;">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>
      </div>

      <!-- Iframe Screen -->
      <iframe id="modal-preview-iframe" src="" style="flex:1;width:100%;height:100%;border:none;background:#ffffff;" title="Quick Preview"></iframe>
    </div>
  </div>

  <!-- 7. Master Footer -->
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
        <a href="index.php" style="color:var(--text-secondary);text-decoration:none;">Trang Chủ</a>
        <a href="explore.php" style="color:var(--text-secondary);text-decoration:none;">Cổng Khám Phá Website</a>
        <a href="user/dashboard.php" style="color:var(--text-secondary);text-decoration:none;">Trang Thành Viên</a>
        <a href="test_system.php" style="color:var(--text-secondary);text-decoration:none;" target="_blank">Kiểm Thử Hệ Thống</a>
      </div>
    </div>
  </footer>

  <script src="assets/js/ceo-app.js"></script>
  <script>
    function openQuickPreview(url, title) {
      document.getElementById('modal-theme-title').innerText = title;
      document.getElementById('modal-preview-iframe').src = url;
      document.getElementById('modal-external-link').href = url;
      document.getElementById('quick-preview-modal').classList.add('active');
    }

    function closeQuickPreview() {
      document.getElementById('quick-preview-modal').classList.remove('active');
      document.getElementById('modal-preview-iframe').src = '';
    }
  </script>
</body>
</html>
