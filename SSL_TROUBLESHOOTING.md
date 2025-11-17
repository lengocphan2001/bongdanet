# Khắc Phục Lỗi SSL - Error 526 Invalid SSL Certificate

## Tổng Quan
Lỗi **526 Invalid SSL certificate** xảy ra khi Cloudflare không thể xác thực SSL certificate từ origin server (VPS của bạn).

## Nguyên Nhân

1. **Origin server không có SSL certificate**
2. **Origin server không hỗ trợ HTTPS** (port 443 chưa mở hoặc chưa cấu hình)
3. **SSL certificate không hợp lệ** hoặc không khớp domain
4. **Cloudflare SSL mode không đúng**

## Giải Pháp

### Giải Pháp 1: Sử dụng Cloudflare SSL Mode "Flexible" (Nhanh Nhất) ⭐

**Khi nào dùng**: Khi origin server (VPS) chưa có SSL certificate

#### Các Bước:

1. **Đăng nhập Cloudflare Dashboard**
2. **Chọn domain** `mon88.click`
3. **Vào SSL/TLS** → **Overview**
4. **Đổi SSL/TLS encryption mode** từ `Full (strict)` sang **`Flexible`**

   - **Flexible**: Cloudflare ↔ Visitor (HTTPS), Cloudflare ↔ Origin (HTTP)
   - Origin server không cần SSL certificate

5. **Lưu và đợi 1-2 phút** để cập nhật

#### Lưu Ý:
- ⚠️ **Flexible mode**: Traffic giữa Cloudflare và origin server là HTTP (không mã hóa)
- ✅ **Full mode**: Cần SSL certificate trên origin server
- ✅ **Full (strict)**: Cần SSL certificate hợp lệ và được tin cậy

---

### Giải Pháp 2: Cài SSL Certificate trên Origin Server (Khuyến Nghị)

#### Option A: Sử dụng Cloudflare Origin Certificate (Miễn Phí)

1. **Tạo Origin Certificate trong Cloudflare**:
   - Vào **SSL/TLS** → **Origin Server**
   - Click **Create Certificate**
   - Chọn:
     - **Private key type**: RSA (2048)
     - **Hostnames**: `mon88.click`, `*.mon88.click`
     - **Validity**: 15 years
   - Click **Create**

2. **Lưu lại 2 đoạn code**:
   - **Origin Certificate** (certificate)
   - **Private Key** (private key)

3. **Cài đặt trên Apache (XAMPP)**:

   a. Tạo thư mục cho certificates:
   ```powershell
   New-Item -ItemType Directory -Path "C:\xampp\apache\conf\ssl" -Force
   ```

   b. Tạo file `mon88.click.crt`:
   - Mở Notepad với quyền Administrator
   - Paste **Origin Certificate** vào
   - Lưu tại: `C:\xampp\apache\conf\ssl\mon88.click.crt`
   - Format:
     ```
     -----BEGIN CERTIFICATE-----
     [certificate content]
     -----END CERTIFICATE-----
     ```

   c. Tạo file `mon88.click.key`:
   - Mở Notepad với quyền Administrator
   - Paste **Private Key** vào
   - Lưu tại: `C:\xampp\apache\conf\ssl\mon88.click.key`
   - Format:
     ```
     -----BEGIN PRIVATE KEY-----
     [private key content]
     -----END PRIVATE KEY-----
     ```

4. **Cấu hình Apache Virtual Host HTTPS**:

   Mở file: `C:\xampp\apache\conf\extra\httpd-vhosts.conf`

   Thêm Virtual Host HTTPS:
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

5. **Bật SSL Module trong Apache**:

   Mở file: `C:\xampp\apache\conf\httpd.conf`

   Tìm và bỏ dấu `#` ở:
   ```apache
   LoadModule ssl_module modules/mod_ssl.so
   Include conf/extra/httpd-ssl.conf
   ```

6. **Mở Port 443 trong Firewall**:
   ```powershell
   New-NetFirewallRule -DisplayName "HTTPS" -Direction Inbound -LocalPort 443 -Protocol TCP -Action Allow
   ```

7. **Khởi động lại Apache** trong XAMPP Control Panel

