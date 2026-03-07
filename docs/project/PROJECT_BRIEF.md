# PROJECT_BRIEF.md - EOLF Trading Sales & Inventory System

**Created:** 2026-03-04 (Tuesday, 2:40 PM)  
**Project Name:** eolf  
**Client:** EOLF Trading (Melvs personal project)  
**Repository:** https://github.com/melvinmmelo/lara_eolf_sains  
**Local Path:** `~/logia-innovations/projects/eolf/codebase/`

---

## Project Overview

**EOLF Trading Sales and Inventory System** — A Laravel 11 application managing sales, inventory, order slips, delivery receipts, and equipment tracking across multiple branches (EFTO-CAG, EFTO-TAR).

**Purpose:**
- Multi-branch sales management
- Inventory tracking with stock reconciliation
- Order processing with delivery management
- Customer equipment tracking
- Financial reporting and analytics

---

## Tech Stack

### Backend
- **Framework:** Laravel 11 (PHP 8.2+)
- **Database:** MySQL
- **Packages:**
  - Spatie Laravel Permission (role-based access)
  - Spatie Laravel Activity Log (audit trail)
  - Spatie Laravel Backup
  - PHPSpreadsheet (Excel exports)
  - Laravel Breeze (authentication)

### Frontend
- **UI Framework:** AdminLTE 3
- **CSS:** Tailwind CSS
- **JavaScript:** Alpine.js
- **Build Tool:** Vite (asset compilation)

### Development Tools
- Laravel Pint (code formatting)
- PHPUnit (testing)
- Laravel Tinker (REPL)

---

## Architecture

### Multi-Branch System
- **Branch Codes:** `EFTO-CAG` (Cagayan), `EFTO-TAR` (Tarlac)
- **Branch Selection:** Stored in `session('branch_code')`
- **Data Isolation:** Most models have `scopeBranch($query, $branch_code)` for filtering
- **Access Control:** Users redirected to `branch-select` route if no branch selected

### Core Business Entities

#### Order Flow
1. **Inbound** — Customer orders with products (JSON field), payment tracking, status lifecycle
2. **OrderSlip** — Groups multiple inbound orders for delivery organization
3. **LoadingTicket** — Vehicle loading documentation
4. **DeliveryReceipt** — Proof of delivery linked to inbound orders

#### Inventory Management
- **Product** — Composite key from ProductType + ProductVariant (e.g., `PROD_TYPE_VARIANT`)
- **ItemMasterData** — Branch-specific stock levels (stocks, reserved, available_stocks)
- **Inbound.products** — JSON field containing `[{ptype_code, quantity, price, order}]`
- **BadOrder / InventoryBadOrder** — Damaged/returned product tracking
- **StockReconciliation** — Inventory adjustment tracking

#### Customer & Pricing
- **Customers** — Has multiple stores, linked to equipment, inbounds, bad orders
- **StoreInfo** — Customer store locations
- **pricelevels** — Different pricing tiers for customers
- **prices** — Price configurations per product

#### Equipment & Logistics
- **Equipment / EquipmentStore** — Track customer equipment (freezers, coolers)
- **Vehicles / Drivers** — Delivery logistics
- **PullOutForm** — Equipment removal/replacement tracking

### Key Code Patterns

**Product Code Generation:**
```php
// Auto-generated: {product_type_code}_{product_variant_code}
// Handled in Product::boot() creating event
```

**Order Code Generation:**
```php
// Pattern: {year}-{branch_prefix}{order_no}
// EFTO-CAG: C prefix (e.g., 25-C00001)
// EFTO-TAR: T prefix (e.g., 25-T00001)
// See Inbound::getCodeAttribute() accessor
```

**Order Statuses:**
- Common: `Completed`, `Paid`, `Cancelled`, `Deleted`, `Free`
- Orders for loading: status=`Completed` AND ticket_sequence_no=0
- Active orders: status IN (`Completed`, `Paid`, `Free`)

