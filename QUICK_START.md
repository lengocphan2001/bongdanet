# Hướng Dẫn Deploy Nhanh - mon88.click

## Chọn Phương Thức Deploy

### Option 1: XAMPP (Đơn giản, Khuyến nghị) ⭐
- Dễ cài đặt và cấu hình
- Phù hợp cho website nhỏ và vừa
- Xem hướng dẫn: `DEPLOYMENT_XAMPP.md`

### Option 2: IIS (Chuyên nghiệp)
- Phù hợp cho production lớn
- Cần cấu hình phức tạp hơn
- Xem hướng dẫn: `DEPLOYMENT.md`

---

## Deploy với XAMPP (Đơn giản nhất)

### Checklist Trước Khi Deploy

- [ ] XAMPP đã được cài đặt (PHP 8.2+)
- [ ] Apache và MySQL đang chạy trong XAMPP Control Panel
- [ ] Database `mon88_click` đã được tạo trong phpMyAdmin
- [ ] Composer đã được cài đặt
- [ ] Node.js 18+ đã được cài đặt
- [ ] Domain `mon88.click` đã trỏ về IP VPS

## Các Bước Deploy Nhanh với XAMPP

### 1. Upload Code lên VPS
```powershell
# Tạo thư mục
New-Item -ItemType Directory -Path "C:\xampp\htdocs\mon88.click" -Force

# Upload code vào thư mục trên (sử dụng FTP, Git, hoặc copy trực tiếp)
```

### 2. Cấu Hình Apache Virtual Host
1. Mở file: `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
2. Thêm vào cuối file:
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
</VirtualHost>
```
3. Mở file: `C:\xampp\apache\conf\httpd.conf`
4. Tìm và bỏ dấu `#` ở dòng: `#LoadModule rewrite_module modules/mod_rewrite.so`
5. Tìm và bỏ dấu `#` ở dòng: `#Include conf/extra/httpd-vhosts.conf`
6. Khởi động lại Apache trong XAMPP Control Panel

### 3. Tạo MySQL Database
1. Mở phpMyAdmin: `http://localhost/phpmyadmin`
2. Click **New** → Tạo database tên: `mon88_click`
3. Chọn Collation: `utf8mb4_unicode_ci` → Click **Create**

### 4. Chạy Script Deploy
```powershell
cd C:\xampp\htdocs\mon88.click
.\deploy-xampp.ps1
```

**Lưu ý**: Trước khi chạy script, đảm bảo đã tạo database và cấu hình thông tin MySQL trong file `.env`

### 5. Cấu Hình Domain
- Trong DNS provider, thêm A record: `@` → IP VPS
- Thêm A record: `www` → IP VPS
- Mở Windows Firewall cho port 80

### 6. Test Website
Truy cập: `http://mon88.click`

## Các Lệnh Thường Dùng

### Cập nhật Code
```powershell
cd C:\xampp\htdocs\mon88.click
git pull  # Nếu dùng Git
.\deploy-xampp.ps1
```

### Xem Logs
```powershell
# Laravel logs
Get-Content "C:\xampp\htdocs\mon88.click\storage\logs\laravel.log" -Tail 50

# Apache error logs
Get-Content "C:\xampp\apache\logs\error.log" -Tail 50
```

### Clear Cache
```powershell
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Chạy Migrations
```powershell
php artisan migrate --force
```

### Tạo Storage Link
```powershell
php artisan storage:link
```

## Troubleshooting Nhanh

### Website không load
1. Kiểm tra Apache đang chạy trong XAMPP Control Panel
2. Kiểm tra Virtual Host đã được cấu hình đúng
3. Kiểm tra file `.htaccess` trong thư mục `public`
4. Kiểm tra mod_rewrite đã được bật

### Lỗi 500
1. Kiểm tra permissions: `storage` và `bootstrap/cache`
2. Kiểm tra file `.env` đã được tạo
3. Kiểm tra `APP_KEY` đã được generate
4. Xem Apache error log: `C:\xampp\apache\logs\error.log`

### Lỗi 403 Forbidden
1. Kiểm tra `AllowOverride All` trong Virtual Host
2. Kiểm tra quyền thư mục
3. Kiểm tra `Require all granted` trong Directory

### Assets không load
```powershell
npm run build
```

### Database lỗi
1. Kiểm tra MySQL đang chạy trong XAMPP Control Panel
2. Kiểm tra database đã được tạo trong phpMyAdmin
3. Kiểm tra thông tin kết nối trong file `.env`
4. Test kết nối:
```powershell
php artisan migrate --force
```

## Liên Kết Hữu Ích

- **Hướng dẫn XAMPP chi tiết**: Xem file `DEPLOYMENT_XAMPP.md`
- **Hướng dẫn IIS**: Xem file `DEPLOYMENT.md`
- **Cấu hình SSL/HTTPS**: Xem file `SSL_SETUP.md` ⭐
- **Cấu hình environment**: Xem file `ENV_SETUP.md`
- **Script deploy XAMPP**: Xem file `deploy-xampp.ps1`
- **Script deploy IIS**: Xem file `deploy.ps1`