8. **Test HTTPS trực tiếp** (bỏ qua Cloudflare):
   - Truy cập: `https://[IP_VPS]` (thay [IP_VPS] bằng IP thực của VPS)
   - Nếu thấy cảnh báo certificate, đó là bình thường (Origin Certificate chỉ dùng với Cloudflare)
   - Nếu không kết nối được, kiểm tra lại cấu hình

9. **Cập nhật Cloudflare SSL Mode**:
   - Vào **SSL/TLS** → **Overview**
   - Đổi sang **`Full (strict)`**
   - Đợi 1-2 phút

---

### Giải Pháp 3: Kiểm Tra Port 443

#### Kiểm Tra Apache Đang Lắng Nghe Port 443

```powershell
# Kiểm tra port 443 đã mở
netstat -an | findstr :443

# Hoặc test kết nối
Test-NetConnection -ComputerName localhost -Port 443
```

#### Nếu Port 443 Chưa Mở:

1. **Kiểm tra Firewall**:
   ```powershell
   # Xem firewall rules
   Get-NetFirewallRule | Where-Object {$_.DisplayName -like "*443*" -or $_.DisplayName -like "*HTTPS*"}
   
   # Mở port 443
   New-NetFirewallRule -DisplayName "HTTPS" -Direction Inbound -LocalPort 443 -Protocol TCP -Action Allow
   ```

2. **Kiểm tra Apache đang chạy**:
   - XAMPP Control Panel → Apache phải đang chạy
   - Kiểm tra Apache error log: `C:\xampp\apache\logs\error.log`

---

### Giải Pháp 4: Kiểm Tra Certificate Files

#### Kiểm Tra Format Certificate

1. **Kiểm tra file .crt**:
   ```powershell
   Get-Content "C:\xampp\apache\conf\ssl\mon88.click.crt"
   ```
   - Phải bắt đầu bằng: `-----BEGIN CERTIFICATE-----`
   - Phải kết thúc bằng: `-----END CERTIFICATE-----`
   - Không có ký tự thừa, không có spaces ở đầu/cuối

2. **Kiểm tra file .key**:
   ```powershell
   Get-Content "C:\xampp\apache\conf\ssl\mon88.click.key"
   ```
   - Phải bắt đầu bằng: `-----BEGIN PRIVATE KEY-----` hoặc `-----BEGIN RSA PRIVATE KEY-----`
   - Phải kết thúc bằng: `-----END PRIVATE KEY-----` hoặc `-----END RSA PRIVATE KEY-----`

3. **Kiểm tra Quyền Truy Cập**:
   ```powershell
   # Cấp quyền đọc cho Apache
   icacls "C:\xampp\apache\conf\ssl\mon88.click.crt" /grant "Everyone:R"
   icacls "C:\xampp\apache\conf\ssl\mon88.click.key" /grant "Everyone:R"
   ```

---

### Giải Pháp 5: Kiểm Tra Apache Configuration

#### Kiểm Tra mod_ssl Đã Bật

1. Mở file: `C:\xampp\apache\conf\httpd.conf`
2. Tìm dòng:
   ```apache
   LoadModule ssl_module modules/mod_ssl.so
   ```
3. Đảm bảo **không có dấu `#`** ở đầu dòng

#### Kiểm Tra Virtual Host HTTPS

