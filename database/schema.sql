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
