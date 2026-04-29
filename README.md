# EOLF Trading Sales and Inventory System

Laravel 11 application managing sales, inventory, order slips, delivery receipts, and equipment tracking across multiple branches (EFTO-CAG, EFTO-TAR).

## Stack

- Laravel 11 (PHP 8.2+), MySQL
- Vite, AdminLTE 3, Alpine.js, Tailwind CSS
- Spatie: laravel-permission, laravel-activitylog, laravel-backup
- PHPSpreadsheet for Excel exports

## Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
```

Local DB uses the shared MySQL stack: set `DB_HOST=shared_mysql` in `.env` (not `127.0.0.1`). phpMyAdmin: http://localhost/phpmyadmin/

## Develop

```bash
php artisan serve     # app server
npm run dev           # vite asset watcher
npm run build         # production assets
vendor/bin/pint       # format PHP
php artisan test      # run tests
```

## Architecture

See [CLAUDE.md](CLAUDE.md) for the full architecture overview, business entities, branch isolation model, and gotchas.

## Recent updates

### April 2026
- Today's-orders printout now includes spoons and signature columns
- Order dashboard sales-by-product-type sorted by configured product-type sequence
- Material withdrawals: structured pickers, zero-quantity guard, list scoped to current branch
- Stop-selling customers page with reactivate action (admin-only)
- Order edit: re-price cart on price-level change, delete-all-items action, block save with no items
- Dashboard: sales volume by product type and by flavor

### May 2025
- Stock reconciliation
- Inventory bad order
- Monthly sales graph (based on Completed and Paid orders)
- Duplicate-order validation
- Summary and totals columns on orders index
