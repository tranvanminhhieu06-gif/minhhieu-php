<?php
/**
 * ====================================================================
 * HIEU CEO - USER & CLIENT ACCESS GUARD (QUYỀN TRUY CẬP NGƯỜI DÙNG)
 * ====================================================================
 * Quản lý phiên đăng nhập người dùng, khách hàng, giỏ yêu thích,
 * lịch sử xem live và các tương tác đánh giá dự án website.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/helper.php';

// Khởi tạo các mảng phiên người dùng nếu chưa có
if (!isset($_SESSION['user_favorites'])) {
    $_SESSION['user_favorites'] = [];
}
if (!isset($_SESSION['user_recent_views'])) {
    $_SESSION['user_recent_views'] = [];
}

/**
 * Kiểm tra xem có người dùng (User hoặc Khách đã đăng nhập) hay không
 */
function isUserLoggedIn(): bool {
    return !empty($_SESSION['user_id']);
}

/**
 * Lấy thông tin hồ sơ của Người Dùng / Khách Hàng hiện tại
 */
function getUserProfile(): ?array {
    if (!isUserLoggedIn()) {
        return null;
    }
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['user_name'] ?? 'Khách Trải Nghiệm',
        'email' => $_SESSION['user_email'] ?? '',
        'full_name' => $_SESSION['user_fullname'] ?? 'Khách Thăm Quan',
        'role' => $_SESSION['user_role'] ?? 'viewer',
        'title' => $_SESSION['user_title'] ?? 'VIP Member',
        'avatar' => $_SESSION['user_avatar'] ?? 'assets/images/user-avatar.png',
        'favorites' => $_SESSION['user_favorites'] ?? [],
        'recent_views' => $_SESSION['user_recent_views'] ?? []
    ];
}

/**
 * Bắt buộc người dùng phải đăng nhập tài khoản Thành Viên trước khi thực hiện hành động
 */
function requireUserAuth(string $loginRedirect = 'user/login.php'): void {
    if (!isUserLoggedIn()) {
        $_SESSION['flash_warning'] = 'Vui lòng đăng nhập tài khoản Thành Viên để sử dụng tính năng này.';
        header("Location: {$loginRedirect}");
        exit;
    }
}

/**
 * Thêm hoặc gỡ một giao diện khỏi danh sách yêu thích của người dùng
 */
function toggleUserFavoriteTheme(int $themeId): bool {
    if (!isset($_SESSION['user_favorites'])) {
        $_SESSION['user_favorites'] = [];
    }
    $key = array_search($themeId, $_SESSION['user_favorites'], true);
    if ($key !== false) {
        unset($_SESSION['user_favorites'][$key]);
        $_SESSION['user_favorites'] = array_values($_SESSION['user_favorites']);
        return false; // Removed
    } else {
        $_SESSION['user_favorites'][] = $themeId;
        return true; // Added
    }
}

/**
 * Kiểm tra xem giao diện có nằm trong danh sách yêu thích của người dùng không
 */
function isThemeFavoritedByUser(int $themeId): bool {
    return in_array($themeId, $_SESSION['user_favorites'] ?? [], true);
}

/**
 * Lấy danh sách đầy đủ thông tin các theme được yêu thích
 */
function getUserFavoriteThemesDetailed(): array {
    $favoriteIds = $_SESSION['user_favorites'] ?? [];
    if (empty($favoriteIds)) {
        return [];
    }

    $allThemes = getAllThemes();
    $favorites = [];
    foreach ($allThemes as $t) {
        if (in_array((int)$t['id'], $favoriteIds, true)) {
            $favorites[] = $t;
        }
    }
    return $favorites;
}

/**
 * Ghi nhận lịch sử xem Live một dự án
 */
function recordProjectView(string $folderOrId, string $name = '', string $url = ''): void {
    if (!isset($_SESSION['user_recent_views'])) {
        $_SESSION['user_recent_views'] = [];
    }
    
    // Xóa mục trùng lặp trước đó
    foreach ($_SESSION['user_recent_views'] as $k => $item) {
        if ($item['key'] === $folderOrId) {
            unset($_SESSION['user_recent_views'][$k]);
            break;
        }
    }
    
    // Thêm mục mới lên đầu danh sách
    array_unshift($_SESSION['user_recent_views'], [
        'key' => $folderOrId,
        'name' => $name ?: $folderOrId,
        'url' => $url,
        'time' => time()
    ]);
    
    // Giữ tối đa 10 mục gần nhất
    $_SESSION['user_recent_views'] = array_slice($_SESSION['user_recent_views'], 0, 10);
}

/**
 * Lấy danh sách lịch sử xem gần đây
 */
function getUserRecentViews(): array {
    return $_SESSION['user_recent_views'] ?? [];
}

/**
 * Đăng ký tài khoản thành viên mới
 */
