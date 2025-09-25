# Kiosk Setup Guide for Mobile Access

## Your Network Information
- **Your Computer IP**: 192.168.1.22
- **Kiosk URL**: http://192.168.1.22/Click2Serve/Click2Serve/public
- **Kiosk Key**: VCWJNWCRFA7MXAPTANQOGQJHASQLTVMT

## Step 1: Configure XAMPP for Network Access

### 1.1 Edit Apache Configuration
1. Open XAMPP Control Panel
2. Click "Config" next to Apache
3. Select "Apache (httpd.conf)"
4. Find the line `Listen 80`
5. Change it to `Listen 0.0.0.0:80` (allows connections from any IP)
6. Save the file
7. Restart Apache in XAMPP Control Panel

### 1.2 Configure Windows Firewall
1. Open Windows Defender Firewall
2. Click "Allow an app or feature through Windows Defender Firewall"
3. Click "Change settings" then "Allow another app..."
4. Browse to `C:\xampp\apache\bin\httpd.exe`
5. Add it and check both "Private" and "Public" networks
6. Click OK

## Step 2: Configure Surefox Lite for Kiosk Mode

### 2.1 Download and Install Surefox Lite
1. Download Surefox Lite from: https://www.surefox.com/
2. Install it on your kiosk computer

### 2.2 Configure Kiosk Mode
1. Open Surefox Lite
2. Go to Settings > Kiosk Mode
3. Enable "Kiosk Mode"
4. Set "Homepage URL" to: `http://192.168.1.22/Click2Serve/Click2Serve/public`
5. Enable "Lock to Homepage" (prevents browsing other sites)
6. Enable "Disable Address Bar"
7. Enable "Disable Navigation Buttons"
8. Enable "Disable Right-Click Menu"
9. Enable "Disable Keyboard Shortcuts"
10. Set "Full Screen Mode" to ON
11. Save settings

### 2.3 Additional Security Settings
1. Go to Settings > Security
2. Enable "Block Pop-ups"
3. Enable "Disable Downloads"
4. Enable "Disable File Access"
5. Set "Allowed Domains" to only: `192.168.1.22`

## Step 3: Access from Mobile Devices

### 3.1 From iPhone/iPad (iOS)
1. Connect to the same WiFi network as your kiosk computer
2. Open Safari browser
3. Navigate to: `http://192.168.1.22/Click2Serve/Click2Serve/public`
4. The kiosk interface should load

### 3.2 From Android Tablet/Phone
1. Connect to the same WiFi network as your kiosk computer
2. Open Chrome browser
3. Navigate to: `http://192.168.1.22/Click2Serve/Click2Serve/public`
4. The kiosk interface should load

### 3.3 For Better Mobile Experience
1. Add to Home Screen (iOS): Tap Share button > Add to Home Screen
2. Add to Home Screen (Android): Tap menu > Add to Home Screen

## Step 4: Test the Setup

### 4.1 Test Network Access
1. From your mobile device, try accessing: `http://192.168.1.22/Click2Serve/Click2Serve/public`
2. Verify the kiosk interface loads properly
3. Test all functionality (tracking, services, etc.)

### 4.2 Test Kiosk Lock
1. On the kiosk computer, open Surefox Lite
2. Try to navigate to other websites (should be blocked)
3. Try using keyboard shortcuts (should be disabled)
4. Try right-clicking (should be disabled)

## Troubleshooting

### If Mobile Devices Can't Access:
1. Check if both devices are on the same WiFi network
2. Verify XAMPP is running and accessible
3. Check Windows Firewall settings
4. Try accessing from another computer first

### If Surefox Lite Doesn't Lock Properly:
1. Restart Surefox Lite after configuration changes
2. Check if all security settings are enabled
3. Verify the homepage URL is correct

## Security Notes

- The kiosk is only accessible from devices on the same network
- Surefox Lite prevents users from browsing other websites
- All navigation controls are disabled in kiosk mode
- The setup uses your local network IP (192.168.1.22)

## Maintenance

- If your computer's IP changes, update the URL in Surefox Lite settings
- Restart XAMPP if the service becomes unavailable
- Monitor Surefox Lite logs for any issues
