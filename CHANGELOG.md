# Changelog

All notable changes to the EOLF Trading Sales and Inventory System.

This project does not use version tags; entries are grouped by month, newest first, and are derived from the commit history on `main`. Format loosely follows [Keep a Changelog](https://keepachangelog.com/).

---

## August 2026

### Added
- **Sales by Freezer report** (admin-only): one row per freezer (DEGIC code) per customer, Jan–Dec amount columns for a selectable year, row and column totals, and an Excel export matching the manual monitoring spreadsheet — months with no sales are left blank with a green fill. Amounts are order-line revenue (see `docs/specs/003-sales-by-freezer.md`).

### Fixed
- **Order slip print layout reworked for legal landscape.** Column capacity is now a single 38-product constant shared by the page budget and the blade chunking, with column span computed by `ceil()` instead of three hardcoded 23/45/>45 tiers that no longer matched what fits on a page — long orders used to clip or push the footer off the sheet. The view is rebuilt on a 9-column grid: long orders spill into extra columns inside the same box, and per-product-type subtotals print on the last line of each type. An explicit `14in x 8.5in @page` rule stops Chrome falling back to Letter and doubling up slips per sheet. The footer is restructured into encoded/printed by, checker, loading time and remarks, with a `page N of M` marker; the unused FREE/CHARGE checkbox and truck-checker rows are dropped.

---

## July 2026

### Fixed
- **Customer and store deletion hardened after a data-loss incident.** Customer deletion is removed entirely; store deletion is guarded so a store that owns records cannot be deleted, and store changes are now logged. Deleting a store no longer silently deletes its parent customer.
- Missing customers render as missing instead of 500-ing the page.
- Null-safe store address on the customer update form.
- Null-safe branch lookup in the global view composer.
- Date-filter validation on reports; unused eager loads dropped.

### Performance
- Removed an N+1 query in `InboundService` product summaries.

### Added
- Sales-by-product-type report with date filter and Excel export.

### Changed
- Test suite points at a dedicated `eolf_test` database.
- Pint applied to `ReportGeneratorController`.
- Added the `eolf-restore-backup` skill; agent artifacts and production DB snapshots are gitignored.

### Docs
- Added `docs/STATE.md`; recorded the customer-deletion incident and its open decisions; documented that **pushing `main` deploys to production** and that the deploy workflow is parked on a failing `ssh-keyscan` step.

---

## June 2026

### Added
- Admin **expenses module** with Excel export, later expanded with BIR tax fields and searchable accounts; "Pag-ibig Loan" added to the expense account categories.
- Payment report grouped by customer, with Excel export.
- Per-customer price levels with branch-default fallback.
- Creation-date column on the loading tickets list.
- Admins can propagate a renamed customer across report snapshots.

### Fixed
- Equipment-store no longer bulk-assigns the entire freezer pool.
- Archived/inactive products hidden from DPR item selection.
- Archived/inactive product types hidden from the available-stocks report.
- `activity_log.subject_id` widened to string so string-keyed models can be audited.

### Changed
- Schema baselined into migrations (Phase 0).

### Docs
- CLAUDE.md documents schema-via-dump, the two bad-order systems, and activity tracking.

---

## May 2026

### Added
- Product type active status manageable from the edit form.
- Name search on the sales invoices page.
- Admins can edit the order date on the edit-order page.
- Admins can pick the date when printing today's orders.

### Fixed
- Filters preserved across activity-log pagination.

---

## April 2026

### Added
- **User activity tracking** with an admin viewer.
- Today's-orders printout includes spoons and signature columns.
- Order dashboard sales-by-product-type ordered by the configured product-type sequence rather than alphabetically.
- Material withdrawals: structured pickers, zero-quantity guard, list scoped to the current branch.
- Stop-selling customers page with a reactivate action (admin-only).
- Order edit: re-price cart items when the price level changes; delete-all-items action.

### Fixed
- Saving an edited order with no items is blocked.
- Order edit flow and product list rendering stabilised.

---

## March 2026

### Added
- Dashboard sales volume by product type and by flavor.
- Materials: receive-delivery page, withdrawal print/reprint, and requestor/issuer names on the withdrawal print view.
- Admin DPR move-branch, delete, and secured revert-order.
- Customer name search on paid orders (datatable removed).

### Fixed
- Memory exhaustion on the done-delivery-receipts page.
- Bad order price storage; undefined relationship; edit/delete of an order item.
- `order_date` saved before bad-order processing; `findOrFail` used for customer lookup.
- New driver saving; mw-form redesign; print form polish.

### Changed
- CI/CD config updates, `migrate --force` in the pipeline, stray migration files removed.

---

## January – February 2026

### Added
- Bad order prices table and pricing functionality, with bad-order pricing separated from the regular pricing structure.
- FOC inbound discount update command.
- Material withdrawal review step and history listing; improved activity logging.
- Other-remarks field on pull-out equipment.

### Changed
- **Pricing structure migrated from `p_code` to product type.**
- Sales report enhancements and adjustments; DR no. replaces "degic".
- Inactive data filtered out of views.

### Fixed
- Tax-withheld formula corrected, including a per-customer calculation fix.
- FOC excluded from the collected amount.
- Form validation and pricing-level edit functionality.

---

## Q4 2025 (October – December)

### Added
- Sales invoice input in ordering and reports; editable SI.
- Reports downloadable in Excel.
- Product archiving module.
- Freezer delivery charge label in reports.
- CLAUDE.md added.

### Changed
- Navigate straight to the product display when a search returns a single result.

---

## Q3 2025 (July – September)

### Added
- New version of the sales report by customer, plus additional sales reports.
- Inventory bad order reverse.

### Changed
- UI for arranging order slips before printing; add-payment disabled when there is no order slip; UX polish on report printing; inbound model enhancements.

---

## Q2 2025 (April – June)

### Added
- **Stock reconciliation.**
- Encoding of inventory bad orders, with auto-generated reference numbers.
- Monthly sales graph on the dashboard (Completed and Paid orders).
- Pull-out form (POF) and freezer gatepass forms, finalised for report use.
- Customer update form.
- Report listing customers with no sales beyond two months.
- Duplicate-order prevention; summary in item master data.

### Fixed
- Cancelled orders hidden in order slips; DR amounts; backup command log error.

---

## Q1 2025 (January – March)

### Added
- Inbound report; revert-order-to-stocks with DB backup; user module enhancements; view for un-DR'd orders.

### Fixed
- Deleted-order validation; inbound inventory validation; DR index limited to completed orders; product variant editing; backup and logging issues; branch-select error notices.

### Changed
- HTTPS forced in production; migrations added for the new OVH server; CI/CD adjusted to skip dev dependencies.

---

## 2024 (March – December)

Initial build-out and stabilisation of the system, roughly 280 commits. Highlights:

### Added
- Core ordering architecture, including a reworked ordering and editing module (October 2024).
- Order slips, loading tickets, delivery receipts and their numbering (order nos., service fee handling, loading ticket numbers).
- Bad orders (new system), materials inventory and materials withdrawal, bulk order status updates.
- Add-payment on orders; editable DR discount and paid orders.
- Available-stocks report; ticket reprint.
- Equipment history; vehicle and driver management.
- Automated database backups via Spatie, scheduled with cron.
- Audit trail with descriptive entries.

### Fixed
- Payment, FOC and reserved-stock handling across the delete and cancel paths.
- Insufficient-stock and available-stock calculations; inbound balance adjustments.
- Cancelled inbounds excluded from reports and DR; paid status when an order has a bad order; invoice amount excluding the service fee.
