# Fix Redis Error - Queue Worker

## Vấn Đề

Khi chạy `php artisan queue:work`, gặp lỗi:
```
Class "Redis" not found
```

## Nguyên Nhân

Laravel queue worker cần cache để lưu restart signal. Nếu `.env` không có `CACHE_STORE`, nó sẽ mặc định dùng Redis.

## Giải Pháp

### Bước 1: Thêm vào `.env`

Mở file `.env` và thêm (hoặc đảm bảo có):
```env
CACHE_STORE=file
QUEUE_CONNECTION=database
```

### Bước 2: Clear Config Cache

```bash
php artisan config:clear
```

### Bước 3: Test Queue Worker

```bash
php artisan queue:work --tries=3 --verbose --once
```

Nếu không có lỗi, bạn có thể chạy:
```bash
php artisan queue:work --tries=3 --verbose
```

## Script Helper

Tôi đã tạo script helper:
- **Windows**: `start-queue-worker.bat`
- **Linux/Mac**: `start-queue-worker.sh` (chạy `chmod +x start-queue-worker.sh` trước)

## Kiểm Tra

```bash
# Check config
php artisan tinker
>>> config('cache.default');
>>> config('queue.default');
```

Phải trả về:
- `cache.default` = `file`
- `queue.default` = `database`

## Nếu Vẫn Lỗi

1. Kiểm tra `.env` có đúng không
2. Clear tất cả cache:
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

3. Kiểm tra `config/cache.php` và `config/queue.php` không bị override

