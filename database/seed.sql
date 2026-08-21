-- Seed Data for HIEU CEO Theme Hub
USE `hieu_ceo_db`;

-- 1. Insert Executive Users (Default Password for all: admin123)
-- Password hash for 'admin123' using BCRYPT: $2y$10$wNqV9N1mB49B61QYvYF8v.d8P39rA01Q9Q8L3fC4n7T8q6Z5j5U4i (or generated via PHP)
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `full_name`, `role`, `title`, `avatar`, `status`, `last_login`) VALUES
(1, 'ceo_hieu', 'ceo@hieu.vn', '$2y$10$08PuJVj3F30zADh3flUCXOzAAUnAi0wGxUURigpesUcjv2EynPxLC', 'HIEU TRAN', 'ceo', 'Founder & Chief Executive Officer', 'assets/images/ceo-avatar.png', 'active', NOW()),
(2, 'cdo_elena', 'cdo@hieu.vn', '$2y$10$08PuJVj3F30zADh3flUCXOzAAUnAi0wGxUURigpesUcjv2EynPxLC', 'Elena Vance', 'cdo', 'Chief Design Officer', 'assets/images/cdo-avatar.png', 'active', NOW()),
(3, 'dev_alex', 'dev@hieu.vn', '$2y$10$08PuJVj3F30zADh3flUCXOzAAUnAi0wGxUURigpesUcjv2EynPxLC', 'Alex Thorne', 'developer', 'Lead Theme Architect', 'assets/images/dev-avatar.png', 'active', NOW()),
(4, 'viewer_guest', 'guest@hieu.vn', '$2y$10$08PuJVj3F30zADh3flUCXOzAAUnAi0wGxUURigpesUcjv2EynPxLC', 'Khách Thăm Quan', 'viewer', 'VIP Observer', 'assets/images/viewer-avatar.png', 'active', NOW());

-- 2. Insert Theme Categories
INSERT INTO `theme_categories` (`id`, `name`, `slug`, `icon`, `description`, `badge_text`, `sort_order`) VALUES
(1, 'Thời Trang & May Mặc', 'fashion', 'fa-vest-patches', 'Giao diện thương mại điện tử chuyên ngành may mặc cao cấp, lookbook tương tác, flash sale giờ vàng', 'Hot Trend', 1),
(2, 'Công Nghệ & Thiết Bị AI', 'tech', 'fa-microchip', 'Giao diện showroom công nghệ, điện thoại, máy tính, phụ kiện viễn thông và giải pháp IoT', 'High Performance', 2),
(3, 'Tri Thức & Sách Nghệ Thuật', 'books', 'fa-book-open-reader', 'Giao diện nhà sách hiện đại, thư viện điện tử, học liệu trực tuyến và câu lạc bộ độc giả', 'Educational', 3),
(4, 'Nội Thất & Decor Tối Giản', 'furniture', 'fa-couch', 'Giao diện nội thất kiến trúc Bắc Âu, không gian sống sang trọng, vật liệu decor cao cấp', 'Luxury Living', 4),
(5, 'Thể Hình & Dinh Dưỡng Elite', 'fitness', 'fa-dumbbell', 'Giao diện phòng Gym đẳng cấp, tính toán chỉ số BMI, bán thực phẩm bổ sung Whey & Protein', 'Pro Athlete', 5);

