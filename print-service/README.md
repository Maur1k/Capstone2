# 🔥 Thermal Printer Integration - OC-PT210

This guide will help you integrate your **Officom OC-PT210** portable thermal receipt printer with your Click2Serve Laravel application.

## 📋 Prerequisites

- **Physical Device**: OC-PT210 Thermal Printer connected via USB
- **Node.js**: Version 14 or higher
- **Laravel**: Your existing Click2Serve application
- **Windows**: XAMPP environment

## 🚀 Quick Setup

### 1. Start the Print Service

Navigate to the print service directory and start it:

```bash
cd C:\xampp\htdocs\Click2Serve\print-service
npm start
```

**OR** double-click `start.bat` for easy startup.

### 2. Verify Printer Connection

The service will automatically detect your OC-PT210 printer when connected via USB.

### 3. Test the Integration

Visit these URLs in your browser to test:

- **Health Check**: `http://localhost:4000/health`
- **Test Print**: `http://localhost:4000/test-print` (POST request)
- **Laravel Health**: `http://your-laravel-app/api/print/health`

## 🖨️ How It Works

### Architecture
```
Laravel App → HTTP Request → Node.js Service → ESC/POS Commands → OC-PT210 Printer
```

### Workflow
1. User submits request in kiosk
2. Laravel generates tracking number and queue number
3. Laravel calls Node.js print service via HTTP
4. Print service sends ESC/POS commands to printer
5. Thermal receipt prints instantly

## 📡 API Endpoints

### Node.js Print Service (Port 4000)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/health` | Service health check |
| POST | `/print` | Print receipt |
| POST | `/test-print` | Test printer |

### Laravel Integration (Your App)

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/request` | Submit request and print |
| GET | `/api/print/test` | Test print service |
| GET | `/api/print/health` | Check print service health |

## 📝 Usage Examples

### Submit Request and Print Receipt

```javascript
// Frontend JavaScript
fetch('/api/request', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        full_name: 'John Doe',
        document_type: 'Certificate of Marriage',
        email: 'john@example.com',
        phone: '09123456789',
        purpose: 'Personal use'
    })
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        alert(`Request submitted! Tracking: ${data.tracking_number}, Queue: ${data.queue_number}`);
    }
});
```

### Direct Print Service Call

```bash
curl -X POST http://localhost:4000/print \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Jericho Suarez",
    "service": "Certificate of Marriage",
    "tracking": "CR-20250113-ABC123",
    "queue_no": "05"
  }'
```

## 🔧 Configuration

### Print Service Configuration

The print service runs on `http://localhost:4000` by default. You can change this in `server.js`:

```javascript
const PORT = process.env.PORT || 4000;
```

### Laravel Configuration

The Laravel app calls the print service at `http://localhost:4000`. Update this in `RequestController.php` if needed:

```php
$response = Http::timeout(10)->post('http://localhost:4000/print', $data);
```

## 🛠️ Troubleshooting

### Printer Not Detected
1. Ensure OC-PT210 is connected via USB
2. Check Windows Device Manager for printer
3. Install printer drivers if needed
4. Restart the print service

### Print Service Not Starting
1. Check if port 4000 is available
2. Run `npm install` in print-service directory
3. Check Node.js version: `node --version`

### Laravel Can't Connect to Print Service
1. Ensure print service is running on port 4000
2. Check firewall settings
3. Verify `http://localhost:4000/health` responds

### Print Quality Issues
1. Check printer paper alignment
2. Clean printer head if needed
3. Adjust print density in ESC/POS commands

## 📊 Receipt Format

The printed receipt includes:

```
        ALAMINOS CITY
        CLICK2SERVE
========================
Queue No: 05
Name: Jericho Suarez
Service: Certificate of Marriage
Tracking: CR-20250113-ABC123
Date: 2025-01-13
Time: 14:30:25
========================
    Please keep this slip
    for tracking purposes

        Thank you!
```

## 🔄 Development Mode

For development with auto-restart:

```bash
cd print-service
npm run dev
```

This uses `nodemon` to automatically restart the service when files change.

## 📦 Dependencies

### Print Service
- `express`: Web server
- `body-parser`: Request parsing
- `escpos`: ESC/POS command generation
- `escpos-usb`: USB printer communication

### Laravel
- `guzzlehttp/guzzle`: HTTP client (included with Laravel)

## 🚨 Important Notes

1. **USB Connection**: The OC-PT210 must be connected via USB for the service to work
2. **Port Availability**: Ensure port 4000 is not used by other applications
3. **Windows Permissions**: The service needs permission to access USB devices
4. **Error Handling**: The system gracefully handles print failures - requests are still processed even if printing fails

## 📞 Support

If you encounter issues:

1. Check the console output of the print service
2. Check Laravel logs in `storage/logs/laravel.log`
3. Verify printer connection in Windows Device Manager
4. Test with the `/test-print` endpoint

---

**Happy Printing! 🖨️✨**