function registerMemberAccount(string $username, string $email, string $password, string $fullName, string $avatar = 'assets/images/user-avatar.png'): array {
    try {
        $db = getDb();
        
        // Kiểm tra trùng username hoặc email
        $stmt = $db->prepare("SELECT id FROM `users` WHERE `username` = :u OR `email` = :e LIMIT 1");
        $stmt->execute([':u' => $username, ':e' => $email]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Tên người dùng hoặc Email này đã tồn tại trong hệ thống.'];
        }
        
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $insert = $db->prepare("INSERT INTO `users` (`username`, `email`, `password_hash`, `full_name`, `role`, `title`, `avatar`, `status`, `created_at`, `last_login`) 
                                VALUES (:u, :e, :p, :fn, 'viewer', 'Thành Viên VIP', :av, 'active', NOW(), NOW())");
        $insert->execute([
            ':u' => $username,
            ':e' => $email,
            ':p' => $hash,
            ':fn' => $fullName,
            ':av' => $avatar
        ]);
        
        $newId = (int)$db->lastInsertId();
        
        // Tự động đăng nhập
        $_SESSION['user_id'] = $newId;
        $_SESSION['user_name'] = $username;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_fullname'] = $fullName;
        $_SESSION['user_role'] = 'viewer';
        $_SESSION['user_title'] = 'Thành Viên VIP';
        $_SESSION['user_avatar'] = $avatar;
        
        logUserAction('REGISTER', "Thành viên {$fullName} ({$email}) đăng ký tài khoản mới thành công.");
        
        return ['success' => true, 'user_id' => $newId];
    } catch (Exception $e) {
        if (str_contains($e->getMessage(), '2002') || str_contains($e->getMessage(), 'refused') || str_contains($e->getMessage(), 'actively refused')) {
            return ['success' => false, 'message' => 'Không thể kết nối đến máy chủ MySQL. Vui lòng mở XAMPP Control Panel và bấm Start tại mục MySQL.'];
        }
        return ['success' => false, 'message' => 'Lỗi hệ thống khi đăng ký: ' . $e->getMessage()];
    }
}

/**
 * Cập nhật thông tin tài khoản thành viên
 */
function updateUserProfile(int $userId, string $fullName, ?string $newPassword = null, ?string $avatar = null): array {
    try {
        $db = getDb();
        $fields = ["`full_name` = :fn"];
        $params = [':fn' => $fullName, ':id' => $userId];
        
        if (!empty($newPassword)) {
            $fields[] = "`password_hash` = :p";
            $params[':p'] = password_hash($newPassword, PASSWORD_BCRYPT);
        }
        if (!empty($avatar)) {
            $fields[] = "`avatar` = :av";
            $params[':av'] = $avatar;
        }
        
        $sql = "UPDATE `users` SET " . implode(', ', $fields) . " WHERE `id` = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        
        // Cập nhật session
        $_SESSION['user_fullname'] = $fullName;
        if (!empty($avatar)) {
            $_SESSION['user_avatar'] = $avatar;
        }
        
        logUserAction('UPDATE_PROFILE', "Thành viên ID {$userId} cập nhật thông tin hồ sơ.");
        return ['success' => true];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Lấy thông tin tài khoản mẫu & công nghệ cho từng dự án
 */
function getDemoCredentialsForProject(string $folderOrCode): array {
    $demoMap = [
        'HieuWeb01' => [
            'name' => 'Thời Trang HieuMini Luxury Studio',
            'tagline' => 'Đỉnh Cao Thương Mại Điện Tử May Mặc & Lookbook 3D',
            'admin_email' => 'admin@hieumini.vn',
            'admin_pass' => 'admin123',
            'customer_email' => 'khachhang@gmail.com',
            'customer_pass' => 'admin123',
            'tech' => ['PHP 8.2', 'MySQL', 'JavaScript ES6', 'VietQR MBBank', 'Glassmorphism UI'],
            'features' => ['Hero Slider', 'Flash Sale Countdown', 'Size Guide Modal', 'Giỏ hàng VietQR MBBank', 'Hóa đơn điện tử PDF', 'Quản lý đơn hàng']
        ],
        'HieuWeb02' => [
            'name' => 'Nhà Sách Hieu Bookstore Hub',
            'tagline' => 'Không Gian Đọc Đẳng Cấp & Thư Viện Tri Thức Số',
            'admin_email' => 'admin@hieubooks.vn',
            'admin_pass' => 'admin123',
            'customer_email' => 'docgia@gmail.com',
            'customer_pass' => 'admin123',
            'tech' => ['PHP 8.2', 'MySQL', 'Plus Jakarta Sans', 'E-Book Reader', 'Responsive 60FPS'],
            'features' => ['Đọc thử trích đoạn sách', 'Đánh giá sao độc giả', 'Bộ lọc thể loại đa chiều', 'Mã giảm giá FREESHIP', 'Tra cứu đơn hàng']
        ],
        'HieuWeb03' => [
            'name' => 'Nội Thất Hieu Living Decor',
            'tagline' => 'Kiến Trúc Nội Thất Tinh Tế Cho Không Gian Sống Xanh',
            'admin_email' => 'admin@hieuliving.vn',
            'admin_pass' => 'admin123',
            'customer_email' => 'khachhang@gmail.com',
            'customer_pass' => 'admin123',
            'tech' => ['PHP 8.2', 'MySQL', 'Showroom 3D', 'Bắc Âu Minimalist', 'CSS Grid'],
            'features' => ['Showroom 3D góc rộng', 'Bộ sưu tập theo phòng (Living, Bed, Office)', 'Tư vấn nội thất cá nhân hóa', 'Báo giá nhanh']
        ],
        'DatCyber' => [
            'name' => 'Gia Dụng Thông Minh DatCyber',
            'tagline' => 'Showroom Thiết Bị Gia Dụng Thông Minh & Tiện Ích Gia Đình',
            'admin_email' => 'admin@datcyber.vn',
            'admin_pass' => '123456',
            'customer_email' => 'khachhang@gmail.com',
            'customer_pass' => '123456',
            'tech' => ['PHP 8.2', 'MySQL', 'Bootstrap 5', 'AJAX Cart', 'Responsive UI'],
            'features' => ['Flash Sale đếm ngược', 'Giỏ hàng AJAX mượt mà', 'Mã giảm giá DATCYBER10', 'Quản lý đơn hàng & sản phẩm']
        ],
        'HieuWeb05' => [
            'name' => 'Thể Hình & Dinh Dưỡng Hieu Pro Gym',
            'tagline' => 'Nền Tảng Thể Hình Đỉnh Cao & Dinh Dưỡng Thể Thao',
            'admin_email' => 'admin@hieugym.vn',
            'admin_pass' => 'admin123',
            'customer_email' => 'hoi_vien@gmail.com',
            'customer_pass' => 'admin123',
            'tech' => ['PHP 8.2', 'MySQL', 'BMI Algorithm', 'Montserrat Athletic', 'Video Hero'],
            'features' => ['Máy tính chỉ số BMI khoa học', 'Bán thực phẩm bổ sung Whey Isolate & Creatine', 'Đặt lịch huấn luyện viên PT cá nhân', 'Lịch tập 7 ngày']
        ],
        'HieuCyberPortfolio' => [
            'name' => 'Hieu Cyber Portfolio Pro',
            'tagline' => 'Showcase Công Nghệ Đỉnh Cao & Hồ Sơ Năng Lực Trực Quan',
            'admin_email' => 'ceo@hieu.vn',
            'admin_pass' => 'admin123',
            'customer_email' => 'guest@hieu.vn',
            'customer_pass' => 'admin123',
            'tech' => ['HTML5', 'CSS3 Modern', 'JavaScript', 'Glassmorphism', 'High FPS'],
            'features' => ['Showcase dự án công nghệ', 'Giao diện tối ưu Cyberpunk', 'Tốc độ tải siêu mượt', 'Tương thích mọi thiết bị']
        ]
    ];

    // Tra cứu theo folder name hoặc key
    foreach ($demoMap as $k => $v) {
        if (strcasecmp($k, $folderOrCode) === 0 || str_contains(strtolower($folderOrCode), strtolower($k))) {
            return $v;
        }
    }

    return [
        'name' => $folderOrCode,
        'tagline' => 'Dự án website trực tuyến trong thư mục projects/' . $folderOrCode,
        'admin_email' => 'admin@' . strtolower($folderOrCode) . '.vn',
        'admin_pass' => 'admin123',
        'customer_email' => 'user@' . strtolower($folderOrCode) . '.vn',
        'customer_pass' => 'admin123',
        'tech' => ['PHP / HTML5', 'CSS3', 'JavaScript', 'Responsive'],
        'features' => ['Xem trực tiếp 60FPS', 'Tương thích đa thiết bị', 'Tích hợp cơ sở dữ liệu']
    ];
}

/**
 * Ghi nhận nhật ký tương tác của người dùng
 */
function logUserAction(string $action, string $details): void {
    $userId = $_SESSION['user_id'] ?? null;
    try {
        $db = getDb();
        $stmt = $db->prepare("INSERT INTO `system_logs` (`user_id`, `action_type`, `description`, `ip_address`) VALUES (:user_id, :action, :details, :ip)");
        $stmt->execute([
            ':user_id' => $userId,
            ':action' => 'USER_' . $action,
            ':details' => $details,
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ]);
    } catch (Exception $e) {
        // Silently continue
    }
}