-- 3. Insert Master Themes
INSERT INTO `themes` (`id`, `category_id`, `name`, `slug`, `code_name`, `tagline`, `description`, `thumbnail`, `preview_url`, `folder_path`, `version`, `author`, `status`, `is_featured`, `rating`, `downloads_count`, `views_count`, `primary_color`, `secondary_color`, `accent_color`, `bg_color`, `font_family`, `layout_type`) VALUES
(1, 1, 'HieuMini Luxury Fashion Studio', 'hieumini-fashion-studio', 'HIEU_WEB_01', 'Đỉnh Cao Thương Mại Điện Tử May Mặc & Lookbook 3D', 'Hệ thống website thời trang may mặc thế hệ mới với Hero Slider trình chiếu bộ sưu tập, Flash Sale đồng hồ đếm ngược, bộ lọc size/giá đa chiều, Size Guide quy đổi kích cỡ và giỏ hàng thông minh tích hợp VietQR MBBank.', 'assets/images/themes/fashion-preview.png', 'projects/HieuWeb01/index.php', 'HieuWeb01', '2.4.0', 'HIEU CEO Studio', 'active', 1, 4.98, 1420, 8940, '#6366f1', '#ec4899', '#06b6d4', '#0f172a', 'Outfit', 'executive_glass'),
(2, 3, 'Hieu Modern Book & Publishing Hub', 'hieu-modern-bookstore', 'HIEU_WEB_02', 'Không Gian Đọc Đẳng Cấp & Thư Viện Tri Thức Số', 'Giao diện hiệu sách sang trọng với danh mục đa dạng từ văn học, kinh tế, công nghệ đến kỹ năng sống; hỗ trợ đọc thử mẫu, đánh giá độc giả, mã giảm giá và trang cá nhân theo dõi đơn.', 'assets/images/themes/book-preview.png', 'projects/HieuWeb02/index.php', 'HieuWeb02', '2.1.0', 'HIEU CEO Studio', 'ready', 1, 4.93, 1150, 6420, '#0284c7', '#f59e0b', '#10b981', '#0b1329', 'Plus Jakarta Sans', 'executive_glass'),
(3, 4, 'Hieu Living & Scandinavian Decor', 'hieu-living-decor', 'HIEU_WEB_03', 'Kiến Trúc Nội Thất Tinh Tế Cho Không Gian Sống Xanh', 'Giao diện nội thất theo phong cách tối giản Bắc Âu, tối ưu hình ảnh sản phẩm góc rộng, bộ sưu tập phòng khách, phòng ngủ, phòng làm việc cùng tư vấn thiết kế nội thất cá nhân hóa.', 'assets/images/themes/furniture-preview.png', 'projects/HieuWeb03/index.php', 'HieuWeb03', '2.2.0', 'HIEU CEO Studio', 'ready', 1, 4.96, 980, 5200, '#10b981', '#64748b', '#f59e0b', '#0f172a', 'Outfit', 'executive_glass'),
(4, 2, 'Hieu CyberTech Innovation Matrix', 'hieu-cybertech-gadgets', 'HIEU_WEB_04', 'Showroom Thiết Bị Công Nghệ & Hệ Sinh Thái AI', 'Website bán lẻ điện thoại flagship, laptop gaming, linh kiện PC và phụ kiện thông minh; tích hợp so sánh thông số kỹ thuật, bảo hành điện tử và mua trả góp 0%.', 'assets/images/themes/tech-preview.png', 'projects/HieuWeb04/index.php', 'HieuWeb04', '2.5.0', 'HIEU CEO Studio', 'ready', 1, 4.97, 1890, 11400, '#8b5cf6', '#3b82f6', '#06b6d4', '#090d16', 'Outfit', 'executive_glass'),
(5, 5, 'Hieu Pro Gym & Athletic Matrix', 'hieu-gym-fitness-pro', 'HIEU_WEB_05', 'Nền Tảng Thể Hình Đỉnh Cao & Dinh Dưỡng Thể Thao', 'Website phòng tập Gym & phân phối thực phẩm bổ sung Whey Isolate, Creatine; tích hợp công cụ tính toán chỉ số BMI khoa học, lịch tập cá nhân và gói tập PT cao cấp.', 'assets/images/themes/gym-preview.png', 'projects/HieuWeb05/index.php', 'HieuWeb05', '2.6.0', 'HIEU CEO Studio', 'ready', 1, 4.99, 2140, 14250, '#ef4444', '#f97316', '#eab308', '#0b0f19', 'Montserrat', 'executive_glass'),
(6, 2, 'HieuMini Market Đồ Án Website', 'hieumini-source-market', 'HIEU_WEB_06', 'Sàn Thương Mại Điện Tử & Phân Phối Mã Nguồn Web PHP', 'Nền tảng mua bán mã nguồn website PHP MySQL chất lượng cao, code sạch, chuẩn SEO, bảo mật tối ưu và kiểm thử tự động 37 hạng mục.', 'assets/images/themes/minimart-preview.png', 'projects/HieuWeb06/index.php', 'HieuWeb06', '3.0.0', 'HIEU CEO Studio', 'ready', 1, 5.00, 2890, 16800, '#7c3aed', '#06b6d4', '#10b981', '#0b0b1a', 'Outfit', 'executive_glass');


