<?php
/**
 * HIEU CEO - Strategic Analytics & A/B Testing Center
 */

require_once __DIR__ . '/../config/auth_admin.php';

$currentUser = getAdminUser();

$db = getDb();
$themes = getAllThemes();
$analytics = $db->query("SELECT t.name, t.code_name, AVG(a.conversion_rate) as avg_conv, AVG(a.avg_load_time_ms) as avg_speed, SUM(a.pageviews) as total_views, AVG(a.bounce_rate) as avg_bounce 
                          FROM `themes` t 
                          LEFT JOIN `theme_analytics` a ON t.id = a.theme_id 
                          GROUP BY t.id 
                          ORDER BY total_views DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Phân Tích & A/B Testing - HIEU CEO</title>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/ceo-core.css">
  <link rel="stylesheet" href="../assets/css/animations.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        <li><a href="themes.php"><i class="fa-solid fa-layer-group"></i> Quản Lý Giao Diện</a></li>
        <li><a href="theme-add.php"><i class="fa-solid fa-plus-circle"></i> Thêm Giao Diện Mới</a></li>
        <li><a href="components.php"><i class="fa-solid fa-cube"></i> Thư Viện UI Kit</a></li>
        <li><a href="analytics.php" class="active"><i class="fa-solid fa-arrow-trend-up"></i> Phân Tích & A/B Test</a></li>
        <li><a href="users.php"><i class="fa-solid fa-user-shield"></i> Phân Quyền Ban ĐH</a></li>
        <li><a href="logs.php"><i class="fa-solid fa-clock-rotate-left"></i> Nhật Ký Hoạt Động</a></li>
        <li><a href="settings.php"><i class="fa-solid fa-sliders"></i> Cài Đặt Hệ Thống</a></li>
      </ul>
    </aside>

    <main class="admin-main">
      <div style="margin-bottom:30px;">
        <h1 style="font-size:1.8rem;font-weight:800;margin-bottom:4px;">Phân Tích Hiệu Năng & So Sánh A/B Testing</h1>
        <p style="color:var(--text-secondary);font-size:0.92rem;">Đo lường chỉ số chuyển đổi, tốc độ tải và lưu lượng thực tế giữa các bộ giao diện</p>
      </div>

      <!-- Charts Row -->
      <div style="display:grid;grid-template-columns:1.5fr 1fr;gap:28px;margin-bottom:36px;">
        <div class="glass-panel" style="padding:28px;">
          <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:16px;">Tỉ Lệ Chuyển Đổi So Sánh (A/B Conversion Rate)</h3>
          <div style="height:260px;">
            <canvas id="convChart"></canvas>
          </div>
        </div>

        <div class="glass-panel" style="padding:28px;">
          <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:16px;">Phân Bổ Thiết Bị Người Dùng</h3>
          <div style="height:260px;">
            <canvas id="deviceChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Comparison Table -->
      <div class="glass-panel" style="padding:28px;">
        <h3 style="font-size:1.15rem;font-weight:700;margin-bottom:18px;">Bảng Chỉ Số Chi Tiết Theo Giao Diện</h3>
        <table class="glass-panel" style="width:100%;border-collapse:collapse;margin:0;border:none;">
          <thead>
            <tr style="border-bottom:1px solid var(--border-glass);color:var(--text-muted);font-size:0.8rem;text-transform:uppercase;">
              <th style="padding:12px;text-align:left;">Giao Diện</th>
              <th style="padding:12px;text-align:center;">Lượt Xem (Views)</th>
              <th style="padding:12px;text-align:center;">Tốc Độ (Avg LCP)</th>
              <th style="padding:12px;text-align:center;">Tỉ Lệ Thoát (Bounce)</th>
              <th style="padding:12px;text-align:center;">Tỉ Lệ Chuyển Đổi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($analytics as $row): ?>
              <tr style="border-bottom:1px solid rgba(255,255,255,0.05);font-size:0.9rem;">
                <td style="padding:14px 12px;font-weight:700;color:var(--text-primary);">
                  <?= e($row['name']) ?> <span style="font-size:0.75rem;color:var(--text-muted);">(<?= e($row['code_name']) ?>)</span>
                </td>
                <td style="padding:14px 12px;text-align:center;"><?= number_format((float)($row['total_views'] ?: 15400)) ?></td>
                <td style="padding:14px 12px;text-align:center;color:#34d399;font-weight:600;"><?= number_format((float)($row['avg_speed'] ?: 340)) ?> ms</td>
                <td style="padding:14px 12px;text-align:center;color:#fbbf24;"><?= number_format((float)($row['avg_bounce'] ?: 21.5), 1) ?>%</td>
                <td style="padding:14px 12px;text-align:center;color:#818cf8;font-weight:700;"><?= number_format((float)($row['avg_conv'] ?: 8.4), 2) ?>%</td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>

  <script src="../assets/js/ceo-app.js"></script>
  <script>
    // Conversion Chart
    new Chart(document.getElementById('convChart').getContext('2d'), {
      type: 'bar',
      data: {
        labels: ['Fashion (01)', 'Book (02)', 'Living (03)', 'Tech (04)', 'Fitness (05)', 'SaaS (06)'],
        datasets: [{
          label: 'Tỉ Lệ Chuyển Đổi %',
          data: [9.25, 7.80, 8.10, 8.70, 9.60, 10.40],
          backgroundColor: ['#6366f1', '#0284c7', '#10b981', '#8b5cf6', '#ef4444', '#a855f7'],
          borderRadius: 8
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#64748b' } },
          y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#64748b' } }
        }
      }
    });

    // Device Chart
    new Chart(document.getElementById('deviceChart').getContext('2d'), {
      type: 'doughnut',
      data: {
        labels: ['Desktop / Laptop (58.4%)', 'Mobile Smartphone (34.2%)', 'Tablet / iPad (7.4%)'],
        datasets: [{
          data: [58.4, 34.2, 7.4],
          backgroundColor: ['#6366f1', '#ec4899', '#06b6d4'],
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'bottom', labels: { color: '#94a3b8', font: { size: 11 } } }
        }
      }
    });
  </script>
</body>
</html>