**Financial Calculations (Inbound):**
- `getTotalAmountAttribute()` — Products total
- `getGrandTotalAttribute()` — Total + service fee (1000 if `is_with_sf`)
- `getTotalBalanceAttribute()` — Grand total - bad order amount - discount - delivered amount

**JSON Product Storage:**
```php
// inbounds.products format:
[
  {
    "ptype_code": "ICE",
    "quantity": 10,
    "price": 25.00,
    "order": 1
  }
]
```

---

## File Organization

### Routes
- `routes/web.php` — Main application routes (23KB)
- `routes/ajaxreq.php` — AJAX endpoints
- `routes/auth.php` — Authentication routes

### Controllers
- `app/Http/Controllers/` — Main controllers
- `ReportGeneratorController` — Sales reports, Excel exports

### Models
- `app/Models/` — Eloquent models
- Extensive use of scopes (`scopeBranch`, `scopeActive`, etc.)
- Model accessors for computed values

### Helpers
- `app/Helpers/helpers.php` — Global helper functions
  - `getTotalOfProducts($products)` — Calculate total amount
  - `getSummaryOfProducts($products)` — Group by product type

### Views
- `resources/views/layouts/app.blade.php` — AdminLTE 3 layout
- `resources/views/report/` — Report templates
- Shared view data from `AppServiceProvider`:
  - `$gbranches` — All branches
  - `$branch_name` — Current branch name

---

## Recent Updates (May 20, 2025)

1. ✅ Added stock reconciliation feature
2. ✅ Added inventory bad order feature
3. ✅ Updated monthly sales graph in dashboard
   - Sales based on completed and paid orders
4. ✅ Added validation of duplicated orders
5. ✅ Added summary in orders index
6. ✅ Added totals column in order index

---

## Development Workflow

### Setup Commands
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
php artisan serve          # Start development server
npm run dev                # Watch assets
npm run build              # Production build
vendor/bin/pint            # Format code
php artisan test           # Run tests
```

### Database Operations
```bash
php artisan migrate
php artisan migrate:rollback
php artisan migrate:refresh
php artisan db:show --database=mysql
```

---

## Security & Permissions

- **Authorization:** Spatie Laravel Permission package
- **Admin Gate:** `Gate::define('admin', ...)`
- **Middleware:** `->middleware('can:admin')` restricts routes
- **Activity Logging:** Spatie ActivityLog on model changes
- **HTTPS:** Forced in production (`AppServiceProvider::boot()`)

---

## Report Generation

Reports via `ReportGeneratorController`:
- Sales reports with Excel export (PHPSpreadsheet)
- Order slips, delivery receipts, loading tickets
- Customer/product summaries
- Stock availability reports

**Example Routes:**
- `/reports/sales` — Sales report (admin only)
- `/reports/sales-by-customer` — Customer sales breakdown
- `/order-slip/{code}` — Individual order slip

---

## Important Considerations

### Database Design
- JSON columns for flexible product data in orders
- Model accessors heavily used (call in correct order)
- Scopes for branch filtering and status queries
- Schema default string length: 191 (MySQL compatibility)

### Code Modification Rules
- Accessor dependencies must be maintained when editing order calculations
- Product codes are auto-generated (don't manually set `code` field)
- Branch filtering required on multi-branch data queries
- Test financial calculations thoroughly when modifying pricing logic

### Common Tasks

**Adding a new product:**
```php
Product::create([
    'product_type_code' => 'ICE',
    'product_variant_code' => 'TUBE',
    // code auto-generated as 'ICE_TUBE'
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

---

## Project Status

**Current State:** Production Laravel 11 application with active development  
**Documentation:** Extensive CLAUDE.md for coding agents  
**Testing:** PHPUnit configured, test coverage unknown  
**Deployment:** GitHub-based, manual pull deployment  

**Ready for:** Feature development, enhancements, bug fixes

---

**Next Steps:** Awaiting feature request or scope definition from CEO.
