# Hướng Dẫn Deploy Website với XAMPP - mon88.click

## Tổng Quan
Hướng dẫn deploy website Laravel 12 lên Windows VPS sử dụng XAMPP (Apache) - đơn giản và dễ dàng hơn IIS.

## Yêu Cầu Hệ Thống

### Phần Mềm Cần Cài Đặt:
1. **XAMPP** (bao gồm Apache, PHP, MySQL)
2. **Composer** (PHP package manager)
3. **Node.js 18+** và **npm**

## Bước 1: Cài Đặt XAMPP

### 1.1. Tải và Cài Đặt XAMPP
1. Tải XAMPP từ: https://www.apachefriends.org/download.html
2. Chọn phiên bản có **PHP 8.2+** (XAMPP 8.2.x hoặc 8.3.x)
3. Chạy installer và cài đặt vào `C:\xampp` (mặc định)
4. Trong quá trình cài đặt, chọn:
   - ✅ Apache
   - ✅ MySQL (nếu cần)
   - ✅ PHP
   - ✅ phpMyAdmin (tùy chọn)

### 1.2. Khởi Động Apache và MySQL
1. Mở **XAMPP Control Panel**
2. Click **Start** cho **Apache**
3. Click **Start** cho **MySQL**
4. Đảm bảo cả Apache và MySQL chạy thành công (màu xanh)

**Lưu ý**: Cả Apache và MySQL đều cần chạy để website hoạt động.

### 1.3. Kiểm Tra PHP
1. Mở browser: `http://localhost`
2. Click **phpinfo()** để kiểm tra phiên bản PHP
3. Đảm bảo PHP 8.2+ đã được cài đặt

## Bước 2: Cài Đặt Composer

### 2.1. Tải và Cài Đặt Composer
1. Tải Composer-Setup.exe từ: https://getcomposer.org/download/
2. Chạy installer
3. Composer sẽ tự động tìm PHP trong XAMPP: `C:\xampp\php\php.exe`
4. Hoàn tất cài đặt

### 2.2. Kiểm Tra Composer
```powershell
composer --version
```

## Bước 3: Cài Đặt Node.js

### 3.1. Tải và Cài Đặt Node.js
1. Tải Node.js từ: https://nodejs.org/
2. Cài đặt phiên bản LTS (18+)
3. Kiểm tra:
```powershell
node --version
npm --version
```

## Bước 4: Upload Code lên VPS

### 4.1. Tạo Thư Mục Website
```powershell
# Tạo thư mục trong htdocs của XAMPP
New-Item -ItemType Directory -Path "C:\xampp\htdocs\mon88.click" -Force
```

### 4.2. Upload Files
Upload toàn bộ code vào thư mục: `C:\xampp\htdocs\mon88.click\`

**Lưu ý**: Upload toàn bộ project, không chỉ thư mục `public`

## Bước 5: Cấu Hình Apache Virtual Host

### 5.1. Mở File httpd-vhosts.conf
Mở file: `C:\xampp\apache\conf\extra\httpd-vhosts.conf`

### 5.2. Thêm Virtual Host
Thêm đoạn code sau vào cuối file:

```apache
<VirtualHost *:80>
    ServerName mon88.click
    ServerAlias www.mon88.click
    DocumentRoot "C:/xampp/htdocs/mon88.click/public"
    
    <Directory "C:/xampp/htdocs/mon88.click/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "C:/xampp/apache/logs/mon88.click_error.log"
    CustomLog "C:/xampp/apache/logs/mon88.click_access.log" common
</VirtualHost>
```

### 5.3. Bật mod_rewrite
1. Mở file: `C:\xampp\apache\conf\httpd.conf`
2. Tìm dòng: `#LoadModule rewrite_module modules/mod_rewrite.so`
3. Bỏ dấu `#` để bật module:
   ```
   LoadModule rewrite_module modules/mod_rewrite.so
   ```

### 5.4. Bật Virtual Hosts
1. Trong file `httpd.conf`, tìm dòng:
   ```
   #Include conf/extra/httpd-vhosts.conf
   ```
2. Bỏ dấu `#`:
   ```
   Include conf/extra/httpd-vhosts.conf
   ```

