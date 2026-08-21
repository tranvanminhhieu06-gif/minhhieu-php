<?php
/**
 * HIEU CEO - Executive Team & RBAC Management
 */

require_once __DIR__ . '/../config/auth_admin.php';
requireAdminAuth(['ceo']);

$currentUser = getAdminUser();

$db = getDb();
$users = $db->query("SELECT * FROM `users` ORDER BY `id` ASC")->fetchAll();
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="vi" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Phân Quyền Ban Điều Hành - HIEU CEO</title>
  
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
        <li><a href="users.php" class="active"><i class="fa-solid fa-user-shield"></i> Phân Quyền Ban ĐH</a></li>
        <li><a href="logs.php"><i class="fa-solid fa-clock-rotate-left"></i> Nhật Ký Hoạt Động</a></li>
        <li><a href="settings.php"><i class="fa-solid fa-sliders"></i> Cài Đặt Hệ Thống</a></li>
      </ul>
    </aside>

    <main class="admin-main">
      <div class="ceo-flex-between" style="margin-bottom:30px;">
        <div>
          <h1 style="font-size:1.8rem;font-weight:800;margin-bottom:4px;">Ban Điều Hành & Phân Quyền RBAC</h1>
          <p style="color:var(--text-secondary);font-size:0.92rem;">Quản lý tài khoản và cấp quyền truy cập hệ thống theo cấp bậc</p>
        </div>
      </div>

      <div class="glass-panel" style="padding:0;overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
          <thead>
            <tr style="background:rgba(28,28,28,0.6);border-bottom:1px solid var(--border-glass);color:var(--text-muted);font-size:0.8rem;text-transform:uppercase;">
              <th style="padding:14px 18px;text-align:left;">Thành Viên</th>
              <th style="padding:14px 18px;text-align:left;">Email</th>
              <th style="padding:14px 18px;text-align:center;">Vai Trò (Role)</th>
              <th style="padding:14px 18px;text-align:center;">Trạng Thái</th>
              <th style="padding:14px 18px;text-align:right;">Đăng Nhập Cuối</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $u): ?>
              <tr style="border-bottom:1px solid rgba(255,255,255,0.05);font-size:0.9rem;">
                <td style="padding:16px 18px;display:flex;align-items:center;gap:12px;">
                  <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#ffb8e7,#d68cb8);display:flex;align-items:center;justify-content:center;font-weight:700;color:#000;">
                    <?= substr($u['full_name'], 0, 1) ?>
                  </div>
                  <div>
                    <div style="font-weight:700;color:var(--text-primary);"><?= e($u['full_name']) ?></div>
                    <div style="font-size:0.75rem;color:var(--text-muted);"><?= e($u['title']) ?></div>
                  </div>
                </td>
                <td style="padding:16px 18px;color:var(--text-secondary);font-family:var(--font-mono);"><?= e($u['email']) ?></td>
                <td style="padding:16px 18px;text-align:center;">
                  <span class="badge-ceo <?= $u['role'] === 'ceo' ? 'badge-gold' : ($u['role'] === 'cdo' ? 'badge-active' : 'badge-ready') ?>">
                    <?= strtoupper($u['role']) ?>
                  </span>
                </td>
                <td style="padding:16px 18px;text-align:center;">
                  <span class="badge-ceo badge-active">Hoạt Động</span>
                </td>
                <td style="padding:16px 18px;text-align:right;color:var(--text-muted);font-size:0.85rem;">
                  <?= timeAgo($u['last_login']) ?>
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
