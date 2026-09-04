# Architecture

Database schema, module map, and cross-module dependency guide for the USM Hospital Pharmacy Management System.

---

## Database Schema

All tables live in the `usm_pharmacy_system` MySQL database. Full column definitions are in `database/migrations/`.

### Entity Relationship Overview

```
users ──────────────────────┐
  │                         │
  ├── patients ──────────── prescriptions ── prescription_items ── medicines
  │       (user_id)         (patient_id)          (medicine_id)        │
  │                         (encoded_by → users)                       │
  │                                  │                                 stock_batches
  │                         pos_transactions ── pos_transaction_items ──┘
  │                         (cashier_id → users)  (batch_id → stock_batches)
  │                         (prescription_id)
  │
  └── stock_movements
        (created_by → users)
        (medicine_id → medicines)
        (batch_id → stock_batches)
```

### Table Reference

| Table | Owns | Key relationships |
|---|---|---|
| `users` | Auth, profile, `role_id` | Central actor; referenced by `patients`, `prescriptions`, `pos_transactions`, `stock_movements` |
| `roles` / `permissions` / pivot tables | Spatie RBAC | Linked to `users` via Spatie's own pivot tables |
| `patients` | Hospital patient profile, `patient_type` enum (`student`/`resident`), `id_number` | `belongs_to users`; `has_many prescriptions` |
| `medicines` | Medicine catalogue; `reorder_level` used for future stockout alert | `has_many stock_batches`, `has_many prescription_items` |
| `stock_batches` | One row per received batch; tracks `quantity_remaining` and `expiry_date` | `belongs_to medicines`; key table for FEFO logic |
| `prescriptions` | Prescription header; `status` enum: `pending`, `routed`, `dispensed`, `cancelled` | `belongs_to patients`, `belongs_to users (encoded_by)`; `has_many prescription_items` |
| `prescription_items` | One row per medicine line on a prescription | `belongs_to prescriptions`, `belongs_to medicines` |
| `pos_transactions` | POS receipt; `prescription_id` is nullable (null = OTC sale) | `belongs_to prescriptions (nullable)`, `belongs_to users (cashier)` |
| `pos_transaction_items` | Itemised sale lines; links the specific batch used | `belongs_to pos_transactions`, `belongs_to medicines`, `belongs_to stock_batches` |
| `stock_movements` | Immutable audit log; `type` enum: `in`, `out`, `disposal`, `adjustment` | `belongs_to medicines`, `belongs_to stock_batches`, `belongs_to users (created_by)` |

---

## Module Map

### Module 1 — Auth / RBAC (Member 1)

Manages authentication and role-based access control.

| File | Role |
|---|---|
| `database/migrations/2026_09_04_135704_create_permission_tables.php` | Spatie RBAC tables |
| `database/migrations/2026_09_04_135717_add_role_id_to_users_table.php` | Adds `role_id` to `users` |
| `database/seeders/RoleSeeder.php` | Creates the 6 roles |
| `database/seeders/DatabaseSeeder.php` | Creates one demo user per role |
| `app/Models/User.php` | User model with Spatie `HasRoles` |
| `routes/auth.php` | Breeze auth routes (login, register, password reset) |

**Roles:** `nurse`, `medical_secretary`, `pharmacist`, `stock_manager`, `patient`, `admin`

---

### Module 2 — Prescription (Member 1)

Handles prescription creation, editing, and routing to the pharmacy.

| File | Role |
|---|---|
| `app/Http/Controllers/PrescriptionController.php` | CRUD + route-to-pharmacy action |
| `app/Http/Requests/StorePrescriptionRequest.php` | Validates prescription + items + optional new patient |
| `app/Models/Prescription.php` | Status scopes: `pending()`, `routed()`, `dispensed()`, `cancelled()` |
| `app/Models/PrescriptionItem.php` | Line-item model |
| `app/Models/Patient.php` | Patient model; inline registration supported from prescription create form |
| `app/Policies/PrescriptionPolicy.php` | Gate rules: who can create, view, route, cancel |
| `routes/web.php` (prefix `/prescriptions`) | Prescription routes; guarded by `role:nurse|medical_secretary` |

**Status lifecycle:** `pending` → `routed` (via `routeToPharmacy`) → `dispensed` or `cancelled`

---

### Module 3 — Pharmacy / POS (Member 2)

Handles the dispensing queue, FEFO dispensing, OTC sales, and receipts.

| File | Role |
|---|---|
| `app/Http/Controllers/PosController.php` | Queue, process, dispense, OTC, receipt |
| `app/Http/Requests/DispensePrescriptionRequest.php` | Validates batch allocations and payment method |
| `app/Http/Requests/OtcSaleRequest.php` | Validates OTC items and payment method |
| `app/Services/DispensingService.php` | **Core service** — FEFO suggestion, prescription dispensing, OTC sale |
| `app/Models/PosTransaction.php` | Transaction model |
| `app/Models/PosTransactionItem.php` | Item model |
| `app/Policies/PosTransactionPolicy.php` | Gate rules for POS access |
| `routes/web.php` (prefix `/pos`) | POS routes; guarded by `role:pharmacist` |

---

### Module 4 — Inventory (Member 2, in progress)

Manages the medicine catalogue and stock-batch receiving. Currently only stock **out** is wired (via dispensing). Stock **in** (receiving) has no UI yet.

| File | Role |
|---|---|
| `app/Models/Medicine.php` | Catalogue model; `available_stock` accessor aggregates batch quantities |
| `app/Models/StockBatch.php` | Batch model; `scopeActive()` and `scopeFefo()` |
| `app/Models/StockMovement.php` | Audit log model |
| `database/seeders/MedicineAndBatchSeeder.php` | Seeds sample medicines and batches with varied expiry dates |

---

### Module 5 — Dual Risk Prediction Engine (Member 3, not started)

Will consume `stock_movements`, `stock_batches`, and `medicines.reorder_level` to predict stockouts and expiry risk. No code exists yet.

**Expected:** a service class (e.g., `RiskPredictionService`), scheduled Artisan command, and a dashboard widget.

---

### Module 6 — Patient Portal (Member 1, not started)

Will let patients log in and view their prescription history and transaction receipts. No routes or views exist yet.

---

### Module 7 — AI Chatbot (Member 3, not started)

Contextual assistant for pharmacy staff and patients. No code exists yet. Depends on risk engine data being available.

---

## Cross-Module Dependencies

| Dependency | Rule |
|---|---|
| **Stock decrement** | Any module that removes stock (dispensing, expiry disposal) **must** call `DispensingService` methods — do not write direct `stock_batches` decrement queries in new controllers. |
| **Stock movement audit** | Every inventory change must produce a `stock_movements` row. `DispensingService` does this automatically; the inventory-receiving UI must do it too with `type = 'in'`. |
| **FEFO ordering** | Always use `StockBatch::scopeActive()->scopeFefo()` rather than re-writing the `orderBy expiry_date` query manually. |
| **Role middleware** | Use Spatie's `role:` middleware on route groups, not manual `if ($user->hasRole(...))` guards in controllers. Save that for `Gate::authorize()` inside the controller where policy context is needed. |
| **`medicines.is_active`** | `PosController::otcCreate` already filters on this column, but it **does not exist in the migration**. Before the inventory module adds the batch-receiving UI, a migration must add this column. |
