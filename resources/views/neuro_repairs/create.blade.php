@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3 pb-4 border-b border-slate-800">
        <a href="{{ route('neuro_repairs.index') }}" class="p-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-400 hover:text-slate-200 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="orbitron-title text-xl font-black text-slate-100 tracking-wider">LOG NEW REPAIR TICKET</h1>
            <p class="text-slate-400 text-xs mt-0.5 uppercase tracking-widest font-semibold font-sans">Intake logger for client hardware diagnostics and repair estimates</p>
        </div>
    </div>

    <!-- Form Panel -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <form action="{{ route('neuro_repairs.store') }}" method="POST" class="space-y-8">
            @csrf

            <!-- Section 1: Customer Details -->
            <div class="space-y-4">
                <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2">1. Client Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Customer Full Name *</label>
                        <input type="text" name="customer_name" required placeholder="e.g. David Silva" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Contact Phone *</label>
                        <input type="text" name="customer_phone" required placeholder="e.g. 0777123456" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">WhatsApp Phone</label>
                        <input type="text" name="customer_whatsapp" placeholder="e.g. 0777123456" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">NIC Number</label>
                        <input type="text" name="customer_nic" placeholder="e.g. 199905202888" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Email Address</label>
                        <input type="email" name="customer_email" placeholder="e.g. client@example.com" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Company</label>
                        <input type="text" name="customer_company" placeholder="e.g. Neuronet Labs" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Address</label>
                        <input type="text" name="customer_address" placeholder="e.g. No 12, Galle Road, Colombo" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Referred By</label>
                        <input type="text" name="referred_by" placeholder="e.g. Friend, Google Search" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                </div>
            </div>

            <!-- Section 2: Hardware Information -->
            <div class="space-y-4 pt-4 border-t border-slate-800/60">
                <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2">2. Laptop Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Brand Name</label>
                        <input type="text" name="device_brand" placeholder="e.g. Asus, Dell, Lenovo" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Device Model Name *</label>
                        <input type="text" name="device_model" required placeholder="e.g. ROG Strix G15" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Serial Number / Tag</label>
                        <input type="text" name="device_serial" placeholder="e.g. SN-ROG-882910" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Color</label>
                        <input type="text" name="device_color" placeholder="e.g. Eclipse Gray" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Processor</label>
                        <input type="text" name="device_processor" placeholder="e.g. Intel i7-12700H" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Storage Config</label>
                        <input type="text" name="device_storage" placeholder="e.g. 512GB NVMe SSD" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">RAM Capacity</label>
                        <input type="text" name="device_ram" placeholder="e.g. 16GB DDR5" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Display Size</label>
                        <input type="text" name="device_display_size" placeholder="e.g. 15.6 Inch" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Battery Model / Type</label>
                        <input type="text" name="device_battery" placeholder="e.g. 90Wh 4-Cell" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Charger Rating (Watts)</label>
                        <input type="text" name="device_charger_watt" placeholder="e.g. 240W" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                </div>
            </div>

            <!-- Section 3: Issues Description -->
            <div class="space-y-4 pt-4 border-t border-slate-800/60">
                <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2">3. Customer Complaint / Error Details</h2>
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Issue / Fault Description *</label>
                    <textarea name="issue_description" required placeholder="Describe the failure symptoms in detail..." rows="3" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors"></textarea>
                </div>
            </div>

            <!-- Section 4: Physical Condition Checklists -->
            <div class="space-y-4 pt-4 border-t border-slate-800/60">
                <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2">4. Physical Condition</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @php
                        $conditions = ['Scratches', 'Cracks', 'Water Damage', 'Bent Body', 'Screen Damage', 'Broken Hinges', 'Missing Rubber Feet', 'Touchpad Damage'];
                    @endphp
                    @foreach($conditions as $cond)
                        <label class="inline-flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                            <input type="checkbox" name="physical_condition[]" value="{{ $cond }}" class="rounded bg-slate-950 border-slate-800 text-cyan-500 focus:ring-0">
                            <span>{{ $cond }}</span>
                        </label>
                    @endforeach
                </div>
                <div class="mt-2">
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Other Physical Notes</label>
                    <input type="text" name="physical_condition_other" placeholder="e.g. Scuffed bottom casing" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                </div>
            </div>

            <!-- Section 5: Accessories Received -->
            <div class="space-y-4 pt-4 border-t border-slate-800/60">
                <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2">5. Accessories Received</h2>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                    @php
                        $accs = ['Charger', 'Battery', 'RAM', 'Adapter', 'Bag', 'Dock', 'Mouse', 'Missing Screws', 'Keyboard Damage', 'HDD/SSD'];
                    @endphp
                    @foreach($accs as $acc)
                        <label class="inline-flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                            <input type="checkbox" name="accessories_received[]" value="{{ $acc }}" class="rounded bg-slate-950 border-slate-800 text-cyan-500 focus:ring-0">
                            <span>{{ $acc }}</span>
                        </label>
                    @endforeach
                </div>
                <div class="mt-2">
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Other Accessories / Notes</label>
                    <input type="text" name="accessories_other" placeholder="e.g. Original laptop box, pen drive" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                </div>
            </div>

            <!-- Section 6: Security & Data -->
            <div class="space-y-4 pt-4 border-t border-slate-800/60">
                <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2">6. Security & Data</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Windows Password</label>
                        <input type="text" name="windows_password" placeholder="e.g. Admin123" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">BIOS Password</label>
                        <input type="text" name="bios_password" placeholder="e.g. bios@99" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">BitLocker Encryption Status</label>
                        <select name="bitlocker_status" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                            <option value="OFF">OFF / Unencrypted</option>
                            <option value="ON">ON / Encrypted</option>
                        </select>
                    </div>
                </div>
                <div class="flex flex-col md:flex-row gap-6 pt-2">
                    <label class="inline-flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                        <input type="checkbox" name="data_backup_required" value="1" class="rounded bg-slate-950 border-slate-800 text-cyan-500 focus:ring-0">
                        <span>Data Backup Required (අත්‍යවශ්‍ය දත්ත උපස්ථ කිරීමක් අවශ්‍යද?)</span>
                    </label>
                    <label class="inline-flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                        <input type="checkbox" name="customer_accept_data_loss" value="1" class="rounded bg-slate-950 border-slate-800 text-cyan-500 focus:ring-0">
                        <span class="text-amber-400 font-semibold">Customer Accepts Data Loss Risk (දත්ත අහිමි වීමේ අවදානම පිළිගනී)</span>
                    </label>
                </div>
            </div>

            <!-- Section 7: Technical Inspection -->
            <div class="space-y-4 pt-4 border-t border-slate-800/60">
                <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2">7. Technical Inspection</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @php
                        $insps = ['Display', 'Power ON', 'Charging', 'USB Ports', 'Keyboard', 'WiFi', 'Audio', 'Fan', 'Camera', 'Battery Detection', 'Board Condition', 'Overheating'];
                    @endphp
                    @foreach($insps as $insp)
                        <label class="inline-flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                            <input type="checkbox" name="technical_inspection[]" value="{{ $insp }}" class="rounded bg-slate-950 border-slate-800 text-cyan-500 focus:ring-0">
                            <span>{{ $insp }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Section 8: Chip Level Repair Notes -->
            <div class="space-y-4 pt-4 border-t border-slate-800/60">
                <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2">8. Chip Level Repair Notes</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @php
                        $chips = ['No Power', 'No Display', 'Water Damage', 'Short Circuit', 'BIOS Issue', 'CPU Rail Issue', 'Charging IC', 'Dead Board', 'GPU Issue', 'Overheating'];
                    @endphp
                    @foreach($chips as $chip)
                        <label class="inline-flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                            <input type="checkbox" name="chip_level_repair_notes[]" value="{{ $chip }}" class="rounded bg-slate-950 border-slate-800 text-cyan-500 focus:ring-0">
                            <span>{{ $chip }}</span>
                        </label>
                    @endforeach
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-2">
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Board Model Number</label>
                        <input type="text" name="board_model" placeholder="e.g. LA-D801P" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Freelancer / Technician Name</label>
                        <input type="text" name="freelancer_technician" placeholder="e.g. Nuwan" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Sent Date</label>
                        <input type="date" name="sent_date" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Return Date</label>
                        <input type="date" name="return_date" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                </div>
            </div>

            <!-- Section 9: Costing Details -->
            <div class="space-y-4 pt-4 border-t border-slate-800/60">
                <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2">9. Costing & Estimates</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Inspection / Diagnostic Fee (Rs.)</label>
                        <input type="number" name="inspection_fee" step="0.01" min="0" value="0.00" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors mono-text">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Estimated Repair Labor (Rs.) *</label>
                        <input type="number" name="estimate_cost" required step="0.01" min="0" value="0.00" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors mono-text">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Advance Payment Received (Rs.)</label>
                        <input type="number" name="advance_payment" step="0.01" min="0" value="0.00" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors mono-text">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Assigned Diagnostic Technician</label>
                        <select name="assigned_technician_id" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                            <option value="">Leave Unassigned...</option>
                            @foreach($technicians as $tech)
                                <option value="{{ $tech->id }}">{{ $tech->name }} (Active Tech)</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="md:col-span-2">
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Internal Notes & Description</label>
                    <textarea name="notes" placeholder="e.g. Left with original charger, laptop sleeve, 16GB RAM installed..." rows="2" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors"></textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-3 border-t border-slate-800 pt-4">
                <a href="{{ route('neuro_repairs.index') }}" class="px-4 py-2 bg-slate-850 hover:bg-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-colors">
                    CANCEL
                </a>
                <button type="submit" class="px-4 py-2 bg-cyan-500 text-slate-950 font-black rounded-lg text-xs uppercase tracking-widest transition-all hover:bg-cyan-400 shadow-neon-cyan">
                    CREATE SERVICE TICKET
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