-- 4. Insert Theme Sections
INSERT INTO `theme_sections` (`theme_id`, `section_key`, `section_name`, `is_enabled`, `sort_order`, `config_json`) VALUES
(1, 'hero_slider', 'Hero Carousel & Dynamic Banners', 1, 1, '{"animation": "fade-slide", "autoplay": true, "duration": 5000}'),
(1, 'flash_sale', 'Flash Sale Countdown & Live Deals', 1, 2, '{"show_timer": true, "discount_badge": true}'),
(1, 'featured_categories', 'Grid 7 Danh Mục Thời Trang', 1, 3, '{"columns": 4, "hover_zoom": true}'),
(1, 'best_sellers', 'Sản Phẩm Bán Chạy Nhất', 1, 4, '{"limit": 8, "show_ratings": true}'),
(1, 'brand_lookbook', 'Lookbook & Phong Cách Trẻ', 1, 5, '{"masonry": true, "lightbox": true}'),
(1, 'newsletter_cta', 'Đăng Ký Nhận Voucher 10%', 1, 6, '{"button_glow": true}'),
(2, 'hero_search', 'Hero Search & Sách Nổi Bật', 1, 1, '{"show_quote": true}'),
(2, 'book_categories', 'Danh Mục Sách Theo Thể Loại', 1, 2, '{"limit": 6}'),
(2, 'new_releases', 'Sách Mới Phát Hành', 1, 3, '{"limit": 8}'),
(3, 'hero_interior', 'Hero Phòng Khách & Showroom 3D', 1, 1, '{"video_bg": false}'),
(3, 'room_planner', 'Bộ Sưu Tập Theo Không Gian', 1, 2, '{"tabs": ["living", "bedroom", "office"]}'),
(4, 'hero_tech_matrix', 'Hero Flagship & Particle Grid', 1, 1, '{"particles": true}'),
(4, 'tech_specs', 'So Sánh Cấu Hình Nhanh', 1, 2, '{"specs_limit": 4}'),
(5, 'hero_gym_matrix', 'Hero Động Lực Tập Luyện & Video', 1, 1, '{"glitch_effect": true}'),
(5, 'bmi_calculator', 'Máy Tính Chỉ Số Cơ Thể BMI', 1, 2, '{"auto_diet_plan": true}');

-- 5. Insert Theme Tokens
INSERT INTO `theme_tokens` (`theme_id`, `token_key`, `token_value`, `token_type`) VALUES
(1, '--ceo-primary', '#6366f1', 'color'),
(1, '--ceo-secondary', '#ec4899', 'color'),
(1, '--ceo-accent', '#06b6d4', 'color'),
(1, '--ceo-bg-dark', '#0f172a', 'color'),
(1, '--ceo-radius', '16px', 'radius'),
(1, '--ceo-font', 'Outfit, sans-serif', 'typography'),
(1, '--ceo-glass-blur', '20px', 'glass'),
(1, '--ceo-glass-opacity', '0.65', 'glass'),
(2, '--ceo-primary', '#0284c7', 'color'),
(2, '--ceo-secondary', '#f59e0b', 'color'),
(2, '--ceo-font', 'Plus Jakarta Sans, sans-serif', 'typography');

