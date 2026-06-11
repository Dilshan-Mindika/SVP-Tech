<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Quotation #{{ $quotation->quotation_number }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            line-height: 1.4;
        }
        .ticket {
            max-width: 300px;
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 15px;
        }
        .header {
            text-align: center;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 16px;
            margin: 0;
            letter-spacing: 2px;
            font-weight: bold;
        }
        .header p {
            margin: 3px 0 0 0;
            font-size: 10px;
        }
        .details-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .details-table td {
            padding: 3px 0;
        }
        .details-table td.label {
            color: #666;
            width: 40%;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .items-table th {
            border-bottom: 1px solid #000;
            text-align: left;
            padding: 4px 0;
            font-size: 10px;
            text-transform: uppercase;
        }
        .items-table td {
            padding: 6px 0;
            vertical-align: top;
        }
        .items-table .price-col {
            text-align: right;
        }
        .summary-section {
            border-top: 1px dashed #000;
            padding-top: 8px;
            margin-top: 10px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
        }
        .summary-row.total {
            font-weight: bold;
            font-size: 14px;
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 5px;
        }
        .notes-section {
            border-top: 1px dashed #000;
            margin-top: 10px;
            padding-top: 5px;
            font-size: 10px;
            color: #333;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            border-top: 1px dashed #000;
            padding-top: 10px;
            font-size: 9px;
        }
        @page {
            size: auto;
            margin: 0;
        }
        @media print {
            body {
                margin: 10mm;
                padding: 0;
            }
            .ticket {
                border: none;
                max-width: 100%;
                width: 100%;
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
        .no-print-bar {
            background: #0f172a;
            color: #38bdf8;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 8px;
            max-width: 300px;
            margin: 0 auto 20px auto;
            font-family: sans-serif;
            font-size: 11px;
            font-weight: bold;
        }
        .print-btn {
            background: #0284c7;
            color: #fff;
            border: none;
            padding: 5px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .print-btn:hover {
            background: #0369a1;
        }
    </style>
</head>
<body>

    <div class="no-print-bar no-print">
        <span>PREVIEW MODE (ESTIMATE PROPOSAL)</span>
        <button class="print-btn" onclick="window.print()">PRINT</button>
    </div>

    <div class="ticket">
        <div class="header">
            <h1>NEURONET</h1>
            <p>Computer Store & Service Center</p>
            <p>Tel: 011-2345678 | Colombo, Sri Lanka</p>
            <p style="margin-top: 5px; font-weight: bold; font-size: 11px; border: 1px solid #000; padding: 2px; display: inline-block;">QUOTATION PROPOSAL</p>
        </div>

        <table class="details-table">
            <tr>
                <td class="label">Quote Ref:</td>
                <td><strong>{{ $quotation->quotation_number }}</strong></td>
            </tr>
            <tr>
                <td class="label">Date Logged:</td>
                <td>{{ $quotation->created_at->format('Y-m-d H:i') }}</td>
            </tr>
            <tr>
                <td class="label">Valid Until:</td>
                <td>{{ \Carbon\Carbon::parse($quotation->valid_until)->format('Y-m-d') }}</td>
            </tr>
            <tr>
                <td class="label">Customer:</td>
                <td>{{ $quotation->customer_name }}</td>
            </tr>
            <tr>
                <td class="label">Phone:</td>
                <td>{{ $quotation->customer_phone }}</td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th style="text-align: center;">Qty</th>
                    <th class="price-col">Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation->items as $item)
                    <tr>
                        <td>
                            {{ $item->product->name }}
                        </td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td class="price-col">Rs. {{ number_format($item->price * $item->quantity, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary-section">
            <div class="summary-row">
                <span>Subtotal:</span>
                <span>Rs. {{ number_format($quotation->subtotal, 2) }}</span>
            </div>
            <div class="summary-row">
                <span>VAT ({{ $quotation->tax > 0 ? '15%' : '0%' }}):</span>
                <span>Rs. {{ number_format($quotation->tax, 2) }}</span>
            </div>
            <div class="summary-row total">
                <span>TOTAL:</span>
                <span>Rs. {{ number_format($quotation->total, 2) }}</span>
            </div>
        </div>

        @if($quotation->notes)
            <div class="notes-section">
                <strong>Notes/Terms:</strong>
                <p style="margin: 3px 0 0 0; font-style: italic;">{{ $quotation->notes }}</p>
            </div>
        @endif

        <div class="footer">
            <p>This is a price quote, not an invoice.</p>
            <p>Prices are valid until date shown above.</p>
            <p>System node: QT-{{ str_pad($quotation->id, 4, '0', STR_PAD_LEFT) }}</p>
        </div>
    </div>

</body>
</html>
