# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

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
php artisan migrate
php artisan db:seed
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

# Refresh database (drops all tables and re-runs migrations)
php artisan migrate:refresh

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
- **StockReconciliation** - Inventory adjustment tracking

**Customer & Pricing:**
- **Customers** - Has multiple stores, linked to equipment, inbounds, bad orders
- **StoreInfo** - Customer store locations
- **pricelevels** - Different pricing tiers for customers
- **prices** - Price configurations per product

**Equipment & Logistics:**
- **Equipment** / **EquipmentStore** - Track customer equipment (freezers, coolers)
- **Vehicles** / **Drivers** - Delivery logistics
- **PullOutForm** - Equipment removal/replacement tracking

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

### Permissions & Authorization
- Uses Spatie Laravel Permission package
- Gate defined for admin role: `Gate::define('admin', ...)`
- Middleware: `->middleware('can:admin')` restricts routes
- Activity logging with Spatie ActivityLog on model changes

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
