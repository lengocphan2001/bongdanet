# Cấu Hình Environment Variables

## File .env cho Production

Tạo file `.env` trong thư mục gốc của project với nội dung sau:

```env
APP_NAME="BongDaNet"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_TIMEZONE=Asia/Ho_Chi_Minh
APP_URL=https://mon88.click
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
APP_MAINTENANCE_STORE=database

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# Database Configuration
# MySQL Configuration (XAMPP)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mon88_click
DB_USERNAME=root
DB_PASSWORD=

# Note: 
# - DB_USERNAME: root (mặc định XAMPP)
# - DB_PASSWORD: để trống nếu chưa set password cho MySQL
# - Nếu dùng SQLite, thay đổi như sau:
#   DB_CONNECTION=sqlite
#   DB_DATABASE=C:\xampp\htdocs\mon88.click\database\database.sqlite

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
CACHE_STORE=file
CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@mon88.click"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"

# Soccer API Configuration
SOCCER_API_BASE_URL=https://api.soccersapi.com/v2.2
SOCCER_API_USERNAME=Zr1NN
SOCCER_API_TOKEN=DqCDvCP0ye
```

## Các Bước Cấu Hình

1. **Tạo file .env**: Copy nội dung trên vào file `.env` trong thư mục gốc project

2. **Generate APP_KEY**: Chạy lệnh sau để tạo application key:
   ```powershell
   php artisan key:generate
   ```

3. **Cập nhật APP_URL**: Đảm bảo `APP_URL` trỏ đúng domain của bạn:
   ```env
   APP_URL=https://mon88.click
   ```

4. **Cấu hình Database**: 
   - **Tạo MySQL database**: Mở phpMyAdmin (`http://localhost/phpmyadmin`) và tạo database `mon88_click`
   - **Cập nhật thông tin kết nối**: Điền `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` trong file `.env`
   - Mặc định XAMPP: `DB_USERNAME=root`, `DB_PASSWORD=` (để trống)

5. **Cấu hình Soccer API**: Kiểm tra và cập nhật thông tin API nếu cần

## Lưu Ý Bảo Mật

- **KHÔNG** commit file `.env` lên Git
- Đảm bảo file `.env` có quyền truy cập hạn chế
- Thay đổi `APP_KEY` sau khi deploy
- Đặt `APP_DEBUG=false` trong production
- Sử dụng HTTPS (`APP_URL=https://...`)

