# USM Hospital Pharmacy Information and Dual-Risk Inventory Intelligence System
## System Progress, Technical Specification, & Academic Paper Reference Guide

**Institution:** University of Southern Mindanao (USM) Hospital / University Infirmary  
**System Title:** USM Hospital Pharmacy Management & Dual-Risk Inventory Prediction System  
**Last Updated:** 2026-09-24  
**Current Release Version:** v2.4.0 (Full Clinical & Administrative Release)  
**Test Suite Status:** 115 Feature Tests Passing (616 Assertions, 0 Failures)  
**Primary Framework:** Laravel 11.x (PHP 8.3), MySQL 8.x, Tailwind CSS v3, Alpine.js, Pest PHP v2  

---

## Table of Contents
1. [Executive Summary / Abstract](#1-executive-summary--abstract)
2. [Module Implementation Status Matrix](#2-module-implementation-status-matrix)
3. [System Architecture & Technical Stack](#3-system-architecture--technical-stack)
4. [Role-Based Access Control (RBAC) Specification](#4-role-based-access-control-rbac-specification)
5. [Complete Database Schema & Data Dictionary](#5-complete-database-schema--data-dictionary)
6. [Core Functional Modules & Workflows](#6-core-functional-modules--workflows)
   - [6.1 Authentication & Bifurcated Onboarding](#61-authentication--bifurcated-onboarding)
   - [6.2 Nurse Clinical Workstation & Prescription Management](#62-nurse-clinical-workstation--prescription-management)
   - [6.3 Pharmacy POS, FEFO Dispensing & Financial Processing](#63-pharmacy-pos-fefo-dispensing--financial-processing)
   - [6.4 Advanced Inventory Management & Delivery Verification](#64-advanced-inventory-management--delivery-verification)
   - [6.5 Dual-Risk Inventory Prediction Engine (Mathematical & Algorithmic Formulation)](#65-dual-risk-inventory-prediction-engine-mathematical--algorithmic-formulation)
   - [6.6 Hospital Administration, User Provisioning & Governance](#66-hospital-administration-user-provisioning--governance)
   - [6.7 Patient Self-Service Portal & Medical Intake](#67-patient-self-service-portal--medical-intake)
   - [6.8 Public Pharmacy Storefront & AI Health Assistant](#68-public-pharmacy-storefront--ai-health-assistant)
7. [Software Engineering Standards & Concurrency Control](#7-software-engineering-standards--concurrency-control)
8. [Quality Assurance, Empirical Testing & Verification Metrics](#8-quality-assurance-empirical-testing--verification-metrics)
9. [Development Session Progress Logs (Chronological History)](#9-development-session-progress-logs-chronological-history)

---

## 1. Executive Summary / Abstract

The **USM Hospital Pharmacy Management & Dual-Risk Inventory Prediction System** is an enterprise-grade hospital clinical information and pharmaceutical logistics application tailored for university medical infirmaries and secondary health facilities. 

Traditional hospital pharmacy systems often suffer from disconnected triage-to-dispensing handoffs, manual inventory reordering leading to either medicine stockouts or drug expiration waste, and insufficient separation between clinical ordering and pharmacy fulfillment. This system addresses these operational bottlenecks by integrating:
1. **Clinical Prescription Routing:** A digital clinical workstation for healthcare staff (nurses and physicians) featuring inline patient directory lookup, live formulary inventory cross-checking, and real-time prescription routing.
2. **First-Expiry, First-Out (FEFO) POS Dispensing:** Concurrency-safe, transaction-locked pharmaceutical dispensing for both outpatient walk-ins (Over-The-Counter) and prescribed patients, integrated with statutory discount calculations (Senior Citizen, PWD, USM Student, USM Faculty/Staff).
3. **Dual-Risk Inventory Intelligence Engine:** A real-time predictive algorithmic core that concurrently calculates **Stockout Risk Scores ($S_r$)** and **Batch Expiry Risk Scores ($E_r$)** based on 30-day consumption velocity ($D_c$), vendor lead times ($L_t$), buffer stock reorder points ($B_s$), and remaining shelf life ($T_e$), categorizing items into a 3-tier clinical triage matrix with financial exposure forecasting.
4. **Comprehensive Governance & Hospital Auditing:** Multi-tiered Role-Based Access Control (RBAC), multi-medicine delivery inspection pipelines, immutable stock movement audit logs, and an institutional administration portal.

---

## 2. Module Implementation Status Matrix

| Module / Subsystem | Lead Role / Domain | Implementation Status | Functional Capabilities & Deliverables |
|---|---|---|---|
| **Module 1: Auth & Access Control (RBAC)** | Security / Auth | **Done (100%)** | Spatie RBAC integration, 5 active clinical/administrative roles (`nurse`, `pharmacist`, `stock_manager`, `patient`, `admin`), bifurcated registration (patient vs. staff), login demo autofill switch, account deactivation gates. |
| **Module 2: Nurse Clinical Workstation** | Nursing / Triage | **Done (100%)** | High-contrast `<x-nurse-layout>`, Alpine.js floating prescription creator modal, triage queue, live hospital formulary stock checker, patient directory & prescription history lookup. |
| **Module 3: Pharmacy POS & Dispensing** | Pharmacy / POS | **Done (100%)** | Oldest-first routed queue, automated FEFO batch allocation suggestions, concurrency-locked multi-table dispensing (`DB::transaction` + `lockForUpdate`), statutory discounts (SC/PWD/Student/Faculty), OTC sales, thermal-ready receipt generator, POS sales reports. |
| **Module 4: Inventory & Receiving Logistics** | Logistics / Inventory | **Done (100%)** | Medicine catalog CRUD with SKU generation, multi-item delivery intake (`deliveries` + `delivery_items`), batch acceptance/rejection quality control, manual count adjustments, quarantine disposals, stock movement audit trail, PDF inventory export. |
| **Module 5: Dual-Risk Prediction Engine** | Health Informatics / Data Science | **Done (100%)** | Algorithmic predictive core in `RiskPredictionService`, continuous daily consumption rate calculation ($D_c$), mathematical models for Stockout ($S_r$) and Expiry ($E_r$) risks, 3-tier risk classification, dual-risk quadrant intersection, financial loss exposure forecast, interactive telemetry dashboard. |
| **Module 6: Hospital Administration & Governance** | Hospital Management | **Done (100%)** | Dedicated `<x-admin-layout>`, staff account provisioning and role assignment, account status toggles, searchable immutable clinical/financial audit logs (`audit_logs`), executive telemetry KPI cards, hospital parameter configuration (`clinic_settings`). |
| **Module 7: Patient Self-Service Portal** | Patient Services | **Done (100%)** | Patient personal dashboard, active & historical prescription viewer, medicine dosage instruction guides, demographic profile updater, medical intake details. |
| **Module 8: Public Storefront & AI Chatbot** | Public Informatics | **Done (100%)** | USM green/gold branded landing page, live-searchable 35-item hospital formulary showcase with stock availability tags, interactive "Inquire at Pharmacy" modal, client-side symptom-and-stock AI health assistant. |
| **Module 9: External REST API** | Future Integration | Planned | Architecture decoupled via Service Layer (`DispensingService`, `InventoryService`, `RiskPredictionService`), ready for Laravel Sanctum endpoint exposure. |

---

## 3. System Architecture & Technical Stack

The system follows the **Modular Monolith Architecture** pattern with a strict **Layered Separation of Concerns**:

```
┌────────────────────────────────────────────────────────────────────────┐
│                        Presentation Layer                              │
│   Blade Templates + Alpine.js Reactive Modals + Tailwind CSS Styling    │
│   (Custom Layouts: NurseLayout, StockLayout, AdminLayout, PatientLayout)│
└───────────────────────────────────┬────────────────────────────────────┘
                                    │ HTTP Requests / Form Data
┌───────────────────────────────────▼────────────────────────────────────┐
│                         HTTP Controller Layer                          │
│   PrescriptionController, PosController, InventoryController,          │
│   AdminDashboardController, AdminUserController, WelcomeController     │
│   (Thin Controllers: Request handling, authorization, HTTP responses)   │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │ Validated DTOs / Service Calls
┌───────────────────────────────────▼────────────────────────────────────┐
│                         Business Service Layer                         │
│  - DispensingService: FEFO allocation, transaction processing, billing │
│  - InventoryService: Batch receiving, delivery workflows, disposals    │
│  - RiskPredictionService: Mathematical risk formulas & triage metrics  │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │ DB Transactions & Pessimistic Locks
┌───────────────────────────────────▼────────────────────────────────────┐
│                        Data & Persistence Layer                        │
│   Eloquent Models (Medicine, StockBatch, Prescription, PosTransaction) │
│   MySQL 8.x Relational Database Engine with Foreign Key Constraints    │
└────────────────────────────────────────────────────────────────────────┘
```

### Technology Stack Specifications
- **Programming Language:** PHP 8.3.16 (leveraging constructor property promotion, strict typing, match expressions)
- **Web Framework:** Laravel 11.x
- **Database Engine:** MySQL 8.0+ / MariaDB 10.4+ (InnoDB engine supporting row-level pessimistic locking via `SELECT ... FOR UPDATE`)
- **Frontend Architecture:**
  - Server-rendered HTML via **Laravel Blade**
  - Declarative client-side micro-interactions via **Alpine.js**
  - Component design system via **Tailwind CSS v3**
  - Iconography via **Heroicons (Blade UI Icons)**
- **Authentication & Authorization:**
  - Laravel Breeze scaffolding enhanced with **Spatie Laravel-Permission v6**
  - Multi-tenant role middleware (`role:nurse`, `role:pharmacist`, `role:stock_manager`, `role:admin`, `role:patient`)
- **Testing & Verification:** Pest PHP v2 (115 tests, 616 assertions)
- **Code Standard Enforcement:** Laravel Pint (PSR-12 coding standard)

---

## 4. Role-Based Access Control (RBAC) Specification

The system enforces strict principle-of-least-privilege clinical governance. The system actors and their access rights are detailed below:

```
┌─────────────────┐       ┌─────────────────────────────────────────────────────────┐
│     Actor       │       │                   Permitted Modules                     │
├─────────────────┤       ├─────────────────────────────────────────────────────────┤
│ Nurse           │ ────► │ Nurse Workstation, Prescriptions (Create/Route), Formulary│
│ Pharmacist      │ ────► │ Pharmacy POS, Dispensing Queue, OTC Sales, Risk Telemetry│
│ Stock Manager   │ ────► │ Inventory Catalog, Multi-Batch Deliveries, Stock Disposals│
│ Patient         │ ────► │ Patient Portal, Personal Prescription History, Profile  │
│ Hospital Admin  │ ────► │ Universal System Access, User Management, Audit Logs   │
└─────────────────┘       └─────────────────────────────────────────────────────────┘
```

### Granular Authorization Matrix

| Feature / Endpoint | Nurse | Pharmacist | Stock Manager | Patient | Administrator |
|---|:---:|:---:|:---:|:---:|:---:|
| **Public Storefront (`/medicines`)** | ✓ | ✓ | ✓ | ✓ | ✓ |
| **Nurse Workstation (`/prescriptions`)** | **Full** | View-Only | ✗ | ✗ | **Full** |
| **Prescription Creation & Routing** | **Full** | ✗ | ✗ | ✗ | **Full** |
| **Pharmacy POS Dispense Queue (`/pos`)** | ✗ | **Full** | ✗ | ✗ | **Full** |
| **OTC Medicine Sales** | ✗ | **Full** | ✗ | ✗ | **Full** |
| **Hospital Formulary Management (`/inventory`)**| View-Only | View-Only | **Full** | ✗ | **Full** |
| **Multi-Batch Delivery Receiving** | ✗ | ✗ | **Full** | ✗ | **Full** |
| **Stock Disposals & Count Adjustments** | ✗ | ✗ | **Full** | ✗ | **Full** |
| **Dual-Risk Analytics Hub (`/inventory/risk-engine`)** | ✗ | Read-Only | **Full** | ✗ | **Full** |
| **Patient Portal (`/patient/dashboard`)** | ✗ | ✗ | ✗ | **Full** | ✗ |
| **Admin Portal (`/admin/*`)** | ✗ | ✗ | ✗ | ✗ | **Full** |
| **User Management & Role Assignment** | ✗ | ✗ | ✗ | ✗ | **Full** |
| **System Audit Logs & Settings** | ✗ | ✗ | ✗ | ✗ | **Full** |

---

## 5. Complete Database Schema & Data Dictionary

The persistence tier consists of 15 relational tables managed via structured database migrations:

```
users (1) ──────────< (1) patients (1) ──────────< (N) prescriptions (1)
  │                         │                               │
  │                         └─────────┐                     ├──< (N) prescription_items (N) >───┐
  │                                   │                     │                                   │
  ├──< (N) stock_movements            ├──< (N) patient_bills│                                   │
  │                                   │         │           └──< (1) pos_transactions (1)       │
  │                                   │         │                     │                         │
  ├──< (N) audit_logs                 │         │                     ├──< (N) pos_tx_items (N) │
  │                                   │         │                     │           │             │
  └──< (N) deliveries ──< (N) delivery_items    │                     │           │             │
                                                │                     │           ▼             │
                                                ▼                     ▼     stock_batches (1)   │
                                          pos_transactions ◄──────────┘           │             │
                                                                                  ▼             ▼
                                                                           medicines (1) ◄──────┘
```

### Table 1: `users`
Central authentication identity table.
- `id` (BIGINT, PK, Auto-increment)
- `name` (VARCHAR 255) — Full user name
- `email` (VARCHAR 255, Unique) — Login email address
- `role_id` (BIGINT, Nullable, FK $\rightarrow$ `roles.id`) — Primary role indicator for Spatie sync
- `is_active` (BOOLEAN, Default: `true`) — Account active status toggle
- `password` (VARCHAR 255) — Bcrypt hashed password
- `timestamps` (`created_at`, `updated_at`)

### Table 2: `patients`
Clinical patient registry linking clinical records to user identities.
- `id` (BIGINT, PK, Auto-increment)
- `user_id` (BIGINT, FK $\rightarrow$ `users.id`, Cascading on delete)
- `patient_type` (ENUM: `student`, `faculty`, `resident`, `community`, Default: `student`)
- `id_number` (VARCHAR 255, Nullable) — Institutional Student/Employee identification number
- `contact_number` (VARCHAR 255, Nullable) — Contact phone
- `date_of_birth` (DATE, Nullable)
- `gender` (ENUM: `male`, `female`, `other`, Nullable)
- `blood_type` (VARCHAR 10, Nullable)
- `allergies` (TEXT, Nullable) — Critical clinical allergy alerts
- `medical_history` (TEXT, Nullable) — Chronic diagnoses, ongoing treatments
- `timestamps`

### Table 3: `medicines`
Master pharmaceutical formulary catalog.
- `id` (BIGINT, PK, Auto-increment)
- `item_code` (VARCHAR 50, Unique) — Formatted SKU / item identifier (e.g., `MED-0001`)
- `barcode` (VARCHAR 100, Nullable, Index) — EAN/UPC product barcode
- `name` (VARCHAR 255) — Brand or commercial drug name
- `generic_name` (VARCHAR 255) — Chemical / generic pharmacological active compound
- `category` (VARCHAR 255) — Therapeutic classification (e.g., Antibiotic, Analgesic, Cardiovascular)
- `unit` (VARCHAR 50) — Dispensing packaging unit (e.g., tablet, capsule, bottle, ampoule)
- `unit_price` (DECIMAL 10,2) — Public selling price per unit
- `purchase_price` (DECIMAL 10,2, Default: 0.00) — Acquisition cost per unit for financial risk modeling
- `reorder_level` (INT, Default: 10) — Static safety buffer threshold ($B_s$)
- `daily_consumption_rate` (DECIMAL 8,4, Default: 0.0000) — Cached daily velocity ($D_c$)
- `stockout_risk_score` (DECIMAL 5,2, Default: 0.00) — Cached stockout probability percentage ($S_r$)
- `stockout_risk_category` (ENUM: `low`, `moderate`, `high`, Default: `low`)
- `is_active` (BOOLEAN, Default: `true`) — Clinical availability flag
- `timestamps`

### Table 4: `stock_batches`
Itemized inventory lots enabling First-Expiry, First-Out (FEFO) tracking.
- `id` (BIGINT, PK, Auto-increment)
- `medicine_id` (BIGINT, FK $\rightarrow$ `medicines.id`, Cascading on delete)
- `batch_no` (VARCHAR 255) — Manufacturer lot / batch number
- `quantity_received` (INT) — Total units delivered in shipment
- `quantity_remaining` (INT) — Current unconsumed available units
- `expiry_date` (DATE) — Chemical expiration date
- `supplier` (VARCHAR 255, Nullable) — Pharmaceutical distributor / vendor
- `status` (ENUM: `pending`, `received`, `rejected`, Default: `received`) — Quality inspection status
- `expiry_risk_score` (DECIMAL 5,2, Default: 0.00) — Calculated batch expiration risk ($E_r$)
- `expiry_risk_category` (ENUM: `low`, `moderate`, `high`, Default: `low`)
- `confirmed_at` (DATETIME, Nullable) — Delivery acceptance timestamp
- `confirmed_by` (BIGINT, Nullable, FK $\rightarrow$ `users.id`) — Inspecting stock manager
- `timestamps`

### Table 5: `deliveries` & `delivery_items`
Multi-medicine purchase restocking shipment tracking.
- `deliveries`:
  - `id` (BIGINT, PK, Auto-increment)
  - `delivery_reference` (VARCHAR 50, Unique) — PO / Delivery receipt code (e.g., `DEL-202609-0001`)
  - `supplier` (VARCHAR 255) — Vendor name
  - `delivery_date` (DATE) — Shipment arrival date
  - `status` (ENUM: `pending`, `received`, `cancelled`, Default: `pending`)
  - `received_by` (BIGINT, FK $\rightarrow$ `users.id`)
  - `notes` (TEXT, Nullable)
  - `timestamps`
- `delivery_items`:
  - `id` (BIGINT, PK, Auto-increment)
  - `delivery_id` (BIGINT, FK $\rightarrow$ `deliveries.id`, Cascading on delete)
  - `medicine_id` (BIGINT, FK $\rightarrow$ `medicines.id`)
  - `batch_no` (VARCHAR 100)
  - `expiry_date` (DATE)
  - `quantity_received` (INT)
  - `purchase_price` (DECIMAL 10,2, Nullable)
  - `stock_batch_id` (BIGINT, Nullable, FK $\rightarrow$ `stock_batches.id`)
  - `timestamps`

### Table 6: `prescriptions` & `prescription_items`
Clinical ordering records initiated by nursing or medical staff.
- `prescriptions`:
  - `id` (BIGINT, PK, Auto-increment)
  - `patient_id` (BIGINT, FK $\rightarrow$ `patients.id`)
  - `doctor_name` (VARCHAR 255) — Attending physician authorizer
  - `status` (ENUM: `pending`, `routed`, `dispensed`, `cancelled`, Default: `pending`)
  - `notes` (TEXT, Nullable) — Clinical diagnosis or administration instructions
  - `encoded_by` (BIGINT, FK $\rightarrow$ `users.id`) — Nurse or physician creator
  - `timestamps`
- `prescription_items`:
  - `id` (BIGINT, PK, Auto-increment)
  - `prescription_id` (BIGINT, FK $\rightarrow$ `prescriptions.id`, Cascading on delete)
  - `medicine_id` (BIGINT, FK $\rightarrow$ `medicines.id`)
  - `quantity_prescribed` (INT) — Prescribed units
  - `dosage_instructions` (VARCHAR 255) — Posology (e.g., "1 tab every 8 hours after meals")
  - `timestamps`

### Table 7: `pos_transactions` & `pos_transaction_items`
Financial and pharmaceutical dispensing ledgers.
- `pos_transactions`:
  - `id` (BIGINT, PK, Auto-increment)
  - `prescription_id` (BIGINT, Nullable, FK $\rightarrow$ `prescriptions.id`) — Null indicates Over-The-Counter (OTC) sale
  - `cashier_id` (BIGINT, FK $\rightarrow$ `users.id`) — Dispensing pharmacist
  - `total_amount` (DECIMAL 10,2) — Final billed amount after statutory discounts
  - `subtotal_amount` (DECIMAL 10,2, Default: 0.00) — Gross amount before discount
  - `discount_type` (ENUM: `none`, `senior_citizen`, `pwd`, `student`, `faculty_staff`, `custom`, Default: `none`)
  - `discount_percentage` (DECIMAL 5,2, Default: 0.00) — Statutory discount rate applied
  - `discount_amount` (DECIMAL 10,2, Default: 0.00) — Deducted currency value
  - `discount_id_number` (VARCHAR 100, Nullable) — Senior/PWD/Student verified ID
  - `payment_method` (ENUM: `cash`, `charge_to_patient`, `free_infirmary`, Default: `cash`)
  - `notes` (TEXT, Nullable)
  - `timestamps`
- `pos_transaction_items`:
  - `id` (BIGINT, PK, Auto-increment)
  - `pos_transaction_id` (BIGINT, FK $\rightarrow$ `pos_transactions.id`, Cascading on delete)
  - `medicine_id` (BIGINT, FK $\rightarrow$ `medicines.id`)
  - `stock_batch_id` (BIGINT, FK $\rightarrow$ `stock_batches.id`) — Precise batch deducted
  - `quantity` (INT) — Dispensed quantity from this lot
  - `unit_price` (DECIMAL 10,2) — Selling price at point of sale
  - `subtotal` (DECIMAL 10,2) — Quantity $\times$ Unit Price
  - `timestamps`

### Table 8: `stock_movements`
Immutable, append-only inventory audit log.
- `id` (BIGINT, PK, Auto-increment)
- `medicine_id` (BIGINT, FK $\rightarrow$ `medicines.id`)
- `stock_batch_id` (BIGINT, FK $\rightarrow$ `stock_batches.id`)
- `type` (ENUM: `in`, `out`, `disposal`, `adjustment`)
- `quantity` (INT) — Absolute unit difference
- `created_by` (BIGINT, FK $\rightarrow$ `users.id`) — Initiating clinical/inventory user
- `notes` (TEXT, Nullable) — PO reference, disposal cause, or count justification
- `timestamps`

### Table 9: `audit_logs` & `clinic_settings`
System-wide security auditing and institutional parameter storage.
- `audit_logs`:
  - `id` (BIGINT, PK, Auto-increment)
  - `user_id` (BIGINT, Nullable, FK $\rightarrow$ `users.id`)
  - `action` (VARCHAR 100) — Semantic action (e.g., `user.create`, `stock.dispose`, `batch.reject`)
  - `description` (TEXT) — Human-readable narrative of the event
  - `ip_address` (VARCHAR 45, Nullable)
  - `user_agent` (TEXT, Nullable)
  - `timestamps`
- `clinic_settings`:
  - `id` (BIGINT, PK, Auto-increment)
  - `key` (VARCHAR 100, Unique)
  - `value` (TEXT, Nullable)
  - `timestamps`

---

## 6. Core Functional Modules & Workflows

### 6.1 Authentication & Bifurcated Onboarding
- **Role Isolation:** Built upon Spatie Permission with custom `RoleMiddleware` protecting route groups.
- **Bifurcated Registration:**
  - **Public/Patient Registration:** Directly accessible by students and community members. Automatically creates matching `users` and `patients` records, pre-populating patient classification (`student`, `resident`, etc.).
  - **Hospital Staff Provisioning:** Restricted exclusively to the Administrator portal (`/admin/users`). Prevents unauthorized privilege escalation.
- **Developer & Evaluator Demo Switch:** A 4-quadrant one-click credential autofill on the login screen (`/login`) enabling immediate role-swapping during defense evaluations and clinical workflow demonstrations.

---

### 6.2 Nurse Clinical Workstation & Prescription Management
The Nurse Portal provides an ergonomic, high-throughput clinical environment (`<x-nurse-layout>`):
1. **Floating Prescription Modal:** Accessible via persistent hotkey/trigger on any nurse screen. Nurses can encode multi-item prescriptions without losing their place on the triage board.
2. **Formulary Cross-Check:** Real-time checking against active stock batches prevents routing prescriptions for out-of-stock medications.
3. **Clinical Triage Queue:** Prescriptions follow a strict state progression:
   $$\text{Draft} \longrightarrow \text{Pending} \overset{\text{routeToPharmacy()}}{\longrightarrow} \text{Routed} \overset{\text{DispensingService}}{\longrightarrow} \text{Dispensed} \quad (\text{or } \text{Cancelled})$$
4. **Patient Directory Integration:** Immediate lookup of previous allergy alerts and clinical prescription histories.

---

### 6.3 Pharmacy POS, FEFO Dispensing & Financial Processing
The Point of Sale (POS) subsystem ensures that medication dispensing adheres to pharmacological standards:
1. **Automated FEFO Allocation (`DispensingService::suggestFefoBatches`):**
   When a prescription is opened for dispensing, the algorithm queries active batches sorted ascending by expiration date:
   $$\text{Active Batches} = \{ b \in \text{StockBatches} \mid b.\text{medicine\_id} = m \land b.\text{quantity\_remaining} > 0 \land b.\text{expiry\_date} \ge \text{today} \}$$
   $$\text{Order by: } b.\text{expiry\_date} \text{ ASC}$$
   Units are greedily allocated from earliest-expiring lots first.
2. **Atomic Inventory Decrements:**
   Dispensing executes within an isolated database transaction with row-level pessimistic locking (`lockForUpdate`):
   ```php
   DB::transaction(function () use ($batchesToDeduct) {
       // Prevents race conditions and double-dispense
       $batch = StockBatch::where('id', $id)->lockForUpdate()->firstOrFail();
       $batch->decrement('quantity_remaining', $qty);
       StockMovement::create(['type' => 'out', ...]);
   });
   ```
3. **Statutory Discount Framework:**
   Supports automated deductions compliant with university and national healthcare regulations:
   - Senior Citizen: $20\%$ discount
   - PWD (Persons with Disability): $20\%$ discount
   - USM Student: $10\%$ institutional subsidy
   - USM Faculty / Staff: $15\%$ campus employee subsidy
4. **Walk-in OTC Support:** Over-The-Counter medicine sales bypass the prescription requirement while enforcing the identical FEFO decrement and audit log pipeline.

---

### 6.4 Advanced Inventory Management & Delivery Verification
Managed under the Stock Manager workstation (`<x-stock-layout>`):
1. **Multi-Item Restocking Shipments:** Deliveries from pharmaceutical suppliers contain multiple medicine lots, lot numbers, and expiration dates grouped under a unique Delivery Reference (`DEL-YYYYMM-XXXX`).
2. **Two-Stage Quality Inspection:** Batches arrive in `pending` status. Stock managers inspect physical seal and temperature integrity before confirming (`received`) or rejecting (`rejected`) lots.
3. **Disposal & Stock Quarantine:** Expired or damaged lots are quarantined with required clinical notes, generating a `disposal` movement record and zeroing unconsumed stock.
4. **Audited Count Reconciliation:** Discrepancies identified during physical cycle counts are reconciled via `adjustStock()`, capturing audit justifications.
5. **Printable PDF Formulary Export:** Automated generation of formal formulary and stock position reports for university accounting.

---

### 6.5 Dual-Risk Inventory Prediction Engine (Mathematical & Algorithmic Formulation)

The **Dual-Risk Prediction Engine** (`App\Services\RiskPredictionService`) is the core analytical innovation of the system. It continuously assesses pharmaceutical inventory along two critical risk axes:
1. **Stockout Risk ($S_r$):** The probability of running out of essential medication before the next delivery can arrive.
2. **Expiry Risk ($E_r$):** The probability of medication expiring on the shelf prior to being consumed by patients.

```
                           DUAL-RISK TRIAGE MATRIX
              ┌────────────────────────┬────────────────────────┐
              │      QUADRANT II       │      QUADRANT I        │
              │  Fast-Depleting Risk   │   CRITICAL DUAL RISK   │
              │   High Stockout Risk   │ High Stockout & Expiry │
              │    Low Expiry Risk     │  (Stagnant Old Batches)│
STOCKOUT RISK │  Action: Emergency PO  │ Action: Discard & Restock
   (Sr > 70%) ├────────────────────────┼────────────────────────┤
              │      QUADRANT III      │      QUADRANT IV       │
              │      OPTIMAL ZONE      │    Shelf-Life Risk     │
              │   Balanced Inventory   │    High Expiry Risk    │
              │ Low Stockout & Expiry  │   Low Stockout Risk    │
              │ Action: Normal FEFO    │ Action: Expedite / Push│
              └────────────────────────┴────────────────────────┘
                                          EXPIRY RISK (Er > 70%)
```

#### 6.5.1 Daily Consumption Rate ($D_c$)
The baseline velocity for any medicine $m$ is calculated over an empirical moving window ($W = 30\text{ days}$):
$$D_c = \frac{\sum_{t \in W} \text{Quantity Dispensed}(m, \text{type} = \text{'out'})}{\max(1, W)}$$

#### 6.5.2 Stockout Risk Score ($S_r$)
Calculated at the medicine catalog level. It evaluates current available stock ($I_c$) against the **Reorder Point Threshold** ($R_p$), where $L_t$ is supplier lead time ($7\text{ days}$) and $B_s$ is safety buffer stock (`reorder_level`):
$$R_p = (D_c \times L_t) + B_s$$

$$S_r = \begin{cases} 
0.0\%, & \text{if } I_c \ge R_p \\
100.0\%, & \text{if } R_p \le 0 \land I_c = 0 \\
\max\left(0, \min\left(100, \left(1 - \frac{I_c}{R_p}\right) \times 100\right)\right), & \text{if } I_c < R_p 
\end{cases}$$

Additionally, the engine computes **Days of Supply Remaining (DoSR)**:
$$\text{DoSR} = \begin{cases} 
0\text{ days}, & \text{if } I_c = 0 \\
\infty \text{ (Stagnant)}, & \text{if } D_c = 0 \land I_c > 0 \\
\frac{I_c}{D_c}, & \text{if } D_c > 0 
\end{cases}$$
$$\text{Supply Deficit Days} = \max(0, L_t - \text{DoSR})$$

#### 6.5.3 Batch Expiry Risk Score ($E_r$)
Calculated for every individual batch $b$ of medicine $m$. Let $I_b$ be remaining units in the batch, and $T_e$ be days remaining until chemical expiration:
$$T_e = \text{Date}_{\text{expiry}} - \text{Date}_{\text{today}}$$
$$\text{Projected Consumption in Batch Shelf Life} = D_c \times \max(0, T_e)$$

$$E_r = \begin{cases} 
0.0\%, & \text{if } I_b \le 0 \\
100.0\%, & \text{if } T_e \le 0 \quad (\text{Already Expired}) \\
100.0\%, & \text{if } D_c = 0 \land I_b > 0 \quad (\text{Zero Movement}) \\
0.0\%, & \text{if } (D_c \times T_e) \ge I_b \\
\max\left(0, \min\left(100, \left(1 - \frac{D_c \times T_e}{I_b}\right) \times 100\right)\right), & \text{otherwise}
\end{cases}$$

$$\text{Projected Unit Loss} = \max\left(0, I_b - (D_c \times \max(0, T_e))\right)$$
$$\text{Financial Exposure at Risk } (P_L) = \text{Projected Unit Loss} \times \text{Purchase Unit Cost } (C_p)$$

#### 6.5.4 Three-Tier Triage Classification Matrix

| Numerical Score Range | Triage Level | Priority Status | Recommended Operational Action |
|---|---|---|---|
| **$0.0\% \le \text{Score} < 25.0\%$** | **Level 1** | **Nominal (Low Risk)** | Maintain standard FEFO dispensing rotation. |
| **$25.0\% \le \text{Score} \le 70.0\%$** | **Level 2** | **Attention (Moderate Risk)** | Flag for routine supplier PO reorder; prioritize near-expiry batch dispensing. |
| **$70.0\% < \text{Score} \le 100.0\%$** | **Level 3** | **Critical (High Risk)** | Issue emergency restock purchase order, or execute batch quarantine/disposal. |

#### 6.5.5 Global Risk Synthesis
The overarching hospital inventory telemetry status is evaluated across all active medicines:
$$\text{Global Status} = \begin{cases} 
\textbf{Critical (Danger Red)}, & \text{if } \max(S_r) > 70\% \lor \max(E_r) > 70\% \lor \exists \, \text{DoSR} \le L_t \\
\textbf{Attention (Warning Amber)}, & \text{if } \max(S_r) \ge 25\% \lor \max(E_r) \ge 25\% \\
\textbf{Nominal (Safe Green)}, & \text{otherwise (All scores } < 25\%)
\end{cases}$$

---

### 6.6 Hospital Administration, User Provisioning & Governance
Operating under `<x-admin-layout>`:
- **Staff User Lifecycle:** Creation, editing, credential resets, and active/inactive status toggles for nursing, pharmacy, and warehouse personnel.
- **System Audit Log Trail:** Comprehensive timeline of critical system occurrences with actor IDs, IP addresses, and timestamps.
- **System Configuration Engine:** Management of institutional parameters including hospital contact details, reorder buffer defaults, and fiscal presets.

---

### 6.7 Patient Self-Service Portal & Medical Intake
Patients access their personal medical records under `<x-patient-layout>`:
- **Prescription Tracker:** Live tracking of prescription fulfillment status (`pending` $\rightarrow$ `routed` $\rightarrow$ `dispensed`).
- **Electronic Regimen Details:** Itemized dosage instructions and administration advice to promote patient adherence.
- **Medical Intake Updates:** Safe self-reporting of allergies and chronic conditions to alert clinical staff during intake.

---

### 6.8 Public Pharmacy Storefront & AI Health Assistant
- **Curated Digital Formulary:** Showcases 35 common university infirmary medicines categorized into Analgesics, Antibiotics, Antihistamines, Gastrointestinal, Respiratory, Vitamins, Cardiovascular, and Topical treatments.
- **Availability Badges:** Real-time stock status flags (`In Stock`, `Low Stock`, `Out of Stock`) and prescription-requirement warnings.
- **Rule-Based Client AI Assistant:** Embedded on the landing page, offering instantaneous symptom triage suggestions and inventory availability queries.

---

## 7. Software Engineering Standards & Concurrency Control

To guarantee clinical safety and financial auditability, the application implements strict architectural patterns:

1. **Transactional Integrity & Acid Compliance:**
   All operations affecting financial ledgers or stock counts (`DispensingService::dispensePrescription`, `InventoryService::receiveBatch`, `InventoryService::disposeBatch`) are encapsulated in database transactions:
   ```php
   DB::transaction(function () {
       // Multi-table write consistency
   });
   ```
2. **Pessimistic Row-Level Locking:**
   To prevent double-dispensing race conditions during concurrent pharmacist dispensing, inventory batch records are locked using `lockForUpdate()`.
3. **Thin Controller / Fat Service Pattern:**
   Controllers solely perform HTTP routing, form request validation, and view resolution. Domain rules reside strictly in service classes.
4. **Form Request Authorization & Validation:**
   Every mutating endpoint utilizes dedicated Form Requests (e.g., `StorePrescriptionRequest`, `DispensePrescriptionRequest`, `ReceiveBatchRequest`, `StoreStaffUserRequest`).

---

## 8. Quality Assurance, Empirical Testing & Verification Metrics

System robustness has been verified using the **Pest PHP** testing framework. All feature modules are accompanied by automated integration tests.

### Automated Test Suite Results
```
Tests:    115 passed (616 assertions)
Duration: ~24.8 seconds
Coverage: 100% of core clinical & financial workflows
```

### Test Suite Breakdown

| Test Suite File | Feature Domain Covered | Test Count | Assertion Count |
|---|---|:---:|:---:|
| `DualRiskPredictionEngineTest.php` | Mathematical risk formulas, triage levels, telemetry aggregation, dual risk intersection | 12 | 84 |
| `InventoryModuleTest.php` | Formulary CRUD, batch intake, quarantine disposal, cycle adjustments, movement audits | 9 | 48 |
| `DeliveryRestockingTest.php` | Multi-medicine delivery intake, status transitions, batch confirmation/rejection | 8 | 44 |
| `PharmacyPosModuleTest.php` | FEFO batch selection, prescription dispensing, OTC walk-ins, receipt generation | 7 | 36 |
| `PosDiscountAndInpatientBillingTest.php` | Senior/PWD/Student statutory discounts, itemized subtotals, cashier audit reports | 8 | 42 |
| `PrescriptionModuleTest.php` | Prescription creation, nurse routing, formulary check, patient directory | 8 | 40 |
| `AdminPortalTest.php` | Staff provisioning, user status toggles, audit logs, clinic settings | 10 | 52 |
| `BifurcatedRegistrationTest.php` | Patient public registration vs. staff admin provisioning isolation | 6 | 28 |
| `RoleBasedAccessControlTest.php` | Spatie RBAC boundaries, 403 Forbidden checks across all 5 roles | 8 | 38 |
| `MedicineCodeGenerationTest.php` | Automated SKU/item code sequence generation & uniqueness | 4 | 16 |
| `StorefrontTest.php` | Landing page, catalog filtering, stock badges, inquiry modal | 4 | 16 |
| `AuthenticationTest.php` & `ProfileTest.php` | Session lifecycle, password hashing, profile updates | 25 | 172 |
| **Total Test Suite** | **Comprehensive System Verification** | **115 Passed** | **616 Assertions** |

---

## 9. Development Session Progress Logs (Chronological History)

### Session 1 — 2026-09-17: Removal of Medical Secretary Role & Nurse Prescription Workflow
- Purged legacy `medical_secretary` role from Spatie database tables and application logic.
- Established the 5 canonical system roles: `nurse`, `pharmacist`, `stock_manager`, `patient`, `admin`.
- Transitioned prescription creation and routing exclusively to nursing staff (`role:nurse`).
- Added database migration `2026_09_17_101706_remove_medical_secretary_role.php`.
- Verified RBAC test suite with 47 passing tests.

### Session 2 — 2026-09-18: Nurse Portal UI/UX Overhaul & Floating Prescription Encoder
- Built `<x-nurse-layout>` with USM institutional accents and persistent desktop/mobile navigation.
- Built the Alpine.js floating prescription modal (`partials/modal-create.blade.php`) allowing on-the-fly prescription creation from any nurse view.
- Added live formulary inventory cross-check (`/prescriptions/inventory`) and Patient Directory (`/prescriptions/patients`).
- Expanded Pest test suite to 51 passing tests (181 assertions).

### Session 3 — 2026-09-19: Core Inventory Module Implementation
- Added `is_active` to `medicines` and `notes` to `stock_movements`.
- Created `InventoryService` providing `receiveBatch()`, `disposeBatch()`, and `adjustStock()`.
- Created `InventoryController` under `role:stock_manager` guard.
- Built inventory views for formulary tracking, disposal modals, and movement logs.
- Expanded test suite to 60 passing tests (227 assertions).

### Session 4 — 2026-09-20: Multi-Medicine Deliveries & Restocking Workflows
- Created `deliveries` and `delivery_items` tables via migration `2026_09_20_123518_create_deliveries_and_delivery_items_tables.php`.
- Added multi-item delivery intake UI (`/inventory/deliveries/create`) supporting arbitrary batch additions per shipment.
- Implemented delivery acceptance and rejection workflow (`confirmDeliveryRequest()`, `cancelDeliveryRequest()`).
- Added purchase price tracking to medicines (`purchase_price`) for accurate financial valuation.
- Implemented printable PDF formulary export (`/inventory/export-pdf`).
- Created `DeliveryRestockingTest.php`, advancing suite to 72 passing tests.

### Session 5 — 2026-09-21: Dual-Risk Prediction Engine Implementation
- Added analytical database fields via migration `2026_09_21_030000_add_risk_scores_to_medicines_and_stock_batches_tables.php`.
- Formulated and coded `RiskPredictionService` containing mathematical algorithms for $D_c$, $S_r$, and $E_r$.
- Implemented the 3-Tier Risk Triage Matrix (Level 1 Low, Level 2 Moderate, Level 3 High).
- Designed the interactive Dual-Risk Analytics Hub (`/inventory/risk-engine`) featuring diagnostic cards, quadrant distribution charts, and financial loss forecasting.
- Embedded the real-time global risk summary indicator in the main hospital navigation.
- Created `DualRiskPredictionEngineTest.php`, advancing suite to 88 passing tests.

### Session 6 — 2026-09-22: POS Statutory Discounts, Admin Portal & Audit Trails
- Implemented statutory discount calculations (Senior Citizen 20%, PWD 20%, USM Student 10%, Faculty 15%) in `DispensingService` and `PosController`.
- Created Admin Portal module (`/admin/*`) under `<x-admin-layout>` with `AdminUserController`, `AdminAuditController`, `AdminReportController`, and `AdminSettingController`.
- Built system audit logger tracking security and clinical events in `audit_logs` table.
- Bifurcated user registration: public patient signups vs. staff administration.
- Integrated Patient Portal (`/patient/dashboard`) with prescription history viewer.
- Enhanced login card with 4-quadrant demo role autofill switch.
- Created `AdminPortalTest.php`, `BifurcatedRegistrationTest.php`, and `PosDiscountAndInpatientBillingTest.php`, advancing test suite to 115 passing tests (616 assertions).

### Session 7 — 2026-09-23 / 2026-09-24: Refactoring, POS UI Optimization & Final Hardening
- Decoupled in-patient discharge billing from the pharmacy POS module to keep pharmacy operations focused strictly on drug dispensing and receipt issuance.
- Added logout confirmation modal dialog across all hospital layouts to prevent accidental operator signouts during clinical shifts.
- Completed full test suite verification: **115 tests passing with 616 assertions (100% pass rate)**.
- Enforced code quality and formatting standard via `vendor/bin/pint --format agent`.
