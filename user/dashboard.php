<?php
/**
 * HIEU CEO - User & Member Dashboard (Bảng Điều Khiển Thành Viên)
 * Quản lý các dự án yêu thích, lịch sử xem live và thông tin tài khoản
 */

require_once __DIR__ . '/../config/auth_user.php';

requireUserAuth('login.php?redirect=dashboard.php');

$currentUser = getUserProfile();
$favoriteThemes = getUserFavoriteThemesDetailed();
$recentViews = getUserRecentViews();
$flash = getFlash();
$activeTab = sanitize($_GET['tab'] ?? 'favorites'); // 'favorites', 'recent', 'profile'

$updateSuccess = '';
$updateError = '';

// Xử lý cập nhật thông tin tài khoản
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!verifyCsrfToken($csrfToken)) {
        $updateError = 'Phiên bảo mật đã hết hạn. Vui lòng tải lại trang (CSRF).';
    } else {
        $fullName = sanitize($_POST['full_name'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        $avatar = sanitize($_POST['avatar'] ?? '');

        if (empty($fullName)) {
            $updateError = 'Họ và tên không được để trống.';
        } elseif (!empty($password) && strlen($password) < 6) {
            $updateError = 'Mật khẩu mới phải có tối thiểu 6 ký tự.';
        } elseif (!empty($password) && $password !== $passwordConfirm) {
            $updateError = 'Mật khẩu xác nhận không khớp.';
        } else {
            $res = updateUserProfile((int)$currentUser['id'], $fullName, !empty($password) ? $password : null, !empty($avatar) ? $avatar : null);
            if ($res['success']) {
                $updateSuccess = 'Cập nhật thông tin tài khoản thành công!';
                $currentUser = getUserProfile(); // Làm mới dữ liệu
            } else {
                $updateError = $res['message'];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bảng Điều Khiển Thành Viên - HIEU CEO Portal</title>
  <meta name="description" content="Quản lý danh sách website yêu thích, lịch sử xem live và thông tin tài khoản thành viên.">

  <!-- Font Awesome 6.5 Pro Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  <link rel="stylesheet" href="../assets/css/ceo-core.css">
  <link rel="stylesheet" href="../assets/css/animations.css">
  <link rel="stylesheet" href="../assets/css/live-view.css">
</head>
<body>
  <div class="ceo-mesh-bg"></div>

  <!-- Top Executive Navbar -->
  <header class="ceo-container">
    <nav class="ceo-navbar ceo-flex-between animate-fade-up">
      <a href="../index.php" class="ceo-logo">
        <div class="logo-icon"><i class="fa-solid fa-crown"></i></div>
        <div>
          <span>HIEU<span class="text-gold-gradient">.MEMBER</span></span>
          <span style="display:block;font-size:0.68rem;letter-spacing:0.12em;color:var(--text-accent);font-weight:600;">USER DASHBOARD</span>
        </div>
      </a>

      <div style="display:flex;align-items:center;gap:12px;">
        <a href="../live-view.php" class="btn-ceo-primary btn-ripple" style="padding:8px 18px;font-size:0.85rem;">
          <i class="fa-solid fa-circle-play mr-1"></i> Xem Live Dự Án
        </a>

        <a href="../logout.php" class="btn-ceo-secondary" style="padding:8px 14px;font-size:0.85rem;color:#fb7185;" title="Đăng Xuất">
          <i class="fa-solid fa-right-from-bracket"></i>
        </a>
      </div>
    </nav>
  </header>

  <main class="ceo-container" style="padding-top:20px;padding-bottom:80px;">

    <!-- Flash Alerts -->
    <?php if ($updateSuccess || !empty($flash['success'])): ?>
      <div class="glass-card animate-fade-up" style="padding:14px 20px;margin-bottom:24px;border-color:rgba(16,185,129,0.4);background:rgba(16,185,129,0.1);color:#34d399;font-size:0.9rem;display:flex;align-items:center;gap:10px;">
        <i class="fa-solid fa-circle-check"></i>
        <span><?= e($updateSuccess ?: $flash['success']) ?></span>
      </div>
    <?php endif; ?>

    <?php if ($updateError): ?>
      <div class="glass-card animate-fade-up" style="padding:14px 20px;margin-bottom:24px;border-color:rgba(244,63,94,0.4);background:rgba(244,63,94,0.1);color:#fb7185;font-size:0.9rem;display:flex;align-items:center;gap:10px;">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span><?= e($updateError) ?></span>
      </div>
    <?php endif; ?>

    <!-- 1. Member Profile Banner -->
    <section class="glass-panel animate-fade-up" style="padding:28px 32px;margin-bottom:32px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px;">
      <div style="display:flex;align-items:center;gap:20px;">
        <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg, rgba(255,184,231,0.3), rgba(137,245,255,0.3));border:2px solid var(--ceo-primary);display:flex;align-items:center;justify-content:center;font-size:2rem;color:var(--text-accent);box-shadow:0 0 25px rgba(255,184,231,0.4);">
          <i class="fa-solid fa-user-astronaut"></i>
        </div>

        <div>
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;">
            <h1 style="font-size:1.6rem;font-weight:800;margin:0;color:var(--text-primary);"><?= e($currentUser['full_name']) ?></h1>
            <span class="badge-ceo badge-gold" style="font-size:0.75rem;">
              <i class="fa-solid fa-crown mr-1"></i> <?= e($currentUser['title']) ?>
            </span>
          </div>
          <div style="font-size:0.85rem;color:var(--text-secondary);display:flex;align-items:center;gap:16px;">
            <span><i class="fa-solid fa-envelope mr-1 text-muted"></i> <?= e($currentUser['email']) ?></span>
            <span><i class="fa-solid fa-circle-check mr-1 text-success"></i> Trạng Thái: Đang Hoạt Động</span>
          </div>
        </div>
      </div>

      <!-- Quick stats ribbon -->
      <div style="display:flex;gap:12px;flex-wrap:wrap;width:100%;max-width:320px;">
        <div class="glass-card" style="padding:10px 16px;text-align:center;flex:1;min-width:110px;">
          <div style="font-size:0.72rem;color:var(--text-muted);font-weight:700;text-transform:uppercase;">Yêu Thích</div>
          <div style="font-size:1.3rem;font-weight:800;color:#fb7185;"><?= count($favoriteThemes) ?> Dự Án</div>
        </div>
        <div class="glass-card" style="padding:10px 16px;text-align:center;flex:1;min-width:110px;">
          <div style="font-size:0.72rem;color:var(--text-muted);font-weight:700;text-transform:uppercase;">Đã Xem Live</div>
          <div style="font-size:1.3rem;font-weight:800;color:#89f5ff;"><?= count($recentViews) ?> Lần</div>
        </div>
      </div>
    </section>

    <!-- 2. Navigation Tabs -->
    <div style="display:flex;gap:10px;margin-bottom:24px;border-bottom:1px solid rgba(255,255,255,0.1);padding-bottom:14px;overflow-x:auto;-webkit-overflow-scrolling:touch;white-space:nowrap;" class="animate-fade-up category-tabs-scroll">
      <a href="dashboard.php?tab=favorites" class="btn-ceo-secondary" style="padding:8px 18px;border-radius:var(--radius-full);font-size:0.85rem;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;flex-shrink:0;background:<?= $activeTab === 'favorites' ? 'linear-gradient(135deg, #ffb8e7, #d68cb8)' : 'rgba(255,255,255,0.05)' ?>;color:<?= $activeTab === 'favorites' ? '#000' : '#fff' ?>;">
        <i class="fa-solid fa-heart" style="color:<?= $activeTab === 'favorites' ? '#000' : '#fb7185' ?>;"></i> Yêu Thích (<?= count($favoriteThemes) ?>)
      </a>

      <a href="dashboard.php?tab=recent" class="btn-ceo-secondary" style="padding:8px 18px;border-radius:var(--radius-full);font-size:0.85rem;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;flex-shrink:0;background:<?= $activeTab === 'recent' ? 'linear-gradient(135deg, #ffb8e7, #d68cb8)' : 'rgba(255,255,255,0.05)' ?>;color:<?= $activeTab === 'recent' ? '#000' : '#fff' ?>;">
        <i class="fa-solid fa-clock-rotate-left" style="color:<?= $activeTab === 'recent' ? '#000' : '#89f5ff' ?>;"></i> Lịch Sử Live
      </a>

      <a href="dashboard.php?tab=profile" class="btn-ceo-secondary" style="padding:8px 18px;border-radius:var(--radius-full);font-size:0.85rem;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;flex-shrink:0;background:<?= $activeTab === 'profile' ? 'linear-gradient(135deg, #ffb8e7, #d68cb8)' : 'rgba(255,255,255,0.05)' ?>;color:<?= $activeTab === 'profile' ? '#000' : '#fff' ?>;">
        <i class="fa-solid fa-user-gear"></i> Tài Khoản
      </a>
    </div>

    <!-- ==================== TAB 1: FAVORITES ==================== -->
    <?php if ($activeTab === 'favorites'): ?>
      <section class="animate-fade-up">
        <?php if (empty($favoriteThemes)): ?>
          <div class="glass-panel" style="padding:60px 20px;text-align:center;max-width:600px;margin:40px auto;">
            <div style="width:70px;height:70px;border-radius:50%;background:rgba(244,63,94,0.15);display:flex;align-items:center;justify-content:center;margin:0 auto 18px auto;color:#fb7185;font-size:2rem;">
              <i class="fa-regular fa-heart"></i>
            </div>
            <h3 style="font-size:1.3rem;font-weight:800;margin-bottom:8px;">Chưa Có Dự Án Yêu Thích Nào</h3>
            <p style="color:var(--text-secondary);font-size:0.9rem;line-height:1.6;margin-bottom:24px;">
              Hãy khám phá các dự án website trong hệ sinh thái và bấm biểu tượng trái tim để lưu vào bộ sưu tập cá nhân của bạn.
            </p>
            <a href="../live-view.php" class="btn-ceo-primary btn-ripple" style="display:inline-flex;padding:12px 24px;font-size:0.95rem;">
              <i class="fa-solid fa-play mr-2"></i> Trải Nghiệm Xem Live Ngay
            </a>
          </div>
        <?php else: ?>
          <div class="ceo-grid-3">
            <?php foreach ($favoriteThemes as $t): ?>
              <?php $tColor = $t['primary_color'] ?: '#ffb8e7'; ?>
              <div class="website-card animate-fade-up">
                <div class="website-card-banner" style="border-top: 4px solid <?= e($tColor) ?>;">
                  <div style="text-align:center;position:relative;z-index:2;padding:0 20px;">
                    <span class="badge-ceo badge-gold" style="font-size:0.7rem;margin-bottom:6px;">
                      <i class="fa-solid fa-star mr-1"></i> <?= number_format($t['rating'], 1) ?>
                    </span>
                    <h4 style="font-size:1.15rem;font-weight:800;color:#ffffff;margin:0;"><?= e($t['name']) ?></h4>
                  </div>
                  <i class="fa-solid fa-heart banner-icon" style="color:#fb7185;opacity:0.2;"></i>
                </div>

                <div style="padding:22px;">
                  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                    <span class="badge-ceo badge-active" style="font-size:0.72rem;"><?= e($t['category_name']) ?></span>
                    <span style="font-size:0.78rem;color:#89f5ff;font-family:var(--font-mono);"><i class="fa-solid fa-folder mr-1"></i> <?= e($t['folder_path']) ?></span>
                  </div>

                  <p style="color:var(--text-secondary);font-size:0.88rem;line-height:1.6;margin-bottom:20px;min-height:55px;">
                    <?= e($t['tagline'] ?: $t['description']) ?>
                  </p>

                  <div style="display:grid;grid-template-columns:2fr 1fr;gap:10px;">
                    <a href="../live-view.php?theme_id=<?= $t['id'] ?>" class="btn-ceo-primary btn-ripple" style="padding:10px;font-size:0.88rem;justify-content:center;">
                      <i class="fa-solid fa-circle-play mr-1"></i> Xem Live Đa Thiết Bị
                    </a>
                    <button onclick="removeFavorite(<?= $t['id'] ?>, this)" class="btn-ceo-secondary" style="padding:10px;font-size:0.85rem;color:#fb7185;justify-content:center;" title="Gỡ khỏi danh sách yêu thích">
                      <i class="fa-solid fa-trash-can"></i> Gỡ
                    </button>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </section>

    <!-- ==================== TAB 2: RECENT VIEWS ==================== -->
    <?php elseif ($activeTab === 'recent'): ?>
      <section class="animate-fade-up">
        <?php if (empty($recentViews)): ?>
          <div class="glass-panel" style="padding:60px 20px;text-align:center;max-width:600px;margin:40px auto;">
            <div style="width:70px;height:70px;border-radius:50%;background:rgba(137,245,255,0.15);display:flex;align-items:center;justify-content:center;margin:0 auto 18px auto;color:#89f5ff;font-size:2rem;">
              <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <h3 style="font-size:1.3rem;font-weight:800;margin-bottom:8px;">Chưa Có Lịch Sử Xem Gần Đây</h3>
            <p style="color:var(--text-secondary);font-size:0.9rem;line-height:1.6;margin-bottom:24px;">
              Mỗi khi bạn mở trình xem Live, hệ thống sẽ tự động ghi nhớ để bạn dễ dàng tiếp tục trải nghiệm sau này.
            </p>
            <a href="../live-view.php" class="btn-ceo-primary btn-ripple" style="display:inline-flex;padding:12px 24px;font-size:0.95rem;">
              <i class="fa-solid fa-play mr-2"></i> Khởi Chạy Live Viewer
            </a>
          </div>
        <?php else: ?>
          <div class="glass-panel" style="padding:24px;">
            <h3 style="font-size:1.15rem;font-weight:800;margin-bottom:18px;">Các Dự Án Bạn Đã Khám Phá Gần Đây:</h3>
            <div style="display:flex;flex-direction:column;gap:12px;">
              <?php foreach ($recentViews as $item): ?>
                <div class="glass-card" style="padding:16px 20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
                  <div style="display:flex;align-items:center;gap:14px;">
                    <div style="width:42px;height:42px;border-radius:10px;background:rgba(137,245,255,0.15);display:flex;align-items:center;justify-content:center;color:#89f5ff;font-size:1.2rem;">
                      <i class="fa-solid fa-laptop-code"></i>
                    </div>
                    <div>
                      <h4 style="font-size:1rem;font-weight:700;margin:0 0 2px 0;"><?= e($item['name']) ?></h4>
                      <span style="font-size:0.75rem;color:var(--text-muted);font-family:var(--font-mono);">
                        <i class="fa-solid fa-clock mr-1"></i> Xem lúc <?= date('H:i - d/m/Y', $item['time']) ?>
                      </span>
                    </div>
                  </div>

                  <div style="display:flex;gap:8px;">
                    <a href="../live-view.php?project=<?= urlencode($item['key']) ?>" class="btn-ceo-primary btn-ripple" style="padding:8px 16px;font-size:0.82rem;">
                      <i class="fa-solid fa-play mr-1"></i> Tiếp Tục Xem Live
                    </a>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
      </section>

    <!-- ==================== TAB 3: PROFILE SETTINGS ==================== -->
    <?php else: ?>
      <section class="animate-fade-up" style="max-width:600px;margin:0 auto;">
        <div class="glass-panel" style="padding:32px;">
          <h2 style="font-size:1.35rem;font-weight:800;margin-bottom:6px;">Cài Đặt Hồ Sơ Thành Viên</h2>
          <p style="color:var(--text-secondary);font-size:0.85rem;margin-bottom:24px;">Cập nhật họ tên, đổi mật khẩu và tùy biến ảnh đại diện của bạn.</p>

          <form action="dashboard.php?tab=profile" method="POST">
            <input type="hidden" name="action" value="update_profile">
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

            <div style="margin-bottom:18px;">
              <label style="display:block;font-size:0.82rem;font-weight:600;color:var(--text-secondary);margin-bottom:6px;">
                <i class="fa-solid fa-id-card mr-1"></i> Họ và Tên
              </label>
              <input type="text" name="full_name" class="glass-input" value="<?= e($currentUser['full_name']) ?>" required>
            </div>

            <div style="margin-bottom:18px;">
              <label style="display:block;font-size:0.82rem;font-weight:600;color:var(--text-secondary);margin-bottom:6px;">
                <i class="fa-solid fa-envelope mr-1"></i> Email (Không thể thay đổi)
              </label>
              <input type="email" class="glass-input" value="<?= e($currentUser['email']) ?>" disabled style="opacity:0.6;cursor:not-allowed;">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:24px;">
              <div>
                <label style="display:block;font-size:0.82rem;font-weight:600;color:var(--text-secondary);margin-bottom:6px;">
                  <i class="fa-solid fa-lock mr-1"></i> Mật Khẩu Mới (để trống nếu không đổi)
                </label>
                <input type="password" name="password" class="glass-input" placeholder="••••••••">
              </div>
              <div>
                <label style="display:block;font-size:0.82rem;font-weight:600;color:var(--text-secondary);margin-bottom:6px;">
                  <i class="fa-solid fa-shield-check mr-1"></i> Xác Nhận Mật Khẩu
                </label>
                <input type="password" name="password_confirm" class="glass-input" placeholder="••••••••">
              </div>
            </div>

            <button type="submit" class="btn-ceo-primary btn-ripple" style="width:100%;padding:12px;font-size:0.95rem;justify-content:center;font-weight:700;">
              <i class="fa-solid fa-floppy-disk mr-2"></i> Lưu Thay Đổi
            </button>
          </form>
        </div>
      </section>
    <?php endif; ?>

  </main>

  <script>
    async function removeFavorite(themeId, btnElement) {
      if (!confirm('Bạn có chắc muốn gỡ dự án này khỏi danh sách yêu thích?')) return;
      try {
        const res = await fetch('../api/toggle_favorite.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ theme_id: themeId })
        });
        const data = await res.json();
        if (data.success) {
          btnElement.closest('.website-card').style.opacity = '0';
          setTimeout(() => location.reload(), 300);
        }
      } catch (e) {
        alert('Lỗi kết nối máy chủ');
      }
    }
  </script>
</body>
</html>
