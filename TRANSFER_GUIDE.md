# Click2Serve System Transfer Guide

## 📋 **System Overview**
This is a Laravel-based municipal kiosk system with:
- Admin dashboard for super administrators
- Officer dashboard for department officers
- Activity logging system
- Request management system
- Service management

## 🗂️ **Essential Files Structure**
```
Click2Serve/
├── Click2Serve/                 # Main Laravel application
│   ├── app/
│   │   ├── Http/Controllers/    # All controllers
│   │   ├── Middleware/          # Authentication middleware
│   │   └── Models/              # Database models
│   ├── database/
│   │   ├── migrations/          # Database schema
│   │   └── seeders/             # Database seeders
│   ├── public/                  # Web-accessible files
│   │   ├── assets/              # Icons and images
│   │   └── css/                 # Stylesheets
│   ├── resources/views/         # Blade templates
│   ├── routes/web.php           # Application routes
│   └── .env                     # Environment configuration
├── chatbot-service/             # Node.js chatbot service
├── print-service/               # Node.js print service
└── storage/                     # File storage
```

## 🚀 **Installation Steps on New Laptop**

### **1. Prerequisites**
- XAMPP (PHP 8.2+, MySQL, Apache)
- Node.js (for chatbot and print services)
- Composer (PHP dependency manager)
- **Visual Studio Build Tools** (for print service native modules)

### **2. Database Setup**
1. Start XAMPP (Apache + MySQL)
2. Create database: `click2serve`
3. Import database or run migrations:
   ```bash
   cd Click2Serve
   php artisan migrate
   php artisan db:seed
   ```

### **3. Environment Configuration**
1. Copy `.env.example` to `.env`
2. Update database credentials:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=click2serve
   DB_USERNAME=root
   DB_PASSWORD=
   ```
3. Generate application key:
   ```bash
   php artisan key:generate
   ```

### **4. Visual Studio Build Tools Setup**
**REQUIRED for Print Service to work!**

The print service uses native Node.js modules (`usb`, `escpos-usb`) that require compilation:

1. **Download Visual Studio Build Tools:**
   - Go to: https://visualstudio.microsoft.com/downloads/
   - Download "Build Tools for Visual Studio 2022"
   - Install with these workloads:
     - ✅ **C++ build tools**
     - ✅ **Windows 10/11 SDK**
     - ✅ **MSVC v143 - VS 2022 C++ x64/x86 build tools**

2. **Alternative (Smaller Download):**
   - Download "Visual Studio Installer"
   - Select "C++ build tools" workload
   - Install only the essential components

### **5. Start Services**
1. **Laravel Application:**
   ```bash
   cd Click2Serve
   php artisan serve --host=127.0.0.1 --port=8000
   ```

2. **Chatbot Service:**
   ```bash
   cd chatbot-service
   npm install
   npm start
   ```

3. **Print Service:**
   ```bash
   cd print-service
   npm install
   npm start
   ```

## 🔧 **Configuration Files**

### **Essential Files to Keep:**
- `Click2Serve/.env` - Environment configuration
- `Click2Serve/config/` - All configuration files
- `Click2Serve/database/migrations/` - Database schema
- `Click2Serve/database/seeders/` - Database seeders
- `Click2Serve/routes/web.php` - Application routes
- `Click2Serve/public/assets/` - Icons and images
- `Click2Serve/public/css/` - Stylesheets

### **Files Removed (No longer needed):**
- Test files (`test-*.html`, `debug.php`)
- Unused seeders (`TestRequestSeeder`, `TestOfficerSeeder`, `ActivityLogSeeder`)
- Unused models (`User.php`, `SmsLog.php`)
- Duplicate assets (kept only in `public/` folder)
- Configuration scripts (`configure-*.bat`, `configure-*.ps1`)

## 🗄️ **Database Requirements**

### **Required Tables:**
- `admins` - Admin and officer accounts
- `activity_logs` - Officer activity tracking
- `services` - Available services
- `service_types` - Service categories
- `civil_services` - Civil service requests
- `other_services` - Other service requests
- `settings` - System settings

### **Default Admin Account:**
- Email: `admin@example.com`
- Password: `SuperAdmin123!`
- Role: `super_admin`

## 🌐 **Access URLs**

### **Main Application:**
- Home: `http://127.0.0.1:8000/`
- Admin Login: `http://127.0.0.1:8000/admin/login`
- Officer Login: `http://127.0.0.1:8000/officer/login`

### **Services:**
- Chatbot: `http://127.0.0.1:3000`
- Print Service: `http://127.0.0.1:3001`

## 🔐 **Default Accounts**

### **Super Admin:**
- Email: `admin@example.com`
- Password: `SuperAdmin123!`

### **Test Officer:**
- Email: `officer@example.com`
- Password: `password123`
- Department: `City Veterinary Office`

## 📝 **Important Notes**

1. **File Permissions:** Ensure `storage/` and `bootstrap/cache/` are writable
2. **Database:** Always backup before making changes
3. **Environment:** Never commit `.env` file to version control
4. **Logs:** Check `storage/logs/laravel.log` for errors
5. **Cache:** Clear cache if issues occur: `php artisan cache:clear`

## 🚨 **Troubleshooting**

### **Common Issues:**
1. **500 Error:** Check file permissions and `.env` configuration
2. **Database Connection:** Verify MySQL is running and credentials are correct
3. **Missing Assets:** Ensure `public/assets/` folder exists
4. **Route Not Found:** Clear route cache: `php artisan route:clear`
5. **Print Service Won't Start:** Install Visual Studio Build Tools (see step 4)
6. **USB Module Compilation Error:** Ensure C++ build tools are installed

### **Useful Commands:**
```bash
php artisan migrate:fresh --seed    # Reset database with seeders
php artisan config:clear            # Clear configuration cache
php artisan route:clear             # Clear route cache
php artisan view:clear              # Clear view cache
```

## 🖨️ **Print Service Dependencies**

### **Why Visual Studio Build Tools are Required:**
The print service uses these native Node.js modules that require compilation:

- **`usb@1.6.3`** - USB device communication
- **`escpos-usb@3.0.0-alpha.2`** - ESC/POS printer commands via USB

These modules contain C++ code that needs to be compiled for Windows.

### **Installation Order:**
1. **First:** Install Visual Studio Build Tools
2. **Then:** Install Node.js
3. **Finally:** Run `npm install` in print-service directory

### **Verification:**
After installation, verify with:
```bash
cd print-service
npm install
# Should complete without compilation errors
```

---
**Last Updated:** $(date)
**Version:** 1.0
**Laravel Version:** 11.x
