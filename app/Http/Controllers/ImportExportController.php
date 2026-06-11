<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\Repair;
use App\Models\Appointment;
use App\Models\BankAccount;
use App\Models\Salary;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\Grn;
use App\Models\WarrantyClaim;
use App\Models\ProductReturn;
use App\Models\ProductSerial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ImportExportController extends Controller
{
    /**
     * Configuration mappings for all importable/exportable entities.
     */
    protected function getConfigs()
    {
        return [
            'products' => [
                'model' => Product::class,
                'title' => 'Products List',
                'fields' => [
                    'name' => ['label' => 'Product Name', 'type' => 'string', 'rules' => 'required|string|max:255', 'desc' => 'Full name of the product', 'sample' => 'Intel Core i7 13700K'],
                    'category_name' => ['label' => 'Category', 'type' => 'string', 'rules' => 'required|string|max:255', 'desc' => 'Category name (will find or create)', 'sample' => 'Processors'],
                    'brand' => ['label' => 'Brand', 'type' => 'string', 'rules' => 'required|string|max:255', 'desc' => 'Brand/Manufacturer name', 'sample' => 'Intel'],
                    'sku' => ['label' => 'SKU', 'type' => 'string', 'rules' => 'required|string|max:255|unique:products,sku', 'desc' => 'Stock Keeping Unit (must be unique)', 'sample' => 'CPU-INT-13700K'],
                    'buying_price' => ['label' => 'Buying Price', 'type' => 'numeric', 'rules' => 'required|numeric|min:0', 'desc' => 'Unit cost price in Rs.', 'sample' => 95000.00],
                    'price' => ['label' => 'Selling Price', 'type' => 'numeric', 'rules' => 'required|numeric|min:0', 'desc' => 'Unit retail price in Rs.', 'sample' => 110000.00],
                    'stock' => ['label' => 'Stock Level', 'type' => 'integer', 'rules' => 'required|integer|min:0', 'desc' => 'Initial stock count', 'sample' => 15],
                    'warranty_months' => ['label' => 'Warranty Months', 'type' => 'integer', 'rules' => 'nullable|integer|min:0', 'desc' => 'Warranty coverage period', 'sample' => 36],
                    'description' => ['label' => 'Description', 'type' => 'string', 'rules' => 'nullable|string', 'desc' => 'Detailed product overview', 'sample' => '13th Gen LGA1700 desktop processor'],
                ]
            ],
            'customers' => [
                'model' => Customer::class,
                'title' => 'Customers Directory',
                'fields' => [
                    'name' => ['label' => 'Name', 'type' => 'string', 'rules' => 'required|string|max:255', 'desc' => 'Customer full name', 'sample' => 'John Doe'],
                    'phone' => ['label' => 'Phone', 'type' => 'string', 'rules' => 'required|string|max:20|unique:customers,phone', 'desc' => 'Primary contact number (must be unique)', 'sample' => '0771234567'],
                    'email' => ['label' => 'Email', 'type' => 'string', 'rules' => 'nullable|email|max:255', 'desc' => 'Email address', 'sample' => 'john@example.com'],
                    'address' => ['label' => 'Address', 'type' => 'string', 'rules' => 'nullable|string|max:500', 'desc' => 'Physical home or business address', 'sample' => '123 Main St, Colombo'],
                    'loyalty_points' => ['label' => 'Loyalty Points', 'type' => 'integer', 'rules' => 'nullable|integer|min:0', 'desc' => 'Current loyalty points balance', 'sample' => 120]
                ]
            ],
            'suppliers' => [
                'model' => Supplier::class,
                'title' => 'Suppliers Directory',
                'fields' => [
                    'name' => ['label' => 'Contact Person', 'type' => 'string', 'rules' => 'required|string|max:255', 'desc' => 'Primary contact name', 'sample' => 'Sarah Connor'],
                    'company_name' => ['label' => 'Company Name', 'type' => 'string', 'rules' => 'required|string|max:255', 'desc' => 'Registered business/supplier entity name', 'sample' => 'Cyberdyne Systems'],
                    'phone' => ['label' => 'Phone', 'type' => 'string', 'rules' => 'required|string|max:20|unique:suppliers,phone', 'desc' => 'Supplier phone number (must be unique)', 'sample' => '0112233445'],
                    'email' => ['label' => 'Email', 'type' => 'string', 'rules' => 'nullable|email|max:255', 'desc' => 'Supplier email', 'sample' => 'info@cyberdyne.com'],
                    'address' => ['label' => 'Address', 'type' => 'string', 'rules' => 'nullable|string|max:500', 'desc' => 'Registered office address', 'sample' => 'Industrial Zone, Kandy'],
                    'tax_number' => ['label' => 'Tax Number', 'type' => 'string', 'rules' => 'nullable|string|max:50', 'desc' => 'VAT/TIN registered number', 'sample' => 'TX-998811-2']
                ]
            ],
            'employees' => [
                'model' => Employee::class,
                'title' => 'Employees Directory',
                'fields' => [
                    'name' => ['label' => 'Full Name', 'type' => 'string', 'rules' => 'required|string|max:255', 'desc' => 'Employee full name', 'sample' => 'Nisansala Silva'],
                    'designation' => ['label' => 'Designation', 'type' => 'string', 'rules' => 'required|string|max:255', 'desc' => 'Work role title', 'sample' => 'Cashier'],
                    'salary_amount' => ['label' => 'Basic Salary', 'type' => 'numeric', 'rules' => 'required|numeric|min:0', 'desc' => 'Base monthly salary in Rs.', 'sample' => 65000.00],
                    'phone' => ['label' => 'Phone', 'type' => 'string', 'rules' => 'required|string|max:20', 'desc' => 'Personal contact phone', 'sample' => '0711122334'],
                    'email' => ['label' => 'Email', 'type' => 'string', 'rules' => 'required|email|max:255|unique:employees,email', 'desc' => 'Work email address (must be unique)', 'sample' => 'nisansala@neuronet.com'],
                    'joining_date' => ['label' => 'Joining Date', 'type' => 'date', 'rules' => 'required|date_format:Y-m-d', 'desc' => 'Date hired in YYYY-MM-DD format', 'sample' => '2025-01-15'],
                    'status' => ['label' => 'Status', 'type' => 'string', 'rules' => 'nullable|in:active,inactive', 'desc' => 'Employment status (active or inactive)', 'sample' => 'active']
                ]
            ],
            'expenses' => [
                'model' => Expense::class,
                'title' => 'Operating Expenses Log',
                'fields' => [
                    'expense_no' => ['label' => 'Expense No', 'type' => 'string', 'rules' => 'nullable|string|max:50', 'desc' => 'Custom voucher/slip number (optional)', 'sample' => 'EXP-2026-001'],
                    'category' => ['label' => 'Category', 'type' => 'string', 'rules' => 'required|string|max:255', 'desc' => 'Category (Utility, Salaries, Rent, Stock, etc.)', 'sample' => 'Utility'],
                    'amount' => ['label' => 'Amount Spent', 'type' => 'numeric', 'rules' => 'required|numeric|min:0', 'desc' => 'Expense transaction amount in Rs.', 'sample' => 4500.00],
                    'date_incurred' => ['label' => 'Date Incurred', 'type' => 'date', 'rules' => 'required|date_format:Y-m-d', 'desc' => 'Billing date in YYYY-MM-DD format', 'sample' => '2026-05-01'],
                    'payment_method' => ['label' => 'Payment Method', 'type' => 'string', 'rules' => 'required|in:cash,bank', 'desc' => 'Source account type (cash or bank)', 'sample' => 'bank'],
                    'details' => ['label' => 'Details', 'type' => 'string', 'rules' => 'nullable|string', 'desc' => 'Brief description of expense', 'sample' => 'SLT Fibre monthly bill']
                ]
            ],
            'repairs' => [
                'model' => Repair::class,
                'title' => 'Repairs & Services Log',
                'fields' => [
                    'repair_job_no' => ['label' => 'Job No', 'type' => 'string', 'rules' => 'nullable|string|max:50', 'desc' => 'Job token ID (auto-generated if omitted)', 'sample' => 'JOB-2026-99A'],
                    'customer_name' => ['label' => 'Customer Name', 'type' => 'string', 'rules' => 'required|string|max:255', 'desc' => 'Customer name', 'sample' => 'Kamal Perera'],
                    'customer_phone' => ['label' => 'Customer Phone', 'type' => 'string', 'rules' => 'required|string|max:20', 'desc' => 'Customer phone', 'sample' => '0722233445'],
                    'customer_email' => ['label' => 'Customer Email', 'type' => 'string', 'rules' => 'nullable|email|max:255', 'desc' => 'Customer email', 'sample' => 'kamal@example.com'],
                    'device_model' => ['label' => 'Device Model', 'type' => 'string', 'rules' => 'required|string|max:255', 'desc' => 'Device model/brand details', 'sample' => 'Asus ROG Laptop'],
                    'device_serial' => ['label' => 'Device Serial', 'type' => 'string', 'rules' => 'nullable|string|max:255', 'desc' => 'Manufacturer serial number', 'sample' => 'SN-ASUS-9912'],
                    'issue_description' => ['label' => 'Issue Description', 'type' => 'string', 'rules' => 'required|string', 'desc' => 'Fault descriptions and instructions', 'sample' => 'No power, charging port damaged'],
                    'estimate_cost' => ['label' => 'Estimate Cost', 'type' => 'numeric', 'rules' => 'required|numeric|min:0', 'desc' => 'Estimated cost in Rs.', 'sample' => 15000.00],
                    'final_cost' => ['label' => 'Final Cost', 'type' => 'numeric', 'rules' => 'nullable|numeric|min:0', 'desc' => 'Actual cost in Rs. (if completed)', 'sample' => 15000.00],
                    'status' => ['label' => 'Status', 'type' => 'string', 'rules' => 'nullable|in:pending,in-progress,completed,cancelled', 'desc' => 'Job status (pending, in-progress, completed, cancelled)', 'sample' => 'pending'],
                    'notes' => ['label' => 'Technician Notes', 'type' => 'string', 'rules' => 'nullable|string', 'desc' => 'Additional service details', 'sample' => 'Replaced charging controller board']
                ]
            ],
            'appointments' => [
                'model' => Appointment::class,
                'title' => 'Scheduled Appointments',
                'fields' => [
                    'appointment_no' => ['label' => 'Appointment No', 'type' => 'string', 'rules' => 'nullable|string|max:50', 'desc' => 'Slip reference ID (optional)', 'sample' => 'APT-2026-11X'],
                    'customer_name' => ['label' => 'Customer Name', 'type' => 'string', 'rules' => 'required|string|max:255', 'desc' => 'Customer name', 'sample' => 'Nimal Fernando'],
                    'customer_phone' => ['label' => 'Customer Phone', 'type' => 'string', 'rules' => 'required|string|max:20', 'desc' => 'Customer phone', 'sample' => '0755566778'],
                    'customer_email' => ['label' => 'Customer Email', 'type' => 'string', 'rules' => 'nullable|email|max:255', 'desc' => 'Customer email', 'sample' => 'nimal@example.com'],
                    'appointment_time' => ['label' => 'Appointment Time', 'type' => 'datetime', 'rules' => 'required|date_format:Y-m-d H:i', 'desc' => 'Date & Time in YYYY-MM-DD HH:MM format', 'sample' => '2026-06-01 10:00'],
                    'reason' => ['label' => 'Reason', 'type' => 'string', 'rules' => 'required|string', 'desc' => 'Reason for visit', 'sample' => 'PC building consultation'],
                    'status' => ['label' => 'Status', 'type' => 'string', 'rules' => 'nullable|in:scheduled,completed,cancelled', 'desc' => 'Status (scheduled, completed, cancelled)', 'sample' => 'scheduled']
                ]
            ],
            'bank_accounts' => [
                'model' => BankAccount::class,
                'title' => 'Bank Accounts Mainframe',
                'fields' => [
                    'bank_name' => ['label' => 'Bank Name', 'type' => 'string', 'rules' => 'required|string|max:255', 'desc' => 'Name of the banking institute', 'sample' => 'Commercial Bank'],
                    'account_name' => ['label' => 'Account Name', 'type' => 'string', 'rules' => 'required|string|max:255', 'desc' => 'Holder name', 'sample' => 'Neuronet Shop Main'],
                    'account_number' => ['label' => 'Account Number', 'type' => 'string', 'rules' => 'required|string|max:255|unique:bank_accounts,account_number', 'desc' => 'Unique account number', 'sample' => '8009112233'],
                    'branch' => ['label' => 'Branch', 'type' => 'string', 'rules' => 'required|string|max:255', 'desc' => 'Bank branch name', 'sample' => 'Kollupitiya'],
                    'is_active' => ['label' => 'Is Active', 'type' => 'boolean', 'rules' => 'nullable|boolean', 'desc' => 'Account active status (1 for Active, 0 for Inactive)', 'sample' => 1]
                ]
            ],
            'salaries' => [
                'model' => Salary::class,
                'title' => 'Salary Disbursements',
                'fields' => [
                    'payslip_no' => ['label' => 'Payslip No', 'type' => 'string', 'rules' => 'nullable|string|max:50', 'desc' => 'Salary Slip ID (optional)', 'sample' => 'PAY-2026-05A'],
                    'employee_email' => ['label' => 'Employee Email', 'type' => 'string', 'rules' => 'required|email|exists:employees,email', 'desc' => 'Work email of the registered Employee to pay', 'sample' => 'nisansala@neuronet.com'],
                    'amount_paid' => ['label' => 'Amount Paid', 'type' => 'numeric', 'rules' => 'required|numeric|min:0', 'desc' => 'Amount paid in Rs.', 'sample' => 65000.00],
                    'paid_for_month' => ['label' => 'Paid For Month', 'type' => 'string', 'rules' => 'required|string|max:50', 'desc' => 'Disbursed month details', 'sample' => 'May 2026'],
                    'payment_date' => ['label' => 'Payment Date', 'type' => 'date', 'rules' => 'required|date_format:Y-m-d', 'desc' => 'Payment Date (YYYY-MM-DD)', 'sample' => '2026-05-25'],
                    'payment_method' => ['label' => 'Payment Method', 'type' => 'string', 'rules' => 'required|string|max:50', 'desc' => 'Payment method (bank, cash, etc.)', 'sample' => 'bank']
                ]
            ],
            'invoices' => [
                'model' => Invoice::class,
                'title' => 'Invoices Ledger',
                'fields' => [
                    'invoice_number' => ['label' => 'Invoice Number', 'type' => 'string', 'rules' => 'required|string|max:50|unique:invoices,invoice_number', 'desc' => 'Unique Invoice ID', 'sample' => 'INV-2026-012A'],
                    'customer_phone' => ['label' => 'Customer Phone', 'type' => 'string', 'rules' => 'required|string|exists:customers,phone', 'desc' => 'Registered Customer Phone', 'sample' => '0771234567'],
                    'total_amount' => ['label' => 'Total Amount', 'type' => 'numeric', 'rules' => 'required|numeric|min:0', 'desc' => 'Total invoice value in Rs.', 'sample' => 15000.00],
                    'amount_paid' => ['label' => 'Amount Paid', 'type' => 'numeric', 'rules' => 'required|numeric|min:0', 'desc' => 'Wages paid in Rs.', 'sample' => 15000.00],
                    'discount' => ['label' => 'Discount', 'type' => 'numeric', 'rules' => 'nullable|numeric|min:0', 'desc' => 'Applied discount in Rs.', 'sample' => 0.00],
                    'payment_method' => ['label' => 'Payment Method', 'type' => 'string', 'rules' => 'required|in:cash,bank,koko,payzy', 'desc' => 'Method (cash, bank, koko, payzy)', 'sample' => 'cash'],
                    'status' => ['label' => 'Status', 'type' => 'string', 'rules' => 'required|in:paid,unpaid,partial,installment', 'desc' => 'Invoice payment status', 'sample' => 'paid'],
                    'due_date' => ['label' => 'Due Date', 'type' => 'date', 'rules' => 'nullable|date_format:Y-m-d', 'desc' => 'Due date in YYYY-MM-DD format', 'sample' => '2026-06-26']
                ]
            ],
            'quotations' => [
                'model' => Quotation::class,
                'title' => 'Quotations Ledger',
                'fields' => [
                    'quotation_number' => ['label' => 'Quotation Number', 'type' => 'string', 'rules' => 'required|string|max:50|unique:quotations,quotation_number', 'desc' => 'Unique Quotation ID', 'sample' => 'QT-2026-003C'],
                    'customer_phone' => ['label' => 'Customer Phone', 'type' => 'string', 'rules' => 'required|string|exists:customers,phone', 'desc' => 'Registered Customer Phone', 'sample' => '0771234567'],
                    'total' => ['label' => 'Total', 'type' => 'numeric', 'rules' => 'required|numeric|min:0', 'desc' => 'Total quotation value in Rs.', 'sample' => 25000.00],
                    'valid_until' => ['label' => 'Valid Until', 'type' => 'date', 'rules' => 'required|date_format:Y-m-d', 'desc' => 'Expiration Date (YYYY-MM-DD)', 'sample' => '2026-06-15'],
                    'status' => ['label' => 'Status', 'type' => 'string', 'rules' => 'nullable|in:active,converted,expired', 'desc' => 'Quotation status', 'sample' => 'active'],
                    'notes' => ['label' => 'Notes', 'type' => 'string', 'rules' => 'nullable|string', 'desc' => 'Additional quotation details', 'sample' => 'Delivery within 3 working days']
                ]
            ],
            'grns' => [
                'model' => Grn::class,
                'title' => 'Goods Received Ledger',
                'fields' => [
                    'grn_number' => ['label' => 'GRN Number', 'type' => 'string', 'rules' => 'required|string|max:50|unique:grns,grn_number', 'desc' => 'Unique Goods Received Note ID', 'sample' => 'GRN-2026-015Z'],
                    'supplier_phone' => ['label' => 'Supplier Phone', 'type' => 'string', 'rules' => 'required|string|exists:suppliers,phone', 'desc' => 'Registered Supplier Phone', 'sample' => '0112233445'],
                    'total_amount' => ['label' => 'Total Amount', 'type' => 'numeric', 'rules' => 'required|numeric|min:0', 'desc' => 'Total inventory bill value in Rs.', 'sample' => 450000.00],
                    'date_received' => ['label' => 'Date Received', 'type' => 'date', 'rules' => 'required|date_format:Y-m-d', 'desc' => 'Received date (YYYY-MM-DD)', 'sample' => '2026-05-25'],
                    'notes' => ['label' => 'Notes', 'type' => 'string', 'rules' => 'nullable|string', 'desc' => 'Purchase details or notes', 'sample' => 'Received batch of Intel and Asus items']
                ]
            ],
            'warranties' => [
                'model' => WarrantyClaim::class,
                'title' => 'Warranty Claims Ledger',
                'fields' => [
                    'claim_number' => ['label' => 'Claim Number', 'type' => 'string', 'rules' => 'required|string|max:50|unique:warranty_claims,claim_number', 'desc' => 'Unique Claim Ticket ID', 'sample' => 'WC-2026-004'],
                    'customer_phone' => ['label' => 'Customer Phone', 'type' => 'string', 'rules' => 'required|string|exists:customers,phone', 'desc' => 'Registered Customer Phone', 'sample' => '0771234567'],
                    'invoice_number' => ['label' => 'Invoice Number', 'type' => 'string', 'rules' => 'required|string|exists:invoices,invoice_number', 'desc' => 'Purchased Invoice number', 'sample' => 'INV-2026-012A'],
                    'product_sku' => ['label' => 'Product SKU', 'type' => 'string', 'rules' => 'required|string|exists:products,sku', 'desc' => 'Claimed Product SKU', 'sample' => 'CPU-INT-13700K'],
                    'serial_number' => ['label' => 'Serial Number', 'type' => 'string', 'rules' => 'required|string', 'desc' => 'Item physical serial number', 'sample' => 'SN-998811A'],
                    'claim_date' => ['label' => 'Claim Date', 'type' => 'date', 'rules' => 'required|date_format:Y-m-d', 'desc' => 'Claimed Date (YYYY-MM-DD)', 'sample' => '2026-05-26'],
                    'issue_description' => ['label' => 'Issue Description', 'type' => 'string', 'rules' => 'required|string', 'desc' => 'Observed hardware issue details', 'sample' => 'Overheating and system crash'],
                    'status' => ['label' => 'Status', 'type' => 'string', 'rules' => 'nullable|in:pending,approved,rejected', 'desc' => 'Status (pending, approved, rejected)', 'sample' => 'pending']
                ]
            ],
            'returns' => [
                'model' => ProductReturn::class,
                'title' => 'Product Returns Ledger',
                'fields' => [
                    'return_number' => ['label' => 'Return Number', 'type' => 'string', 'rules' => 'required|string|max:50|unique:returns,return_number', 'desc' => 'Unique Return ID', 'sample' => 'RT-2026-011'],
                    'invoice_number' => ['label' => 'Invoice Number', 'type' => 'string', 'rules' => 'required|string|exists:invoices,invoice_number', 'desc' => 'Purchased Invoice number', 'sample' => 'INV-2026-012A'],
                    'supplier_phone' => ['label' => 'Supplier Phone', 'type' => 'string', 'rules' => 'nullable|string|exists:suppliers,phone', 'desc' => 'Supplier Phone (if supplier return)', 'sample' => '0112233445'],
                    'type' => ['label' => 'Type', 'type' => 'string', 'rules' => 'required|in:customer,supplier', 'desc' => 'Return flow type (customer or supplier)', 'sample' => 'customer'],
                    'refund_amount' => ['label' => 'Refund Amount', 'type' => 'numeric', 'rules' => 'required|numeric|min:0', 'desc' => 'Returned credit or cash amount in Rs.', 'sample' => 15000.00],
                    'reason' => ['label' => 'Reason', 'type' => 'string', 'rules' => 'required|string', 'desc' => 'Reason for product return', 'sample' => 'Incompatible with hardware specifications'],
                    'status' => ['label' => 'Status', 'type' => 'string', 'rules' => 'nullable|string', 'desc' => 'Return processing status', 'sample' => 'completed']
                ]
            ],
            'serials' => [
                'model' => ProductSerial::class,
                'title' => 'Product Serial Numbers',
                'fields' => [
                    'product_sku' => ['label' => 'Product SKU', 'type' => 'string', 'rules' => 'required|string|exists:products,sku', 'desc' => 'Catalog Product SKU', 'sample' => 'CPU-INT-13700K'],
                    'serial_number' => ['label' => 'Serial Number', 'type' => 'string', 'rules' => 'required|string', 'desc' => 'Item physical serial number', 'sample' => 'SN-998811A'],
                    'status' => ['label' => 'Status', 'type' => 'string', 'rules' => 'nullable|in:in_stock,sold,returned', 'desc' => 'Availability status (in_stock, sold, returned)', 'sample' => 'in_stock']
                ]
            ]
        ];
    }

    /**
     * Replicates the exact query logic from parent index pages to respect active filters.
     */
    protected function getFilteredQuery($type, Request $request)
    {
        $configs = $this->getConfigs();
        if (!isset($configs[$type])) {
            abort(404, 'Invalid data type requested.');
        }

        $modelClass = $configs[$type]['model'];
        $query = $modelClass::query();

        // Apply filters dynamically depending on the entity type
        $search = $request->search;
        if (!empty($search)) {
            switch ($type) {
                case 'products':
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('sku', 'like', "%{$search}%")
                          ->orWhere('brand', 'like', "%{$search}%");
                    break;
                case 'customers':
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('phone', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    break;
                case 'suppliers':
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('company_name', 'like', "%{$search}%")
                          ->orWhere('phone', 'like', "%{$search}%");
                    break;
                case 'employees':
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('designation', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    break;
                case 'expenses':
                    $query->where('expense_no', 'like', "%{$search}%")
                          ->orWhere('category', 'like', "%{$search}%")
                          ->orWhere('details', 'like', "%{$search}%");
                    break;
                case 'repairs':
                    $query->where('repair_job_no', 'like', "%{$search}%")
                          ->orWhere('customer_name', 'like', "%{$search}%")
                          ->orWhere('customer_phone', 'like', "%{$search}%")
                          ->orWhere('device_model', 'like', "%{$search}%");
                    break;
                case 'appointments':
                    $query->where('appointment_no', 'like', "%{$search}%")
                          ->orWhere('customer_name', 'like', "%{$search}%")
                          ->orWhere('customer_phone', 'like', "%{$search}%")
                          ->orWhere('reason', 'like', "%{$search}%");
                    break;
                case 'bank_accounts':
                    $query->where('bank_name', 'like', "%{$search}%")
                          ->orWhere('account_name', 'like', "%{$search}%")
                          ->orWhere('account_number', 'like', "%{$search}%");
                    break;
                case 'salaries':
                    $query->where('payslip_no', 'like', "%{$search}%")
                          ->orWhereHas('employee', function($q) use ($search) {
                              $q->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                          });
                    break;
                case 'invoices':
                    $query->where('invoice_number', 'like', "%{$search}%")
                          ->orWhereHas('customer', function($q) use ($search) {
                              $q->where('name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                          });
                    break;
                case 'quotations':
                    $query->where('quotation_number', 'like', "%{$search}%")
                          ->orWhere('customer_name', 'like', "%{$search}%")
                          ->orWhere('customer_phone', 'like', "%{$search}%");
                    break;
                case 'grns':
                    $query->where('grn_number', 'like', "%{$search}%")
                          ->orWhereHas('supplier', function($q) use ($search) {
                              $q->where('company_name', 'like', "%{$search}%")
                                ->orWhere('name', 'like', "%{$search}%");
                          });
                    break;
                case 'warranties':
                    $query->where('claim_number', 'like', "%{$search}%")
                          ->orWhere('serial_number', 'like', "%{$search}%")
                          ->orWhereHas('customer', function($q) use ($search) {
                              $q->where('name', 'like', "%{$search}%");
                          });
                    break;
                case 'returns':
                    $query->where('return_number', 'like', "%{$search}%")
                          ->orWhere('reason', 'like', "%{$search}%");
                    break;
                case 'serials':
                    $query->where('serial_number', 'like', "%{$search}%")
                          ->orWhereHas('product', function($q) use ($search) {
                              $q->where('name', 'like', "%{$search}%")
                                ->orWhere('sku', 'like', "%{$search}%");
                          });
                    break;
            }
        }

        // Apply filters depending on index queries
        if ($type === 'products') {
            if ($request->has('category') && $request->category !== 'all') {
                $query->where('category_id', $request->category);
            }
            if ($request->has('stock_filter')) {
                if ($request->stock_filter === 'low') {
                    $query->where('stock', '<', 5)->where('stock', '>', 0);
                } elseif ($request->stock_filter === 'out') {
                    $query->where('stock', 0);
                }
            }
        }

        // Apply Date Range filters standard on systems
        if ($request->has('date_range') && !empty($request->date_range)) {
            $range = $request->date_range;
            $field = 'created_at';
            if ($type === 'expenses') $field = 'date_incurred';
            if ($type === 'appointments') $field = 'appointment_time';
            if ($type === 'grns') $field = 'date_received';
            if ($type === 'warranties') $field = 'claim_date';
            if ($type === 'returns') $field = 'created_at';
            if ($type === 'salaries') $field = 'payment_date';

            switch ($range) {
                case 'today':
                    $query->whereDate($field, Carbon::today());
                    break;
                case 'weekly':
                    $query->whereBetween($field, [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'monthly':
                    $query->whereMonth($field, Carbon::now()->month)->whereYear($field, Carbon::now()->year);
                    break;
                case 'annually':
                    $query->whereYear($field, Carbon::now()->year);
                    break;
                case 'custom':
                    if ($request->has('start_date') && $request->has('end_date')) {
                        $query->whereBetween($field, [Carbon::parse($request->start_date)->startOfDay(), Carbon::parse($request->end_date)->endOfDay()]);
                    }
                    break;
            }
        }

        // Payment status filters standard on invoices
        if ($type === 'invoices' && $request->has('status_filter') && $request->status_filter !== 'all') {
            $query->where('status', $request->status_filter);
        }

        return $query;
    }

    /**
     * Generates and downloads a sample CSV file matching the schema constraints.
     */
    public function sample($type)
    {
        $configs = $this->getConfigs();
        if (!isset($configs[$type])) {
            abort(404, 'Invalid data type.');
        }

        $fields = $configs[$type]['fields'];
        
        $headers = [];
        $sampleRow = [];
        foreach ($fields as $key => $config) {
            $headers[] = $config['label'];
            $sampleRow[] = $config['sample'];
        }

        $filename = "sample_{$type}_template.csv";
        
        $callback = function() use ($headers, $sampleRow) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fputcsv($file, $sampleRow);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }

    /**
     * Generates and downloads a sample Excel (.xls) file matching the schema constraints.
     */
    public function sampleExcel($type)
    {
        $configs = $this->getConfigs();
        if (!isset($configs[$type])) {
            abort(404, 'Invalid data type.');
        }

        $fields = $configs[$type]['fields'];
        $title = $configs[$type]['title'];
        
        $headers = [];
        $sampleRow = [];
        foreach ($fields as $key => $config) {
            $headers[] = $config['label'];
            $sampleRow[] = $config['sample'];
        }

        $filename = "sample_{$type}_template.xls";
        
        $callback = function() use ($headers, $sampleRow, $title) {
            $out = fopen('php://output', 'w');
            
            $xml = '<?xml version="1.0"?>' . "\r\n";
            $xml .= '<?mso-application progid="Excel.Sheet"?>' . "\r\n";
            $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . "\r\n";
            $xml .= ' xmlns:o="urn:schemas-microsoft-com:office:office"' . "\r\n";
            $xml .= ' xmlns:x="urn:schemas-microsoft-com:office:excel"' . "\r\n";
            $xml .= ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"' . "\r\n";
            $xml .= ' xmlns:html="http://www.w3.org/TR/REC-html40">' . "\r\n";
            $xml .= ' <Worksheet ss:Name="' . htmlspecialchars(substr($title, 0, 30)) . '">' . "\r\n";
            $xml .= '  <Table>' . "\r\n";
            
            // Headers row
            $xml .= '   <Row>' . "\r\n";
            foreach ($headers as $header) {
                $xml .= '    <Cell><Data ss:Type="String">' . htmlspecialchars($header) . '</Data></Cell>' . "\r\n";
            }
            $xml .= '   </Row>' . "\r\n";
            
            // Sample row
            $xml .= '   <Row>' . "\r\n";
            foreach ($sampleRow as $val) {
                $typeStr = 'String';
                if (is_numeric($val)) {
                    $typeStr = 'Number';
                }
                $xml .= '    <Cell><Data ss:Type="' . $typeStr . '">' . htmlspecialchars((string)$val) . '</Data></Cell>' . "\r\n";
            }
            $xml .= '   </Row>' . "\r\n";
            
            $xml .= '  </Table>' . "\r\n";
            $xml .= ' </Worksheet>' . "\r\n";
            $xml .= '</Workbook>' . "\r\n";
            
            fwrite($out, $xml);
            fclose($out);
        };

        return response()->stream($callback, 200, [
            "Content-type"        => "application/vnd.ms-excel",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }

    /**
     * Exports filtered records to an Excel-compatible CSV file.
     */
    public function export($type, Request $request)
    {
        $configs = $this->getConfigs();
        if (!isset($configs[$type])) {
            abort(404);
        }

        $fields = $configs[$type]['fields'];
        $query = $this->getFilteredQuery($type, $request);
        
        // Eager load category for products to fetch the name
        if ($type === 'products') {
            $query->with('category');
        } elseif ($type === 'salaries') {
            $query->with('employee');
        } elseif ($type === 'invoices' || $type === 'quotations' || $type === 'warranties') {
            $query->with('customer');
        } elseif ($type === 'grns') {
            $query->with('supplier');
        }

        $records = $query->latest()->get();

        $headers = [];
        foreach ($fields as $key => $config) {
            $headers[] = $config['label'];
        }
        $headers[] = 'Created At';

        $filename = "exported_{$type}_" . date('Ymd_His') . ".csv";

        $callback = function() use ($records, $headers, $fields, $type) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);

            foreach ($records as $rec) {
                $row = [];
                foreach ($fields as $key => $config) {
                    // Resolve custom relationship mappings
                    if ($type === 'products' && $key === 'category_name') {
                        $row[] = $rec->category ? $rec->category->name : 'N/A';
                    } elseif ($type === 'salaries' && $key === 'employee_email') {
                        $row[] = $rec->employee ? $rec->employee->email : 'N/A';
                    } elseif ($type === 'invoices' && $key === 'customer_phone') {
                        $row[] = $rec->customer ? $rec->customer->phone : 'N/A';
                    } elseif ($type === 'quotations' && $key === 'customer_phone') {
                        $row[] = $rec->customer_phone ?? ($rec->customer ? $rec->customer->phone : 'N/A');
                    } elseif ($type === 'grns' && $key === 'supplier_phone') {
                        $row[] = $rec->supplier ? $rec->supplier->phone : 'N/A';
                    } elseif ($type === 'warranties' && $key === 'customer_phone') {
                        $row[] = $rec->customer ? $rec->customer->phone : 'N/A';
                    } elseif ($type === 'warranties' && $key === 'invoice_number') {
                        $row[] = $rec->invoice ? $rec->invoice->invoice_number : 'N/A';
                    } elseif ($type === 'warranties' && $key === 'product_sku') {
                        $row[] = $rec->product ? $rec->product->sku : 'N/A';
                    } elseif ($type === 'returns' && $key === 'invoice_number') {
                        $row[] = $rec->invoice ? $rec->invoice->invoice_number : 'N/A';
                    } elseif ($type === 'returns' && $key === 'supplier_phone') {
                        $row[] = $rec->supplier ? $rec->supplier->phone : 'N/A';
                    } elseif ($type === 'serials' && $key === 'product_sku') {
                        $row[] = $rec->product ? $rec->product->sku : 'N/A';
                    } else {
                        $row[] = $rec->$key;
                    }
                }
                $row[] = $rec->created_at->format('Y-m-d H:i:s');
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }

    /**
     * Exports filtered records to an Excel spreadsheet (.xls) file.
     */
    public function exportExcel($type, Request $request)
    {
        $configs = $this->getConfigs();
        if (!isset($configs[$type])) {
            abort(404);
        }

        $fields = $configs[$type]['fields'];
        $title = $configs[$type]['title'];
        $query = $this->getFilteredQuery($type, $request);
        
        if ($type === 'products') {
            $query->with('category');
        } elseif ($type === 'salaries') {
            $query->with('employee');
        } elseif ($type === 'invoices' || $type === 'quotations' || $type === 'warranties') {
            $query->with('customer');
        } elseif ($type === 'grns') {
            $query->with('supplier');
        }

        $records = $query->latest()->get();

        $headers = [];
        foreach ($fields as $key => $config) {
            $headers[] = $config['label'];
        }
        $headers[] = 'Created At';

        $filename = "exported_{$type}_" . date('Ymd_His') . ".xls";

        $callback = function() use ($records, $headers, $fields, $type, $title) {
            $out = fopen('php://output', 'w');
            
            $xml = '<?xml version="1.0"?>' . "\r\n";
            $xml .= '<?mso-application progid="Excel.Sheet"?>' . "\r\n";
            $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . "\r\n";
            $xml .= ' xmlns:o="urn:schemas-microsoft-com:office:office"' . "\r\n";
            $xml .= ' xmlns:x="urn:schemas-microsoft-com:office:excel"' . "\r\n";
            $xml .= ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"' . "\r\n";
            $xml .= ' xmlns:html="http://www.w3.org/TR/REC-html40">' . "\r\n";
            $xml .= ' <Worksheet ss:Name="' . htmlspecialchars(substr($title, 0, 30)) . '">' . "\r\n";
            $xml .= '  <Table>' . "\r\n";
            
            // Header Row
            $xml .= '   <Row>' . "\r\n";
            foreach ($headers as $header) {
                $xml .= '    <Cell><Data ss:Type="String">' . htmlspecialchars($header) . '</Data></Cell>' . "\r\n";
            }
            $xml .= '   </Row>' . "\r\n";

            // Data Rows
            foreach ($records as $rec) {
                $xml .= '   <Row>' . "\r\n";
                foreach ($fields as $key => $config) {
                    $val = '';
                    if ($type === 'products' && $key === 'category_name') {
                        $val = $rec->category ? $rec->category->name : 'N/A';
                    } elseif ($type === 'salaries' && $key === 'employee_email') {
                        $val = $rec->employee ? $rec->employee->email : 'N/A';
                    } elseif ($type === 'invoices' && $key === 'customer_phone') {
                        $val = $rec->customer ? $rec->customer->phone : 'N/A';
                    } elseif ($type === 'quotations' && $key === 'customer_phone') {
                        $val = $rec->customer_phone ?? ($rec->customer ? $rec->customer->phone : 'N/A');
                    } elseif ($type === 'grns' && $key === 'supplier_phone') {
                        $val = $rec->supplier ? $rec->supplier->phone : 'N/A';
                    } elseif ($type === 'warranties' && $key === 'customer_phone') {
                        $val = $rec->customer ? $rec->customer->phone : 'N/A';
                    } elseif ($type === 'warranties' && $key === 'invoice_number') {
                        $val = $rec->invoice ? $rec->invoice->invoice_number : 'N/A';
                    } elseif ($type === 'warranties' && $key === 'product_sku') {
                        $val = $rec->product ? $rec->product->sku : 'N/A';
                    } elseif ($type === 'returns' && $key === 'invoice_number') {
                        $val = $rec->invoice ? $rec->invoice->invoice_number : 'N/A';
                    } elseif ($type === 'returns' && $key === 'supplier_phone') {
                        $val = $rec->supplier ? $rec->supplier->phone : 'N/A';
                    } elseif ($type === 'serials' && $key === 'product_sku') {
                        $val = $rec->product ? $rec->product->sku : 'N/A';
                    } else {
                        $val = $rec->$key;
                    }

                    $typeStr = 'String';
                    if (is_numeric($val)) {
                        $typeStr = 'Number';
                    }
                    $xml .= '    <Cell><Data ss:Type="' . $typeStr . '">' . htmlspecialchars((string)$val) . '</Data></Cell>' . "\r\n";
                }
                
                $createdAt = $rec->created_at ? $rec->created_at->format('Y-m-d H:i:s') : '';
                $xml .= '    <Cell><Data ss:Type="String">' . htmlspecialchars($createdAt) . '</Data></Cell>' . "\r\n";
                $xml .= '   </Row>' . "\r\n";
            }
            
            $xml .= '  </Table>' . "\r\n";
            $xml .= ' </Worksheet>' . "\r\n";
            $xml .= '</Workbook>' . "\r\n";
            
            fwrite($out, $xml);
            fclose($out);
        };

        return response()->stream($callback, 200, [
            "Content-type"        => "application/vnd.ms-excel",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }

    /**
     * Renders records into a print-friendly preview screen that immediately prompts print/save-to-pdf.
     */
    public function print($type, Request $request)
    {
        $configs = $this->getConfigs();
        if (!isset($configs[$type])) {
            abort(404);
        }

        $fields = $configs[$type]['fields'];
        $query = $this->getFilteredQuery($type, $request);

        if ($type === 'products') {
            $query->with('category');
        } elseif ($type === 'salaries') {
            $query->with('employee');
        } elseif ($type === 'invoices' || $type === 'quotations' || $type === 'warranties') {
            $query->with('customer');
        } elseif ($type === 'grns') {
            $query->with('supplier');
        }

        $records = $query->latest()->get();
        $title = $configs[$type]['title'];

        return view('import_export.print', compact('records', 'fields', 'title', 'type'));
    }

    /**
     * Processes JSON payload of parsed rows, validates them, maps dependencies, and inserts them in a transaction.
     */
    public function import($type, Request $request)
    {
        $configs = $this->getConfigs();
        if (!isset($configs[$type])) {
            return response()->json(['success' => false, 'message' => 'Invalid data type.'], 400);
        }

        $rows = $request->input('rows');
        if (!is_array($rows) || empty($rows)) {
            return response()->json(['success' => false, 'message' => 'No records found in the uploaded file.'], 400);
        }

        $fieldsConfig = $configs[$type]['fields'];
        $modelClass = $configs[$type]['model'];

        // Build validation rules for single row mapping
        $rules = [];
        foreach ($fieldsConfig as $key => $config) {
            // We strip unique database checks from raw imports to validate manually or handle differently
            $rule = $config['rules'];
            
            // Map inputs to DB column labels
            $rules[$config['label']] = $rule;
        }

        $errors = [];
        $importedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                $rowNum = $index + 2; // offset for header row (index 0 is row 2)

                // Standardize keys (trim spaces)
                $trimmedRow = [];
                foreach ($row as $k => $v) {
                    $trimmedRow[trim($k)] = $v;
                }

                // Validate row
                $validator = Validator::make($trimmedRow, $rules);
                if ($validator->fails()) {
                    foreach ($validator->errors()->all() as $err) {
                        $errors[] = "Row {$rowNum}: {$err}";
                    }
                    continue;
                }

                // If errors already logged, skip actual insertion (dry run failures collection)
                if (!empty($errors)) {
                    continue;
                }

                // Map labels back to table columns
                $rowData = [];
                foreach ($fieldsConfig as $key => $config) {
                    $label = $config['label'];
                    $val = $trimmedRow[$label] ?? null;

                    // Type conversions
                    if ($config['type'] === 'numeric') {
                        $val = (float)$val;
                    } elseif ($config['type'] === 'integer') {
                        $val = (int)$val;
                    } elseif ($config['type'] === 'boolean') {
                        $val = filter_var($val, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
                    }

                    $rowData[$key] = $val;
                }

                // Handle entity specific dependencies & lookups
                if ($type === 'products') {
                    $categoryName = $trimmedRow[$fieldsConfig['category_name']['label']] ?? 'General';
                    $category = Category::firstOrCreate(
                        ['name' => $categoryName],
                        ['slug' => Str::slug($categoryName)]
                    );
                    $rowData['category_id'] = $category->id;
                    $rowData['slug'] = Str::slug($rowData['name']) . '-' . rand(100, 999);
                } elseif ($type === 'salaries') {
                    $email = $trimmedRow[$fieldsConfig['employee_email']['label']];
                    $employee = Employee::where('email', $email)->first();
                    if (!$employee) {
                        $errors[] = "Row {$rowNum}: Employee email '{$email}' is not registered.";
                        continue;
                    }
                    $rowData['employee_id'] = $employee->id;
                    if (empty($rowData['payslip_no'])) {
                        $rowData['payslip_no'] = 'PAY-' . strtoupper(Str::random(10));
                    }
                } elseif ($type === 'invoices') {
                    $phone = $trimmedRow[$fieldsConfig['customer_phone']['label']];
                    $customer = Customer::where('phone', $phone)->first();
                    if (!$customer) {
                        $errors[] = "Row {$rowNum}: Customer with phone '{$phone}' is not registered.";
                        continue;
                    }
                    $rowData['customer_id'] = $customer->id;
                    $rowData['user_id'] = auth()->id();
                    $rowData['subtotal'] = $rowData['total_amount'] - ($rowData['discount'] ?? 0);
                    $rowData['tax'] = 0;
                    $rowData['total'] = $rowData['total_amount'];
                    $rowData['balance'] = $rowData['total_amount'] - $rowData['amount_paid'];
                    $rowData['is_paid'] = $rowData['balance'] <= 0;
                } elseif ($type === 'quotations') {
                    $phone = $trimmedRow[$fieldsConfig['customer_phone']['label']];
                    $customer = Customer::where('phone', $phone)->first();
                    if (!$customer) {
                        $errors[] = "Row {$rowNum}: Customer with phone '{$phone}' is not registered.";
                        continue;
                    }
                    $rowData['customer_id'] = $customer->id;
                    $rowData['customer_name'] = $customer->name;
                    $rowData['subtotal'] = $rowData['total'];
                    $rowData['tax'] = 0;
                } elseif ($type === 'grns') {
                    $phone = $trimmedRow[$fieldsConfig['supplier_phone']['label']];
                    $supplier = Supplier::where('phone', $phone)->first();
                    if (!$supplier) {
                        $errors[] = "Row {$rowNum}: Supplier with phone '{$phone}' is not registered.";
                        continue;
                    }
                    $rowData['supplier_id'] = $supplier->id;
                    $rowData['received_by'] = auth()->id();
                } elseif ($type === 'warranties') {
                    $phone = $trimmedRow[$fieldsConfig['customer_phone']['label']];
                    $customer = Customer::where('phone', $phone)->first();
                    if (!$customer) {
                        $errors[] = "Row {$rowNum}: Customer phone '{$phone}' not found.";
                        continue;
                    }
                    $invNum = $trimmedRow[$fieldsConfig['invoice_number']['label']];
                    $invoice = Invoice::where('invoice_number', $invNum)->first();
                    if (!$invoice) {
                        $errors[] = "Row {$rowNum}: Invoice '{$invNum}' not found.";
                        continue;
                    }
                    $sku = $trimmedRow[$fieldsConfig['product_sku']['label']];
                    $product = Product::where('sku', $sku)->first();
                    if (!$product) {
                        $errors[] = "Row {$rowNum}: Product SKU '{$sku}' not found.";
                        continue;
                    }
                    $rowData['customer_id'] = $customer->id;
                    $rowData['invoice_id'] = $invoice->id;
                    $rowData['product_id'] = $product->id;
                } elseif ($type === 'returns') {
                    $invNum = $trimmedRow[$fieldsConfig['invoice_number']['label']];
                    $invoice = Invoice::where('invoice_number', $invNum)->first();
                    if (!$invoice) {
                        $errors[] = "Row {$rowNum}: Invoice '{$invNum}' not found.";
                        continue;
                    }
                    $rowData['invoice_id'] = $invoice->id;
                    
                    $suppPhoneLabel = $fieldsConfig['supplier_phone']['label'];
                    if (!empty($trimmedRow[$suppPhoneLabel])) {
                        $phone = $trimmedRow[$suppPhoneLabel];
                        $supplier = Supplier::where('phone', $phone)->first();
                        if ($supplier) {
                            $rowData['supplier_id'] = $supplier->id;
                        }
                    }
                } elseif ($type === 'serials') {
                    $sku = $trimmedRow[$fieldsConfig['product_sku']['label']];
                    $product = Product::where('sku', $sku)->first();
                    if (!$product) {
                        $errors[] = "Row {$rowNum}: Product SKU '{$sku}' not found.";
                        continue;
                    }
                    $rowData['product_id'] = $product->id;
                }

                // Perform Eloquent insert
                $modelClass::create($rowData);
                $importedCount++;
            }

            if (!empty($errors)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Import halted due to validation errors. No records were saved.',
                    'errors' => $errors
                ], 422);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "Import completed successfully! {$importedCount} records loaded into mainframe."
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'A fatal database error occurred during import: ' . $e->getMessage()
            ], 500);
        }
    }
}