### 5.5. Cấu Hình Hosts File (Cho Local Testing)
1. Mở file: `C:\Windows\System32\drivers\etc\hosts` (với quyền Administrator)
2. Thêm dòng:
   ```
   127.0.0.1    mon88.click
   127.0.0.1    www.mon88.click
   ```

### 5.6. Khởi Động Lại Apache
1. Trong XAMPP Control Panel, click **Stop** cho Apache
2. Click **Start** lại Apache

## Bước 6: Cài Đặt Dependencies

### 6.1. Mở PowerShell tại thư mục project
```powershell
cd C:\xampp\htdocs\mon88.click
```

### 6.2. Cài Đặt PHP Dependencies
```powershell
composer install --optimize-autoloader --no-dev
```

### 6.3. Cài Đặt Node.js Dependencies
```powershell
npm install
```

### 6.4. Build Frontend Assets
```powershell
npm run build
```

## Bước 7: Cấu Hình Environment

### 7.1. Tạo File .env
```powershell
# Copy từ .env.example (nếu có) hoặc tạo mới
Copy-Item .env.example .env
# Hoặc tạo file .env mới
```

### 7.2. Tạo MySQL Database
1. Mở **phpMyAdmin**: `http://localhost/phpmyadmin`
2. Click **New** để tạo database mới
3. Đặt tên database: `mon88_click` (hoặc tên khác)
4. Chọn **Collation**: `utf8mb4_unicode_ci`
5. Click **Create**

**Hoặc sử dụng SQL:**
```sql
CREATE DATABASE mon88_click CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 7.3. Cấu Hình .env
Mở file `.env` và cấu hình:

```env
APP_NAME="BongDaNet"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://mon88.click

LOG_CHANNEL=stack
LOG_LEVEL=error

# MySQL Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mon88_click
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file
SESSION_LIFETIME=120

BROADCAST_CONNECTION=log
CACHE_STORE=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync

SOCCER_API_BASE_URL=https://api.soccersapi.com/v2.2
SOCCER_API_USERNAME=Zr1NN
SOCCER_API_TOKEN=DqCDvCP0ye
```

**Lưu ý**: 
- `DB_USERNAME`: Mặc định là `root` cho XAMPP
- `DB_PASSWORD`: Để trống nếu chưa set password cho MySQL trong XAMPP
- Nếu đã set password cho MySQL, điền password vào `DB_PASSWORD`

### 7.4. Generate Application Key
```powershell
php artisan key:generate
```

### 7.5. Chạy Migrations
```powershell
php artisan migrate --force
```

### 7.6. Seed Admin User
```powershell
php artisan db:seed --class=AdminUserSeeder
```

**Thông tin đăng nhập admin**:
- **Email**: `admin@bongdanet.co`
- **Password**: `admin123`
- ⚠️ **Lưu ý**: Đổi mật khẩu ngay sau lần đăng nhập đầu tiên!

**Hoặc seed tất cả**:
```powershell
php artisan db:seed
```

### 7.7. Tạo Storage Link
```powershell
php artisan storage:link
```

### 7.8. Optimize Laravel
```powershell
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Bước 8: Cấu Hình Permissions

### 8.1. Cấp Quyền Cho Thư Mục
```powershell
# Cấp quyền cho thư mục storage và bootstrap/cache
icacls "C:\xampp\htdocs\mon88.click\storage" /grant "Everyone:(OI)(CI)F" /T
icacls "C:\xampp\htdocs\mon88.click\bootstrap\cache" /grant "Everyone:(OI)(CI)F" /T
```

**Lưu ý**: Không cần cấp quyền cho thư mục `database` khi dùng MySQL vì database được lưu trong MySQL server, không phải file.

## Bước 9: Cấu Hình Domain

### 9.1. Cấu Hình DNS
Trong DNS provider của domain `mon88.click`, thêm:
- **A Record**: `@` → IP của VPS
- **A Record**: `www` → IP của VPS

### 9.2. Cấu Hình Firewall
1. Mở **Windows Firewall**
2. Cho phép port **80** (HTTP) và **443** (HTTPS nếu có)

## Bước 10: Kiểm Tra và Test

### 10.1. Test Local
1. Mở browser: `http://mon88.click` (nếu đã cấu hình hosts file)
2. Hoặc: `http://localhost/mon88.click/public`

