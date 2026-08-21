<?php
/**
 * ====================================================================
 * HIEU CEO - ADMIN & EXECUTIVE ACCESS GUARD (QUYỀN TRUY CẬP QUẢN TRỊ)
 * ====================================================================
 * Tệp này chuyên biệt quản lý và kiểm soát toàn bộ quyền truy cập dành
 * cho Ban Điều Hành & Quản trị viên (CEO, CDO, Lead Dev, Admin).
 * Bất kỳ truy cập trái phép nào từ người dùng thường hoặc chưa đăng nhập
 * sẽ bị chặn lại hoặc chuyển hướng an toàn.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/helper.php';

/**
 * Danh sách các vai trò được phép truy cập phân hệ Quản trị Admin
 */
const ADMIN_ALLOWED_ROLES = ['ceo', 'cdo', 'admin', 'developer'];

/**
 * Kiểm tra xem người dùng hiện tại có đang đăng nhập với quyền Admin hay không
 */
function isAdminLoggedIn(): bool {
    if (empty($_SESSION['user_id']) || empty($_SESSION['user_role'])) {
        return false;
    }
    return in_array($_SESSION['user_role'], ADMIN_ALLOWED_ROLES, true);
}

/**
 * Lấy thông tin chi tiết của Quản trị viên / CEO đang đăng nhập
 */
function getAdminUser(): ?array {
    if (!isAdminLoggedIn()) {
        return null;
    }
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['user_name'] ?? '',
        'email' => $_SESSION['user_email'] ?? '',
        'full_name' => $_SESSION['user_fullname'] ?? '',
        'role' => $_SESSION['user_role'] ?? 'admin',
        'title' => $_SESSION['user_title'] ?? 'Executive Officer',
        'avatar' => $_SESSION['user_avatar'] ?? 'assets/images/ceo-avatar.png'
    ];
}

/**
 * Bắt buộc quyền Quản trị viên khi truy cập các trang trong khu vực admin/
 * 
 * @param array $roles Danh sách vai trò cụ thể được phép (mặc định là toàn bộ ADMIN_ALLOWED_ROLES)
 * @param string $loginRedirect Trang chuyển hướng khi chưa đăng nhập
 */
function requireAdminAuth(array $roles = ADMIN_ALLOWED_ROLES, string $loginRedirect = '../login.php'): void {
    // 0. MẶC ĐỊNH TẮT HẲN: Phân hệ Admin chỉ bật khi server có biến môi trường ADMIN_PASSWORD
    if (!isAdminEnabled()) {
        renderAdminDisabledPage();
        exit;
    }

    // 1. Chưa đăng nhập -> Chuyển hướng về cổng đăng nhập Admin
    if (empty($_SESSION['user_id']) || empty($_SESSION['user_role'])) {
        $_SESSION['flash_error'] = 'Khu vực quản trị yêu cầu đăng nhập tài khoản Ban Điều Hành (CEO / CDO / Admin).';
        header("Location: {$loginRedirect}");
        exit;
    }

    $currentRole = $_SESSION['user_role'];

    // 2. Đã đăng nhập nhưng là tài khoản Người Dùng thường (viewer / customer / user)
    if (!in_array($currentRole, $roles, true)) {
        renderAdminForbiddenPage($currentRole);
        exit;
    }
}

/**
 * Hiển thị giao diện thông báo Khóa Admin khi phân hệ Quản trị bị tắt mặc định
 * (Chỉ bật khi máy chủ được khởi chạy có truyền biến môi trường ADMIN_PASSWORD)
 */
