# USM Pharmacy — Project Progress

**Last updated:** 2026-09-19

## Module Status

| Module | Owner | Status | Notes |
|---|---|---|---|
| Auth / RBAC | Member 1 | Done | Spatie Permission integrated; 5 roles active (`nurse`, `pharmacist`, `stock_manager`, `patient`, `admin`); `medical_secretary` role removed; Figma-styled login page with demo autofill & patient registration modal |
| Prescription | Member 1 | Done | Nurse-only creation & routing to pharmacy queue; modal-based prescription encoder; inventory verification & patient directory integrated |
| Nurse Portal | Member 1 | Done | Dedicated clinical workstation layout (`<x-nurse-layout>`), clinical KPI widgets, triage queue, live inventory check, and patient directory |
| Pharmacy / POS | Member 2 | Done | FEFO dispense, OTC sales, receipt view, all behind `pharmacist` role |
| Inventory | Member 2 | In progress | Stock decrements wired; nurse live inventory check view complete; batch-receiving UI not yet built |
| Inventory | Member 2 | Done | Medicine CRUD, batch receiving UI, delivery & movement audit logs, disposal tracking, count adjustments, dedicated dashboard & navigation for stock_manager & admin |
| Dual Risk Engine | Member 3 | Not started | `stock_movements` data exists; prediction algorithm not designed |
| Patient Portal & Landing Page | Member 1 | Done | USM branded landing page with AI health assistant, features, stats band, and CTA |
| Pharmacy Storefront | Member 1 | Done | Full hospital catalog with category sidebar, live search, 35 Figma products, stock badges, and inquiry modal |
| Mobile App | Not yet decided | Not started | No API routes exist yet |
| AI Chatbot | Member 3 | In progress | Interactive frontend chatbot on landing page with symptom advice & stock lookup |

## Database Schema (tables that exist)

| Table | Purpose |
|---|---|
| `users` | Auth + profile; stores `role_id` for Spatie sync |
| `roles` / `permissions` / pivot tables | Spatie RBAC (auto-created by Spatie migration) |
| `patients` | Hospital patient records; linked to `users`; type: `student`, `faculty`, `community`, `resident`, etc. |
| `medicines` | Medicine catalogue — name, generic name, category, unit, price, reorder level |
| `stock_batches` | Per-batch inventory: `batch_no`, quantities received/remaining, `expiry_date`, supplier |
| `prescriptions` | Prescription header; status: `pending` → `routed` → `dispensed` / `cancelled` |
| `prescription_items` | Line items (medicine + quantity + dosage) for a prescription |
| `pos_transactions` | POS receipt; links to a prescription (nullable for OTC) and cashier |
| `pos_transaction_items` | Itemised lines for a transaction; records batch, unit price, subtotal |
| `stock_movements` | Audit log of every inventory change; type enum: `in`, `out`, `disposal`, `adjustment` |

See `database/migrations/` for full column lists.

## What is Functional Right Now

- **Landing Page (`/`):**
  - Full USM green & gold branding with responsive navbar and shield logo.
  - Interactive AI Health Assistant with instant answers for symptoms, stock lookup, and first aid guidance.
  - Suggestion chips, features grid, stats counter band, and store CTA.
- **Pharmacy Storefront (`/medicines`):**
  - Complete hospital catalog with 35 curated medical items across 8 categories.
  - Left category sidebar with real-time stock counts and trust badges.
  - Live client-side product search and category filtering with clear button.
  - Status tags (`SALE`, `NEW`, `CRIT`, `Out of Stock`), 5-star ratings, unit pricing, and prescription warnings.
  - "Inquire at Pharmacy" interactive modal detailing dispensing location and prescription policies.
- **Authentication & Login (`/login`):**
  - Figma-designed login card with institutional branding.
  - Show/hide password toggle and client validation.
  - Interactive 2x2 demo accounts autofill grid for Nurse, Pharmacist, Stock Keeper, and Patient.
  - All 4 demo accounts seeded in database with passwords ready for testing.
  - Patient registration modal supporting full name, patient type, email, contact, and password.
