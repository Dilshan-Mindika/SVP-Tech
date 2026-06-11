@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('neuro_customers.index') }}" class="text-slate-400 hover:text-cyan-400 transition-colors p-2 hover:bg-slate-800/50 rounded-lg">
                <i class="fa-solid fa-chevron-left"></i>
            </a>
            <div>
                <h1 class="orbitron-title text-2xl font-black text-slate-100 tracking-wider">{{ strtoupper($customer->name) }}</h1>
                <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest font-semibold font-sans">Customer Details</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('neuro_customers.edit', $customer->id) }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold rounded-lg text-sm transition-colors flex items-center gap-2 border border-slate-700">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>EDIT</span>
            </a>
            <form action="{{ route('neuro_customers.destroy'), $customer->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this customer?')" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-rose-900/20 hover:bg-rose-900/40 text-rose-400 font-bold rounded-lg text-sm transition-colors flex items-center gap-2 border border-rose-800/40">
                    <i class="fa-solid fa-trash"></i>
                    <span>DELETE</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Profile Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Contact Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 space-y-3 md:col-span-2">
            <h3 class="orbitron-title text-sm font-black text-slate-200 uppercase tracking-widest border-b border-slate-800 pb-3">Contact Information</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-[10px] text-slate-500 uppercase tracking-wider font-bold block">Phone</span>
                    <span class="text-slate-200 font-mono mt-1 block">{{ $customer->phone }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-500 uppercase tracking-wider font-bold block">Email</span>
                    <span class="text-slate-200 mt-1 block">{{ $customer->email ?? '—' }}</span>
                </div>
                <div class="col-span-2">
                    <span class="text-[10px] text-slate-500 uppercase tracking-wider font-bold block">Address</span>
                    <span class="text-slate-300 mt-1 block">{{ $customer->address ?? '—' }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-500 uppercase tracking-wider font-bold block">Member Since</span>
                    <span class="text-slate-200 font-mono mt-1 block">{{ \Carbon\Carbon::parse($customer->created_at)->format('d M Y') }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-500 uppercase tracking-wider font-bold block">Customer ID</span>
                    <span class="text-cyan-400 font-mono font-bold mt-1 block">CUS-#{{ str_pad($customer->id, 4, '0', STR_PAD_LEFT) }}</span>
                </div>
            </div>
        </div>

        <!-- Loyalty Points Card -->
        <div class="bg-slate-900 border border-amber-500/20 rounded-xl p-5 flex flex-col items-center justify-center text-center space-y-2">
            <i class="fa-solid fa-star text-amber-400 text-3xl"></i>
            <span class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Loyalty Points Balance</span>
            <h2 class="text-4xl font-extrabold text-amber-400 mono-text">{{ number_format($customer->loyalty_points) }}</h2>
            <span class="text-[10px] text-slate-500">points available</span>
            <span class="text-[10px] text-slate-500 font-medium">1 point = Rs. 1 discount</span>
        </div>
    </div>

    <!-- Purchase History -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 space-y-4">
        <h3 class="orbitron-title text-sm font-black text-slate-200 uppercase tracking-widest border-b border-slate-800 pb-3">Purchase History</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-widest font-semibold text-[10px]">
                        <th class="py-3 px-4">Invoice No.</th>
                        <th class="py-3 px-4 text-right">Total</th>
                        <th class="py-3 px-4 text-center">Payment</th>
                        <th class="py-3 px-4 text-center">Date</th>
                        <th class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-850">
                    @forelse($invoices as $inv)
                        <tr class="hover:bg-slate-800/10 transition-colors">
                            <td class="py-3 px-4 font-bold text-cyan-400 mono-text">{{ $inv->invoice_number }}</td>
                            <td class="py-3 px-4 text-right font-bold text-slate-200 mono-text">Rs. {{ number_format($inv->total, 2) }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 bg-slate-800 text-slate-300 font-bold rounded text-[9px] uppercase tracking-wider">{{ $inv->payment_method }}</span>
                            </td>
                            <td class="py-3 px-4 text-center text-slate-400 font-mono">{{ \Carbon\Carbon::parse($inv->created_at)->format('d M Y') }}</td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('neuro_invoices.show', $inv->id) }}" class="text-cyan-400 hover:text-cyan-300 font-bold text-[10px] uppercase tracking-wider transition-colors">View &rarr;</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-600">
                                <i class="fa-solid fa-receipt text-2xl mb-2 block opacity-40"></i>
                                <span>No purchase history found.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Loyalty Transaction Log -->
    @if($customer->loyaltyTransactions->count())
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 space-y-4">
        <h3 class="orbitron-title text-sm font-black text-slate-200 uppercase tracking-widest border-b border-slate-800 pb-3">Loyalty Points History</h3>
        <div class="space-y-2 max-h-64 overflow-y-auto pr-1">
            @foreach($customer->loyaltyTransactions as $txn)
                <div class="flex items-center justify-between p-3 rounded-lg border {{ $txn->points > 0 ? 'border-emerald-500/20 bg-emerald-500/5' : 'border-rose-500/20 bg-rose-500/5' }}">
                    <div>
                        <span class="text-[10px] uppercase font-bold tracking-widest {{ $txn->points > 0 ? 'text-emerald-400' : 'text-rose-400' }} block">
                            {{ $txn->transaction_type }}
                        </span>
                        <span class="text-xs text-slate-300 mt-0.5 block">{{ $txn->description }}</span>
                    </div>
                    <div class="text-right">
                        <span class="font-bold mono-text {{ $txn->points > 0 ? 'text-emerald-400' : 'text-rose-400' }} block">
                            {{ $txn->points > 0 ? '+' : '' }}{{ $txn->points }} pts
                        </span>
                        <span class="text-[10px] text-slate-500 font-mono block">{{ \Carbon\Carbon::parse($txn->created_at)->format('d M Y') }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
