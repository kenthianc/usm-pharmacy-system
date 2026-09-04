# Setup Guide

Clone-to-running instructions for the USM Hospital Pharmacy Management System. Assumes a fresh machine — no prior knowledge of the project needed.

---

## Prerequisites

| Requirement | Minimum version | Notes |
|---|---|---|
| PHP | 8.3 | With extensions: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo` |
| Composer | Latest stable | [getcomposer.org](https://getcomposer.org/) |
| Node.js & npm | Node 18+ | Required for Vite/Tailwind asset compilation |
| MySQL | 8.0+ | Running instance; create the database before migrating |
| Git | Any recent | For cloning |

> **Windows/Laragon users:** PHP 8.3, Composer, Node, and MySQL are bundled with Laragon. No separate installs needed.

---

## Step-by-Step Installation

### 1. Clone the repository
```bash
git clone <repository-url>
cd usm-pharmacy-system
```

### 2. Install PHP dependencies
```bash
composer install
```

### 3. Set up the environment file
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure your database

Open `.env` and fill in your MySQL credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=usm_pharmacy_system
DB_USERNAME=root
DB_PASSWORD=          # leave blank for Laragon default
```

Create the database in MySQL first:
```sql
CREATE DATABASE usm_pharmacy_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Run migrations and seed the database

```bash
php artisan migrate --seed
```

This runs all 13 migrations in dependency order and seeds:
- **RoleSeeder** — creates the 6 application roles
- **DatabaseSeeder** — creates one demo user per role + assigns roles via Spatie
- **PatientSeeder** — creates sample patient records
- **MedicineAndBatchSeeder** — creates sample medicines with stock batches (expiry dates spread across near and far future)

### 6. Build frontend assets

```bash
npm install
npm run build
```

For active development, use the dev server instead (hot-reload):
```bash
npm run dev
```

### 7. Start the application

```bash
php artisan serve
```

Visit `http://localhost:8000`.

**Alternative — one command for dev:** starts the web server, Vite dev server, and the queue worker together:
```bash
composer run dev
```

---

## Seeded Demo Accounts

All passwords are `password`.

| Role | Email | Can do |
|---|---|---|
| `admin` | `admin@usm.edu.ph` | All routes |
| `pharmacist` | `pharmacist@usm.edu.ph` | POS queue, dispense, OTC sales |
| `nurse` | `nurse@usm.edu.ph` | Create & route prescriptions |
| `medical_secretary` | `medsec@usm.edu.ph` | Create & route prescriptions |
| `stock_manager` | `stockmanager@usm.edu.ph` | Inventory management (module in progress) |
| `patient` | `patient@usm.edu.ph` | Patient portal (not yet built) |

---

## Common Issues & Fixes

**"Vite manifest not found" error on page load**
Assets haven't been compiled. Run `npm run build`, or start `npm run dev` in a separate terminal.

**Database connection refused**
Ensure MySQL is running and the credentials in `.env` match your local setup. On Laragon, the default user is `root` with a blank password.

**Foreign key constraint fails during migration**
Run a fresh migration instead of rolling back manually:
```bash
php artisan migrate:fresh --seed
```

**`storage/` or `bootstrap/cache/` permission errors (Linux/macOS only)**
```bash
chmod -R 775 storage bootstrap/cache
```
On Windows with Laragon this is not normally needed.

**`Class "Spatie\Permission\Models\Role" not found` after cloning**
You likely skipped `composer install`. Run it and then retry.

---

## Running Tests

```bash
php artisan test
# or directly:
vendor/bin/pest
```

To run a single file or filter by name:
```bash
php artisan test tests/Feature/PrescriptionModuleTest.php
vendor/bin/pest --filter="nurse can create a prescription"
```

