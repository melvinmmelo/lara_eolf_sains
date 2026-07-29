# Spec 001 — Sales by Product Type

**Status:** Draft
**Issue:** [#32](https://github.com/melvinmmelo/lara_eolf_sains/issues/32) (label: `reports`)
**Date:** 2026-07-29

## Goal

A branch-scoped sales report that groups completed sales by **product type**
(not product code), over a selectable date range, showing quantity sold and
peso revenue per type with a grand-total row, plus an Excel export — matching
the existing sales-report family.

## Acceptance criteria

- [ ] Admin opens `/reports/sales-by-product-type` and sees one row per product type with **Qty** and **Amount**
- [ ] Rows are ordered by `product_types.sequence_no` (the configured business order), not alphabetically
- [ ] Date filter offers daily / weekly / monthly / yearly / custom, identical to `/reports/sales`
- [ ] A totals row sums Qty and Amount across all types; Amount total equals the sum of every counted order line
- [ ] Orders with `products` stored as a JSON **object** (285 live rows) are counted, not skipped
- [ ] Only the current branch's orders are counted (`session('branch_code')`)
- [ ] Excel export returns the same rows and totals as the screen for the same filter
- [ ] A non-admin user is denied (403)

## Reuse

| Existing code | How it's used |
|---|---|
| `ReportGeneratorController::getSalesQuery()` | **Reused as-is** for date filter + branch + status/`is_foc` exclusions. Single source of the filter semantics. |
| `App\Models\ProductType` (`code` PK, `name`, `sequence_no`) | One `pluck`/`keyBy` lookup for names + ordering |
| `ReportGeneratorController::exportSalesReport()` | Pattern for the PHPSpreadsheet export (headers, styling, download response) |
| `resources/views/report/sales.blade.php` | Pattern for the AdminLTE filter form + table markup |
| `resources/views/layouts/aside.blade.php` (Reports menu, ~line 282) | New menu entry beside `report.sales` |

**Deliberately NOT reused:** `InboundService::getTotalOfAllInboundProducts()`
— it groups by product *code*, carries no amounts, and runs
`ProductType::code()->first()` inside its per-line loop (N+1: one query per
order line). Copying it would spread that. It stays untouched; the existing
`productsSummary` report keeps using it.

## New

- **Migration:** none.
- **Backend:** `app/Services/SalesByProductTypeService.php` — one static/instance
  method taking the `getSalesQuery()` result, decoding `products` with
  `json_decode($json, true)` (handles both array and object encodings),
  accumulating `qty` and `amount = quantity × price` per `ptype_code`, joined to
  a single pre-fetched `ProductType` map and sorted by `sequence_no`.
- **Controller:** `ReportGeneratorController::salesByProductType()` +
  `exportSalesByProductType()`.
- **Routes:** `GET /reports/sales-by-product-type` → `report.sales-by-product-type`;
  `GET /reports/sales-by-product-type/export` → `.export`. Both
  `->middleware('can:admin')` — matching `report.sales`, since this exposes
  revenue. `[assumed]` — `sales-by-customer` is *not* admin-gated, so the family
  is inconsistent; I follow the money-bearing sibling.
- **Frontend:** `resources/views/report/sales-by-product-type.blade.php` + one
  sidebar link. No design system in this project (legacy AdminLTE) — matches
  sibling report markup.

## Amount semantics (read before implementing)

Amount is **order-line revenue only**: `Σ quantity × price` over the counted
lines. It deliberately excludes the ₱1000 service fee (`is_with_sf`), discounts,
and bad-order deductions, because those are order-level and cannot be attributed
to a single product type. **This report's total will therefore NOT equal
`Σ grand_total` for the same range** — that is correct, not a bug. The screen
states this in a footnote so it is never read as a discrepancy.

## Scale notes

- **Volume:** 11,077 inbounds over ~2 years (~5.5k/yr); 13 product types. A
  `yearly` filter loads ~5.5k rows and decodes their JSON in PHP — acceptable
  (~sub-second), and the result set is 13 rows so no pagination. No new indexes:
  `getSalesQuery` filters on `order_date` + `branch_code`, already indexed.
- **Media / Queue / Cache / Rate limit:** none.

## Test plan

`tests/Feature/SalesByProductTypeReportTest.php`

- groups quantities and amounts by product type across multiple orders
- **counts an order whose `products` is an object-encoded JSON blob** (the 285-row case) — the regression guard for the documented gotcha
- orders rows by `sequence_no`, not alphabetically
- excludes `Cancelled` / `Deleted` / `is_foc` orders (asserting `getSalesQuery` semantics carry over)
- excludes another branch's orders
- honours a custom `start_date`/`end_date` window
- totals row equals the sum of the per-type rows
- non-admin gets 403 — **mutation-tested**: the `can:admin` middleware is removed, the test must go red, then restored
- export route returns a 200 XLSX for the same filter

Tests use plain `get()` (not `getJson()`) since these are Blade pages.

## Progress

- [ ] Service + unit-level aggregation correct
- [ ] Controller + routes + authz
- [ ] Blade view + sidebar link
- [ ] Excel export
- [ ] Feature tests green
- [ ] Review passed, merged

## Out of scope

- Reconciling to `grand_total` (service fee / discounts / bad orders) — see *Amount semantics*
- Drill-down from a product type to its variants or orders
- Changing or consolidating the existing `productsSummary` report
- Fixing the N+1 in `InboundService::getTotalOfAllInboundProducts` (candidate follow-up issue)
