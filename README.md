# Click2Serve

Click2Serve is a Laravel 12 municipal service kiosk and web platform. It supports public service requests, appointments, request tracking, officer workflows, administration, notifications, and kiosk wayfinding.

## Requirements

- PHP 8.2 or newer with the required extensions enabled
- Composer 2
- Node.js 20 LTS or newer and npm
- MySQL or MariaDB
- XAMPP is recommended on Windows

The application repository is this directory. The optional print and chatbot services live beside it in the original workspace and are not required to run the Laravel application.

## Quick Start on Windows

From the cloned repository directory:

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
npm install
npm run build
```

Start MySQL from XAMPP, then create the development database and run the clean seeders:

```powershell
& 'C:\xampp\mysql\bin\mysql.exe' -u root -e "CREATE DATABASE IF NOT EXISTS click2serve CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate --seed
```

Update `.env` if your database credentials differ. Do not commit `.env` or any API keys.

Set `SEED_ADMIN_EMAIL` and `SEED_ADMIN_PASSWORD` in `.env` before seeding. The seeder creates the local administrator with those values and prints the account email, not the password.

```powershell
php artisan storage:link
php artisan optimize:clear
php artisan serve --host=127.0.0.1 --port=8000
```

Open <http://127.0.0.1:8000>.

If PHP is supplied by XAMPP but is not on `PATH`, use the full executable path:

```powershell
& 'C:\xampp\php\php.exe' artisan serve --host=127.0.0.1 --port=8000
```

## Database

The repository does not include a database dump because production-like dumps can contain personal data. Use migrations and seeders for a clean local database. Do not run `migrate:fresh` against a database containing data you need.

## Frontend

Blade templates provide the application UI. Vite compiles the shared assets:

```powershell
npm run dev
```

Use `npm run build` for a production asset build.

## Optional companion services

The Laravel app can integrate with these services when they are available:

| Service | Default address | Purpose |
| --- | --- | --- |
| Print service | `http://127.0.0.1:4000` | Thermal receipt printing |
| Chatbot service | `http://127.0.0.1:5000` | Chatbot responses and knowledge-base operations |
| Semaphore | External HTTPS API | SMS notifications |

The print and chatbot folders are not part of this repository. Obtain them from the project owner, install their dependencies, and configure their URLs and feature flags in `.env`. Keep those integrations disabled for a basic local run unless you have their credentials and hardware.

SMS is disabled unless `SEMAPHORE_ENABLED=true` and a valid API key are configured. Never use real SMS credentials in a public issue, pull request, or sample file.

## Useful URLs

- Public kiosk: <http://127.0.0.1:8000/>
- Website: <http://127.0.0.1:8000/website>
- Admin login: <http://127.0.0.1:8000/admin/login>
- Officer login: <http://127.0.0.1:8000/officer/login>
- Request tracker: <http://127.0.0.1:8000/track>
- Wayfinding kiosk: <http://127.0.0.1:8000/3dmap>

Use the credentials supplied privately by the project owner. Do not publish default or real passwords in this repository.

## Tests and checks

```powershell
php artisan route:list
php artisan test
npm run build
```

Some feature tests require a complete test database schema. If tests fail during setup, check that migrations are current and that the test database is isolated from development data.

## Repository hygiene

Before opening the repository publicly, confirm that `.env`, API keys, database dumps containing personal data, logs, uploaded files, and dependency directories are not committed.

More information:

- [Transfer and setup guide](TRANSFER_GUIDE.md)
- [Kiosk network guide](kiok-setup-guide.md)
- [End-user and staff manual](Click2Serve%20-%20User%20Manual.md)
