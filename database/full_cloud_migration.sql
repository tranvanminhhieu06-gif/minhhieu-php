-- =========================================================
-- 👑 HIEU CEO - FULL CLOUD CONSOLIDATED DATABASE DUMP 👑
-- =========================================================

-- 1. MASTER PORTAL DB (hieu_ceo_db)
-- HIEU CEO - Master Website Interface & Theme Hub
-- Database Schema for MySQL 8.0+ / MariaDB 10.4+

CREATE DATABASE IF NOT EXISTS `hieu_ceo_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `hieu_ceo_db`;

-- Drop existing tables in reverse dependency order
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `system_logs`;
DROP TABLE IF EXISTS `theme_analytics`;
DROP TABLE IF EXISTS `theme_tokens`;
DROP TABLE IF EXISTS `theme_sections`;
DROP TABLE IF EXISTS `ui_components`;
DROP TABLE IF EXISTS `ceo_metrics`;
DROP TABLE IF EXISTS `system_settings`;
DROP TABLE IF EXISTS `themes`;
DROP TABLE IF EXISTS `theme_categories`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Users Table (Executive & Design Team)
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `role` ENUM('ceo', 'cdo', 'developer', 'viewer') NOT NULL DEFAULT 'ceo',
    `title` VARCHAR(100) DEFAULT 'Chief Executive Officer',
    `avatar` VARCHAR(255) DEFAULT 'assets/images/ceo-avatar.png',
    `status` ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    `last_login` DATETIME NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Theme Categories Table
CREATE TABLE `theme_categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `icon` VARCHAR(50) DEFAULT 'fa-layer-group',
    `description` TEXT,
    `badge_text` VARCHAR(50) DEFAULT NULL,
    `sort_order` INT DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Themes Master Table
CREATE TABLE `themes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `slug` VARCHAR(150) NOT NULL UNIQUE,
    `code_name` VARCHAR(50) NOT NULL,
    `tagline` VARCHAR(255) DEFAULT NULL,
    `description` TEXT,
    `thumbnail` VARCHAR(255) DEFAULT NULL,
    `preview_url` VARCHAR(255) NOT NULL,
    `folder_path` VARCHAR(255) NOT NULL,
    `version` VARCHAR(20) DEFAULT '1.0.0',
    `author` VARCHAR(100) DEFAULT 'HIEU CEO Studio',
    `status` ENUM('active', 'ready', 'beta', 'archived') NOT NULL DEFAULT 'ready',
    `is_featured` TINYINT(1) DEFAULT 0,
    `rating` DECIMAL(3,2) DEFAULT 5.00,
    `downloads_count` INT DEFAULT 0,
    `views_count` INT DEFAULT 0,
    `primary_color` VARCHAR(20) DEFAULT '#6366f1',
    `secondary_color` VARCHAR(20) DEFAULT '#ec4899',
    `accent_color` VARCHAR(20) DEFAULT '#06b6d4',
    `bg_color` VARCHAR(20) DEFAULT '#0f172a',
    `font_family` VARCHAR(50) DEFAULT 'Outfit',
    `layout_type` VARCHAR(50) DEFAULT 'executive_glass',
    `custom_css` TEXT NULL,
    `custom_js` TEXT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`category_id`) REFERENCES `theme_categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Theme Sections Table (For Live Modular Customizer)
CREATE TABLE `theme_sections` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `theme_id` INT NOT NULL,
    `section_key` VARCHAR(50) NOT NULL,
    `section_name` VARCHAR(100) NOT NULL,
    `is_enabled` TINYINT(1) DEFAULT 1,
    `sort_order` INT DEFAULT 0,
    `config_json` JSON NULL,
    FOREIGN KEY (`theme_id`) REFERENCES `themes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Theme Custom Tokens Table
CREATE TABLE `theme_tokens` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `theme_id` INT NOT NULL,
    `token_key` VARCHAR(50) NOT NULL,
    `token_value` VARCHAR(255) NOT NULL,
    `token_type` VARCHAR(30) DEFAULT 'color',
    FOREIGN KEY (`theme_id`) REFERENCES `themes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. UI Components Library Table
