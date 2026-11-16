# WebotApp-AutoERP 🚗⚙️
### Automobile Garage & Workshop Management ERP Suite
**Production-Ready Enterprise Software for Multi-Brand Garages, Two/Four-Wheeler Workshops & Dealerships**

---

## 📌 Overview

**WebotApp-AutoERP** is a full-featured, enterprise-grade Automobile Workshop Management ERP built with **Laravel 11**, **PHP 8.4**, and **MySQL**. It covers the complete lifecycle of vehicle maintenance operations: from online appointment booking and 360° vehicle check-in to automated job cards, live floor lift Kanban monitoring, transaction-safe inventory ledgers, multi-point quality checks, gate passes, and Indian GST tax invoicing.

---

## 🌟 Key Features & Modules

### 1. Multi-Role RBAC & Tailored Dashboards
- **Executive Operations Dashboard** (Admin & Workshop Manager): 360° garage throughput, live bay utilization, Chart.js pipeline distribution, revenue velocity, and receivables.
- **Service Advisory Desk** (Service Advisor): Today's booked appointments, vehicle check-in inspections, customer estimate approvals, and ready-for-delivery gate passes.
- **Mechanic Workbench** (Technicians): Assigned repair work orders, complaint checklists, parts requisitions, time logging, and quality check submissions.
- **Spare Parts & Inventory Hub** (Inventory Manager): Low stock alerts with reorder triggers, purchase order GRN inwarding, 1-click workshop part issuance, and stock movement ledgers.
- **Financial & Indian GST Portal** (Accountant & Cashier): Cash/UPI collection registers, customer credit aging, GSTR-1 tax liability breakdown, and expense tracking.

### 2. Vehicle Check-In & Inspection
- Visual 360° vehicle body scratch, dent, and damage checklist.
- Fuel level recording (25%, 50%, 75%, 100%) and odometer logging with historical audit trails.
- Customer belongings inventory (jack, spare tire, toolkit, documents).

### 3. Live Workshop Floor & Lift Kanban
- Real-time visual monitoring of diagnostic bays, two-post lifts, and repair stations.
- Drag-and-drop / 1-click status transitions: `draft` ➔ `pending_estimate` ➔ `estimate_approved` ➔ `work_in_progress` ➔ `quality_check` ➔ `ready_for_delivery` ➔ `delivered`.
- Automatic bay allocation on repair start and release on delivery.

### 4. Transaction-Safe Spare Parts Inventory
- Thread-safe stock locking (`lockForUpdate`) on part issues to prevent race conditions.
- Automated stock decrement upon issue and stock refund upon return.
- Complete Stock Movement Audit Ledger with debit/credit quantities, unit costs, and running balances.
- Vendor purchase orders with 1-click Goods Receipt Note (GRN) inventory reception.

### 5. Indian GST Tax Compliance Engine
- Automatic distinction between **Intra-State** (CGST 9% + SGST 9%) and **Inter-State** (IGST 18%).
- Standard automotive HSN codes (HSN 8708 for spare parts) and SAC codes (SAC 998729 for labour charges).
- Professional thermal & A4 print tax invoices with QR code verification and security Gate Passes.

---

## 🛠️ Technology Stack

| Component | Technology / Library |
|---|---|
| **Backend** | Laravel 11.6.1 |
| **Language** | PHP 8.4+ |
| **Database** | MySQL 8+ / MariaDB |
| **Frontend** | Laravel Blade, Tailwind CSS, Alpine.js, Livewire 3 |
| **Visuals & Charts**| Chart.js 4, Lucide Icons |
| **PDF & Export** | Barryvdh DomPDF, Streamed CSV Exports |
| **Test Suite** | PHPUnit 11 (100% Pass, 12 Tests, 152 Assertions) |

---

## 👥 Demo Accounts (Password: `password`)

A 1-click login quick-bar is provided on the login page:

| Role | Email | Password | Primary Scope |
|---|---|---|---|
| **Workshop Admin** | `admin@automobile.com` | `password` | Complete access, RBAC, settings |
| **Workshop Manager** | `manager@automobile.com` | `password` | Operations, bay assignment, approvals |
| **Service Advisor** | `advisor@automobile.com` | `password` | Check-in, estimates, job cards, CRM |
| **Lead Mechanic** | `mechanic@automobile.com` | `password` | Work orders, parts request, QC |
| **Inventory Manager**| `inventory@automobile.com`| `password` | Spare parts, stock ledgers, POs |
| **Accountant** | `accountant@automobile.com`| `password` | Invoicing, payments, GST reports |

---

## 🚀 Installation & Local Setup

```bash
# 1. Clone repository
git clone https://github.com/webotapp-academy/WebotApp-AutoERP.git
cd WebotApp-AutoERP

# 2. Install PHP & Node dependencies
composer install
npm install
npm run build

# 3. Environment configuration
cp .env.example .env
php artisan key:generate

# Configure database credentials in .env:
# DB_DATABASE=automobile_erp
# DB_USERNAME=root
# DB_PASSWORD=

# 4. Run migrations and demo seeder
php artisan migrate --seed

# 5. Run test suite
php artisan test

# 6. Start development server
php artisan serve
```

---

## 🧪 Automated Test Suite

Run unit and feature tests:
```bash
php artisan test
```

Includes tests for:
- Intra-state vs Inter-state GST calculation and discounts.
- Strict Job Card state machine lifecycle transitions.
- End-to-end check-in ➔ job card ➔ parts issue ➔ QC ➔ invoice ➔ payment ➔ gate pass.
- Role-specific dashboard access and RBAC authorization.

---

## 📄 License

The WebotApp-AutoERP software is open-sourced under the [MIT license](LICENSE).
