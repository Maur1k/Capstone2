# Click2Serve - Transfer & Setup Guide

## 📦 Before Transferring (On Source Device)

### Files to EXCLUDE from ZIP (to reduce size):
These folders will be regenerated on the new device:
- `Click2Serve/Click2Serve/vendor/` (PHP dependencies - ~150MB)
- `print-service/node_modules/` (~50MB)
- `chatbot-service/node_modules/` (~30MB)
- `Click2Serve/Click2Serve/storage/logs/*.log` (optional)
- `Click2Serve/Click2Serve/storage/framework/cache/*`
- `Click2Serve/Click2Serve/storage/framework/sessions/*`
- `Click2Serve/Click2Serve/storage/framework/views/*`

### Files to INCLUDE (Important):
- `Click2Serve/Click2Serve/.env` (contains your database and API credentials)
- All batch files (`.bat`)
- All other project files

---

## 🖥️ Setup on New Device

### Prerequisites
1. **XAMPP** (includes PHP 8.1+ and MySQL/MariaDB)
   - Download: https://www.apachefriends.org/
   - Install to `C:\xampp`

2. **Composer** (PHP package manager)
   - Download: https://getcomposer.org/download/
   - Install globally

3. **Node.js 16+** (for print and chatbot services)
   - Download: https://nodejs.org/
   - Install LTS version

---

## 📋 Step-by-Step Setup

### Step 1: Extract the ZIP
```
Extract to: C:\xampp\htdocs\
```
Final structure should be:
```
C:\xampp\htdocs\Click2Serve\
├── Click2Serve\     <- Laravel app
├── print-service\
├── chatbot-service\
└── docs\
```

### Step 2: Start XAMPP Services
1. Open XAMPP Control Panel
2. Start **Apache**
3. Start **MySQL**

### Step 3: Install Dependencies
```
Double-click: C:\xampp\htdocs\Click2Serve\Click2Serve\setup-new-device.bat
```
This will:
- Install PHP dependencies (composer install)
- Generate application key
- Clear caches
- Create storage link
- Install Node.js dependencies for services

### Step 4: Setup Database
```
Double-click: C:\xampp\htdocs\Click2Serve\Click2Serve\FRESH_SETUP.bat
```
This will:
- Create the `click2serve` database
- Run all migrations
- Seed default data

**Alternative Manual Setup:**
```powershell
cd C:\xampp\htdocs\Click2Serve\Click2Serve
php artisan migrate --force
php artisan db:seed --force
```

### Step 5: Configure Environment (if needed)
Edit `Click2Serve\Click2Serve\.env` file:
```env
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=click2serve
DB_USERNAME=root
DB_PASSWORD=

# SMS Service (Semaphore)
SEMAPHORE_API_KEY=your_api_key_here
SEMAPHORE_SENDER_NAME=Click2Serve
SEMAPHORE_ENABLED=true
```

---

## 🚀 Running the Application

### Quick Start (Recommended)
```
Double-click: C:\xampp\htdocs\Click2Serve\Click2Serve\start-click2serve.bat
```
This starts all services automatically.

### Manual Start

**Terminal 1 - Laravel Server:**
```powershell
cd C:\xampp\htdocs\Click2Serve\Click2Serve
php artisan serve
```
Access: http://127.0.0.1:8000

**Terminal 2 - Print Service:**
```powershell
cd C:\xampp\htdocs\Click2Serve\print-service
npm start
```
Runs on: http://localhost:4000

**Terminal 3 - Chatbot Service:**
```powershell
cd C:\xampp\htdocs\Click2Serve\chatbot-service
npm start
```
Runs on: http://localhost:5000

---

## 🔗 Access URLs

| Feature | URL |
|---------|-----|
| Kiosk Mode | http://127.0.0.1:8000 |
| Website | http://127.0.0.1:8000/website |
| Admin Panel | http://127.0.0.1:8000/admin |
| Officer Dashboard | http://127.0.0.1:8000/officer |
| Service Tracker | http://127.0.0.1:8000/track |
| Book Appointment | http://127.0.0.1:8000/service |

---

## 🔐 Default Login Credentials

### Admin
- URL: http://127.0.0.1:8000/admin
- Email: `admin@example.com`
- Password: `password`

### Officer Accounts (after seeding)
- Civil Registry: `civil@gmail.com` / `password`
- BPLO: `bplo@gmail.com` / `password`
- CTRU: `ctru@gmail.com` / `password`
- Veterinary: `vet@gmail.com` / `password`

---

## ❗ Troubleshooting

### "Class not found" errors
```powershell
cd C:\xampp\htdocs\Click2Serve\Click2Serve
composer dump-autoload
php artisan optimize:clear
```

### Database connection errors
1. Ensure MySQL is running in XAMPP
2. Check `.env` database credentials
3. Verify database `click2serve` exists

### Storage/Permission errors
```powershell
php artisan storage:link
```
On Windows, run as Administrator if needed.

### CSS/JS not loading
```powershell
php artisan cache:clear
php artisan view:clear
```

### SMS not sending
1. Check `SEMAPHORE_API_KEY` in `.env`
2. Ensure `SEMAPHORE_ENABLED=true`
3. Check Laravel logs: `storage/logs/laravel.log`

---

## ✅ Setup Checklist

- [ ] XAMPP installed
- [ ] Composer installed
- [ ] Node.js installed
- [ ] XAMPP Apache & MySQL running
- [ ] `setup-new-device.bat` completed
- [ ] `FRESH_SETUP.bat` completed
- [ ] `start-click2serve.bat` running
- [ ] Can access http://127.0.0.1:8000
- [ ] Can login to admin panel

---

**Last Updated:** November 2025
