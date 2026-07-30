# Spec 002 — Soft-delete customers

**Status:** Draft — awaiting Melvin's approval, NOT implemented
**Date:** 2026-07-30
**Origin:** the 2026-06-17 customer-deletion incident (see `docs/STATE.md`
DECISION LOG and the `destroyStore` PARKED entry)

## Goal

Make customer deletion recoverable. Today `customers` rows are hard-deleted
with no audit-safe way back: recovering `customers#453` required reading the
Spatie activity log's `old` payload and hand-writing an `INSERT`, and that
only worked because model logging happened to have been added six weeks
earlier. Customers 461 and 467, deleted before that instrumentation existed,
are unrecoverable — their names survive only because `inbounds` denormalizes
`customer_name`.

`fix/003-customer-delete-guard` already stops the *accidental* deletion path.
This spec addresses the remaining half: an intentional deletion is still
irreversible.

## Why this is a spec and not a commit

Adding `SoftDeletes` to `Customers` installs a **global scope on every
existing query against the model**. This is a legacy schema where branch
filtering is already manual and easy to get wrong (CLAUDE.md), so the blast
radius is wide and mostly invisible at the call site:

- `Customers::scopeActive()` and the stop-selling list (`/customers/stop-selling`)
- every customer picker in the order, bad-order, and equipment flows
- `Inbound::customer()`, `NewBadOrder::customer()`, `EquipmentStore::customer()`
  and the 13 views that render `->fullName` — a soft-deleted customer would
  make these relations resolve to `null` again, which is exactly the 500 this
  branch just fixed, reintroduced by a different mechanism
- every report that joins or eager-loads customers

That last point is the trap: **soft-deleting a customer must not re-orphan
their orders in the UI.** Historical orders must keep resolving their customer.

## Acceptance criteria

- [ ] `customers.deleted_at` exists (nullable timestamp, indexed)
- [ ] `Customers` uses `SoftDeletes`; `destroy()` and `destroyStore()` soft-delete
- [ ] A soft-deleted customer disappears from every customer list and picker
- [ ] A soft-deleted customer's **historical orders, bad orders, order slips,
      delivery receipts and tickets still render their name** — relations resolve
      via `withTrashed()`, so no page regresses to `[deleted customer]`
- [ ] Reports covering a period that includes a soft-deleted customer's orders
      show the same totals as before the deletion
- [ ] An admin-only restore path exists (list + restore), mirroring the existing
      stop-selling reactivate flow at `/customers/stop-selling`
- [ ] The reference guard from `fix/003` still applies: a customer with history
      is stop-sold, not deleted — soft-delete does not become the new default
      way to remove an active customer
- [ ] Tests cover: list exclusion, historical-order rendering, report totals,
      restore, and that the guard still refuses

## Migration ladder (expand → backfill → contract)

1. **Expand** — add `deleted_at`, nullable, no behaviour change. Deployable alone.
2. **Adopt** — add the trait; audit every `Customers::` query and relation, adding
   `withTrashed()` where history must still resolve. This is the bulk of the work
   and the part that needs `db-designer` / a careful reuse audit.
3. **Contract** — none. Nothing is dropped; no data is rewritten.

## Open questions for Melvin

1. **Should customers be deletable at all?** The app already has stop-selling,
   which is the reversible, business-meaningful way to retire a customer. If the
   answer is no, this spec collapses to "remove the Delete button" — much smaller
   and arguably more correct. **This question should be settled before any code.**
2. Restore UI: fold into the existing stop-selling screen, or a separate
   `/customers/deleted` list?
3. Do 461/467 get reconstructed as part of this, or stay as the orphan case?

## Out of scope

Foreign-key constraints on the schema generally. This DB has no FKs anywhere,
which is why the incident was possible at all; fixing that is a separate,
larger `db-designer` project (`db/preventive-cleanup-phases` already carries a
FK commit).
