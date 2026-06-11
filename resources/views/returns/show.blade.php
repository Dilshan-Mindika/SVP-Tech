@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header/Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-800">
        <div class="flex items-center gap-3">
            <a href="{{ route('returns.index') }}" class="p-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-400 hover:text-slate-200 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xs uppercase tracking-widest font-black text-cyan-400">Return Details</span>
                    <span class="text-slate-650">•</span>
                    <span class="text-xs uppercase tracking-widest font-black text-slate-450 mono-text">{{ $return->created_at->format('Y-m-d H:i') }}</span>
                </div>
                <h1 class="orbitron-title text-xl font-black text-slate-100 tracking-wider mt-0.5">RETURN: {{ $return->return_number }}</h1>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('returns.edit', $return->id) }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold rounded-lg text-xs transition-colors flex items-center gap-2 border border-slate-700">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>EDIT</span>
            </a>
            <form action="{{ route('returns.destroy', $return->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this return log?')" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-rose-900/20 hover:bg-rose-900/40 text-rose-400 font-bold rounded-lg text-xs transition-colors flex items-center gap-2 border border-rose-800/40">
                    <i class="fa-solid fa-trash"></i>
                    <span>DELETE</span>
                </button>
            </form>
            <button onclick="window.print()" class="px-4 py-2 bg-slate-900 hover:bg-slate-850 border border-slate-800 text-slate-350 hover:text-slate-200 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                <i class="fa-solid fa-print"></i>
                <span>PRINT</span>
            </button>
        </div>
    </div>

    <!-- Details Deck -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Return Summary & Items -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Items Card -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-4">
                <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-3 flex items-center gap-2">
                    <i class="fa-solid fa-list-check"></i>
                    <span>Returned Items</span>
                </h2>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-widest font-semibold text-[10px]">
                                <th class="pb-3 pr-4">Product / SKU</th>
                                <th class="pb-3 px-4 text-center">Quantity</th>
                                <th class="pb-3 px-4 text-right">Unit Price</th>
                                <th class="pb-3 pl-4 text-right">Total Price</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-850">
                            @foreach($return->items as $item)
                                <tr class="hover:bg-slate-850/20 transition-colors">
                                    <td class="py-3.5 pr-4">
                                        <div class="font-bold text-slate-200">{{ $item->product->name }}</div>
                                        <div class="text-[10px] text-slate-500 font-mono mt-0.5">SKU: {{ $item->product->sku }} | Category: {{ $item->product->category->name ?? 'Uncategorized' }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-semibold text-slate-300 mono-text">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right text-slate-400 font-medium mono-text">
                                        Rs. {{ number_format($item->unit_price, 2) }}
                                    </td>
                                    <td class="py-3.5 pl-4 text-right text-cyan-400 font-bold mono-text">
                                        Rs. {{ number_format($item->unit_price * $item->quantity, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Aggregate total info -->
                <div class="pt-4 border-t border-slate-800/60 flex justify-end">
                                    <div class="w-64 space-y-1.5 text-xs text-right">
                                        <div class="flex justify-between text-slate-400">
                                            <span>Total Item Value:</span>
                                            <span class="font-semibold text-slate-200 mono-text">Rs. {{ number_format($return->items->sum(fn($i) => $i->unit_price * $i->quantity), 2) }}</span>
                                        </div>
                                        <div class="flex justify-between text-cyan-400 font-bold text-sm border-t border-slate-800/80 pt-2">
                                            <span>Refund Amount:</span>
                                            <span class="mono-text">Rs. {{ number_format($return->refund_amount, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
            </div>

            <!-- Contextual Ledger Card -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-4">
                <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-3 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>System Information</span>
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                    <!-- Link Details -->
                    <div class="space-y-3">
                        <div>
                            <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-bold">Return Type</span>
                            @if($return->type === 'customer_return')
                                <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 inline-block mt-1">
                                    Customer Return (Restocked)
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-violet-500/10 text-violet-400 border border-violet-500/20 inline-block mt-1">
                                    Supplier Return (Deducted)
                                </span>
                            @endif
                        </div>

                        @if($return->type === 'customer_return')
                            <div>
                                <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-bold">Original Invoice</span>
                                @if($return->invoice)
                                    <a href="{{ route('invoices.show', $return->invoice->id) }}" class="text-cyan-400 hover:text-cyan-300 font-bold font-mono text-sm block mt-1 hover:underline">
                                        <i class="fa-solid fa-receipt mr-1"></i>{{ $return->invoice->invoice_number }}
                                    </a>
                                @else
                                    <span class="text-slate-400 italic block mt-1">Direct Return (No invoice link)</span>
                                @endif
                            </div>
                        @else
                            <div>
                                <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-bold">Supplier</span>
                                @if($return->supplier)
                                    <div class="mt-1">
                                        <span class="text-slate-200 font-bold block">{{ $return->supplier->company_name }}</span>
                                        <span class="text-slate-400 text-[11px] block mt-0.5">Contact: {{ $return->supplier->name }} ({{ $return->supplier->phone }})</span>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic block mt-1">Direct Supplier Return</span>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Inventory Details -->
                    <div class="space-y-3">
                        <div>
                            <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-bold">Stock Update Details</span>
                            <span class="text-slate-300 block mt-1 font-semibold">
                                <i class="fa-solid fa-cube text-cyan-400 mr-1.5"></i>
                                @if($return->type === 'customer_return')
                                    Stock quantities automatically incremented. Serial statuses set to <code class="text-emerald-400 font-mono">in_stock</code>.
                                @else
                                    Stock quantities automatically decremented. Serial statuses set to <code class="text-rose-400 font-mono">returned</code>.
                                @endif
                            </span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-bold">Database ID</span>
                            <span class="text-slate-500 font-mono block mt-1">UUID/ID: #{{ $return->id }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Refund status & details -->
        <div class="space-y-6">
            <!-- Refund Value Card -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-4 relative overflow-hidden">
                <!-- Background Glow -->
                <div class="absolute -top-24 -right-24 h-48 w-48 rounded-full bg-cyan-500/5 blur-3xl"></div>

                <h2 class="orbitron-title text-xs font-bold text-slate-400 uppercase tracking-widest border-b border-slate-800 pb-2">REFUND VALUE</h2>
                
                <div class="py-2">
                    <span class="text-[10px] text-slate-500 uppercase tracking-wider font-bold block">Refund Amount</span>
                    <div class="text-3xl font-black text-cyan-400 tracking-wider mono-text mt-1 neon-glow-text">
                        Rs. {{ number_format($return->refund_amount, 2) }}
                    </div>
                </div>

                <div class="border-t border-slate-800/80 pt-3 grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-bold">Return Status</span>
                        <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20 inline-block mt-1">
                            {{ strtoupper($return->status) }}
                        </span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-bold">Authorized By</span>
                        <span class="text-slate-300 font-semibold block mt-1">
                            System Admin
                        </span>
                    </div>
                </div>
            </div>

            <!-- Return Reason Card -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-4">
                <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2">Reason for Return</h2>
                <div class="p-4 bg-slate-950 border border-slate-850 rounded-lg text-xs text-slate-300 italic leading-relaxed">
                    "{{ $return->reason }}"
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
