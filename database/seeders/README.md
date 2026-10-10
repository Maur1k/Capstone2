# Database Seeders

This directory contains all database seeders for the Click2Serve application.

## Quick Start

```bash
# Run all seeders (fresh database)
php artisan migrate:fresh --seed

# Run all seeders (existing database)
php artisan db:seed

# Run a specific seeder
php artisan db:seed --class=SeederName
```

## Available Seeders

### Core Seeders (Required)

| Seeder | Description | Run Command |
|--------|-------------|-------------|
| `AdminSeeder` | Admin and Officer accounts | `php artisan db:seed --class=AdminSeeder` |
| `ServiceSeeder` | Services and Service Types | `php artisan db:seed --class=ServiceSeeder` |
| `FloorPoiSeeder` | Floor plans and POIs | `php artisan db:seed --class=FloorPoiSeeder` |
| `FeatureToggleSeeder` | Feature toggles | `php artisan db:seed --class=FeatureToggleSeeder` |
| `QueueSettingsSeeder` | Queue display settings | `php artisan db:seed --class=QueueSettingsSeeder` |

### Content Seeders

| Seeder | Description | Run Command |
|--------|-------------|-------------|
| `MunicipalDataSeeder` | Municipal info, officials, tourist spots | `php artisan db:seed --class=MunicipalDataSeeder` |
| `WebsiteContentSeeder` | About, Blog posts | `php artisan db:seed --class=WebsiteContentSeeder` |

### Test Data Seeders

| Seeder | Description | Run Command |
|--------|-------------|-------------|
| `TestQueueSeeder` | Sample queue data for testing | `php artisan db:seed --class=TestQueueSeeder` |

## Local Login Credentials

Before seeding, set these values in `.env`:

```env
SEED_ADMIN_EMAIL=admin@example.test
SEED_ADMIN_PASSWORD=change-me-local
```

`AdminSeeder` creates the super-admin account from those values. Do not commit `.env`, production passwords, or database exports.

## Queue Settings

The `QueueSettingsSeeder` configures:

- Queue number feature toggle per department
- Office display names (e.g., "City Civil Registry Office")
- Queue number prefixes (e.g., "CRO" for CRO #1, CRO #2, etc.)
- Column names (e.g., "Queueing", "To Submit", "Processed")
- Window configurations with names

## Test Queue Data

The `TestQueueSeeder` creates sample queue items for testing:

### Civil Registry Office (CRO)
- **Queueing Column**: Queue #101, #102, #103 (status: inquiry)
- **To Submit Column**: Queue #104, #105, #106 (status: checklist_given, accepted, incomplete_requirements)
- **Processed Column**: Queue #107, #108 (status: registered, positive_result)

### BPLO
- **Queueing Column**: Queue #201, #202 (status: inquiry)
- **To Submit Column**: Queue #203 (status: incomplete_requirements)
- **Processed Column**: Queue #204 (status: registered)

## Transferring Data Between PCs

To transfer non-sensitive structure and sample content to another PC:

1. **Avoid exporting production data to a public repository.** For a clean developer database, run:
   ```bash
   php artisan migrate:fresh --seed
   ```

2. **For test data:**
   ```bash
   php artisan db:seed --class=TestQueueSeeder
   ```

## Seeder Order

When running `php artisan db:seed`, seeders run in this order:

1. `AdminSeeder` - Creates admin/officer accounts
2. `ServiceSeeder` - Creates services and service types
3. `FloorPoiSeeder` - Creates floor plans and POIs
4. `FeatureToggleSeeder` - Sets up feature toggles
5. `QueueSettingsSeeder` - Configures queue display
6. `MunicipalDataSeeder` - Municipal information
7. `WebsiteContentSeeder` - Website content
8. Certificate seeders (Marriage, CENOMAR, Death)


