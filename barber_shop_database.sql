-- Barber Shop Database SQL Script
-- Tạo bởi Augment Agent

-- Hướng dẫn sử dụng:
-- 1. Tạo database mới: CREATE DATABASE barber_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- 2. Sử dụng database: USE barber_shop;
-- 3. Chạy file SQL này để tạo cấu trúc và dữ liệu mẫu

-- Lưu ý: File này sẽ xóa tất cả các bảng hiện có trong database và tạo lại từ đầu
-- Nếu bạn muốn giữ lại dữ liệu hiện tại, hãy sao lưu trước khi chạy file này

-- Đặt charset và collation
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Xóa các bảng nếu đã tồn tại (theo thứ tự ngược lại để tránh lỗi khóa ngoại)
DROP TABLE IF EXISTS `time_slots`;
DROP TABLE IF EXISTS `email_verifications`;
DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `appointment_services`;
DROP TABLE IF EXISTS `invoices`;
DROP TABLE IF EXISTS `appointments`;
DROP TABLE IF EXISTS `barber_schedules`;
DROP TABLE IF EXISTS `barbers`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `services`;
DROP TABLE IF EXISTS `news`;
DROP TABLE IF EXISTS `contacts`;
DROP TABLE IF EXISTS `settings`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `password_reset_tokens`;
DROP TABLE IF EXISTS `personal_access_tokens`;

-- Tạo bảng users
CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','barber','customer') NOT NULL DEFAULT 'customer',
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng password_reset_tokens
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng personal_access_tokens
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng categories
CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('service','product','news') NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng services
CREATE TABLE `services` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration` int(11) NOT NULL COMMENT 'Duration in minutes',
  `image` varchar(255) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `services_slug_unique` (`slug`),
  KEY `services_category_id_foreign` (`category_id`),
  CONSTRAINT `services_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng products
CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  KEY `products_category_id_foreign` (`category_id`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng barbers
CREATE TABLE `barbers` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `description` text DEFAULT NULL,
  `experience` int(11) NOT NULL DEFAULT 0 COMMENT 'Experience in years',
  `specialty` varchar(255) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `barbers_user_id_foreign` (`user_id`),
  CONSTRAINT `barbers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng barber_schedules
CREATE TABLE `barber_schedules` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `barber_id` bigint(20) UNSIGNED NOT NULL,
  `day_of_week` tinyint(4) NOT NULL COMMENT '0: Sunday, 1-6: Monday to Saturday',
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `is_day_off` tinyint(4) NOT NULL DEFAULT 0,
  `max_appointments` int(11) NOT NULL DEFAULT 3,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `barber_schedules_barber_id_foreign` (`barber_id`),
  CONSTRAINT `barber_schedules_barber_id_foreign` FOREIGN KEY (`barber_id`) REFERENCES `barbers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng appointments
CREATE TABLE `appointments` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `barber_id` bigint(20) UNSIGNED NOT NULL,
  `appointment_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `time_slot` varchar(255) DEFAULT NULL,
  `status` enum('pending','confirmed','canceled','completed') NOT NULL DEFAULT 'pending',
  `booking_code` varchar(20) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `payment_method` enum('cash','bank_transfer') NOT NULL DEFAULT 'cash',
  `payment_status` enum('pending','paid') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `appointments_booking_code_unique` (`booking_code`),
  KEY `appointments_user_id_foreign` (`user_id`),
  KEY `appointments_barber_id_foreign` (`barber_id`),
  CONSTRAINT `appointments_barber_id_foreign` FOREIGN KEY (`barber_id`) REFERENCES `barbers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `appointments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng appointment_services
CREATE TABLE `appointment_services` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `appointment_id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `appointment_services_appointment_id_foreign` (`appointment_id`),
  KEY `appointment_services_service_id_foreign` (`service_id`),
  CONSTRAINT `appointment_services_appointment_id_foreign` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `appointment_services_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng invoices
CREATE TABLE `invoices` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `appointment_id` bigint(20) UNSIGNED NOT NULL,
  `invoice_number` varchar(20) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','bank_transfer') NOT NULL DEFAULT 'cash',
  `payment_status` enum('pending','paid') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
  KEY `invoices_appointment_id_foreign` (`appointment_id`),
  CONSTRAINT `invoices_appointment_id_foreign` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng news
CREATE TABLE `news` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1: published, 0: draft',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `view_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `news_slug_unique` (`slug`),
  KEY `news_category_id_foreign` (`category_id`),
  KEY `news_user_id_foreign` (`user_id`),
  CONSTRAINT `news_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `news_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng contacts
CREATE TABLE `contacts` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `reply` text DEFAULT NULL,
  `replied_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: unread, 1: read',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng settings
CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `group` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng reviews
CREATE TABLE `reviews` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `barber_id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` text DEFAULT NULL,
  `images` json DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reviews_user_id_foreign` (`user_id`),
  KEY `reviews_barber_id_foreign` (`barber_id`),
  KEY `reviews_service_id_foreign` (`service_id`),
  CONSTRAINT `reviews_barber_id_foreign` FOREIGN KEY (`barber_id`) REFERENCES `barbers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng email_verifications
CREATE TABLE `email_verifications` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_verifications_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng time_slots
CREATE TABLE `time_slots` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `barber_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `time_slot` varchar(255) NOT NULL,
  `booked_count` int(11) NOT NULL DEFAULT 0,
  `max_bookings` int(11) NOT NULL DEFAULT 2,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `time_slots_barber_id_date_time_slot_unique` (`barber_id`,`date`,`time_slot`),
  CONSTRAINT `time_slots_barber_id_foreign` FOREIGN KEY (`barber_id`) REFERENCES `barbers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- Thêm dữ liệu mẫu

-- Thêm dữ liệu vào bảng users
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `phone`, `address`, `avatar`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@example.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '0123456789', 'Hà Nội, Việt Nam', NULL, 1, NULL, NOW(), NOW()),
(2, 'Dũng', 'dung@example.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'barber', '0987654321', 'Hà Nội, Việt Nam', 'barbers/dung.jpg', 1, NULL, NOW(), NOW()),
(3, 'Hùng', 'hung@example.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'barber', '0912345678', 'Hà Nội, Việt Nam', 'barbers/hung.jpg', 1, NULL, NOW(), NOW()),
(4, 'Huyền Trần', 'huyen@example.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', '0923456789', 'Hà Nội, Việt Nam', NULL, 1, NULL, NOW(), NOW()),
(5, 'Minh Nguyễn', 'minh@example.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', '0934567890', 'Hà Nội, Việt Nam', NULL, 1, NULL, NOW(), NOW());

-- Thêm dữ liệu vào bảng categories
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `type`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Dịch vụ cắt tóc', 'dich-vu-cat-toc', 'Các dịch vụ cắt tóc cơ bản', 'service', 1, NOW(), NOW()),
(2, 'Dịch vụ uốn tóc', 'dich-vu-uon-toc', 'Các dịch vụ uốn tóc', 'service', 1, NOW(), NOW()),
(3, 'Dịch vụ nhuộm tóc', 'dich-vu-nhuom-toc', 'Các dịch vụ nhuộm tóc', 'service', 1, NOW(), NOW()),
(4, 'Sản phẩm chăm sóc tóc', 'san-pham-cham-soc-toc', 'Các sản phẩm chăm sóc tóc', 'product', 1, NOW(), NOW()),
(5, 'Sản phẩm tạo kiểu tóc', 'san-pham-tao-kieu-toc', 'Các sản phẩm tạo kiểu tóc', 'product', 1, NOW(), NOW()),
(6, 'Tin tức', 'tin-tuc', 'Tin tức về barber shop', 'news', 1, NOW(), NOW()),
(7, 'Mẹo chăm sóc tóc', 'meo-cham-soc-toc', 'Mẹo chăm sóc tóc', 'news', 1, NOW(), NOW());

-- Thêm dữ liệu vào bảng services
INSERT INTO `services` (`id`, `category_id`, `name`, `slug`, `description`, `price`, `duration`, `image`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Cắt tóc nam cơ bản', 'cat-toc-nam-co-ban', 'Dịch vụ cắt tóc nam cơ bản bao gồm cắt, gội, sấy và vẽ kiểu', 100000.00, 30, 'services/cat-toc-nam-co-ban.jpg', 1, NOW(), NOW()),
(2, 1, 'Cắt tóc + xả râu', 'cat-toc-xa-rau', 'Dịch vụ cắt tóc kèm xả râu', 150000.00, 45, 'services/cat-toc-xa-rau.jpg', 1, NOW(), NOW()),
(3, 2, 'Uốn tóc nam', 'uon-toc-nam', 'Dịch vụ uốn tóc nam cơ bản', 200000.00, 60, 'services/uon-toc-nam.jpg', 1, NOW(), NOW()),
(4, 3, 'Nhuộm tóc nam', 'nhuom-toc-nam', 'Dịch vụ nhuộm tóc nam cơ bản', 250000.00, 90, 'services/nhuom-toc-nam.jpg', 1, NOW(), NOW()),
(5, 1, 'Cạo râu', 'cao-rau', 'Dịch vụ cạo râu chuyên nghiệp', 50000.00, 15, 'services/cao-rau.jpg', 1, NOW(), NOW());

-- Thêm dữ liệu vào bảng products
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `price`, `stock`, `image`, `status`, `created_at`, `updated_at`) VALUES
(1, 4, 'Dầu gội nam Dove', 'dau-goi-nam-dove', 'Dầu gội dành cho nam giới', 120000.00, 50, 'products/dau-goi-nam-dove.jpg', 1, NOW(), NOW()),
(2, 5, 'Sáp vuốt tóc Glanzen', 'sap-vuot-toc-glanzen', 'Sáp vuốt tóc giữ nếp lâu', 180000.00, 30, 'products/sap-vuot-toc-glanzen.jpg', 1, NOW(), NOW()),
(3, 5, 'Gồm xịt tóc Glanzen', 'gom-xit-toc-glanzen', 'Gồm xịt tóc giữ nếp lâu', 150000.00, 25, 'products/gom-xit-toc-glanzen.jpg', 1, NOW(), NOW());

-- Thêm dữ liệu vào bảng barbers
INSERT INTO `barbers` (`id`, `user_id`, `description`, `experience`, `specialty`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 'Thợ cắt tóc chuyên nghiệp với nhiều năm kinh nghiệm', 5, 'Cắt tóc, tạo kiểu tóc nam', 1, NOW(), NOW()),
(2, 3, 'Thợ cắt tóc chuyên nghiệp với nhiều năm kinh nghiệm', 3, 'Nhuộm tóc, uốn tóc nam', 1, NOW(), NOW());

-- Thêm dữ liệu vào bảng barber_schedules
INSERT INTO `barber_schedules` (`id`, `barber_id`, `day_of_week`, `start_time`, `end_time`, `is_day_off`, `max_appointments`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '08:00:00', '18:00:00', 0, 3, NOW(), NOW()),
(2, 1, 2, '08:00:00', '18:00:00', 0, 3, NOW(), NOW()),
(3, 1, 3, '08:00:00', '18:00:00', 0, 3, NOW(), NOW()),
(4, 1, 4, '08:00:00', '18:00:00', 0, 3, NOW(), NOW()),
(5, 1, 5, '08:00:00', '18:00:00', 0, 3, NOW(), NOW()),
(6, 1, 6, '08:00:00', '18:00:00', 0, 3, NOW(), NOW()),
(7, 1, 0, '00:00:00', '00:00:00', 1, 0, NOW(), NOW()),
(8, 2, 1, '08:00:00', '18:00:00', 0, 3, NOW(), NOW()),
(9, 2, 2, '08:00:00', '18:00:00', 0, 3, NOW(), NOW()),
(10, 2, 3, '08:00:00', '18:00:00', 0, 3, NOW(), NOW()),
(11, 2, 4, '08:00:00', '18:00:00', 0, 3, NOW(), NOW()),
(12, 2, 5, '08:00:00', '18:00:00', 0, 3, NOW(), NOW()),
(13, 2, 6, '00:00:00', '00:00:00', 1, 0, NOW(), NOW()),
(14, 2, 0, '00:00:00', '00:00:00', 1, 0, NOW(), NOW());

-- Thêm dữ liệu vào bảng news
INSERT INTO `news` (`id`, `category_id`, `user_id`, `title`, `slug`, `content`, `image`, `status`, `is_featured`, `view_count`, `created_at`, `updated_at`) VALUES
(1, 6, 1, 'Khai trương Barber Shop chi nhánh mới', 'khai-truong-barber-shop-chi-nhanh-moi', '<p>Chúng tôi vui mừng thông báo khai trương chi nhánh mới tại trung tâm thành phố.</p><p>Nhiều ưu đãi hấp dẫn dành cho khách hàng trong tuần khai trương.</p>', 'news/khai-truong.jpg', 1, 1, 120, NOW(), NOW()),
(2, 7, 1, 'Cách chăm sóc tóc nam hiệu quả', 'cach-cham-soc-toc-nam-hieu-qua', '<p>Bài viết chia sẻ các mẹo chăm sóc tóc nam hiệu quả tại nhà.</p><p>Các sản phẩm chăm sóc tóc phù hợp với từng loại tóc.</p>', 'news/cham-soc-toc.jpg', 1, 1, 85, NOW(), NOW());

-- Thêm dữ liệu vào bảng settings
INSERT INTO `settings` (`id`, `key`, `value`, `group`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'Barber Shop', 'general', NOW(), NOW()),
(2, 'site_description', 'Tiệm cắt tóc nam chuyên nghiệp', 'general', NOW(), NOW()),
(3, 'shop_address', '123 Đường ABC, Quận XYZ, Hà Nội', 'contact', NOW(), NOW()),
(4, 'shop_phone', '0123456789', 'contact', NOW(), NOW()),
(5, 'shop_email', 'info@barbershop.com', 'contact', NOW(), NOW()),
(6, 'facebook', 'https://facebook.com/barbershop', 'social', NOW(), NOW()),
(7, 'instagram', 'https://instagram.com/barbershop', 'social', NOW(), NOW()),
(8, 'working_hours', 'Thứ 2 - Thứ 7: 8:00 - 18:00', 'general', NOW(), NOW());

-- Thêm dữ liệu vào bảng time_slots
INSERT INTO `time_slots` (`id`, `barber_id`, `date`, `time_slot`, `booked_count`, `max_bookings`, `created_at`, `updated_at`) VALUES
(1, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '08:00 - 08:30', 0, 2, NOW(), NOW()),
(2, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '08:30 - 09:00', 1, 2, NOW(), NOW()),
(3, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '09:00 - 09:30', 0, 2, NOW(), NOW()),
(4, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '09:30 - 10:00', 0, 2, NOW(), NOW()),
(5, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '10:00 - 10:30', 2, 2, NOW(), NOW()),
(6, 2, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '08:00 - 08:30', 0, 2, NOW(), NOW()),
(7, 2, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '08:30 - 09:00', 0, 2, NOW(), NOW()),
(8, 2, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '09:00 - 09:30', 1, 2, NOW(), NOW());

-- Thêm dữ liệu vào bảng appointments
INSERT INTO `appointments` (`id`, `user_id`, `barber_id`, `appointment_date`, `start_time`, `end_time`, `time_slot`, `status`, `booking_code`, `customer_name`, `email`, `phone`, `payment_method`, `payment_status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 4, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '08:30:00', '09:00:00', '08:30 - 09:00', 'confirmed', 'BK-ABCDEF12', 'Huyền Trần', 'huyen@example.com', '0923456789', 'cash', 'pending', 'Không có ghi chú', NOW(), NOW()),
(2, 5, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '10:00:00', '10:30:00', '10:00 - 10:30', 'confirmed', 'BK-ABCDEF13', 'Minh Nguyễn', 'minh@example.com', '0934567890', 'cash', 'pending', 'Không có ghi chú', NOW(), NOW()),
(3, NULL, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '10:00:00', '10:30:00', '10:00 - 10:30', 'pending', 'BK-ABCDEF14', 'Khách hàng mới', 'khachhang@example.com', '0912345678', 'bank_transfer', 'pending', 'Không có ghi chú', NOW(), NOW()),
(4, 4, 2, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '09:00:00', '09:30:00', '09:00 - 09:30', 'pending', 'BK-ABCDEF15', 'Huyền Trần', 'huyen@example.com', '0923456789', 'cash', 'pending', 'Không có ghi chú', NOW(), NOW());

