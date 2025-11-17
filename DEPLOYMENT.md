# Hướng Dẫn Deploy Website lên Windows VPS

## Tổng Quan
Website Laravel 12 cần được deploy lên Windows VPS với domain `mon88.click`.

## Yêu Cầu Hệ Thống

### Phần Mềm Cần Cài Đặt:
1. **PHP 8.2+** (hoặc PHP 8.3)
2. **Composer** (PHP package manager)
3. **Node.js 18+** và **npm**
4. **IIS (Internet Information Services)** hoặc **Nginx**
5. **URL Rewrite Module** cho IIS (nếu dùng IIS)
6. **PHP Manager for IIS** (nếu dùng IIS)

## Bước 1: Chuẩn Bị VPS Windows

### 1.1. Cài Đặt IIS
1. Mở **Server Manager**
2. Chọn **Add Roles and Features**
3. Chọn **Web Server (IIS)**
4. Cài đặt các tính năng:
   - IIS Management Console
   - URL Rewrite Module (tải từ Microsoft)
   - CGI

### 1.2. Cài Đặt PHP 8.2+
1. Tải PHP từ: https://windows.php.net/download/
2. Chọn **Thread Safe** version, **x64**
3. Giải nén vào `C:\php`
4. Copy `php.ini-production` thành `php.ini`
5. Mở `php.ini` và bật các extension:
   ```
   extension=curl
   extension=fileinfo
   extension=gd
   extension=mbstring
   extension=openssl
   extension=pdo_sqlite
   extension=sqlite3
   extension=zip
   ```
6. Thêm PHP vào PATH environment variable

### 1.3. Cài Đặt Composer
1. Tải Composer-Setup.exe từ: https://getcomposer.org/download/
2. Chạy installer và chọn PHP path: `C:\php\php.exe`

### 1.4. Cài Đặt Node.js
1. Tải Node.js từ: https://nodejs.org/
2. Cài đặt phiên bản LTS (18+)
3. Kiểm tra: `node --version` và `npm --version`

### 1.5. Cài Đặt PHP Manager for IIS
1. Tải từ: https://phpmanager.codeplex.com/
2. Cài đặt và cấu hình PHP trong IIS

## Bước 2: Upload Code lên VPS

### 2.1. Tạo Thư Mục Website
```powershell
# Tạo thư mục cho website
New-Item -ItemType Directory -Path "C:\inetpub\wwwroot\mon88.click"
```

### 2.2. Upload Files
Có thể sử dụng:
- **FTP/SFTP** (FileZilla, WinSCP)
- **Git** (nếu có repository)
- **Remote Desktop** và copy trực tiếp

Đảm bảo upload toàn bộ thư mục project vào `C:\inetpub\wwwroot\mon88.click\`

## Bước 3: Cấu Hình Website

### 3.1. Cấu Hình IIS Site
1. Mở **IIS Manager**
2. Right-click **Sites** → **Add Website**
3. Cấu hình:
   - **Site name**: `mon88.click`
   - **Application pool**: Tạo mới `mon88.click`
   - **Physical path**: `C:\inetpub\wwwroot\mon88.click\public`
   - **Binding**: 
     - Type: `http` hoặc `https`
     - IP address: `All Unassigned`
     - Port: `80` (hoặc `443` cho HTTPS)
     - Host name: `mon88.click`

### 3.2. Cấu Hình Application Pool
1. Chọn Application Pool `mon88.click`
2. Right-click → **Advanced Settings**
3. Đặt:
   - **.NET CLR Version**: `No Managed Code`
   - **Managed Pipeline Mode**: `Integrated`
   - **Identity**: `ApplicationPoolIdentity` hoặc tài khoản có quyền

### 3.3. Cấu Hình PHP trong IIS
1. Mở **PHP Manager** trong IIS
2. Chọn website `mon88.click`
3. Click **Register new PHP version**
4. Chọn: `C:\php\php-cgi.exe`
5. Click **Check phpinfo()` để kiểm tra

### 3.4. Cấu Hình Permissions
```powershell
# Cấp quyền cho thư mục storage và bootstrap/cache
icacls "C:\inetpub\wwwroot\mon88.click\storage" /grant "IIS_IUSRS:(OI)(CI)F" /T
icacls "C:\inetpub\wwwroot\mon88.click\bootstrap\cache" /grant "IIS_IUSRS:(OI)(CI)F" /T
```

## Bước 4: Cài Đặt Dependencies

### 4.1. Mở PowerShell tại thư mục project
```powershell
cd C:\inetpub\wwwroot\mon88.click
```

### 4.2. Cài Đặt PHP Dependencies
```powershell
composer install --optimize-autoloader --no-dev
```