-- 6. Insert UI Components
INSERT INTO `ui_components` (`id`, `category`, `name`, `slug`, `description`, `html_code`, `css_code`, `js_code`, `preview_badge`, `tags`) VALUES
(1, 'Buttons', 'CEO Glass Neon Button', 'btn-glass-neon', 'Nút bấm hiệu ứng kính mờ viền neon phát sáng khi hover với chuyển động mượt mà', '<button class="ceo-btn-neon"><span class="glow"></span><i class="fa-solid fa-bolt mr-2"></i> Khám Phá Ngay</button>', '.ceo-btn-neon { background: rgba(99,102,241,0.2); backdrop-filter: blur(12px); border: 1px solid rgba(99,102,241,0.5); color: #fff; padding: 12px 28px; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 0 20px rgba(99,102,241,0.25); } .ceo-btn-neon:hover { transform: translateY(-3px) scale(1.03); box-shadow: 0 0 35px rgba(99,102,241,0.6); background: rgba(99,102,241,0.4); }', NULL, 'Luxury Pro', 'button,glass,neon,glow'),
(2, 'Cards', 'Executive 3D Tilt Card', 'card-3d-tilt', 'Thẻ giao diện tích hợp hiệu ứng nghiêng 3D theo con trỏ chuột và viền gradient', '<div class="ceo-card-3d"><div class="card-inner"><div class="badge-ceo">TOP CHOICE</div><h3>Hieu Obsidian AI</h3><p>Hệ thống quản trị và tự động hóa cho tập đoàn đa quốc gia.</p></div></div>', '.ceo-card-3d { background: linear-gradient(135deg, rgba(30,41,59,0.7), rgba(15,23,42,0.8)); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; padding: 24px; backdrop-filter: blur(16px); transition: all 0.4s ease; } .ceo-card-3d:hover { border-color: rgba(99,102,241,0.6); box-shadow: 0 20px 40px rgba(0,0,0,0.4), 0 0 30px rgba(99,102,241,0.2); transform: translateY(-6px); }', NULL, '3D Motion', 'card,glass,3d,tilt'),
(3, 'Stats', 'Pulsing Metric Counter Badge', 'stat-pulse-badge', 'Khối thống kê có chấm đèn LED nhấp nháy báo hiệu dữ liệu thời gian thực', '<div class="stat-badge-pulse"><span class="led-pulse"></span><span class="stat-label">Server Uptime:</span><strong class="stat-val">99.98%</strong></div>', '.stat-badge-pulse { display: inline-flex; align-items: center; gap: 8px; background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); padding: 6px 14px; border-radius: 9999px; color: #34d399; font-size: 13px; } .led-pulse { width: 8px; height: 8px; background: #10b981; border-radius: 50%; box-shadow: 0 0 10px #10b981; animation: pulseGlow 1.5s infinite; } @keyframes pulseGlow { 0%, 100% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.4); opacity: 0.5; } }', NULL, 'Live Data', 'stats,pulse,badge,counter'),
(4, 'Navigation', 'Floating Glass Navbar', 'nav-floating-glass', 'Thanh điều hướng nổi lơ lửng với hiệu ứng kính mờ và menu tương tác thông minh', '<nav class="ceo-floating-nav"><div class="logo"><i class="fa-solid fa-crown mr-2"></i> HIEU CEO</div><ul class="nav-links"><li><a href="#" class="active">Trang Chủ</a></li><li><a href="#">Giao Diện</a></li><li><a href="#">Tùy Biến</a></li></ul></nav>', '.ceo-floating-nav { position: fixed; top: 20px; left: 50%; transform: translateX(-50%); width: calc(100% - 40px); max-width: 1200px; background: rgba(15,23,42,0.75); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.12); border-radius: 18px; padding: 14px 28px; display: flex; align-items: center; justify-content: space-between; z-index: 1000; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }', NULL, 'Core UI', 'navbar,floating,glass');

-- 7. Insert CEO Strategic Metrics
INSERT INTO `ceo_metrics` (`id`, `metric_key`, `metric_title`, `current_value`, `target_value`, `change_percent`, `is_positive`, `metric_unit`, `metric_icon`, `chart_data_json`) VALUES
(1, 'active_themes_count', 'Giao Diện Đang Vận Hành', '7', '10', '+40.0%', 1, 'Websites', 'fa-cubes', '{"labels":["T1","T2","T3","T4","T5","T6","T7","T8"],"values":[3,4,4,5,5,6,6,7]}'),
(2, 'total_pageviews', 'Tổng Lượt Truy Cập Tháng', '3,428,900', '4,000,000', '+32.8%', 1, 'Lượt Xem', 'fa-eye', '{"labels":["T1","T2","T3","T4","T5","T6","T7","T8"],"values":[1.2,1.5,1.9,2.2,2.5,2.9,3.1,3.4]}'),
(3, 'conversion_rate', 'Tỉ Lệ Chuyển Đổi Khách Hàng', '6.84%', '7.50%', '+18.5%', 1, 'Tỉ Lệ %', 'fa-arrow-trend-up', '{"labels":["T1","T2","T3","T4","T5","T6","T7","T8"],"values":[4.2,4.6,5.1,5.5,5.9,6.2,6.5,6.8]}'),
(4, 'avg_page_speed', 'Tốc Độ Tải Giao Diện (LCP)', '0.38s', '0.50s', '-42.0%', 1, 'Giây (Ultra Fast)', 'fa-gauge-high', '{"labels":["T1","T2","T3","T4","T5","T6","T7","T8"],"values":[0.85,0.72,0.61,0.54,0.48,0.44,0.40,0.38]}');

