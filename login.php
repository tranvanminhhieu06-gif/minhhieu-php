<?php
/**
 * HIEU CEO - Executive Login Portal
 */

require_once __DIR__ . '/config/helper.php';

if (isLoggedIn()) {
    header('Location: admin/index.php');
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
        $error = 'Vui lòng nhập đầy đủ Email và Mật khẩu điều hành.';
    } else {
        try {
            $db = getDb();
            $stmt = $db->prepare("SELECT * FROM `users` WHERE `email` = :email AND `status` = 'active' LIMIT 1");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                // Login Success
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['username'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_fullname'] = $user['full_name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_title'] = $user['title'];
                $_SESSION['user_avatar'] = $user['avatar'];

                // Update last login
                $db->prepare("UPDATE `users` SET `last_login` = NOW() WHERE `id` = :id")->execute([':id' => $user['id']]);

                logSystemAction($user['id'], 'AUTH_LOGIN', "Người dùng {$user['full_name']} ({$user['role']}) đăng nhập thành công.");

                setFlash('success', "Chào mừng trở lại, {$user['full_name']} ({$user['title']})!");
                header('Location: admin/index.php');
                exit;
            } else {
                $error = 'Tài khoản email hoặc mật khẩu không chính xác.';
            }
        } catch (Exception $e) {
            $error = 'Lỗi hệ thống khi xác thực: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng Nhập Ban Điều Hành - HIEU CEO</title>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="assets/css/ceo-core.css">
  <link rel="stylesheet" href="assets/css/animations.css">
</head>
<body style="display:flex;align-items:center;justify-content:center;min-height:100vh;padding:20px;">
  <div class="ceo-mesh-bg"></div>

  <div class="glass-panel animate-fade-scale" style="max-width:460px;width:100%;padding:40px 36px;box-shadow:var(--shadow-lg);">
    <div style="text-align:center;margin-bottom:30px;">
      <a href="index.php" class="ceo-logo" style="justify-content:center;margin-bottom:12px;">
        <div class="logo-icon">
          <i class="fa-solid fa-crown"></i>
        </div>
        <span>HIEU<span class="text-gold-gradient">.CEO</span></span>
      </a>
      <h2 style="font-size:1.4rem;font-weight:800;margin-bottom:6px;">Xác Thực Ban Điều Hành</h2>
      <p style="font-size:0.88rem;color:var(--text-secondary);">Hệ thống bảo mật phân quyền điều hành cấp cao</p>
    </div>

    <?php if (!empty($error)): ?>
      <div style="background:rgba(244,63,94,0.15);border:1px solid rgba(244,63,94,0.35);color:#fda4af;padding:12px 16px;border-radius:var(--radius-md);margin-bottom:20px;font-size:0.88rem;display:flex;align-items:center;gap:10px;">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span><?= e($error) ?></span>
      </div>
    <?php endif; ?>

    <?php if (!empty($flash['error'])): ?>
      <div style="background:rgba(244,63,94,0.15);border:1px solid rgba(244,63,94,0.35);color:#fda4af;padding:12px 16px;border-radius:var(--radius-md);margin-bottom:20px;font-size:0.88rem;">
        <?= e($flash['error']) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

      <div class="form-group">
        <label class="form-label" for="login-email">Email Điều Hành:</label>
        <div style="position:relative;">
          <input type="email" id="login-email" name="email" value="<?= e($_POST['email'] ?? 'ceo@hieu.vn') ?>" required class="glass-input" style="padding-left:42px;">
          <i class="fa-solid fa-envelope" style="position:absolute;left:15px;top:50%;transform:translateY(-50%);color:var(--text-muted);"></i>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="login-password">Mật Khẩu:</label>
        <div style="position:relative;">
          <input type="password" id="login-password" name="password" value="admin123" required class="glass-input" style="padding-left:42px;">
          <i class="fa-solid fa-key" style="position:absolute;left:15px;top:50%;transform:translateY(-50%);color:var(--text-muted);"></i>
        </div>
      </div>

      <button type="submit" class="btn-ceo-primary btn-ripple" style="width:100%;padding:14px;font-size:1rem;margin-top:10px;">
        <i class="fa-solid fa-right-to-bracket mr-2"></i> Đăng Nhập Hệ Thống
      </button>
    </form>

    <!-- Quick Demo Autofill Chips -->
    <div style="margin-top:30px;padding-top:24px;border-top:1px solid var(--border-glass);">
      <div style="font-size:0.8rem;color:var(--text-muted);margin-bottom:10px;text-align:center;">
        Tài Khoản Mẫu Trải Nghiệm Nhanh (Pass: <code>admin123</code>):
      </div>
      <div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap;">
        <button type="button" onclick="fillCreds('ceo@hieu.vn')" class="badge-ceo badge-gold" style="cursor:pointer;border:none;">
          <i class="fa-solid fa-crown mr-1"></i> CEO (Full Quyền)
        </button>
        <button type="button" onclick="fillCreds('cdo@hieu.vn')" class="badge-ceo badge-ready" style="cursor:pointer;border:none;">
          <i class="fa-solid fa-pen-nib mr-1"></i> CDO (Design)
        </button>
        <button type="button" onclick="fillCreds('dev@hieu.vn')" class="badge-ceo badge-active" style="cursor:pointer;border:none;">
          <i class="fa-solid fa-code mr-1"></i> Developer
        </button>
      </div>
    </div>

    <div style="text-align:center;margin-top:20px;">
      <a href="index.php" style="color:var(--text-secondary);font-size:0.85rem;text-decoration:none;">
        <i class="fa-solid fa-arrow-left mr-1"></i> Quay lại Trang Chủ
      </a>
    </div>
  </div>

  <script>
    function fillCreds(email) {
      document.getElementById('login-email').value = email;
      document.getElementById('login-password').value = 'admin123';
    }
  </script>
</body>
</html>
