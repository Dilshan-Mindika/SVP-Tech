@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-800">
        <div class="flex items-center gap-3">
            <a href="{{ route('grn.index') }}" class="p-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-400 hover:text-slate-200 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="orbitron-title text-xl font-black text-slate-100 tracking-wider">{{ $grn->grn_number }}</h1>
                    <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                        PROCESSED
                    </span>
                </div>
                <p class="text-slate-400 text-xs mt-0.5 uppercase tracking-widest font-semibold">Processed by {{ $grn->receiver->name }} on {{ \Carbon\Carbon::parse($grn->date_received)->format('l, d F Y') }}</p>
            </div>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold rounded-lg text-xs transition-colors flex items-center gap-2 border border-slate-700">
                <i class="fa-solid fa-print"></i>
                <span>PRINT</span>
            </button>
            <a href="{{ route('grn.edit', $grn->id) }}" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold rounded-lg text-xs transition-colors flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>EDIT</span>
            </a>
            <form action="{{ route('grn.destroy', $grn->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this GRN?')" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3 py-1.5 bg-rose-950/20 border border-rose-900/50 hover:bg-rose-900/20 text-rose-400 font-bold rounded-lg text-xs transition-all flex items-center gap-2">
                    <i class="fa-solid fa-trash"></i>
                    <span>DELETE</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Info Panels -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Supplier card -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 md:col-span-2 space-y-4">
            <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800/80 pb-2">Supplier Info</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">Company Name</span>
                    <span class="text-slate-200 font-bold text-sm">{{ $grn->supplier->company_name }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">Contact Representative</span>
                    <span class="text-slate-200 font-semibold">{{ $grn->supplier->name }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">Phone Number</span>
                    <span class="text-slate-200 mono-text">{{ $grn->supplier->phone }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">Email Address</span>
                    <span class="text-slate-200">{{ $grn->supplier->email ?: 'N/A' }}</span>
                </div>
                @if($grn->supplier->tax_number)
                    <div class="md:col-span-2">
                        <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">VAT / Tax ID</span>
                        <span class="text-slate-200 mono-text">{{ $grn->supplier->tax_number }}</span>
                    </div>
                @endif
                <div class="md:col-span-2">
                    <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">Registered Address</span>
                    <span class="text-slate-200">{{ $grn->supplier->address ?: 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- Meta Summary card -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-4">
            <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800/80 pb-2">Summary</h2>
            <div class="space-y-3 text-xs">
                <div class="flex justify-between items-center">
                    <span class="text-slate-400">Total Items:</span>
                    <span class="font-bold text-slate-200 mono-text">{{ $grn->items->count() }} lines</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-400">Total Quantity:</span>
                    <span class="font-bold text-slate-200 mono-text">{{ $grn->items->sum('quantity') + $grn->items->sum('free_quantity') }} units</span>
                </div>
                <div class="flex justify-between items-center border-t border-slate-800/60 pt-2">
                    <span class="text-slate-400">Subtotal:</span>
                    <span class="font-bold text-slate-350 mono-text">Rs. {{ number_format($grn->subtotal ?: $grn->total_amount, 2) }}</span>
                </div>
                @if($grn->discount_amount > 0)
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400">Discount:</span>
                        <span class="font-bold text-rose-400 mono-text">-Rs. {{ number_format($grn->discount_amount, 2) }} ({{ $grn->discount_percentage }}%)</span>
                    </div>
                @endif
                @if($grn->service_charges > 0)
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400">Courier / Transport:</span>
                        <span class="font-bold text-slate-350 mono-text">Rs. {{ number_format($grn->service_charges, 2) }}</span>
                    </div>
                @endif
                <div class="border-t border-slate-800 pt-3 flex justify-between items-center">
                    <span class="text-xs text-slate-400 uppercase tracking-wider font-bold">Grand Total:</span>
                    <span class="text-md font-black text-cyan-400 mono-text">Rs. {{ number_format($grn->total_amount, 2) }}</span>
                </div>
                <div class="border-t border-slate-800/60 pt-2 space-y-1 text-[11px]">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Payment:</span>
                        <span class="text-slate-300 font-semibold">{{ $grn->payment_type ?: 'Cash' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Status:</span>
                        <span class="font-bold {{ $grn->is_paid ? 'text-emerald-400' : 'text-rose-400' }}">{{ $grn->is_paid ? 'PAID' : 'UNPAID' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Paid Amount:</span>
                        <span class="text-slate-300 mono-text">Rs. {{ number_format($grn->paid_amount ?: ($grn->is_paid ? $grn->total_amount : 0.00), 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-800">
            <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest">Products</h2>
        </div>
        <table class="w-full text-left text-xs border-collapse">
            <thead>
                <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-widest font-semibold text-[9px] bg-slate-900/40">
                    <th class="py-3 px-6">Product details</th>
                    <th class="py-3 px-6 text-right">Cost Price</th>
                    <th class="py-3 px-6 text-right">Sale Price</th>
                    <th class="py-3 px-6 text-right">Whole Sale Price</th>
                    <th class="py-3 px-6">Barcode</th>
                    <th class="py-3 px-6 text-center">Recieved Quantity</th>
                    <th class="py-3 px-6 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-850">
                @foreach($grn->items as $item)
                    <tr class="hover:bg-slate-800/5 transition-colors">
                        <td class="py-3.5 px-6">
                            <span class="text-slate-200 font-bold block">{{ $item->product->name }}</span>
                            <span class="text-[10px] text-slate-500 block">SKU: {{ $item->product->sku }} | Brand: {{ $item->product->brand }}</span>
                            @if($item->expire_date)
                                <span class="text-[9px] text-rose-400 block mt-0.5">Expires: {{ \Carbon\Carbon::parse($item->expire_date)->format('Y-m-d') }}</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-6 text-right text-slate-300 mono-text">
                            Rs. {{ number_format($item->buying_price, 2) }}
                            @if($item->discount_amount > 0)
                                <span class="text-[9px] text-rose-400 block">(-Rs. {{ number_format($item->discount_amount, 2) }} total disc)</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-6 text-right text-slate-300 mono-text">
                            Rs. {{ number_format($item->product->price, 2) }}
                        </td>
                        <td class="py-3.5 px-6 text-right text-slate-300 mono-text">
                            Rs. {{ number_format($item->wholesale_price ?: $item->product->wholesale_price, 2) }}
                        </td>
                        <td class="py-3.5 px-6 font-mono text-slate-400">
                            {{ $item->barcode ?: ($item->product->barcode ?: 'None') }}
                        </td>
                        <td class="py-3.5 px-6 text-center font-semibold text-slate-200 mono-text">
                            {{ $item->quantity }}
                            @if($item->free_quantity > 0)
                                <span class="text-[9px] text-emerald-400 block mt-0.5">(+{{ $item->free_quantity }} Free)</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-6 text-right text-cyan-400 font-bold mono-text">
                            Rs. {{ number_format(($item->buying_price - ($item->single_discount_amount ?: 0.00)) * $item->quantity, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Notes & Serial Auto-Generation notice -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-2">
            <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">Shipment Notes / References</span>
            <p class="text-xs text-slate-350 italic">
                {{ $grn->notes ?: 'No records attached to this shipment note.' }}
            </p>
        </div>

        <div class="bg-cyan-500/5 border border-cyan-500/20 rounded-xl p-6 flex items-start gap-4 shadow-[0_0_15px_rgba(6,182,212,0.05)]">
            <div class="text-cyan-400 text-xl pt-1">
                <i class="fa-solid fa-microchip"></i>
            </div>
            <div>
                <span class="text-cyan-400 block uppercase tracking-wider text-[9px] font-black orbitron-title">AUTOMATIC SERIAL NUMBERS</span>
                <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">
                    The system has auto-generated unique, sequential serial number labels for all received stock lines (using pattern `[SKU]-XXXX`). These tags are now activated in the <a href="{{ route('products.serials') }}" class="text-cyan-400 font-semibold underline hover:text-cyan-300">Serials Registry</a> as `In Stock` and are immediately eligible for POS checkout or custom service builds.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
