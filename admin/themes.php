<?php
/**
 * HIEU CEO - Master Themes Management
 */

require_once __DIR__ . '/../config/auth_admin.php';

$currentUser = getAdminUser();
$category = sanitize($_GET['category'] ?? 'all');
$search = sanitize($_GET['search'] ?? '');
$themes = getAllThemes($category, $search);
$categories = getAllCategories();
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="vi" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quản Lý Giao Diện - HIEU CEO Theme Hub</title>
  
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
    .theme-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    .theme-table th { text-align: left; padding: 14px 18px; background: rgba(28, 28, 28, 0.6); color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--border-glass); }
    .theme-table td { padding: 16px 18px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 0.9rem; vertical-align: middle; }
    .theme-table tr:hover { background: rgba(255,255,255,0.02); }
  </style>
</head>
<body>
  <div class="ceo-mesh-bg"></div>

  <div class="admin-layout">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
      <a href="../index.php" class="ceo-logo" style="margin-bottom:10px;">
        <div class="logo-icon"><i class="fa-solid fa-crown"></i></div>
        <span>HIEU<span class="text-gold-gradient">.CEO</span></span>
      </a>

      <ul class="sidebar-menu">
        <li><a href="index.php"><i class="fa-solid fa-chart-pie"></i> Bảng Điều Khiển CEO</a></li>
        <li><a href="themes.php" class="active"><i class="fa-solid fa-layer-group"></i> Quản Lý Giao Diện</a></li>
        <li><a href="theme-add.php"><i class="fa-solid fa-plus-circle"></i> Thêm Giao Diện Mới</a></li>
        <li><a href="components.php"><i class="fa-solid fa-cube"></i> Thư Viện UI Kit</a></li>
        <li><a href="analytics.php"><i class="fa-solid fa-arrow-trend-up"></i> Phân Tích & A/B Test</a></li>
        <li><a href="users.php"><i class="fa-solid fa-user-shield"></i> Phân Quyền Ban ĐH</a></li>
        <li><a href="logs.php"><i class="fa-solid fa-clock-rotate-left"></i> Nhật Ký Hoạt Động</a></li>
        <li><a href="settings.php"><i class="fa-solid fa-sliders"></i> Cài Đặt Hệ Thống</a></li>
      </ul>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
      <div class="ceo-flex-between" style="margin-bottom:30px;">
        <div>
          <h1 style="font-size:1.8rem;font-weight:800;margin-bottom:4px;">Quản Lý Danh Sách Giao Diện</h1>
          <p style="color:var(--text-secondary);font-size:0.92rem;">Toàn quyền cấu hình, kích hoạt, nhân bản và xóa giao diện hệ thống</p>
        </div>

        <a href="theme-add.php" class="btn-ceo-primary btn-ripple">
          <i class="fa-solid fa-plus mr-1"></i> Thêm Giao Diện Mới
        </a>
      </div>

      <!-- Filters Row -->
      <div class="glass-panel" style="padding:18px 24px;margin-bottom:28px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
          <a href="themes.php" class="badge-ceo <?= $category === 'all' ? 'badge-active' : 'badge-ready' ?>" style="text-decoration:none;padding:6px 14px;">Tất Cả</a>
          <?php foreach ($categories as $c): ?>
            <a href="themes.php?category=<?= e($c['slug']) ?>" class="badge-ceo <?= $category === $c['slug'] ? 'badge-active' : 'badge-ready' ?>" style="text-decoration:none;padding:6px 14px;">
              <?= e($c['name']) ?>
            </a>
          <?php endforeach; ?>
        </div>

        <form method="GET" action="themes.php" style="position:relative;width:280px;">
          <input type="text" name="search" value="<?= e($search) ?>" placeholder="Tìm kiếm theo tên / code..." class="glass-input" style="padding:8px 14px 8px 36px;font-size:0.85rem;">
          <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:0.8rem;"></i>
        </form>
      </div>

      <!-- Theme Table -->
      <div class="glass-panel" style="padding:0;overflow-x:auto;">
        <table class="theme-table">
          <thead>
            <tr>
              <th>Giao Diện & Mã Định Danh</th>
              <th>Danh Mục</th>
              <th>Trạng Thái</th>
              <th>Bảng Màu</th>
              <th>Font Chữ</th>
              <th>Đánh Giá</th>
              <th style="text-align:right;">Thao Tác</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($themes as $t): ?>
              <tr>
                <td>
                  <div style="font-weight:700;font-size:0.95rem;color:var(--text-primary);"><?= e($t['name']) ?></div>
                  <div style="font-size:0.78rem;color:var(--text-muted);font-family:var(--font-mono);">
                    Code: <strong><?= e($t['code_name']) ?></strong> • Ver: <?= e($t['version']) ?>
                  </div>
                </td>
                <td>
                  <span class="badge-ceo badge-ready" style="font-size:0.75rem;"><?= e($t['category_name']) ?></span>
                </td>
                <td>
                  <span class="badge-ceo <?= $t['status'] === 'active' ? 'badge-active' : 'badge-ready' ?>">
                    <i class="fa-solid <?= $t['status'] === 'active' ? 'fa-circle-check' : 'fa-circle-pause' ?> mr-1"></i>
                    <?= $t['status'] === 'active' ? 'Đang Chạy' : 'Sẵn Sàng' ?>
                  </span>
                </td>
                <td>
                  <div style="display:flex;gap:6px;">
                    <span style="width:16px;height:16px;border-radius:50%;background:<?= e($t['primary_color']) ?>;" title="Primary"></span>
                    <span style="width:16px;height:16px;border-radius:50%;background:<?= e($t['secondary_color']) ?>;" title="Secondary"></span>
                    <span style="width:16px;height:16px;border-radius:50%;background:<?= e($t['accent_color']) ?>;" title="Accent"></span>
                  </div>
                </td>
                <td><strong><?= e($t['font_family']) ?></strong></td>
                <td><i class="fa-solid fa-star" style="color:#f59e0b;"></i> <?= number_format($t['rating'], 1) ?></td>
                <td style="text-align:right;">
                  <div style="display:inline-flex;gap:6px;">
                    <?php if ($t['status'] !== 'active'): ?>
                      <button class="btn-icon btn-activate-theme" data-theme-id="<?= $t['id'] ?>" data-theme-name="<?= e($t['name']) ?>" title="Kích hoạt">
                        <i class="fa-solid fa-power-off" style="color:#10b981;"></i>
                      </button>
                    <?php endif; ?>
                    <a href="../theme-preview.php?theme_id=<?= $t['id'] ?>" class="btn-icon" title="Xem trước mô phỏng"><i class="fa-solid fa-eye"></i></a>
                    <a href="../customizer.php?theme_id=<?= $t['id'] ?>" class="btn-icon" title="Tùy biến trực quan"><i class="fa-solid fa-wand-magic-sparkles" style="color:#f59e0b;"></i></a>
                    <a href="theme-edit.php?id=<?= $t['id'] ?>" class="btn-icon" title="Chỉnh sửa thông số"><i class="fa-solid fa-pen-to-square"></i></a>
                    <button onclick="duplicateTheme(<?= $t['id'] ?>)" class="btn-icon" title="Nhân bản"><i class="fa-solid fa-copy"></i></button>
                    <?php if ($t['status'] !== 'active'): ?>
                      <button onclick="deleteTheme(<?= $t['id'] ?>, '<?= e($t['name']) ?>')" class="btn-icon" title="Xóa"><i class="fa-solid fa-trash" style="color:#f43f5e;"></i></button>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>

  <script src="../assets/js/ceo-app.js"></script>
  <script>
    async function duplicateTheme(id) {
      if (!confirm('Bạn có chắc chắn muốn nhân bản giao diện này?')) return;
      try {
        const res = await fetch('../api/themes.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action: 'duplicate', theme_id: id })
        });
        const d = await res.json();
        if (d.success) {
          showToast('Nhân bản giao diện thành công!', 'success');
          setTimeout(() => location.reload(), 1000);
        } else {
          showToast(d.message, 'error');
        }
      } catch (e) {
        showToast('Lỗi khi nhân bản.', 'error');
      }
    }

    async function deleteTheme(id, name) {
      if (!confirm(`Bạn có chắc chắn muốn xóa giao diện "${name}"?`)) return;
      try {
        const res = await fetch('../api/themes.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action: 'delete', theme_id: id })
        });
        const d = await res.json();
        if (d.success) {
          showToast(`Đã xóa giao diện "${name}" thành công!`, 'success');
          setTimeout(() => location.reload(), 1000);
        } else {
          showToast(d.message, 'error');
        }
      } catch (e) {
        showToast('Lỗi khi xóa giao diện.', 'error');
      }
    }
  </script>
</body>
</html>