- **Nurse Clinical Workstation & Portal (`/dashboard`, `/prescriptions`):**
  - **Dedicated Layout (`<x-nurse-layout>`):** High-productivity clinical interface with green USM institutional accents, persistent sidebar navigation (Dashboard, Prescriptions, Live Inventory, Patient Directory), user menu, and responsive mobile drawer.
  - **Nurse Dashboard:** Role-specific clinical dashboard with real-time summary cards (total prescriptions, routed, pending, low stock items), quick-action buttons (Launch New Prescription modal, view stock, browse patients), and a live routed prescription triage queue.
  - **Floating Modal Prescription Creator (`partials/modal-create.blade.php`):** Accessible from anywhere in the nurse portal to rapidly encode multi-item prescriptions without navigating away or losing context. Includes inline patient registration, dynamic medicine line items with real-time unit indicators, dosage instructions, and doctor name assignment.
  - **Live Inventory Check (`/prescriptions/inventory`):** Dedicated nurse view displaying the hospital formulary, live batch stock counts, stock status badges (`In Stock`, `Low Stock`, `Out of Stock`), and reorder levels to prevent prescribing unavailable drugs.
  - **Patient Directory (`/prescriptions/patients`):** Searchable patient register showing patient classification (student, faculty, resident, community), contact details, identification numbers, and prescription history links.
- **Prescription & POS Workflows:**
  - Any seeded user can log in; wrong-role routes return 403 via Spatie middleware.
  - Nurses can create a prescription with one or more medicine items. Inline patient registration is supported (creates a `users` + `patients` row in a transaction).
  - Prescriptions list view has status tabs (`All`, `Pending`, `Routed`, `Dispensed`, `Cancelled`), search, and "mine / all" scope toggle.
  - A `pending` prescription can be routed to the pharmacy queue (stock check runs first); status becomes `routed`.
  - Pharmacists see the routed queue ordered oldest-first. Processing a prescription auto-suggests FEFO batch allocations from `DispensingService::suggestFefoBatches`.
  - Pharmacists can confirm dispensing — `DispensingService::dispensePrescription` decrements `stock_batches.quantity_remaining`, creates `pos_transaction_items`, creates `stock_movements` (`type=out`), and marks the prescription `dispensed`, all inside a single `DB::transaction` with `lockForUpdate`.
  - OTC walk-in sales go through `DispensingService::processOtcSale` — same FEFO and audit logic, no prescription linked.
  - A printable receipt view renders after any completed transaction.
  - Pest feature tests cover: storefront and landing page (`StorefrontTest`), authentication (`AuthenticationTest`), registration (`RegistrationTest`), prescription CRUD, routing, inventory lookup & patients (`PrescriptionModuleTest`), POS dispense & OTC (`PharmacyPosModuleTest`), and role-based access (`RoleBasedAccessControlTest`).

## What is Deliberately NOT Built Yet

- Inventory receiving UI (no route/controller for adding new medicines or stock batches).
- Risk prediction algorithm (data collection schema exists, model not designed).
- REST API endpoints (mobile app requires these).
- Partial-dispensing workflow (current system marks the whole prescription `dispensed` in one step).

## Open Decisions

- **Partial dispensing:** If a pharmacist adjusts quantities below what was prescribed, should a new "partially dispensed" status exist, or should the original prescription stay `routed` with a partial transaction?
- **`medicines.is_active` column:** `PosController::otcCreate` filters on `is_active`, but the migration does not create this column. Needs a migration or the query needs updating.
- **Risk score formula weights:** Stockout vs. expiry prediction algorithm — inputs, thresholds, and weights not yet agreed.
- **Mobile API auth:** OAuth (Sanctum) vs. session-based — not yet decided.

## Key Conventions

- **Service layer:** Business logic lives in `app/Services/` (e.g., `DispensingService`). Controllers are thin — they call services and redirect.
- **DB safety:** All multi-table writes use `DB::transaction()` + `lockForUpdate()` on stock rows.
- **Validation:** Form Requests only — no inline `$request->validate()` in controllers.
- **Role names (exact strings):** `nurse`, `pharmacist`, `stock_manager`, `patient`, `admin`.
- **FEFO:** Always sort batches `orderBy('expiry_date', 'asc')` and filter `expiry_date >= today` and `quantity_remaining > 0`. Use `StockBatch::scopeActive()` and `scopeFefo()`.
- **Table naming:** snake_case plural (`pos_transactions`, not `posTransactions`).
- **Tests:** Pest feature tests in `tests/Feature/`; use factories + role assignment; no test should rely on seeded DB data.

---
*Update this file at the end of every work session before opening a PR.*

---

## Session Progress — 2026-09-17: Removal of Medical Secretary & Nurse Prescription Workflow

### Summary of Changes

- **Removal of Medical Secretary Role & User:**
  - Completely removed the `medical_secretary` role from `RoleSeeder` and the system's role definitions. Valid roles are now: `nurse`, `pharmacist`, `stock_manager`, `patient`, and `admin`.
  - Removed the `Medical Secretary User` demo account (`medsec@usm.edu.ph`) from `DatabaseSeeder` and `docs/SETUP.md`.
  - Added database migration `2026_09_17_101706_remove_medical_secretary_role.php` to clean up the `medical_secretary` role from Spatie tables (`roles`, `model_has_roles`, `role_has_permissions`), clear user `role_id` references, delete the `medsec@usm.edu.ph` user, and purge Spatie's permission cache.

