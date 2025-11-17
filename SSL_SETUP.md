# Hướng Dẫn Cấu Hình SSL Certificate - HTTPS

## Tổng Quan
Hướng dẫn cấu hình SSL certificate để website `mon88.click` hiển thị kết nối an toàn (HTTPS).

## Phương Pháp 1: Cloudflare (Khuyến Nghị - Dễ Nhất) ⭐

### Ưu Điểm
- ✅ Miễn phí SSL certificate
- ✅ Dễ cấu hình, không cần cài đặt trên server
- ✅ CDN và bảo mật bổ sung
- ✅ Tự động gia hạn

### Các Bước

#### 1. Đăng Ký Cloudflare
1. Truy cập: https://www.cloudflare.com/
2. Đăng ký tài khoản miễn phí
3. Click **Add a Site**
4. Nhập domain: `mon88.click`
5. Chọn plan **Free**

#### 2. Cấu Hình DNS
1. Cloudflare sẽ quét DNS records hiện tại
2. Đảm bảo có A record trỏ về IP VPS:
   - **Type**: A
   - **Name**: `@` (hoặc `mon88.click`)
   - **Content**: IP của VPS
   - **Proxy status**: 🟠 Proxied (bật proxy để dùng SSL)
3. Thêm A record cho `www`:
   - **Type**: A
   - **Name**: `www`
   - **Content**: IP của VPS
   - **Proxy status**: 🟠 Proxied

#### 3. Cập Nhật Nameservers
1. Cloudflare sẽ cung cấp 2 nameservers
2. Vào DNS provider của domain (nơi mua domain)
3. Thay đổi nameservers thành nameservers của Cloudflare
4. Đợi 24-48 giờ để DNS propagate

#### 4. Cấu Hình SSL/TLS
1. Trong Cloudflare Dashboard, chọn domain `mon88.click`
2. Vào **SSL/TLS** → **Overview**
3. Chọn **Full (strict)** mode:
   - **Full**: Cloudflare → Server (HTTPS)
   - **Strict**: Xác thực certificate
4. Vào **SSL/TLS** → **Edge Certificates**
5. Bật **Always Use HTTPS** (tự động redirect HTTP → HTTPS)
6. Bật **Automatic HTTPS Rewrites**

#### 5. Cấu Hình Origin Certificate (Tùy Chọn - Khuyến Nghị)
Để bảo mật tốt hơn, tạo Origin Certificate:

1. Vào **SSL/TLS** → **Origin Server**
2. Click **Create Certificate**
3. Chọn:
   - **Private key type**: RSA (2048)
   - **Hostnames**: `mon88.click`, `*.mon88.click`
   - **Validity**: 15 years
4. Click **Create**
5. **Lưu lại 2 đoạn code**:
   - **Origin Certificate** (certificate)
   - **Private Key** (private key)

#### 6. Cài Đặt Origin Certificate trên Apache (XAMPP)

1. Tạo thư mục cho certificates:
```powershell
New-Item -ItemType Directory -Path "C:\xampp\apache\conf\ssl" -Force
```

2. Tạo file `mon88.click.crt`:
   - Mở Notepad với quyền Administrator
   - Paste **Origin Certificate** vào
   - Lưu tại: `C:\xampp\apache\conf\ssl\mon88.click.crt`

3. Tạo file `mon88.click.key`:
   - Mở Notepad với quyền Administrator
   - Paste **Private Key** vào
   - Lưu tại: `C:\xampp\apache\conf\ssl\mon88.click.key`

4. Cấu hình Apache Virtual Host cho HTTPS:
   - Mở file: `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
   - Thêm Virtual Host HTTPS:

```apache
<VirtualHost *:443>
    ServerName mon88.click
    ServerAlias www.mon88.click
    DocumentRoot "C:/xampp/htdocs/mon88.click/public"
    
    <Directory "C:/xampp/htdocs/mon88.click/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    # SSL Configuration
    SSLEngine on
    SSLCertificateFile "C:/xampp/apache/conf/ssl/mon88.click.crt"
    SSLCertificateKeyFile "C:/xampp/apache/conf/ssl/mon88.click.key"
    
    ErrorLog "C:/xampp/apache/logs/mon88.click_ssl_error.log"
    CustomLog "C:/xampp/apache/logs/mon88.click_ssl_access.log" common
</VirtualHost>
```

5. Bật SSL Module trong Apache:
   - Mở file: `C:\xampp\apache\conf\httpd.conf`
   - Tìm và bỏ dấu `#` ở:
   ```
   LoadModule ssl_module modules/mod_ssl.so
   Include conf/extra/httpd-ssl.conf
   ```

