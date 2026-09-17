# USM Pharmacy — Project Progress

**Last updated:** 2026-09-17

## Module Status

| Module | Owner | Status | Notes |
|---|---|---|---|
| Auth / RBAC | Member 1 | Done | Spatie Permission integrated; 6 roles seeded; Figma-styled login page with demo autofill & patient registration modal |
| Prescription | Member 1 | Done | Nurse/MedSec create prescriptions → route to pharmacy queue |
| Pharmacy / POS | Member 2 | Done | FEFO dispense, OTC sales, receipt view, all behind `pharmacist` role |
| Inventory | Member 2 | In progress | Stock decrements wired; batch-receiving UI not yet built |
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
- **Prescription & POS Workflows:**
  - Any seeded user can log in; wrong-role routes return 403 via Spatie middleware.
  - Nurses and Medical Secretaries can create a prescription with one or more medicine items. Inline patient registration is supported (creates a `users` + `patients` row in a transaction).
  - Prescriptions list view has status tabs, search, and "mine / all" scope toggle.
  - A `pending` prescription can be routed to the pharmacy queue (stock check runs first); status becomes `routed`.
  - Pharmacists see the routed queue ordered oldest-first. Processing a prescription auto-suggests FEFO batch allocations from `DispensingService::suggestFefoBatches`.
  - Pharmacists can confirm dispensing — `DispensingService::dispensePrescription` decrements `stock_batches.quantity_remaining`, creates `pos_transaction_items`, creates `stock_movements` (`type=out`), and marks the prescription `dispensed`, all inside a single `DB::transaction` with `lockForUpdate`.
  - OTC walk-in sales go through `DispensingService::processOtcSale` — same FEFO and audit logic, no prescription linked.
  - A printable receipt view renders after any completed transaction.
  - Pest feature tests cover: storefront and landing page (`StorefrontTest`), authentication (`AuthenticationTest`), registration (`RegistrationTest`), prescription CRUD & routing (`PrescriptionModuleTest`), POS dispense & OTC (`PharmacyPosModuleTest`), and role-based access (`RoleBasedAccessControlTest`).

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
- **Role names (exact strings):** `nurse`, `medical_secretary`, `pharmacist`, `stock_manager`, `patient`, `admin`.
- **FEFO:** Always sort batches `orderBy('expiry_date', 'asc')` and filter `expiry_date >= today` and `quantity_remaining > 0`. Use `StockBatch::scopeActive()` and `scopeFefo()`.
- **Table naming:** snake_case plural (`pos_transactions`, not `posTransactions`).
- **Tests:** Pest feature tests in `tests/Feature/`; use factories + role assignment; no test should rely on seeded DB data.

---
*Update this file at the end of every work session before opening a PR.*

