@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-800">
        <div class="flex items-center gap-3">
            <a href="{{ route('neuro_repairs.index') }}" class="p-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-400 hover:text-slate-200 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="orbitron-title text-xl font-black text-slate-100 tracking-wider">{{ $repair->repair_job_no }}</h1>
                    
                    @if($repair->status === 'received')
                        <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-slate-550/10 text-slate-400 border border-slate-500/20">
                            Received
                        </span>
                    @elseif($repair->status === 'diagnosing')
                        <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-violet-500/10 text-violet-400 border border-violet-500/20">
                            Diagnosing
                        </span>
                    @elseif($repair->status === 'repairing')
                        <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                            Repairing
                        </span>
                    @elseif($repair->status === 'ready')
                        <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shadow-[0_0_8px_rgba(16,185,129,0.1)]">
                            Ready for Pickup
                        </span>
                    @elseif($repair->status === 'delivered')
                        <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">
                            Delivered & Closed
                        </span>
                    @endif
                </div>
                <p class="text-slate-400 text-xs mt-0.5 uppercase tracking-widest font-semibold font-sans">Repair Details</p>
            </div>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('neuro_repairs.receipt', $repair->id) }}" target="_blank" class="px-4 py-2 bg-cyan-950 hover:bg-cyan-900 border border-cyan-850/60 text-cyan-400 hover:text-cyan-200 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                <i class="fa-solid fa-receipt"></i>
                <span>PRINT INTAKE FORM</span>
            </a>
            <a href="{{ route('neuro_repairs.edit', $repair->id) }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold rounded-lg text-xs transition-colors flex items-center gap-2 border border-slate-700">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>EDIT REPAIR</span>
            </a>
            <form action="{{ route('neuro_repairs.destroy', $repair->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this repair job?')" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-rose-900/20 hover:bg-rose-900/40 text-rose-400 font-bold rounded-lg text-xs transition-colors flex items-center gap-2 border border-rose-800/40">
                    <i class="fa-solid fa-trash"></i>
                    <span>DELETE</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Alert for auto-generated Invoice -->
    @if($repair->invoice)
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 rounded-xl text-xs font-semibold flex items-center justify-between gap-3 shadow-lg">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-base"></i>
                <div>
                    <span class="font-bold">INVOICE AUTOMATICALLY GENERATED:</span> 
                    Invoice #{{ $repair->invoice->invoice_number }} has been generated for this job since it is marked as completed.
                </div>
            </div>
            <a href="{{ route('neuro_invoices.show', $repair->invoice->id) }}" class="px-3 py-1.5 bg-emerald-550 hover:bg-emerald-500 text-slate-950 font-bold rounded-lg transition-colors text-[10px] uppercase tracking-wider">
                View Invoice
            </a>
        </div>
    @elseif(in_array($repair->status, ['ready', 'delivered']))
        <div class="p-4 bg-amber-500/10 border border-amber-500/30 text-amber-400 rounded-xl text-xs font-semibold flex items-center gap-2">
            <i class="fa-solid fa-circle-info text-base"></i>
            <span>No invoice generated. Update this repair status to trigger the automated invoice generation.</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Details Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Ticket Info Panel -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-4">
                <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800/80 pb-2">1. Client Details</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                    <div>
                        <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">Customer Name</span>
                        <span class="text-slate-200 font-bold text-sm">{{ $repair->customer_name }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">Customer Phone</span>
                        <span class="text-slate-200 font-bold font-mono">{{ $repair->customer_phone }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">WhatsApp Contact</span>
                        <span class="text-slate-200 font-bold font-mono">{{ $repair->customer_whatsapp ?: 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">NIC Number</span>
                        <span class="text-slate-200 font-semibold font-mono">{{ $repair->customer_nic ?: 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">Company / Organization</span>
                        <span class="text-slate-200 font-semibold">{{ $repair->customer_company ?: 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">Referred By</span>
                        <span class="text-slate-200 font-semibold">{{ $repair->referred_by ?: 'N/A' }}</span>
                    </div>
                    <div class="md:col-span-2">
                        <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">Customer Address</span>
                        <span class="text-slate-200">{{ $repair->customer_address ?: 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Laptop Profile Card -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-4">
                <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800/80 pb-2">2. Hardware Profile</h2>
                
                <div class="grid grid-cols-2 md:grid-cols-3 gap-6 text-xs">
                    <div>
                        <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">Brand</span>
                        <span class="text-slate-200 font-semibold">{{ $repair->device_brand ?: 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">Model</span>
                        <span class="text-slate-200 font-semibold">{{ $repair->device_model }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">Serial Tag</span>
                        <span class="text-slate-200 font-semibold font-mono">{{ $repair->device_serial ?: 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">Color</span>
                        <span class="text-slate-200 font-semibold">{{ $repair->device_color ?: 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">Processor</span>
                        <span class="text-slate-200 font-semibold">{{ $repair->device_processor ?: 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">Storage</span>
                        <span class="text-slate-200 font-semibold">{{ $repair->device_storage ?: 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">RAM</span>
                        <span class="text-slate-200 font-semibold">{{ $repair->device_ram ?: 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">Display Size</span>
                        <span class="text-slate-200 font-semibold">{{ $repair->device_display_size ?: 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">Battery</span>
                        <span class="text-slate-200 font-semibold">{{ $repair->device_battery ?: 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">Charger Watts</span>
                        <span class="text-slate-200 font-semibold">{{ $repair->device_charger_watt ?: 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Complaint and Conditions -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-4">
                <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800/80 pb-2">3. Intake Diagnostic Checklist</h2>
                
                <div class="space-y-4 text-xs">
                    <div>
                        <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">Customer Complaint</span>
                        <div class="p-3 bg-slate-950 border border-slate-800 rounded-lg text-slate-300 mt-1 font-medium">
                            {{ $repair->issue_description }}
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                        <div>
                            <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold mb-2">Physical Condition</span>
                            <div class="flex flex-wrap gap-1.5">
                                @forelse($repair->physical_condition ?: [] as $cond)
                                    <span class="px-2 py-1 bg-slate-950 border border-slate-800 rounded text-slate-300">{{ $cond }}</span>
                                @empty
                                    <span class="text-slate-600 italic">No physical issues recorded.</span>
                                @endforelse
                            </div>
                            @if($repair->physical_condition_other)
                                <p class="text-slate-400 mt-2 font-semibold">Other Notes: <span class="text-slate-200">{{ $repair->physical_condition_other }}</span></p>
                            @endif
                        </div>
                        <div>
                            <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold mb-2">Accessories Received</span>
                            <div class="flex flex-wrap gap-1.5">
                                @forelse($repair->accessories_received ?: [] as $acc)
                                    <span class="px-2 py-1 bg-slate-950 border border-slate-800 rounded text-slate-300">{{ $acc }}</span>
                                @empty
                                    <span class="text-slate-600 italic">No accessories received.</span>
                                @endforelse
                            </div>
                            @if($repair->accessories_other)
                                <p class="text-slate-400 mt-2 font-semibold">Other Items: <span class="text-slate-200">{{ $repair->accessories_other }}</span></p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security, Inspection & Chip Level Notes -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-4">
                <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800/80 pb-2">4. Technical Parameters & Security</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                    <div class="space-y-2">
                        <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">Security Credentials</span>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-semibold">Windows Pass</span>
                                <span class="text-slate-200 font-bold font-mono">{{ $repair->windows_password ?: 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-semibold">BIOS Pass</span>
                                <span class="text-slate-200 font-bold font-mono">{{ $repair->bios_password ?: 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="pt-2 space-y-1">
                            <div class="flex justify-between">
                                <span class="text-slate-400">BitLocker Encryption:</span>
                                <span class="font-bold {{ $repair->bitlocker_status === 'ON' ? 'text-amber-400' : 'text-slate-300' }}">{{ $repair->bitlocker_status }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Data Backup Required:</span>
                                <span class="font-bold {{ $repair->data_backup_required ? 'text-cyan-400' : 'text-slate-300' }}">{{ $repair->data_backup_required ? 'YES' : 'NO' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400 text-amber-500">Data Loss Risk Accepted:</span>
                                <span class="font-bold {{ $repair->customer_accept_data_loss ? 'text-emerald-400' : 'text-rose-400' }}">{{ $repair->customer_accept_data_loss ? 'YES' : 'NO' }}</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold mb-2">Technical Inspection Highlights</span>
                        <div class="flex flex-wrap gap-1.5">
                            @forelse($repair->technical_inspection ?: [] as $insp)
                                <span class="px-2 py-0.5 bg-slate-950 border border-slate-850 rounded text-slate-300 text-[10px]">{{ $insp }}</span>
                            @empty
                                <span class="text-slate-600 italic">No inspection tags checked.</span>
                            @endforelse
                        </div>
                    </div>
                    @if($repair->chip_level_repair_notes)
                        <div class="md:col-span-2 pt-2 border-t border-slate-800/60">
                            <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold mb-2">Chip Level Repair Notes</span>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($repair->chip_level_repair_notes as $chip)
                                    <span class="px-2 py-0.5 bg-rose-950/20 border border-rose-900/30 rounded text-rose-350 text-[10px]">{{ $chip }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <div class="md:col-span-2 grid grid-cols-2 md:grid-cols-4 gap-4 pt-2">
                        <div>
                            <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">Board Model</span>
                            <span class="text-slate-200 font-semibold font-mono">{{ $repair->board_model ?: 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">Freelancer Tech</span>
                            <span class="text-slate-200 font-semibold">{{ $repair->freelancer_technician ?: 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">Sent Date</span>
                            <span class="text-slate-200 font-mono">{{ $repair->sent_date ? $repair->sent_date->format('Y-m-d') : 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">Return Date</span>
                            <span class="text-slate-200 font-mono">{{ $repair->return_date ? $repair->return_date->format('Y-m-d') : 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Spare Parts Used -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-800 flex justify-between items-center">
                    <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest">Spare Parts Used</h2>
                    <span class="text-[10px] text-slate-400 uppercase tracking-widest font-mono font-bold">Items: {{ $repair->items->count() }}</span>
                </div>
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-widest font-semibold text-[9px] bg-slate-900/40">
                            <th class="py-3 px-6">Part Description</th>
                            <th class="py-3 px-6 font-mono">SKU</th>
                            <th class="py-3 px-6 text-right">Unit Price</th>
                            <th class="py-3 px-6 text-center">Qty Used</th>
                            <th class="py-3 px-6 text-right">Total Price</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-850">
                        @forelse($repair->items as $item)
                            <tr class="hover:bg-slate-800/5 transition-colors">
                                <td class="py-3.5 px-6 font-bold text-slate-200">
                                    {{ $item->product->name }}
                                </td>
                                <td class="py-3.5 px-6 text-slate-400 uppercase tracking-wider mono-text">
                                    {{ $item->product->sku }}
                                </td>
                                <td class="py-3.5 px-6 text-right text-slate-400 mono-text">
                                    Rs. {{ number_format($item->price, 2) }}
                                </td>
                                <td class="py-3.5 px-6 text-center font-semibold text-slate-200 mono-text">
                                    {{ $item->quantity }}
                                </td>
                                <td class="py-3.5 px-6 text-right text-cyan-400 font-bold mono-text">
                                    Rs. {{ number_format($item->price * $item->quantity, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-slate-600">
                                    <i class="fa-solid fa-microchip text-xl mb-1 block opacity-40"></i>
                                    <span>No spare parts used for this repair.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sidebar Actions Column -->
        <div class="space-y-6">
            <!-- Costing Overview Card -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-4">
                <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800/80 pb-2">Financial Summary</h2>
                <div class="space-y-3 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-450">Diagnostic Fee:</span>
                        <span class="font-bold text-slate-200 mono-text">Rs. {{ number_format($repair->inspection_fee, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-450">Labor Estimate:</span>
                        <span class="font-bold text-slate-200 mono-text">Rs. {{ number_format($repair->estimate_cost, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-450">Spare Parts Cost:</span>
                        <span class="font-bold text-slate-200 mono-text">
                            Rs. {{ number_format($repair->items->sum(fn($i) => $i->price * $i->quantity), 2) }}
                        </span>
                    </div>
                    <div class="border-t border-slate-800 pt-3 flex justify-between items-center">
                        <span class="text-xs text-slate-400 uppercase tracking-wider font-bold">Total Bill:</span>
                        <span class="text-lg font-black text-cyan-400 mono-text">Rs. {{ number_format($repair->final_cost, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-1">
                        <span class="text-slate-450">Advance Payment:</span>
                        <span class="font-bold text-emerald-400 mono-text">Rs. {{ number_format($repair->advance_payment, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-450">Balance Due:</span>
                        <span class="font-bold text-rose-400 mono-text">Rs. {{ number_format($repair->final_cost - $repair->advance_payment, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Technician Details Card -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-3 text-xs">
                <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800/80 pb-2">Technician Assignment</h2>
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400 text-lg">
                        <i class="fa-solid fa-user-gear"></i>
                    </div>
                    <div>
                        @if($repair->technician)
                            <span class="font-bold text-slate-200 block text-sm">{{ $repair->technician->name }}</span>
                            <span class="text-[10px] uppercase tracking-wider text-slate-500 font-bold block">Assigned Technician</span>
                        @else
                            <span class="font-bold text-rose-400 block text-sm">Unassigned</span>
                            <span class="text-[10px] uppercase tracking-wider text-slate-500 font-bold block">No technician assigned</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Collection Card -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-3 text-xs">
                <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800/80 pb-2">Collection Confirmation</h2>
                @if($repair->collected_by || $repair->date_collected)
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-slate-450">Collected By:</span>
                            <span class="font-bold text-slate-200">{{ $repair->collected_by }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-450">Date Collected:</span>
                            <span class="font-bold text-slate-200 font-mono">{{ $repair->date_collected ? $repair->date_collected->format('Y-m-d') : 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-450">Remaining Balance Paid:</span>
                            <span class="font-bold text-emerald-400 font-mono">Rs. {{ number_format($repair->remaining_balance_paid, 2) }}</span>
                        </div>
                    </div>
                @else
                    <span class="text-slate-500 italic block">Not yet collected by the customer.</span>
                @endif
            </div>

            <!-- Add Spare Part Console Form -->
            @if($repair->status !== 'delivered')
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-4">
                    <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800/80 pb-2">Add Spare Part</h2>
                    <form action="{{ route('neuro_repairs.parts', $repair->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Select Part</label>
                            <select name="product_id" required class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                                <option value="">Select Part...</option>
                                @foreach($spareParts as $part)
                                    <option value="{{ $part->id }}">{{ $part->name }} [SKU: {{ $part->sku }}] (Rs. {{ number_format($part->price, 2) }} - Stock: {{ $part->stock }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Quantity Used</label>
                            <input type="number" name="quantity" required min="1" value="1" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 mono-text">
                        </div>
                        <button type="submit" class="w-full py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold rounded-lg text-xs uppercase tracking-wider transition-colors border border-slate-700">
                            ADD TO BILL
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
