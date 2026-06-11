@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header Controls -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-800">
        <div class="flex items-center gap-3">
            <a href="{{ route('neuro_invoices.index') }}" class="p-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-400 hover:text-slate-200 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="orbitron-title text-xl font-black text-slate-100 tracking-wider">INVOICE #{{ $invoice->invoice_number }}</h1>
                <p class="text-slate-400 text-xs mt-0.5 uppercase tracking-widest font-semibold">Details of the invoice</p>
            </div>
        </div>
        
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('neuro_invoices.print', $invoice->id) }}" target="_blank" class="px-3 py-1.5 bg-cyan-500 text-slate-950 font-bold rounded-lg text-xs transition-all hover:bg-cyan-400 shadow-neon-cyan flex items-center gap-2">
                <i class="fa-solid fa-print"></i>
                <span>PRINT</span>
            </a>
            <a href="{{ route('neuro_invoices.print', $invoice->id) }}?download=1" target="_blank" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold rounded-lg text-xs transition-colors flex items-center gap-2">
                <i class="fa-solid fa-file-pdf"></i>
                <span>DOWNLOAD</span>
            </a>
            <a href="{{ route('neuro_invoices.edit', $invoice->id) }}" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold rounded-lg text-xs transition-colors flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>EDIT</span>
            </a>
            
            @php
                $emailSubject = rawurlencode("Invoice #" . $invoice->invoice_number . " - NEURONET");
                $emailBody = rawurlencode("Hi,\n\nPlease find your invoice #" . $invoice->invoice_number . " details here: " . route('neuro_invoices.show', $invoice->id) . "\n\nThank you for choosing NEURONET Computer Store!");
                
                $whatsappText = rawurlencode("Hi, please find your invoice #" . $invoice->invoice_number . " details here: " . route('neuro_invoices.show', $invoice->id));
            @endphp

            <a href="mailto:{{ $invoice->customer ? $invoice->customer->email : '' }}?subject={{ $emailSubject }}&body={{ $emailBody }}" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold rounded-lg text-xs transition-colors flex items-center gap-2">
                <i class="fa-solid fa-envelope"></i>
                <span>EMAIL</span>
            </a>
            <a href="https://api.whatsapp.com/send?text={{ $whatsappText }}" target="_blank" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-lg text-xs transition-colors flex items-center gap-2">
                <i class="fa-brands fa-whatsapp"></i>
                <span>WHATSAPP</span>
            </a>

            @if(Auth::user()->hasPermission('create-warranty'))
            <a href="{{ route('warranty.create', ['invoice_id' => $invoice->id]) }}" class="px-3 py-1.5 bg-purple-650 hover:bg-purple-600 text-white font-bold rounded-lg text-xs transition-colors flex items-center gap-2">
                <i class="fa-solid fa-shield-halved"></i>
                <span>WARRANTY CLAIM</span>
            </a>
            @endif

            <form action="{{ route('neuro_invoices.destroy', $invoice->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this invoice?')" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3 py-1.5 bg-rose-950/20 border border-rose-900/50 hover:bg-rose-900/20 text-rose-400 font-bold rounded-lg text-xs transition-all flex items-center gap-2">
                    <i class="fa-solid fa-trash"></i>
                    <span>DELETE</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Billing Details -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 space-y-3">
            <h3 class="orbitron-title text-xs font-black text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2">Details</h3>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-400">Invoice Ref:</span>
                    <span class="font-bold text-slate-200">{{ $invoice->invoice_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Date Issued:</span>
                    <span class="text-slate-200">{{ $invoice->created_at->format('Y-m-d h:i A') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Cashier:</span>
                    <span class="text-slate-200 font-semibold">{{ $invoice->user->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Salesperson:</span>
                    <span class="text-slate-200 font-semibold">{{ $invoice->employee->name ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Sale Type:</span>
                    <span class="text-slate-200 font-semibold">{{ $invoice->sale_type }}</span>
                </div>
                @if($invoice->due_date)
                    <div class="flex justify-between">
                        <span class="text-slate-400">Due Date:</span>
                        <span class="text-rose-400 font-bold">{{ $invoice->due_date->format('Y-m-d') }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Customer Profile -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 space-y-3">
            <h3 class="orbitron-title text-xs font-black text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2">Customer Info</h3>
            <div class="space-y-2 text-xs">
                @if($invoice->customer)
                    <div class="flex justify-between">
                        <span class="text-slate-400">Name:</span>
                        <span class="font-bold text-slate-200">{{ $invoice->customer->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Contact:</span>
                        <span class="text-slate-200 font-semibold">{{ $invoice->customer->phone }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Address:</span>
                        <span class="text-slate-300">{{ $invoice->customer->address ?? 'N/A' }}</span>
                    </div>
                @else
                    <div class="text-center py-4 text-slate-500 italic">
                        <span>Walk-in / Anonymous Customer</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- System Profile -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 space-y-3">
            <h3 class="orbitron-title text-xs font-black text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2">Payment Info</h3>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between items-center">
                    <span class="text-slate-400">Tax Mode:</span>
                    <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold
                        {{ $invoice->is_tax_invoice ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'bg-slate-800 text-slate-400 border border-slate-700' }}">
                        {{ $invoice->is_tax_invoice ? 'Tax Invoice (15% VAT)' : 'Standard Invoice' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Payment:</span>
                    <span class="text-slate-200 font-bold uppercase">{{ $invoice->payment_method }}</span>
                </div>
                @if($invoice->bankAccount)
                    <div class="flex flex-col text-slate-400 pt-1 border-t border-slate-800/50 mt-1 space-y-1">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Bank:</span>
                            <span class="text-slate-200 font-semibold">{{ $invoice->bankAccount->bank_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Account No:</span>
                            <span class="text-slate-300 font-mono text-[11px] font-bold">{{ $invoice->bankAccount->account_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Name:</span>
                            <span class="text-slate-400 text-[10px] italic">{{ $invoice->bankAccount->account_name }}</span>
                        </div>
                    </div>
                @endif
                @if($invoice->customer)
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400">Loyalty Earned:</span>
                        <span class="text-emerald-400 font-bold">+{{ floor($invoice->total / 100) }} points</span>
                    </div>
                @endif
                <div class="flex justify-between items-center">
                    <span class="text-slate-400">Status:</span>
                    @if($invoice->status === 'paid')
                        <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Fully Paid</span>
                    @elseif($invoice->status === 'partial')
                        <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">Partially Paid</span>
                    @elseif($invoice->status === 'installment')
                        <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-purple-500/10 text-purple-400 border border-purple-500/20">Installment</span>
                    @else
                        <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">Unpaid</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Special Note -->
    @if($invoice->special_note)
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 text-xs text-slate-300">
            <span class="font-bold text-slate-400 block mb-1">Special Note:</span>
            <p>{{ $invoice->special_note }}</p>
        </div>
    @endif

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
                    <th class="py-3 px-6">Assigned Serial</th>
                    <th class="py-3 px-6 text-center font-bold">Qty</th>
                    <th class="py-3 px-6 text-center font-bold">Free Qty</th>
                    <th class="py-3 px-6 text-right">Unit Price</th>
                    <th class="py-3 px-6 text-right">Discount</th>
                    <th class="py-3 px-6">Warranty</th>
                    <th class="py-3 px-6 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-850">
                @foreach($invoice->items as $item)
                    <tr class="hover:bg-slate-800/10 transition-colors">
                        <td class="py-3.5 px-6">
                            <span class="font-bold text-slate-200 block">{{ $item->product->name }}</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">{{ $item->product->brand }}</span>
                        </td>
                        <td class="py-3.5 px-6 font-semibold text-slate-300">{{ $item->product->sku }}</td>
                        <td class="py-3.5 px-6 text-cyan-400 font-bold mono-text">{{ $item->serial_number ?? 'N/A' }}</td>
                        <td class="py-3.5 px-6 text-center text-slate-200 font-bold">{{ $item->quantity }}</td>
                        <td class="py-3.5 px-6 text-center text-slate-400 font-bold">{{ $item->free_quantity }}</td>
                        <td class="py-3.5 px-6 text-right text-slate-300 mono-text">Rs. {{ number_format($item->unit_price, 2) }}</td>
                        <td class="py-3.5 px-6 text-right text-rose-400 mono-text">
                            @if($item->discount_amount > 0)
                                -Rs. {{ number_format($item->discount_amount, 2) }} ({{ $item->discount_percentage }}%)
                            @else
                                0.00
                            @endif
                        </td>
                        <td class="py-3.5 px-6 text-slate-300">{{ $item->warranty ?? 'N/A' }}</td>
                        <td class="py-3.5 px-6 text-right text-slate-100 font-bold mono-text">Rs. {{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary section -->
        <div class="p-6 border-t border-slate-800 bg-slate-950/40 flex justify-end">
            <div class="w-80 space-y-2 text-xs text-slate-400">
                <div class="flex justify-between">
                    <span>Subtotal:</span>
                    <span class="font-bold text-slate-200 mono-text">Rs. {{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Global Discount:</span>
                    <span class="font-bold text-rose-400 mono-text">
                        @if($invoice->global_discount_amount > 0)
                            -Rs. {{ number_format($invoice->global_discount_amount, 2) }} ({{ $invoice->global_discount_percentage }}%)
                        @else
                            0.00
                        @endif
                    </span>
                </div>
                <div class="flex justify-between">
                    <span>Service Charges:</span>
                    <span class="font-bold text-slate-200 mono-text">Rs. {{ number_format($invoice->service_charges, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Tax (VAT {{ $invoice->is_tax_invoice ? '15%' : '0%' }}):</span>
                    <span class="font-bold text-slate-200 mono-text">Rs. {{ number_format($invoice->tax, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm font-bold text-cyan-400 border-t border-slate-800 pt-2">
                    <span>Grand Total:</span>
                    <span class="text-base font-black mono-text">Rs. {{ number_format($invoice->total, 2) }}</span>
                </div>
                <div class="flex justify-between border-t border-slate-850 pt-2">
                    <span>Customer Paid:</span>
                    <span class="font-bold text-slate-200 mono-text">Rs. {{ number_format($invoice->customer_paid, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Balance Returned:</span>
                    <span class="font-bold text-emerald-400 mono-text">Rs. {{ number_format($invoice->balance, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
