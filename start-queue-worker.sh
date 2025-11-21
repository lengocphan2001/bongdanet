#!/bin/bash
echo "Starting Queue Worker..."
echo ""
echo "Make sure .env has:"
echo "  CACHE_STORE=file"
echo "  QUEUE_CONNECTION=database"
echo ""
echo "Press Ctrl+C to stop"
echo ""
php artisan queue:work --tries=3 --verbose

