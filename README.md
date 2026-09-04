# USM Hospital Pharmacy Management System

A capstone project for the **University of Southern Mindanao** that digitises and streamlines hospital pharmacy operations. The system covers digital prescription routing (nurse → pharmacy queue), FEFO-automated inventory and dispensing, a Point-of-Sale interface for both prescription and OTC transactions, and lays the data foundation for a planned dual-risk prediction engine (expiry + stockout), a patient-facing portal, and an AI chatbot for staff and patients.

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.3 / Laravel 13.x |
| Database | MySQL (`usm_pharmacy_system`) |
| Frontend | Blade · Tailwind CSS v3 · Alpine.js |
| Auth scaffold | Laravel Breeze (dev dependency) |
| RBAC | Spatie Laravel Permission v8 |
| Testing | Pest v4 |
| Build tool | Vite 8 via `laravel-vite-plugin` |

## Team & Module Ownership

| Member | Modules |
|---|---|
| Member 1 | Auth/RBAC · Prescription module · Patient Portal |
| Member 2 | Pharmacy/POS module · Inventory Management |
| Member 3 | Dual Risk Prediction Engine · AI Chatbot |

## Documentation

| File | Purpose |
|---|---|
| [docs/SETUP.md](docs/SETUP.md) | Clone-to-running instructions for new contributors |
| [docs/PROGRESS.md](docs/PROGRESS.md) | Current build state — paste into an AI session for fast context |
| [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) | Database schema, module map, and cross-module dependencies |
| [docs/CONTRIBUTING.md](docs/CONTRIBUTING.md) | Branching, PR, and commit conventions |

---

*This project is developed strictly for academic and capstone purposes at the University of Southern Mindanao. Not licensed for production use.*
