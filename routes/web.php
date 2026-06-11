<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PaymentController;

// ERP Ported Controllers
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
use App\Http\Controllers\RepairController;

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

    // Payments
    Route::get('/invoices/{invoice}/payment', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/invoices/{invoice}/payments', [PaymentController::class, 'store'])->name('payments.store');

    // =========================================================================
    // --- CloudTech Feature Console ---
    // =========================================================================

    Route::get('/search', [SearchController::class, 'index'])->name('search');

    // Invoices (ERP)
    Route::middleware('permission:create-invoices')->group(function () {
        Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('/invoices/store', [InvoiceController::class, 'store'])->name('invoices.store');
    });
    Route::middleware('permission:read-invoices')->group(function () {
        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
        Route::get('/invoices/{invoice}/items-json', [InvoiceController::class, 'itemsJson'])->name('invoices.items_json');
    });
    Route::middleware('permission:update-invoices')->group(function () {
        Route::get('/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
        Route::post('/invoices/{invoice}/update', [InvoiceController::class, 'update'])->name('invoices.update');
    });
    Route::middleware('permission:delete-invoices')->group(function () {
        Route::delete('/invoices/{invoice}/delete', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
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

    // Customers (ERP)
    Route::middleware('permission:create-customers')->group(function () {
        Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
        Route::post('/customers/store', [CustomerController::class, 'store'])->name('customers.store');
    });
    Route::middleware('permission:read-customers')->group(function () {
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
        Route::get('/customers/{customer}/ledger', [CustomerController::class, 'ledger'])->name('customers.ledger');
    });
    Route::middleware('permission:update-customers')->group(function () {
        Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
        Route::post('/customers/{customer}/update', [CustomerController::class, 'update'])->name('customers.update');
    });
    Route::middleware('permission:delete-customers')->group(function () {
        Route::delete('/customers/{customer}/delete', [CustomerController::class, 'destroy'])->name('customers.destroy');
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

    // Repairs (ERP)
    Route::middleware('permission:create-repairs')->group(function () {
        Route::get('/repairs/create', [RepairController::class, 'create'])->name('repairs.create');
        Route::post('/repairs/store', [RepairController::class, 'store'])->name('repairs.store');
    });
    Route::middleware('permission:read-repairs')->group(function () {
        Route::get('/repairs', [RepairController::class, 'index'])->name('repairs.index');
        Route::get('/repairs/{repair}', [RepairController::class, 'show'])->name('repairs.show');
        Route::get('/repairs/{repair}/receipt', [RepairController::class, 'receipt'])->name('repairs.receipt');
    });
    Route::middleware('permission:update-repairs')->group(function () {
        Route::get('/repairs/{repair}/edit', [RepairController::class, 'edit'])->name('repairs.edit');
        Route::post('/repairs/{repair}/update', [RepairController::class, 'update'])->name('repairs.update');
        Route::post('/repairs/{repair}/parts', [RepairController::class, 'addParts'])->name('repairs.parts');
    });
    Route::middleware('permission:delete-repairs')->group(function () {
        Route::delete('/repairs/{repair}/delete', [RepairController::class, 'destroy'])->name('repairs.destroy');
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
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });

    // Unified Import/Export Engine
    Route::post('/import/{type}', [App\Http\Controllers\ImportExportController::class, 'import'])->name('import.store');
    Route::get('/import/{type}/sample', [App\Http\Controllers\ImportExportController::class, 'sample'])->name('import.sample');
    Route::get('/import/{type}/sample/excel', [App\Http\Controllers\ImportExportController::class, 'sampleExcel'])->name('import.sample.excel');
    Route::get('/export/{type}', [App\Http\Controllers\ImportExportController::class, 'export'])->name('export.csv');
    Route::get('/export/{type}/excel', [App\Http\Controllers\ImportExportController::class, 'exportExcel'])->name('export.excel');
    Route::get('/export/{type}/print', [App\Http\Controllers\ImportExportController::class, 'print'])->name('export.print');
});

