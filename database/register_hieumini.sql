-- =====================================================================
--  ĐĂNG KÝ DỰ ÁN "HieuMini" VÀO HUB HIEU CEO
--  Chạy trên cơ sở dữ liệu của hub:  hieu_ceo_db
--  (phpMyAdmin → chọn hieu_ceo_db → thẻ Import → chọn tệp này)
--
--  Sau khi chạy, HieuMini sẽ xuất hiện tại:
--    - explore.php  → thẻ "Bộ Sưu Tập Giao Diện"
--    - live-view.php → trình xem mô phỏng đa thiết bị
--    - admin/projects.php → trạng thái "Đã đăng ký"
--
--  Tệp này chạy lại nhiều lần vẫn an toàn (dùng ON DUPLICATE KEY UPDATE).
-- =====================================================================

SET NAMES utf8mb4;
USE `hieu_ceo_db`;

-- ---------------------------------------------------------------------
-- 1. Danh mục mới: Thương mại số & Mã nguồn
-- ---------------------------------------------------------------------
INSERT INTO `theme_categories` (`id`, `name`, `slug`, `icon`, `description`, `badge_text`, `sort_order`)
VALUES (
  6,
  'Thương Mại Số & Mã Nguồn',
  'digital',
  'fa-code',
  'Giao diện chợ mã nguồn, sản phẩm số, giấy phép sử dụng nhiều mức và bàn giao tự động',
  'Digital Goods',
  6
)
ON DUPLICATE KEY UPDATE
  `name` = VALUES(`name`),
  `icon` = VALUES(`icon`),
  `description` = VALUES(`description`),
  `badge_text` = VALUES(`badge_text`);

-- ---------------------------------------------------------------------
-- 2. Đăng ký giao diện HieuMini
-- ---------------------------------------------------------------------
INSERT INTO `themes` (
  `category_id`, `name`, `slug`, `code_name`, `tagline`, `description`,
  `thumbnail`, `preview_url`, `folder_path`, `version`, `author`, `status`,
  `is_featured`, `rating`, `downloads_count`, `views_count`,
  `primary_color`, `secondary_color`, `accent_color`, `bg_color`,
  `font_family`, `layout_type`
) VALUES (
  6,
  'HieuMini — Chợ Mã Nguồn Website Chuẩn SEO',
  'hieumini-cho-ma-nguon-website',
  'HIEU_HIEUMINI',
  'Thương mại điện tử cho sản phẩm số: 3 mức giấy phép, giỏ hàng AJAX, 19/19 hạng mục SEO',
  'Website thương mại điện tử bán mã nguồn dự án website, viết bằng PHP 8 thuần và MySQL, không dùng framework. Gồm 12 bảng dữ liệu chuẩn 3NF, giỏ hàng AJAX, ba mức giấy phép sử dụng, mã giảm giá, đánh giá sản phẩm, blog chuẩn SEO và khu quản trị 11 trang. Tối ưu công cụ tìm kiếm đầy đủ với 6 loại dữ liệu có cấu trúc Schema.org, sitemap XML động, robots.txt và RSS. Giao diện Dark Neon Glassmorphism với 14 hiệu ứng chuyển động 60 FPS, tôn trọng tuỳ chọn giảm chuyển động.',
  'projects/HieuMini/assets/images/og-cover.svg',
  'projects/HieuMini/index.php',
  'projects/HieuMini',
  '1.0.0',
  'Trần Văn Minh Hiếu',
  'active',
  1,
  5.00,
  0,
  0,
  '#7C3AED',
  '#22D3EE',
  '#22D3EE',
  '#0B0B18',
  'Space Grotesk',
  'executive_glass'
)
ON DUPLICATE KEY UPDATE
  `category_id`      = VALUES(`category_id`),
  `name`             = VALUES(`name`),
  `tagline`          = VALUES(`tagline`),
  `description`      = VALUES(`description`),
  `thumbnail`        = VALUES(`thumbnail`),
  `preview_url`      = VALUES(`preview_url`),
  `folder_path`      = VALUES(`folder_path`),
  `status`           = VALUES(`status`),
  `is_featured`      = VALUES(`is_featured`),
  `primary_color`    = VALUES(`primary_color`),
  `secondary_color`  = VALUES(`secondary_color`),
  `accent_color`     = VALUES(`accent_color`),
  `bg_color`         = VALUES(`bg_color`),
  `font_family`      = VALUES(`font_family`);

-- ---------------------------------------------------------------------
-- 3. Khối nội dung mặc định cho trình tuỳ biến (customizer.php)
-- ---------------------------------------------------------------------
INSERT INTO `theme_sections` (`theme_id`, `section_key`, `section_name`, `is_enabled`, `sort_order`)
SELECT t.`id`, s.`k`, s.`n`, 1, s.`o`
FROM `themes` t
JOIN (
  SELECT 'hero_main'   AS `k`, 'Hero: Mã nguồn chuẩn SEO'        AS `n`, 1 AS `o`
  UNION ALL SELECT 'categories', 'Sáu danh mục dự án',             2
  UNION ALL SELECT 'featured',   'Dự án bán chạy nhất',            3
  UNION ALL SELECT 'features',   'Sáu cam kết chất lượng',         4
  UNION ALL SELECT 'steps',      'Bốn bước mua hàng',              5
  UNION ALL SELECT 'reviews',    'Đánh giá của khách hàng',        6
  UNION ALL SELECT 'blog',       'Blog kiến thức lập trình web',   7
  UNION ALL SELECT 'cta',        'Kêu gọi hành động cuối trang',   8
) s
WHERE t.`slug` = 'hieumini-cho-ma-nguon-website'
  AND NOT EXISTS (
    SELECT 1 FROM `theme_sections` ts
    WHERE ts.`theme_id` = t.`id` AND ts.`section_key` = s.`k`
  );

-- ---------------------------------------------------------------------
-- 4. Kiểm tra kết quả
-- ---------------------------------------------------------------------
SELECT t.`id`, t.`name`, t.`status`, t.`folder_path`, t.`preview_url`, c.`name` AS `danh_muc`
FROM `themes` t
JOIN `theme_categories` c ON c.`id` = t.`category_id`
WHERE t.`slug` = 'hieumini-cho-ma-nguon-website';