-- Thêm dữ liệu vào bảng appointment_services
INSERT INTO `appointment_services` (`id`, `appointment_id`, `service_id`, `price`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 100000.00, NOW(), NOW()),
(2, 1, 5, 50000.00, NOW(), NOW()),
(3, 2, 3, 200000.00, NOW(), NOW()),
(4, 3, 4, 250000.00, NOW(), NOW()),
(5, 4, 2, 150000.00, NOW(), NOW());

-- Thêm dữ liệu vào bảng invoices
INSERT INTO `invoices` (`id`, `appointment_id`, `invoice_number`, `amount`, `payment_method`, `payment_status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 'INV-20250001', 150000.00, 'cash', 'pending', NULL, NOW(), NOW()),
(2, 2, 'INV-20250002', 200000.00, 'cash', 'pending', NULL, NOW(), NOW());

-- Thêm dữ liệu vào bảng reviews
INSERT INTO `reviews` (`id`, `user_id`, `barber_id`, `service_id`, `rating`, `comment`, `images`, `status`, `created_at`, `updated_at`) VALUES
(1, 4, 1, 1, 5, 'Dịch vụ rất tốt, thợ cắt tóc chuyên nghiệp', NULL, 1, NOW(), NOW()),
(2, 5, 2, 3, 4, 'Dịch vụ tốt, giá cả hợp lý', NULL, 1, NOW(), NOW());

-- Thêm dữ liệu vào bảng email_verifications
INSERT INTO `email_verifications` (`id`, `email`, `name`, `password`, `otp`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'test@example.com', 'Người dùng mới', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123456', DATE_ADD(NOW(), INTERVAL 10 MINUTE), NOW(), NOW());