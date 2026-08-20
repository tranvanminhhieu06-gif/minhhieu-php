<?php
/**
 * HIEU CEO - User & Member Registration Portal
 * Cổng Đăng Ký Thành Viên Trải Nghiệm & Khách Hàng
 */

require_once __DIR__ . '/../config/auth_user.php';

if (isUserLoggedIn()) {
    header('Location: ../index.php');
    exit;
}

$redirect = sanitize($_GET['redirect'] ?? ($_POST['redirect'] ?? '../live-view.php'));
if (str_starts_with($redirect, 'http://') || str_starts_with($redirect, 'https://') || str_starts_with($redirect, '//')) {
    $redirect = '../live-view.php';
}

$error = '';
$success = '';
$flash = getFlash();

$avatars = [
    'assets/images/user-avatar.png' => 'Phi Hành Gia',
    'assets/images/ceo-avatar.png' => 'CEO VIP',
    'assets/images/cdo-avatar.png' => 'Designer',
    'assets/images/dev-avatar.png' => 'Coder Matrix',
    'assets/images/viewer-avatar.png' => 'Khách Tham Quan'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = sanitize($_POST['full_name'] ?? '');
    $username = sanitize($_POST['username'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';
    $avatar = sanitize($_POST['avatar'] ?? 'assets/images/user-avatar.png');
    $csrfToken = $_POST['csrf_token'] ?? '';
    $postRedirect = sanitize($_POST['redirect'] ?? $redirect);

    if (!verifyCsrfToken($csrfToken)) {
        $error = 'Phiên bảo mật đã hết hạn. Vui lòng tải lại trang (CSRF Token).';
    } elseif (empty($fullName) || empty($username) || empty($email) || empty($password)) {
        $error = 'Vui lòng điền đầy đủ tất cả các trường thông tin bắt buộc.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Địa chỉ Email không đúng định dạng.';
    } elseif (strlen($password) < 6) {
        $error = 'Mật khẩu phải có độ dài tối thiểu từ 6 ký tự.';
    } elseif ($password !== $passwordConfirm) {
        $error = 'Mật khẩu xác nhận không khớp.';
    } else {
        $regResult = registerMemberAccount($username, $email, $password, $fullName, $avatar);
        if ($regResult['success']) {
            setFlash('success', "🎉 Chào mừng {$fullName}! Bạn đã đăng ký và tự động đăng nhập thành công.");
            header("Location: {$postRedirect}");
            exit;
        } else {
            $error = $regResult['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng Ký Thành Viên - HIEU CEO Portal</title>
  <meta name="description" content="Tạo tài khoản thành viên để trải nghiệm xem live các dự án website, lưu mục yêu thích và gửi phản hồi.">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/ceo-core.css">
  <link rel="stylesheet" href="../assets/css/animations.css">
</head>
<body style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:30px 20px;">
  <div class="ceo-mesh-bg"></div>

  <div class="glass-card animate-scale-up" style="max-width:500px;width:100%;padding:40px 36px;box-shadow:0 25px 60px rgba(0,0,0,0.8), var(--shadow-glow);">
    <div style="text-align:center;margin-bottom:28px;">
      <a href="../index.php" class="ceo-logo" style="justify-content:center;margin-bottom:14px;">
        <div class="logo-icon"><i class="fa-solid fa-crown"></i></div>
        <span>HIEU<span class="text-gold-gradient">.MEMBER</span></span>
      </a>
      <h1 style="font-size:1.5rem;font-weight:800;margin-bottom:6px;">Đăng Ký Tài Khoản Mới</h1>
      <p style="color:var(--text-secondary);font-size:0.88rem;">Trải nghiệm trọn vẹn trình xem Live website & lưu yêu thích</p>
    </div>

    <?php if ($error): ?>
      <div class="glass-card" style="padding:12px 16px;margin-bottom:20px;border-color:rgba(244,63,94,0.4);background:rgba(244,63,94,0.1);color:#fb7185;font-size:0.88rem;display:flex;align-items:center;gap:10px;">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span><?= e($error) ?></span>
      </div>
    <?php endif; ?>

    <form action="register.php" method="POST">
      <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
      <input type="hidden" name="redirect" value="<?= e($redirect) ?>">

      <div style="margin-bottom:16px;">
        <label style="display:block;font-size:0.82rem;font-weight:600;color:var(--text-secondary);margin-bottom:6px;">
          <i class="fa-solid fa-id-card mr-1"></i> Họ và Tên của bạn
        </label>
        <input type="text" name="full_name" id="full_name" class="glass-input" placeholder="Nguyễn Văn A" value="<?= e($_POST['full_name'] ?? '') ?>" required autofocus>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
        <div>
          <label style="display:block;font-size:0.82rem;font-weight:600;color:var(--text-secondary);margin-bottom:6px;">
            <i class="fa-solid fa-user mr-1"></i> Tên tài khoản
          </label>
          <input type="text" name="username" id="username" class="glass-input" placeholder="nguyenvana" value="<?= e($_POST['username'] ?? '') ?>" required>
        </div>
        <div>
          <label style="display:block;font-size:0.82rem;font-weight:600;color:var(--text-secondary);margin-bottom:6px;">
            <i class="fa-solid fa-envelope mr-1"></i> Email
          </label>
          <input type="email" name="email" id="email" class="glass-input" placeholder="user@gmail.com" value="<?= e($_POST['email'] ?? '') ?>" required>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px;">
        <div>
          <label style="display:block;font-size:0.82rem;font-weight:600;color:var(--text-secondary);margin-bottom:6px;">
            <i class="fa-solid fa-lock mr-1"></i> Mật khẩu
          </label>
          <input type="password" name="password" id="password" class="glass-input" placeholder="••••••••" required>
        </div>
        <div>
          <label style="display:block;font-size:0.82rem;font-weight:600;color:var(--text-secondary);margin-bottom:6px;">
            <i class="fa-solid fa-shield-check mr-1"></i> Nhập lại mật khẩu
          </label>
          <input type="password" name="password_confirm" id="password_confirm" class="glass-input" placeholder="••••••••" required>
        </div>
      </div>

      <!-- Avatar Selection -->
      <div style="margin-bottom:24px;">
        <label style="display:block;font-size:0.82rem;font-weight:600;color:var(--text-secondary);margin-bottom:10px;">
          <i class="fa-solid fa-face-smile mr-1"></i> Chọn Ảnh Đại Diện
        </label>
        <div style="display:flex;gap:12px;justify-content:center;">
          <label style="cursor:pointer;text-align:center;">
            <input type="radio" name="avatar" value="assets/images/user-avatar.png" checked style="display:none;" onchange="updateAvatarStyle(this)">
            <div class="avatar-option selected" style="width:48px;height:48px;border-radius:50%;background:rgba(99,102,241,0.2);border:2px solid #6366f1;display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:#818cf8;">
              <i class="fa-solid fa-user-astronaut"></i>
            </div>
            <span style="font-size:0.7rem;color:var(--text-muted);margin-top:4px;display:block;">Khách VIP</span>
          </label>

          <label style="cursor:pointer;text-align:center;">
            <input type="radio" name="avatar" value="assets/images/ceo-avatar.png" style="display:none;" onchange="updateAvatarStyle(this)">
            <div class="avatar-option" style="width:48px;height:48px;border-radius:50%;background:rgba(245,158,11,0.15);border:2px solid transparent;display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:#f59e0b;">
              <i class="fa-solid fa-crown"></i>
            </div>
            <span style="font-size:0.7rem;color:var(--text-muted);margin-top:4px;display:block;">CEO</span>
          </label>

          <label style="cursor:pointer;text-align:center;">
            <input type="radio" name="avatar" value="assets/images/cdo-avatar.png" style="display:none;" onchange="updateAvatarStyle(this)">
            <div class="avatar-option" style="width:48px;height:48px;border-radius:50%;background:rgba(236,72,153,0.15);border:2px solid transparent;display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:#ec4899;">
              <i class="fa-solid fa-palette"></i>
            </div>
            <span style="font-size:0.7rem;color:var(--text-muted);margin-top:4px;display:block;">Design</span>
          </label>

          <label style="cursor:pointer;text-align:center;">
            <input type="radio" name="avatar" value="assets/images/dev-avatar.png" style="display:none;" onchange="updateAvatarStyle(this)">
            <div class="avatar-option" style="width:48px;height:48px;border-radius:50%;background:rgba(56,189,248,0.15);border:2px solid transparent;display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:#38bdf8;">
              <i class="fa-solid fa-code"></i>
            </div>
            <span style="font-size:0.7rem;color:var(--text-muted);margin-top:4px;display:block;">Dev</span>
          </label>
        </div>
      </div>

      <button type="submit" class="btn-ceo-primary btn-ripple" style="width:100%;padding:13px;font-size:0.98rem;justify-content:center;margin-bottom:20px;font-weight:700;">
        <i class="fa-solid fa-user-plus mr-2"></i> Đăng Ký & Truy Cập Ngay
      </button>
    </form>

    <div style="text-align:center;font-size:0.85rem;color:var(--text-muted);display:flex;justify-content:space-between;align-items:center;border-top:1px solid rgba(255,255,255,0.08);padding-top:16px;">
      <a href="../index.php" style="color:var(--text-secondary);text-decoration:none;">← Về Trang Chủ</a>
      <span>Đã có tài khoản? <a href="login.php" style="color:var(--text-accent);text-decoration:none;font-weight:700;">Đăng nhập</a></span>
    </div>
  </div>

  <script>
    function updateAvatarStyle(radio) {
      document.querySelectorAll('.avatar-option').forEach(el => {
        el.style.borderColor = 'transparent';
      });
      radio.closest('label').querySelector('.avatar-option').style.borderColor = '#6366f1';
    }
  </script>
</body>
</html>