CREATE TABLE `ui_components` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `category` VARCHAR(50) NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `description` TEXT,
    `html_code` TEXT NOT NULL,
    `css_code` TEXT NULL,
    `js_code` TEXT NULL,
    `preview_badge` VARCHAR(50) DEFAULT 'CEO Ready',
    `tags` VARCHAR(255) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. CEO Executive Metrics Table
CREATE TABLE `ceo_metrics` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `metric_key` VARCHAR(50) NOT NULL UNIQUE,
    `metric_title` VARCHAR(100) NOT NULL,
    `current_value` VARCHAR(50) NOT NULL,
    `target_value` VARCHAR(50) NOT NULL,
    `change_percent` VARCHAR(20) DEFAULT '+0%',
    `is_positive` TINYINT(1) DEFAULT 1,
    `metric_unit` VARCHAR(30) DEFAULT '',
    `metric_icon` VARCHAR(50) DEFAULT 'fa-chart-line',
    `chart_data_json` JSON NULL,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Theme Analytics Table
CREATE TABLE `theme_analytics` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `theme_id` INT NOT NULL,
    `report_date` DATE NOT NULL,
    `pageviews` INT DEFAULT 0,
    `unique_visitors` INT DEFAULT 0,
    `bounce_rate` DECIMAL(5,2) DEFAULT 0.00,
    `avg_load_time_ms` INT DEFAULT 0,
    `conversion_rate` DECIMAL(5,2) DEFAULT 0.00,
    FOREIGN KEY (`theme_id`) REFERENCES `themes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. System Logs Table (Audit & Security)
CREATE TABLE `system_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `action_type` VARCHAR(50) NOT NULL,
    `description` TEXT NOT NULL,
    `ip_address` VARCHAR(45) DEFAULT '127.0.0.1',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. System Settings Table
