<?php
/**
 * HIEU CEO - UI Components Library & Kit
 */

require_once __DIR__ . '/../config/auth_admin.php';

$currentUser = getAdminUser();

$db = getDb();
$components = $db->query("SELECT * FROM `ui_components` ORDER BY `id` ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kho Linh Kiện UI Kit - HIEU CEO</title>
  
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
    .component-box { background: rgba(16, 16, 16, 0.7); border: 1px solid var(--border-glass); border-radius: var(--radius-lg); margin-bottom: 30px; overflow: hidden; }
    .component-preview { padding: 36px; background: rgba(7, 9, 19, 0.5); display: flex; align-items: center; justify-content: center; min-height: 150px; border-bottom: 1px solid var(--border-glass); }
    .code-preview-block { background: #000000; padding: 18px; font-family: var(--font-mono); font-size: 0.8rem; color: #89f5ff; overflow-x: auto; white-space: pre-wrap; margin: 0; }
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
        <li><a href="components.php" class="active"><i class="fa-solid fa-cube"></i> Thư Viện UI Kit</a></li>
        <li><a href="analytics.php"><i class="fa-solid fa-arrow-trend-up"></i> Phân Tích & A/B Test</a></li>
        <li><a href="users.php"><i class="fa-solid fa-user-shield"></i> Phân Quyền Ban ĐH</a></li>
        <li><a href="logs.php"><i class="fa-solid fa-clock-rotate-left"></i> Nhật Ký Hoạt Động</a></li>
        <li><a href="settings.php"><i class="fa-solid fa-sliders"></i> Cài Đặt Hệ Thống</a></li>
      </ul>
    </aside>

    <main class="admin-main">
      <div class="ceo-flex-between" style="margin-bottom:30px;">
        <div>
          <h1 style="font-size:1.8rem;font-weight:800;margin-bottom:4px;">Thư Viện Linh Kiện UI Kit</h1>
          <p style="color:var(--text-secondary);font-size:0.92rem;">Các thành phần giao diện chuẩn điều hành sẵn sàng sao chép và nhúng vào dự án</p>
        </div>
      </div>

      <?php foreach ($components as $c): ?>
        <div class="component-box animate-fade-up">
          <div style="padding:16px 24px;background:rgba(28,28,28,0.4);border-bottom:1px solid var(--border-glass);display:flex;align-items:center;justify-content:space-between;">
            <div>
              <span class="badge-ceo badge-active" style="font-size:0.72rem;margin-bottom:4px;"><?= e($c['category']) ?></span>
              <h3 style="font-size:1.15rem;font-weight:700;margin:0;"><?= e($c['name']) ?></h3>
            </div>
            <button onclick="copyCode('code-<?= $c['id'] ?>')" class="btn-ceo-secondary" style="padding:6px 14px;font-size:0.82rem;">
              <i class="fa-solid fa-copy mr-1"></i> Sao Chép Mã
            </button>
          </div>

          <!-- Live Visual Component Display -->
          <div class="component-preview">
            <?php if (!empty($c['css_code'])): ?>
              <style><?= $c['css_code'] ?></style>
            <?php endif; ?>
            <?= $c['html_code'] ?>
          </div>

          <!-- Code Snippet Area -->
          <pre id="code-<?= $c['id'] ?>" class="code-preview-block"><?= e($c['html_code']) ?><?php if (!empty($c['css_code'])): ?>

/* CSS Styles */
<?= e($c['css_code']) ?><?php endif; ?></pre>
        </div>
      <?php endforeach; ?>
    </main>
  </div>

  <script src="../assets/js/ceo-app.js"></script>
  <script>
    function copyCode(elemId) {
      const text = document.getElementById(elemId).innerText;
      navigator.clipboard.writeText(text).then(() => {
        showToast('Đã sao chép mã nguồn vào bộ nhớ tạm!', 'success');
      });
    }
  </script>
</body>
</html>
