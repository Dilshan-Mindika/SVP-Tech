<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $invoice->is_tax_invoice ? 'Tax Invoice' : 'Invoice' }} #{{ $invoice->invoice_number }}</title>
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;650;700;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, sans-serif;
            color: #1e293b;
            line-height: 1.5;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        /* Web Preview Wrapper */
        @media screen {
            body {
                background-color: #f8fafc;
                padding: 40px 15px;
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            .invoice-container {
                background: #ffffff;
                width: 210mm;
                min-height: 297mm;
                padding: 20mm;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
            }
            .no-print-bar {
                background-color: #0f172a;
                color: #38bdf8;
                padding: 12px 24px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-radius: 10px;
                width: 210mm;
                margin-bottom: 24px;
                box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            }
            .no-print-bar span {
                font-size: 12px;
                font-weight: 700;
                letter-spacing: 1px;
                text-transform: uppercase;
            }
            .print-btn {
                background: #0284c7;
                color: #ffffff;
                border: none;
                padding: 6px 16px;
                border-radius: 6px;
                font-weight: 700;
                font-size: 12px;
                cursor: pointer;
                transition: background 0.2s;
                text-transform: uppercase;
            }
            .print-btn:hover {
                background: #0369a1;
            }
        }

        @page {
            size: A4;
            margin: 0;
        }
        @media print {
            body {
                background-color: #ffffff;
                margin: 15mm;
                padding: 0;
            }
            .invoice-container {
                width: 100%;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }

        /* Invoice Layout Styling */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 24px;
            margin-bottom: 24px;
        }
        .company-logo {
            font-size: 28px;
            font-weight: 900;
            letter-spacing: 2px;
            color: #0f172a;
            margin: 0;
            text-transform: uppercase;
        }
        .company-details {
            font-size: 11px;
            color: #64748b;
            margin-top: 6px;
            line-height: 1.6;
        }
        .invoice-title-block {
            text-align: right;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: 900;
            letter-spacing: 1px;
            margin: 0;
            color: #0f172a;
            text-transform: uppercase;
        }
        .tax-badge {
            display: inline-block;
            background-color: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.5px;
            padding: 2px 8px;
            border-radius: 4px;
            margin-top: 6px;
            text-transform: uppercase;
        }
        
        .metadata-grid {
            display: grid;
            grid-cols: 2; /* fallback */
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 40px;
            margin-bottom: 30px;
        }
        .meta-card h3 {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            margin: 0 0 10px 0;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 6px;
        }
        .meta-card p {
            margin: 4px 0;
            font-size: 12px;
            color: #334155;
        }
        .meta-card strong {
            color: #0f172a;
        }
        .meta-inline {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 6px;
        }
        .meta-inline span.label {
            color: #64748b;
        }
        .meta-inline span.val {
            font-weight: 600;
            color: #334155;
        }

        /* Table Styling */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 10px 12px;
            border-bottom: 2px solid #e2e8f0;
            text-align: left;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 12px;
            vertical-align: top;
            color: #334155;
        }
        .items-table td.qty-col, .items-table th.qty-col {
            text-align: center;
            width: 80px;
        }
        .items-table td.price-col, .items-table th.price-col {
            text-align: right;
            width: 120px;
            font-family: 'JetBrains Mono', monospace;
        }
        .item-name {
            font-weight: 700;
            color: #0f172a;
        }
        .item-meta {
            font-size: 9px;
            color: #64748b;
            margin-top: 4px;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }
        .item-meta span {
            background-color: #f1f5f9;
            padding: 1px 6px;
            border-radius: 3px;
        }
        .item-meta span.discount-pill {
            background-color: #fef2f2;
            color: #ef4444;
        }
        
        /* Summary Grid */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 30px;
            align-items: start;
        }
        .notes-column {
            grid-column: span 7;
        }
        .totals-column {
            grid-column: span 5;
        }
        .notes-card {
            background-color: #f8fafc;
            border: 1px solid #f1f5f9;
            padding: 16px;
            border-radius: 8px;
        }
        .notes-card h4 {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin: 0 0 8px 0;
            color: #475569;
        }
        .notes-card p {
            font-size: 11px;
            color: #64748b;
            margin: 0;
            line-height: 1.6;
        }
        
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 6px 0;
            font-size: 12px;
            color: #64748b;
        }
        .totals-table td.val {
            text-align: right;
            font-weight: 600;
            color: #334155;
            font-family: 'JetBrains Mono', monospace;
        }
        .totals-table tr.total-row td {
            border-top: 2px solid #e2e8f0;
            padding-top: 10px;
            font-weight: 700;
            color: #0f172a;
        }
        .totals-table tr.total-row td.val {
            font-size: 16px;
            font-weight: 900;
            color: #0f172a;
        }
        .totals-table tr.balance-row td {
            color: #16a34a;
            font-weight: 650;
        }
        
        .footer-section {
            border-top: 2px solid #f1f5f9;
            padding-top: 20px;
            margin-top: 50px;
            text-align: center;
        }
        .footer-section p {
            font-size: 10px;
            color: #94a3b8;
            margin: 4px 0;
        }
    </style>
