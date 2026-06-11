@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header Controls -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-800">
        <div class="flex items-center gap-3">
            <a href="{{ route('quotations.index') }}" class="p-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-400 hover:text-slate-200 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="orbitron-title text-xl font-black text-slate-100 tracking-wider">QUOTATION #{{ $quotation->quotation_number }}</h1>
                <p class="text-slate-400 text-xs mt-0.5 uppercase tracking-widest font-semibold">Details of the quotation</p>
            </div>
        </div>
        
        <div class="flex gap-2">
            <a href="{{ route('quotations.print', $quotation->id) }}" target="_blank" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold rounded-lg text-xs transition-colors flex items-center gap-2 border border-slate-700">
                <i class="fa-solid fa-print"></i>
                <span>PRINT</span>
            </a>
            <a href="{{ route('quotations.edit', $quotation->id) }}" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold rounded-lg text-xs transition-colors flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>EDIT</span>
            </a>
            <form action="{{ route('quotations.destroy', $quotation->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this quotation?')" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3 py-1.5 bg-rose-950/20 border border-rose-900/50 hover:bg-rose-900/20 text-rose-400 font-bold rounded-lg text-xs transition-all flex items-center gap-2">
                    <i class="fa-solid fa-trash"></i>
                    <span>DELETE</span>
                </button>
            </form>
            @if($quotation->status !== 'accepted' && !\Carbon\Carbon::parse($quotation->valid_until)->isPast())
                <form action="{{ route('quotations.convert', $quotation->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 bg-cyan-500 text-slate-950 font-bold rounded-lg text-xs transition-all hover:bg-cyan-400 shadow-neon-cyan flex items-center gap-2">
                        <i class="fa-solid fa-receipt"></i>
                        <span>CONVERT TO INVOICE</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Quote Details -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 space-y-3">
            <h3 class="orbitron-title text-xs font-black text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2">Details</h3>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-400">Quote Ref:</span>
                    <span class="font-bold text-slate-200">{{ $quotation->quotation_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Date Logged:</span>
                    <span class="text-slate-200">{{ $quotation->created_at->format('Y-m-d') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Valid Until:</span>
                    <span class="text-slate-200 font-semibold">{{ \Carbon\Carbon::parse($quotation->valid_until)->format('Y-m-d') }}</span>
                </div>
            </div>
        </div>

        <!-- Client Profile -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 space-y-3">
            <h3 class="orbitron-title text-xs font-black text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2">Customer Info</h3>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-400">Name:</span>
                    <span class="font-bold text-slate-200">{{ $quotation->customer_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Contact:</span>
                    <span class="text-slate-200 font-semibold">{{ $quotation->customer_phone }}</span>
                </div>
                @if($quotation->customer)
                    <div class="flex justify-between">
                        <span class="text-slate-400">Email:</span>
                        <span class="text-slate-300">{{ $quotation->customer->email ?? 'N/A' }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Operational Status -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 space-y-3">
            <h3 class="orbitron-title text-xs font-black text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2">Status Info</h3>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between items-center">
                    <span class="text-slate-400">Tax Type:</span>
                    <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold
                        {{ $quotation->tax > 0 ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'bg-slate-800 text-slate-400 border border-slate-700' }}">
                        {{ $quotation->tax > 0 ? 'Tax Estimate (15%)' : 'Standard Estimate' }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-400">Status:</span>
                    @if($quotation->status === 'accepted')
                        <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                            Accepted / Invoiced
                        </span>
                    @elseif(\Carbon\Carbon::parse($quotation->valid_until)->isPast())
                        <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                            Expired
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                            Active
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Items Listing -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <div class="p-5 border-b border-slate-800">
            <h3 class="orbitron-title text-sm font-black text-slate-200 uppercase tracking-widest">Products</h3>
        </div>
        <table class="w-full text-left text-xs border-collapse">
            <thead>
                <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-widest font-semibold text-[10px]">
                    <th class="py-3 px-6">Product Details</th>
                    <th class="py-3 px-6">SKU Code</th>
                    <th class="py-3 px-6 text-center font-bold">Qty</th>
                    <th class="py-3 px-6 text-right">Unit Price</th>
                    <th class="py-3 px-6 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-850">
                @foreach($quotation->items as $item)
                    <tr class="hover:bg-slate-800/10 transition-colors">
                        <td class="py-3.5 px-6">
                            <span class="font-bold text-slate-200 block">{{ $item->product->name }}</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">{{ $item->product->brand }}</span>
                        </td>
                        <td class="py-3.5 px-6 font-semibold text-slate-300">{{ $item->product->sku }}</td>
                        <td class="py-3.5 px-6 text-center text-slate-200 font-bold">{{ $item->quantity }}</td>
                        <td class="py-3.5 px-6 text-right text-slate-300 mono-text">Rs. {{ number_format($item->price, 2) }}</td>
                        <td class="py-3.5 px-6 text-right text-slate-100 font-bold mono-text">Rs. {{ number_format($item->price * $item->quantity, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary & Notes section -->
        <div class="p-6 border-t border-slate-800 bg-slate-950/40 flex flex-col md:flex-row justify-between gap-6">
            <div class="flex-1 space-y-2 text-xs">
                <span class="text-slate-400 font-bold uppercase tracking-wider block">Notes / Terms:</span>
                <p class="text-slate-300 leading-relaxed bg-slate-900 border border-slate-800 rounded-lg p-3">
                    {{ $quotation->notes ?? 'No special notes logged for this estimate.' }}
                </p>
            </div>
            <div class="w-72 space-y-2 text-xs text-slate-400 shrink-0">
                <div class="flex justify-between">
                    <span>Subtotal:</span>
                    <span class="font-bold text-slate-200 mono-text">Rs. {{ number_format($quotation->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Estimated Tax (VAT {{ $quotation->tax > 0 ? '15%' : '0%' }}):</span>
                    <span class="font-bold text-slate-200 mono-text">Rs. {{ number_format($quotation->tax, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm font-bold text-cyan-400 border-t border-slate-800 pt-2">
                    <span>Grand Total:</span>
                    <span class="text-base font-black mono-text">Rs. {{ number_format($quotation->total, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
