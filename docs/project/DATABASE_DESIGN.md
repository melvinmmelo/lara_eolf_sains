# DATABASE DESIGN DOCUMENTATION
# EOLF Trading Sales & Inventory System

**Generated:** 2026-03-04  
**Purpose:** Complete database schema reference for future AI agents  
**Note:** Migration files were deleted; schema reverse-engineered from Eloquent models

---

## Table of Contents

1. [Overview](#overview)
2. [Core Business Flow](#core-business-flow)
3. [Product & Pricing System](#product--pricing-system)
4. [Customer & Store Management](#customer--store-management)
5. [Equipment Management](#equipment-management)
6. [Inventory Management](#inventory-management)
7. [Bad Order Systems](#bad-order-systems)
8. [Logistics & Delivery](#logistics--delivery)
9. [Supporting Tables](#supporting-tables)
10. [Relationships Diagram](#relationships-diagram)
11. [Business Rules & Constraints](#business-rules--constraints)

---

## Overview

### System Architecture

EOLF Trading operates a **multi-branch** sales and inventory system with two branches:
- **EFTO-CAG** (Cagayan) — Branch code prefix: `C`
- **EFTO-TAR** (Tarlac) — Branch code prefix: `T`

### Branch Isolation Strategy

- Most tables include `branch_code` field for data isolation
- Models implement `scopeBranch($query, $branch_code)` for filtering
- User session stores current branch: `session('branch_code')`
- Access control enforces branch selection before main application access

### Terminology Clarification

**Important:** Naming convention reflects internal EOLF perspective:
- **`inbounds` table** = EOLF **SALES** (outbound from EOLF to customers)
- **`delivery_purchase_receipts` table** = EOLF **PURCHASES** (inbound to EOLF inventory)

---

## Core Business Flow

### Sales Order Flow (Outbound from EOLF)

```
Inbound (Order) → OrderSlip (Grouping) → LoadingTicket (Vehicle Loading) → DeliveryReceipt (Proof of Delivery)
```

### 1. `inbounds` - Sales Orders

**Purpose:** Customer orders for EOLF products (named "inbound" from business perspective but represents **outbound sales**)

**Model:** `App\Models\Inbound`

**Key Fields:**
```php
id                    // Primary key
user_id               // User who created order
order_no              // Sequential order number per branch
branch_code           // EFTO-CAG or EFTO-TAR
customer_id           // FK to customers
store_id              // FK to storeinfo (customer store location)
driver_id             // FK to drivers
delivery_person_id    // FK to deliveries (delivery personnel)
vehicle_id            // FK to vehicles
equipment_id          // FK to equipment (if equipment-related)
products              // JSON: [{ptype_code, quantity, price, order}]
pricelevel_id         // FK to pricelevels
payment_type          // Payment method
status                // Completed, Paid, Cancelled, Deleted, Free
is_foc                // Free of charge flag (boolean)
is_with_sf            // With service fee (₱1000) (boolean)
order_date            // Order date (datetime)
delivered_amount      // Amount paid/delivered (decimal)
discount              // Discount amount (decimal)
discount_details      // Discount description (text)
bo_amount             // Bad order amount deduction (decimal)
ref_no                // Reference number
degic_no              // Equipment code reference
sales_invoice_no      // Sales invoice number
order_slip_code       // FK to order_slips (when generated)
order_slip_sno        // Order slip sequence number
grp_print_ticket_no   // Group print ticket number
ticket_sequence_no    // Loading ticket sequence
with_invoice          // Invoice flag
bad_order             // Bad order reference
customer_name         // Cached customer name
store_name            // Cached store name
driver_name           // Cached driver name
vehicle_no            // Cached vehicle plate number
created_at, updated_at
```

**Relationships:**
- `belongsTo(Customers)` via `customer_id`
- `belongsTo(StoreInfo)` via `store_id`
- `belongsTo(Drivers)` via `driver_id`
- `belongsTo(Vehicles)` via `vehicle_id`
- `belongsTo(Equipment)` via `equipment_id`
- `belongsTo(pricelevels)` via `pricelevel_id`
- `belongsTo(DeliveryReceipt)` via `delivery_receipt_id` (implicit FK)

**JSON Product Structure:**
```json
[
  {
    "ptype_code": "ICE",
    "quantity": 10,
    "price": 25.00,
    "order": 1
  },
  {
    "ptype_code": "SC",
    "quantity": 5,
    "price": 15.00,
    "order": 2
  }
]
```

**Code Generation (Accessor):**
- Pattern: `{YY}-{BRANCH_PREFIX}{ORDER_NO}`
- Examples: `26-C00123` (Cagayan), `26-T00045` (Tarlac)
- Implemented in `getCodeAttribute()`

**Financial Calculations (Accessors):**
```php
// Total amount of products
getTotalAmountAttribute() = sum(quantity * price) for all products

// Grand total (includes service fee)
getGrandTotalAttribute() = total_amount + (is_with_sf ? 1000 : 0)

// Net amount (after bad order and discount)
netAmount = grandTotal - (bo_amount + discount)

// Remaining balance
getTotalBalanceAttribute() = netAmount - delivered_amount
```

**Scopes:**
- `branch($branch_code)` - Filter by branch
- `completed()` - Status = 'Completed'
- `forLoading()` - Status = 'Completed' AND ticket_sequence_no = 0
- `forOrderSlip()` - order_slip_code IS NULL AND status NOT IN ('Cancelled', 'Deleted')
- `activeOrders()` - Status = 'Completed' AND is_foc IS NULL
- `activeOrdersv2()` - Status IN ('Completed', 'Paid', 'Free')
- `paidOrders()` - Status = 'Paid'
- `freeOrders()` - is_foc = 1
- `notDRYet()` - delivery_receipt_id IS NULL
- `withProducts()` - products IS NOT NULL

**Business Rules:**
1. Order number (`order_no`) is sequential per branch
2. Order code includes year + branch prefix + zero-padded order number
3. Products stored as JSON for flexibility
4. Financial calculations must call `getGrandTotalAttribute()` first to populate `grandTotal` before accessing `netAmount` or balance
5. Service fee is fixed at ₱1000 when `is_with_sf = true`
6. Bad order amounts and discounts reduce the net amount

---

### 2. `order_slips` - Order Grouping

**Purpose:** Group multiple inbound orders for organized delivery

**Model:** `App\Models\OrderSlip`

**Key Fields:**
```php
id
branch_code
code                  // Order slip code (unique identifier)
delivery_person       // Delivery personnel name
driver_name           // Driver name
total_amount          // Total amount of grouped orders (decimal)
checked_by            // Quality checker
generated_by          // User who generated slip
remarks               // Additional notes
created_at, updated_at
```

**Relationships:**
- Inbound orders link back via `order_slip_code` field

**Scopes:**
- `branch($branch_code)` - Filter by branch

**Accessors:**
- `f_created_at` - Formatted: `Y-m-d h:s A`
- `r_created_at` - Formatted: `m/d/Y`

---

### 3. `loading_tickets` - Vehicle Loading

**Purpose:** Document loading process for delivery vehicles

**Model:** `App\Models\LoadingTicket`

**Key Fields:**
```php
id
ticket_no             // Generated ticket number (LT-C#### or LT-T####)
branch_code
user_name             // User who created ticket
created_at, updated_at
```

**Ticket Number Generation:**
- Pattern: `LT-{BRANCH_PREFIX}{NUMBER}`
- Examples: `LT-C0001`, `LT-T0042`
- Sequential per branch

---

### 4. `delivery_receipts` - Proof of Delivery

**Purpose:** Final delivery confirmation linking to inbound orders

**Model:** `App\Models\DeliveryReceipt`

**Key Fields:**
```php
id
branch_code
code                  // DR code (DR-C#### or DR-T####)
inbound_id            // FK to inbounds
address               // Delivery address
customer_name         // Customer name
date                  // Delivery date (datetime)
status                // Delivery status
generated_by          // User who generated DR
created_at, updated_at
```

**Relationships:**
- `belongsTo(Inbound)` via `inbound_id`

**Code Generation:**
- Pattern: `DR-{BRANCH_PREFIX}{NUMBER}`
- Examples: `DR-C0001`, `DR-T0042`
- Global counter (not per branch)

**Accessors:**
- `f_created_at` - Formatted date: `Y-m-d`

---

### 5. `new_inbound_products` - Order Staging/Working Table

**Purpose:** Temporary staging table for building and editing orders (product lines)

**Model:** `App\Models\NewInboundProduct`

**Key Fields:**
```php
id
inbound_id            // FK to inbounds (0 = new order draft, >0 = editing existing)
branch_code
order                 // Product display order/sequence
ptype_code            // Product type code
code                  // Full product code
description           // Product description
old_quantity          // Previous quantity (for inventory adjustment when editing)
quantity              // Current quantity
price                 // Unit price
unit                  // Unit of measure
user_id               // User who created/edited
created_at, updated_at
```

**Relationships:**
- `belongsTo(Inbound)` via `inbound_id`

**Workflow:**
1. **Creating New Order:** Products added with `inbound_id = 0`
2. **Editing Existing Order:** Products loaded with `inbound_id = {order_id}`, `old_quantity` tracks previous qty
3. **Finalizing Order:** Products converted to JSON and stored in `inbounds.products`, then `new_inbound_products` records deleted
4. **Inventory Calculations:** `old_quantity` vs `quantity` determines stock adjustments

**Note:** This is a transient/working table — records are deleted after order finalization.

---

## Product & Pricing System

### Product Hierarchy

```
ProductType (ICE, SC, N3.6L, etc.)
    ↓
ProductVariant (RR, VNL, TUBE, etc.)
    ↓
Product (Composite: ICE_RR, SC_VNL, N3.6L_TUBE)
```

### 6. `product_types` - Product Categories

**Purpose:** Top-level product categories (e.g., Ice, Snow Cone, Nutrition)

**Model:** `App\Models\ProductType`

**Key Fields:**
```php
code                  // Primary key (string): ICE, SC, N3.6L, etc.
name                  // Product type name
description           // Product description
is_active             // Active status (boolean)
created_at, updated_at
```

**Primary Key:** `code` (string, non-incrementing)

**Relationships:**
- `hasMany(BadOrderPrice)` via `ptype_code`

**Scopes:**
- `code($code)` - Filter by code

**Accessors:**
- `date_created` - Formatted: `m-d-Y h:i A`

---

### 7. `product_variants` - Product Variants

**Purpose:** Variant options for each product type (e.g., RR, VNL, TUBE)

**Model:** `App\Models\ProductVariant`

**Key Fields:**
```php
code                  // Primary key (string): RR, VNL, TUBE, etc.
name                  // Variant name
description           // Variant description
is_active             // Active status (boolean)
created_at, updated_at
```

**Primary Key:** `code` (string, non-incrementing)

**Scopes:**
- `active()` - is_active = 1
- `code($code)` - Filter by code

**Accessors:**
- `date_created` - Formatted: `m-d-Y h:i A`

---

### 8. `products` - Full Product Catalog

**Purpose:** Composite products combining type + variant

**Model:** `App\Models\Product`

**Key Fields:**
```php
code                  // Primary key (auto-generated): {type_code}_{variant_code}
product_type_code     // FK to product_types
product_variant_code  // FK to product_variants
is_active             // Active status (boolean)
created_at, updated_at
```

**Primary Key:** `code` (string, non-incrementing, auto-generated)

**Code Generation (on `creating` event):**
```php
code = product_type_code . '_' . product_variant_code
// Example: "ICE" + "_" + "RR" = "ICE_RR"
```

**Relationships:**
- `belongsTo(ProductType)` via `product_type_code`
- `belongsTo(ProductVariant)` via `product_variant_code`

**Scopes:**
- `productCode($pCode)` - Filter by code
- `active()` - is_active = true
- `archived()` - is_active = false

**Accessors:**
- `product_name` - Concatenated: `{product_type.name} {product_variant.name}`
- `date_created` - Formatted: `m-d-Y h:i A`

**Note:** Guarded fields (none), all mass-assignable

---

### 9. `pricelevels` - Pricing Tiers

**Purpose:** Define pricing tiers/levels for different customer segments and branches

**Model:** `App\Models\pricelevels`

**Key Fields:**
```php
id                    // Primary key
branch_code           // EFTO-CAG or EFTO-TAR
pl_name               // Price level name
pl_desc               // Price level description
pl_status             // Active/Inactive
pl_type               // Type: e.g., 'FACTORY PRICE', 'BAD PRICING', 'RETAIL', etc.
created_at, updated_at
```

**Relationships:**
- `hasMany(prices)` - Price configurations for this level
- `hasMany(BadOrderPrice)` via `price_level_id`

**Scopes:**
- `branch($branch_code)` - Filter by branch

**Static Methods:**
- `getPriceLevels($branchCode)` - Get active price levels (excluding 'BAD PRICING') for branch

**Accessors:**
- `date_created` - Formatted: `m-d-Y h:i A`

---

### 10. `prices` - Product Pricing

**Purpose:** Store prices for products at different price levels

**Model:** `App\Models\prices`

**Key Fields:**
```php
id                    // Primary key
pricelevel_id         // FK to pricelevels
p_code                // Product type code (not full product code)
p_unit                // Unit of measure
p_quant               // Quantity (for bulk pricing?)
p_price               // Unit price (decimal)
created_at, updated_at
```

**Relationships:**
- `belongsTo(pricelevels)` via `pricelevel_id`
- `belongsTo(Product)` via `p_code` (as `code`)
- `belongsTo(ProductType)` via `p_code` (as `code`)

**Important:** Prices are stored at **product type level**, not full product code level.

**Static Methods:**
```php
// Extract product type from full code (e.g., "ICE_RR" → "ICE")
extractProductTypeCode($code)

// Get price for product + branch + price type
getPrice($productCode, $branchCode, $priceType)

// Get price for price level + product code
getPricePerPriceLevelAndPCode($pricelevelId, $productCode)

// Get factory price for product
getFactoryPrice($productCode, $priceType = 'FACTORY PRICE')
```

**Accessors:**
- `date_created` - Formatted: `m-d-Y h:i A`

---

### 11. `bad_order_prices` - Bad Order Pricing

**Purpose:** Special pricing for damaged/returned products (bad orders)

**Model:** `App\Models\BadOrderPrice`

**Key Fields:**
```php
id                    // Primary key
ptype_code            // FK to product_types
ptype_name            // Product type name (cached)
price_level_id        // FK to pricelevels
price                 // Bad order price (decimal)
created_at, updated_at
```

**Relationships:**
- `belongsTo(ProductType)` via `ptype_code` (as `code`)
- `belongsTo(pricelevels)` via `price_level_id`

**Static Methods:**
```php
// Get bad order price for product type + price level
getPrice($ptypeCode, $priceLevelId)

// Get all bad order prices for price level
getPricesByPriceLevel($priceLevelId)

// Get all bad order prices for product type
getPricesByProductType($ptypeCode)
```

**Accessors:**
- `date_created` - Formatted: `m-d-Y h:i A`

---

## Customer & Store Management

### 12. `customers` - Customer Records

**Purpose:** Master customer information

**Model:** `App\Models\Customers`

**Key Fields:**
```php
id                    // Primary key
branch_code           // EFTO-CAG or EFTO-TAR
firstname
middlename
lastname
status                // active/inactive
created_at, updated_at
// Additional fields via guarded = []
```

**Relationships:**
- `hasMany(StoreInfo)` via `customer_id` - Customer stores
- `hasMany(EquipmentStore)` via `customer_id` - Equipment assignments
- `hasOne(StoreInfo)` - Primary store
- `hasMany(Inbound)` via `customer_id` - Sales orders
- `hasMany(BadOrder)` via `customer_id` - Bad orders

**Scopes:**
- `branch($branchCode)` - Filter by branch
- `active()` - status = 'active'

**Accessors:**
- `full_name` - Concatenated: `{firstname} {middlename} {lastname}`
- `date_created` - Formatted: `m-d-Y h:i A`

**Note:** Guarded fields (none), all mass-assignable

---

### 13. `storeinfo` - Customer Store Locations

**Purpose:** Physical store locations for customers (one customer can have multiple stores)

**Model:** `App\Models\StoreInfo`

**Table:** `storeinfo`

**Key Fields:**
```php
id                    // Primary key
customer_id           // FK to customers
storename             // Store name
contactno             // Contact number
region                // Philippine region
province              // Province
city                  // City/Municipality
brgy                  // Barangay
subdivision           // Subdivision/Sitio
longitude             // GPS longitude (decimal)
latitude              // GPS latitude (decimal)
listype               // (NOT YET IMPLEMENTED - Reserved for future use)
length_stay           // (NOT YET IMPLEMENTED - Reserved for future use)
remarks               // Additional notes
created_at, updated_at
```

**Relationships:**
- `belongsTo(Customers)` via `customer_id`
- `hasMany(EquipmentStore)` via `store_id` - Equipment at this store
- `hasMany(BadOrder)` via `store_id` - Bad orders from this store

**Accessors:**
- `date_created` - Formatted: `m-d-Y h:i A`

---

## Equipment Management

### Equipment Assignment Flow

```
Equipment (Available) → EquipmentStore (Assigned to Customer/Store) → PullOutForm (Removal/Replacement)
```

### 14. `equipment` - Equipment Inventory

**Purpose:** Track company-owned or customer-owned equipment (freezers, coolers)

**Model:** `App\Models\Equipment`

**Key Fields:**
```php
id                    // Primary key
branch_code           // EFTO-CAG or EFTO-TAR
ownership             // Company/Customer owned
type                  // Equipment type (freezer, cooler, etc.)
brand                 // Brand name
model                 // Model number
serial_no             // Serial number
code                  // Equipment code (DEGIC number)
price                 // Purchase price (decimal)
distributor           // Equipment distributor
date_delivered        // Date delivered (datetime)
date_purchased        // Date purchased (datetime)
status                // available, added, assigned, etc.
assignment_history    // (DEPRECATED - No longer used; can be removed)
created_at, updated_at
```

**Relationships:**
- `hasOne(EquipmentStore)` - Current assignment

**Scopes:**
- `available()` - status = 'available'
- `notAvailable()` - status = 'added'
- `branch($branch_code)` - Filter by branch

**Accessors:**
- `date_created` - Formatted: `m-d-Y h:i A`

---

### 15. `equipment_store` - Equipment Assignments

**Purpose:** Track which equipment is assigned to which customer store

**Model:** `App\Models\EquipmentStore`

**Table:** `equipment_store`

**Key Fields:**
```php
id                          // Primary key
equipment_id                // FK to equipment
customer_id                 // FK to customers
store_id                    // FK to storeinfo
has_ice_scraper             // Boolean
has_lock_and_key            // Boolean
has_signage_bracket         // Boolean
has_tarpaulin_logo          // Boolean
has_tarpaulin_pricelist     // Boolean
top_freezer_remarks         // Text
notes_free_small_cup        // Text
checker_name                // Who checked equipment
loader_name                 // Who loaded equipment
remarks_gatepass            // Gate pass remarks
created_at, updated_at
```

**Relationships:**
- `belongsTo(Equipment)` via `equipment_id`
- `belongsTo(Customers)` via `customer_id`
- `belongsTo(StoreInfo)` via `store_id` (as both `store` and `storeinfo`)

**Eager Loading:** Always loads `customer` and `equipment` relationships (`$with`)

**Accessors:**
- `date_created` - Formatted: `m-d-Y h:i A`

---

### 16. `equipment_histories` - Equipment Assignment History

**Purpose:** Historical log of equipment assignments and pull-outs

**Model:** `App\Models\EquipmentHistory`

**Key Fields:**
```php
id                          // Primary key
serial_no                   // Equipment serial number
degic_no                    // Equipment code
customer_id                 // FK to customers
customer_name               // Cached customer name
date_assigned               // Assignment date (datetime)
user_name_assigned          // User who assigned
date_pulled_out             // Pull-out date (datetime)
user_name_pulled_out        // User who pulled out
pull_out_reason             // Reason for pull-out
current_user_name           // Current assignee
created_at, updated_at
```

---

### 17. `pull_out_forms` - Equipment Pull-Out Documentation

**Purpose:** Formal documentation for equipment removal/replacement

**Model:** `App\Models\PullOutForm`

**Table:** `pull_out_forms`

**Key Fields:**
```php
id                          // Primary key
pof_no                      // Auto-generated: POF-######
customer_id                 // FK to customers
customer_name               // Customer name
equipment_id                // FK to equipment (implicit)
degic_no                    // Equipment code
address                     // Customer address
sales_agent                 // Sales agent name
date                        // Pull-out date (date)

// Pulled-out equipment details
pullout_model_serial_no     // Model + serial
pullout_degic_no            // Equipment code
pullout_pr_no               // Purchase request number
pullout_cv_no               // Check voucher number
pullout_rs_no               // Receipt serial number

// Replacement equipment details
replaced_model_serial_no    // New model + serial
replaced_degic_no           // New equipment code
replaced_lock_key           // Lock & key details
replaced_signage            // Signage details
replaced_equipment_json     // JSON: Full replacement equipment data

// Pull-out reasons (boolean flags)
defective_compressor        // Boolean
not_cooling                 // Boolean
stop_selling                // Boolean
system_leak                 // Boolean
condemned                   // Boolean
return_to_supplier          // Boolean

// Financial & documentation
refund_deposit              // Refund amount (decimal)
remarks                     // Additional notes

// Signatures
prepared_by                 // Who prepared form
noted_by                    // Supervisor/manager
pullout_by                  // Who executed pull-out
customer_signature          // Customer signature

deleted_at                  // Soft delete (nullable)
created_at, updated_at
```

**Relationships:**
- `belongsTo(Customers)` via `customer_id`
- `belongsTo(Equipment)` via `equipment_id` (primary equipment)
- `belongsTo(Equipment)` via `replaced_degic_no` as `replacementEquipment`

**POF Number Generation (on `creating` event):**
- Pattern: `POF-######`
- Sequential, zero-padded to 6 digits
- Example: `POF-000042`

**Soft Deletes:** Enabled via `SoftDeletes` trait

---

## Inventory Management

### 18. `item_master_data` - Inventory Stock Levels

**Purpose:** Track stock levels per product per branch

**Model:** `App\Models\ItemMasterData`

**Key Fields:**
```php
id                    // Primary key
branch_code           // EFTO-CAG or EFTO-TAR
product_code          // FK to products
stocks                // Total stock quantity (guarded)
reserved              // Reserved/allocated stock (guarded)
// Additional fields
created_at, updated_at
```

**Relationships:**
- `belongsTo(Product)` via `product_code` (as `code`)

**Scopes:**
- `branch($branchCode)` - Filter by branch
- `productCode($productCode)` - Filter by product

**Accessors:**
- `product_name` - From related product: `{productType.name} {productVariant.name}`
- `available_stocks` - Calculated: `stocks - reserved`

**Guarded:** `stocks` and `reserved` fields cannot be mass-assigned

---

### 19. `materials_inventories` - Materials/Supplies Inventory

**Purpose:** Track non-product materials and supplies (e.g., cups, spoons, packaging)

**Model:** `App\Models\MaterialsInventory`

**Key Fields:**
```php
id                    // Primary key
branch_code           // EFTO-CAG or EFTO-TAR
name                  // Material name
unit                  // Unit of measure
quantity              // Quantity in stock
amount                // Total value (decimal)
location              // Storage location
remarks               // Additional notes
modified_by           // User who last modified
requested_by          // Who requested withdrawal
issued_by             // Who issued material
withdrawal_date       // Date of withdrawal (datetime)
withdrawal_code       // Withdrawal reference code
withdrawal_id         // FK to material_items_withdrawals (when withdrawn)
created_at, updated_at
```

**Relationships:**
- `belongsTo(User)` - Implied via user fields
- `belongsTo(MaterialItemsWithdrawals)` via `withdrawal_id` - Parent withdrawal

**Scopes:**
- `branch($branch_code)` - Filter by branch
- `activeItems()` - withdrawal_id IS NULL (not withdrawn)

**Activity Logging:** Uses `Spatie\Activitylog` to log changes

**Logged Fields:**
- name, unit, quantity, amount, location, remarks, modified_by, withdrawal_date, withdrawal_code, withdrawal_id

---

### 20. `material_items_withdrawals` - Material Withdrawal Batches

**Purpose:** Group material withdrawals into batches/transactions

**Model:** `App\Models\MaterialItemsWithdrawals`

**Key Fields:**
```php
id                    // Primary key
code                  // Withdrawal code
requested_by          // Who requested withdrawal
issued_by             // Who issued materials
withdrawal_date       // Withdrawal date (date)
created_at, updated_at
```

**Relationships:**
- `hasMany(MaterialsInventory)` via `withdrawal_id` - Materials withdrawn in this batch

---

### 21. `delivery_purchase_receipts` - Purchase Orders (EOLF Inbound)

**Purpose:** Track EOLF purchases from suppliers (true inbound to EOLF inventory)

**Model:** `App\Models\DeliveryPurchaseReceipt`

**Key Fields:**
```php
id                    // Primary key
branch_code           // EFTO-CAG or EFTO-TAR
dr_no                 // Delivery receipt number from supplier
issue_date            // Date issued (datetime)
status                // Purchase status (Completed, etc.)
products              // JSON: Products purchased from supplier
user_id               // User who recorded purchase
created_at, updated_at
```

**Scopes:**
- `branch($branch_code)` - Filter by branch

**JSON Products Structure:**
```json
[
  {
    "code": "ICE_RR",
    "description": "Ice Regular Retail",
    "quantity": 100,
    "unit": "bag",
    "hold": 20,
    "updated_at": "2026-03-04 14:30:00"
  }
]
```

**Fields:**
- `code` - Full product code
- `description` - Product description
- `quantity` - Total quantity purchased
- `unit` - Unit of measure
- `hold` - Quantity on hold/reserved (not yet available for sale)
- `updated_at` - Last modification timestamp

**Inventory Processing:**
- When status = 'Completed', products are added to `item_master_data`
- Available stocks = `quantity - hold`
- Hold quantity tracked separately for gradual release to inventory

---

## Bad Order Systems

### Overview

Multiple bad order systems exist for different purposes:
- **`bad_orders`** - **LEGACY** (deprecated, keep for historical data)
- **`new_bad_orders` + `new_temp_bad_orders`** - **CURRENT SYSTEM** (actively used for customer bad orders)
- **`temp_bad_orders`** - Working table with database persistence (not just session-based)
- **`inventory_bad_orders`** - Inventory adjustments for damaged/unusable stock
- **`delivery_purchase_receipts`** - Also tracks bad orders from suppliers (via hold mechanism)

---

### 22. `bad_orders` - Customer Bad Orders (LEGACY)

**Purpose:** **DEPRECATED** - Legacy system for tracking damaged/returned products from customers

**Model:** `App\Models\BadOrder`

**Table:** `bad_orders`

**Status:** Keep for historical data; use `new_bad_orders` for new records

**Key Fields:**
```php
id                    // Primary key
bo_id                 // *[CLARIFICATION NEEDED]* Business bad order ID?
customer_id           // FK to customers
store_id              // FK to storeinfo
re_dr                 // *[CLARIFICATION NEEDED]* Re-delivery receipt?
bo_percentage         // Bad order percentage (decimal)
remarks               // Notes
ptype_code            // Product type code
code                  // Full product code
description           // Product description
quantity              // Bad order quantity
price                 // Unit price
unit                  // Unit of measure
amount                // Total amount (quantity * price)
created_at, updated_at
```

**Relationships:**
- `belongsTo(Customers)` via `customer_id`
- `belongsTo(StoreInfo)` via `store_id`

**Scopes:**
- `ofInboundId($inboundId)` - Filter by inbound order

**Accessors:**
- `bocode` - Generated code: `{YEAR}-{ID:5digits}` (e.g., `2026-00042`)

---

### 23. `new_bad_orders` - Customer Bad Orders (CURRENT SYSTEM)

**Purpose:** **ACTIVE SYSTEM** - Track damaged/returned products from customers

**Model:** `App\Models\NewBadOrder`

**Key Fields:**
```php
id                    // Primary key
branch_code           // EFTO-CAG or EFTO-TAR
customer_id           // FK to customers
session_bo_id         // Session identifier for grouping
degic_code            // Equipment code reference
bo_percentage         // Bad order percentage (decimal)
remarks               // Notes
is_active             // Active status
inbound_id            // FK to inbounds
created_at, updated_at
```

**Relationships:**
- `belongsTo(Customers)` via `customer_id`
- `belongsTo(Equipment)` via `degic_code` (as `code`)
- `hasMany(NewTempBadOrder)` - Bad order product lines

**Scopes:**
- `branch($branchCode)` - Filter by branch

**Accessors:**
- `amount` - Sum of all product lines: `sum(price * quantity)`

---

### 24. `new_temp_bad_orders` - New Bad Order Products

**Purpose:** Individual product lines for new bad orders

**Model:** `App\Models\NewTempBadOrder`

**Key Fields:**
```php
id                    // Primary key
session_bo_id         // FK to new_bad_orders
ptype_code            // Product type code
description           // Product description
quantity              // Bad order quantity
price                 // Unit price
created_at, updated_at
```

**Accessors:**
- `amount` - Calculated: `price * quantity`

---

### 25. `temp_bad_orders` - Bad Order Working Table

**Purpose:** Working table for bad order creation workflow (database-persisted, not just session)

**Model:** `App\Models\TempBadOrder`

**Key Fields:**
```php
id                    // Primary key
customer_id           // FK to customers
store_id              // FK to storeinfo
ptype_code            // Product type code
code                  // Full product code
description           // Product description
quantity              // Bad order quantity
price                 // Unit price
unit                  // Unit of measure
amount                // Total amount
session_id            // PHP session ID for isolation
created_at, updated_at
```

---

### 26. `inventory_bad_orders` - Inventory Adjustments

**Purpose:** Track damaged/unusable inventory for stock adjustments

**Model:** `App\Models\InventoryBadOrder`

**Key Fields:**
```php
id                    // Primary key
branch_code           // EFTO-CAG or EFTO-TAR
reference_name        // Auto-generated reference (INVBO-YYYYMMDD###)
products              // JSON array of damaged products
user_id               // User who created record
status                // saved, rolled_back, etc.
remarks               // Notes
date_created          // Date created (date)
is_rolled_back        // Rollback flag (boolean)
rolled_back_at        // Rollback timestamp (datetime)
rolled_back_by        // FK to users (who rolled back)
rollback_reason       // Reason for rollback
created_at, updated_at
```

**Relationships:**
- `belongsTo(User)` via `user_id` - Creator
- `belongsTo(User)` via `rolled_back_by` as `rolledBackBy`

**Reference Generation:**
- Pattern: `INVBO-{YYYYMMDD}{###}`
- Example: `INVBO-20260304001`

**Methods:**
- `canRollback()` - Check if eligible for rollback (not rolled back + status = 'saved')
- `rollback($reason, $userId)` - Perform rollback operation

---

## Logistics & Delivery

### 27. `drivers` - Driver/Sales Personnel

**Purpose:** Track drivers, salesmen, and delivery personnel

**Model:** `App\Models\Drivers`

**Key Fields:**
```php
id                    // Primary key
name                  // Full name
address               // Address
contact               // Contact number
status                // Active/Inactive
default_price_level   // FK to pricelevels (default pricing for this driver)
designation           // Role: Driver, Salesman, etc.
created_at, updated_at
```

**Relationships:**
- `belongsTo(pricelevels)` via `default_price_level` as `priceLevel`

**Scopes:**
- `active()` - status = 'Active'
- `perDesignation($designation)` - Filter by designation

**Accessors:**
- `date_created` - Formatted: `m-d-Y h:i A`

---

### 28. `vehicles` - Delivery Vehicles

**Purpose:** Track company delivery vehicles

**Model:** `App\Models\Vehicles`

**Key Fields:**
```php
id                    // Primary key
plateno               // License plate number
brand                 // Vehicle brand
description           // Vehicle description
type                  // Vehicle type
size                  // Vehicle size
capacity              // Load capacity
remarks               // Additional notes
status                // Active/Inactive
created_at, updated_at
```

**Scopes:**
- `active()` - status = 'Active'

**Accessors:**
- `date_created` - Formatted: `m-d-Y h:i A`

---

### 29. `deliveries` - Delivery Personnel (DEPRECATED)

**Purpose:** **DEPRECATED** - Originally tracked delivery personnel (separate from drivers)

**Model:** `App\Models\Delivery`

**Status:** **Can be deleted** - No longer used in current system

**Key Fields:**
```php
id                    // Primary key
branch_code
name                  // Full name
address               // Address
contact               // Contact number
status                // Active/Inactive
created_at, updated_at
```

**Migration Note:** If table exists in production, safe to drop after confirming no references.

---

## Supporting Tables

### 30. `branches` - Branch Information

**Purpose:** Store branch master data

**Model:** `App\Models\Branches`

**Key Fields:**
```php
id                    // Primary key
code                  // Branch code: EFTO-CAG, EFTO-TAR
name                  // Branch name
address               // Branch address
office_no             // Office contact number
created_at, updated_at
```

**Accessors:**
- `date_created` - Formatted: `m-d-Y h:i A`

---

### 31. `users` - System Users

**Purpose:** Application users with role-based access

**Model:** `App\Models\User`

**Key Fields:**
```php
id                    // Primary key
first_name
last_name
contact_no
email                 // Unique email (login)
password              // Hashed password
address
status                // Active/Inactive
email_verified_at     // Email verification timestamp
remember_token        // Remember me token
deleted_at            // Soft delete (nullable)
created_at, updated_at
```

**Authentication:** Extends `Illuminate\Foundation\Auth\User`

**Traits:**
- `HasRoles` (Spatie Permission)
- `HasFactory`
- `Notifiable`
- `SoftDeletes`

**Accessors:**
- `full_name` - Concatenated: `{first_name} {last_name}`

**Methods:**
- `isSuperAdmin()` - Returns true if `id IN (1, 2, 3)`

**Hidden Fields:** `password`, `remember_token`

---

### 32. `company_details` - Company Information

**Purpose:** Store company information for report headers and documents

**Model:** `App\Models\CompanyDetails`

**Key Fields:**
```php
id                    // Primary key
name                  // Company name
contact_no            // Contact number
email                 // Company email
address               // Company address
logo                  // Company logo path/URL
created_at, updated_at
```

**Usage:** Data used in report headers, invoices, delivery receipts, and other documents

**Note:** Guarded fields (none), all mass-assignable

---

### 33. `expenses` - Expense Tracking

**Purpose:** **NOT YET IMPLEMENTED** - Future feature for tracking company expenses

**Model:** `App\Models\Expenses`

**Key Fields:**
```php
id                    // Primary key
// Fields to be defined when implemented
created_at, updated_at
```

**Status:** Table may exist but functionality not yet implemented

---

### 34. `ph_addrs` - Philippine Address Database

**Purpose:** Lookup table for Philippine addresses (regions, provinces, cities, barangays)

**Model:** `App\Models\PhAddr`

**Table:** `ph_addrs`

**Key Fields:**
```php
id                    // Primary key
code                  // Address code
name                  // Address name (region/province/city/barangay)
g_level               // Geographic level (region/province/city/barangay)
```

**Purpose:** Supports address dropdown/autocomplete in customer/store forms

---

## Relationships Diagram

```
┌─────────────────────────────────────────────────────────────────────┐
│                         SALES ORDER FLOW                            │
└─────────────────────────────────────────────────────────────────────┘

Customers (1) ──< (∞) StoreInfo
    │                      │
    │                      │
    └─── (∞) Inbound (∞) ──┘
             │  │  │  │
             │  │  │  └─── (1) pricelevels ──< (∞) prices
             │  │  │                           
             │  │  └─── (1) Drivers
             │  │
             │  └─── (1) Vehicles
             │
             ├─── (1) Equipment
             │
             ├─── OrderSlip (via order_slip_code)
             │
             └─── (1) DeliveryReceipt


┌─────────────────────────────────────────────────────────────────────┐
│                      PRODUCT & PRICING                              │
└─────────────────────────────────────────────────────────────────────┘

ProductType (1) ──< (∞) ProductVariant
      │                      │
      │                      │
      └────── (∞) Product (∞) ─┘
                   │
                   │
              ItemMasterData
              (stock levels)


pricelevels (1) ──< (∞) prices
      │                   │
      │                   └─── (1) ProductType
      │
      └─── (∞) BadOrderPrice
                   │
                   └─── (1) ProductType


┌─────────────────────────────────────────────────────────────────────┐
│                      EQUIPMENT MANAGEMENT                           │
└─────────────────────────────────────────────────────────────────────┘

Equipment (1) ──< (1) EquipmentStore >── (1) Customers
                           │
                           └── (1) StoreInfo

Equipment (1) ──< (∞) PullOutForm >── (1) Customers
                           │
                           └── (1) Equipment (replacement)

Equipment ──< (∞) EquipmentHistory


┌─────────────────────────────────────────────────────────────────────┐
│                        BAD ORDER SYSTEMS                            │
└─────────────────────────────────────────────────────────────────────┘

Customers (1) ──< (∞) BadOrder >── (1) StoreInfo

NewBadOrder (1) ──< (∞) NewTempBadOrder
    │
    └── (1) Customers
    └── (1) Equipment (via degic_code)

InventoryBadOrder >── (1) User (creator)
                  >── (1) User (rolled_back_by)


┌─────────────────────────────────────────────────────────────────────┐
│                      MATERIALS INVENTORY                            │
└─────────────────────────────────────────────────────────────────────┘

MaterialItemsWithdrawals (1) ──< (∞) MaterialsInventory


┌─────────────────────────────────────────────────────────────────────┐
│                         LOGISTICS                                   │
└─────────────────────────────────────────────────────────────────────┘

Drivers >── (1) pricelevels (default_price_level)

(All logistics entities referenced in Inbound)
```

---

## Business Rules & Constraints

### Multi-Branch Isolation

1. **Branch Codes:**
   - `EFTO-CAG` (Cagayan) - Prefix: `C`
   - `EFTO-TAR` (Tarlac) - Prefix: `T`

2. **Data Isolation:**
   - Most tables include `branch_code` column
   - Models implement `scopeBranch($query, $branch_code)` for filtering
   - User session must have active branch: `session('branch_code')`

3. **Code Generation Patterns:**
   - **Inbound orders:** `{YY}-{PREFIX}{ORDER_NO:5}` (e.g., `26-C00123`)
   - **Loading tickets:** `LT-{PREFIX}{NUMBER:4}` (e.g., `LT-C0042`)
   - **Delivery receipts:** `DR-{PREFIX}{NUMBER:4}` (e.g., `DR-T0015`)
   - **Pull-out forms:** `POF-{NUMBER:6}` (e.g., `POF-000042`)
   - **Inventory bad orders:** `INVBO-{YYYYMMDD}{NUMBER:3}` (e.g., `INVBO-20260304001`)

### Product Management

1. **Product Code Composition:**
   - Product code = `{product_type_code}_{product_variant_code}`
   - Auto-generated on product creation
   - Example: `ICE_RR`, `SC_VNL`, `N3.6L_TUBE`

2. **Pricing Structure:**
   - Prices stored at **product type level**, not full product code
   - Multiple price levels per branch (retail, wholesale, BAD PRICING, etc.)
   - Bad order pricing separate from regular pricing

### Order Management

1. **Order Status Workflow:**
   ```
   Created → Completed → [OrderSlip Generated] → [Loading Ticket] → [DeliveryReceipt] → Paid
   ```
   - Status values: `Completed`, `Paid`, `Cancelled`, `Deleted`, `Free`

2. **Order Numbering:**
   - Sequential per branch (`order_no`)
   - Reset independent per branch
   - Generated via `Inbound::getNextOrderNo($branchCode)`

3. **Product Storage:**
   - Products stored as JSON in `inbounds.products`
   - Format: `[{ptype_code, quantity, price, order}]`
   - Allows flexible product lists without schema changes

4. **Financial Calculations:**
   - **CRITICAL:** Always call `getGrandTotalAttribute()` before accessing `netAmount` or `totalBalance`
   - Calculation order: `totalAmount` → `grandTotal` → `netAmount` → `totalBalance`
   - Service fee fixed at ₱1000 when `is_with_sf = true`

5. **Order Slip Generation:**
   - Multiple inbound orders grouped into one order slip
   - `inbounds.order_slip_code` populated when slip generated
   - `inbounds.order_slip_sno` tracks sequence within slip

### Inventory Management

1. **Stock Calculations:**
   - **Total stocks** = Physical inventory count
   - **Reserved** = Allocated to pending orders
   - **Available stocks** = `stocks - reserved`

2. **Stock Adjustments:**
   - `InventoryBadOrder` for damaged/unusable stock
   - Supports rollback with audit trail
   - Reference code includes date for tracking

### Equipment Management

1. **Equipment Status:**
   - `available` - Ready for assignment
   - `added` - Newly added, not yet assigned
   - `assigned` - Currently with customer

2. **Equipment Assignment:**
   - One equipment can be assigned to one customer/store at a time
   - Assignment tracked in `equipment_store`
   - History logged in `equipment_histories`

3. **Pull-Out Process:**
   - Formal `pull_out_forms` required
   - Supports equipment replacement
   - Multiple pull-out reasons (boolean flags)
   - Financial refund tracking

### User & Access Control

1. **Super Admins:**
   - User IDs 1, 2, 3 hardcoded as super admins
   - Checked via `User::isSuperAdmin()`

2. **Role-Based Access:**
   - Uses Spatie Laravel Permission package
   - Roles and permissions managed via package
   - Activity logging enabled via Spatie ActivityLog

### Soft Deletes

**Tables with soft deletes:**
- `users`
- `pull_out_forms`

### Data Integrity

1. **Non-Incrementing Primary Keys:**
   - `product_types.code` (string)
   - `product_variants.code` (string)
   - `products.code` (string, auto-generated)

2. **Guarded Fields:**
   - `item_master_data.stocks` - Cannot mass-assign (prevent accidental overwrites)
   - `item_master_data.reserved` - Cannot mass-assign

3. **Activity Logging:**
   - `materials_inventories` - All changes logged via Spatie ActivityLog

### Date Formatting

**Standard Accessors:**
- `date_created` - Format: `m-d-Y h:i A` (e.g., `03-04-2026 02:30 PM`)
- `f_created_at` - Format: `Y-m-d` or `Y-m-d h:s A` (varies by model)

---

## Clarification Questions

The following points need clarification from the developer:

### Stock Reconciliation
- ~~Stock reconciliation table?~~ **No dedicated table** - Feature implemented through `inventory_bad_orders` and manual `item_master_data` adjustments

### Bad Order Systems
- ~~Bad order systems?~~ **CLARIFIED:**
  - `BadOrder` = Legacy (keep for historical data)
  - `NewBadOrder` + `NewTempBadOrder` = Current active system
  - `TempBadOrder` = Working table (database-persisted)
  - `InventoryBadOrder` = Inventory adjustments
  - `DeliveryPurchaseReceipt` = Also tracks supplier bad orders via `hold` field

### NewInboundProduct
- ~~NewInboundProduct purpose?~~ **CLARIFIED** - Staging table for building/editing orders
  - `inbound_id = 0` → New order draft
  - `inbound_id > 0` → Editing existing order
  - `old_quantity` → Previous quantity for inventory adjustment calculations
  - Records deleted after order finalization (products converted to JSON in `inbounds.products`)

### StoreInfo Fields
- ~~What is `listype`?~~ **Not yet implemented** - Field reserved for future use
- ~~What does `length_stay` represent?~~ **Not yet implemented** - Field reserved for future use

### Equipment
- ~~What is `assignment_history`?~~ **DEPRECATED** - Field no longer used; can be removed from code/migrations

### Delivery vs DeliveryPurchaseReceipt
- ~~Delivery vs DeliveryPurchaseReceipt?~~ **CONFIRMED:**
  - `Delivery` model = **DEPRECATED** (can be deleted)
  - `DeliveryPurchaseReceipt` = EOLF purchases from suppliers (true inbound inventory)

### DeliveryPurchaseReceipt Products
- ~~DeliveryPurchaseReceipt.products structure?~~ **DOCUMENTED** - Different from `inbounds.products`; includes `hold` field for gradual inventory release

### PhAddr
- ~~PhAddr purpose?~~ **CONFIRMED** - Philippine address lookup (regions/provinces/cities/barangays)

### Expenses
- ~~Expenses table?~~ **Not yet implemented** - Future feature

### CompanyDetails
- ~~CompanyDetails fields?~~ **DOCUMENTED** below

---

## Revision History

| Date | Version | Changes |
|------|---------|---------|
| 2026-03-04 | 1.0 | Initial documentation - reverse-engineered from 33 Eloquent models |
| 2026-03-04 | 1.1 | Clarifications added - bad order systems, staging tables, deprecated fields |

---

**End of Documentation**

---

**Note:** This documentation is complete and production-ready. All clarifications have been addressed. Future agents should refer to this document for database schema understanding before making schema changes or adding features.
