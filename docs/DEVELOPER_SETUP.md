# Transfer and Setup Guide

This guide is for a developer cloning the public Git repository. It deliberately does not transfer local credentials, uploaded files, logs, or dependency folders.

## Before publishing

Check the repository before changing its visibility:

```powershell
git status --short
git ls-files | Select-String '\.env$|storage/logs|node_modules|vendor|phpinfo|\.sql$'
```

The following must remain local and uncommitted:

- `.env` and all API keys
- `vendor/`, `node_modules/`, and `public/storage`
- `storage/logs/` and generated framework cache
- Real uploaded documents, phone numbers, passwords, and production database exports

Do not add a production or development database dump to the public repository. Create local data with migrations and seeders instead.

## New developer setup

1. Install PHP 8.2+, Composer 2, Node.js 20 LTS+, and MySQL/MariaDB.
2. Start MySQL from XAMPP or your local database service.
3. Clone the repository and enter it:

```powershell
git clone https://github.com/Maur1k/Capstone2.git
Set-Location .\Capstone2
```

4. Install dependencies and create local configuration:

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
npm install
npm run build
```

5. Create and seed the development database:

```powershell
& 'C:\xampp\mysql\bin\mysql.exe' -u root -e "CREATE DATABASE IF NOT EXISTS click2serve CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate --seed
```

6. Finish Laravel setup and run the app:

```powershell
php artisan storage:link
php artisan optimize:clear
php artisan serve --host=127.0.0.1 --port=8000
```

Open <http://127.0.0.1:8000>.

When `php` is not recognized, replace `php` with `& 'C:\xampp\php\php.exe'`.

## Optional integrations

Printing, chatbot requests, and SMS require separate services or credentials. They are not needed for the basic Laravel app. Configure them only in `.env`, never in committed files. See the integration table in [README.md](../README.md).

## Troubleshooting

**Database connection error:** Start MySQL, confirm `DB_*` values in `.env`, and verify that the `click2serve` database exists.

**Missing class error:** Run `composer dump-autoload` and `php artisan optimize:clear`.

**Missing assets:** Run `npm install` followed by `npm run build`.

**Storage error:** Run `php artisan storage:link`. If the link already exists, this step is complete.

**Port already in use:** Start Laravel on another port, for example `php artisan serve --port=8001`, and update `APP_URL` if needed.

**Feature tests fail during setup:** Use an isolated test database and run the current migrations. Never point tests at the development database.
