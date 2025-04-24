# Hướng dẫn Deploy Barber Shop lên Hosting VPSTTT

## 1. Chuẩn bị trước khi deploy

### 1.1. Biên dịch assets
```bash
# Cài đặt các gói npm
npm install

# Biên dịch assets cho môi trường production
npm run build
```

### 1.2. Tối ưu hóa autoload
```bash
composer install --optimize-autoloader --no-dev
```

## 2. Chuẩn bị file để upload

### 2.1. Các thư mục và file cần upload
- `app/`
- `bootstrap/`
- `config/`
- `database/`
- `public/`
- `resources/`
- `routes/`
- `storage/`
- `vendor/` (đã được tối ưu hóa)
- `.htaccess` (file mới tạo ở thư mục gốc)
- `artisan`
- `composer.json`
- `composer.lock`
- `package.json`
- `vite.config.js`

### 2.2. Các file không cần upload
- `.git/`
- `node_modules/`
- `.env` (sẽ sử dụng file .env.production)
- `.env.example`
- `.env.production` (sẽ được đổi tên thành .env trên hosting)

## 3. Upload lên hosting

### 3.1. Tạo database
- Tạo database mới trên hosting (ví dụ: `mtu271_barber`)
- Tạo user database và cấp quyền đầy đủ cho database vừa tạo

### 3.2. Upload files
- Upload tất cả các file và thư mục đã chuẩn bị lên thư mục gốc của hosting
- Đổi tên file `.env.production` thành `.env` trên hosting
- Cập nhật thông tin database trong file `.env` với thông tin database thực tế

### 3.3. Thiết lập quyền
```bash
# Thiết lập quyền cho thư mục storage và bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

## 4. Cấu hình sau khi upload

### 4.1. Kết nối SSH vào hosting (nếu có)
```bash
ssh username@mtu271.vpsttt.vn
```

### 4.2. Di chuyển vào thư mục dự án
```bash
cd public_html
```

### 4.3. Tạo symbolic link cho storage
```bash
php artisan storage:link
```

### 4.4. Chạy migration và seeder
```bash
# Nếu muốn tạo database từ migration
php artisan migrate --seed

# Hoặc import file SQL đã có sẵn thông qua phpMyAdmin
```

### 4.5. Tạo cache cho ứng dụng
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 4.6. Xóa cache cũ (nếu cần)
```bash
php artisan cache:clear
```

## 5. Kiểm tra và xử lý lỗi

### 5.1. Kiểm tra lỗi
- Kiểm tra file log tại `storage/logs/laravel.log`
- Tạm thời bật `APP_DEBUG=true` trong file `.env` để xem chi tiết lỗi

### 5.2. Xử lý lỗi phổ biến

#### 5.2.1. Lỗi quyền truy cập
```bash
chmod -R 775 storage bootstrap/cache
chown -R your_user:www-data storage bootstrap/cache
```

#### 5.2.2. Lỗi không tìm thấy thư mục storage/app/public
```bash
mkdir -p storage/app/public
php artisan storage:link
```

#### 5.2.3. Lỗi không kết nối được database
- Kiểm tra lại thông tin kết nối database trong file `.env`
- Đảm bảo user database có quyền truy cập từ host của ứng dụng

#### 5.2.4. Lỗi không gửi được email
- Kiểm tra cấu hình SMTP trong file `.env`
- Đảm bảo mật khẩu ứng dụng Gmail đã được cấu hình đúng

## 6. Bảo mật sau khi deploy

### 6.1. Đảm bảo APP_DEBUG=false
- Kiểm tra file `.env` và đảm bảo `APP_DEBUG=false` để tăng bảo mật

### 6.2. Bảo vệ file .env
```bash
# Thêm vào file .htaccess ở thư mục gốc
<Files .env>
    Order allow,deny
    Deny from all
</Files>
```

### 6.3. Bảo vệ thư mục storage
- Đảm bảo các thư mục nhạy cảm không thể truy cập trực tiếp từ web

## 7. Kiểm tra hoạt động

### 7.1. Kiểm tra trang chủ
- Truy cập trang chủ và đảm bảo tất cả các tính năng hoạt động bình thường

### 7.2. Kiểm tra trang quản trị
- Đăng nhập vào trang quản trị và kiểm tra các chức năng

### 7.3. Kiểm tra chức năng đặt lịch
- Thử đặt lịch hẹn và kiểm tra email thông báo

### 7.4. Kiểm tra chức năng upload hình ảnh
- Thử upload hình ảnh và kiểm tra xem hình ảnh có được lưu trữ đúng cách không

## 8. Cập nhật sau khi deploy

### 8.1. Cập nhật ứng dụng
```bash
# Kéo code mới từ git (nếu có)
git pull

# Cài đặt các gói composer mới
composer install --optimize-autoloader --no-dev

# Chạy migration (nếu có thay đổi database)
php artisan migrate

# Cập nhật cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 8.2. Backup dữ liệu định kỳ
- Backup database định kỳ
- Backup thư mục storage/app định kỳ