-- 8. Insert Theme Analytics Records
INSERT INTO `theme_analytics` (`theme_id`, `report_date`, `pageviews`, `unique_visitors`, `bounce_rate`, `avg_load_time_ms`, `conversion_rate`) VALUES
(1, CURDATE() - INTERVAL 6 DAY, 12500, 8900, 24.5, 380, 7.20),
(1, CURDATE() - INTERVAL 5 DAY, 14200, 9800, 23.8, 375, 7.45),
(1, CURDATE() - INTERVAL 4 DAY, 15800, 11200, 22.1, 360, 7.80),
(1, CURDATE() - INTERVAL 3 DAY, 18900, 13400, 21.5, 350, 8.15),
(1, CURDATE() - INTERVAL 2 DAY, 21400, 15200, 20.8, 340, 8.40),
(1, CURDATE() - INTERVAL 1 DAY, 24600, 17800, 19.9, 335, 8.90),
(1, CURDATE(), 28400, 20500, 18.5, 330, 9.25),
(5, CURDATE(), 31200, 22100, 17.2, 310, 9.60),
(4, CURDATE(), 26800, 19400, 21.0, 345, 8.70);

-- 9. Insert System Logs
INSERT INTO `system_logs` (`id`, `user_id`, `action_type`, `description`, `ip_address`, `created_at`) VALUES
(1, 1, 'AUTH_LOGIN', 'CEO HIEU TRAN đăng nhập thành công vào Hệ thống Điều hành', '127.0.0.1', NOW() - INTERVAL 2 HOUR),
(2, 1, 'THEME_ACTIVATE', 'Kích hoạt thành công giao diện mặc định: HieuMini Luxury Fashion Studio (HIEU_WEB_01)', '127.0.0.1', NOW() - INTERVAL 1 HOUR),
(3, 2, 'THEME_CUSTOMIZE', 'CDO Elena Vance cập nhật bảng màu neon và typography font Outfit cho toàn bộ giao diện', '127.0.0.1', NOW() - INTERVAL 45 MINUTE),
(4, 3, 'CACHE_FLUSH', 'Lead Theme Architect Alex Thorne xóa cache toàn hệ thống và tối ưu CSS rendering', '127.0.0.1', NOW() - INTERVAL 15 MINUTE);

-- 10. Insert System Settings
INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_group`, `description`) VALUES
('site_name', 'HIEU CEO - Master Website Interface & Theme Hub', 'general', 'Tên thương hiệu hệ thống quản trị giao diện'),
('site_tagline', 'Hệ Thống Quản Lý Giao Diện Website & Trình Tùy Biến Trực Quan Chuẩn CEO', 'general', 'Khẩu hiệu thương hiệu'),
('site_logo', 'assets/images/hieu-ceo-logo.png', 'general', 'Logo thương hiệu'),
('active_theme_id', '1', 'theme', 'ID của giao diện đang được kích hoạt chính'),
('default_font', 'Outfit', 'theme', 'Phông chữ tiêu chuẩn cho toàn hệ thống'),
('dark_mode_default', '1', 'appearance', 'Bật chế độ Dark Mode Luxury mặc định'),
('animation_level', 'smooth_ultra', 'appearance', 'Mức độ hiệu ứng chuyển động và micro-interactions'),
('maintenance_mode', '0', 'system', 'Chế độ bảo trì hệ thống (0: Tắt, 1: Bật)'),
('cache_status', 'enabled', 'system', 'Trạng thái bộ nhớ đệm'),
('api_rate_limit', '120', 'security', 'Giới hạn số lượt gọi API mỗi phút'),
('csrf_protection', '1', 'security', 'Bảo vệ biểu mẫu với Token CSRF');