function renderAdminDisabledPage(): void {
    http_response_code(403);
    ?>
    <!DOCTYPE html>
    <html lang="vi" data-theme="dark">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>403 - Phân Hệ Quản Trị Đang Tắt | HIEU CEO Security Guard</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link rel="stylesheet" href="../assets/css/ceo-core.css">
        <link rel="stylesheet" href="../assets/css/animations.css">
    </head>
    <body style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;">
        <div class="ceo-mesh-bg"></div>

        <div class="glass-card animate-scale-up" style="max-width:580px;width:100%;padding:40px;text-align:center;border-color:rgba(234,179,8,0.4);box-shadow:0 25px 60px rgba(0,0,0,0.8), 0 0 35px rgba(234,179,8,0.2);">
            <div style="width:76px;height:76px;border-radius:50%;background:rgba(234,179,8,0.15);border:1px solid rgba(234,179,8,0.4);display:flex;align-items:center;justify-content:center;margin:0 auto 20px auto;color:#facc15;font-size:2.2rem;">
                <i class="fa-solid fa-lock"></i>
            </div>

            <span class="badge-ceo badge-ready" style="background:rgba(234,179,8,0.2);color:#facc15;border-color:rgba(234,179,8,0.4);margin-bottom:12px;">
                🔒 ADMIN ACCESS DISABLED (DEFAULT)
            </span>

            <h1 style="font-size:1.75rem;font-weight:800;color:var(--text-primary);margin-bottom:12px;">
                Phân Hệ Quản Trị Đang Tắt
            </h1>

            <p style="color:var(--text-secondary);font-size:0.95rem;line-height:1.6;margin-bottom:24px;">
                Theo quy chuẩn bảo mật, toàn bộ trang Quản trị Admin của <strong>DoAnWebsite</strong> mặc định được <strong>tắt hoàn toàn</strong>. Phân hệ này chỉ được kích hoạt khi máy chủ được khởi chạy có thiết lập biến môi trường <code>ADMIN_PASSWORD</code>.
            </p>

            <div style="background:rgba(0,0,0,0.45);border:1px dashed rgba(234,179,8,0.3);border-radius:12px;padding:16px;margin-bottom:28px;text-align:left;font-size:0.88rem;">
                <div style="color:#facc15;font-weight:700;margin-bottom:8px;">
                    <i class="fa-solid fa-terminal mr-1"></i> Cách kích hoạt phân hệ Admin khi chạy Server:
                </div>
                <div style="color:var(--text-secondary);line-height:1.7;font-family:monospace;font-size:0.83rem;">
                    <div style="margin-bottom:6px;">
                        <span style="color:#94a3b8;"># PowerShell (Windows):</span><br>
                        <code style="color:#38bdf8;">$env:ADMIN_PASSWORD="mat_khau_cua_ban"; php -S localhost:8000</code>
                    </div>
                    <div style="margin-bottom:6px;">
                        <span style="color:#94a3b8;"># Linux / macOS / Bash:</span><br>
                        <code style="color:#38bdf8;">ADMIN_PASSWORD="mat_khau_cua_ban" php -S localhost:8000</code>
                    </div>
                    <div>
                        <span style="color:#94a3b8;"># Cloud / Docker (Render/Apache):</span><br>
                        <span style="color:#cbd5e1;">Thêm biến môi trường <code>ADMIN_PASSWORD</code> vào Environment Variables.</span>
                    </div>
                </div>
            </div>

            <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
                <a href="../explore.php" class="btn-ceo-primary btn-ripple" style="padding:10px 22px;">
                    <i class="fa-solid fa-compass mr-1"></i> Đến Cổng Khám Phá
                </a>
                <a href="../live-view.php" class="btn-ceo-secondary" style="padding:10px 20px;">
                    <i class="fa-solid fa-desktop mr-1"></i> Xem Trình Chiếu Live
                </a>
            </div>
        </div>
    </body>
    </html>
    <?php
}

/**
 * Kiểm tra quyền hạn chuyên biệt của từng cấp quản trị
 */
