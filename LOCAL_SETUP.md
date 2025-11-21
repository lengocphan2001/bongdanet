# Hướng Dẫn Setup & Test Trên Local

## Bước 1: Cấu Hình Cache (File Cache - Đơn Giản Nhất)

File cache đã có sẵn, không cần cài thêm gì. 

**Quan trọng:** Đảm bảo `.env` có:
```env
CACHE_STORE=file
QUEUE_CONNECTION=database
```

Nếu không có, thêm vào `.env` và chạy:
```bash
php artisan config:clear
```

## Bước 2: Tạo Database Tables cho Queue

```bash
php artisan queue:table
php artisan migrate
```

## Bước 3: Test Background Job

### Terminal 1: Chạy Queue Worker
```bash
php artisan queue:work --tries=3 --verbose
```

### Terminal 2: Warm Cache Lần Đầu
```bash
php artisan matches:warm-cache
```

Bạn sẽ thấy job được dispatch và chạy trong Terminal 1.

## Bước 4: Test API Endpoint

Mở browser hoặc dùng Postman:
```
GET http://localhost:8000/api/all-matches-table
```

**Lần đầu tiên:**
- Sẽ fetch từ API (mất 2-5 giây)
- Cache data cho lần sau
- Response có `"from_cache": false`

**Lần thứ 2 (trong vòng 30 giây):**
- Trả về từ cache ngay lập tức (< 100ms)
- Response có `"from_cache": true`
- Header `X-Cache-Status: HIT`

## Bước 5: Test Scheduled Task

### Cách 1: Chạy Thủ Công
```bash
php artisan schedule:run
```

### Cách 2: Test với Tinker
```bash
php artisan tinker
```

```php
// Dispatch job thủ công
\App\Jobs\FetchMatchesDataJob::dispatch();

// Check cache
Cache::get('matches:all:prefetched');
Cache::get('matches:all:prefetched:fresh');
```

## Bước 6: Monitor Cache

### Xem Cache Keys
```bash
php artisan tinker
```

```php
// Check cache exists
Cache::has('matches:all:prefetched');
Cache::has('matches:all:prefetched:fresh');

// Get cache data
$data = Cache::get('matches:all:prefetched');
echo "Live matches: " . count($data['live'] ?? []) . "\n";
echo "Upcoming matches: " . count($data['upcoming'] ?? []) . "\n";
echo "Cache age: " . (now()->timestamp - ($data['timestamp'] ?? 0)) . " seconds\n";
```

### Xem Logs
```bash
# Windows PowerShell
Get-Content storage\logs\laravel.log -Tail 50 -Wait

# Linux/Mac
tail -f storage/logs/laravel.log | grep "FetchMatchesDataJob\|getAllMatchesTable"
```

## Bước 7: Test Stale-While-Revalidate

1. **Warm cache:**
```bash
php artisan matches:warm-cache
```

2. **Đợi 35 giây** (để fresh cache expire)

3. **Call API lại:**
```
GET http://localhost:8000/api/all-matches-table
```

4. **Kết quả:**
- Response có `"stale": true`
- Header `X-Cache-Status: STALE`
- Vẫn trả về ngay (< 100ms)
- Background job tự động refresh cache

## Bước 8: Test với Nhiều Requests

### Script Test (test-performance.php)
```php
<?php
// Tạo file test-performance.php trong root

$url = 'http://localhost:8000/api/all-matches-table';
$iterations = 10;

echo "Testing $iterations requests...\n\n";

$times = [];
for ($i = 1; $i <= $iterations; $i++) {
    $start = microtime(true);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $end = microtime(true);
    $time = ($end - $start) * 1000; // Convert to milliseconds
    $times[] = $time;
    
    $data = json_decode($response, true);
    $fromCache = $data['from_cache'] ?? false;
    $cacheStatus = $httpCode === 200 ? 'OK' : 'ERROR';
    
    echo sprintf(
        "Request #%d: %.2fms | Cache: %s | Status: %s\n",
        $i,
        $time,
        $fromCache ? 'YES' : 'NO',
        $cacheStatus
    );
}

echo "\n--- Statistics ---\n";
echo "Average: " . number_format(array_sum($times) / count($times), 2) . "ms\n";
echo "Min: " . number_format(min($times), 2) . "ms\n";
echo "Max: " . number_format(max($times), 2) . "ms\n";
```

Chạy:
```bash
php test-performance.php
```

## Troubleshooting Local

### Queue không chạy
```bash
# Check jobs table
php artisan tinker
>>> DB::table('jobs')->count();

# Clear failed jobs
php artisan queue:flush
```

### Cache không hoạt động
```bash
# Clear cache
php artisan cache:clear

# Check cache driver
php artisan tinker
>>> config('cache.default');
>>> Cache::put('test', 'value', 60);
>>> Cache::get('test');
```

### Job không được dispatch
```bash
# Check logs
tail -f storage/logs/laravel.log

# Test dispatch manually
php artisan tinker
>>> \App\Jobs\FetchMatchesDataJob::dispatch();
```

## Checklist Local Testing

- [ ] Queue worker đang chạy
- [ ] Cache được tạo sau khi warm
- [ ] API trả về từ cache (< 100ms)
- [ ] Stale cache hoạt động đúng
- [ ] Background job tự động refresh
- [ ] Logs hiển thị đúng thông tin

## Next Steps

Sau khi test xong trên local, bạn có thể:
1. Deploy lên server
2. Setup Redis (tùy chọn, nhanh hơn)
3. Setup Supervisor cho queue worker
4. Setup cron job

Xem `PERFORMANCE_OPTIMIZATION.md` để biết chi tiết production setup.

