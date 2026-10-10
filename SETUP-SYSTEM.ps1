# Click2Serve System Setup Script
# This script helps set up the Click2Serve system on Windows

Write-Host "============================================" -ForegroundColor Cyan
Write-Host "   Click2Serve - System Setup Script" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""

# Get the directory where this script is located
$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location $ScriptDir

# Step 1: Check PHP Version
Write-Host "[Step 1/7] Checking PHP Version..." -ForegroundColor Yellow
$phpVersion = php -v 2>&1 | Select-String -Pattern "PHP (\d+\.\d+)" | ForEach-Object { $_.Matches[0].Groups[1].Value }
$phpVersionMajor = [int]($phpVersion -split '\.')[0]
$phpVersionMinor = [int]($phpVersion -split '\.')[1]

Write-Host "Current PHP Version: $phpVersion" -ForegroundColor White

if ($phpVersionMajor -lt 8 -or ($phpVersionMajor -eq 8 -and $phpVersionMinor -lt 2)) {
    Write-Host ""
    Write-Host "ERROR: PHP 8.2 or higher is required!" -ForegroundColor Red
    Write-Host "Current version: PHP $phpVersion" -ForegroundColor Red
    Write-Host ""
    Write-Host "To upgrade PHP in XAMPP:" -ForegroundColor Yellow
    Write-Host "1. Download PHP 8.2+ from: https://windows.php.net/download/" -ForegroundColor White
    Write-Host "2. Extract to: C:\xampp\php (backup old folder first)" -ForegroundColor White
    Write-Host "3. Copy php.ini from old PHP folder to new one" -ForegroundColor White
    Write-Host "4. Update php.ini extensions as needed" -ForegroundColor White
    Write-Host "5. Restart your terminal/PowerShell" -ForegroundColor White
    Write-Host ""
    Write-Host "Alternative: Install PHP 8.2+ separately and add to PATH" -ForegroundColor Yellow
    Write-Host ""
    $continue = Read-Host "Do you want to continue anyway? (y/n)"
    if ($continue -ne "y" -and $continue -ne "Y") {
        Write-Host "Setup cancelled." -ForegroundColor Red
        exit 1
    }
} else {
    Write-Host "✓ PHP version is compatible!" -ForegroundColor Green
}

Write-Host ""

