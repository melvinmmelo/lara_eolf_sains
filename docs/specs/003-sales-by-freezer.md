# Spec 003 — Sales by Freezer

**Status:** Implemented (working tree, 2026-08-31 — tests not yet run locally, see *Progress*)
**Issue:** none (requested via screenshot of the manual Excel sheet "MONTHLY MONITORING OF SALES PER FREEZER")
**Date:** 2026-08-31

## Goal

A branch-scoped, admin-only report reproducing the manually maintained
"Sales by Freezer" spreadsheet: one row per **freezer (DEGIC code) per
customer**, twelve monthly amount columns (Jan..Dec of a selectable year), a
per-row Total, a column-totals footer row, and an Excel export.

## Acceptance criteria

- [ ] Admin opens `/reports/sales-by-freezer` and sees one row per DEGIC code + customer, with Jan..Dec columns and a row Total
- [ ] A Year selector filters the whole report (defaults to the current year; options span from the branch's earliest order year)
- [ ] A footer row totals each month column and the grand total
- [ ] Orders with no `degic_no` are grouped per customer under **N/A**, not dropped
- [ ] The same DEGIC code under two different customers yields two rows (freezer reassignment history stays visible)
- [ ] Orders with `products` stored as a JSON **object** (the ~285-row gotcha) are counted
- [ ] Only the current branch's orders are counted; `Cancelled`/`Deleted`/`is_foc` are excluded (same semantics as `getSalesQuery()`)
- [ ] Excel export returns the same rows/totals for the same year, with the title row and Mon-YYYY headers
- [ ] Non-admin gets 403 on both routes

## Reuse

| Existing code | How it's used |
|---|---|
| `SalesByProductTypeService` (spec 001) | Pattern copied: static `summarize()`, tolerant `products` JSON decoding (array **and** object encodings), line revenue = `Σ qty × price` |
| `inbounds.degic_no` (denormalized `equipment.code`) | The grouping key — already what every other report displays as "DEGIC No" (`exportSalesReport`, `exportPaymentReport`) |
| `inbounds.customer_name` / `store_name` (denormalized) | Row labels; avoids joining `customers`/`storeinfo` for a year-wide result set |
| `exportSalesByProductType()` | Export pattern (header styling, totals row, `streamDownload`) |
| `resources/views/report/sales-by-product-type.blade.php` | Filter form + table + footnote markup |

**Deliberately NOT grouped via `equipment_id`:** the column is `varchar(191)`,
untyped, and `degic_no` is the denormalized key the rest of the reporting
family already trusts.

## New

- **Migration:** none.
- **Backend:** `app/Services/SalesByFreezerService.php` — buckets line revenue
  into `months[1..12]` keyed by `degic_no|customer_id`; rows sorted by customer
  name then DEGIC code.
- **Controller:** `ReportGeneratorController::salesByFreezer()` +
  `exportSalesByFreezer()` + private `freezerSalesQuery(int $year)` (own query,
  not `getSalesQuery()`, because the filter is a whole calendar year and the
  select is stripped to the 7 columns the aggregation reads — a year of orders
  is the largest set any report loads).
- **Routes:** `GET /reports/sales-by-freezer` → `report.sales-by-freezer`;
  `GET /reports/sales-by-freezer/export` → `.export`. Both `can:admin`
  (money-bearing, matching `report.sales` / spec 001's reasoning).
- **Frontend:** `resources/views/report/sales-by-freezer.blade.php` (year
  dropdown, 15-column table inside `.table-responsive`, zero months rendered
  blank like the source spreadsheet) + sidebar link (snowflake icon).

## Amount semantics

Same as spec 001: **order-line revenue only** (`Σ quantity × price`). The ₱1000
service fee, discounts and bad-order deductions are order-level and excluded,
so the grand total will NOT equal the Sales report's `Σ grand_total`. The
screen footnote states this.

## Scale notes

~5.5k inbounds per branch-year hydrated with a 7-column select and aggregated
in PHP — same order of work as spec 001's `yearly` filter. Result set is one
row per active freezer (hundreds), rendered without pagination like the source
spreadsheet. No new indexes.

## Test plan

`tests/Feature/SalesByFreezerReportTest.php`

- buckets amounts into the right month and row; multiple freezers per customer split correctly
- null `degic_no` → N/A row; same DEGIC under two customers → two rows
- object-encoded `products` JSON counted (regression guard)
- year filter honoured; invalid year is a validation error, not a 500
- excludes `Cancelled`/`Deleted`/`is_foc` and other branches
- footer totals equal the sum of rows
- non-admin 403 on report and export; export returns an XLSX

## Progress

- [x] Service + controller + routes + authz
- [x] Blade view + sidebar link
- [x] Excel export
- [x] Feature tests written; `php -l`, `route:list`, `view:cache`, Pint all clean
- [ ] Feature tests green — **blocked locally on 2026-08-31: the `shared_mysql`
      container does not exist on this machine, so the `eolf_test` DB is
      unreachable. Run `php artisan test tests/Feature/SalesByFreezerReportTest.php`
      once the shared MySQL stack is up.**
- [ ] Real-data reconciliation against a production copy
- [ ] Review passed, merged

## Out of scope

- Reconciling to `grand_total` (see *Amount semantics*)
- The "REMARKS" column from the source spreadsheet (free-text, no backing field)
- Drill-down from a freezer row to its orders
- Multi-year or custom date ranges (the source sheet is strictly Jan–Dec)