### 4.3. Cài Đặt Node.js Dependencies
```powershell
npm install
```

### 4.4. Build Frontend Assets
```powershell
npm run build
```

## Bước 5: Cấu Hình Environment

### 5.1. Tạo File .env
```powershell
# Copy từ .env.example (nếu có) hoặc tạo mới
Copy-Item .env.example .env
# Hoặc tạo file .env mới
```

### 5.2. Cấu Hình .env
Mở file `.env` và cấu hình:

```env
APP_NAME="BongDaNet"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://mon88.click

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=sqlite
DB_DATABASE=C:\inetpub\wwwroot\mon88.click\database\database.sqlite

BROADCAST_CONNECTION=log
CACHE_STORE=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

SOCCER_API_BASE_URL=https://api.soccersapi.com/v2.2
SOCCER_API_USERNAME=Zr1NN
SOCCER_API_TOKEN=DqCDvCP0ye
```

### 5.3. Generate Application Key
```powershell
php artisan key:generate
```

### 5.4. Tạo Database SQLite
```powershell
# Tạo file database nếu chưa có
New-Item -ItemType File -Path "database\database.sqlite" -Force
```

### 5.5. Chạy Migrations
```powershell
php artisan migrate --force
```

### 5.6. Tạo Storage Link
```powershell
php artisan storage:link
```

### 5.7. Optimize Laravel
```powershell
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Bước 6: Cấu Hình Domain

### 6.1. Cấu Hình DNS
Trong DNS provider của domain `mon88.click`, thêm:
- **A Record**: `@` → IP của VPS
- **A Record**: `www` → IP của VPS

### 6.2. Cấu Hình SSL (HTTPS) - Tùy chọn nhưng khuyến nghị
1. Cài đặt **Let's Encrypt** hoặc SSL certificate
2. Hoặc sử dụng **Cloudflare** (miễn phí SSL)
3. Cấu hình binding HTTPS trong IIS

## Bước 7: Kiểm Tra và Test

### 7.1. Kiểm Tra Permissions
Đảm bảo các thư mục có quyền:
- `storage/` - Read/Write
- `bootstrap/cache/` - Read/Write
- `database/` - Read/Write

### 7.2. Test Website
1. Mở browser và truy cập: `http://mon88.click`
2. Kiểm tra các trang chính
3. Kiểm tra API endpoints

### 7.3. Kiểm Tra Logs
```powershell
# Xem Laravel logs
Get-Content "C:\inetpub\wwwroot\mon88.click\storage\logs\laravel.log" -Tail 50
```

## Bước 8: Cấu Hình Bảo Mật

### 8.1. Ẩn .env File
Đảm bảo `.env` không được truy cập từ web

### 8.2. Cấu Hình Firewall
Mở port 80 và 443 trong Windows Firewall

### 8.3. Cập Nhật PHP
Thường xuyên cập nhật PHP để bảo mật

## Troubleshooting

### Lỗi 500 Internal Server Error
1. Kiểm tra file `storage/logs/laravel.log`
2. Kiểm tra permissions của thư mục `storage` và `bootstrap/cache`
3. Kiểm tra PHP error log

### Lỗi Permission Denied
```powershell
# Cấp quyền đầy đủ
icacls "C:\inetpub\wwwroot\mon88.click" /grant "IIS_IUSRS:(OI)(CI)F" /T
```

### Lỗi Database
- Kiểm tra file `database/database.sqlite` tồn tại
- Kiểm tra quyền ghi cho thư mục `database/`

### Assets không load
- Chạy lại `npm run build`
- Kiểm tra file `public/build/manifest.json` tồn tại

## Script Deploy Tự Động

Tạo file `deploy.ps1` để tự động hóa:

```powershell
# deploy.ps1
cd C:\inetpub\wwwroot\mon88.click

# Pull latest code (nếu dùng Git)
# git pull origin main

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Laravel commands
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force

Write-Host "Deployment completed!"
```

## Lưu Ý Quan Trọng

1. **Backup**: Luôn backup database và code trước khi deploy
2. **Environment**: Đảm bảo `APP_ENV=production` và `APP_DEBUG=false`
3. **SSL**: Nên sử dụng HTTPS cho production
4. **Monitoring**: Cài đặt monitoring để theo dõi website
5. **Updates**: Thường xuyên cập nhật dependencies và security patches

## Hỗ Trợ

Nếu gặp vấn đề, kiểm tra:
- Laravel logs: `storage/logs/laravel.log`
- IIS logs: `C:\inetpub\logs\LogFiles\`
- PHP error log: Kiểm tra trong `php.ini`

