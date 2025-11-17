# PowerShell Deployment Script for Laravel on Windows IIS
# Usage: .\deploy.ps1

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Laravel Deployment Script" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Set project directory
$projectPath = "C:\inetpub\wwwroot\mon88.click"

# Check if directory exists
if (-Not (Test-Path $projectPath)) {
    Write-Host "Error: Project directory not found at $projectPath" -ForegroundColor Red
    exit 1
}

# Change to project directory
Set-Location $projectPath
Write-Host "Working directory: $projectPath" -ForegroundColor Green
Write-Host ""

# Step 1: Install/Update Composer Dependencies
Write-Host "Step 1: Installing Composer dependencies..." -ForegroundColor Yellow
composer install --optimize-autoloader --no-dev --no-interaction
if ($LASTEXITCODE -ne 0) {
    Write-Host "Error: Composer install failed" -ForegroundColor Red
    exit 1
}
Write-Host "Composer dependencies installed successfully!" -ForegroundColor Green
Write-Host ""

# Step 2: Install/Update NPM Dependencies
Write-Host "Step 2: Installing NPM dependencies..." -ForegroundColor Yellow
npm install
if ($LASTEXITCODE -ne 0) {
    Write-Host "Error: NPM install failed" -ForegroundColor Red
    exit 1
}
Write-Host "NPM dependencies installed successfully!" -ForegroundColor Green
Write-Host ""

# Step 3: Build Frontend Assets
Write-Host "Step 3: Building frontend assets..." -ForegroundColor Yellow
npm run build
if ($LASTEXITCODE -ne 0) {
    Write-Host "Error: NPM build failed" -ForegroundColor Red
    exit 1
}
Write-Host "Frontend assets built successfully!" -ForegroundColor Green
Write-Host ""

# Step 4: Check .env file
Write-Host "Step 4: Checking .env file..." -ForegroundColor Yellow
if (-Not (Test-Path ".env")) {
    Write-Host "Warning: .env file not found. Creating from .env.example..." -ForegroundColor Yellow
    if (Test-Path ".env.example") {
        Copy-Item ".env.example" ".env"
        Write-Host ".env file created. Please configure it before continuing." -ForegroundColor Yellow
        Write-Host "Press any key to continue after configuring .env..."
        $null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
    } else {
        Write-Host "Error: .env.example not found. Please create .env manually." -ForegroundColor Red
        exit 1
    }
}
Write-Host ".env file exists!" -ForegroundColor Green
Write-Host ""

# Step 5: Generate Application Key (if needed)
Write-Host "Step 5: Checking application key..." -ForegroundColor Yellow
$envContent = Get-Content ".env" -Raw
if ($envContent -notmatch "APP_KEY=base64:") {
    Write-Host "Generating application key..." -ForegroundColor Yellow
    php artisan key:generate
    Write-Host "Application key generated!" -ForegroundColor Green
} else {
    Write-Host "Application key already exists!" -ForegroundColor Green
}
Write-Host ""

# Step 6: Create database file if using SQLite
Write-Host "Step 6: Checking database..." -ForegroundColor Yellow
$dbPath = "database\database.sqlite"
if (-Not (Test-Path $dbPath)) {
    Write-Host "Creating SQLite database file..." -ForegroundColor Yellow
    New-Item -ItemType File -Path $dbPath -Force | Out-Null
    Write-Host "Database file created!" -ForegroundColor Green
} else {
    Write-Host "Database file exists!" -ForegroundColor Green
}
Write-Host ""

# Step 7: Run Migrations
Write-Host "Step 7: Running database migrations..." -ForegroundColor Yellow
php artisan migrate --force
if ($LASTEXITCODE -ne 0) {
    Write-Host "Warning: Migrations may have failed. Check the output above." -ForegroundColor Yellow
}
Write-Host "Migrations completed!" -ForegroundColor Green
Write-Host ""

# Step 7.5: Seed Database (Admin User)
Write-Host "Step 7.5: Seeding database (admin user)..." -ForegroundColor Yellow
php artisan db:seed --class=AdminUserSeeder
if ($LASTEXITCODE -ne 0) {
    Write-Host "Warning: Seeding may have failed. Check the output above." -ForegroundColor Yellow
} else {
    Write-Host "Admin user seeded successfully!" -ForegroundColor Green
    Write-Host "  Email: admin@bongdanet.co" -ForegroundColor Gray
    Write-Host "  Password: admin123" -ForegroundColor Gray
    Write-Host "  ⚠️  Please change the password after first login!" -ForegroundColor Yellow
}
Write-Host ""

# Step 8: Create Storage Link
Write-Host "Step 8: Creating storage link..." -ForegroundColor Yellow
php artisan storage:link
Write-Host "Storage link created!" -ForegroundColor Green
Write-Host ""

# Step 9: Set Permissions
Write-Host "Step 9: Setting directory permissions..." -ForegroundColor Yellow
$directories = @("storage", "bootstrap\cache", "database")
foreach ($dir in $directories) {
    $fullPath = Join-Path $projectPath $dir
    if (Test-Path $fullPath) {
        icacls $fullPath /grant "IIS_IUSRS:(OI)(CI)F" /T /Q | Out-Null
        Write-Host "  Permissions set for: $dir" -ForegroundColor Gray
    }
}
Write-Host "Permissions configured!" -ForegroundColor Green
Write-Host ""

# Step 10: Cache Configuration
Write-Host "Step 10: Caching configuration..." -ForegroundColor Yellow
php artisan config:cache
php artisan route:cache
php artisan view:cache
Write-Host "Configuration cached!" -ForegroundColor Green
Write-Host ""

# Step 11: Clear old caches
Write-Host "Step 11: Clearing old caches..." -ForegroundColor Yellow
php artisan cache:clear
php artisan view:clear
Write-Host "Old caches cleared!" -ForegroundColor Green
Write-Host ""

# Final Summary
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Deployment Completed Successfully!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Verify .env configuration" -ForegroundColor White
Write-Host "2. Test the website: http://mon88.click" -ForegroundColor White
Write-Host "3. Check logs if there are any issues: storage\logs\laravel.log" -ForegroundColor White
Write-Host ""

