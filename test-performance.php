<?php
/**
 * Performance Test Script
 * Test API response times with and without cache
 */

$url = 'http://localhost:8000/api/all-matches-table';
$iterations = 10;

echo "=== Performance Test ===\n";
echo "URL: $url\n";
echo "Iterations: $iterations\n\n";

$times = [];
$cacheHits = 0;
$cacheMisses = 0;

for ($i = 1; $i <= $iterations; $i++) {
    $start = microtime(true);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $end = microtime(true);
    $time = ($end - $start) * 1000; // Convert to milliseconds
    $times[] = $time;
    
    $data = json_decode($response, true);
    $fromCache = $data['from_cache'] ?? false;
    $stale = $data['stale'] ?? false;
    
    if ($fromCache) {
        $cacheHits++;
        $cacheStatus = $stale ? 'STALE' : 'FRESH';
    } else {
        $cacheMisses++;
        $cacheStatus = 'MISS';
    }
    
    $status = $httpCode === 200 ? 'OK' : 'ERROR';
    
    echo sprintf(
        "Request #%2d: %6.2fms | Cache: %-5s | Status: %s\n",
        $i,
        $time,
        $cacheStatus,
        $status
    );
}

echo "\n=== Statistics ===\n";
echo "Total requests: $iterations\n";
echo "Cache hits: $cacheHits (" . round($cacheHits / $iterations * 100, 1) . "%)\n";
echo "Cache misses: $cacheMisses (" . round($cacheMisses / $iterations * 100, 1) . "%)\n";
echo "Average time: " . number_format(array_sum($times) / count($times), 2) . "ms\n";
echo "Min time: " . number_format(min($times), 2) . "ms\n";
echo "Max time: " . number_format(max($times), 2) . "ms\n";

// Performance assessment
$avgTime = array_sum($times) / count($times);
if ($avgTime < 100) {
    echo "\n✅ EXCELLENT: Average response time < 100ms\n";
} elseif ($avgTime < 500) {
    echo "\n✅ GOOD: Average response time < 500ms\n";
} elseif ($avgTime < 2000) {
    echo "\n⚠️  WARNING: Average response time > 500ms\n";
} else {
    echo "\n❌ CRITICAL: Average response time > 2s\n";
}

if ($cacheHits / $iterations > 0.9) {
    echo "✅ Cache hit rate > 90%\n";
} else {
    echo "⚠️  Cache hit rate < 90% - Check queue worker\n";
}

