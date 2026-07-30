# Spec 002 — Customer deletion

**Status:** Closed by decision, 2026-07-30 — **customers are never deleted.**
Implemented in `fix/003-customer-delete-guard`; no soft-delete work is needed.
**Origin:** the 2026-06-17 customer-deletion incident (see `docs/STATE.md`)

## The decision

Melvin, 2026-07-30: **"customer should not be deleted"**.

Retiring a customer is what stop-selling is for — it already exists
(`/customers/stop-selling`, `CustomersController::stopSelling()` +
`reactivate()`, `customers.status`), it is reversible, and it preserves every
order the customer ever placed. Deletion offered nothing stop-selling doesn't,
and cost a six-week production outage to learn it.

## What this replaced

This spec was originally drafted as "add `SoftDeletes` to `Customers`" — make
deletion recoverable, since recovering `customers#453` required reading the
Spatie activity log's `old` payload and hand-writing an `INSERT`, and customers
461/467 (deleted before model logging existed) were not recoverable at all.

That work is **not needed**. Soft deletes would have added a global scope to
every existing `Customers` query on a legacy schema — every picker, every
report, every `->customer->fullName` in 13 views — carrying a real risk of
re-orphaning historical orders in the UI, which is the exact 500 the incident
produced. Removing the capability is smaller, safer, and eliminates the failure
mode rather than making it survivable.

## What was implemented instead

- `CustomersController::destroyStore()` deletes the store and releases its
  equipment, and **never touches the customer** — with history or without.
- `CustomersController::destroy()` does not exist, and neither does the
  `customer.destroy` route. (The method never existed; the route pointed at
  nothing, so the Delete button had always 500-ed.)
- The Delete button is gone from `customers.blade.php`,
  `edit_customer.blade.php` and `create-customer.blade.php`.
- `tests/Feature/CustomerDeletionGuardTest.php` locks it in: no delete route,
  no delete form in any view, and the customer survives every `destroyStore()`
  path.

## Known consequence, accepted

A customer created by mistake can no longer be removed through the UI — it can
only be set to stop-selling, which hides it from active pickers. Over time,
mis-keyed customers accumulate as stop-sold rows. This was judged the better
trade against silently destroying a trading customer's history. If cleanup is
ever wanted, it should be an admin-only, reference-checked tool with its own
spec — not a Delete button on a list row.

## Out of scope

Foreign-key constraints generally. This DB has no FKs anywhere, which is why
the incident was possible; `db/preventive-cleanup-phases` carries a start on
that and it remains a separate `db-designer` project. See the orphan findings
in `docs/STATE.md` PARKED (52 `inbounds.store_id`, 39
`new_temp_bad_orders.new_bad_order_id`).
