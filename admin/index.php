<?php
/**
 * HIEU CEO - Executive Admin Dashboard
 * CEO Master Command & Analytics Center
 */

require_once __DIR__ . '/../config/auth_admin.php';

$currentUser = getAdminUser();
$db = getDb();

// Fetch metrics
$metrics = $db->query("SELECT * FROM `ceo_metrics`")->fetchAll();
$themes = getAllThemes();
$activeTheme = getActiveTheme();
$recentLogs = $db->query("SELECT l.*, u.full_name, u.role FROM `system_logs` l LEFT JOIN `users` u ON l.user_id = u.id ORDER BY l.created_at DESC LIMIT 6")->fetchAll();
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="vi" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bảng Điều Khiển CEO - HIEU CEO Theme Hub</title>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/ceo-core.css">
  <link rel="stylesheet" href="../assets/css/animations.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    .admin-layout {
      display: grid;
      grid-template-columns: 260px 1fr;
      min-height: 100vh;
    }
    .admin-sidebar {
      background: #090d16;
      border-right: 1px solid var(--border-glass);
      padding: 24px 18px;
      display: flex;
      flex-direction: column;
    }
    .sidebar-menu {
      list-style: none;
      margin-top: 30px;
      display: flex;
      flex-direction: column;
      gap: 6px;
      flex: 1;
    }
    .sidebar-menu a {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 16px;
      color: var(--text-secondary);
      text-decoration: none;
      border-radius: var(--radius-md);
      font-weight: 600;
      font-size: 0.92rem;
      transition: all var(--transition-fast);
    }
    .sidebar-menu a:hover, .sidebar-menu a.active {
      background: rgba(99, 102, 241, 0.15);
      color: #818cf8;
      border-left: 3px solid #6366f1;
    }
    .admin-main {
      padding: 30px 40px;
      overflow-y: auto;
    }
    .admin-topbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 36px;
    }
    @media (max-width: 1024px) {
      .admin-layout { grid-template-columns: 1fr; }
      .admin-sidebar { display: none; }
      .admin-main { padding: 20px; }
    }
  </style>
