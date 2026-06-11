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

// Neuro Ported Controllers
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\GrnController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\WarrantyController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\OperationsController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\NeuroCustomerController;
use App\Http\Controllers\NeuroDashboardController;
use App\Http\Controllers\NeuroInvoiceController;
use App\Http\Controllers\NeuroRepairController;
use App\Http\Controllers\NeuroReportsController;

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
    Route::patch('/repair-jobs/{job}/payment-status', [RepairJobController::class, 'updatePaymentStatus'])->name('repair-jobs.update-payment-status');
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

    // =========================================================================
    // --- Neuro System Feature Porting ---
    // =========================================================================

    // Dashboard (Neuro Console)
    Route::get('/neuro/dashboard', [NeuroDashboardController::class, 'index'])->name('neuro_dashboard');
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    // Invoices (Neuro)
    Route::middleware('permission:create-invoices')->group(function () {
        Route::get('/neuro/invoices/create', [NeuroInvoiceController::class, 'create'])->name('neuro_invoices.create');
        Route::post('/neuro/invoices/store', [NeuroInvoiceController::class, 'store'])->name('neuro_invoices.store');
    });
    Route::middleware('permission:read-invoices')->group(function () {
        Route::get('/neuro/invoices', [NeuroInvoiceController::class, 'index'])->name('neuro_invoices.index');
        Route::get('/neuro/invoices/{invoice}', [NeuroInvoiceController::class, 'show'])->name('neuro_invoices.show');
        Route::get('/neuro/invoices/{invoice}/print', [NeuroInvoiceController::class, 'print'])->name('neuro_invoices.print');
        Route::get('/neuro/invoices/{invoice}/items-json', [NeuroInvoiceController::class, 'itemsJson'])->name('neuro_invoices.items_json');
    });
    Route::middleware('permission:update-invoices')->group(function () {
        Route::get('/neuro/invoices/{invoice}/edit', [NeuroInvoiceController::class, 'edit'])->name('neuro_invoices.edit');
        Route::post('/neuro/invoices/{invoice}/update', [NeuroInvoiceController::class, 'update'])->name('neuro_invoices.update');
    });
    Route::middleware('permission:delete-invoices')->group(function () {
        Route::delete('/neuro/invoices/{invoice}/delete', [NeuroInvoiceController::class, 'destroy'])->name('neuro_invoices.destroy');
    });

    // Quotations
    Route::middleware('permission:create-quotations')->group(function () {
        Route::get('/quotations/create', [QuotationController::class, 'create'])->name('quotations.create');
        Route::post('/quotations/store', [QuotationController::class, 'store'])->name('quotations.store');
        Route::post('/quotations/{quotation}/convert', [QuotationController::class, 'convertToInvoice'])->name('quotations.convert');
    });
    Route::middleware('permission:read-quotations')->group(function () {
        Route::get('/quotations', [QuotationController::class, 'index'])->name('quotations.index');
        Route::get('/quotations/{quotation}', [QuotationController::class, 'show'])->name('quotations.show');
        Route::get('/quotations/{quotation}/print', [QuotationController::class, 'print'])->name('quotations.print');
    });
    Route::middleware('permission:update-quotations')->group(function () {
        Route::get('/quotations/{quotation}/edit', [QuotationController::class, 'edit'])->name('quotations.edit');
        Route::post('/quotations/{quotation}/update', [QuotationController::class, 'update'])->name('quotations.update');
    });
    Route::middleware('permission:delete-quotations')->group(function () {
        Route::delete('/quotations/{quotation}/delete', [QuotationController::class, 'destroy'])->name('quotations.destroy');
    });

    // Customers (Neuro)
    Route::middleware('permission:create-customers')->group(function () {
        Route::get('/neuro/customers/create', [NeuroCustomerController::class, 'create'])->name('neuro_customers.create');
        Route::post('/neuro/customers/store', [NeuroCustomerController::class, 'store'])->name('neuro_customers.store');
    });
    Route::middleware('permission:read-customers')->group(function () {
        Route::get('/neuro/customers', [NeuroCustomerController::class, 'index'])->name('neuro_customers.index');
        Route::get('/neuro/customers/{customer}', [NeuroCustomerController::class, 'show'])->name('neuro_customers.show');
    });
    Route::middleware('permission:update-customers')->group(function () {
        Route::get('/neuro/customers/{customer}/edit', [NeuroCustomerController::class, 'edit'])->name('neuro_customers.edit');
        Route::post('/neuro/customers/{customer}/update', [NeuroCustomerController::class, 'update'])->name('neuro_customers.update');
    });
    Route::middleware('permission:delete-customers')->group(function () {
        Route::delete('/neuro/customers/{customer}/delete', [NeuroCustomerController::class, 'destroy'])->name('neuro_customers.destroy');
    });

    // GRN (Goods Received Notes)
    Route::middleware('permission:create-grn')->group(function () {
        Route::get('/grn/create', [GrnController::class, 'create'])->name('grn.create');
        Route::post('/grn/store', [GrnController::class, 'store'])->name('grn.store');
    });
    Route::middleware('permission:read-grn')->group(function () {
        Route::get('/grn', [GrnController::class, 'index'])->name('grn.index');
        Route::get('/grn/{grn}', [GrnController::class, 'show'])->name('grn.show');
    });
    Route::middleware('permission:update-grn')->group(function () {
        Route::get('/grn/{grn}/edit', [GrnController::class, 'edit'])->name('grn.edit');
        Route::post('/grn/{grn}/update', [GrnController::class, 'update'])->name('grn.update');
    });
    Route::middleware('permission:delete-grn')->group(function () {
        Route::delete('/grn/{grn}/delete', [GrnController::class, 'destroy'])->name('grn.destroy');
    });

    // Suppliers
    Route::middleware('permission:read-suppliers')->group(function () {
        Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    });
    Route::middleware('permission:create-suppliers')->group(function () {
        Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
        Route::post('/suppliers/store', [SupplierController::class, 'store'])->name('suppliers.store');
    });
    Route::middleware('permission:update-suppliers')->group(function () {
        Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
        Route::post('/suppliers/{supplier}/update', [SupplierController::class, 'update'])->name('suppliers.update');
    });
    Route::middleware('permission:delete-suppliers')->group(function () {
        Route::delete('/suppliers/{supplier}/delete', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
    });

    // Products
    Route::middleware('permission:read-products')->group(function () {
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/serials', [ProductController::class, 'serials'])->name('products.serials');
    });
    Route::middleware('permission:create-products')->group(function () {
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products/store', [ProductController::class, 'store'])->name('products.store');
    });
    Route::middleware('permission:update-products')->group(function () {
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::post('/products/{product}/update', [ProductController::class, 'update'])->name('products.update');
        Route::post('/products/{product}/toggle-visibility', [ProductController::class, 'toggleVisibility'])->name('products.toggle-visibility');
    });
    Route::middleware('permission:delete-products')->group(function () {
        Route::delete('/products/{product}/delete', [ProductController::class, 'destroy'])->name('products.destroy');
    });

    // Categories
    Route::middleware('permission:create-categories')->group(function () {
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories/store', [CategoryController::class, 'store'])->name('categories.store');
    });
    Route::middleware('permission:read-categories')->group(function () {
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    });
    Route::middleware('permission:update-categories')->group(function () {
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::post('/categories/{category}/update', [CategoryController::class, 'update'])->name('categories.update');
    });
    Route::middleware('permission:delete-categories')->group(function () {
        Route::delete('/categories/{category}/delete', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });
    Route::middleware('permission:read-categories')->group(function () {
        Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    });

    // Repairs (Neuro)
    Route::middleware('permission:create-repairs')->group(function () {
        Route::get('/neuro/repairs/create', [NeuroRepairController::class, 'create'])->name('neuro_repairs.create');
        Route::post('/neuro/repairs/store', [NeuroRepairController::class, 'store'])->name('neuro_repairs.store');
    });
    Route::middleware('permission:read-repairs')->group(function () {
        Route::get('/neuro/repairs', [NeuroRepairController::class, 'index'])->name('neuro_repairs.index');
        Route::get('/neuro/repairs/{repair}', [NeuroRepairController::class, 'show'])->name('neuro_repairs.show');
        Route::get('/neuro/repairs/{repair}/receipt', [NeuroRepairController::class, 'receipt'])->name('neuro_repairs.receipt');
    });
    Route::middleware('permission:update-repairs')->group(function () {
        Route::get('/neuro/repairs/{repair}/edit', [NeuroRepairController::class, 'edit'])->name('neuro_repairs.edit');
        Route::post('/neuro/repairs/{repair}/update', [NeuroRepairController::class, 'update'])->name('neuro_repairs.update');
        Route::post('/neuro/repairs/{repair}/parts', [NeuroRepairController::class, 'addParts'])->name('neuro_repairs.parts');
    });
    Route::middleware('permission:delete-repairs')->group(function () {
        Route::delete('/neuro/repairs/{repair}/delete', [NeuroRepairController::class, 'destroy'])->name('neuro_repairs.destroy');
    });

    // Warranty Claims
    Route::middleware('permission:create-warranty')->group(function () {
        Route::get('/warranty/create', [WarrantyController::class, 'create'])->name('warranty.create');
        Route::post('/warranty/store', [WarrantyController::class, 'store'])->name('warranty.store');
        Route::get('/warranty/customer/{customer}/invoices-json', [WarrantyController::class, 'customerInvoicesJson'])->name('warranty.customer_invoices_json');
    });
    Route::middleware('permission:read-warranty')->group(function () {
        Route::get('/warranty', [WarrantyController::class, 'index'])->name('warranty.index');
        Route::get('/warranty/{claim}', [WarrantyController::class, 'show'])->name('warranty.show');
    });
    Route::middleware('permission:update-warranty')->group(function () {
        Route::post('/warranty/{claim}/status', [WarrantyController::class, 'updateStatus'])->name('warranty.status');
        Route::get('/warranty/{claim}/edit', [WarrantyController::class, 'edit'])->name('warranty.edit');
        Route::post('/warranty/{claim}/update', [WarrantyController::class, 'update'])->name('warranty.update');
    });
    Route::middleware('permission:delete-warranty')->group(function () {
        Route::delete('/warranty/{claim}/delete', [WarrantyController::class, 'destroy'])->name('warranty.destroy');
    });

    // Appointments
    Route::middleware('permission:read-appointments')->group(function () {
        Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    });
    Route::middleware('permission:create-appointments')->group(function () {
        Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
        Route::post('/appointments/store', [AppointmentController::class, 'store'])->name('appointments.store');
    });
    Route::middleware('permission:update-appointments')->group(function () {
        Route::post('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.status');
        Route::get('/appointments/{appointment}/edit', [AppointmentController::class, 'edit'])->name('appointments.edit');
        Route::post('/appointments/{appointment}/update', [AppointmentController::class, 'update'])->name('appointments.update');
    });
    Route::middleware('permission:delete-appointments')->group(function () {
        Route::delete('/appointments/{appointment}/delete', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
    });

    // Expenses
    Route::middleware('permission:read-expenses')->group(function () {
        Route::get('/expenses', [OperationsController::class, 'expensesIndex'])->name('expenses.index');
    });
    Route::middleware('permission:create-expenses')->group(function () {
        Route::get('/expenses/create', [OperationsController::class, 'expenseCreate'])->name('expenses.create');
        Route::post('/expenses/store', [OperationsController::class, 'expenseStore'])->name('expenses.store');
    });
    Route::middleware('permission:update-expenses')->group(function () {
        Route::get('/expenses/{expense}/edit', [OperationsController::class, 'expenseEdit'])->name('expenses.edit');
        Route::post('/expenses/{expense}/update', [OperationsController::class, 'expenseUpdate'])->name('expenses.update');
    });
    Route::middleware('permission:delete-expenses')->group(function () {
        Route::delete('/expenses/{expense}/delete', [OperationsController::class, 'expenseDestroy'])->name('expenses.destroy');
    });

    // Returns
    Route::middleware('permission:create-returns')->group(function () {
        Route::get('/returns/create', [OperationsController::class, 'returnCreate'])->name('returns.create');
        Route::post('/returns/store', [OperationsController::class, 'returnStore'])->name('returns.store');
    });
    Route::middleware('permission:read-returns')->group(function () {
        Route::get('/returns', [OperationsController::class, 'returnsIndex'])->name('returns.index');
        Route::get('/returns/{return}', [OperationsController::class, 'returnShow'])->name('returns.show');
    });
    Route::middleware('permission:update-returns')->group(function () {
        Route::get('/returns/{return}/edit', [OperationsController::class, 'returnEdit'])->name('returns.edit');
        Route::post('/returns/{return}/update', [OperationsController::class, 'returnUpdate'])->name('returns.update');
    });
    Route::middleware('permission:delete-returns')->group(function () {
        Route::delete('/returns/{return}/delete', [OperationsController::class, 'returnDestroy'])->name('returns.destroy');
    });

    // Employees
    Route::middleware('permission:read-employees')->group(function () {
        Route::get('/employees', [StaffController::class, 'index'])->name('employees.index');
    });
    Route::middleware('permission:create-employees')->group(function () {
        Route::get('/employees/create', [StaffController::class, 'create'])->name('employees.create');
        Route::post('/employees/store', [StaffController::class, 'store'])->name('employees.store');
    });
    Route::middleware('permission:update-employees')->group(function () {
        Route::get('/employees/{employee}/edit', [StaffController::class, 'edit'])->name('employees.edit');
        Route::post('/employees/{employee}/update', [StaffController::class, 'update'])->name('employees.update');
    });
    Route::middleware('permission:delete-employees')->group(function () {
        Route::delete('/employees/{employee}/delete', [StaffController::class, 'destroy'])->name('employees.destroy');
    });

    // Salaries
    Route::middleware('permission:read-salaries')->group(function () {
        Route::get('/salaries', [StaffController::class, 'salariesIndex'])->name('salaries.index');
    });
    Route::middleware('permission:create-salaries')->group(function () {
        Route::get('/salaries/create', [StaffController::class, 'salaryCreate'])->name('salaries.create');
        Route::post('/salaries/store', [StaffController::class, 'salaryStore'])->name('salaries.store');
    });
    Route::middleware('permission:update-salaries')->group(function () {
        Route::get('/salaries/{salary}/edit', [StaffController::class, 'salaryEdit'])->name('salaries.edit');
        Route::post('/salaries/{salary}/update', [StaffController::class, 'salaryUpdate'])->name('salaries.update');
    });
    Route::middleware('permission:delete-salaries')->group(function () {
        Route::delete('/salaries/{salary}/delete', [StaffController::class, 'salaryDestroy'])->name('salaries.destroy');
    });

    // User Management
    Route::middleware('permission:read-users')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create')->middleware('permission:create-users');
        Route::post('/users/store', [UserController::class, 'store'])->name('users.store')->middleware('permission:create-users');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('permission:update-users');
        Route::post('/users/{user}/update', [UserController::class, 'update'])->name('users.update')->middleware('permission:update-users');
        Route::delete('/users/{user}/delete', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:delete-users');
    });

    // Roles & Permissions Management
    Route::middleware('permission:read-roles')->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create')->middleware('permission:create-roles');
        Route::post('/roles/store', [RoleController::class, 'store'])->name('roles.store')->middleware('permission:create-roles');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit')->middleware('permission:update-roles');
        Route::post('/roles/{role}/update', [RoleController::class, 'update'])->name('roles.update')->middleware('permission:update-roles');
        Route::delete('/roles/{role}/delete', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('permission:delete-roles');

        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create')->middleware('permission:create-roles');
        Route::post('/permissions/store', [PermissionController::class, 'store'])->name('permissions.store')->middleware('permission:create-roles');
        Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit')->middleware('permission:update-roles');
        Route::post('/permissions/{permission}/update', [PermissionController::class, 'update'])->name('permissions.update')->middleware('permission:update-roles');
        Route::delete('/permissions/{permission}/delete', [PermissionController::class, 'destroy'])->name('permissions.destroy')->middleware('permission:delete-roles');
    });

    // Bank Accounts Management
    Route::middleware('permission:read-bank-accounts')->group(function () {
        Route::get('/bank-accounts', [BankAccountController::class, 'index'])->name('bank_accounts.index');
    });
    Route::middleware('permission:create-bank-accounts')->group(function () {
        Route::get('/bank-accounts/create', [BankAccountController::class, 'create'])->name('bank_accounts.create');
        Route::post('/bank-accounts/store', [BankAccountController::class, 'store'])->name('bank_accounts.store');
    });
    Route::middleware('permission:update-bank-accounts')->group(function () {
        Route::get('/bank-accounts/{bank_account}/edit', [BankAccountController::class, 'edit'])->name('bank_accounts.edit');
        Route::post('/bank-accounts/{bank_account}/update', [BankAccountController::class, 'update'])->name('bank_accounts.update');
    });
    Route::middleware('permission:delete-bank-accounts')->group(function () {
        Route::delete('/bank-accounts/{bank_account}/delete', [BankAccountController::class, 'destroy'])->name('bank_accounts.destroy');
    });

    // Reports Engine
    Route::middleware('permission:read-expenses')->group(function () {
        Route::get('/neuro/reports', [NeuroReportsController::class, 'index'])->name('neuro_reports.index');
    });

    // Unified Import/Export Engine
    Route::post('/import/{type}', [App\Http\Controllers\ImportExportController::class, 'import'])->name('import.store');
    Route::get('/import/{type}/sample', [App\Http\Controllers\ImportExportController::class, 'sample'])->name('import.sample');
    Route::get('/import/{type}/sample/excel', [App\Http\Controllers\ImportExportController::class, 'sampleExcel'])->name('import.sample.excel');
    Route::get('/export/{type}', [App\Http\Controllers\ImportExportController::class, 'export'])->name('export.csv');
    Route::get('/export/{type}/excel', [App\Http\Controllers\ImportExportController::class, 'exportExcel'])->name('export.excel');
    Route::get('/export/{type}/print', [App\Http\Controllers\ImportExportController::class, 'print'])->name('export.print');
});

