# Pricing Structure Fix Plan

## Problem Summary

| Current (Wrong) | Correct |
|-----------------|---------|
| `prices.p_code` = `SC_RR` (full product code) | `prices.p_code` = `SC` (product type only) |
| One price per product variant | One price per product type |
| Price lookup by full code | Price lookup by product type |

## Data Structure Reference

### Product Types (Container Sizes)
| Code | Name | Volume |
|------|------|--------|
| `SC` | 90ml Small Cup Regular | 90ml |
| `MC` | 135ml Medium Cup Special | 135ml |
| `BC` | 180ml Big Cup | 180ml |
| `1L` | 1 Liter | 1000ml |
| `1.7L` | 1.7 Liter | 1700ml |
| `3.6L` | 3.6 Liter | 3600ml |
| `N3.6L` | No Label 3.6L | 3600ml |
| `HG` | Half Gallon | 1892ml |
| `PT` | Pint | 473ml |
| `LOL` | Ice Lolly | 60ml |
| `ICB` | Ice Buko | 60ml |
| `ICC` | Ice Cream Cones | 110ml |
| `SPS` | Special Sticks | 60ml |

### Products (Type + Variant Combinations)
Examples:
- `SC_UBE` = Small Cup + Ube flavor
- `SC_RR` = Small Cup + Rocky Road flavor
- `1L_CNP` = 1 Liter + Coconut Pandan flavor
- `N3.6L_VNL` = No Label 3.6L + Vanilla flavor

**Key Insight**: Price is determined by container size (product type), NOT by flavor (variant).

## Current Flow Analysis

### Problem: Price Lookup Fails

```
ordering.blade.php
    ↓ User clicks product button (e.g., "SC_RR")
    ↓ addProduct("SC_RR") called (line 594)
    ↓
AJAX: /inboundin/SC_RR/1/4
    ↓
InboundController::ajaxInboundList() (line 226)
    ↓
prices::where('p_code', 'SC_RR')  ← WRONG: Looking for full code
    ↓
Returns NULL → "Price not found" error
```

### Discovery: BAD PRICING Already Works Correctly!

| Component | BAD PRICING (Correct) | Regular Pricing (Wrong) |
|-----------|----------------------|------------------------|
| **View** (pricing.blade.php) | Lines 191-201: `$productTypes` dropdown, field `product_type` | Lines 138-149: `$products` dropdown, field `price_code` |
| **Controller** (PricesController.php) | Lines 60-97: Stores `$request->product_type` | Lines 98-121: Stores `$request->price_code` |
| **Stored Value** | `p_code` = `SC` (correct) | `p_code` = `SC_RR` (wrong) |

## Files to Modify

### 1. Model: `/app/Models/prices.php`

**Current Code (lines 44-48):**
```php
public static function getPricePerPriceLevelAndPCode($pricelevelId, $productCode)
{
    $price = prices::where('p_code', $productCode)->where('pricelevel_id', $pricelevelId)->first();
    return ($price) ? $price : null;
}
```

**Changes Required:**
- Add `extractProductTypeCode()` helper method
- Update `getPrice()` (line 35-42): Extract product type before lookup
- Update `getPricePerPriceLevelAndPCode()` (line 44-48): Extract product type before lookup
- Update `getFactoryPrice()` (line 50-57): Extract product type before lookup
- Keep `product()` relationship for backward compatibility in display
- Keep `productType()` relationship

**New Helper Method:**
```php
/**
 * Extract product type code from full product code
 * Examples: "SC_RR" → "SC", "N3.6L_VNL" → "N3.6L", "SC" → "SC"
 */
public static function extractProductTypeCode($code)
{
    if (empty($code)) {
        return null;
    }

    // If code contains underscore, extract everything before last underscore
    // This handles cases like N3.6L_VNL correctly
    $lastUnderscorePos = strrpos($code, '_');
    if ($lastUnderscorePos !== false) {
        return substr($code, 0, $lastUnderscorePos);
    }

    // Already a product type code
    return $code;
}
```

### 2. View: `/resources/views/pricing.blade.php`