</head>
<body>

    <div class="no-print-bar no-print">
        <span>A4 PRINT PREVIEW ({{ $invoice->is_tax_invoice ? 'TAX INVOICE' : 'STANDARD INVOICE' }})</span>
        <button class="print-btn" onclick="window.print()">Print / Save PDF</button>
    </div>

    <div class="invoice-container">
        <!-- Header -->
        <div class="header-section">
            <div style="display: flex; gap: 20px; align-items: center;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 80px; width: auto; max-width: 220px; object-fit: contain;">
                <div>
                    <div class="company-details" style="margin-top: 0; border-left: 2px solid #e2e8f0; padding-left: 15px;">
                        <p style="margin: 0; font-size: 13px; color: #0f172a;"><strong>CLOUDTECH Computer Store & Service Center</strong></p>
                        <p style="margin: 2px 0;">321 Galle Road, Colombo 03, Sri Lanka</p>
                        <p style="margin: 2px 0;">Tel: +94 11 2345678 | Email: sales@cloudtech.online</p>
                        @if($invoice->is_tax_invoice)
                            <p style="margin: 5px 0 0 0; font-weight: 700; color: #0f172a; font-size: 12px;">VAT Reg No: 409123456-7000</p>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="invoice-title-block">
                <h2 class="invoice-title">{{ $invoice->is_tax_invoice ? 'Tax Invoice' : 'Invoice' }}</h2>
                @if($invoice->is_tax_invoice)
                    <span class="tax-badge">VAT 15% Registered</span>
                @else
                    <span class="tax-badge" style="background-color: #f1f5f9; color: #475569; border-color: #cbd5e1;">Standard Sale</span>
                @endif
                
                <div style="margin-top: 15px; font-size: 12px; line-height: 1.6; text-align: right;">
                    <div class="meta-inline" style="justify-content: flex-end; gap: 15px;">
                        <span class="label">Invoice Ref:</span>
                        <span class="val">{{ $invoice->invoice_number }}</span>
                    </div>
                    <div class="meta-inline" style="justify-content: flex-end; gap: 15px;">
                        <span class="label">Date:</span>
                        <span class="val">{{ $invoice->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                    @if($invoice->due_date)
                        <div class="meta-inline" style="justify-content: flex-end; gap: 15px;">
                            <span class="label" style="color: #ef4444;">Due Date:</span>
                            <span class="val" style="color: #ef4444;">{{ $invoice->due_date->format('Y-m-d') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Metadata Grid -->
        <div class="metadata-grid">
            <!-- Issuer -->
            <div class="meta-card">
                <h3>Issuer Info</h3>
                <p><strong>Issued By:</strong> {{ config('app.name', 'CloudTech') }} Shop</p>
                <p><strong>Cashier:</strong> {{ $invoice->user->name }}</p>
                @if($invoice->employee)
                    <p><strong>Sales Representative:</strong> {{ $invoice->employee->name }}</p>
                @endif
                <p><strong>Sale Channel:</strong> {{ $invoice->sale_type }}</p>
            </div>
            
            <!-- Customer -->
            <div class="meta-card">
                <h3>Billed To (Customer)</h3>
                @if($invoice->customer)
                    <p><strong>Name:</strong> {{ $invoice->customer->name }}</p>
                    <p><strong>Phone:</strong> {{ $invoice->customer->phone }}</p>
                    <p><strong>Address:</strong> {{ $invoice->customer->address ?? 'N/A' }}</p>
                @else
                    <p class="italic" style="color: #94a3b8; margin-top: 8px;">Walk-in Customer / Cash Sale</p>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th class="qty-col">Qty</th>
                    <th style="width: 110px; text-align: center; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #475569; background-color: #f8fafc; border-bottom: 2px solid #e2e8f0; padding: 10px 12px;">Warranty</th>
                    <th class="price-col">Unit Price</th>
                    <th class="price-col">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                    <tr>
                        <td>
                            <span class="item-name">{{ $item->product->name }}</span>
                            <div class="item-meta">
                                @if($item->serial_number)
                                    <span>S/N: {{ $item->serial_number }}</span>
                                @endif
                                @if($item->free_quantity > 0)
                                    <span>Free Qty: {{ $item->free_quantity }}</span>
                                @endif
                                @if($item->discount_amount > 0)
                                    <span class="discount-pill">Disc: -Rs. {{ number_format($item->discount_amount, 2) }} ({{ $item->discount_percentage }}%)</span>
                                @endif
                            </div>
                        </td>
                        <td class="qty-col">{{ $item->quantity }}</td>
                        <td style="text-align: center; font-weight: 600; color: #475569;">{{ $item->warranty ?: 'No Warranty' }}</td>
                        <td class="price-col">Rs. {{ number_format($item->unit_price, 2) }}</td>
                        <td class="price-col">Rs. {{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary-grid">
            <!-- Notes -->
            <div class="notes-column">
                <div class="notes-card" style="display: flex; justify-content: space-between; gap: 15px; align-items: center;">
                    <div style="flex-grow: 1;">
                        <h4>Notes & Terms</h4>
                        @if($invoice->special_note)
                            <p style="margin-bottom: 10px;"><strong>Client Note:</strong> "{{ $invoice->special_note }}"</p>
                        @endif
                        <p>1. Warranty claims require presenting this original invoice document.</p>
                        <p style="margin-top: 4px;">2. Scan the validation QR code on the right to verify credentials & initiate warranty claims.</p>
                        @if($invoice->bankAccount)
                            <div style="margin-top: 10px; padding-top: 8px; border-top: 1px dashed #cbd5e1; font-size: 10px; color: #334155; line-height: 1.4;">
                                <p style="margin: 0; font-weight: 700; text-transform: uppercase; color: #475569; font-size: 9px; letter-spacing: 0.5px;">Bank Transfer Payment Account</p>
                                <p style="margin: 2px 0 0 0;">Bank: <strong>{{ $invoice->bankAccount->bank_name }}</strong> @if($invoice->bankAccount->branch) ({{ $invoice->bankAccount->branch }} Branch) @endif</p>
                                <p style="margin: 1px 0 0 0;">Account No: <strong style="font-family: 'JetBrains Mono', monospace; font-size: 11px;">{{ $invoice->bankAccount->account_number }}</strong></p>
                                <p style="margin: 1px 0 0 0;">Account Name: {{ $invoice->bankAccount->account_name }}</p>
                            </div>
                        @endif
                    </div>
                    <div style="text-align: center; flex-shrink: 0; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data={{ urlencode(route('warranty.create', ['invoice_id' => $invoice->id])) }}" alt="RMA QR Code" style="display: block; border: 1px solid #e2e8f0; padding: 3px; background: #fff; width: 90px; height: 90px; border-radius: 4px;">
                        <span style="font-size: 8px; font-weight: 700; color: #64748b; margin-top: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Scan for RMA / Claim</span>
                    </div>
                </div>
            </div>
            
            <!-- Totals -->
            <div class="totals-column">
                <table class="totals-table">
                    <tr>
                        <td>Subtotal:</td>
                        <td class="val">Rs. {{ number_format($invoice->subtotal, 2) }}</td>
                    </tr>
                    @if($invoice->global_discount_amount > 0)
                        <tr>
                            <td>Global Discount ({{ $invoice->global_discount_percentage }}%):</td>
                            <td class="val" style="color: #ef4444;">-Rs. {{ number_format($invoice->global_discount_amount, 2) }}</td>
                        </tr>
                    @endif
                    @if($invoice->service_charges > 0)
                        <tr>
                            <td>Service Charges:</td>
                            <td class="val">Rs. {{ number_format($invoice->service_charges, 2) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td>Tax (VAT {{ $invoice->is_tax_invoice ? '15%' : '0%' }}):</td>
                        <td class="val">Rs. {{ number_format($invoice->tax, 2) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td>Grand Total:</td>
                        <td class="val">Rs. {{ number_format($invoice->total, 2) }}</td>
                    </tr>
                    <tr style="border-top: 1px solid #f1f5f9;">
                        <td>Amount Paid ({{ $invoice->payment_method }}):</td>
                        <td class="val">Rs. {{ number_format($invoice->customer_paid, 2) }}</td>
                    </tr>
                    <tr class="balance-row">
                        <td>Balance Returned:</td>
                        <td class="val">Rs. {{ number_format($invoice->balance, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer-section">
            <p>Thank you for choosing CLOUDTECH Computer Store!</p>
            <p>System Ref: CL-{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }} | Generated at {{ date('Y-m-d H:i:s') }}</p>
        </div>
    </div>

    <script>
        // Auto trigger print/download dialogue on load if requested
        window.addEventListener('load', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('download')) {
                window.print();
            }
        });
    </script>
</body>
</html>