6. Mở port 443 trong Windows Firewall:
```powershell
New-NetFirewallRule -DisplayName "HTTPS" -Direction Inbound -LocalPort 443 -Protocol TCP -Action Allow
```

7. Khởi động lại Apache trong XAMPP Control Panel

#### 7. Cập Nhật .env
```env
APP_URL=https://mon88.click
```

#### 8. Test
- Truy cập: `https://mon88.click`
- Kiểm tra có biểu tượng 🔒 (kết nối an toàn)

---

## Phương Pháp 2: Let's Encrypt (Miễn Phí - Production)

### Yêu Cầu
- Domain đã trỏ về IP VPS
- Có quyền truy cập VPS
- Port 80 và 443 mở

### Các Bước

#### 1. Cài Đặt Certbot cho Windows
1. Tải Win-ACME: https://www.win-acme.com/
2. Hoặc sử dụng Certbot qua WSL (Windows Subsystem for Linux)

#### 2. Sử dụng Win-ACME (Khuyến Nghị cho Windows)
1. Tải Win-ACME và giải nén
2. Chạy `wacs.exe`
3. Chọn option để tạo certificate mới
4. Nhập domain: `mon88.click`
5. Chọn web server: Apache
6. Win-ACME sẽ tự động:
   - Tạo certificate
   - Cấu hình Apache
   - Thiết lập auto-renewal

#### 3. Cấu Hình Apache (Nếu tự cấu hình)
Sau khi có certificate từ Let's Encrypt:

1. Certificate files thường ở:
   - Certificate: `C:\Certbot\live\mon88.click\fullchain.pem`
   - Private Key: `C:\Certbot\live\mon88.click\privkey.pem`

2. Cập nhật Virtual Host:
```apache
<VirtualHost *:443>
    ServerName mon88.click
    ServerAlias www.mon88.click
    DocumentRoot "C:/xampp/htdocs/mon88.click/public"
    
    <Directory "C:/xampp/htdocs/mon88.click/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    SSLEngine on
    SSLCertificateFile "C:/Certbot/live/mon88.click/fullchain.pem"
    SSLCertificateKeyFile "C:/Certbot/live/mon88.click/privkey.pem"
</VirtualHost>
```

---

## Phương Pháp 3: Self-Signed Certificate (Chỉ Cho Testing)

### Lưu Ý
⚠️ Self-signed certificate sẽ hiển thị cảnh báo "Not Secure" trong browser. Chỉ dùng cho testing local.

### Các Bước

#### 1. Tạo Certificate với OpenSSL
1. Cài đặt OpenSSL (có trong XAMPP hoặc tải riêng)
2. Mở PowerShell với quyền Administrator:

```powershell
cd C:\xampp\apache\conf\ssl

# Tạo private key
openssl genrsa -out mon88.click.key 2048

# Tạo certificate signing request
openssl req -new -key mon88.click.key -out mon88.click.csr

# Tạo self-signed certificate (valid 365 days)
openssl x509 -req -days 365 -in mon88.click.csr -signkey mon88.click.key -out mon88.click.crt
```

3. Khi được hỏi thông tin, điền:
   - Country: VN
   - State: (tên tỉnh/thành)
   - City: (tên thành phố)
   - Organization: (tên công ty)
   - Common Name: **mon88.click** (quan trọng!)

#### 2. Cấu Hình Apache
Thêm Virtual Host HTTPS như Phương Pháp 1, bước 6.

---

## Phương Pháp 4: Mua SSL Certificate

### Nhà Cung Cấp
- **Namecheap**: https://www.namecheap.com/security/ssl-certificates/
- **GoDaddy**: https://www.godaddy.com/web-security/ssl-certificate
- **DigiCert**: https://www.digicert.com/
- **Comodo/Sectigo**: https://sectigo.com/

### Quy Trình
1. Mua certificate
2. Tạo CSR (Certificate Signing Request)
3. Xác thực domain
4. Nhận certificate files
5. Cài đặt trên Apache

---

## Cấu Hình Redirect HTTP → HTTPS

### Trong Apache Virtual Host
Thêm Virtual Host HTTP để redirect:

```apache
<VirtualHost *:80>
    ServerName mon88.click
    ServerAlias www.mon88.click
    
    # Redirect to HTTPS
    Redirect permanent / https://mon88.click/
</VirtualHost>
```

### Hoặc trong .htaccess
Thêm vào file `public/.htaccess`:

```apache
# Force HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

## Cập Nhật Laravel Configuration

### 1. Cập Nhật .env
```env
APP_URL=https://mon88.click
```

### 2. Cấu Hình Trusted Proxies (Nếu dùng Cloudflare)
Mở file: `app/Http/Middleware/TrustProxies.php`

```php
protected $proxies = '*'; // Hoặc IP ranges của Cloudflare