- **Nurse-Only Prescription Creation & Routing:**
  - Transitioned the prescription workflow exclusively to the Nurse role: **Nurse → Create Prescription → Route Prescription**.
  - Updated `routes/web.php` route middleware on `/prescriptions` group from `role:nurse|medical_secretary` to `role:nurse` (with system `admin` access preserved via `RoleMiddleware`).
  - Updated `App\Policies\PrescriptionPolicy`:
    - `viewAny()`: `['nurse', 'pharmacist', 'admin']` (pharmacists retain read access to view prescriptions during POS fulfillment).
    - `view()`: `['nurse', 'pharmacist', 'admin']`.
    - `create()`: `['nurse', 'admin']`.
    - `update()`: `['nurse', 'admin']` (when status is `pending`).
    - `route()`: `['nurse', 'admin']` (when status is `pending`).
    - `cancel()`: `['nurse', 'admin']` (when status is `pending`).
  - Updated `resources/views/layouts/navigation.blade.php`:
    - Updated desktop navigation from `@hasanyrole('nurse|medical_secretary|admin')` to `@hasanyrole('nurse|admin')`.
    - Updated responsive mobile navigation from `@hasanyrole('nurse|medical_secretary|admin')` to `@hasanyrole('nurse|admin')`.

- **Documentation & Architecture Updates:**
  - Updated `docs/ARCHITECTURE.md` to reflect the 5 active roles and the `role:nurse` guard on prescription routes.
  - Updated `docs/SETUP.md` demo accounts reference table.

- **Verification & Testing Performed:**
  - Executed migration `2026_09_17_101706_remove_medical_secretary_role` successfully.
  - Updated `tests/Feature/RoleBasedAccessControlTest.php` to assert that `medical_secretary` is not present in seeded roles, only `nurse` and `admin` can access `/prescriptions`, and non-nurse staff/patients are forbidden.
  - Cleaned up `tests/Feature/PrescriptionModuleTest.php` helper roles.
  - Ran full Pest test suite (`php artisan test`): 47 tests passed (157 assertions).
  - Executed code formatter: `vendor/bin/pint --format agent` passed.

---

## Session Progress — 2026-09-18: Nurse Portal UI/UX Redesign, Floating Prescription Modal, Live Inventory Verification & Patient Directory

### Summary of Changes

- **Dedicated Nurse Clinical Layout (`<x-nurse-layout>`):**
  - Created `NurseLayout` component (`app/View/Components/NurseLayout.php` and `resources/views/components/nurse-layout.blade.php`) tailored specifically for clinical nursing staff.
  - Features high-contrast USM emerald-and-gold styling, a persistent desktop sidebar with status badges, quick navigation links (Dashboard, Prescriptions, Live Inventory, Patient Directory), account profile controls, and a responsive mobile slide-out drawer.

- **Nurse Dashboard Overhaul (`dashboard.blade.php`):**
  - Integrated role-aware dashboard rendering so Nurses see a clinical workstation dashboard while maintaining default views for other roles.
  - Clinical KPI cards highlighting Total Prescriptions, Pending Prescriptions, Routed Queue, and Formulary Stock alerts.
  - Quick Action triggers to open the floating prescription modal, jump into the live inventory check, or browse patient records.
  - Integrated active triage queue directly on the dashboard with one-click routing to the pharmacy.

- **Floating Prescription Creator Modal (`partials/modal-create.blade.php`):**
  - Developed a standalone, Alpine.js-powered modal for creating prescriptions on-the-fly without leaving the current view or dashboard.
  - Supports inline patient search or new patient registration on the fly, multiple medicine line item additions, real-time dosage instructions, doctor name attribution, and immediate submission.
  - Preserved standard full-page prescription creation (`prescriptions/create.blade.php`) wrapped with `<x-nurse-layout>` for fallback and direct navigation.

- **Live Inventory Verification View (`/prescriptions/inventory`):**
  - Created `PrescriptionController::inventory` and view `resources/views/prescriptions/inventory.blade.php`.
  - Enables nurses to cross-reference available medicine batches, remaining stock numbers, and stock status flags (`In Stock`, `Low Stock`, `Out of Stock`) before prescribing, preventing pharmacy routing rejections due to stock depletion.

