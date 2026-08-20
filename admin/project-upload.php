<?php
/**
 * HIEU CEO - Project Upload Hub (ZIP & Folder Package)
 */

require_once __DIR__ . '/../config/auth_admin.php';

$currentUser = getAdminUser();

$categories = getAllCategories();
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="vi" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tải Lên Thư Mục Dự Án - HIEU CEO</title>
  
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
    
    /* Drag and Drop Zone */
    .upload-dropzone {
      border: 2px dashed rgba(99, 102, 241, 0.4);
      background: rgba(15, 23, 42, 0.6);
      backdrop-filter: blur(16px);
      border-radius: var(--radius-xl);
      padding: 50px 30px;
      text-align: center;
      cursor: pointer;
      transition: all var(--transition-smooth);
      position: relative;
      overflow: hidden;
      margin-bottom: 28px;
    }
    .upload-dropzone:hover, .upload-dropzone.dragover {
      border-color: #38bdf8;
      background: rgba(99, 102, 241, 0.12);
      box-shadow: 0 0 35px rgba(56, 189, 248, 0.3);
      transform: scale(1.01);
    }
    .upload-progress {
      display: none;
      width: 100%;
      height: 8px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 4px;
      overflow: hidden;
      margin-top: 20px;
    }
    .upload-progress-bar {
      width: 0%;
      height: 100%;
      background: linear-gradient(90deg, #6366f1, #38bdf8);
      transition: width 0.3s ease;
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
        <li><a href="projects.php"><i class="fa-solid fa-folder-tree"></i> Kho Thư Mục Dự Án</a></li>
        <li><a href="project-upload.php" class="active"><i class="fa-solid fa-cloud-arrow-up"></i> Tải Lên Dự Án (.ZIP)</a></li>
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
      <div class="ceo-flex-between" style="margin-bottom:28px;">
        <div>
          <a href="projects.php" style="color:var(--text-accent);text-decoration:none;font-size:0.85rem;margin-bottom:8px;display:inline-block;">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kho Thư Mục Dự Án
          </a>
          <h1 style="font-size:1.8rem;font-weight:800;margin-bottom:4px;">Tải Lên & Tự Động Hiển Thị Dự Án</h1>
          <p style="color:var(--text-secondary);font-size:0.92rem;">
            Tải lên tệp nén <code>.ZIP</code> của bất kỳ website nào để hệ thống tự động giải nén vào <code>projects/</code> và đăng ký hiển thị lên trang chủ
          </p>
        </div>

        <a href="projects.php" class="btn-ceo-secondary">
          <i class="fa-solid fa-folder-tree mr-1"></i> Quản Lý Thư Mục (projects/)
        </a>
      </div>

      <form id="project-upload-form" class="glass-panel" style="padding:32px;">
        <!-- Drag & Drop Zone -->
        <div id="dropzone" class="upload-dropzone">
          <input type="file" id="file-input" name="project_zip" accept=".zip" style="display:none;">
          <div style="width:64px;height:64px;background:rgba(99,102,241,0.2);border-radius:20px;display:inline-flex;align-items:center;justify-content:center;color:#818cf8;font-size:1.8rem;margin-bottom:16px;">
            <i class="fa-solid fa-cloud-arrow-up"></i>
          </div>
          <h3 id="dropzone-title" style="font-size:1.25rem;font-weight:700;margin-bottom:8px;">
            Kéo thả tệp <span class="text-cyan-gradient">.ZIP dự án</span> vào đây hoặc bấm để chọn tệp
          </h3>
          <p id="dropzone-subtitle" style="color:var(--text-secondary);font-size:0.9rem;max-width:520px;margin:0 auto;">
            Hệ thống hỗ trợ toàn diện website PHP, HTML5/CSS3/JS, Bootstrap, Tailwind, tự động tìm kiếm tệp <code>index.php</code> hoặc <code>index.html</code>.
          </p>

          <div id="file-info" style="display:none;margin-top:16px;font-size:0.95rem;color:#34d399;font-weight:600;">
            <i class="fa-solid fa-file-zipper mr-2"></i> <span id="file-name"></span>
          </div>

          <div class="upload-progress" id="progress-bar-container">
            <div class="upload-progress-bar" id="progress-bar"></div>
          </div>
        </div>

        <!-- Metadata Form -->
        <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:18px;display:flex;align-items:center;gap:10px;">
          <i class="fa-solid fa-sliders" style="color:#6366f1;"></i> Thông Tin Hiển Thị Giao Diện
        </h3>

        <div class="ceo-grid-2">
          <div class="form-group">
            <label class="form-label">Tên Giao Diện Hiển Thị (Tùy chọn):</label>
            <input type="text" name="theme_name" id="theme_name" class="glass-input" placeholder="Để trống hệ thống sẽ tự đặt tên theo file / README">
          </div>

          <div class="form-group">
            <label class="form-label">Tên Thư Mục Lưu Trữ trong projects/ (Tùy chọn):</label>
            <input type="text" name="folder_name" id="folder_name" class="glass-input" placeholder="VD: HieuWeb06, MyProject...">
          </div>
        </div>

        <div class="ceo-grid-3">
          <div class="form-group">
            <label class="form-label">Danh Mục Phân Loại:</label>
            <select name="category_id" class="glass-input">
              <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Màu Chủ Đạo:</label>
            <input type="color" name="primary_color" value="#6366f1" class="glass-input" style="height:44px;padding:4px;">
          </div>

          <div class="form-group">
            <label class="form-label">Phông Chữ:</label>
            <select name="font_family" class="glass-input">
              <option value="Outfit">Outfit</option>
              <option value="Plus Jakarta Sans">Plus Jakarta Sans</option>
              <option value="Montserrat">Montserrat</option>
              <option value="Cinzel">Cinzel</option>
            </select>
          </div>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:14px;margin-top:20px;">
          <a href="projects.php" class="btn-ceo-secondary">Hủy Bỏ</a>
          <button type="submit" id="btn-submit-upload" class="btn-ceo-primary btn-ripple">
            <i class="fa-solid fa-cloud-arrow-up mr-2"></i> Tải Lên & Giải Nén Tự Động
          </button>
        </div>
      </form>
    </main>
  </div>

  <script src="../assets/js/ceo-app.js"></script>
  <script>
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('file-input');
    const fileInfo = document.getElementById('file-info');
    const fileNameSpan = document.getElementById('file-name');
    const form = document.getElementById('project-upload-form');
    const submitBtn = document.getElementById('btn-submit-upload');
    const progressContainer = document.getElementById('progress-bar-container');
    const progressBar = document.getElementById('progress-bar');

    dropzone.addEventListener('click', () => fileInput.click());

    dropzone.addEventListener('dragover', (e) => {
      e.preventDefault();
      dropzone.classList.add('dragover');
    });

    dropzone.addEventListener('dragleave', () => {
      dropzone.classList.remove('dragover');
    });

    dropzone.addEventListener('drop', (e) => {
      e.preventDefault();
      dropzone.classList.remove('dragover');
      if (e.dataTransfer.files.length > 0) {
        fileInput.files = e.dataTransfer.files;
        handleFileSelect();
      }
    });

    fileInput.addEventListener('change', handleFileSelect);

    function handleFileSelect() {
      if (fileInput.files.length > 0) {
        const f = fileInput.files[0];
        fileNameSpan.innerText = `${f.name} (${(f.size / 1024 / 1024).toFixed(2)} MB)`;
        fileInfo.style.display = 'block';
        if (!document.getElementById('folder_name').value) {
          const rawName = f.name.replace(/\.[^/.]+$/, "");
          document.getElementById('folder_name').value = rawName.replace(/[^A-Za-z0-9_-]/g, "");
        }
      }
    }

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      if (!fileInput.files.length) {
        showToast('Vui lòng chọn tệp .ZIP dự án trước khi tải lên.', 'warning');
        return;
      }

      const formData = new FormData(form);
      formData.append('action', 'upload_zip');

      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Đang Tải Lên & Giải Nén...';
      progressContainer.style.display = 'block';
      progressBar.style.width = '30%';

      try {
        const response = await fetch('../api/upload_project.php', {
          method: 'POST',
          body: formData
        });
        progressBar.style.width = '80%';

        const result = await response.json();
        progressBar.style.width = '100%';

        if (result.success) {
          showToast(result.message || 'Tải lên và đăng ký thành công!', 'success');
          setTimeout(() => {
            window.location.href = 'projects.php';
          }, 1200);
        } else {
          showToast(result.message || 'Lỗi khi tải lên tệp.', 'error');
          submitBtn.disabled = false;
          submitBtn.innerHTML = '<i class="fa-solid fa-cloud-arrow-up mr-2"></i> Tải Lên & Giải Nén Tự Động';
        }
      } catch (err) {
        showToast('Lỗi máy chủ khi tải lên.', 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-cloud-arrow-up mr-2"></i> Tải Lên & Giải Nén Tự Động';
      }
    });
  </script>
</body>
</html>