CREATE TABLE `system_settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(50) NOT NULL UNIQUE,
    `setting_value` TEXT NOT NULL,
    `setting_group` VARCHAR(50) DEFAULT 'general',
    `description` VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add Performance Indexes
CREATE INDEX idx_themes_status ON `themes`(`status`);
CREATE INDEX idx_themes_category ON `themes`(`category_id`);
CREATE INDEX idx_analytics_date ON `theme_analytics`(`report_date`);
CREATE INDEX idx_logs_created ON `system_logs`(`created_at`);

USE hieu_ceo_db;
-- Seed Data for HIEU CEO Theme Hub
USE `hieu_ceo_db`;

-- 1. Insert Executive Users (Default Password for all: admin123)
-- Password hash for 'admin123' using BCRYPT: $2y$10$wNqV9N1mB49B61QYvYF8v.d8P39rA01Q9Q8L3fC4n7T8q6Z5j5U4i (or generated via PHP)
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `full_name`, `role`, `title`, `avatar`, `status`, `last_login`) VALUES
(1, 'ceo_hieu', 'ceo@hieu.vn', '$2y$10$xW77Q2FsmMh91aMhSbgOQOP8jG8bUfq1H5Z7R0u5vL9Lp5vE5xK9a', 'HIEU TRAN', 'ceo', 'Founder & Chief Executive Officer', 'assets/images/ceo-avatar.png', 'active', NOW()),
(2, 'cdo_elena', 'cdo@hieu.vn', '$2y$10$xW77Q2FsmMh91aMhSbgOQOP8jG8bUfq1H5Z7R0u5vL9Lp5vE5xK9a', 'Elena Vance', 'cdo', 'Chief Design Officer', 'assets/images/cdo-avatar.png', 'active', NOW()),
(3, 'dev_alex', 'dev@hieu.vn', '$2y$10$xW77Q2FsmMh91aMhSbgOQOP8jG8bUfq1H5Z7R0u5vL9Lp5vE5xK9a', 'Alex Thorne', 'developer', 'Lead Theme Architect', 'assets/images/dev-avatar.png', 'active', NOW()),
(4, 'viewer_guest', 'guest@hieu.vn', '$2y$10$xW77Q2FsmMh91aMhSbgOQOP8jG8bUfq1H5Z7R0u5vL9Lp5vE5xK9a', 'KhÃ¡ch ThÄƒm Quan', 'viewer', 'VIP Observer', 'assets/images/viewer-avatar.png', 'active', NOW());

-- 2. Insert Theme Categories
INSERT INTO `theme_categories` (`id`, `name`, `slug`, `icon`, `description`, `badge_text`, `sort_order`) VALUES
(1, 'Thá» i Trang & May Máº·c', 'fashion', 'fa-vest-patches', 'Giao diá»‡n thÆ°Æ¡ng máº¡i Ä‘iá»‡n tá»­ chuyÃªn ngÃ nh may máº·c cao cáº¥p, lookbook tÆ°Æ¡ng tÃ¡c, flash sale giá»  vÃ ng', 'Hot Trend', 1),
(2, 'CÃ´ng Nghá»‡ & Thiáº¿t Bá»‹ AI', 'tech', 'fa-microchip', 'Giao diá»‡n showroom cÃ´ng nghá»‡, Ä‘iá»‡n thoáº¡i, mÃ¡y tÃ­nh, phá»¥ kiá»‡n viá»…n thÃ´ng vÃ  giáº£i phÃ¡p IoT', 'High Performance', 2),
(3, 'Tri Thá»©c & SÃ¡ch Nghá»‡ Thuáº­t', 'books', 'fa-book-open-reader', 'Giao diá»‡n nhÃ  sÃ¡ch hiá»‡n Ä‘áº¡i, thÆ° viá»‡n Ä‘iá»‡n tá»­, há» c liá»‡u trá»±c tuyáº¿n vÃ  cÃ¢u láº¡c bá»™ Ä‘á»™c giáº£', 'Educational', 3),
(4, 'Ná»™i Tháº¥t & Decor Tá»‘i Giáº£n', 'furniture', 'fa-couch', 'Giao diá»‡n ná»™i tháº¥t kiáº¿n trÃºc Báº¯c Ã‚u, khÃ´ng gian sá»‘ng sang trá» ng, váº­t liá»‡u decor cao cáº¥p', 'Luxury Living', 4),
(5, 'Thá»ƒ HÃ¬nh & Dinh DÆ°á»¡ng Elite', 'fitness', 'fa-dumbbell', 'Giao diá»‡n phÃ²ng Gym Ä‘áº³ng cáº¥p, tÃ­nh toÃ¡n chá»‰ sá»‘ BMI, bÃ¡n thá»±c pháº©m bá»• sung Whey & Protein', 'Pro Athlete', 5),
(6, 'Ná» n Táº£ng Doanh Nghiá»‡p SaaS', 'saas', 'fa-cloud-arrow-up', 'Giao diá»‡n quáº£n trá»‹ Ä‘iá»‡n toÃ¡n Ä‘Ã¡m mÃ¢y, AI Automation, giáº£i phÃ¡p chuyá»ƒn Ä‘á»•i sá»‘ cho táº­p Ä‘oÃ n', 'Enterprise', 6),
(7, 'Báº¥t Ä á»™ng Sáº£n & Nghá»‰ DÆ°á»¡ng', 'realestate', 'fa-building-columns', 'Giao diá»‡n má»Ÿ bÃ¡n cÄƒn há»™ biá»‡t thá»± háº¡ng sang, villa nghá»‰ dÆ°á»¡ng, quáº£n lÃ½ tour du lá»‹ch VIP', 'Ultra Luxury', 7);

-- 3. Insert Master Themes
INSERT INTO `themes` (`id`, `category_id`, `name`, `slug`, `code_name`, `tagline`, `description`, `thumbnail`, `preview_url`, `folder_path`, `version`, `author`, `status`, `is_featured`, `rating`, `downloads_count`, `views_count`, `primary_color`, `secondary_color`, `accent_color`, `bg_color`, `font_family`, `layout_type`) VALUES
(1, 1, 'HieuMini Luxury Fashion Studio', 'hieumini-fashion-studio', 'HIEU_WEB_01', 'Ä á»‰nh Cao ThÆ°Æ¡ng Máº¡i Ä iá»‡n Tá»­ May Máº·c & Lookbook 3D', 'Há»‡ thá»‘ng website thá» i trang may máº·c tháº¿ há»‡ má»›i vá»›i Hero Slider trÃ¬nh chiáº¿u bá»™ sÆ°u táº­p, Flash Sale Ä‘á»“ng há»“ Ä‘áº¿m ngÆ°á»£c, bá»™ lá» c size/giÃ¡ Ä‘a chiá» u, Size Guide quy Ä‘á»•i kÃ­ch cá»¡ vÃ  giá»  hÃ ng thÃ´ng minh tÃ­ch há»£p VietQR MBBank.', 'assets/images/themes/fashion-preview.png', 'projects/HieuWeb01/index.php', 'HieuWeb01', '2.4.0', 'HIEU CEO Studio', 'active', 1, 4.98, 1420, 8940, '#6366f1', '#ec4899', '#06b6d4', '#0f172a', 'Outfit', 'executive_glass'),
(2, 3, 'Hieu Modern Book & Publishing Hub', 'hieu-modern-bookstore', 'HIEU_WEB_02', 'KhÃ´ng Gian Ä á» c Ä áº³ng Cáº¥p & ThÆ° Viá»‡n Tri Thá»©c Sá»‘', 'Giao diá»‡n hiá»‡u sÃ¡ch sang trá» ng vá»›i danh má»¥c Ä‘a dáº¡ng tá»« vÄƒn há» c, kinh táº¿, cÃ´ng nghá»‡ Ä‘áº¿n ká»¹ nÄƒng sá»‘ng; há»— trá»£ Ä‘á» c thá»­ máº«u, Ä‘Ã¡nh giÃ¡ Ä‘á»™c giáº£, mÃ£ giáº£m giÃ¡ vÃ  trang cÃ¡ nhÃ¢n theo dÃµi Ä‘Æ¡n.', 'assets/images/themes/book-preview.png', 'projects/HieuWeb02/index.php', 'HieuWeb02', '2.1.0', 'HIEU CEO Studio', 'ready', 1, 4.93, 1150, 6420, '#0284c7', '#f59e0b', '#10b981', '#0b1329', 'Plus Jakarta Sans', 'executive_glass'),
(3, 4, 'Hieu Living & Scandinavian Decor', 'hieu-living-decor', 'HIEU_WEB_03', 'Kiáº¿n TrÃºc Ná»™i Tháº¥t Tinh Táº¿ Cho KhÃ´ng Gian Sá»‘ng Xanh', 'Giao diá»‡n ná»™i tháº¥t theo phong cÃ¡ch tá»‘i giáº£n Báº¯c Ã‚u, tá»‘i Æ°u hÃ¬nh áº£nh sáº£n pháº©m gÃ³c rá»™ng, bá»™ sÆ°u táº­p phÃ²ng khÃ¡ch, phÃ²ng ngá»§, phÃ²ng lÃ m viá»‡c cÃ¹ng tÆ° váº¥n thiáº¿t káº¿ ná»™i tháº¥t cÃ¡ nhÃ¢n hÃ³a.', 'assets/images/themes/furniture-preview.png', 'projects/HieuWeb03/index.php', 'HieuWeb03', '2.2.0', 'HIEU CEO Studio', 'ready', 1, 4.96, 980, 5200, '#10b981', '#64748b', '#f59e0b', '#0f172a', 'Outfit', 'executive_glass'),
(4, 2, 'Hieu CyberTech Innovation Matrix', 'hieu-cybertech-gadgets', 'HIEU_WEB_04', 'Showroom Thiáº¿t Bá»‹ CÃ´ng Nghá»‡ & Há»‡ Sinh ThÃ¡i AI', 'Website bÃ¡n láº» Ä‘iá»‡n thoáº¡i flagship, laptop gaming, linh kiá»‡n PC vÃ  phá»¥ kiá»‡n thÃ´ng minh; tÃ­ch há»£p so sÃ¡nh thÃ´ng sá»‘ ká»¹ thuáº­t, báº£o hÃ nh Ä‘iá»‡n tá»­ vÃ  mua tráº£ gÃ³p 0%.', 'assets/images/themes/tech-preview.png', 'projects/HieuWeb04/index.php', 'HieuWeb04', '2.5.0', 'HIEU CEO Studio', 'ready', 1, 4.97, 1890, 11400, '#8b5cf6', '#3b82f6', '#06b6d4', '#090d16', 'Outfit', 'executive_glass'),
(5, 5, 'Hieu Pro Gym & Athletic Matrix', 'hieu-gym-fitness-pro', 'HIEU_WEB_05', 'Ná» n Táº£ng Thá»ƒ HÃ¬nh Ä á»‰nh Cao & Dinh DÆ°á»¡ng Thá»ƒ Thao', 'Website phÃ²ng táº­p Gym & phÃ¢n phá»‘i thá»±c pháº©m bá»• sung Whey Isolate, Creatine; tÃ­ch há»£p cÃ´ng cá»¥ tÃ­nh toÃ¡n chá»‰ sá»‘ BMI khoa há» c, lá»‹ch táº­p cÃ¡ nhÃ¢n vÃ  gÃ³i táº­p PT cao cáº¥p.', 'assets/images/themes/gym-preview.png', 'projects/HieuWeb05/index.php', 'HieuWeb05', '2.6.0', 'HIEU CEO Studio', 'ready', 1, 4.99, 2140, 14250, '#ef4444', '#f97316', '#eab308', '#0b0f19', 'Montserrat', 'executive_glass'),
(6, 6, 'Hieu Obsidian AI & Cloud Enterprise', 'hieu-obsidian-ai-saas', 'HIEU_WEB_06', 'Giáº£i PhÃ¡p Chuyá»ƒn Ä á»•i Sá»‘ & Tá»± Ä á»™ng HÃ³a TrÃ­ Tuá»‡ NhÃ¢n Táº¡o', 'Giao diá»‡n SaaS dÃ nh cho táº­p Ä‘oÃ n quy mÃ´ lá»›n vá»›i Dashboard thá» i gian thá»±c, báº£ng giÃ¡ Ä‘Äƒng kÃ½ Ä‘á»‹nh ká»³, tÃ­ch há»£p API Gateway vÃ  bÃ¡o cÃ¡o dá»¯ liá»‡u Big Data chuáº©n Ä‘iá» u hÃ nh.', 'assets/images/themes/saas-preview.png', 'projects/HieuWebSaaS/index.php', 'HieuWebSaaS', '3.0.0', 'HIEU CEO Studio', 'ready', 1, 5.00, 3100, 18900, '#6366f1', '#a855f7', '#38bdf8', '#070b14', 'Outfit', 'executive_glass'),
(7, 7, 'Hieu Diamond Luxury Real Estate', 'hieu-diamond-estates', 'HIEU_WEB_07', 'SÃ n Báº¥t Ä á»™ng Sáº£n Háº¡ng Sang & Nghá»‰ DÆ°á»¡ng ThÆ°á»£ng LÆ°u', 'Giao diá»‡n trÃ¬nh diá»…n cÃ¡c siÃªu dá»± Ã¡n cÄƒn há»™ Penthouse, biá»‡t thá»± biá»ƒn, resort nghá»‰ dÆ°á»¡ng 6 sao vá»›i cÃ´ng nghá»‡ thá»±c táº¿ áº£o VR 360 vÃ  há»“ sÆ¡ phÃ¡p lÃ½ minh báº¡ch cho giá»›i Ä‘áº§u tÆ°.', 'assets/images/themes/realestate-preview.png', 'projects/HieuWebEstate/index.php', 'HieuWebEstate', '1.8.0', 'HIEU CEO Studio', 'beta', 0, 4.91, 640, 3800, '#d97706', '#b45309', '#fbbf24', '#0f141c', 'Cinzel', 'executive_glass');

-- 4. Insert Theme Sections
INSERT INTO `theme_sections` (`theme_id`, `section_key`, `section_name`, `is_enabled`, `sort_order`, `config_json`) VALUES
(1, 'hero_slider', 'Hero Carousel & Dynamic Banners', 1, 1, '{"animation": "fade-slide", "autoplay": true, "duration": 5000}'),
(1, 'flash_sale', 'Flash Sale Countdown & Live Deals', 1, 2, '{"show_timer": true, "discount_badge": true}'),
(1, 'featured_categories', 'Grid 7 Danh Má»¥c Thá»i Trang', 1, 3, '{"columns": 4, "hover_zoom": true}'),
(1, 'best_sellers', 'Sáº£n Pháº©m BÃ¡n Cháº¡y Nháº¥t', 1, 4, '{"limit": 8, "show_ratings": true}'),
(1, 'brand_lookbook', 'Lookbook & Phong CÃ¡ch Tráº»', 1, 5, '{"masonry": true, "lightbox": true}'),
(1, 'newsletter_cta', 'ÄÄƒng KÃ½ Nháº­n Voucher 10%', 1, 6, '{"button_glow": true}'),
(2, 'hero_search', 'Hero Search & SÃ¡ch Ná»•i Báº­t', 1, 1, '{"show_quote": true}'),
(2, 'book_categories', 'Danh Má»¥c SÃ¡ch Theo Thá»ƒ Loáº¡i', 1, 2, '{"limit": 6}'),
(2, 'new_releases', 'SÃ¡ch Má»›i PhÃ¡t HÃ nh', 1, 3, '{"limit": 8}'),
(3, 'hero_interior', 'Hero PhÃ²ng KhÃ¡ch & Showroom 3D', 1, 1, '{"video_bg": false}'),
(3, 'room_planner', 'Bá»™ SÆ°u Táº­p Theo KhÃ´ng Gian', 1, 2, '{"tabs": ["living", "bedroom", "office"]}'),
(4, 'hero_tech_matrix', 'Hero Flagship & Particle Grid', 1, 1, '{"particles": true}'),
(4, 'tech_specs', 'So SÃ¡nh Cáº¥u HÃ¬nh Nhanh', 1, 2, '{"specs_limit": 4}'),
(5, 'hero_gym_matrix', 'Hero Äá»™ng Lá»±c Táº­p Luyá»‡n & Video', 1, 1, '{"glitch_effect": true}'),
(5, 'bmi_calculator', 'MÃ¡y TÃ­nh Chá»‰ Sá»‘ CÆ¡ Thá»ƒ BMI', 1, 2, '{"auto_diet_plan": true}');

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
(1, 'Buttons', 'CEO Glass Neon Button', 'btn-glass-neon', 'NÃºt báº¥m hiá»‡u á»©ng kÃ­nh má» viá»n neon phÃ¡t sÃ¡ng khi hover vá»›i chuyá»ƒn Ä‘á»™ng mÆ°á»£t mÃ ', '<button class="ceo-btn-neon"><span class="glow"></span><i class="fa-solid fa-bolt mr-2"></i> KhÃ¡m PhÃ¡ Ngay</button>', '.ceo-btn-neon { background: rgba(99,102,241,0.2); backdrop-filter: blur(12px); border: 1px solid rgba(99,102,241,0.5); color: #fff; padding: 12px 28px; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 0 20px rgba(99,102,241,0.25); } .ceo-btn-neon:hover { transform: translateY(-3px) scale(1.03); box-shadow: 0 0 35px rgba(99,102,241,0.6); background: rgba(99,102,241,0.4); }', NULL, 'Luxury Pro', 'button,glass,neon,glow'),
(2, 'Cards', 'Executive 3D Tilt Card', 'card-3d-tilt', 'Tháº» giao diá»‡n tÃ­ch há»£p hiá»‡u á»©ng nghiÃªng 3D theo con trá» chuá»™t vÃ  viá»n gradient', '<div class="ceo-card-3d"><div class="card-inner"><div class="badge-ceo">TOP CHOICE</div><h3>Hieu Obsidian AI</h3><p>Há»‡ thá»‘ng quáº£n trá»‹ vÃ  tá»± Ä‘á»™ng hÃ³a cho táº­p Ä‘oÃ n Ä‘a quá»‘c gia.</p></div></div>', '.ceo-card-3d { background: linear-gradient(135deg, rgba(30,41,59,0.7), rgba(15,23,42,0.8)); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; padding: 24px; backdrop-filter: blur(16px); transition: all 0.4s ease; } .ceo-card-3d:hover { border-color: rgba(99,102,241,0.6); box-shadow: 0 20px 40px rgba(0,0,0,0.4), 0 0 30px rgba(99,102,241,0.2); transform: translateY(-6px); }', NULL, '3D Motion', 'card,glass,3d,tilt'),
(3, 'Stats', 'Pulsing Metric Counter Badge', 'stat-pulse-badge', 'Khá»‘i thá»‘ng kÃª cÃ³ cháº¥m Ä‘Ã¨n LED nháº¥p nhÃ¡y bÃ¡o hiá»‡u dá»¯ liá»‡u thá»i gian thá»±c', '<div class="stat-badge-pulse"><span class="led-pulse"></span><span class="stat-label">Server Uptime:</span><strong class="stat-val">99.98%</strong></div>', '.stat-badge-pulse { display: inline-flex; align-items: center; gap: 8px; background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); padding: 6px 14px; border-radius: 9999px; color: #34d399; font-size: 13px; } .led-pulse { width: 8px; height: 8px; background: #10b981; border-radius: 50%; box-shadow: 0 0 10px #10b981; animation: pulseGlow 1.5s infinite; } @keyframes pulseGlow { 0%, 100% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.4); opacity: 0.5; } }', NULL, 'Live Data', 'stats,pulse,badge,counter'),
(4, 'Navigation', 'Floating Glass Navbar', 'nav-floating-glass', 'Thanh Ä‘iá»u hÆ°á»›ng ná»•i lÆ¡ lá»­ng vá»›i hiá»‡u á»©ng kÃ­nh má» vÃ  menu tÆ°Æ¡ng tÃ¡c thÃ´ng minh', '<nav class="ceo-floating-nav"><div class="logo"><i class="fa-solid fa-crown mr-2"></i> HIEU CEO</div><ul class="nav-links"><li><a href="#" class="active">Trang Chá»§</a></li><li><a href="#">Giao Diá»‡n</a></li><li><a href="#">TÃ¹y Biáº¿n</a></li></ul></nav>', '.ceo-floating-nav { position: fixed; top: 20px; left: 50%; transform: translateX(-50%); width: calc(100% - 40px); max-width: 1200px; background: rgba(15,23,42,0.75); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.12); border-radius: 18px; padding: 14px 28px; display: flex; align-items: center; justify-content: space-between; z-index: 1000; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }', NULL, 'Core UI', 'navbar,floating,glass');

-- 7. Insert CEO Strategic Metrics
INSERT INTO `ceo_metrics` (`id`, `metric_key`, `metric_title`, `current_value`, `target_value`, `change_percent`, `is_positive`, `metric_unit`, `metric_icon`, `chart_data_json`) VALUES
(1, 'active_themes_count', 'Giao Diá»‡n Äang Váº­n HÃ nh', '7', '10', '+40.0%', 1, 'Websites', 'fa-cubes', '{"labels":["T1","T2","T3","T4","T5","T6","T7","T8"],"values":[3,4,4,5,5,6,6,7]}'),
(2, 'total_pageviews', 'Tá»•ng LÆ°á»£t Truy Cáº­p ThÃ¡ng', '3,428,900', '4,000,000', '+32.8%', 1, 'LÆ°á»£t Xem', 'fa-eye', '{"labels":["T1","T2","T3","T4","T5","T6","T7","T8"],"values":[1.2,1.5,1.9,2.2,2.5,2.9,3.1,3.4]}'),
(3, 'conversion_rate', 'Tá»‰ Lá»‡ Chuyá»ƒn Äá»•i KhÃ¡ch HÃ ng', '6.84%', '7.50%', '+18.5%', 1, 'Tá»‰ Lá»‡ %', 'fa-arrow-trend-up', '{"labels":["T1","T2","T3","T4","T5","T6","T7","T8"],"values":[4.2,4.6,5.1,5.5,5.9,6.2,6.5,6.8]}'),
(4, 'avg_page_speed', 'Tá»‘c Äá»™ Táº£i Giao Diá»‡n (LCP)', '0.38s', '0.50s', '-42.0%', 1, 'GiÃ¢y (Ultra Fast)', 'fa-gauge-high', '{"labels":["T1","T2","T3","T4","T5","T6","T7","T8"],"values":[0.85,0.72,0.61,0.54,0.48,0.44,0.40,0.38]}');

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
(1, 1, 'AUTH_LOGIN', 'CEO HIEU TRAN Ä‘Äƒng nháº­p thÃ nh cÃ´ng vÃ o Há»‡ thá»‘ng Äiá»u hÃ nh', '127.0.0.1', NOW() - INTERVAL 2 HOUR),
(2, 1, 'THEME_ACTIVATE', 'KÃ­ch hoáº¡t thÃ nh cÃ´ng giao diá»‡n máº·c Ä‘á»‹nh: HieuMini Luxury Fashion Studio (HIEU_WEB_01)', '127.0.0.1', NOW() - INTERVAL 1 HOUR),
(3, 2, 'THEME_CUSTOMIZE', 'CDO Elena Vance cáº­p nháº­t báº£ng mÃ u neon vÃ  typography font Outfit cho toÃ n bá»™ giao diá»‡n', '127.0.0.1', NOW() - INTERVAL 45 MINUTE),
(4, 3, 'CACHE_FLUSH', 'Lead Theme Architect Alex Thorne xÃ³a cache toÃ n há»‡ thá»‘ng vÃ  tá»‘i Æ°u CSS rendering', '127.0.0.1', NOW() - INTERVAL 15 MINUTE);

-- 10. Insert System Settings
INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_group`, `description`) VALUES
('site_name', 'HIEU CEO - Master Website Interface & Theme Hub', 'general', 'TÃªn thÆ°Æ¡ng hiá»‡u há»‡ thá»‘ng quáº£n trá»‹ giao diá»‡n'),
('site_tagline', 'Há»‡ Thá»‘ng Quáº£n LÃ½ Giao Diá»‡n Website & TrÃ¬nh TÃ¹y Biáº¿n Trá»±c Quan Chuáº©n CEO', 'general', 'Kháº©u hiá»‡u thÆ°Æ¡ng hiá»‡u'),
('site_logo', 'assets/images/hieu-ceo-logo.png', 'general', 'Logo thÆ°Æ¡ng hiá»‡u'),
('active_theme_id', '1', 'theme', 'ID cá»§a giao diá»‡n Ä‘ang Ä‘Æ°á»£c kÃ­ch hoáº¡t chÃ­nh'),
('default_font', 'Outfit', 'theme', 'PhÃ´ng chá»¯ tiÃªu chuáº©n cho toÃ n há»‡ thá»‘ng'),
('dark_mode_default', '1', 'appearance', 'Báº­t cháº¿ Ä‘á»™ Dark Mode Luxury máº·c Ä‘á»‹nh'),
('animation_level', 'smooth_ultra', 'appearance', 'Má»©c Ä‘á»™ hiá»‡u á»©ng chuyá»ƒn Ä‘á»™ng vÃ  micro-interactions'),
('maintenance_mode', '0', 'system', 'Cháº¿ Ä‘á»™ báº£o trÃ¬ há»‡ thá»‘ng (0: Táº¯t, 1: Báº­t)'),
('cache_status', 'enabled', 'system', 'Tráº¡ng thÃ¡i bá»™ nhá»› Ä‘á»‡m'),
('api_rate_limit', '120', 'security', 'Giá»›i háº¡n sá»‘ lÆ°á»£t gá»i API má»—i phÃºt'),
('csrf_protection', '1', 'security', 'Báº£o vá»‡ biá»ƒu máº«u vá»›i Token CSRF');

