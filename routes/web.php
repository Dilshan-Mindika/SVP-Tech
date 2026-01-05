<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\RepairJobController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ReportingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\InvoicesModuleController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Technician Management (Admin Only)
    Route::resource('technicians', TechnicianController::class);

    // Core Modules
    Route::resource('customers', CustomerController::class);
    Route::get('/customers/{customer}/ledger', [CustomerController::class, 'ledger'])->name('customers.ledger');
    Route::resource('repair-jobs', RepairJobController::class);
    Route::patch('/repair-jobs/{job}/status', [RepairJobController::class, 'updateStatus'])->name('repair-jobs.update-status');
    Route::patch('/repair-jobs/{job}/assign', [RepairJobController::class, 'assignTechnician'])->name('repair-jobs.assign-technician');
    Route::resource('inventory', InventoryController::class);

    // Invoices & Sales
    Route::get('/invoices', [InvoicesModuleController::class, 'index'])->name('invoices.index');
    Route::get('/sales/create', [InvoicesModuleController::class, 'createSale'])->name('sales.create');
    Route::post('/sales', [InvoicesModuleController::class, 'storeSale'])->name('sales.store');
    
    // Legacy/Specific Invoice Actions
    Route::post('/repair-jobs/{job}/invoice', [InvoiceController::class, 'generate'])->name('invoices.generate');
    Route::get('/repair-jobs/{job}/invoice-preview', [InvoiceController::class, 'preview'])->name('invoice-preview');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');

    // Reports
    Route::get('/reports', [ReportingController::class, 'index'])->name('reports.index');
    Route::get('/reports/outstanding', [ReportingController::class, 'outstandingInvoices'])->name('reports.outstanding');

    // Payments
    Route::get('/invoices/{invoice}/payment', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/invoices/{invoice}/payments', [PaymentController::class, 'store'])->name('payments.store');
});