function checkAdminPermission(string $permission): bool {
    $role = $_SESSION['user_role'] ?? '';
    
    // CEO có toàn quyền tối cao
    if ($role === 'ceo') {
        return true;
    }

    // Phân quyền theo ma trận vai trò
    $permissionsMatrix = [
        'cdo' => ['view_themes', 'edit_themes', 'customize_tokens', 'view_components', 'view_analytics', 'upload_projects'],
        'developer' => ['view_themes', 'edit_themes', 'custom_css', 'view_logs', 'system_cache', 'upload_projects'],
        'admin' => ['view_themes', 'edit_themes', 'manage_users', 'view_logs', 'upload_projects']
    ];

    return in_array($permission, $permissionsMatrix[$role] ?? [], true);
}

/**
 * Hiển thị giao diện 403 Forbidden sang trọng khi người dùng thường cố truy cập khu vực Admin
 */
function renderAdminForbiddenPage(string $currentRole): void {
    http_response_code(403);
    ?>
    <!DOCTYPE html>
    <html lang="vi" data-theme="dark">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>403 - Quyền Truy Cập Bị Từ Chối | HIEU CEO Admin Guard</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link rel="stylesheet" href="../assets/css/ceo-core.css">
        <link rel="stylesheet" href="../assets/css/animations.css">
    </head>
    <body style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;">
        <div class="ceo-mesh-bg"></div>

        <div class="glass-card animate-scale-up" style="max-width:540px;width:100%;padding:40px;text-align:center;border-color:rgba(244,63,94,0.4);box-shadow:0 25px 60px rgba(0,0,0,0.8), 0 0 30px rgba(244,63,94,0.2);">
            <div style="width:72px;height:72px;border-radius:50%;background:rgba(244,63,94,0.15);border:1px solid rgba(244,63,94,0.4);display:flex;align-items:center;justify-content:center;margin:0 auto 20px auto;color:#fb7185;font-size:2rem;">
                <i class="fa-solid fa-shield-halved"></i>
            </div>

            <span class="badge-ceo badge-ready" style="background:rgba(244,63,94,0.2);color:#fb7185;border-color:rgba(244,63,94,0.4);margin-bottom:12px;">
                403 ACCESS RESTRICTED
            </span>

            <h1 style="font-size:1.8rem;font-weight:800;color:var(--text-primary);margin-bottom:10px;">
                Khu Vực Dành Riêng Cho Quản Trị
            </h1>

            <p style="color:var(--text-secondary);font-size:0.95rem;line-height:1.6;margin-bottom:24px;">
                Tài khoản của bạn hiện có vai trò <strong><?= strtoupper(htmlspecialchars($currentRole)) ?></strong>, không có thẩm quyền truy cập Trung tâm Điều Hành Admin CEO.
            </p>

            <div style="background:rgba(0,0,0,0.4);border:1px dashed rgba(255,255,255,0.1);border-radius:12px;padding:14px;margin-bottom:28px;text-align:left;font-size:0.85rem;">
                <div style="color:var(--text-accent);font-weight:700;margin-bottom:4px;">
                    <i class="fa-solid fa-circle-info mr-1"></i> Tài khoản Admin hợp lệ:
                </div>
                <div style="color:var(--text-secondary);">
                    • CEO: <code>ceo@hieu.vn</code> / <code>admin123</code><br>
                    • CDO: <code>cdo@hieu.vn</code> / <code>admin123</code><br>
                    • Lead Dev: <code>dev@hieu.vn</code> / <code>admin123</code>
                </div>
            </div>

            <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
                <a href="../explore.php" class="btn-ceo-primary btn-ripple" style="padding:10px 22px;">
                    <i class="fa-solid fa-compass mr-1"></i> Đến Cổng Khám Phá
                </a>
                <a href="../logout.php" class="btn-ceo-secondary" style="padding:10px 20px;">
                    <i class="fa-solid fa-right-from-bracket mr-1"></i> Đổi Tài Khoản
                </a>
            </div>
        </div>
    </body>
    </html>
    <?php
}

// Tự động kích hoạt kiểm tra quyền Admin khi tệp này được nạp
requireAdminAuth();
