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
                
                @if($invoice->balance_due > 0)
                <button @click="paymentModalOpen = true" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-plus mr-1"></i> Record Payment
                </button>
                @endif
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

    <!-- Payment Modal -->
    <div x-data="{ paymentModalOpen: false }" x-show="paymentModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="paymentModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="paymentModalOpen" @click.away="paymentModalOpen = false" x-transition class="inline-block align-bottom bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('payments.store', $invoice->id) }}" method="POST">
                    @csrf
                    <div class="bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-white mb-4" id="modal-title">Record Payment</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-400">Amount (LKR)</label>
                                <input type="number" step="0.01" name="amount" max="{{ $invoice->balance_due }}" value="{{ $invoice->balance_due }}" required class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-md shadow-sm py-2 px-3 text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <p class="mt-1 text-xs text-gray-400">Max: {{ number_format($invoice->balance_due, 2) }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-400">Payment Date</label>
                                <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-md shadow-sm py-2 px-3 text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-400">Method</label>
                                <select name="payment_method" required class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-md shadow-sm py-2 px-3 text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <option value="cash">Cash</option>
                                    <option value="card">Card</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="cheque">Cheque</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-400">Reference / Cheque No</label>
                                <input type="text" name="reference_number" class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-md shadow-sm py-2 px-3 text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-400">Notes</label>
                                <textarea name="notes" rows="2" class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-md shadow-sm py-2 px-3 text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Save Payment
                        </button>
                        <button type="button" @click="paymentModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-600 shadow-sm px-4 py-2 bg-gray-600 text-base font-medium text-white hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
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
        width: 210mm; /* A4 Width */
        min-height: 297mm; /* A4 Height */
        padding: 10mm 15mm;
        margin: 0 auto;
        background: #fff;
        color: var(--print-text);
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
        position: relative;
        font-family: 'Inter', sans-serif;
    }
    
    /* Header */
    .invoice-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        border-bottom: 2px solid var(--print-accent);
        padding-bottom: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .invoice-logo {
        height: 100px; /* Adjusted Logo */
        width: auto;
        display: block;
    }

    .company-details {
        text-align: right;
        font-size: 0.9rem;
    }

    .company-name {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--print-accent);
        margin: 0 0 0.5rem 0;
        text-transform: uppercase;
        letter-spacing: -0.5px;
    }

    .company-details p {
        margin: 0.15rem 0;
    }

    .detail-block { margin-bottom: 0.25rem; }
    .mt-2 { margin-top: 0.4rem; }

    /* Meta Bar */
    .invoice-meta-bar {
        display: flex;
        justify-content: space-between;
        background: #f8fafc;
        border: 1px solid var(--print-border);
        padding: 0.5rem 1rem;
        border-radius: 6px;
        margin-bottom: 1.5rem;
    }

    .meta-item {
        display: flex;
        flex-direction: column;
    }

    .meta-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #64748b;
        font-weight: 600;
        letter-spacing: 0.05em;
    }

    .meta-value {
        font-size: 1rem;
        font-weight: 700;
        color: var(--print-accent);
    }

    /* Grid Layout */
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 3rem;
        margin-bottom: 2rem;
    }

    .box-title {
        font-size: 0.85rem;
        text-transform: uppercase;
        color: #94a3b8;
        border-bottom: 1px solid var(--print-border);
        padding-bottom: 0.4rem;
        margin-bottom: 0.8rem;
        font-weight: 600;
    }

    .client-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--print-accent);
        margin-bottom: 0.3rem;
    }

    .client-info p, .device-info p { margin: 0.2rem 0; }

    .details-table { width: 100%; border-collapse: collapse; }
    .details-table td { padding: 0.2rem 0; vertical-align: top; }
    .details-table .label { width: 80px; font-weight: 600; color: #64748b; }

    /* Main Table */
    .invoice-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 2rem;
    }

    .invoice-table th {
        background: var(--print-accent);
        color: #fff;
        padding: 0.5rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .invoice-table td {
        padding: 0.5rem;
        border-bottom: 1px solid var(--print-border);
        border-left: 1px solid var(--print-border);
        border-right: 1px solid var(--print-border);
        vertical-align: middle;
    }

    .empty-row td {
        height: 2.5rem; /* Specific height for empty spacing */
    }

    .text-right { text-align: right; }

    .total-row td {
        border: none;
        padding-top: 1rem;
    }
    
    .grand-total-row td {
        border-top: 2px solid var(--print-accent);
        padding: 0.8rem;
        color: var(--print-accent);
        font-size: 1.1rem;
        font-weight: 800;
        background: #f8fafc;
    }

    .total-label { text-align: right; padding-right: 1.5rem; }

    /* Footer */
    .footer-section {
        display: flex;
        justify-content: space-between;
        margin-top: auto; /* Push to bottom */
        gap: 2rem;
    }

    .terms {
        flex: 3;
        font-size: 0.75rem;
        color: #64748b;
    }
    
    .terms h4 {
        margin-bottom: 0.5rem;
        color: var(--print-accent);
        font-size: 0.8rem;
    }

    .terms ul { padding-left: 1rem; line-height: 1.4; }

    .signatures {
        flex: 2;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        gap: 2rem;
    }

    .signature-box {
        text-align: center;
    }

    .signature-box .line {
        border-top: 1px dashed #94a3b8;
        margin-bottom: 0.5rem;
    }

    .signature-box p { font-size: 0.8rem; font-weight: 600; }
    .date-placeholder { font-weight: 400; font-size: 0.7rem; margin-top: 0.2rem; }
    
    .print-footer {
        text-align: center;
        font-size: 0.8rem;
        color: #94a3b8;
        margin-top: 2rem;
        border-top: 1px solid #f1f5f9;
        padding-top: 0.5rem;
    }

    /* Print Specifics */
    @media print {
        @page { size: A4; margin: 0; }
        
        /* Hide everything by default */
        body { margin: 0; background: #fff; -webkit-print-color-adjust: exact; }
        
        /* Explicitly hide UI elements */
        .sidebar, 
        .page-header, 
        .toast-container,
        .btn-primary,
        nav,
        aside { 
            display: none !important; 
        }

        /* Reset main layout */
        .app-container {
            display: block !important;
            grid-template-columns: 1fr !important; /* If grid is used */
        }

        .main-content {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: none !important;
        }

        /* Show only invoice */
        .invoice-container {
            visibility: visible !important;
            position: absolute; 
            left: 0; 
            top: 0; 
            width: 210mm; 
            min-height: 297mm; 
            margin: 0; 
            padding: 10mm 15mm; 
            box-shadow: none;
            z-index: 9999;
            background: white;
        }

        /* Ensure content inside invoice is visible */
        .invoice-container * {
            visibility: visible !important;
        }

        .glass { background: #fff !important; color: #000 !important; border: none; }
        
        /* Ensure background colors print */
        .invoice-table th { background-color: var(--print-accent) !important; color: #fff !important; }
        .grand-total-row td { background-color: #f8fafc !important; }
        .invoice-meta-bar { background-color: #f8fafc !important; }
    }
</style>
@endsection
