-- Barber Shop Database SQL Script
-- Tạo bởi Augment Agent
-- Phiên bản: 2.0 (Cập nhật ngày 15/04/2025)

-- Hướng dẫn sử dụng:
-- 1. Tạo database mới: CREATE DATABASE barber_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- 2. Sử dụng database: USE barber_shop;
-- 3. Chạy file SQL này để tạo cấu trúc cơ sở dữ liệu

-- Lưu ý: File này sẽ xóa tất cả các bảng hiện có trong database và tạo lại từ đầu
-- Nếu bạn muốn giữ lại dữ liệu hiện tại, hãy sao lưu trước khi chạy file này

-- Cập nhật trong phiên bản 2.0:
-- 1. Thêm trường appointment_id vào bảng reviews để liên kết với lịch hẹn
-- 2. Cập nhật cấu trúc bảng invoices để phù hợp với nghiệp vụ mới
-- 3. Thêm bảng invoice_service để lưu trữ thông tin chi tiết về dịch vụ trong hóa đơn
-- 4. Cập nhật các phương thức thanh toán trong bảng appointments và invoices
-- 5. Loại bỏ dữ liệu mẫu, chỉ giữ lại cấu trúc cơ sở dữ liệu
-- 6. Loại bỏ bảng personal_access_tokens vì dự án không sử dụng API
-- 7. Thêm comment giải thích ý nghĩa của từng bảng
-- 8. Thêm bảng payment_receipts để lưu trữ biên lai thanh toán

-- Đặt charset và collation
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Xóa các bảng nếu đã tồn tại (theo thứ tự ngược lại để tránh lỗi khóa ngoại)
DROP TABLE IF EXISTS `time_slots`;
DROP TABLE IF EXISTS `email_verifications`;
DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `invoice_service`;
DROP TABLE IF EXISTS `appointment_services`;
DROP TABLE IF EXISTS `invoices`;
DROP TABLE IF EXISTS `appointments`;
DROP TABLE IF EXISTS `barber_schedules`;
DROP TABLE IF EXISTS `barbers`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `services`;
DROP TABLE IF EXISTS `news`;
DROP TABLE IF EXISTS `contacts`;
-- Bỏ bảng settings vì không còn sử dụng
-- DROP TABLE IF EXISTS `settings`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `password_reset_tokens`;
-- Bảng personal_access_tokens đã được loại bỏ vì dự án không sử dụng API
-- DROP TABLE IF EXISTS `personal_access_tokens`;

-- Tạo bảng users
-- Bảng này lưu trữ thông tin người dùng trong hệ thống, bao gồm admin, thợ cắt tóc và khách hàng
-- Mỗi người dùng có thông tin cơ bản như tên, email, mật khẩu, vai trò, số điện thoại, địa chỉ, và trạng thái
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
-- Bảng này lưu trữ các token đặt lại mật khẩu
-- Sử dụng cho chức năng đặt lại mật khẩu khi người dùng quên mật khẩu
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng personal_access_tokens đã được loại bỏ vì không cần thiết cho dự án
-- Dự án không sử dụng API nên không cần lưu trữ các token truy cập

-- Tạo bảng categories
-- Bảng này lưu trữ thông tin danh mục
-- Sử dụng cho phân loại dịch vụ, sản phẩm và tin tức
-- Mỗi danh mục có tên, slug, mô tả, loại (service, product, news) và trạng thái
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
-- Bảng này lưu trữ thông tin dịch vụ cắt tóc
-- Mỗi dịch vụ thuộc về một danh mục
-- Lưu trữ thông tin như tên, slug, mô tả, giá, thời gian thực hiện, hình ảnh và trạng thái
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
-- Bảng này lưu trữ thông tin sản phẩm chăm sóc tóc
-- Mỗi sản phẩm thuộc về một danh mục
-- Lưu trữ thông tin như tên, slug, mô tả, giá, số lượng tồn kho, hình ảnh và trạng thái
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
-- Bảng này lưu trữ thông tin chi tiết về thợ cắt tóc
-- Mỗi thợ cắt tóc liên kết với một người dùng trong bảng users (vai trò 'barber')
-- Lưu trữ thông tin như mô tả, kinh nghiệm, chuyên môn và trạng thái
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
-- Bảng này lưu trữ lịch làm việc của thợ cắt tóc
-- Mỗi bản ghi đại diện cho một ngày trong tuần với thời gian bắt đầu và kết thúc
-- Cũng lưu trữ thông tin về ngày nghỉ và số lượng lịch hẹn tối đa có thể nhận trong ngày
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
-- Bảng này lưu trữ thông tin lịch hẹn cắt tóc
-- Mỗi lịch hẹn liên kết với một khách hàng và một thợ cắt tóc
-- Lưu trữ thông tin như ngày hẹn, giờ bắt đầu, giờ kết thúc, trạng thái, mã đặt lịch
-- Cũng lưu trữ thông tin thanh toán và ghi chú
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
-- Bảng trung gian liên kết giữa lịch hẹn và dịch vụ
-- Mỗi lịch hẹn có thể bao gồm nhiều dịch vụ
-- Lưu trữ giá dịch vụ tại thời điểm đặt lịch
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
-- Bảng này lưu trữ thông tin hóa đơn
-- Mỗi hóa đơn có thể liên kết với một lịch hẹn, một khách hàng và một thợ cắt tóc
-- Lưu trữ thông tin như mã hóa đơn, tạm tính, giảm giá, thuế, tổng tiền
-- Cũng lưu trữ phương thức thanh toán, trạng thái thanh toán, trạng thái hóa đơn và ghi chú
CREATE TABLE `invoices` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_code` varchar(20) NOT NULL,
  `appointment_id` bigint(20) UNSIGNED NULL,
  `user_id` bigint(20) UNSIGNED NULL,
  `barber_id` bigint(20) UNSIGNED NULL,
  `invoice_number` varchar(20) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','bank_transfer','card') NOT NULL DEFAULT 'cash',
  `payment_status` enum('pending','paid') NOT NULL DEFAULT 'pending',
  `status` enum('pending','confirmed','completed','canceled') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoices_invoice_code_unique` (`invoice_code`),
  UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
  KEY `invoices_appointment_id_foreign` (`appointment_id`),
  KEY `invoices_user_id_foreign` (`user_id`),
  KEY `invoices_barber_id_foreign` (`barber_id`),
  CONSTRAINT `invoices_appointment_id_foreign` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoices_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoices_barber_id_foreign` FOREIGN KEY (`barber_id`) REFERENCES `barbers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng invoice_service
