# STATE — EOLF Trading Sales & Inventory

One-page current state. Any fresh session or agent reads this FIRST, then the
doc it points to. Updated at every phase boundary and session end.

**Last updated:** 2026-07-30

## NOW

- **In flight: customer-deletion incident (`/bad-orders` 500 on EFTO-CAG).**
  Data half is DONE on production; the code half is NOT started and is waiting
  on Melvin. Detail in PARKED below and the DECISION LOG entry.

  **Done 2026-07-30:** `customers#453` (LAROSE STORE beside 711) restored on
  production from `activity_log#154483`'s `old` payload. Prod backup of the
  `customers` table taken first: `other:/root/db-backups/eolf-customers-before-453-restore-20260730-212017.sql[.gz]`
  (672 rows, verified). Rollback is `DELETE FROM customers WHERE id = 453;`.
  Verified after: 0 orphans in `new_bad_orders`, all 28 active EFTO-CAG bad
  orders resolve a customer, no new errors in prod `laravel.log`.

  **Prod data is now fully consistent.** `customers#461` (Jessabel Bacton) and
  `#467` (Rodolfo Madrid) reconstructed as name-only `STOP SELLING` stubs, so
  every customer reference in the database resolves: orphans in `inbounds`,
  `new_bad_orders` and `storeinfo` are all **0**. Backup:
  `other:/root/db-backups/eolf-customers-before-461-467-stubs-20260730-213327.sql[.gz]`.

  **Code fix is committed but NOT deployed** — branch
  `fix/003-customer-delete-guard`, 4 commits, unpushed. Prod is still running
  the unguarded `destroyStore()`. Merging + deploying is Melvin's call, and is
  entangled with the unresolved "does merge deploy?" question above.

  **Shipped 2026-07-30 21:58 PHT.** `fix/003` + `fix/004` are on `main` and
  deployed (prod at `a4f6f29`, verified: `customer.destroy` gone from the
  server's route list, 0 errors logged after the deploy, `/bad-orders`,
  `/customers`, `/orders`, `/dashboard` all 302-to-login rather than 500).
  Pushed during a verified-idle window — nobody had touched the app in 30 min.
  **Note:** the push went straight to `main`, bypassing the repo's "changes
  must be made through a pull request" rule (Melvin's explicit instruction);
  #44/#45 went through PRs and future work should too.

- **Last landed:** Issue #32 — Sales by Product Type. PR #44 merged to `main`
  2026-07-29 (merge commit `d46ec2e`), which auto-deployed to production via
  `.github/workflows/main.yml`; run 30454415206 succeeded and
  `/reports/sales-by-product-type` responds on prod (302 → login, as expected
  for an authenticated route). Spec: `docs/specs/001-sales-by-product-type.md`.

  **On `.github/workflows/main.yml` — SETTLED 2026-07-30: pushing `main` DOES
  deploy to production.** ~~Melvin, 2026-07-29: "merge is not deploying in this
  project"~~ — disproven by observation. The push of `a4f6f29` at 21:57 PHT
  triggered run 30549392441, whose `deploy` job succeeded in 20s; prod moved
  from `71072af` to `a4f6f29` and the removed `customer.destroy` route was gone
  from `php artisan route:list` on the server immediately after. **Treat every
  push to `main` as a production release.** The workflow runs `git reset --hard
  origin/main`, `composer install --no-dev`, `artisan db:backup`, `artisan
  migrate --force` — and **no tests**: there is no CI gate, so the suite must be
  run locally before pushing, and pushing outside a quiet window is a live
  deploy on top of working users.

## BLOCKED

- Nothing blocked.

## PARKED

- **52 orphaned `inbounds.store_id` and 39 orphaned
  `new_temp_bad_orders.new_bad_order_id` on production.** Found by the
  referential-integrity sweep on 2026-07-30 (this schema has no foreign keys
  anywhere, so nothing prevents them). No 500 results — every view dereference
  of `->store->` is `??`-guarded and `inbounds.store_name` is denormalized, so
  all 52 orders still display their store correctly. **No new ones can be
  created** (the store guard landed in `fix/004`), but these are not
  retroactively repairable: only 3 distinct stores are missing, and `StoreInfo`
  had no activity logging, so nothing recorded what they were beyond the name.
  Deliberately left as-is. The 39 bad-order line items are unexamined. A full
  audit needs `data-analyzer`; FK constraints are a `db-designer` project
  (`db/preventive-cleanup-phases` carries a start).

