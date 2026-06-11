<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Salary;
use App\Models\User;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Traits\DateFilterable;

class StaffController extends Controller
{
    use DateFilterable;
    public function index(Request $request)
    {
        $query = Employee::with('user');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('designation', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
        }

        $stats = [
            'total_count' => (clone $query)->count(),
            'active_count' => (clone $query)->where('status', 'active')->count(),
            'monthly_payroll' => (clone $query)->where('status', 'active')->sum('salary_amount'),
            'designations_count' => (clone $query)->distinct('designation')->count('designation'),
        ];

        $employees = $query->latest()->paginate(10);
        return view('employees.index', compact('employees', 'stats'));
    }

    public function create()
    {
        // Get users that are not already employees
        $assignedUserIds = Employee::whereNotNull('user_id')->pluck('user_id')->toArray();
        $users = User::whereNotIn('id', $assignedUserIds)->get();
        return view('employees.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'salary_amount' => 'required|numeric|min:0',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'joining_date' => 'required|date',
            'user_id' => 'nullable|exists:users,id',
        ]);

        Employee::create($request->validated());

        return redirect()->route('employees.index')->with('success', "Employee {$request->name} registered successfully.");
    }

    public function edit(Employee $employee)
    {
        $assignedUserIds = Employee::whereNotNull('user_id')
            ->where('user_id', '!=', $employee->user_id)
            ->pluck('user_id')->toArray();
        $users = User::whereNotIn('id', $assignedUserIds)->get();
        return view('employees.edit', compact('employee', 'users'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'salary_amount' => 'required|numeric|min:0',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'joining_date' => 'required|date',
            'status' => 'required|string',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $employee->update($request->validated());

        return redirect()->route('employees.index')->with('success', "Employee {$request->name} details updated.");
    }

    // Salaries (Payroll)
    public function salariesIndex(Request $request)
    {
        $query = Salary::with('employee');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('employee', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhere('payslip_no', 'like', "%{$search}%")
              ->orWhere('paid_for_month', 'like', "%{$search}%");
        }

        // Apply Date Filter from Trait on payment_date column
        $this->applyDateFilter($query, $request, 'payment_date');

        $stats = [
            'total_count' => (clone $query)->count(),
            'total_paid' => (clone $query)->sum('amount_paid'),
            'pending_count' => 0,
            'avg_salary' => (clone $query)->avg('amount_paid') ?? 0,
        ];

        $salaries = $query->latest()->paginate(10);
        return view('salaries.index', compact('salaries', 'stats'));
    }

    public function salaryCreate()
    {
        $employees = Employee::where('status', 'active')->get();
        return view('salaries.create', compact('employees'));
    }

    public function salaryStore(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount_paid' => 'required|numeric|min:0',
            'paid_for_month' => 'required|string|max:50',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
        ]);

        return DB::transaction(function () use ($request) {
            $employee = Employee::find($request->employee_id);

            // Guard: Prevent duplicate payment for same employee+month
            $alreadyPaid = Salary::where('employee_id', $request->employee_id)
                ->where('paid_for_month', $request->paid_for_month)
                ->exists();
            if ($alreadyPaid) {
                return redirect()->back()->withErrors("Salary for {$employee->name} for {$request->paid_for_month} has already been paid. Check payroll records.");
            }

            // Generate Payslip Number
            $lastSalary = Salary::latest('id')->first();
            $nextId = $lastSalary ? $lastSalary->id + 1 : 1;
            $payslipNo = 'PAY-' . date('Ym') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            $salary = Salary::create([
                'employee_id' => $request->employee_id,
                'amount_paid' => $request->amount_paid,
                'paid_for_month' => $request->paid_for_month,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'payslip_no' => $payslipNo,
            ]);

            // Auto-log a corresponding Expense under 'Salary' category
            $expenseNo = 'EXP-SAL-' . str_pad($salary->id, 5, '0', STR_PAD_LEFT);
            Expense::create([
                'expense_no' => $expenseNo,
                'category' => 'Salary',
                'amount' => $request->amount_paid,
                'date_incurred' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'details' => "Automated Salary Payout: {$employee->name} for {$request->paid_for_month}. Payslip: {$payslipNo}",
            ]);

            return redirect()->route('salaries.index')->with('success', "Salary payout of {$request->amount_paid} recorded for {$employee->name}. Payslip: {$payslipNo}");
        });
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', "Employee {$employee->name} deleted successfully.");
    }

    public function salaryEdit(Salary $salary)
    {
        $employees = Employee::all();
        return view('salaries.edit', compact('salary', 'employees'));
    }

    public function salaryUpdate(Request $request, Salary $salary)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount_paid' => 'required|numeric|min:0',
            'paid_for_month' => 'required|string|max:50',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
        ]);

        $salary->update($request->all());

        return redirect()->route('salaries.index')->with('success', "Salary record {$salary->payslip_no} updated successfully.");
    }

    public function salaryDestroy(Salary $salary)
    {
        $salary->delete();
        return redirect()->route('salaries.index')->with('success', "Salary record {$salary->payslip_no} deleted successfully.");
    }
}
