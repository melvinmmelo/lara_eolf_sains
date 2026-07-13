# CLAUDE.md

This file provides guidance to AI coding agents working in this repository.
Claude Code reads it as `CLAUDE.md`; other agents (Codex, Cursor, Gemini CLI, …)
read the same file via the `AGENTS.md` symlink — it is one file, do not fork it.

## Project Overview

EOLF Trading Sales and Inventory System - A Laravel 11 application managing sales, inventory, order slips, delivery receipts, and equipment tracking across multiple branches (EFTO-CAG, EFTO-TAR).

**Tech Stack:**
- Laravel 11 (PHP 8.2+)
- MySQL database
- Vite for asset compilation
- AdminLTE 3 for UI
- Alpine.js, Tailwind CSS
- Spatie packages (laravel-permission, laravel-activitylog, laravel-backup)
- PHPSpreadsheet for Excel exports

## Development Commands

### Initial Setup
```bash
composer install
npm install
cp .env.example .env
php artisan key:generate

# The schema is now baseline-managed. database/schema/mysql-schema.sql captures the
# full structure; `migrate` loads it on an empty DB, then applies the incremental
# migrations on top. So a fresh setup is just:
php artisan migrate          # loads the baseline schema, then runs pending migrations
# (No structural data is included in the baseline. To get production DATA locally,
#  use the /eolf-restore-backup skill, or import the gitignored database/eolf.sql dump.)
# php artisan db:seed        # only for a truly EMPTY schema
```

### Daily Development
```bash
# Start development server
php artisan serve

# Watch and compile assets
npm run dev

# Build assets for production
npm run build

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### Database Operations
```bash
# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Rollback all migrations then re-run them
php artisan migrate:refresh

# Drop all tables and re-run migrations from scratch
php artisan migrate:fresh

# View database info
php artisan db:show --database=mysql
php artisan db:table <table_name> --database=mysql
```

### Testing
```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/ExampleTest.php

# Run tests with coverage
php artisan test --coverage

# Alternative: using phpunit directly
vendor/bin/phpunit
```

### Code Quality
```bash
# Format code using Laravel Pint
vendor/bin/pint