- **`EquipmentStoreController` dereferences `->storeinfo->` unguarded** at
  lines 241 and 339-341. `equipment_store.store_id` currently has 0 orphans so
  it is not reachable, but `destroyStore()` deletes a store without cleaning up
  `equipment_store` rows that point at it, so it can become reachable.

- **`laravel/boost` cannot be installed** — it requires `guzzlehttp/guzzle
  ^7.9`; this project is locked to `7.8.1`. Needs a guzzle upgrade first, which
  belongs in `/upgrade`, not bundled into a fix branch. Attempted 2026-07-30;
  composer.json/lock reverted cleanly.

- **Model factories are missing for everything except `User`.** `InboundControllerTest`
  calls `Product::factory()`, `ProductType::factory()`, `ItemMasterData::factory()`
  etc., none of which exist, so that suite cannot pass. Pre-existing on `main`.
  Spec 001's tests build rows explicitly to work around this.

- **Auth feature tests fail on redirect assertions** (`AuthenticationTest`,
  `PasswordResetTest`, …) — they expect a post-login redirect that the
  `CheckSessionBranch` middleware now changes to `branch-select`. Pre-existing;
  test drift rather than an app bug.

## FOLLOW-UPS

- `main` still has no full schema in migrations; only the unmerged
  `db/preventive-cleanup-phases` branch (7 commits: baseline, indexes, DECIMAL
  money, FKs, `inbound_lines`) carries it. Until that lands, every branch
  needing a test database must cherry-pick `b85e62c` as spec 001 did.
- Three stashes hold parked work: customer-portal auth (`Customers` model +
  `config/auth.php` + migration + proposal doc) and the db-cleanup deploy
  runbook. `git stash list`. The `Customers.php` one is based on
  `db/preventive-cleanup-phases` — pop it there, not on `main`.

## RESOLVED

- **2026-07-29 — N+1 in `InboundService` (was PARKED).** Both
  `getTotalOfAllInboundProducts()` and `…v2()` ran
  `ProductType::code($code)->first()` inside the per-order-line map — **9,040
  queries** for one month of one branch, and unbounded for `v2` (it scans every
  order ever). The duplicated block is now one private
  `summarizeProductsByCode()` helper with the lookup fetched once: **9,040 → 2
  queries**, output byte-identical on real data. It also no longer fatals on a
  line naming a product type that no longer exists (was a null dereference).
  Covered by `tests/Feature/InboundServiceProductSummaryTest.php`, mutation-tested.

- **2026-07-29 — `AppServiceProvider` null branch (was PARKED).** The global
  `view()->composer('*')` dereferenced `$branch->name` unguarded, so any session
  holding a `branch_code` with no matching `branches` row threw on every render
  — a 500 on **every page**, including error pages, which masked 403s as 500s.
  Fixed with `$branch?->name ?? ''` and covered by
  `tests/Feature/BranchViewComposerTest.php` (mutation-tested: restoring the
  unguarded line turns 2 of the 3 tests red).

## DECISION LOG

- **2026-07-30 — Restore `customers#453` on production rather than patch the
  view first.** The customer was deleted by accident (store-delete cascade) six
  days after their most recent order, so restoring returns the data to truth
  rather than working around a bug; it also reconnects 45 paid orders across
  every report, which a view fix would not. Melvin's explicit go, taken during
  a verified-idle window (no request served in 17 min; 3 sessions logged in but
  idle 17/33/82 min). `created_at` deliberately left **NULL**: Spatie logged
  only the fillable attributes, so the original value is unrecoverable, and the
  ~2024-08-23 estimate bracketed from neighbours `#452`/`#454` would read as a
  real record when it is an inference. `updated_at` = restore time. `status`
  restored verbatim as `Active` (capital A) — 35 other rows use it and the
  column is `utf8mb4_unicode_ci`, so `scopeActive()` still matches.
