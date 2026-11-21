<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WorkshopBoardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\JobCardController;
use App\Http\Controllers\EstimateController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\MechanicController;
use App\Http\Controllers\ServiceCatalogController;
use App\Http\Controllers\ServicePackageController;
use App\Http\Controllers\QualityController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\InsuranceController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\CrmController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/quick-login/{email}', [AuthController::class, 'quickLogin'])->name('quick-login');
    Route::post('/login/quick', [AuthController::class, 'quickLogin'])->name('login.quick');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected ERP Routes
Route::middleware('auth')->group(function () {
    // Root redirect to dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/branch/switch/{id}', [DashboardController::class, 'switchBranch'])->name('branch.switch');

    // Workshop Live Floor & Kanban
    Route::get('/workshop/board', [WorkshopBoardController::class, 'index'])->name('workshop.board');
    Route::post('/workshop/board/status', [WorkshopBoardController::class, 'updateStatus'])->name('workshop.board.status');

    // Customers
    Route::resource('customers', CustomerController::class);

    // Vehicles
    Route::get('/vehicles/get-models', [VehicleController::class, 'getModels'])->name('vehicles.get-models');
    Route::get('/vehicles/by-customer/{customerId}', [VehicleController::class, 'byCustomer'])->name('vehicles.by-customer');
    Route::resource('vehicles', VehicleController::class);

    // Appointments
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::post('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.status');

    // Vehicle Check-in Inspections
    Route::resource('inspections', InspectionController::class)->only(['index', 'create', 'store', 'show']);

    // Job Cards Lifecycle
    Route::resource('job-cards', JobCardController::class);
    Route::post('/job-cards/{jobCard}/status', [JobCardController::class, 'updateStatus'])->name('job-cards.status');
    Route::post('/job-cards/{jobCard}/update-status', [JobCardController::class, 'updateStatus'])->name('job-cards.update-status');
    Route::post('/job-cards/{jobCard}/services', [JobCardController::class, 'addService'])->name('job-cards.add-service');
    Route::post('/job-cards/{jobCard}/parts', [JobCardController::class, 'addPart'])->name('job-cards.add-part');
    Route::post('/job-card-parts/{jobCardPart}/issue', [JobCardController::class, 'issuePart'])->name('job-cards.issue-part');
    Route::post('/job-card-parts/{jobCardPart}/return', [JobCardController::class, 'returnPart'])->name('job-cards.return-part');
    Route::post('/job-cards/{jobCard}/invoice', [JobCardController::class, 'createInvoice'])->name('job-cards.create-invoice');
    Route::post('/job-cards/{jobCard}/time-logs', [JobCardController::class, 'logTime'])->name('job-cards.log-time');
    Route::get('/job-cards/{jobCard}/print', [JobCardController::class, 'printWorkOrder'])->name('job-cards.print');
    Route::get('/job-cards/{jobCard}/print-work-order', [JobCardController::class, 'printWorkOrder'])->name('job-cards.print-work-order');
    Route::get('/job-cards/{jobCard}/gate-pass', [JobCardController::class, 'printGatePass'])->name('job-cards.gate-pass');
    Route::get('/job-cards/{jobCard}/print-gate-pass', [JobCardController::class, 'printGatePass'])->name('job-cards.print-gate-pass');

    // Estimates
    Route::resource('estimates', EstimateController::class);
    Route::post('/estimates/{estimate}/status', [EstimateController::class, 'updateStatus'])->name('estimates.status');
    Route::post('/estimates/{estimate}/convert', [EstimateController::class, 'convertToJobCard'])->name('estimates.convert');
    Route::get('/estimates/{estimate}/print', [EstimateController::class, 'printEstimate'])->name('estimates.print');

    // Inventory & Spare Parts
    Route::get('/inventory/ledger', [InventoryController::class, 'ledger'])->name('inventory.ledger');
    Route::post('/inventory/{part}/adjust', [InventoryController::class, 'adjustStock'])->name('inventory.adjust');
    Route::resource('inventory', InventoryController::class);

    // Invoices & Billing (Indian GST)
    Route::resource('invoices', InvoiceController::class);
    Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'printInvoice'])->name('invoices.print');

    // Payments
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');

    // Workshop Mechanics
    Route::get('/mechanics', [MechanicController::class, 'index'])->name('mechanics.index');

    // Services Catalog & Packages
    Route::get('/services', [ServiceCatalogController::class, 'index'])->name('services.index');
    Route::post('/services', [ServiceCatalogController::class, 'store'])->name('services.store');
    Route::get('/packages', [ServicePackageController::class, 'index'])->name('packages.index');
    Route::post('/packages', [ServicePackageController::class, 'store'])->name('packages.store');

    // Quality Control
    Route::get('/quality', [QualityController::class, 'index'])->name('quality.index');
    Route::get('/quality/create', [QualityController::class, 'create'])->name('quality.create');
    Route::post('/quality', [QualityController::class, 'store'])->name('quality.store');

    // Suppliers & Purchase Orders
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
    Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');
    Route::post('/purchases/{purchaseOrder}/receive', [PurchaseController::class, 'receive'])->name('purchases.receive');

    // Insurance Claims
    Route::get('/insurance', [InsuranceController::class, 'index'])->name('insurance.index');
    Route::get('/insurance/create', [InsuranceController::class, 'create'])->name('insurance.create');
    Route::post('/insurance', [InsuranceController::class, 'store'])->name('insurance.store');
    Route::post('/insurance/{claim}/status', [InsuranceController::class, 'updateStatus'])->name('insurance.status');

    // Expenses
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::get('/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');

    // CRM & Follow-ups
    Route::get('/crm/reminders', [CrmController::class, 'reminders'])->name('crm.reminders');
    Route::post('/crm/reminders', [CrmController::class, 'storeReminder'])->name('crm.store-reminder');
    Route::post('/crm/reminders/{reminder}/send', [CrmController::class, 'sendReminder'])->name('crm.send-reminder');

    // Financial Reports & GST
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-sales-csv', [ReportController::class, 'exportSalesCsv'])->name('reports.export-sales-csv');

    // Staff & Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');

    // Workshop Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});