protected $headers = Request::HEADER_X_FORWARDED_FOR |
                     Request::HEADER_X_FORWARDED_HOST |
                     Request::HEADER_X_FORWARDED_PORT |
                     Request::HEADER_X_FORWARDED_PROTO;
```

### 3. Force HTTPS trong Laravel
Trong file `app/Providers/AppServiceProvider.php`:

```php
use Illuminate\Support\Facades\URL;

public function boot()
{
    if (config('app.env') === 'production') {
        URL::forceScheme('https');
    }
}
```

---

## Kiểm Tra SSL

### 1. Test Online
- **SSL Labs**: https://www.ssllabs.com/ssltest/
- **SSL Checker**: https://www.sslshopper.com/ssl-checker.html

### 2. Test Local
```powershell
# Kiểm tra certificate
openssl s_client -connect mon88.click:443 -servername mon88.click
```

### 3. Kiểm Tra Browser
- Mở `https://mon88.click`
- Click vào biểu tượng 🔒
- Xem thông tin certificate

---

## Troubleshooting

### Lỗi 526: Invalid SSL Certificate (Cloudflare)

**Xem hướng dẫn chi tiết**: `SSL_TROUBLESHOOTING.md`

**Giải pháp nhanh**:
1. Nếu chưa có SSL trên origin server: Đổi Cloudflare SSL mode → **Flexible**
2. Nếu đã có SSL: Kiểm tra certificate files và cấu hình Apache
3. Kiểm tra port 443 đã mở và Apache đang lắng nghe

### Lỗi "Your connection is not private"
1. Kiểm tra certificate đã được cài đặt đúng
2. Kiểm tra domain trong certificate khớp với domain thực tế
3. Kiểm tra certificate chưa hết hạn
4. Xóa cache browser

### Lỗi "ERR_SSL_PROTOCOL_ERROR"
1. Kiểm tra Apache đã bật mod_ssl
2. Kiểm tra port 443 đã mở trong firewall
3. Kiểm tra Virtual Host HTTPS đã được cấu hình
4. Kiểm tra certificate files tồn tại và có quyền đọc

### Certificate không tự động gia hạn
1. Với Let's Encrypt: Kiểm tra scheduled task hoặc cron job
2. Với Cloudflare: Tự động gia hạn, không cần làm gì
3. Với Self-signed: Cần tạo lại sau khi hết hạn

### Mixed Content Warnings
1. Đảm bảo tất cả resources (CSS, JS, images) load qua HTTPS
2. Kiểm tra `APP_URL` trong `.env` là HTTPS
3. Sử dụng relative URLs thay vì absolute URLs

---

## Bảo Mật Bổ Sung

### 1. HTTP Strict Transport Security (HSTS)
Thêm vào Virtual Host HTTPS:

```apache
Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
```

### 2. Security Headers
Thêm vào Virtual Host hoặc .htaccess:

```apache
Header always set X-Frame-Options "SAMEORIGIN"
Header always set X-Content-Type-Options "nosniff"
Header always set X-XSS-Protection "1; mode=block"
Header always set Referrer-Policy "strict-origin-when-cross-origin"
```

### 3. Disable Weak Ciphers
Trong `httpd-ssl.conf` hoặc Virtual Host:

```apache
SSLProtocol all -SSLv2 -SSLv3
SSLCipherSuite HIGH:!aNULL:!MD5
```

---

## Tóm Tắt - Phương Pháp Khuyến Nghị

### Cho Production:
1. **Cloudflare** (Dễ nhất, miễn phí, có CDN)
2. **Let's Encrypt** (Miễn phí, phù hợp production)

### Cho Testing:
- **Self-signed certificate** (Nhanh, không cần xác thực)

### Cho Enterprise:
- **Mua SSL Certificate** (Hỗ trợ tốt, warranty)

---

## Checklist Sau Khi Cấu Hình SSL

- [ ] Certificate đã được cài đặt
- [ ] Apache đã cấu hình Virtual Host HTTPS
- [ ] Port 443 đã mở trong firewall
- [ ] HTTP redirect về HTTPS
- [ ] `.env` đã cập nhật `APP_URL=https://...`
- [ ] Test truy cập `https://mon88.click` thành công
- [ ] Kiểm tra SSL rating trên SSL Labs
- [ ] Không có mixed content warnings
- [ ] Certificate tự động gia hạn (nếu dùng Let's Encrypt)

---

## Hỗ Trợ

Nếu gặp vấn đề:
1. Kiểm tra Apache error log: `C:\xampp\apache\logs\error.log`
2. Kiểm tra SSL error log: `C:\xampp\apache\logs\mon88.click_ssl_error.log`
3. Test certificate: `openssl x509 -in mon88.click.crt -text -noout`

