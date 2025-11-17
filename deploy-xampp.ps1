# PowerShell Deployment Script for Laravel on XAMPP
# Usage: .\deploy-xampp.ps1

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Laravel Deployment Script (XAMPP)" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Set project directory
$projectPath = "C:\xampp\htdocs\mon88.click"

# Check if directory exists
if (-Not (Test-Path $projectPath)) {
    Write-Host "Error: Project directory not found at $projectPath" -ForegroundColor Red
    Write-Host "Creating directory..." -ForegroundColor Yellow
    New-Item -ItemType Directory -Path $projectPath -Force | Out-Null
    Write-Host "Directory created. Please upload your code first." -ForegroundColor Yellow
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

# Step 6: Check MySQL Connection
Write-Host "Step 6: Checking MySQL database..." -ForegroundColor Yellow
$envContent = Get-Content ".env" -Raw
if ($envContent -match "DB_CONNECTION=mysql") {
    Write-Host "MySQL configuration detected." -ForegroundColor Green
    Write-Host "Please ensure:" -ForegroundColor Yellow
    Write-Host "  1. MySQL is running in XAMPP Control Panel" -ForegroundColor Gray
    Write-Host "  2. Database has been created in phpMyAdmin" -ForegroundColor Gray
    Write-Host "  3. Database credentials in .env are correct" -ForegroundColor Gray
} elseif ($envContent -match "DB_CONNECTION=sqlite") {
    Write-Host "SQLite configuration detected." -ForegroundColor Yellow
    $dbPath = "database\database.sqlite"
    if (-Not (Test-Path $dbPath)) {
        Write-Host "Creating SQLite database file..." -ForegroundColor Yellow
        New-Item -ItemType File -Path $dbPath -Force | Out-Null
        Write-Host "Database file created!" -ForegroundColor Green
    } else {
        Write-Host "Database file exists!" -ForegroundColor Green
    }
} else {
    Write-Host "Warning: Database configuration not found in .env" -ForegroundColor Yellow
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

# Step 8: Create Storage Link
Write-Host "Step 8: Creating storage link..." -ForegroundColor Yellow
php artisan storage:link
Write-Host "Storage link created!" -ForegroundColor Green
Write-Host ""

# Step 9: Set Permissions
Write-Host "Step 9: Setting directory permissions..." -ForegroundColor Yellow
$directories = @("storage", "bootstrap\cache")
foreach ($dir in $directories) {
    $fullPath = Join-Path $projectPath $dir
    if (Test-Path $fullPath) {
        icacls $fullPath /grant "Everyone:(OI)(CI)F" /T /Q | Out-Null
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

# Step 12: Check Apache Status
Write-Host "Step 12: Checking Apache status..." -ForegroundColor Yellow
$apacheProcess = Get-Process -Name "httpd" -ErrorAction SilentlyContinue
if ($apacheProcess) {
    Write-Host "Apache is running!" -ForegroundColor Green
} else {
    Write-Host "Warning: Apache is not running. Please start it from XAMPP Control Panel." -ForegroundColor Yellow
}
Write-Host ""

# Final Summary
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Deployment Completed Successfully!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Verify .env configuration" -ForegroundColor White
Write-Host "2. Ensure Apache is running in XAMPP Control Panel" -ForegroundColor White
Write-Host "3. Test the website: http://mon88.click" -ForegroundColor White
Write-Host "4. Check logs if there are any issues:" -ForegroundColor White
Write-Host "   - Laravel: storage\logs\laravel.log" -ForegroundColor Gray
Write-Host "   - Apache: C:\xampp\apache\logs\error.log" -ForegroundColor Gray
Write-Host ""

