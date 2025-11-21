# Quick Test Guide - Local Setup

## ✅ Đã Sửa Lỗi

Lỗi `runInBackground()` đã được sửa. Bây giờ bạn có thể test:

## Bước 1: Đảm Bảo .env Đúng

**Quan trọng:** Kiểm tra `.env` có:
```env
CACHE_STORE=file
QUEUE_CONNECTION=database
```

Nếu không có, thêm vào `.env` và chạy:
```bash
php artisan config:clear
```

**Nếu gặp lỗi Redis:** Xem `FIX_REDIS_ERROR.md`

## Bước 2: Chạy Queue Worker

**Mở Terminal 1:**
```bash
php artisan queue:work --tries=3 --verbose
```

Giữ terminal này chạy.

## Bước 3: Warm Cache

**Mở Terminal 2 (mới):**
```bash
php artisan matches:warm-cache
```

Bạn sẽ thấy trong Terminal 1:
```
Processing: App\Jobs\FetchMatchesDataJob
Processed:  App\Jobs\FetchMatchesDataJob
```

## Bước 4: Test API

Mở browser:
```
http://localhost:8000/api/all-matches-table
```

**Lần đầu:** Có thể mất 2-5 giây (nếu cache chưa có)  
**Lần 2:** < 100ms (từ cache)

## Bước 5: Kiểm Tra Cache

```bash
php artisan tinker
```

```php
// Check cache
Cache::has('matches:all:prefetched');
Cache::has('matches:all:prefetched:fresh');

// Get data
$data = Cache::get('matches:all:prefetched');
echo "Live: " . count($data['live'] ?? []) . "\n";
echo "Upcoming: " . count($data['upcoming'] ?? []) . "\n";
```

## Bước 6: Test Scheduled Task (Optional)

```bash
php artisan schedule:run
```

Hoặc test job trực tiếp:
```bash
php artisan tinker
```

```php
\App\Jobs\FetchMatchesDataJob::dispatch();
```

## Troubleshooting

### Nếu gặp lỗi Redis:
Đảm bảo `.env` có:
```env
CACHE_STORE=file
QUEUE_CONNECTION=database
```

Sau đó:
```bash
php artisan config:clear
php artisan cache:clear
```

### Nếu queue không chạy:
```bash
# Check jobs table
php artisan tinker
>>> DB::table('jobs')->count();

# Nếu có jobs failed
php artisan queue:flush
```

### Nếu cache không hoạt động:
```bash
php artisan cache:clear
php artisan matches:warm-cache
```

## Checklist

- [ ] Queue worker đang chạy (Terminal 1)
- [ ] Cache đã được warm
- [ ] API trả về data
- [ ] Response time < 100ms (lần 2)
- [ ] Response có `"from_cache": true`

## Next Steps

Sau khi test xong, bạn có thể:
1. Deploy lên server
2. Setup Redis (tùy chọn, nhanh hơn)
3. Setup Supervisor cho queue worker
4. Setup cron job

Xem `PERFORMANCE_OPTIMIZATION.md` để biết chi tiết.