-- Bảng trung gian liên kết giữa hóa đơn và dịch vụ
-- Mỗi hóa đơn có thể bao gồm nhiều dịch vụ
-- Lưu trữ thông tin như số lượng, giá, giảm giá và thành tiền của từng dịch vụ trong hóa đơn
CREATE TABLE `invoice_service` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_service_invoice_id_foreign` (`invoice_id`),
  KEY `invoice_service_service_id_foreign` (`service_id`),
  CONSTRAINT `invoice_service_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoice_service_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng news
-- Bảng này lưu trữ thông tin tin tức và bài viết
-- Mỗi bài viết thuộc về một danh mục và được tạo bởi một người dùng
-- Lưu trữ thông tin như tiêu đề, slug, nội dung, hình ảnh, trạng thái
-- Cũng lưu trữ thông tin về việc có được đề xuất hay không và số lượt xem
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
-- Bảng này lưu trữ thông tin liên hệ từ khách hàng
-- Lưu trữ thông tin như tên, email, số điện thoại, tiêu đề, nội dung
-- Cũng lưu trữ trạng thái đã đọc và phản hồi từ admin
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

-- Bảng settings đã được loại bỏ vì không còn sử dụng
-- Các giá trị cài đặt đã được thay thế bằng các giá trị mặc định trong code

-- Tạo bảng reviews
-- Bảng này lưu trữ đánh giá của khách hàng
-- Mỗi đánh giá liên kết với một khách hàng, một thợ cắt tóc, một dịch vụ và một lịch hẹn
-- Lưu trữ thông tin như điểm đánh giá, bình luận, hình ảnh, trạng thái
-- Cũng lưu trữ phản hồi từ admin
CREATE TABLE `reviews` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `barber_id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `appointment_id` bigint(20) UNSIGNED NULL,
  `rating` int(11) NOT NULL,
  `comment` text DEFAULT NULL,
  `images` json DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `admin_response` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reviews_user_id_foreign` (`user_id`),
  KEY `reviews_barber_id_foreign` (`barber_id`),
  KEY `reviews_service_id_foreign` (`service_id`),
  KEY `reviews_appointment_id_foreign` (`appointment_id`),
  CONSTRAINT `reviews_barber_id_foreign` FOREIGN KEY (`barber_id`) REFERENCES `barbers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_appointment_id_foreign` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng email_verifications
-- Bảng này lưu trữ thông tin xác thực email
-- Sử dụng cho quá trình đăng ký tài khoản với xác thực OTP
-- Lưu trữ thông tin như email, tên, mật khẩu, mã OTP và thời gian hết hạn
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
-- Bảng này lưu trữ thông tin các khung giờ có thể đặt lịch
-- Mỗi khung giờ liên kết với một thợ cắt tóc và một ngày cụ thể
-- Lưu trữ số lượng lịch hẹn đã đặt và số lượng lịch hẹn tối đa có thể nhận
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

-- Tạo bảng payment_receipts
-- Bảng này lưu trữ thông tin biên lai thanh toán
-- Mỗi biên lai liên kết với một lịch hẹn
-- Lưu trữ thông tin như đường dẫn tệp, ghi chú, trạng thái và ghi chú của admin
CREATE TABLE `payment_receipts` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `appointment_id` bigint(20) UNSIGNED NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_receipts_appointment_id_foreign` (`appointment_id`),
  CONSTRAINT `payment_receipts_appointment_id_foreign` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;