- **Patient Directory View (`/prescriptions/patients`):**
  - Created `PrescriptionController::patients` and view `resources/views/prescriptions/patients.blade.php`.
  - Provides a streamlined directory for nurses to search and view registered patients, their institutional classification (Student, Faculty, Resident, Community), identification number, and prescription history.

- **Prescriptions Management & Display Refresh (`prescriptions/index.blade.php`, `prescriptions/show.blade.php`):**
  - Restyled prescription list into responsive status tabs with status badges, doctor/patient summaries, and quick routing action triggers.
  - Enhanced prescription detail view (`show.blade.php`) with modern clinical cards, batch allocation previews, and print-ready format.

- **Test Suite Verification & Coverage:**
  - Added Pest test coverage in `tests/Feature/PrescriptionModuleTest.php` to verify nurse access to the dedicated inventory check (`prescriptions.inventory`) and catalog details.
  - Ran full Pest test suite (`php artisan test`): **51 tests passed (181 assertions)** with 0 failures.
  - Ran code style fixer (`vendor/bin/pint --format agent`).

---

## Session Progress — 2026-09-19: Inventory Module Implementation

### Summary of Changes

- **Database Migrations:**
  - Added `is_active` boolean (default `true`) to `medicines` (`2026_09_18_230924_add_is_active_to_medicines_table.php`), fixing the open issue where POS OTC creation filtered on `is_active`.
  - Added `notes` nullable text column to `stock_movements` (`2026_09_18_230947_add_notes_to_stock_movements_table.php`) for delivery PO remarks, disposal reasons, and adjustment justifications.

- **Models & Relationships:**
  - `Medicine`: Added `is_active` to fillable/casts, `scopeActive()`, and `stockMovements()` HasMany relationship.
  - `StockBatch`: Added `stockMovements()` HasMany relationship, `scopeActive()`, `scopeFefo()`, and `scopeExpiringSoon(int $days = 30)`.
  - `StockMovement`: Added `notes` to fillable and `scopeOfType(string $type)`.

- **Service Layer (`InventoryService`):**
  - `receiveBatch()`: Atomically registers a `StockBatch` and records an incoming delivery audit movement (`type = 'in'`).
  - `disposeBatch()`: Locks batch (`lockForUpdate`), zeroes `quantity_remaining`, and records disposal movement (`type = 'disposal'`) with required reason.
  - `adjustStock()`: Locks batch (`lockForUpdate`), updates `quantity_remaining`, and records adjustment movement (`type = 'adjustment'`) with reason.

- **Policies & Form Requests:**
  - Created `MedicinePolicy` and `StockBatchPolicy` for granular role-based authorization.
  - Created 5 Form Requests: `StoreMedicineRequest`, `UpdateMedicineRequest`, `ReceiveBatchRequest`, `DisposeBatchRequest`, and `AdjustStockRequest`.

- **Controller & Routes (`InventoryController`):**
  - Registered route group prefix `/inventory` guarded by `role:stock_manager` (with system `admin` access via `RoleMiddleware`).
  - Actions: `index`, `create`, `store`, `show`, `edit`, `update`, `receiveBatch`, `storeReceivedBatch`, `disposeBatch`, `adjustStock`, and `movements`.

- **Navigation & Dashboard Integration:**
  - Added `Inventory` and `Delivery Logs` navigation links for `stock_manager` and `admin` in both desktop and mobile navigation bars (`navigation.blade.php`).
  - Added dedicated Stock Manager / Administrator dashboard view (`dashboard.blade.php`) featuring 5 KPI overview cards (Total Formulary, Healthy Stock, Low Stock Alert, Out of Stock, Expiring in 30 Days), formulary stock snapshot, and recent stock activity logs.

- **Views (`resources/views/inventory/`):**
  - `index.blade.php`: Real-time stock monitor, category filters, search, reorder alert counters, batch status badges.
  - `show.blade.php`: Medicine details, FEFO batch allocations table, expiring warning banners, interactive Alpine.js modals for disposal and count adjustment, and audit log.
  - `create.blade.php` & `edit.blade.php`: Full formulary medicine CRUD forms with validation.
  - `receive-batch.blade.php`: Shipment reception form with batch number uniqueness, expiry date calculation, supplier details, and PO remarks.
  - `movements.blade.php`: Filterable stock movement & delivery logs by movement type (All, In, Out, Disposal, Adjustment) and search.

- **Test Suite Verification:**
  - Created `tests/Feature/InventoryModuleTest.php` with 9 tests covering RBAC, CRUD, batch receiving, disposal, adjustment, and movement logs.
  - Full Pest test suite passed: **60 tests passed (227 assertions)**.
  - Formatted with `vendor/bin/pint --dirty --format agent`.



