<?php
/**
 * HIEU CEO - System Audit Trail & Logs
 */

require_once __DIR__ . '/../config/auth_admin.php';

$currentUser = getAdminUser();

$db = getDb();
$logs = $db->query("SELECT l.*, u.full_name, u.role FROM `system_logs` l LEFT JOIN `users` u ON l.user_id = u.id ORDER BY l.created_at DESC LIMIT 50")->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nhật Ký Kiểm Toán - HIEU CEO</title>
  
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
        <li><a href="themes.php"><i class="fa-solid fa-layer-group"></i> Quản Lý Giao Diện</a></li>
        <li><a href="theme-add.php"><i class="fa-solid fa-plus-circle"></i> Thêm Giao Diện Mới</a></li>
        <li><a href="components.php"><i class="fa-solid fa-cube"></i> Thư Viện UI Kit</a></li>
        <li><a href="analytics.php"><i class="fa-solid fa-arrow-trend-up"></i> Phân Tích & A/B Test</a></li>
        <li><a href="users.php"><i class="fa-solid fa-user-shield"></i> Phân Quyền Ban ĐH</a></li>
        <li><a href="logs.php" class="active"><i class="fa-solid fa-clock-rotate-left"></i> Nhật Ký Hoạt Động</a></li>
        <li><a href="settings.php"><i class="fa-solid fa-sliders"></i> Cài Đặt Hệ Thống</a></li>
      </ul>
    </aside>

    <main class="admin-main">
      <div style="margin-bottom:30px;">
        <h1 style="font-size:1.8rem;font-weight:800;margin-bottom:4px;">Nhật Ký Kiểm Toán & Triển Khai</h1>
        <p style="color:var(--text-secondary);font-size:0.92rem;">Theo dõi toàn bộ thao tác bảo mật, kích hoạt giao diện và thay đổi hệ thống</p>
      </div>

      <div class="glass-panel" style="padding:0;overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
          <thead>
            <tr style="background:rgba(30,41,59,0.6);border-bottom:1px solid var(--border-glass);color:var(--text-muted);font-size:0.8rem;text-transform:uppercase;">
              <th style="padding:14px 18px;text-align:left;">Hành Động</th>
              <th style="padding:14px 18px;text-align:left;">Nội Dung Chi Tiết</th>
              <th style="padding:14px 18px;text-align:left;">Người Thực Hiện</th>
              <th style="padding:14px 18px;text-align:center;">Địa Chỉ IP</th>
              <th style="padding:14px 18px;text-align:right;">Thời Gian</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($logs as $l): ?>
              <tr style="border-bottom:1px solid rgba(255,255,255,0.05);font-size:0.9rem;">
                <td style="padding:16px 18px;">
                  <span class="badge-ceo badge-ready" style="font-size:0.75rem;font-family:var(--font-mono);"><?= e($l['action_type']) ?></span>
                </td>
                <td style="padding:16px 18px;color:var(--text-primary);font-weight:500;"><?= e($l['description']) ?></td>
                <td style="padding:16px 18px;color:var(--text-secondary);">
                  <?= e($l['full_name'] ?: 'Hệ Thống Tự Động') ?>
                  <?php if (!empty($l['role'])): ?>
                    <span style="font-size:0.75rem;color:var(--text-muted);">(<?= strtoupper($l['role']) ?>)</span>
                  <?php endif; ?>
                </td>
                <td style="padding:16px 18px;text-align:center;font-family:var(--font-mono);color:var(--text-muted);font-size:0.8rem;">
                  <?= e($l['ip_address']) ?>
                </td>
                <td style="padding:16px 18px;text-align:right;color:var(--text-muted);font-size:0.85rem;">
                  <?= date('d/m/Y H:i:s', strtotime($l['created_at'])) ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
  <script src="../assets/js/ceo-app.js"></script>
</body>
</html>
