@extends('layouts.app')

@section('title', 'Invoice #' . $invoice->id)

@section('content')
<div class="page-header print:hidden">
    <div class="header-content">
        <h2>Invoice #{{ 'INV-' . preg_replace('/[^0-9]/', '', $invoice->repairJob->job_number) }}</h2>
        <p class="text-muted">{{ ucfirst($invoice->invoice_type) }} Invoice</p>
    </div>
    <button onclick="window.print()" class="btn-primary">
        <i class="fas fa-print" style="margin-right: 0.5rem;"></i> Print Invoice
    </button>
</div>

<div class="invoice-container glass print:no-glass">
    <!-- Invoice Header -->
    <header class="invoice-header">
        <div class="brand-section">
            <img src="{{ asset('images/logo.png') }}" alt="SVP Technologies" class="invoice-logo">
            <!-- <h1 class="company-name">SVP Technologies</h1> -->
        </div>
        <div class="company-details">
            <h2 class="company-name">SVP Technologies</h2>
            <div class="detail-block">
                <p class="address-line">311/C, Thalgaswatta Road, Horahena, Hokandara</p>
                <p class="contact-line"><strong>Phone:</strong> 071-1551800 / 011-2562462</p>
                <p class="web-line"><strong>Web:</strong> svptech.lk / motherboard.lk</p>
            </div>
            <div class="detail-block mt-2">
                <p class="label"><strong>Office:</strong></p>
                <p class="address-line">194A Wanaguru Mawatha, Hokandara</p>
                <p class="contact-line"><strong>Phone:</strong> 011-2562484</p>
            </div>
        </div>
    </header>

    <div class="invoice-meta-bar">
        <div class="meta-item">
            <span class="meta-label">Invoice No:</span>
            <span class="meta-value">INV-{{ preg_replace('/[^0-9]/', '', $invoice->repairJob->job_number) }}</span>
        </div>
        <div class="meta-item">
            <span class="meta-label">Date:</span>
            <span class="meta-value">{{ $invoice->created_at->format('d/m/Y') }}</span>
        </div>
        <div class="meta-item">
            <span class="meta-label">Job No:</span>
            <span class="meta-value" style="font-family: monospace;">{{ $invoice->repairJob->job_number }}</span>
        </div>
    </div>

    <!-- Client & Job Grid -->
    <div class="info-grid">
        <div class="info-box client-info">
            <h3 class="box-title">Invoice To</h3>
            <p class="client-name">{{ $invoice->repairJob->customer->name }}</p>
            @if($invoice->repairJob->customer->address)
            <p class="client-address">{{ $invoice->repairJob->customer->address }}</p>
            @endif
            <p class="client-contact">{{ $invoice->repairJob->customer->phone }}</p>
        </div>

        <div class="info-box device-info">
            <h3 class="box-title">Device Details</h3>
            <table class="details-table">
                <tr>
                    <td class="label">Device:</td>
                    <td>{{ $invoice->repairJob->laptop_brand }} {{ $invoice->repairJob->laptop_model }}</td>
                </tr>
                <tr>
                    <td class="label">Serial No:</td>
                    <td>{{ $invoice->repairJob->serial_number }}</td>
                </tr>
                <tr>
                    <td class="label">Fault:</td>
                    <td>{{ $invoice->repairJob->fault_description }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Line Items -->
    <table class="invoice-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 65%;">Description</th>
                <th style="width: 10%; text-align: center;">Qty</th>
                <th style="width: 20%;" class="text-right">Amount (LKR)</th>
            </tr>
        </thead>
        <tbody>
            @php $rowCount = 0; @endphp

            @if($invoice->repairJob->invoiceItems->count() > 0)
                {{-- New Logic: Use Billable Invoice Items --}}
                @foreach($invoice->repairJob->invoiceItems as $item)
                <tr>
                    <td>{{ ++$rowCount }}</td>
                    <td>{{ $item->description }}</td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->amount * $item->quantity, 2) }}</td>
                </tr>
                @endforeach
            @else
                {{-- Legacy Logic: Use Parts + Labor --}}
                @foreach($invoice->repairJob->parts as $part)
                <tr>
                    <td>{{ ++$rowCount }}</td>
                    <td>{{ $part->part_name }}</td>
                    <td style="text-align: center;">{{ $part->quantity_used }}</td>
                    <td class="text-right">{{ number_format($part->part_cost * $part->quantity_used, 2) }}</td>
                </tr>
                @endforeach
                
                @if($invoice->labor_cost > 0)
                <tr>
                    <td>{{ ++$rowCount }}</td>
                    <td>Service / Labor Charges</td>
                    <td style="text-align: center;">1</td>
                    <td class="text-right">{{ number_format($invoice->labor_cost, 2) }}</td>
                </tr>
                @endif
            @endif

            {{-- Pad with empty rows to ensure at least 4 items for description space --}}
            @for($i = $rowCount; $i < 4; $i++)
            <tr class="empty-row">
                <td>{{ ++$rowCount }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @endfor
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="total-label">Sub Total</td>
                <td class="text-right">{{ number_format($invoice->total_amount, 2) }}</td>
            </tr>
            <!-- Add Tax/Discount rows here if needed in future -->
            <tr class="grand-total-row">
                <td colspan="3" class="total-label">Grand Total</td>
                <td class="text-right">LKR {{ number_format($invoice->total_amount, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Payment History Section (Screen Only) -->
    <div class="print:hidden mt-8 mb-8">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-white">Payment History</h3>
            <div class="flex items-center gap-4">
                <span class="px-3 py-1 rounded-full text-sm font-bold 
                    {{ $invoice->status === 'paid' ? 'bg-green-500/20 text-green-400' : 
                       ($invoice->status === 'partial' ? 'bg-yellow-500/20 text-yellow-400' : 'bg-red-500/20 text-red-400') }}">
                    {{ ucfirst($invoice->status) }}
                </span>
                <span class="text-gray-300">Balance Due: <span class="font-bold text-white">LKR {{ number_format($invoice->balance_due, 2) }}</span></span>
            </div>
        </div>

        @if($invoice->payments->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-left text-gray-300">
                <thead class="bg-white/5 border-b border-white/10 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Method</th>
                        <th class="px-4 py-3">Reference/Notes</th>
                        <th class="px-4 py-3 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->payments as $payment)
                    <tr class="border-b border-white/5">
                        <td class="px-4 py-3">{{ $payment->payment_date->format('Y-m-d') }}</td>
                        <td class="px-4 py-3">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                        <td class="px-4 py-3 text-sm">
                            @if($payment->reference_number)<span class="block text-xs text-blue-400">Ref: {{ $payment->reference_number }}</span>@endif
                            {{ $payment->notes }}
                        </td>
                        <td class="px-4 py-3 text-right font-mono">LKR {{ number_format($payment->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-gray-500 italic">No payments recorded yet.</p>
        @endif
    </div>
        <div class="terms">
            <h4>Terms & Conditions</h4>
            <ul>
                <li>The prices quoted in our estimates are not binding. Final cost based on actual services/materials.</li>
                <li>SVP Technologies is not responsible for data loss or damages due to unforeseen contingencies.</li>
                <li>Inspection fee: Rs. 1,000 (Laptop) / Rs. 500 (Desktop) if repair is declined after estimate.</li>
                <li>Items not collected within 30 days of notice may be disposed of.</li>
                <li>Warranty claims require presentation of this original invoice.</li>
            </ul>
        </div>
        
        <div class="signatures">
            <div class="signature-box">
                <div class="line"></div>
                <p>Customer Signature</p>
                <p class="date-placeholder">Date: .......................</p>
            </div>
            <div class="signature-box">
                <div class="line"></div>
                <p>Authorized Signature</p>
            </div>
        </div>
    </div>
    
    <div class="print-footer">
        <p>Thank you for choosing SVP Technologies!</p>
    </div>
</div>

<style>
    /* CSS Variables for Print Consistency */
    :root {
        --print-accent: #1e293b; /* Dark Slate */
        --print-text: #334155;
        --print-border: #e2e8f0;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    
    /* Layout Container */
    .invoice-container {
        width: 210mm;
        min-height: 297mm; /* Keep A4 size on screen representation */
        padding: 10mm 15mm; /* Reduced padding */
        margin: 0 auto;
        background: #fff;
        color: var(--print-text);
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
        position: relative;
        font-family: 'Inter', sans-serif;
        box-sizing: border-box;
    }
    
    /* Header */
    .invoice-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        border-bottom: 2px solid var(--print-accent);
        padding-bottom: 0.5rem; /* Reduced */
        margin-bottom: 1rem; /* Reduced */
    }

    .invoice-logo {
        height: 60px; /* Reduced from 100px */
        width: auto;
        display: block;
    }

    .company-details {
        text-align: right;
        font-size: 0.8rem; /* Reduced */
    }

    .company-name {
        font-size: 1.2rem; /* Reduced */
        font-weight: 800;
        color: var(--print-accent);
        margin: 0 0 0.2rem 0;
        text-transform: uppercase;
        letter-spacing: -0.5px;
    }

    .company-details p {
        margin: 0.1rem 0;
    }

    .detail-block { margin-bottom: 0.25rem; }
    .mt-2 { margin-top: 0.2rem; }

    /* Meta Bar */
    .invoice-meta-bar {
        display: flex;
        justify-content: space-between;
        background: #f8fafc;
        border: 1px solid var(--print-border);
        padding: 0.4rem 0.8rem; /* Reduced */
        border-radius: 6px;
        margin-bottom: 1rem; /* Reduced */
    }

    .meta-item {
        display: flex;
        flex-direction: column;
    }

    .meta-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        color: #64748b;
        font-weight: 600;
        letter-spacing: 0.05em;
    }

    .meta-value {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--print-accent);
    }

    /* Grid Layout */
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem; /* Reduced */
        margin-bottom: 1.5rem; /* Reduced */
    }

    .box-title {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #94a3b8;
        border-bottom: 1px solid var(--print-border);
        padding-bottom: 0.2rem;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }

    .client-name {
        font-size: 1rem;
        font-weight: 700;
        color: var(--print-accent);
        margin-bottom: 0.2rem;
    }

    .client-info p, .device-info p { margin: 0.1rem 0; font-size: 0.85rem; }

    .details-table { width: 100%; border-collapse: collapse; }
    .details-table td { padding: 0.1rem 0; vertical-align: top; font-size: 0.85rem; }
    .details-table .label { width: 80px; font-weight: 600; color: #64748b; }

    /* Main Table */
    .invoice-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 1rem; /* Reduced */
    }

    .invoice-table th {
        background: var(--print-accent);
        color: #fff;
        padding: 0.4rem; /* Reduced */
        text-align: left;
        font-weight: 600;
        font-size: 0.8rem;
    }

    .invoice-table td {
        padding: 0.4rem; /* Reduced */
        border-bottom: 1px solid var(--print-border);
        border-left: 1px solid var(--print-border);
        border-right: 1px solid var(--print-border);
        vertical-align: middle;
        font-size: 0.85rem;
    }

    .empty-row td {
        height: 1.5rem; /* Reduced */
    }

    .text-right { text-align: right; }

    .total-row td {
        border: none;
        padding-top: 0.5rem;
    }
    
    .grand-total-row td {
        border-top: 2px solid var(--print-accent);
        padding: 0.5rem;
        color: var(--print-accent);
        font-size: 1rem;
        font-weight: 800;
        background: #f8fafc;
    }

    .total-label { text-align: right; padding-right: 1rem; font-size: 0.85rem; }

    /* Footer */
    .footer-section {
        display: flex;
        justify-content: space-between;
        margin-top: auto;
        gap: 1rem;
        padding-top: 1rem;
    }

    .terms {
        flex: 3;
        font-size: 0.65rem; /* Reduced */
        color: #64748b;
    }
    
    .terms h4 {
        margin-bottom: 0.3rem;
        color: var(--print-accent);
        font-size: 0.7rem;
    }

    .terms ul { padding-left: 1rem; line-height: 1.3; margin: 0; }

    .signatures {
        flex: 2;
        display: flex;
        flex-direction: row; /* Side by side signatures */
        justify-content: space-between;
        align-items: flex-end;
        gap: 1rem;
    }

    .signature-box {
        text-align: center;
        width: 45%;
    }

    .signature-box .line {
        border-top: 1px dashed #94a3b8;
        margin-bottom: 0.3rem;
    }

    .signature-box p { font-size: 0.7rem; font-weight: 600; }
    .date-placeholder { font-weight: 400; font-size: 0.6rem; margin-top: 0.1rem; }
    
    .print-footer {
        text-align: center;
        font-size: 0.7rem;
        color: #94a3b8;
        margin-top: 0.5rem;
        border-top: 1px solid #f1f5f9;
        padding-top: 0.3rem;
    }

    /* Print Specifics */
    @media print {
        @page { size: A4; margin: 0; }
        
        body { margin: 0; background: #fff; -webkit-print-color-adjust: exact; }
        
        .sidebar, 
        .page-header, 
        .toast-container,
        .btn-primary,
        nav,
        aside,
        .print\:hidden { 
            display: none !important; 
        }

        .app-container {
            display: block !important;
        }

        .invoice-container {
            visibility: visible !important;
            position: absolute; 
            left: 0; 
            top: 0; 
            width: 210mm; 
            height: 297mm; /* Forced Height */
            margin: 0; 
            padding: 10mm 15mm; 
            box-shadow: none;
            z-index: 9999;
            background: white;
            box-sizing: border-box;
        }

        .invoice-container * {
            visibility: visible !important;
        }

        .glass { background: #fff !important; color: #000 !important; border: none; }
        
        .invoice-table th { background-color: var(--print-accent) !important; color: #fff !important; }
        .grand-total-row td { background-color: #f8fafc !important; }
        .invoice-meta-bar { background-color: #f8fafc !important; }
        
        /* Ensure footer sticks to bottom of page */
        .footer-section {
             position: absolute;
             bottom: 15mm;
             left: 15mm;
             right: 15mm;
        }
        
        .print-footer {
            position: absolute;
            bottom: 5mm;
            left: 0;
            right: 0;
        }
    }
</style>
@endsection
