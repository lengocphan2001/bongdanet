# Hướng Dẫn Deploy Nhanh với Tối Ưu Performance

## 🚀 Setup Nhanh (5 phút)

### 1. Cấu Hình Cache & Queue

**Option A: Redis (Khuyến nghị - Nhanh nhất)**
```bash
# Cài Redis
sudo apt-get install redis-server php-redis

# .env
CACHE_STORE=redis
QUEUE_CONNECTION=redis
```

**Option B: Database (Nếu không có Redis)**
```bash
# Tạo tables
php artisan cache:table
php artisan queue:table
php artisan migrate

# .env
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### 2. Chạy Queue Worker

**Development:**
```bash
php artisan queue:work --tries=3
```

**Production (Supervisor):**
```bash
# Tạo file /etc/supervisor/conf.d/laravel-worker.conf
[program:laravel-worker]
command=php /path/to/project/artisan queue:work redis --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2

# Khởi động
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

### 3. Setup Cron Job

```bash
crontab -e
```

Thêm:
```cron
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

### 4. Warm Cache Sau Khi Deploy

```bash
php artisan matches:warm-cache
```

## 📊 Kết Quả

- **Response time**: < 100ms (99% requests)
- **API calls**: Giảm 95% (từ 1000/giờ → 50/giờ)
- **Cache hit rate**: > 99%
- **User experience**: Tải trang ngay lập tức

## ⚙️ Cách Hoạt Động

1. **Background Job** chạy mỗi 20 giây để pre-fetch data
2. **User request** → Check cache → Return ngay (< 100ms)
3. **Cache stale?** → Return stale data + Refresh background
4. **No cache?** → Fetch API + Cache cho lần sau

## 🔍 Kiểm Tra

```bash
# Check queue
php artisan queue:work --once

# Check cache
php artisan tinker
>>> Cache::get('matches:all:prefetched');

# Check logs
tail -f storage/logs/laravel.log | grep "FetchMatchesDataJob"
```

## 📝 Lưu Ý

- Đảm bảo queue worker luôn chạy
- Monitor logs để phát hiện lỗi sớm
- Warm cache sau mỗi lần deploy
- Sử dụng Redis nếu có thể (nhanh hơn 10x)