- **2026-07-30 — Stores get a delete guard, NOT soft deletes.** Melvin asked
  whether stores should be soft-deleted too. Evidence said no: store deletion
  has happened exactly 3 times ever, all three being the same requests that
  destroyed customers 246/453/684, and the resulting 52 orphaned orders display
  correctly anyway because `inbounds.store_name` is denormalized. Soft deletes
  would have put a global scope on `StoreInfo` — reaching `$customer->stores()`,
  the `storeinfo()` hasOne, equipment-store screens and every store picker — to
  solve a problem with zero user-visible failures. Instead: a reference guard
  (orders / bad orders / equipment) plus `AutoLogsChanges` on `StoreInfo`, which
  had **no activity logging at all** — the reason those 3 stores are gone for
  good while customer 453 was recoverable. If a store ever legitimately needs
  retiring, the answer is a status flag matching `customers.status` and the
  `scopeActive` idiom used by Delivery/Vehicles/Drivers — not soft deletes.
- **2026-07-30 — Customers are never deleted.** Melvin: *"customer should not
  be deleted"*. Stop-selling already retires a customer reversibly while
  preserving their orders, so deletion offered nothing it doesn't — and cost a
  six-week production outage. `destroyStore()` no longer touches the customer
  under any condition; the `customer.destroy` route and the Delete button in
  three views are gone. This **supersedes** the reference-guard approach taken
  earlier the same session (~~delete only customers with no history~~) and
  closes `docs/specs/002` without any soft-delete work — soft deletes would
  have put a global scope on every `Customers` query in a legacy codebase to
  make a capability survivable that is better removed. Accepted consequence:
  mis-keyed customers can only be stop-sold, never removed.
- **2026-07-30 — `created_at` on 453 set to `2024-08-23`, reversing the NULL
  decision made an hour earlier.** ~~Left NULL as an honest unknown~~ — NULL
  turned out to be *unsafe*, not merely honest: `Customers` has
  `protected $appends = ['date_created']` whose accessor calls
  `$this->created_at->format()` unguarded, so `GET /customers/{id}/edit`
  (`response()->json($customer)`, the Edit button) fatals on a row with no
  `created_at`. The restore therefore introduced a fresh 500 on prod, closed by
  backfilling the value. `2024-08-23` is bounded evidence, not a guess: ids are
  sequential and `created_at` monotonic, so `#453` falls between `#452`
  (2024-08-22 10:52) and `#454` (2024-08-24 08:31). The accessor is now
  null-safe in code as well (commit `e1d3462`), across 14 models.
  **Lesson: on a legacy schema, "leave it NULL" is only honest if every reader
  of that column is null-safe — check the accessors and `$appends` first.**
- **2026-07-30 — 461/467 reconstructed as `STOP SELLING` stubs rather than left
  orphaned.** Names come from `inbounds.customer_name`, which the app itself
  denormalized at order time — recorded data, not invention. `STOP SELLING`
  keeps them out of every active picker (`scopeActive`) while making their 7
  paid orders resolve; `created_at` set to each one's first order date, which
  is the latest they can possibly have existed. Everything else left NULL
  because nothing else is known.
- **2026-07-30 — 246 and 684 left deleted.** Both were also deleted by user 2
  (2026-05-11 Bas Tea, 2026-07-15 IAN DAVE VARIETY STORE) but neither left a
  single orphaned row, so there is nothing to repair.
- **2026-07-29 — Spec 001 amount semantics.** Sales-by-product-type reports
  order-LINE revenue (qty × price) only. The ₱1000 service fee, discounts and
  bad-order deductions are order-level and cannot be attributed to a product
  type, so this report deliberately does not foot to the Sales report's grand
  total. Melvin chose this over a fully reconciled variant.
- **2026-07-29 — Test database.** The suite runs against `eolf_test` on the
  shared MySQL stack, not sqlite, because the schema baseline is MySQL-specific.
  `phpunit.xml`'s overrides use `force="true"`; they must never be commented out
  again — doing so destroyed the local `eolf_prod` copy on 2026-07-29 (restored
  from the 01:00 production backup; production itself was never touched).