1. Mở file: `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
2. Đảm bảo có Virtual Host cho port 443
3. Kiểm tra đường dẫn certificate files đúng

#### Test Apache Configuration

```powershell
# Test cấu hình Apache (không start server)
cd C:\xampp\apache\bin
.\httpd.exe -t
```

Nếu có lỗi, sẽ hiển thị thông báo lỗi cụ thể.

---

### Giải Pháp 6: Kiểm Tra Cloudflare Settings

#### 1. Kiểm Tra SSL/TLS Mode

- **Flexible**: Cloudflare ↔ Origin (HTTP) - Không cần SSL trên origin
- **Full**: Cloudflare ↔ Origin (HTTPS) - Cần SSL trên origin
- **Full (strict)**: Cloudflare ↔ Origin (HTTPS + valid certificate)

#### 2. Kiểm Tra Always Use HTTPS

- Vào **SSL/TLS** → **Edge Certificates**
- Đảm bảo **Always Use HTTPS** đang bật

#### 3. Kiểm Tra DNS Records

- Vào **DNS** → **Records**
- Đảm bảo A record có **Proxy status**: 🟠 **Proxied**
- Nếu là ⚪ **DNS only**, Cloudflare sẽ không xử lý SSL

---

## Checklist Khắc Phục Lỗi 526

- [ ] Cloudflare SSL mode đã đặt đúng (Flexible nếu chưa có SSL, Full nếu đã có SSL)
- [ ] Apache đang chạy trong XAMPP Control Panel
- [ ] mod_ssl đã được bật trong httpd.conf
- [ ] Virtual Host HTTPS đã được cấu hình trong httpd-vhosts.conf
- [ ] Certificate files (.crt và .key) tồn tại và có format đúng
- [ ] Đường dẫn certificate files trong Virtual Host đúng
- [ ] Port 443 đã mở trong Windows Firewall
- [ ] Port 443 đang được Apache lắng nghe (netstat -an | findstr :443)
- [ ] DNS records trong Cloudflare có Proxy status: 🟠 Proxied
- [ ] Test HTTPS trực tiếp đến IP VPS (bỏ qua Cloudflare)

---

## Test Kết Nối

### Test 1: Test HTTPS Trực Tiếp (Bỏ Qua Cloudflare)

```powershell
# Test từ local
Test-NetConnection -ComputerName [IP_VPS] -Port 443

# Hoặc dùng browser
https://[IP_VPS]
```

**Kết quả mong đợi**:
- Kết nối thành công (có thể có cảnh báo certificate - đó là bình thường với Origin Certificate)

### Test 2: Test Từ Cloudflare

1. Tạm thời đổi DNS record về **DNS only** (⚪)
2. Test: `https://mon88.click`
3. Nếu lỗi, vấn đề ở origin server
4. Nếu OK, đổi lại **Proxied** (🟠) và test lại

### Test 3: Kiểm Tra Apache Logs

```powershell
# Xem error log
Get-Content "C:\xampp\apache\logs\error.log" -Tail 50

# Xem SSL error log (nếu có)
Get-Content "C:\xampp\apache\logs\mon88.click_ssl_error.log" -Tail 50
```

---

## Lỗi Thường Gặp

### Lỗi: "SSL: error:0A000126:SSL routines::unexpected eof while reading"

**Nguyên nhân**: Certificate file không đúng format hoặc bị thiếu dòng

**Giải pháp**: Kiểm tra lại format certificate files, đảm bảo có đầy đủ BEGIN và END markers

### Lỗi: "SSLEngine: failed to enable"

**Nguyên nhân**: mod_ssl chưa được load

**Giải pháp**: Bỏ comment `LoadModule ssl_module modules/mod_ssl.so` trong httpd.conf

### Lỗi: "Cannot load SSL certificate file"

**Nguyên nhân**: Đường dẫn certificate file sai hoặc không có quyền đọc

**Giải pháp**: 
- Kiểm tra đường dẫn file
- Cấp quyền đọc cho file

### Lỗi: "Port 443 already in use"

**Nguyên nhân**: Port 443 đang được sử dụng bởi service khác

**Giải pháp**:
```powershell
# Tìm process đang dùng port 443
netstat -ano | findstr :443

# Kill process (thay [PID] bằng Process ID)
taskkill /PID [PID] /F
```

---

## Tóm Tắt - Giải Pháp Nhanh

### Nếu Chưa Có SSL Certificate:
1. **Đổi Cloudflare SSL mode → Flexible** (5 phút)
2. Website sẽ hoạt động ngay

### Nếu Muốn SSL End-to-End:
1. **Tạo Cloudflare Origin Certificate** (5 phút)
2. **Cài đặt trên Apache** (10 phút)
3. **Cấu hình Virtual Host HTTPS** (5 phút)
4. **Đổi Cloudflare SSL mode → Full (strict)** (1 phút)

---

## Hỗ Trợ Thêm

Nếu vẫn gặp vấn đề:
1. Kiểm tra Apache error log: `C:\xampp\apache\logs\error.log`
2. Kiểm tra Cloudflare SSL/TLS settings
3. Test kết nối HTTPS trực tiếp đến IP VPS
4. Xem thêm: `SSL_SETUP.md` để cấu hình SSL từ đầu

