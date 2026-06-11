<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLOUDTECH - Repair Receipt {{ $repair->repair_job_no }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            background: #fff;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header-title h1 {
            font-size: 20px;
            font-weight: 800;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header-title p {
            font-size: 11px;
            color: #64748b;
            margin: 2px 0 0 0;
            font-weight: 600;
        }
        .job-badge {
            border: 2px solid #000;
            padding: 8px 15px;
            text-align: center;
            background: #f8fafc;
        }
        .job-badge div {
            font-size: 9px;
            text-transform: uppercase;
            font-weight: bold;
            color: #64748b;
        }
        .job-badge span {
            font-size: 16px;
            font-weight: 900;
            font-family: monospace;
        }
        .section-title {
            background: #0f172a;
            color: #fff;
            padding: 4px 8px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 15px;
            margin-bottom: 10px;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
        }
        .grid-4 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 8px;
        }
        .field-group {
            margin-bottom: 6px;
            display: flex;
            align-items: baseline;
        }
        .field-label {
            font-weight: bold;
            color: #475569;
            width: 110px;
            flex-shrink: 0;
        }
        .field-value {
            border-bottom: 1px dotted #cbd5e1;
            flex-grow: 1;
            padding-left: 5px;
            font-weight: 600;
        }
        .checklist {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 5px;
            margin-top: 5px;
        }
        .checklist-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .checkbox-box {
            width: 11px;
            height: 11px;
            border: 1px solid #000;
            display: inline-block;
            text-align: center;
            line-height: 10px;
            font-weight: bold;
            font-size: 9px;
        }
        .text-area {
            border: 1px solid #cbd5e1;
            padding: 8px;
            min-height: 40px;
            background: #fafafa;
            white-space: pre-wrap;
            margin-top: 5px;
        }
        .terms-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 15px;
            font-size: 9px;
            color: #475569;
        }
        .terms-column h3 {
            font-size: 10px;
            color: #0f172a;
            margin: 0 0 5px 0;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 2px;
        }
        .terms-column ol {
            margin: 0;
            padding-left: 15px;
        }
        .terms-column li {
            margin-bottom: 4px;
            line-height: 1.3;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #cbd5e1;
        }
        .signature-line {
            width: 200px;
            text-align: center;
        }
        .signature-line div {
            border-top: 1px solid #000;
            margin-top: 40px;
            padding-top: 4px;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }
        .no-print-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #0f172a;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        @media print {
            .no-print-btn {
                display: none;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Print Action Button -->
    <button onclick="window.print()" class="no-print-btn">
        <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
        </svg>
        <span>Print Document</span>
    </button>

    <!-- Header Block -->
    <div class="header">
        <div class="header-title">
            <h1>CLOUDTECH COMPUTERS</h1>
            <p>Professional Laptop Repair Intake & Service Form</p>
        </div>
        <div class="job-badge">
            <div>Job No</div>
            <span>{{ $repair->repair_job_no }}</span>
        </div>
    </div>

    <!-- Meta Details Grid -->
    <div class="grid-3">
        <div class="field-group">
            <span class="field-label" style="width: 70px;">Received By:</span>
            <span class="field-value">{{ $repair->technician ? $repair->technician->name : 'System Manager' }}</span>
        </div>
        <div class="field-group">
            <span class="field-label" style="width: 50px;">Date:</span>
            <span class="field-value">{{ $repair->created_at->format('Y-m-d') }}</span>
        </div>
        <div class="field-group">
            <span class="field-label" style="width: 60px;">Priority:</span>
            <span class="field-value" style="text-transform: uppercase;">Normal</span>
        </div>
    </div>

    <!-- Customer Information -->
    <div class="section-title">1. Customer Information</div>
    <div class="grid-2">
        <div>
            <div class="field-group">
                <span class="field-label">Customer Name:</span>
                <span class="field-value">{{ $repair->customer_name }}</span>
            </div>
            <div class="field-group">
                <span class="field-label">Phone:</span>
                <span class="field-value">{{ $repair->customer_phone }}</span>
            </div>
            <div class="field-group">
                <span class="field-label">WhatsApp:</span>
                <span class="field-value">{{ $repair->customer_whatsapp ?: $repair->customer_phone }}</span>
            </div>
            <div class="field-group">
                <span class="field-label">Address:</span>
                <span class="field-value">{{ $repair->customer_address ?: 'N/A' }}</span>
            </div>
        </div>
        <div>
            <div class="field-group">
                <span class="field-label">NIC Number:</span>
                <span class="field-value">{{ $repair->customer_nic ?: 'N/A' }}</span>
            </div>
            <div class="field-group">
                <span class="field-label">Email:</span>
                <span class="field-value">{{ $repair->customer_email ?: 'N/A' }}</span>
            </div>
            <div class="field-group">
                <span class="field-label">Company:</span>
                <span class="field-value">{{ $repair->customer_company ?: 'N/A' }}</span>
            </div>
            <div class="field-group">
                <span class="field-label">Referred By:</span>
                <span class="field-value">{{ $repair->referred_by ?: 'N/A' }}</span>
            </div>
        </div>
    </div>

    <!-- Laptop Information -->
    <div class="section-title">2. Laptop Information</div>
    <div class="grid-3">
        <div>
            <div class="field-group"><span class="field-label" style="width:80px;">Brand:</span><span class="field-value">{{ $repair->device_brand ?: 'N/A' }}</span></div>
            <div class="field-group"><span class="field-label" style="width:80px;">Model:</span><span class="field-value">{{ $repair->device_model }}</span></div>
            <div class="field-group"><span class="field-label" style="width:80px;">Serial Number:</span><span class="field-value">{{ $repair->device_serial ?: 'N/A' }}</span></div>
        </div>
        <div>
            <div class="field-group"><span class="field-label" style="width:80px;">Color:</span><span class="field-value">{{ $repair->device_color ?: 'N/A' }}</span></div>
            <div class="field-group"><span class="field-label" style="width:80px;">Processor:</span><span class="field-value">{{ $repair->device_processor ?: 'N/A' }}</span></div>
            <div class="field-group"><span class="field-label" style="width:80px;">Storage:</span><span class="field-value">{{ $repair->device_storage ?: 'N/A' }}</span></div>
        </div>
        <div>
            <div class="field-group"><span class="field-label" style="width:80px;">RAM:</span><span class="field-value">{{ $repair->device_ram ?: 'N/A' }}</span></div>
            <div class="field-group"><span class="field-label" style="width:80px;">Display Size:</span><span class="field-value">{{ $repair->device_display_size ?: 'N/A' }}</span></div>
            <div class="field-group"><span class="field-label" style="width:80px;">Charger Watt:</span><span class="field-value">{{ $repair->device_charger_watt ?: 'N/A' }}</span></div>
        </div>
    </div>

    <!-- Customer Complaint -->
    <div class="section-title">3. Customer Complaint / Error Details</div>
    <div class="text-area">{{ $repair->issue_description }}</div>

    <!-- Physical Condition -->
    <div class="section-title">4. Physical Condition</div>
    <div class="checklist">
        @php
            $physical_conditions = ['Scratches', 'Cracks', 'Water Damage', 'Bent Body', 'Screen Damage', 'Broken Hinges', 'Missing Rubber Feet', 'Touchpad Damage'];
            $active_physical = $repair->physical_condition ?: [];
        @endphp
        @foreach($physical_conditions as $cond)
            <div class="checklist-item">
                <span class="checkbox-box">{!! in_array($cond, $active_physical) ? '✓' : '&nbsp;' !!}</span>
                <span>{{ $cond }}</span>
            </div>
        @endforeach
    </div>
    <div class="field-group" style="margin-top:8px;">
        <span class="field-label" style="width: 80px;">Other Notes:</span>
        <span class="field-value">{{ $repair->physical_condition_other ?: 'None' }}</span>
    </div>

    <!-- Accessories Received -->
    <div class="section-title">5. Accessories Received</div>
    <div class="checklist">
        @php
            $accessories = ['Charger', 'Battery', 'RAM', 'Adapter', 'Bag', 'Dock', 'Mouse', 'Missing Screws', 'Keyboard Damage', 'HDD/SSD'];
            $active_accessories = $repair->accessories_received ?: [];
        @endphp
        @foreach($accessories as $acc)
            <div class="checklist-item">
                <span class="checkbox-box">{!! in_array($acc, $active_accessories) ? '✓' : '&nbsp;' !!}</span>
                <span>{{ $acc }}</span>
            </div>
        @endforeach
    </div>
    <div class="field-group" style="margin-top:8px;">
        <span class="field-label" style="width: 80px;">Other:</span>
        <span class="field-value">{{ $repair->accessories_other ?: 'None' }}</span>
    </div>

    <!-- Security & Data -->
    <div class="section-title">6. Security & Data (From Customer)</div>
    <div class="grid-2">
        <div>
            <div class="field-group"><span class="field-label">Windows Password:</span><span class="field-value">{{ $repair->windows_password ?: 'N/A' }}</span></div>
            <div class="field-group"><span class="field-label">BIOS Password:</span><span class="field-value">{{ $repair->bios_password ?: 'N/A' }}</span></div>
            <div class="field-group">
                <span class="field-label">BitLocker Status:</span>
                <span class="checkbox-box" style="margin-right:5px;">{!! $repair->bitlocker_status === 'ON' ? '✓' : '&nbsp;' !!}</span> ON
                <span class="checkbox-box" style="margin-left:15px; margin-right:5px;">{!! $repair->bitlocker_status === 'OFF' ? '✓' : '&nbsp;' !!}</span> OFF
            </div>
        </div>
        <div>
            <div class="field-group">
                <span class="field-label">Data Backup Req:</span>
                <span class="checkbox-box" style="margin-right:5px;">{!! $repair->data_backup_required ? '✓' : '&nbsp;' !!}</span> YES
                <span class="checkbox-box" style="margin-left:15px; margin-right:5px;">{!! !$repair->data_backup_required ? '✓' : '&nbsp;' !!}</span> NO
            </div>
            <div class="field-group">
                <span class="field-label">Data Loss Risk Accepted:</span>
                <span class="checkbox-box" style="margin-right:5px;">{!! $repair->customer_accept_data_loss ? '✓' : '&nbsp;' !!}</span> YES
            </div>
        </div>
    </div>

    <!-- Technical Inspection -->
    <div class="section-title">7. Technical Inspection & Chip Level Repair</div>
    <div class="checklist">
        @php
            $inspections = ['Display', 'Power ON', 'Charging', 'USB Ports', 'Keyboard', 'WiFi', 'Audio', 'Fan', 'Camera', 'Battery Detection', 'Board Condition', 'Overheating'];
            $active_inspection = $repair->technical_inspection ?: [];
        @endphp
        @foreach($inspections as $ins)
            <div class="checklist-item">
                <span class="checkbox-box">{!! in_array($ins, $active_inspection) ? '✓' : '&nbsp;' !!}</span>
                <span>{{ $ins }}</span>
            </div>
        @endforeach
    </div>

    <!-- Chip Level Notes -->
    <div class="section-title">8. Chip Level Repair Notes</div>
    <div class="checklist">
        @php
            $chip_notes = ['No Power', 'No Display', 'Water Damage', 'Short Circuit', 'BIOS Issue', 'CPU Rail Issue', 'Charging IC', 'Dead Board', 'GPU Issue', 'Overheating'];
            $active_chip = $repair->chip_level_repair_notes ?: [];
        @endphp
        @foreach($chip_notes as $note)
            <div class="checklist-item">
                <span class="checkbox-box">{!! in_array($note, $active_chip) ? '✓' : '&nbsp;' !!}</span>
                <span>{{ $note }}</span>
            </div>
        @endforeach
    </div>
    <div class="grid-2" style="margin-top:8px;">
        <div>
            <div class="field-group"><span class="field-label">Board Model:</span><span class="field-value">{{ $repair->board_model ?: 'N/A' }}</span></div>
            <div class="field-group"><span class="field-label">Technician:</span><span class="field-value">{{ $repair->freelancer_technician ?: 'N/A' }}</span></div>
        </div>
        <div>
            <div class="field-group"><span class="field-label">Sent Date:</span><span class="field-value">{{ $repair->sent_date ? $repair->sent_date->format('Y-m-d') : 'N/A' }}</span></div>
            <div class="field-group"><span class="field-label">Return Date:</span><span class="field-value">{{ $repair->return_date ? $repair->return_date->format('Y-m-d') : 'N/A' }}</span></div>
        </div>
    </div>

    <!-- Costing & Balance -->
    <div class="section-title">9. Costing</div>
    <div class="grid-4">
        <div class="field-group">
            <span class="field-label" style="width:90px;">Inspection Fee:</span>
            <span class="field-value">Rs. {{ number_format($repair->inspection_fee, 2) }}</span>
        </div>
        <div class="field-group">
            <span class="field-label" style="width:90px;">Estimate Cost:</span>
            <span class="field-value">Rs. {{ number_format($repair->estimate_cost, 2) }}</span>
        </div>
        <div class="field-group">
            <span class="field-label" style="width:90px;">Advance Paid:</span>
            <span class="field-value">Rs. {{ number_format($repair->advance_payment, 2) }}</span>
        </div>
        <div class="field-group">
            <span class="field-label" style="width:60px;">Balance:</span>
            <span class="field-value">Rs. {{ number_format($repair->final_cost - $repair->advance_payment, 2) }}</span>
        </div>
    </div>

    <!-- Collection Confirmation -->
    <div class="section-title">10. Collection Confirmation</div>
    <div class="grid-3">
        <div class="field-group"><span class="field-label" style="width:90px;">Collected By:</span><span class="field-value">{{ $repair->collected_by ?: '________________________' }}</span></div>
        <div class="field-group"><span class="field-label" style="width:90px;">Date Collected:</span><span class="field-value">{{ $repair->date_collected ? $repair->date_collected->format('Y-m-d') : '________________________' }}</span></div>
        <div class="field-group"><span class="field-label" style="width:90px;">Balance Paid:</span><span class="field-value">{{ $repair->remaining_balance_paid > 0 ? 'Rs. ' . number_format($repair->remaining_balance_paid, 2) : '________________________' }}</span></div>
    </div>

    <!-- Terms & Liability (English & Sinhala) -->
    <div class="terms-grid">
        <div class="terms-column">
            <h3>11. REPAIR AUTHORIZATION & TERMS</h3>
            <ol>
                <li>Diagnostic charges are non-refundable.</li>
                <li>Repair estimate may change after complete inspection.</li>
                <li>Dead board risk has been informed to the customer.</li>
                <li>Existing physical damages are accepted before repair.</li>
                <li>The shop is not responsible for data loss during repair.</li>
                <li>Devices not collected within 30 days are not our responsibility.</li>
                <li>Warranty does not cover physical or liquid damage.</li>
            </ol>
        </div>
        <div class="terms-column">
            <h3>12. LIABILITY LIMITATION / වගකීම් සීමාවන්</h3>
            <ol>
                <li>Only issues declared at intake will be considered for repair responsibility. භාරගත් අවස්ථාවේ සඳහන් කළ ගැටලු පමණක් වගකීමට ගනු ලැබේ.</li>
                <li>Any additional or previously unreported issues will be treated as separate charges. පසුව හඳුනාගන්නා හෝ නොදන්වා ඇති ගැටලු වෙනම ගාස්තු ලෙස සැලකේ.</li>
                <li>Shop is not responsible for pre-existing or hidden faults. පෙර තිබූ හෝ සඟවුණු දෝෂ සඳහා වෙළඳසැල වගකීම නොදරයි.</li>
                <li>Customer confirms all known issues are declared. සියලුම දැන සිටි ගැටලු පාරිභෝගිකයා විසින් ප්‍රකාශ කර ඇත.</li>
                <li>Data loss risk accepted by customer. දත්ත අහිමි වීමේ අවදානම පාරිභෝගිකයා පිළිගෙන ඇත.</li>
            </ol>
        </div>
    </div>

    <!-- Signatures -->
    <div class="signatures">
        <div class="signature-line">
            <div>Customer Signature</div>
        </div>
        <div class="signature-line">
            <div>Technician Signature</div>
        </div>
    </div>

</body>
</html>
