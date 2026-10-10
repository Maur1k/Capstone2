# Kiosk Network Setup

This guide explains how to expose Click2Serve to a kiosk device on a private local network. The examples use placeholders because a computer's IP address changes between networks.

## Before you begin

- Run the application locally and confirm that it works at `http://127.0.0.1:8000`.
- Connect the kiosk device and host computer to the same trusted private network.
- Do not expose the development server or XAMPP to the public internet.
- Use a fixed DHCP reservation or update the host IP when the network changes.

## Find the host IP

On Windows, run:

```powershell
ipconfig
```

Use the host's IPv4 address, for example `192.168.1.50`. The kiosk URL for the Laravel development server is:

```text
http://192.168.1.50:8000
```

Start Laravel so other devices can connect:

```powershell
php artisan serve --host=0.0.0.0 --port=8000
```

If PHP is only installed through XAMPP:

```powershell
& 'C:\xampp\php\php.exe' artisan serve --host=0.0.0.0 --port=8000
```

## Windows Firewall

Allow inbound TCP traffic on port `8000` only on the trusted Private network profile. Do not enable a broad Public-network rule. A network administrator should create the firewall rule according to local policy.

## Test from the kiosk

1. Connect the kiosk to the same private Wi-Fi or wired network.
2. Open `http://HOST_IP:8000` in the kiosk browser.
3. Test the public kiosk, website, tracker, and wayfinding pages.
4. Confirm that the browser cannot access administrative pages without authentication.

## Browser kiosk mode

Use the kiosk browser's documented full-screen or locked-homepage mode. Set its homepage to the host URL and restrict navigation to the host address. Do not copy passwords, API keys, or a device-specific kiosk key into this guide.

## Apache/XAMPP deployment

For a longer-lived installation, configure Apache to serve the Laravel `public` directory and use a private DNS name or DHCP reservation. Do not serve the repository root. Keep directory listing disabled and keep `.env` outside the web root.

## Troubleshooting

- If the page is unreachable, verify the host IP, firewall profile, and that Laravel is listening on `0.0.0.0`.
- If the page returns a database error, start MySQL and verify `.env`.
- If styles are missing, run `npm run build` and clear Laravel caches.
- If the IP changes, update the kiosk homepage URL.
- Stop the development server with `Ctrl+C` when the kiosk is offline.