### 10.2. Test Production
1. Mở browser: `http://mon88.click` (từ internet)
2. Kiểm tra các trang chính
3. Kiểm tra API endpoints

### 10.3. Kiểm Tra Logs
```powershell
# Laravel logs
Get-Content "C:\xampp\htdocs\mon88.click\storage\logs\laravel.log" -Tail 50

# Apache error log
Get-Content "C:\xampp\apache\logs\mon88.click_error.log" -Tail 50
```

## Bước 11: Cấu Hình SSL (HTTPS) - Tùy Chọn

**Xem hướng dẫn chi tiết**: `SSL_SETUP.md`

### Tóm Tắt Nhanh:

#### Option 1: Cloudflare (Khuyến nghị - Dễ nhất) ⭐
1. Đăng ký Cloudflare miễn phí
2. Thêm domain `mon88.click`
3. Cấu hình DNS với proxy bật (🟠 Proxied)
4. Bật SSL/TLS → Full (strict)
5. Bật "Always Use HTTPS"

#### Option 2: Let's Encrypt (Miễn phí - Production)
1. Sử dụng Win-ACME để tạo certificate
2. Cấu hình Apache Virtual Host HTTPS
3. Thiết lập auto-renewal

#### Option 3: Self-Signed (Chỉ cho testing)
1. Tạo self-signed certificate với OpenSSL
2. Cấu hình Apache Virtual Host HTTPS

**Lưu ý**: Sau khi cấu hình SSL, cập nhật `APP_URL=https://mon88.click` trong file `.env`

## Troubleshooting

### Lỗi 403 Forbidden
1. Kiểm tra quyền thư mục
2. Kiểm tra cấu hình Virtual Host
3. Kiểm tra `AllowOverride All` trong Directory

### Lỗi 500 Internal Server Error
1. Kiểm tra file `.env` đã được tạo
2. Kiểm tra `APP_KEY` đã được generate
3. Kiểm tra permissions của `storage` và `bootstrap/cache`
4. Xem Apache error log

### Lỗi mod_rewrite không hoạt động
1. Đảm bảo đã bật `mod_rewrite` trong `httpd.conf`
2. Kiểm tra `AllowOverride All` trong Virtual Host
3. Kiểm tra file `.htaccess` trong thư mục `public`

### Website không load từ domain
1. Kiểm tra DNS đã trỏ đúng IP
2. Kiểm tra Firewall đã mở port 80
3. Kiểm tra Virtual Host configuration
4. Kiểm tra Apache đang chạy

### Assets không load
1. Chạy lại: `npm run build`
2. Kiểm tra file `public/build/manifest.json` tồn tại
3. Kiểm tra quyền thư mục `public/build`

## Script Deploy Tự Động

Tạo file `deploy-xampp.ps1` (xem file riêng) để tự động hóa quá trình deploy.

## Lưu Ý Quan Trọng

1. **Backup**: Luôn backup database và code trước khi deploy
2. **Environment**: Đảm bảo `APP_ENV=production` và `APP_DEBUG=false`
3. **Security**: 
   - Không commit file `.env`
   - Sử dụng HTTPS cho production
   - Cập nhật XAMPP và PHP thường xuyên
4. **Performance**: 
   - Sử dụng `php artisan config:cache` trong production
   - Kiểm tra Apache performance settings

## So Sánh XAMPP vs IIS

| Tính năng | XAMPP | IIS |
|-----------|-------|-----|
| Độ khó cài đặt | ⭐ Dễ | ⭐⭐⭐ Khó |
| Cấu hình | ⭐⭐ Đơn giản | ⭐⭐⭐ Phức tạp |
| Performance | ⭐⭐ Tốt | ⭐⭐⭐ Rất tốt |
| Phù hợp cho | Development, Small sites | Enterprise, Large sites |
| Chi phí | Miễn phí | Miễn phí (Windows Server) |

## Hỗ Trợ

Nếu gặp vấn đề, kiểm tra:
- Laravel logs: `storage/logs/laravel.log`
- Apache error log: `C:\xampp\apache\logs\error.log`
- Apache access log: `C:\xampp\apache\logs\access.log`

