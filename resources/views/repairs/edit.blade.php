@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3 pb-4 border-b border-slate-800">
        <a href="{{ route('repairs.show', $repair->id) }}" class="p-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-400 hover:text-slate-200 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="orbitron-title text-xl font-black text-slate-100 tracking-wider">EDIT REPAIR JOB</h1>
            <p class="text-slate-400 text-xs mt-0.5 uppercase tracking-widest font-semibold font-sans">Modify parameters for Ticket #{{ $repair->repair_job_no }}</p>
        </div>
    </div>

    <!-- Form Panel -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <form action="{{ route('repairs.update', $repair->id) }}" method="POST" class="space-y-8">
            @csrf

            <!-- Section 1: Customer Details -->
            <div class="space-y-4">
                <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2">1. Client Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Customer Full Name *</label>
                        <input type="text" name="customer_name" required value="{{ old('customer_name', $repair->customer_name) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Contact Phone *</label>
                        <input type="text" name="customer_phone" required value="{{ old('customer_phone', $repair->customer_phone) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">WhatsApp Phone</label>
                        <input type="text" name="customer_whatsapp" value="{{ old('customer_whatsapp', $repair->customer_whatsapp) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">NIC Number</label>
                        <input type="text" name="customer_nic" value="{{ old('customer_nic', $repair->customer_nic) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Email Address</label>
                        <input type="email" name="customer_email" value="{{ old('customer_email', $repair->customer_email) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Company</label>
                        <input type="text" name="customer_company" value="{{ old('customer_company', $repair->customer_company) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Address</label>
                        <input type="text" name="customer_address" value="{{ old('customer_address', $repair->customer_address) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Referred By</label>
                        <input type="text" name="referred_by" value="{{ old('referred_by', $repair->referred_by) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                </div>
            </div>

            <!-- Section 2: Hardware Information -->
            <div class="space-y-4 pt-4 border-t border-slate-800/60">
                <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2">2. Laptop Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Brand Name</label>
                        <input type="text" name="device_brand" value="{{ old('device_brand', $repair->device_brand) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Device Model Name *</label>
                        <input type="text" name="device_model" required value="{{ old('device_model', $repair->device_model) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Serial Number / Tag</label>
                        <input type="text" name="device_serial" value="{{ old('device_serial', $repair->device_serial) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Color</label>
                        <input type="text" name="device_color" value="{{ old('device_color', $repair->device_color) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Processor</label>
                        <input type="text" name="device_processor" value="{{ old('device_processor', $repair->device_processor) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Storage Config</label>
                        <input type="text" name="device_storage" value="{{ old('device_storage', $repair->device_storage) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">RAM Capacity</label>
                        <input type="text" name="device_ram" value="{{ old('device_ram', $repair->device_ram) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Display Size</label>
                        <input type="text" name="device_display_size" value="{{ old('device_display_size', $repair->device_display_size) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Battery Model / Type</label>
                        <input type="text" name="device_battery" value="{{ old('device_battery', $repair->device_battery) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Charger Rating (Watts)</label>
                        <input type="text" name="device_charger_watt" value="{{ old('device_charger_watt', $repair->device_charger_watt) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                </div>
            </div>

            <!-- Section 3: Issues Description -->
            <div class="space-y-4 pt-4 border-t border-slate-800/60">
                <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2">3. Customer Complaint / Error Details</h2>
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Issue / Fault Description *</label>
                    <textarea name="issue_description" required rows="3" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">{{ old('issue_description', $repair->issue_description) }}</textarea>
                </div>
            </div>

            <!-- Section 4: Physical Condition Checklists -->
            <div class="space-y-4 pt-4 border-t border-slate-800/60">
                <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2">4. Physical Condition</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @php
                        $conditions = ['Scratches', 'Cracks', 'Water Damage', 'Bent Body', 'Screen Damage', 'Broken Hinges', 'Missing Rubber Feet', 'Touchpad Damage'];
                        $active_physical = $repair->physical_condition ?: [];
                    @endphp
                    @foreach($conditions as $cond)
                        <label class="inline-flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                            <input type="checkbox" name="physical_condition[]" value="{{ $cond }}" {{ in_array($cond, $active_physical) ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-800 text-cyan-500 focus:ring-0">
                            <span>{{ $cond }}</span>
                        </label>
                    @endforeach
                </div>
                <div class="mt-2">
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Other Physical Notes</label>
                    <input type="text" name="physical_condition_other" value="{{ old('physical_condition_other', $repair->physical_condition_other) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                </div>
            </div>

            <!-- Section 5: Accessories Received -->
            <div class="space-y-4 pt-4 border-t border-slate-800/60">
                <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2">5. Accessories Received</h2>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                    @php
                        $accs = ['Charger', 'Battery', 'RAM', 'Adapter', 'Bag', 'Dock', 'Mouse', 'Missing Screws', 'Keyboard Damage', 'HDD/SSD'];
                        $active_accessories = $repair->accessories_received ?: [];
                    @endphp
                    @foreach($accs as $acc)
                        <label class="inline-flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                            <input type="checkbox" name="accessories_received[]" value="{{ $acc }}" {{ in_array($acc, $active_accessories) ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-800 text-cyan-500 focus:ring-0">
                            <span>{{ $acc }}</span>
                        </label>
                    @endforeach
                </div>
                <div class="mt-2">
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Other Accessories / Notes</label>
                    <input type="text" name="accessories_other" value="{{ old('accessories_other', $repair->accessories_other) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                </div>
            </div>

            <!-- Section 6: Security & Data -->
            <div class="space-y-4 pt-4 border-t border-slate-800/60">
                <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2">6. Security & Data</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Windows Password</label>
                        <input type="text" name="windows_password" value="{{ old('windows_password', $repair->windows_password) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">BIOS Password</label>
                        <input type="text" name="bios_password" value="{{ old('bios_password', $repair->bios_password) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">BitLocker Encryption Status</label>
                        <select name="bitlocker_status" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                            <option value="OFF" {{ $repair->bitlocker_status === 'OFF' ? 'selected' : '' }}>OFF / Unencrypted</option>
                            <option value="ON" {{ $repair->bitlocker_status === 'ON' ? 'selected' : '' }}>ON / Encrypted</option>
                        </select>
                    </div>
                </div>
                <div class="flex flex-col md:flex-row gap-6 pt-2">
                    <label class="inline-flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                        <input type="checkbox" name="data_backup_required" value="1" {{ $repair->data_backup_required ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-800 text-cyan-500 focus:ring-0">
                        <span>Data Backup Required (අත්‍යවශ්‍ය දත්ත උපස්ථ කිරීමක් අවශ්‍යද?)</span>
                    </label>
                    <label class="inline-flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                        <input type="checkbox" name="customer_accept_data_loss" value="1" {{ $repair->customer_accept_data_loss ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-800 text-cyan-500 focus:ring-0">
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
                        $active_inspection = $repair->technical_inspection ?: [];
                    @endphp
                    @foreach($insps as $insp)
                        <label class="inline-flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                            <input type="checkbox" name="technical_inspection[]" value="{{ $insp }}" {{ in_array($insp, $active_inspection) ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-800 text-cyan-500 focus:ring-0">
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
                        $active_chip = $repair->chip_level_repair_notes ?: [];
                    @endphp
                    @foreach($chips as $chip)
                        <label class="inline-flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                            <input type="checkbox" name="chip_level_repair_notes[]" value="{{ $chip }}" {{ in_array($chip, $active_chip) ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-800 text-cyan-500 focus:ring-0">
                            <span>{{ $chip }}</span>
                        </label>
                    @endforeach
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-2">
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Board Model Number</label>
                        <input type="text" name="board_model" value="{{ old('board_model', $repair->board_model) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Freelancer / Technician Name</label>
                        <input type="text" name="freelancer_technician" value="{{ old('freelancer_technician', $repair->freelancer_technician) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Sent Date</label>
                        <input type="date" name="sent_date" value="{{ $repair->sent_date ? $repair->sent_date->format('Y-m-d') : '' }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Return Date</label>
                        <input type="date" name="return_date" value="{{ $repair->return_date ? $repair->return_date->format('Y-m-d') : '' }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                </div>
            </div>

            <!-- Section 9: Status & Costing Details -->
            <div class="space-y-4 pt-4 border-t border-slate-800/60">
                <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2">9. Status & Costing</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Workflow Status *</label>
                        <select name="status" required class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                            <option value="received" {{ $repair->status === 'received' ? 'selected' : '' }}>Received</option>
                            <option value="diagnosing" {{ $repair->status === 'diagnosing' ? 'selected' : '' }}>Diagnosing</option>
                            <option value="repairing" {{ $repair->status === 'repairing' ? 'selected' : '' }}>Repairing</option>
                            <option value="ready" {{ $repair->status === 'ready' ? 'selected' : '' }}>Ready for Pickup (Auto Invoice)</option>
                            <option value="delivered" {{ $repair->status === 'delivered' ? 'selected' : '' }}>Delivered & Closed (Auto Invoice)</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Assigned Diagnostic Technician</label>
                        <select name="assigned_technician_id" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                            <option value="">Leave Unassigned...</option>
                            @foreach($technicians as $tech)
                                <option value="{{ $tech->id }}" {{ $repair->assigned_technician_id == $tech->id ? 'selected' : '' }}>{{ $tech->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Inspection / Diagnostic Fee (Rs.)</label>
                        <input type="number" name="inspection_fee" step="0.01" min="0" value="{{ old('inspection_fee', $repair->inspection_fee) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors mono-text">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Estimated Repair Labor (Rs.) *</label>
                        <input type="number" name="estimate_cost" required step="0.01" min="0" value="{{ old('estimate_cost', $repair->estimate_cost) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors mono-text">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Advance Payment Received (Rs.)</label>
                        <input type="number" name="advance_payment" step="0.01" min="0" value="{{ old('advance_payment', $repair->advance_payment) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors mono-text">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Final Invoice Cost (Rs.) *</label>
                        <input type="number" name="final_cost" required step="0.01" min="0" value="{{ old('final_cost', $repair->final_cost) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors mono-text">
                        <p class="text-[9px] text-slate-500 mt-1 italic">Calculated based on estimate + parts consumed, but customizable.</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Collected By (Name)</label>
                        <input type="text" name="collected_by" value="{{ old('collected_by', $repair->collected_by) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Date Collected</label>
                        <input type="date" name="date_collected" value="{{ $repair->date_collected ? $repair->date_collected->format('Y-m-d') : '' }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Remaining Balance Paid (Rs.)</label>
                        <input type="number" name="remaining_balance_paid" step="0.01" min="0" value="{{ old('remaining_balance_paid', $repair->remaining_balance_paid) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors mono-text">
                    </div>
                </div>
                <div class="md:col-span-2 mt-2">
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Internal Notes & Accessories Info</label>
                    <textarea name="notes" placeholder="e.g. Left with original charger, laptop sleeve, 16GB RAM installed..." rows="2" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">{{ old('notes', $repair->notes) }}</textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-3 border-t border-slate-800 pt-4">
                <a href="{{ route('repairs.show', $repair->id) }}" class="px-4 py-2 bg-slate-850 hover:bg-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-colors">
                    CANCEL
                </a>
                <button type="submit" class="px-4 py-2 bg-cyan-500 text-slate-950 font-black rounded-lg text-xs uppercase tracking-widest transition-all hover:bg-cyan-400 shadow-neon-cyan">
                    UPDATE RECORD
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