**Current Code (lines 138-149) - ifNotBadPricing form:**
```blade
<label class="form-label" for="price_code">Product Code</label>
<select class="form-control select2bs4" id="price_code" name="price_code">
    @foreach ($products as $product)
        <option value="{{ $product->code }}">
            {{ $product->code . ' ' . $product->productName }}
        </option>
    @endforeach
</select>
```

**Change To (match BAD PRICING pattern lines 191-201):**
```blade
<label class="form-label" for="product_type">Product Type</label>
<select class="form-control select2bs4" id="product_type" name="product_type">
    @foreach ($productTypes as $pType)
        <option value="{{ $pType->code }}">
            {{ $pType->code . ' ' . $pType->name }}
        </option>
    @endforeach
</select>
```

### 3. Controller: `/app/Http/Controllers/PricesController.php`

**Current Code (lines 98-121):**
```php
} else {
    $request->validate([
        'price_code' => 'required',
        'price_unit' => 'required',
        'quant' => 'required',
        'price' => 'required',
    ]);

    $price = prices::where('pricelevel_id', $request->pricing_id)
        ->where('p_code', $request->product_type)  // Note: already uses product_type here (bug?)
        ->first();

    if ($price) {
        return redirect('/pricing/')->withErrors('Price already exists!');
    }

    prices::create([
        'pricelevel_id' => $request->pricing_id,
        'p_code' => $request->price_code,  // ← WRONG: Uses price_code
        'p_unit' => $request->price_unit,
        'p_quant' => $request->quant,
        'p_price' => $request->price,
    ]);
}
```

**Change To:**
```php
} else {
    $request->validate([
        'product_type' => 'required',  // Changed from price_code
        'price_unit' => 'required',
        'quant' => 'required',
        'price' => 'required',
    ]);

    $price = prices::where('pricelevel_id', $request->pricing_id)
        ->where('p_code', $request->product_type)
        ->first();

    if ($price) {
        return redirect('/pricing/')->withErrors('Price already exists for this product type!');
    }

    prices::create([
        'pricelevel_id' => $request->pricing_id,
        'p_code' => $request->product_type,  // Changed from price_code
        'p_unit' => $request->price_unit,
        'p_quant' => $request->quant,
        'p_price' => $request->price,
    ]);
}
```

### 4. Controller: `/app/Http/Controllers/InboundController.php`

**ajaxProductList() - Line 208:**
```php
// Current:
$price = prices::getPricePerPriceLevelAndPCode($pricelevelId, $item->code);

// No change needed IF we update the model method to extract product type
// OR change to:
$productTypeCode = prices::extractProductTypeCode($item->code);
$price = prices::getPricePerPriceLevelAndPCode($pricelevelId, $productTypeCode);
```

**ajaxInboundList() - Line 239:**
```php
// Current:
$price = prices::where('p_code', $code)->where('pricelevel_id', $pid)->first();

// Change to:
$productTypeCode = prices::extractProductTypeCode($code);
$price = prices::where('p_code', $productTypeCode)->where('pricelevel_id', $pid)->first();
```

### 5. Service: `/app/Services/PriceService.php`
- Check if exists and update `getPrice()` to use `extractProductTypeCode()`

### 6. Migration: New migration file

**File:** `database/migrations/YYYY_MM_DD_HHMMSS_fix_prices_p_code_to_product_type.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Backup current prices data (optional - for safety)
        // DB::statement('CREATE TABLE prices_backup AS SELECT * FROM prices');

        // Step 2: Update p_code to extract product type
        // Use SUBSTRING_INDEX to get everything before the last underscore
        DB::statement("
            UPDATE prices
            SET p_code = SUBSTRING_INDEX(p_code, '_',
                CHAR_LENGTH(p_code) - CHAR_LENGTH(REPLACE(p_code, '_', ''))
            )
            WHERE p_code LIKE '%\\_%'
        ");

        // Step 3: Handle duplicates - keep most recent per (pricelevel_id, p_code)
        DB::statement("
            DELETE p1 FROM prices p1
            INNER JOIN prices p2
            WHERE p1.pricelevel_id = p2.pricelevel_id
            AND p1.p_code = p2.p_code
            AND p1.updated_at < p2.updated_at
        ");

        // Step 4: Add unique constraint (optional - prevents future duplicates)
        // Schema::table('prices', function (Blueprint $table) {
        //     $table->unique(['pricelevel_id', 'p_code']);
        // });
    }

    public function down(): void
    {
        // Cannot fully reverse - would need backup table
        // DB::statement('DROP TABLE IF EXISTS prices');
        // DB::statement('RENAME TABLE prices_backup TO prices');
    }
};
```