# Format specific files
vendor/bin/pint app/Models
```

## Architecture Overview

### Multi-Branch System
The application supports multiple branches with branch-specific data isolation:
- Branch codes: `EFTO-CAG` (Cagayan), `EFTO-TAR` (Tarlac)
- Branch selection stored in session: `session('branch_code')`
- Most models have a `scopeBranch($query, $branch_code)` scope for filtering by branch
- User must select a branch before accessing main application (redirected to `branch-select` route if not set)

### Core Business Entities

**Order Flow:**
1. **Inbound** - Customer orders with products (JSON field), payment tracking, status lifecycle
2. **OrderSlip** - Groups multiple inbound orders for delivery organization
3. **LoadingTicket** - Vehicle loading documentation
4. **DeliveryReceipt** - Proof of delivery linked to inbound orders

**Inventory Management:**
- **Product** - Composite key from ProductType + ProductVariant (e.g., `PROD_TYPE_VARIANT`)
- **ItemMasterData** - Branch-specific stock levels (stocks, reserved, available_stocks)
- **Inbound.products** - JSON field containing `[{ptype_code, quantity, price, order}]`
- **BadOrder** / **InventoryBadOrder** - Damaged/returned product tracking
- Stock reconciliation: controller-only feature (`StockReconciliationController`) — adjusts `ItemMasterData` directly, no dedicated model

**Bad Orders (multiple overlapping systems — pick the right one):**
- **Sales-side bad orders (CANONICAL):** `NewBadOrderController` + `NewBadOrder` (header) → `NewTempBadOrder` (line items, `hasMany`). Routes named `newbo.*` under `/bad-orders`, `/bad-order/create|edit`. Pricing via the `BadOrderPrice` lookup (by ProductType + price level).
- **Helpers for that flow (not standalone features):** `addbadorderController` (AJAX item/product fetch: `/fetch-items`, `/get-products/...`) and `TempBadOrderController` (session scratch save/clear).
- **Legacy:** `BadOrderController` + `BadOrder` model — kept only for utility lookups (e.g. `/lastBadOrderOfCustomer/...`). Don't build new sales-side BO features here.
- **Inventory-side bad orders (SEPARATE system):** `InventoryBadOrderController` + `InventoryBadOrder` under `/inventory/bad-orders` — deducts `ItemMasterData` stock directly and supports a `rollback` action. Unrelated to the sales-side BO above.

**Customer & Pricing:**
- **Customers** - Has multiple stores, linked to equipment, inbounds, bad orders
- **StoreInfo** - Customer store locations
- **pricelevels** - Different pricing tiers for customers
- **prices** - Price configurations per product

**Equipment & Logistics:**
- **Equipment** / **EquipmentStore** - Track customer equipment (freezers, coolers)
- **EquipmentHistory** - Audit trail of equipment assignment / pull-out events (serial, customer, dates, users, reason)
- **Vehicles** / **Drivers** - Delivery logistics
- **PullOutForm** - Equipment removal/replacement tracking

**Materials (consumables, separate from products):**
- **MaterialsInventory** - Branch-scoped stock for packaging/supplies; supports bulk-receive
- **MaterialItemsWithdrawals** - Withdrawal records, branch-scoped, with a zero-quantity guard at the controller layer
- Routes under `/materials-inventory` and `/material-withdrawals`

**Other entities:**
- **DeliveryPurchaseReceipt** - Incoming stock receipts from suppliers (NOT customer orders); `/dpr-*` routes, `products` array, supports per-product hold/rollback
- **Delivery** - Delivery-personnel registry (branch-scoped, `scopeActive`); distinct from `DeliveryReceipt` (the proof-of-delivery document)
- **CompanyDetails** - Single-row company info (seeded `name='EOLF'`)
- **Expenses** - Admin-only, branch-scoped expense ledger; routes named `expenses.*` under `/expenses` (CRUD + `/expenses/export` Excel via PHPSpreadsheet). Records BIR petty-cash fields alongside the basics: `taxpayer_type` (VAT/Non-VAT), `tin`, `payee_address`, `petty_cash_no`. Picklists are model constants (`CATEGORIES`, `PAYMENT_METHODS`, `TAXPAYER_TYPES`). Schema is added via migrations (`*_add_fields_to_expenses_table`, `*_add_tax_fields_to_expenses_table`), not `eolf.sql`.

**Customer Lifecycle:**
- `customers.status` - `active` vs stop-selling (any non-`active` value)
- Stop-selling list at `/customers/stop-selling` (admin-only) with a reactivate action
- `Customers::scopeActive()` filters by `status='active'`

**Dashboard:**
- `DashboardController` renders sales-by-product-type, ordered by the configured product type sequence (not alphabetical)

### Key Code Patterns

**Product Code Generation:**
Products use composite codes: `{product_type_code}_{product_variant_code}`
- Handled automatically in `Product::boot()` creating event

**Order Code Generation:**
Inbound orders have formatted codes based on branch:
- Pattern: `{year}-{branch_prefix}{order_no}`
- EFTO-CAG: `C` prefix (e.g., `25-C00001`)
- EFTO-TAR: `T` prefix (e.g., `25-T00001`)
- See `Inbound::getCodeAttribute()` accessor

**Order Statuses:**
Common inbound statuses: `Completed`, `Paid`, `Cancelled`, `Deleted`, `Free`
- Orders for loading: status=`Completed` AND ticket_sequence_no=0
- Active orders: status IN (`Completed`, `Paid`, `Free`)

**Financial Calculations:**
Inbound order totals calculated in accessors:
- `getTotalAmountAttribute()` - Products total
- `getGrandTotalAttribute()` - Total + service fee (1000 if `is_with_sf`)
- `getTotalBalanceAttribute()` - Grand total - bad order amount - discount - delivered amount

**JSON Product Storage:**
Products stored as JSON in `inbounds.products`:
```php
[
  {
    "ptype_code": "ICE",
    "quantity": 10,
    "price": 25.00,
    "order": 1
  }
]
```

Helper functions in `app/Helpers/helpers.php`:
- `getTotalOfProducts($products)` - Calculate total amount
- `getSummaryOfProducts($products)` - Group by product type

**Order-edit cart (`NewInboundProduct` / `new_inbound_products` table):**
The order **edit** screen does not mutate `inbounds.products` JSON in place while editing — it stages line items in the relational `new_inbound_products` table (model `NewInboundProduct`), scoped by `inbound_id` + `branch_code`, soft-deleted via a `status` column (active rows are `whereNull('status')`), ordered by a string `order` column cast to UNSIGNED. See `InboundController` for re-pricing on price-level change, delete-item, and delete-all. The canonical `inbounds.products` JSON remains the source of truth for saved orders.

### Permissions & Authorization
- Uses Spatie Laravel Permission package
- Gate defined for admin role: `Gate::define('admin', ...)`
- Middleware: `->middleware('can:admin')` restricts routes
- Activity logging with Spatie ActivityLog on model changes

### Global Middleware & Activity Tracking
Registered in `bootstrap/app.php` on the `web` group — these run on **every** web request:
- **`CheckSessionBranch`** - enforces branch selection; redirects to `branch-select` when `session('branch_code')` is unset.
- **`LogUserActivity`** - on `terminate()` (after the response is sent): writes a Spatie activity entry (log name `page-visit`: method, url, route, IP, UA, branch, status) and throttle-updates `users.last_active_at` via `saveQuietly()` (60s cache cooldown). Skips HEAD/OPTIONS and asset/health paths.
- **Model audit trail:** changes are logged via the `App\Models\Concerns\AutoLogsChanges` trait (Spatie `LogsActivity`, dirty attrs only), applied broadly — Inbound, Customers, Product, ProductType, ProductVariant, OrderSlip, LoadingTicket, DeliveryReceipt, Equipment, Vehicles, Drivers, BadOrder, MaterialItemsWithdrawals, etc.
- **Admin viewer:** `ActivityLogController` at `/activity-log` (admin-only) — filter by user / log name / date / search, paginated; filters are preserved across pagination.

### Views & Frontend
- Layout: AdminLTE 3 (`resources/views/layouts/app.blade.php`)
- Shared view data from `AppServiceProvider`:
  - `$gbranches` - All branches
  - `$branch_name` - Current branch name from session
- Reports in `resources/views/report/`
- AJAX routes in `routes/ajaxreq.php`

### File Organization
- Controllers in `app/Http/Controllers/`
- Models in `app/Models/`
- Routes: `routes/web.php` (main), `routes/ajaxreq.php` (AJAX), `routes/auth.php` (authentication)
- Helpers: `app/Helpers/helpers.php` (autoloaded via composer.json)

## Report Generation

Reports use `ReportGeneratorController`:
- Sales reports with Excel export (PHPSpreadsheet)
- Order slips, delivery receipts, loading tickets
- Customer/product summaries
- Stock availability reports

Example routes:
- `/reports/sales` - Sales report (admin only)
- `/reports/sales-by-customer` - Customer sales breakdown
- `/order-slip/{code}` - Individual order slip

## Important Notes

### Database Considerations
- **Schema is now baseline-managed (as of 2026-06).** `database/schema/mysql-schema.sql` is the committed baseline (full structure + the `migrations` table contents). `php artisan migrate` on an empty DB loads it, then runs the incremental migrations in `database/migrations/` on top, so `migrate`/`migrate:fresh` reproduce the schema. `database/eolf.sql` (~67MB, **gitignored**) is now only a source of production DATA, not the authoritative structure. Route all future DDL through migrations, not ad-hoc SQL. Regenerate the baseline only from a known-good DB via the MySQL client (the app image ships the MariaDB client; `php artisan schema:dump` fails on it — its mysqldump rejects MySQL-only flags).
- **Local MySQL client TLS:** the app container's MariaDB client would reject the self-signed cert of the shared MySQL 8 server; `docker/mysql/client-ssl-off.cnf` (mounted via docker-compose) disables client TLS so `migrate` can load the baseline. PDO is unaffected.
- **Money columns are DECIMAL(15,2)** on the sales path (inbounds, order_slips, prices, new_inbound_products, materials_inventories, bad_orders, new_temp_bad_orders) — not float. Eloquent returns them as strings; existing accessors coerce fine in arithmetic.
- **Enforced FKs exist only on relational/config tables** (e.g. `prices`→pricelevels/product_types, `customers.pricelevel_id`→pricelevels with ON DELETE SET NULL, `inbound_lines`→inbounds CASCADE). Document/snapshot tables (inbounds.*_id, etc.) deliberately have NO FKs — their refs are immutable point-in-time snapshots.
- **`inbound_lines`** is a relational projection of `inbounds.products` JSON (one row per line), kept in sync by the Inbound `saved` observer and rebuildable via `php artisan inbound:project-lines`. The JSON blob remains the source of truth; use inbound_lines for SQL reporting. NOTE: `products` is sometimes a JSON object (`{"0":{…}}`) rather than an array — decode with PHP/foreach, not `JSON_TABLE('$[*]')`, which silently skips object-encoded rows.
- **Maintenance commands:** `db:audit-money` (pre/post money-conversion drift), `customers:normalize-pricelevel`, `db:audit-stub-tables`, `activitylog:archive [--prune]` (no-data-loss retention), `inbound:project-lines [--fresh|--verify]`.
- JSON columns used for flexible product data in orders
- Model accessors heavily used for computed values (always call in correct order)
- Scopes used extensively for branch filtering and status queries

### Code Modifications
- When editing inbound/order calculations, ensure accessor dependencies are maintained
- Product codes are auto-generated; don't manually set `code` field
- Branch filtering must be applied to queries touching multi-branch data
- Test financial calculations thoroughly when modifying pricing logic

### Environment Specific
- Production forces HTTPS: `AppServiceProvider::boot()`
- Schema default string length: 191 (for MySQL compatibility)
- **Local DB:** uses the shared MySQL stack (`shared_mysql` container on `shared_db_net`). `.env` should set `DB_HOST=shared_mysql`, not `127.0.0.1`. phpMyAdmin at http://localhost/phpmyadmin/

### Gotchas
- **Model class names break Laravel conventions:** `Customers`, `pricelevels`, `prices` (plural / lowercase). Don't autocomplete-guess `Customer::` or `PriceLevel::`.
- **`Inbound::getNextOrderNo` counts rows**, not `MAX(order_no)+1`. Safe today because orders are soft-deleted via `status='Deleted'`, never hard-deleted — if that ever changes, this collides.
- **Two "active orders" scopes on Inbound:** `scopeActiveOrders` (Completed, excludes `is_foc`) vs `scopeActiveOrdersv2` (Completed/Paid/Free). Pick deliberately.
- **Branch filtering is not automatic** — every query touching multi-branch data must call `->branch(session('branch_code'))` explicitly.
- **Bad orders have two unrelated systems** — sales-side (`NewBadOrderController`, canonical) vs inventory-side (`InventoryBadOrderController`). Several legacy BO controllers/models still exist; see *Bad Orders* under Core Business Entities before touching them.
- **`app/Http/Controllers/php_errors.log`** is a committed stray PHP error-log file, not code — ignore it (don't read it as a controller or `require` it).

### Common Tasks

**Adding a new product:**
```php
Product::create([
    'product_type_code' => 'ICE',
    'product_variant_code' => 'TUBE',
    // code will be auto-generated as 'ICE_TUBE'
]);
```

**Filtering by current branch:**
```php
Customers::branch(session('branch_code'))->active()->get();
Inbound::branch(session('branch_code'))->completed()->get();
```

**Creating an order:**
```php
$inbound = Inbound::create([
    'branch_code' => session('branch_code'),
    'order_no' => Inbound::getNextOrderNo(session('branch_code')),
    'customer_id' => $customerId,
    'products' => json_encode($productsArray),
    'status' => 'Completed',
    // ... other fields
]);
// Access computed code: $inbound->code
```
