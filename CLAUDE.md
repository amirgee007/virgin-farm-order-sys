# Virgin Farms Order System — Project Memory

Read this first. It captures the project shape so you don't have to re-explore.

## Stack
- Laravel 10, PHP 8.1+
- Forked from `loshmis/vanguard` (root namespace is `Vanguard\`, not `App\`)
- MySQL, file cache, sync queue, database sessions
- Blade views, jQuery + Bootstrap frontend, webpack mix
- Mail: Mailtrap sandbox (local), `OrderConfirmationMail`, `CartDetailMail`, etc.
- Auth: Sanctum + custom two-factor (`TwoFactorController`), social login, impersonation (`lab404/laravel-impersonate`)

## Namespaces & paths
- Root namespace: `Vanguard\` → maps to `app/`
- User model: `app/User.php` (`Vanguard\User`)  — NOT `app/Models/User.php`
- Models: `app/Models/*` under `Vanguard\Models\`
- Controllers: `app/Http/Controllers/{Web,Api}/`
- Helpers: `app/Support/helpers.php` (autoloaded)
- Views: `resources/views/`
- Routes: `routes/web.php` (565 lines, single file), `routes/api.php`

## Domain (key models)
- `User` — has `state`, `supplier_id`, `carrier_id`, `price_list`, role via Vanguard authz
- `Product` — `item_no`, `product_text`, `color_id`, `unit_of_measure`; price columns: `price_fob`, `price_fedex`, `price_fedex_2` (Hawaii / FedEx+)
- `ProductQuantity` — inventory rows per product
- `ProductGroup` — grouped products with `product_group_product` pivot (`stems`)
- `Cart`, `CartSnapshot` — current cart + historical snapshots
- `Order` + `OrderItem` — finalized orders; `is_standing_order` flag (Jun 2026)
- `WishList` + `WishListItem` — pre-order quote workflow; statuses: draft/submitted/quoted/closed; items have `approval` (Pending/Available) and `is_market_price`
- `Carrier`, `Box`, `ShippingAddress`, `ColorClass`, `PromoCode`, `Setting`

## Pricing logic — CRITICAL
`myPriceColumn()` in `app/Support/helpers.php:50` picks the price column. Don't hand-roll this elsewhere — call the helper.
- Non-US user (`state > 52`) or `supplier_id == 4` (farms-direct) → always `price_fob`
- Default column from `$user->price_list` mapped via `getPrices()`
- Override rules based on `price_list` × `carrier_id` (FedEx = 23):
  - PL=1 (FedEx list) + non-FedEx carrier → `price_fob`
  - PL=2 (FOB) + FedEx carrier → `price_fedex`
  - PL=3 (Hawaii) + non-FedEx → `price_fob`
  - PL=4 (FedEx+) + FedEx → `price_fedex_2`

Note: there is no `ProductPrice` model — pricing is column-selection only.

## Carrier rules (see README.md)
- FedEx (23): no Friday shipping
- Virgin Farms Delivery (17): Tuesday delivery, must order by Monday
- Non-US carriers: `[2, 3, 10, 25, 48]` via `getNonUSCarrier()`
- FedEx Priority + Pickup (23, 32): block orders placed after 3:30 PM with empty cart
- Cube/box fees: `getCubeSizeTax($size)` — date-windowed extra fees via `Setting` key `extra-fees-date`

## Cart & checkout flow
`CartController` (`app/Http/Controllers/Web/CartController.php`):
- `viewCart()` clears `order_note_{userId}` and `standing_order_{userId}` cache on load (checkbox starts unchecked each visit)
- `saveOrderNotes` / `saveStandingOrder` — AJAX, cached 10–15 min
- Cart view: `resources/views/products/inventory/cart.blade.php`
- Standing-order UX: checkbox on cart page → AJAX caches preference → checkout button dynamically swaps `data-confirm-text` between standard and extended-disclaimer messages
- On checkout, `is_standing_order` is persisted on the `Order` row

## Wish List feature (Jun 2026)
Routes under `/wish-list` (prefix `wishlist.`), see `routes/web.php:42-58`.
- Customer flow: `view` (current draft) → `browse` (add products) → `submit`
- Staff flow (Admin/SalesRep): `manage` (list) → `{id}/show` → `decisions` (sales approves Pending/Available) → customer responds via `customer-decisions`
- Views: `resources/views/wish-list/{view,browse,manage,show,index}.blade.php`
- Item attributes are staff-only in detail view; unit-of-measure is hidden; colors shown as dots (matches inventory page styling using `.color-circle` with conic-gradients for MIX/ASSORTED)
- Past wish lists section on `view` page paginates 10 per page
- Browse query joins `colors_class` for color name/description

## Routing conventions
- `routes/web.php` is the single source — no separate route files per feature
- Most routes use Closure-less `[Controller::class, 'method']` form; older routes use `'Controller@method'` strings — both still present
- Permission middleware: `permission:manage.promo.codes` style; role middleware: `role:Admin`
- `auth`, `two-factor`, `registration`, `password-reset` middleware groups

## Auth & authorization
- Vanguard's permission/role system: `Vanguard\Role`, `Vanguard\Permission`
- Helpers: `myRoleName()`, `itsMeUser()` (returns current user with relations)
- Role checks in controllers: `in_array(myRoleName(), ['Admin', 'SalesRep'])`

## Conventions in this codebase
- Controllers wrap method bodies in `try { ... } catch (\Exception $ex) { Log::error(...) }` — keep this pattern
- `protected $guarded = []` on most models (mass-assignment open)
- Use Eloquent scopes (`scopeMine`, `scopeMineDraft`) over global query reuse
- AJAX endpoints return JSON or empty 200; CSRF token from meta tag
- View files mix Blade with inline `<script>` blocks — JS lives next to the markup, not in separate JS files
- Bootstrap classes + `@push('scripts')` stacks; main layout in `resources/views/layouts/`

## Recent feature work (context for current branch)
Wish List (mid-Jun 2026), then standing-order checkout flow (Jun 19).
Latest migrations:
- `2026_06_17_120000_create_wish_lists_table` — wish_lists + wish_list_items
- `2026_06_18_120000_add_approval_to_wish_list_items`
- `2026_06_18_140000_add_customer_decision_to_wish_lists`
- `2026_06_19_010000_drop_snapshot_columns_from_wish_list_items`
- `2026_06_19_120000_add_is_standing_order_to_orders_table` ← needs `php artisan migrate`

## Files that are large — read with offset/limit if needed
- `app/Http/Controllers/Web/ProductsController.php` (1471 lines) — main inventory/search/reports
- `app/Http/Controllers/Web/WishListController.php` (486 lines)
- `app/Http/Controllers/Web/CartController.php` (418 lines)
- `app/Support/helpers.php` — pricing, carrier rules, cart helpers (`getMyCart`, `myPriceColumn`, `getCubeSizeTax`, `isDeliveryChargesApply`, `itsMeUser`, `myRoleName`, `getPrices`, `getNonUSCarrier`)
- `routes/web.php` (565 lines) — all web routes

## User preferences (IMPORTANT)
- **Write simple, easy code.** No clever abstractions, no over-engineering. Plain Laravel/Blade/jQuery as used elsewhere in this project. Match existing style.
- **Never `git push` or create a PR without explicit permission.** Don't commit either unless asked.
- Keep changes small and focused on what was requested.

## What NOT to do
- Don't add a `ProductPrice` model — pricing uses column selection only
- Don't put User in `app/Models/` — it lives at `app/User.php`
- Don't write under `App\` namespace — this project uses `Vanguard\`
- Don't persist standing-order checkbox state across page loads — it's intentionally cleared on `viewCart` (consent is captured at checkout via dynamic confirm dialog)
- Don't bypass `myPriceColumn()` — pricing rules are non-obvious

## Quick references
- Test routes: `TestAmirController` (`/abc`, `/test-cubes`, `/amir/{size}`) — dev scratchpad
- Settings stored in DB via `akaunting/laravel-setting`; date-window features keyed under `Setting::key`
- IDE helper: `_ide_helper.php` at root (don't edit; regenerated)