</head>
<body>
  <div class="ceo-mesh-bg"></div>

  <div class="admin-layout">
    <!-- 1. Left Sidebar Navigation -->
    <aside class="admin-sidebar">
      <a href="../index.php" class="ceo-logo" style="margin-bottom:10px;">
        <div class="logo-icon">
          <i class="fa-solid fa-crown"></i>
        </div>
        <span>HIEU<span class="text-gold-gradient">.CEO</span></span>
      </a>

      <ul class="sidebar-menu">
        <li>
          <a href="index.php" class="active">
            <i class="fa-solid fa-chart-pie"></i> Bảng Điều Khiển CEO
          </a>
        </li>
        <li>
          <a href="themes.php">
            <i class="fa-solid fa-layer-group"></i> Quản Lý Giao Diện
          </a>
        </li>
        <li>
          <a href="theme-add.php">
            <i class="fa-solid fa-plus-circle"></i> Thêm Giao Diện Mới
          </a>
        </li>
        <li>
          <a href="components.php">
            <i class="fa-solid fa-cube"></i> Thư Viện UI Kit
          </a>
        </li>
        <li>
          <a href="analytics.php">
            <i class="fa-solid fa-arrow-trend-up"></i> Phân Tích & A/B Test
          </a>
        </li>
        <li>
          <a href="users.php">
            <i class="fa-solid fa-user-shield"></i> Phân Quyền Ban ĐH
          </a>
        </li>
        <li>
          <a href="logs.php">
            <i class="fa-solid fa-clock-rotate-left"></i> Nhật Ký Hoạt Động
          </a>
        </li>
        <li>
          <a href="settings.php">
            <i class="fa-solid fa-sliders"></i> Cài Đặt Hệ Thống
          </a>
        </li>
      </ul>

      <!-- User Profile Badge -->
      <div class="glass-card" style="padding:14px;display:flex;align-items:center;gap:12px;margin-top:auto;">
        <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#ec4899);display:flex;align-items:center;justify-content:center;font-weight:700;">
          <?= substr($currentUser['full_name'], 0, 1) ?>
        </div>
        <div style="flex:1;overflow:hidden;">
          <div style="font-size:0.85rem;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= e($currentUser['full_name']) ?></div>
          <div style="font-size:0.75rem;color:#818cf8;text-transform:uppercase;"><?= e($currentUser['role']) ?></div>
        </div>
        <a href="../logout.php" style="color:var(--text-muted);" title="Đăng xuất"><i class="fa-solid fa-power-off"></i></a>
      </div>
    </aside>

    <!-- 2. Main Executive Content -->
    <main class="admin-main">
      <!-- Topbar -->
      <header class="admin-topbar">
        <div>
          <h1 style="font-size:1.85rem;font-weight:800;margin-bottom:4px;">
            Tổng Quan Chiến Lược <span class="text-gold-gradient">CEO</span>
          </h1>
          <p style="color:var(--text-secondary);font-size:0.92rem;">
            Chào mừng <?= e($currentUser['full_name']) ?> • Phiên bản hệ thống 3.0.0 Pro Enterprise
          </p>
        </div>

        <div style="display:flex;align-items:center;gap:14px;">
          <a href="../index.php" class="btn-ceo-secondary" target="_blank">
            <i class="fa-solid fa-globe mr-1"></i> Xem Storefront
          </a>
          <a href="theme-add.php" class="btn-ceo-primary btn-ripple">
            <i class="fa-solid fa-plus mr-1"></i> Thêm Giao Diện
          </a>
        </div>
      </header>

      <?php if (!empty($flash['success'])): ?>
        <div style="background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.35);color:#34d399;padding:14px 20px;border-radius:var(--radius-md);margin-bottom:24px;display:flex;align-items:center;gap:12px;" class="animate-fade-up">
          <i class="fa-solid fa-circle-check"></i>
          <span><?= e($flash['success']) ?></span>
        </div>
      <?php endif; ?>

      <!-- 3. CEO Strategic KPI Cards -->
      <div class="ceo-grid-4" style="margin-bottom:32px;">
        <?php foreach ($metrics as $m): ?>
          <div class="glass-card ceo-card-tilt animate-fade-up">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
              <span style="font-size:0.82rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;">
                <?= e($m['metric_title']) ?>
              </span>
              <div style="width:36px;height:36px;background:rgba(99,102,241,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;color:#818cf8;">
                <i class="fa-solid <?= e($m['metric_icon']) ?>"></i>
              </div>
            </div>
            <div style="font-size:1.85rem;font-weight:800;margin-bottom:8px;color:var(--text-primary);">
              <?= e($m['current_value']) ?>
            </div>
            <div style="display:flex;align-items:center;gap:6px;font-size:0.8rem;color:<?= $m['is_positive'] ? '#34d399' : '#f43f5e' ?>;">
              <i class="fa-solid <?= $m['is_positive'] ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' ?>"></i>
              <strong><?= e($m['change_percent']) ?></strong> so với tháng trước
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- 4. Real-time Charts & Active Theme Spotlight -->
      <div style="display:grid;grid-template-columns:1.6fr 1fr;gap:28px;margin-bottom:36px;">
        <!-- Left: Traffic & Conversion Chart -->
        <div class="glass-panel" style="padding:28px;">
          <div class="ceo-flex-between" style="margin-bottom:20px;">
            <h3 style="font-size:1.15rem;font-weight:700;">Biểu Đồ Lưu Lượng & Tương Tác 7 Ngày</h3>
            <span class="badge-ceo badge-active">Live Telemetry</span>
          </div>
          <div style="height:280px;">
            <canvas id="ceoTrafficChart"></canvas>
          </div>
        </div>

        <!-- Right: Active Theme Status Card -->
        <div class="glass-panel" style="padding:28px;display:flex;flex-direction:column;justify-content:space-between;">
          <div>
            <div class="ceo-flex-between" style="margin-bottom:14px;">
              <span class="badge-ceo badge-gold">ĐANG VẬN HÀNH</span>
              <span style="font-size:0.8rem;color:var(--text-muted);"><?= e($activeTheme['category_name']) ?></span>
            </div>
            <h3 style="font-size:1.35rem;font-weight:800;margin-bottom:10px;"><?= e($activeTheme['name']) ?></h3>
            <p style="color:var(--text-secondary);font-size:0.88rem;line-height:1.55;margin-bottom:20px;">
              <?= e($activeTheme['tagline'] ?: $activeTheme['description']) ?>
            </p>
          </div>

          <div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px;">
              <a href="../theme-preview.php?theme_id=<?= $activeTheme['id'] ?>" class="btn-ceo-primary" style="padding:10px;font-size:0.85rem;text-align:center;">
                <i class="fa-solid fa-desktop mr-1"></i> Xem Mô Phỏng
              </a>
              <a href="../customizer.php?theme_id=<?= $activeTheme['id'] ?>" class="btn-ceo-secondary" style="padding:10px;font-size:0.85rem;text-align:center;">
                <i class="fa-solid fa-wand-magic-sparkles mr-1" style="color:var(--ceo-gold);"></i> Tùy Biến
              </a>
            </div>
            <div style="font-size:0.78rem;color:var(--text-muted);text-align:center;">
              Mã: <code><?= e($activeTheme['code_name']) ?></code> | Thư mục: <code><?= e($activeTheme['folder_path']) ?></code>
            </div>
          </div>
        </div>
      </div>

      <!-- 5. Quick Theme Switcher & Recent Audit Logs -->
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:28px;">
        <!-- Quick Switcher Table -->
        <div class="glass-panel" style="padding:28px;">
          <div class="ceo-flex-between" style="margin-bottom:18px;">
            <h3 style="font-size:1.15rem;font-weight:700;">Giao Diện Trọng Điểm</h3>
            <a href="themes.php" style="color:var(--text-accent);font-size:0.85rem;text-decoration:none;">Xem tất cả (<?= count($themes) ?>) →</a>
          </div>

          <div style="display:flex;flex-direction:column;gap:12px;">
            <?php foreach (array_slice($themes, 0, 4) as $t): ?>
              <div class="glass-card" style="padding:12px 16px;display:flex;align-items:center;justify-content:space-between;">
                <div>
                  <div style="font-weight:700;font-size:0.92rem;"><?= e($t['name']) ?></div>
                  <div style="font-size:0.78rem;color:var(--text-muted);"><?= e($t['category_name']) ?> • ⭐ <?= number_format($t['rating'], 1) ?></div>
                </div>

                <div>
                  <?php if ($t['status'] === 'active'): ?>
                    <span class="badge-ceo badge-active" style="font-size:0.75rem;">Đang Chạy</span>
                  <?php else: ?>
                    <button class="btn-ceo-primary btn-activate-theme" data-theme-id="<?= $t['id'] ?>" data-theme-name="<?= e($t['name']) ?>" style="padding:6px 14px;font-size:0.78rem;">
                      Kích Hoạt
                    </button>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Audit Logs Stream -->
        <div class="glass-panel" style="padding:28px;">
          <div class="ceo-flex-between" style="margin-bottom:18px;">
            <h3 style="font-size:1.15rem;font-weight:700;">Nhật Ký Điều Hành Gần Đây</h3>
            <a href="logs.php" style="color:var(--text-accent);font-size:0.85rem;text-decoration:none;">Xem toàn bộ →</a>
          </div>

          <div style="display:flex;flex-direction:column;gap:12px;">
            <?php foreach ($recentLogs as $log): ?>
              <div style="display:flex;align-items:flex-start;gap:12px;padding-bottom:10px;border-bottom:1px solid rgba(255,255,255,0.05);font-size:0.85rem;">
                <div style="width:8px;height:8px;border-radius:50%;background:#6366f1;margin-top:6px;"></div>
                <div style="flex:1;">
                  <div style="color:var(--text-primary);font-weight:500;"><?= e($log['description']) ?></div>
                  <div style="color:var(--text-muted);font-size:0.75rem;margin-top:2px;">
                    <?= timeAgo($log['created_at']) ?> • IP: <?= e($log['ip_address']) ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script src="../assets/js/ceo-app.js"></script>
  <script>
    // Render Chart.js Telemetry
    const ctx = document.getElementById('ceoTrafficChart').getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['6 Ngày Trước', '5 Ngày Trước', '4 Ngày Trước', '3 Ngày Trước', '2 Ngày Trước', 'Hôm Qua', 'Hôm Nay'],
        datasets: [{
          label: 'Lượt Xem Giao Diện (Pageviews)',
          data: [12500, 14200, 15800, 18900, 21400, 24600, 28400],
          borderColor: '#6366f1',
          backgroundColor: 'rgba(99, 102, 241, 0.15)',
          fill: true,
          tension: 0.4,
          borderWidth: 3
        }, {
          label: 'Lượng Khách Duy Nhất (Visitors)',
          data: [8900, 9800, 11200, 13400, 15200, 17800, 20500],
          borderColor: '#ec4899',
          backgroundColor: 'transparent',
          tension: 0.4,
          borderWidth: 2,
          borderDash: [5, 5]
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { labels: { color: '#94a3b8', font: { family: 'Outfit', size: 12 } } }
        },
        scales: {
          x: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#64748b' } },
          y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#64748b' } }
        }
      }
    });
  </script>
</body>
</html>
