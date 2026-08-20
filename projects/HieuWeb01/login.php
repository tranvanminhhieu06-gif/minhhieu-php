<?php
/**
 * Trang Đăng Nhập HieuMini
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

if (is_logged_in()) {
    redirect('index.php');
}

$redirect = $_GET['redirect'] ?? 'index.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        set_flash('danger', 'Vui lòng nhập đầy đủ Email và Mật khẩu.');
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];

            set_flash('success', 'Đăng nhập thành công! Chào mừng ' . $user['full_name']);

            if ($user['role'] === 'admin') {
                redirect('admin/index.php');
            } else {
                redirect($redirect);
            }
        } else {
            set_flash('danger', 'Email hoặc mật khẩu không chính xác.');
        }
    }
}

$pageTitle = "Đăng Nhập Tài Khoản";
require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding: 40px 20px 80px;">
    <div style="max-width: 440px; margin: 0 auto; background: #fff; border-radius: var(--radius-lg); border: 1px solid var(--border); padding: 36px; box-shadow: var(--shadow-md);">
        <div style="text-align: center; margin-bottom: 24px;">
            <h2 style="font-size: 1.6rem; color: var(--primary);">Đăng Nhập HieuMini</h2>
            <p style="color: var(--text-muted); font-size: 0.875rem; margin-top: 4px;">Chào mừng bạn quay trở lại với thời trang HieuMini</p>
        </div>

        <!-- Tài khoản mẫu gợi ý -->
        <div style="background: #f8fafc; border: 1px dashed var(--border); padding: 12px; border-radius: var(--radius-md); font-size: 0.8rem; margin-bottom: 20px; color: var(--secondary);">
            <div><strong>Tài khoản Quản trị:</strong> <code>admin@hieumini.vn</code> | MK: <code>admin123</code></div>
            <div><strong>Tài khoản Khách hàng:</strong> <code>khachhang@gmail.com</code> | MK: <code>admin123</code></div>
        </div>

        <form action="login.php?redirect=<?= urlencode($redirect) ?>" method="POST">
            <div class="form-group">
                <label class="form-label">Địa chỉ Email</label>
                <input type="email" name="email" class="form-control" placeholder="admin@hieumini.vn" required>
            </div>

            <div class="form-group">
                <label class="form-label">Mật khẩu</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-accent btn-lg btn-block" style="margin-top: 10px;">
                <i class="fa-solid fa-right-to-bracket"></i> Đăng Nhập
            </button>
        </form>

        <div style="text-align: center; margin-top: 24px; font-size: 0.875rem; color: var(--text-muted);">
            Chưa có tài khoản? <a href="register.php" style="color: var(--accent); font-weight: 700;">Đăng ký ngay</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