# Step 2: Check Composer
Write-Host "[Step 2/7] Checking Composer..." -ForegroundColor Yellow
try {
    $composerVersion = composer --version 2>&1
    Write-Host "✓ Composer is installed: $($composerVersion -split "`n" | Select-Object -First 1)" -ForegroundColor Green
} catch {
    Write-Host "ERROR: Composer is not installed or not in PATH!" -ForegroundColor Red
    Write-Host "Download from: https://getcomposer.org/download/" -ForegroundColor Yellow
    exit 1
}

Write-Host ""

# Step 3: Check Node.js
Write-Host "[Step 3/7] Checking Node.js..." -ForegroundColor Yellow
try {
    $nodeVersion = node --version 2>&1
    Write-Host "✓ Node.js is installed: $nodeVersion" -ForegroundColor Green
} catch {
    Write-Host "WARNING: Node.js is not installed or not in PATH!" -ForegroundColor Yellow
    Write-Host "Node.js is required for print-service and chatbot-service" -ForegroundColor Yellow
    Write-Host "Download from: https://nodejs.org/" -ForegroundColor Yellow
}

Write-Host ""

# Step 4: Check MySQL/XAMPP
Write-Host "[Step 4/7] Checking MySQL Connection..." -ForegroundColor Yellow
try {
    $mysqlTest = & C:\xampp\mysql\bin\mysql.exe -u root -e "SELECT 1;" 2>&1
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✓ MySQL is accessible" -ForegroundColor Green
    } else {
        Write-Host "WARNING: Could not connect to MySQL. Make sure XAMPP MySQL is running!" -ForegroundColor Yellow
    }
} catch {
    Write-Host "WARNING: Could not test MySQL connection. Make sure XAMPP MySQL is running!" -ForegroundColor Yellow
}

Write-Host ""

# Step 5: Install PHP Dependencies
Write-Host "[Step 5/7] Installing PHP Dependencies (Composer)..." -ForegroundColor Yellow
Write-Host "This may take a few minutes..." -ForegroundColor Gray
try {
    composer install --no-interaction
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✓ PHP dependencies installed successfully!" -ForegroundColor Green
    } else {
        Write-Host "ERROR: Composer install failed!" -ForegroundColor Red
        exit 1
    }
} catch {
    Write-Host "ERROR: Failed to run composer install!" -ForegroundColor Red
    exit 1
}

Write-Host ""

# Step 6: Setup Environment File
Write-Host "[Step 6/7] Setting up Environment File..." -ForegroundColor Yellow
if (-not (Test-Path ".env")) {
    if (Test-Path ".env.example") {
        Copy-Item ".env.example" ".env"
        Write-Host "✓ Created .env file from .env.example" -ForegroundColor Green
    } else {
        Write-Host "WARNING: .env.example not found. Creating basic .env file..." -ForegroundColor Yellow
        @"
APP_NAME=Click2Serve
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=click2serve
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"
"@ | Out-File -FilePath ".env" -Encoding utf8
        Write-Host "✓ Created basic .env file" -ForegroundColor Green
    }
} else {
    Write-Host "✓ .env file already exists" -ForegroundColor Green
}

# Generate application key if not set
$envContent = Get-Content ".env" -Raw
if ($envContent -notmatch "APP_KEY=base64:") {
    Write-Host "Generating application key..." -ForegroundColor Gray
    php artisan key:generate --force
    Write-Host "✓ Application key generated" -ForegroundColor Green
} else {
    Write-Host "✓ Application key already set" -ForegroundColor Green
}

Write-Host ""

# Step 7: Database Setup
Write-Host "[Step 7/7] Database Setup..." -ForegroundColor Yellow
Write-Host ""
$setupDb = Read-Host "Do you want to create/refresh the database? (y/n)"
if ($setupDb -eq "y" -or $setupDb -eq "Y") {
    Write-Host "Creating database..." -ForegroundColor Gray
    try {
        & C:\xampp\mysql\bin\mysql.exe -u root -e "DROP DATABASE IF EXISTS click2serve; CREATE DATABASE click2serve CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>&1 | Out-Null
        if ($LASTEXITCODE -eq 0) {
            Write-Host "✓ Database created successfully!" -ForegroundColor Green
        } else {
            Write-Host "WARNING: Could not create database. You may need to create it manually." -ForegroundColor Yellow
        }
    } catch {
        Write-Host "WARNING: Could not create database. You may need to create it manually." -ForegroundColor Yellow
    }
    
    Write-Host "Running migrations..." -ForegroundColor Gray
    php artisan migrate --force
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✓ Migrations completed!" -ForegroundColor Green
    } else {
        Write-Host "WARNING: Migrations failed. Check your database configuration in .env" -ForegroundColor Yellow
    }
    
    Write-Host "Seeding database..." -ForegroundColor Gray
    php artisan db:seed --force
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✓ Database seeded!" -ForegroundColor Green
    } else {
        Write-Host "WARNING: Seeding failed." -ForegroundColor Yellow
    }
} else {
    Write-Host "Skipping database setup. Run 'php artisan migrate' and 'php artisan db:seed' manually." -ForegroundColor Yellow
}

Write-Host ""

# Step 8: Install Node.js Dependencies (Optional)
Write-Host "[Optional] Installing Node.js Dependencies..." -ForegroundColor Yellow
$installNode = Read-Host "Do you want to install Node.js dependencies for print-service and chatbot-service? (y/n)"
if ($installNode -eq "y" -or $installNode -eq "Y") {
    if (Get-Command node -ErrorAction SilentlyContinue) {
        Write-Host "Installing print-service dependencies..." -ForegroundColor Gray
        Set-Location "..\print-service"
        npm install
        Set-Location $ScriptDir
        
        Write-Host "Installing chatbot-service dependencies..." -ForegroundColor Gray
        Set-Location "..\chatbot-service"
        npm install
        Set-Location $ScriptDir
        
        Write-Host "✓ Node.js dependencies installed!" -ForegroundColor Green
    } else {
        Write-Host "Node.js not found. Skipping Node.js dependencies." -ForegroundColor Yellow
    }
}

Write-Host ""

# Final Steps
Write-Host "============================================" -ForegroundColor Cyan
Write-Host "   Setup Complete!" -ForegroundColor Green
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Next Steps:" -ForegroundColor Yellow
Write-Host "1. Review and update .env file with your settings" -ForegroundColor White
Write-Host "2. Make sure XAMPP MySQL is running" -ForegroundColor White
Write-Host "3. Run 'php artisan serve --host=127.0.0.1 --port=8000' to start Laravel" -ForegroundColor White
Write-Host "   Optional print/chatbot services must be started separately." -ForegroundColor White
Write-Host ""
Write-Host "Access URLs:" -ForegroundColor Yellow
Write-Host "  - Kiosk Mode:    http://127.0.0.1:8000" -ForegroundColor White
Write-Host "  - Website:       http://127.0.0.1:8000/website" -ForegroundColor White
Write-Host "  - Admin Panel:   http://127.0.0.1:8000/admin" -ForegroundColor White
Write-Host "  - Officer Login: http://127.0.0.1:8000/officer" -ForegroundColor White
Write-Host ""
Write-Host "Default Admin Login:" -ForegroundColor Yellow
Write-Host "  Email: admin@example.com" -ForegroundColor White
Write-Host "  Password: password" -ForegroundColor White
Write-Host ""
Write-Host "Press any key to exit..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")













