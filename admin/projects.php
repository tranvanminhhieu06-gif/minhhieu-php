<?php
/**
 * HIEU CEO - Projects Storage & Workspace Manager
 * Displays all website project folders in projects/ directory
 */

require_once __DIR__ . '/../config/auth_admin.php';

$currentUser = getAdminUser();

$projects = scanProjectsDirectory();
$categories = getAllCategories();
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="vi" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kho Thư Mục Dự Án - HIEU CEO</title>
  
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
    .project-card {
      background: rgba(15, 23, 42, 0.7);
      border: 1px solid var(--border-glass);
      border-radius: var(--radius-lg);
      padding: 24px;
      transition: all var(--transition-smooth);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .project-card:hover {
      border-color: var(--border-glass-hover);
      box-shadow: var(--shadow-glow);
      transform: translateY(-4px);
    }
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
        <li><a href="themes.php"><i class="fa-solid fa-layer-group"></i> Quản Lý Giao Diện</a></li>
        <li><a href="projects.php" class="active"><i class="fa-solid fa-folder-tree"></i> Kho Thư Mục Dự Án</a></li>
        <li><a href="project-upload.php"><i class="fa-solid fa-cloud-arrow-up"></i> Tải Lên Dự Án (.ZIP)</a></li>
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
      <div class="ceo-flex-between" style="margin-bottom:30px;flex-wrap:wrap;gap:16px;">
        <div>
          <h1 style="font-size:1.8rem;font-weight:800;margin-bottom:4px;">Kho Lưu Trữ Thư Mục Dự Án (projects/)</h1>
          <p style="color:var(--text-secondary);font-size:0.92rem;">
            Quản lý trực tiếp các thư mục website thực tế nằm trong <code>c:\Users\tranv\Desktop\DoAnWebsite\projects\</code>
          </p>
        </div>

        <div style="display:flex;gap:12px;">
          <button onclick="location.reload()" class="btn-ceo-secondary" title="Quét lại thư mục">
            <i class="fa-solid fa-arrows-rotate mr-1"></i> Quét Thư Mục
          </button>
          <a href="project-upload.php" class="btn-ceo-primary btn-ripple">
            <i class="fa-solid fa-cloud-arrow-up mr-1"></i> Tải Lên Dự Án Mới (.ZIP)
          </a>
        </div>
      </div>

      <!-- Overview Stats -->
      <div class="glass-panel" style="padding:20px 28px;margin-bottom:32px;display:grid;grid-template-columns:repeat(3, 1fr);gap:20px;text-align:center;">
        <div>
          <div style="font-size:0.8rem;color:var(--text-muted);font-weight:700;text-transform:uppercase;">Tổng Thư Mục Dự Án</div>
          <div style="font-size:1.8rem;font-weight:800;color:var(--text-primary);margin-top:4px;"><?= count($projects) ?> Dự Án</div>
        </div>
        <div>
          <div style="font-size:0.8rem;color:var(--text-muted);font-weight:700;text-transform:uppercase;">Đã Đăng Ký Hiển Thị Lên Web</div>
          <div style="font-size:1.8rem;font-weight:800;color:#34d399;margin-top:4px;">
            <?= count(array_filter($projects, fn($p) => $p['is_registered'])) ?> Đang Vận Hành
          </div>
        </div>
        <div>
          <div style="font-size:0.8rem;color:var(--text-muted);font-weight:700;text-transform:uppercase;">Đường Dẫn Lưu Trữ Vật Lý</div>
          <div style="font-size:0.95rem;font-weight:600;color:#38bdf8;margin-top:8px;font-family:var(--font-mono);">
            DoAnWebsite/projects/
          </div>
        </div>
      </div>

      <!-- Project Cards Grid -->
      <div class="ceo-grid-3">
        <?php foreach ($projects as $p): ?>
          <div class="project-card animate-fade-up">
            <div>
              <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:14px;">
                <div style="width:44px;height:44px;background:rgba(99,102,241,0.18);border-radius:12px;display:flex;align-items:center;justify-content:center;color:#818cf8;font-size:1.3rem;">
                  <i class="fa-solid <?= $p['has_index'] ? 'fa-folder-open' : 'fa-folder' ?>"></i>
                </div>

                <span class="badge-ceo <?= $p['is_registered'] ? 'badge-active' : 'badge-ready' ?>" style="font-size:0.75rem;">
                  <i class="fa-solid <?= $p['is_registered'] ? 'fa-circle-check' : 'fa-clock' ?> mr-1"></i>
                  <?= $p['is_registered'] ? 'Đã Hiển Thị' : 'Chưa Đăng Ký' ?>
                </span>
              </div>

              <h3 style="font-size:1.2rem;font-weight:700;margin-bottom:6px;color:var(--text-primary);">
                <?= e($p['theme_name'] ?: $p['folder_name']) ?>
              </h3>

              <div style="font-size:0.8rem;color:var(--text-muted);font-family:var(--font-mono);margin-bottom:16px;">
                Thư mục: <strong>projects/<?= e($p['folder_name']) ?>/</strong>
              </div>

              <!-- Folder Specs -->
              <div style="background:rgba(0,0,0,0.3);border-radius:10px;padding:10px 14px;margin-bottom:18px;font-size:0.82rem;display:flex;justify-content:space-between;">
                <div>
                  <span style="color:var(--text-muted);">Tập tin:</span> <strong><?= $p['file_count'] ?> files</strong>
                </div>
                <div>
                  <span style="color:var(--text-muted);">Dung lượng:</span> <strong><?= $p['size'] ?></strong>
                </div>
                <div>
                  <span style="color:var(--text-muted);">Entry:</span> <code style="color:#34d399;"><?= $p['entry_file'] ?></code>
                </div>
              </div>
            </div>

            <!-- Actions Row -->
            <div>
              <?php if ($p['is_registered'] && $p['theme_id']): ?>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;">
                  <a href="../theme-preview.php?theme_id=<?= $p['theme_id'] ?>" class="btn-ceo-secondary" style="padding:8px;font-size:0.82rem;text-align:center;">
                    <i class="fa-solid fa-eye mr-1"></i> Xem Thử
                  </a>
                  <a href="../customizer.php?theme_id=<?= $p['theme_id'] ?>" class="btn-ceo-secondary" style="padding:8px;font-size:0.82rem;text-align:center;">
                    <i class="fa-solid fa-wand-magic-sparkles mr-1" style="color:var(--ceo-gold);"></i> Tùy Biến
                  </a>
                </div>
              <?php else: ?>
                <button onclick="registerFolder('<?= e($p['folder_name']) ?>')" class="btn-ceo-primary btn-ripple" style="width:100%;padding:10px;font-size:0.85rem;margin-bottom:8px;">
                  <i class="fa-solid fa-bolt mr-1"></i> Đăng Ký Lên Trang Chủ
                </button>
              <?php endif; ?>

              <div style="display:flex;justify-content:space-between;align-items:center;">
                <a href="../<?= e($p['preview_url']) ?>" target="_blank" style="font-size:0.8rem;color:var(--text-accent);text-decoration:none;">
                  <i class="fa-solid fa-arrow-up-right-from-square mr-1"></i> Mở Trực Tiếp
                </a>
                <button onclick="deleteFolder('<?= e($p['folder_name']) ?>')" style="background:none;border:none;color:#f43f5e;font-size:0.8rem;cursor:pointer;padding:4px;">
                  <i class="fa-solid fa-trash mr-1"></i> Xóa
                </button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </main>
  </div>

  <script src="../assets/js/ceo-app.js"></script>
  <script>
    async function registerFolder(folderName) {
      if (!confirm(`Bạn có muốn đăng ký dự án "${folderName}" để hiển thị lên trang chủ và bảng điều khiển?`)) return;
      try {
        const res = await fetch('../api/upload_project.php?action=register_folder', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ folder_name: folderName })
        });
        const d = await res.json();
        if (d.success) {
          showToast(d.message || 'Đăng ký dự án thành công!', 'success');
          setTimeout(() => location.reload(), 1000);
        } else {
          showToast(d.message, 'error');
        }
      } catch (e) {
        showToast('Lỗi khi đăng ký dự án.', 'error');
      }
    }

    async function deleteFolder(folderName) {
      if (!confirm(`CẢNH BÁO: Bạn có chắc chắn muốn XÓA VĨNH VIỄN thư mục dự án "projects/${folderName}"?`)) return;
      try {
        const res = await fetch('../api/upload_project.php?action=delete_project', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ folder_name: folderName })
        });
        const d = await res.json();
        if (d.success) {
          showToast(d.message || 'Đã xóa thư mục dự án!', 'success');
          setTimeout(() => location.reload(), 1000);
        } else {
          showToast(d.message, 'error');
        }
      } catch (e) {
        showToast('Lỗi khi xóa thư mục.', 'error');
      }
    }
  </script>
</body>
</html>
