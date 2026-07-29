# STATE — EOLF Trading Sales & Inventory

One-page current state. Any fresh session or agent reads this FIRST, then the
doc it points to. Updated at every phase boundary and session end.

**Last updated:** 2026-07-29

## NOW

- **Issue #32 — Sales by Product Type** — implemented and verified on
  `feat/001-sales-by-product-type`. See `docs/specs/001-sales-by-product-type.md`.
  Remaining: code review, then squash-merge to `main`. Not pushed yet.

## BLOCKED

- Nothing blocked.

## PARKED

- **`AppServiceProvider:48` dereferences a null branch.** The global `view()->composer('*')`
  does `$branch = Branches::where('code', session('branch_code'))->first();
  $branchName = $branch->name;` with no null guard. Any session holding a
  `branch_code` that no longer exists in `branches` → `Attempt to read property
  "name" on null` → **500 on every page**, unrecoverable without clearing the
  session. Found 2026-07-29 while writing spec 001 tests (it 500s the 403 error
  page too, masking authorization failures as server errors). One-line fix
  (`$branch?->name ?? ''`), but out of scope for spec 001.

- **Model factories are missing for everything except `User`.** `InboundControllerTest`
  calls `Product::factory()`, `ProductType::factory()`, `ItemMasterData::factory()`
  etc., none of which exist, so that suite cannot pass. Pre-existing on `main`.
  Spec 001's tests build rows explicitly to work around this.

- **Auth feature tests fail on redirect assertions** (`AuthenticationTest`,
  `PasswordResetTest`, …) — they expect a post-login redirect that the
  `CheckSessionBranch` middleware now changes to `branch-select`. Pre-existing;
  test drift rather than an app bug.

- **N+1 in `InboundService::getTotalOfAllInboundProducts()`** — runs
  `ProductType::code($code)->first()` inside the per-order-line loop, so a wide
  date range on `/reports/products-summary` fires one query per line. Candidate
  follow-up issue.

## FOLLOW-UPS

- `main` still has no full schema in migrations; only the unmerged
  `db/preventive-cleanup-phases` branch (7 commits: baseline, indexes, DECIMAL
  money, FKs, `inbound_lines`) carries it. Until that lands, every branch
  needing a test database must cherry-pick `b85e62c` as spec 001 did.
- Three stashes hold parked work: customer-portal auth (`Customers` model +
  `config/auth.php` + migration + proposal doc) and the db-cleanup deploy
  runbook. `git stash list`. The `Customers.php` one is based on
  `db/preventive-cleanup-phases` — pop it there, not on `main`.

## DECISION LOG

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
