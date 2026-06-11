<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index(Request $request)
    {
        $query = BankAccount::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('bank_name', 'like', "%{$search}%")
                  ->orWhere('account_name', 'like', "%{$search}%")
                  ->orWhere('account_number', 'like', "%{$search}%");
            });
        }

        $stats = [
            'total_count' => (clone $query)->count(),
            'active_count' => (clone $query)->where('is_active', true)->count(),
            'inactive_count' => (clone $query)->where('is_active', false)->count(),
            'bank_transactions_value' => \App\Models\Invoice::whereIn('bank_account_id', (clone $query)->pluck('id'))->sum('total'),
        ];

        $bankAccounts = $query->latest()->paginate(10);
        return view('bank_accounts.index', compact('bankAccounts', 'stats'));
    }

    public function create()
    {
        return view('bank_accounts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'branch' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $bankAccount = BankAccount::create($request->all());

        return redirect()->route('bank_accounts.index')->with('success', "Bank Account {$bankAccount->bank_name} registered successfully.");
    }

    public function edit(BankAccount $bankAccount)
    {
        return view('bank_accounts.edit', compact('bankAccount'));
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'branch' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $bankAccount->update($request->all());

        return redirect()->route('bank_accounts.index')->with('success', "Bank Account {$bankAccount->bank_name} updated successfully.");
    }

    public function destroy(BankAccount $bankAccount)
    {
        // Check if there are any invoices linked to this bank account
        if ($bankAccount->invoices()->exists()) {
            return back()->withErrors("Cannot delete bank account {$bankAccount->bank_name} as it has linked invoices.");
        }

        $bankAccount->delete();

        return redirect()->route('bank_accounts.index')->with('success', "Bank Account deleted successfully.");
    }
}