### 7. Seeder: `/database/seeders/DatabaseSeeder.php`
- Update any price seeds to use product type codes only (e.g., `SC` instead of `SC_RR`)

## Implementation Order

```
1. ✅ Backup database (prices table)
   mysqldump -u root -p database_name prices > prices_backup.sql

2. Update prices.php model
   - Add extractProductTypeCode() helper
   - Update getPricePerPriceLevelAndPCode() to use helper
   - Update getPrice() to use helper
   - Update getFactoryPrice() to use helper

3. Update InboundController.php
   - Line 239: Use extractProductTypeCode() before price lookup
   - Line 208: Already handled by model update (or add explicit extraction)

4. Update pricing.blade.php
   - Lines 138-149: Change dropdown from products to productTypes
   - Change field name from price_code to product_type

5. Update PricesController.php
   - Lines 100-121: Change validation and store to use product_type

6. Check/Update PriceService.php (if exists)

7. Create and run migration to clean existing data

8. Update seeders (if needed)

9. Test all price-related flows
```

## Backward Compatibility

The `extractProductTypeCode()` helper ensures existing code passing full product codes will still work:
- Input: `"SC_RR"` → Returns: `"SC"`
- Input: `"N3.6L_VNL"` → Returns: `"N3.6L"`
- Input: `"SC"` → Returns: `"SC"` (already a type code)
- Input: `""` or `null` → Returns: `null`

## Testing Checklist

- [ ] Create new price entry → Uses product type dropdown
- [ ] View pricing list → Shows product type names correctly
- [ ] Create order (ordering.blade.php) → Prices fetched by product type
- [ ] Edit order → Prices remain correct
- [ ] Bad order pricing → Already works (no change needed)
- [ ] ajaxProductList → Returns correct prices for all variants of same type
- [ ] ajaxInboundList → Adds products with correct prices
- [ ] Historical orders → JSON products unaffected (prices stored in order)

## Risk Mitigation

| Risk | Mitigation |
|------|------------|
| Duplicate prices after migration | Keep most recent price per (pricelevel_id, product_type) |
| Historical orders affected | No impact - prices already stored in JSON within orders |
| External integrations | Helper method handles full codes transparently |
| Migration failure | Backup table before migration, test on staging first |
| Price lookup returns NULL | Helper returns original code if no underscore found |

## Decision Log

- **Duplicate Handling**: Keep most recent price (by `updated_at`) when multiple variants collapse to one product type
- **N3.6L Edge Case**: `extractProductTypeCode()` uses `strrpos()` (last underscore) to correctly handle `N3.6L_VNL` → `N3.6L`
- **Backward Compatibility**: Model methods extract product type internally, so callers don't need to change
- **Unique Constraint**: Optional - can be added after verifying data is clean

## SQL Verification Queries

```sql
-- Before migration: Check current p_code values
SELECT p_code, pricelevel_id, p_price, updated_at
FROM prices
ORDER BY p_code;

-- Check for potential duplicates after extraction
SELECT
    SUBSTRING_INDEX(p_code, '_', CHAR_LENGTH(p_code) - CHAR_LENGTH(REPLACE(p_code, '_', ''))) as extracted_type,
    pricelevel_id,
    COUNT(*) as count
FROM prices
WHERE p_code LIKE '%\\_%'
GROUP BY extracted_type, pricelevel_id
HAVING count > 1;

-- After migration: Verify all p_code values are product types
SELECT DISTINCT p_code FROM prices ORDER BY p_code;
```
