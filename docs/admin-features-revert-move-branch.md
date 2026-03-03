# Admin Features: Revert Inbound & Move DPR to Another Branch

## 1. Revert Inbound Order Items to Inventory

### What it does
Allows an admin to revert all products from a completed sales order (Inbound) back to inventory stocks. Useful for correcting wrongly delivered or entered orders.

### Files Changed
- `app/Http/Controllers/ItemMasterDataController.php` — `revertOrderItems()`
- `resources/views/ordering-view.blade.php`

### How it works
1. Admin opens the **View Order** page (`/order/{inboundId}/view`)
2. Clicks the **"Revert Order Items to Inventory"** button (visible to admins only via `@can('admin')`)
3. A modal appears requiring:
   - **Reason** — why the revert is being done
   - **Admin Password** — verified against the authenticated user's password via `Hash::check()`
4. On confirmation:
   - Each product's `quantity` is added back to `ItemMasterData.stocks` for the current branch
   - Activity is logged with: admin name, reason, and list of reverted products
   - A database backup is triggered (`Artisan::call('db:backup')`) before changes

### Route
```
POST /revert-order-items/{inbound}   →   itemdata.revertOrderItems
```

### Access
Admin only (`@can('admin')`)

---

## 2. Move Delivery Purchase Receipt to Another Branch

### What it does
Allows an admin to transfer a completed DPR (company purchase/inventory) from one branch to another. This reverses the inventory impact on the source branch and applies it to the target branch.

### Files Changed
- `app/Http/Controllers/DeliveryPurchaseReceiptController.php` — new `moveToBranch()` method
- `routes/web.php` — new route
- `resources/views/delivery-purchase-receipts/products.blade.php` — button + modal

### How it works
1. Admin opens the **DPR Products** page (`/dpr-products/{dprId}`) of a **Completed** DPR
2. Clicks the **"Move to Another Branch"** button (visible to admins only)
3. A modal appears requiring:
   - **Target Branch** — dropdown of all branches except the current one
   - **Reason** — why the move is being done
   - **Admin Password** — verified via `Hash::check()`
4. On confirmation (inside a DB transaction):
   - **Source branch**: `stocks -= (quantity - hold)`, `hold_quantity -= hold` for each product
   - **Target branch**: adds to existing `ItemMasterData` record, or creates one if it doesn't exist
   - `DPR.branch_code` is updated to the target branch
   - Activity is logged with: admin name, from/to branch, reason
5. Redirects to the DPR index on success

### Constraints
- Only **Completed** DPRs can be moved
- Target branch must differ from the current branch
- Runs inside a `DB::transaction()` — all changes roll back on failure

### Route
```
POST /delivery-purchase-receipts/{dprId}/move-branch   →   dpr.moveBranch   (middleware: can:admin)
```

### Access
Admin only (`can:admin` middleware + `@can('admin')`)

---

## Common Patterns Used
- Password confirmation: `Hash::check($request->password, auth()->user()->password)`
- Activity logging: Spatie `activity()->performedOn($model)->withProperties([...])->log(...)`
- Bootstrap modal triggered by button (`data-toggle="modal"`)
