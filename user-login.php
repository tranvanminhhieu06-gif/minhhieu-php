<?php
/**
 * HIEU CEO - User & Member Login Portal
 * Cổng Đăng Nhập Dành Cho Người Dùng & Khách Hàng
 */

require_once __DIR__ . '/config/auth_user.php';

if (isUserLoggedIn()) {
    header('Location: explore.php');
    exit;
}

$error = '';
$flash = getFlash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $csrfToken = $_POST['csrf_token'] ?? '';

    if (!verifyCsrfToken($csrfToken)) {
        $error = 'Yêu cầu không hợp lệ hoặc phiên bảo mật đã hết hạn (CSRF).';
    } elseif (empty($email) || empty($password)) {
        $error = 'Vui lòng nhập đầy đủ Email và Mật khẩu thành viên.';
    } else {
        try {
            $db = getDb();
            $stmt = $db->prepare("SELECT * FROM `users` WHERE `email` = :email AND `status` = 'active' LIMIT 1");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                // Member Login Success
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['username'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_fullname'] = $user['full_name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_title'] = $user['title'];
                $_SESSION['user_avatar'] = $user['avatar'];

                logUserAction('LOGIN', "Thành viên {$user['full_name']} đăng nhập thành công.");

                setFlash('success', "Chào mừng bạn trở lại, {$user['full_name']}!");
                header('Location: explore.php');
                exit;
            } else {
                $error = 'Email hoặc mật khẩu thành viên không chính xác.';
            }
        } catch (Exception $e) {
            $error = 'Lỗi hệ thống khi đăng nhập: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng Nhập Thành Viên - HIEU CEO Portal</title>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="assets/css/ceo-core.css">
  <link rel="stylesheet" href="assets/css/animations.css">
</head>
<body style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;">
  <div class="ceo-mesh-bg"></div>

  <div class="glass-card animate-scale-up" style="max-width:440px;width:100%;padding:40px 32px;box-shadow:0 25px 60px rgba(0,0,0,0.8), var(--shadow-glow);">
    <div style="text-align:center;margin-bottom:28px;">
      <a href="index.php" class="ceo-logo" style="justify-content:center;margin-bottom:14px;">
        <div class="logo-icon"><i class="fa-solid fa-user-astronaut"></i></div>
        <span>HIEU<span class="text-gold-gradient">.MEMBER</span></span>
      </a>
      <h1 style="font-size:1.45rem;font-weight:800;margin-bottom:6px;">Cổng Đăng Nhập Thành Viên</h1>
      <p style="color:var(--text-secondary);font-size:0.85rem;">Trải nghiệm các giao diện website & lưu mục yêu thích</p>
    </div>

    <?php if ($error): ?>
      <div class="glass-card" style="padding:12px 16px;margin-bottom:20px;border-color:rgba(244,63,94,0.4);background:rgba(244,63,94,0.1);color:#fb7185;font-size:0.88rem;display:flex;align-items:center;gap:10px;">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span><?= e($error) ?></span>
      </div>
    <?php endif; ?>

    <?php if (!empty($flash['warning'])): ?>
      <div class="glass-card" style="padding:12px 16px;margin-bottom:20px;border-color:rgba(245,158,11,0.4);background:rgba(245,158,11,0.1);color:#fbbf24;font-size:0.88rem;display:flex;align-items:center;gap:10px;">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <span><?= e($flash['warning']) ?></span>
      </div>
    <?php endif; ?>

    <form action="user-login.php" method="POST">
      <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

      <div style="margin-bottom:18px;">
        <label style="display:block;font-size:0.82rem;font-weight:600;color:var(--text-secondary);margin-bottom:8px;">
          <i class="fa-solid fa-envelope mr-1"></i> Email Thành Viên
        </label>
        <input type="email" name="email" id="email" class="glass-input" placeholder="guest@hieu.vn" required autofocus>
      </div>

      <div style="margin-bottom:24px;">
        <label style="display:block;font-size:0.82rem;font-weight:600;color:var(--text-secondary);margin-bottom:8px;">
          <i class="fa-solid fa-lock mr-1"></i> Mật Khẩu
        </label>
        <input type="password" name="password" id="password" class="glass-input" placeholder="••••••••" required>
      </div>

      <button type="submit" class="btn-ceo-primary btn-ripple" style="width:100%;padding:12px;font-size:0.95rem;justify-content:center;margin-bottom:20px;">
        <i class="fa-solid fa-right-to-bracket mr-2"></i> Đăng Nhập Thành Viên
      </button>
    </form>

    <!-- Quick Credentials Box for testing -->
    <div style="background:rgba(0,0,0,0.35);border:1px dashed rgba(255,255,255,0.12);border-radius:12px;padding:12px 14px;margin-bottom:20px;font-size:0.8rem;">
      <div style="color:var(--text-accent);font-weight:700;margin-bottom:6px;display:flex;align-items:center;gap:6px;">
        <i class="fa-solid fa-bolt text-warning"></i> Tài Khoản Khách Mẫu:
      </div>
      <div style="cursor:pointer;" onclick="fillUserCredentials('guest@hieu.vn', 'admin123')">
        👤 Khách Trải Nghiệm: <code>guest@hieu.vn</code> / <code>admin123</code>
      </div>
    </div>

    <div style="text-align:center;font-size:0.82rem;color:var(--text-muted);display:flex;justify-content:space-between;">
      <a href="index.php" style="color:var(--text-secondary);text-decoration:none;">← Về Trang Chủ</a>
      <a href="login.php" style="color:var(--text-accent);text-decoration:none;font-weight:600;">
        <i class="fa-solid fa-shield-halved mr-1"></i> Cổng Admin CEO →
      </a>
    </div>
  </div>

  <script>
    function fillUserCredentials(email, pass) {
      document.getElementById('email').value = email;
      document.getElementById('password').value = pass;
    }
  </script>
</body>
</html>
