<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @switch($reportType)
            @case('sales') Sales Report @break
            @case('profit') Profit & Loss Report @break
            @case('stock') Stock & Inventory Valuation Report @break
            @case('product') Product Movement & Sales Report @break
            @case('customer_payment') Customer Payment Method Report @break
            @case('sales_ref') Sales Representative Performance Report @break
            @case('customer_credit') Customer Credit & Outstanding Report @break
            @case('supplier') Supplier & GRN Audit Report @break
            @case('sale_type') Sale Channels Report @break
            @case('return') Product Returns Report @break
            @case('expenses') Expense Categories Report @break
            @case('attendance') Staff Attendance Metrics Report @break
            @case('salary') Payroll Salaries Payout Report @break
        @endswitch
        ({{ $fromDate }} to {{ $toDate }})
    </title>
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 40px 20px;
            font-size: 11px;
            line-height: 1.5;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .header-left h1 {
            font-size: 24px;
            margin: 0;
            letter-spacing: 1px;
            font-weight: 800;
            text-transform: uppercase;
        }
        .header-left p {
            margin: 4px 0 0 0;
            font-size: 11px;
            color: #555;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-right {
            text-align: right;
        }
        .header-right h2 {
            font-size: 14px;
            margin: 0;
            font-weight: 700;
            color: #0284c7;
            text-transform: uppercase;
        }
        .header-right p {
            margin: 4px 0 0 0;
            font-size: 10px;
            color: #666;
        }
        .meta-grid {
            display: grid;
            grid-template-cols: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 25px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 15px;
        }
        .meta-item span {
            display: block;
            font-size: 9px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 2px;
        }
        .meta-item strong {
            font-size: 12px;
            color: #0f172a;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .report-table th {
            background-color: #f1f5f9;
            border-bottom: 2px solid #cbd5e1;
            padding: 8px 10px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
            font-weight: 700;
            color: #334155;
        }
        .report-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
            vertical-align: middle;
        }
        .report-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .text-right {
            text-align: right !important;
        }
        .text-center {
            text-align: center !important;
        }
        .mono {
            font-family: 'JetBrains Mono', 'Courier New', Courier, monospace;
            font-weight: 600;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .badge-success {
            background-color: #dcfce7;
            color: #166534;
        }
        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .badge-warning {
            background-color: #fef9c3;
            color: #854d0e;
        }
        .summary-box {
            display: flex;
            justify-content: flex-end;
            margin-top: 10px;
        }
        .summary-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 15px;
            min-width: 250px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            font-size: 11px;
            border-bottom: 1px dashed #e2e8f0;
        }
        .summary-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .summary-row.grand-total {
            font-size: 13px;
            font-weight: 800;
            color: #0f172a;
            border-top: 1px solid #cbd5e1;
            padding-top: 8px;
            margin-top: 4px;
        }
        .footer {
            text-align: center;
            margin-top: 50px;
            border-top: 1px dashed #cbd5e1;
            padding-top: 15px;
            font-size: 9px;
            color: #64748b;
        }
        .no-print-bar {
            background: #0f172a;
            color: #38bdf8;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 8px;
            margin-bottom: 25px;
            font-family: sans-serif;
            font-size: 11px;
            font-weight: bold;
        }
        .print-btn {
            background: #0284c7;
            color: #fff;
            border: none;
            padding: 6px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            font-size: 11px;
            transition: background 0.2s;
        }
        @page {
            size: auto;
            margin: 0;
        }
        @media print {
            body {
                margin: 15mm;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar no-print">
        <span>REPORTS ENGINE - SYSTEM PRINT PREVIEW</span>
        <button class="print-btn" onclick="window.print()">PRINT REPORT</button>
    </div>

    <div class="container">
        <div class="header">
            <div class="header-left">
                <h1>NEURONET</h1>
                <p>Computer Store & Service Center | ERP Console</p>
            </div>
            <div class="header-right">
                <h2>
                    @switch($reportType)
                        @case('sales') Sales Report @break
                        @case('profit') Profit & Loss Report @break
                        @case('stock') Stock Valuation Report @break
                        @case('product') Product Sales Report @break
                        @case('customer_payment') Customer Payment Report @break
                        @case('sales_ref') Sales Representative Report @break
                        @case('customer_credit') Customer Credit Report @break
                        @case('supplier') Supplier GRN Report @break
                        @case('sale_type') Sale Channels Report @break
                        @case('return') Product Returns Report @break
                        @case('expenses') Expenses Summary Report @break
                        @case('attendance') Staff Attendance Report @break
                        @case('salary') Payroll Salaries Report @break
                    @endswitch
                </h2>
                <p>Generated on: {{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}</p>
            </div>
        </div>

        <div class="meta-grid">
            <div class="meta-item">
                <span>Filter Start Date</span>
                <strong>{{ $fromDate }}</strong>
            </div>
            <div class="meta-item">
                <span>Filter End Date</span>
                <strong>{{ $toDate }}</strong>
            </div>
            <div class="meta-item">
                <span>Generated By</span>
                <strong>{{ Auth::user()->name }}</strong>
            </div>
        </div>

        <!-- Report Data Rendering -->
        @if($reportType === 'sales')
            <!-- 1. Sales Report -->
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Invoice Number</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Type</th>
                        <th>Payment Method</th>
                        <th class="text-center">Paid Status</th>
                        <th class="text-right">Subtotal</th>
                        <th class="text-right">Discount</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totSales = 0; @endphp
                    @forelse($data as $row)
                        @php $totSales += $row->total; @endphp
                        <tr>
                            <td class="mono" style="color: #0284c7;">{{ $row->invoice_number }}</td>
                            <td>{{ $row->created_at->format('Y-m-d H:i') }}</td>
                            <td><strong>{{ $row->customer ? $row->customer->name : 'Walk-in' }}</strong></td>
                            <td>{{ $row->sale_type }}</td>
                            <td>{{ $row->payment_method }}</td>
                            <td class="text-center">
                                <span class="badge {{ $row->is_paid ? 'badge-success' : 'badge-danger' }}">
                                    {{ $row->is_paid ? 'Paid' : 'Unpaid' }}
                                </span>
                            </td>
                            <td class="text-right mono">Rs. {{ number_format($row->subtotal, 2) }}</td>
                            <td class="text-right mono" style="color: #b91c1c;">-Rs. {{ number_format($row->discount, 2) }}</td>
                            <td class="text-right mono" style="font-weight: bold;">Rs. {{ number_format($row->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center" style="color: #94a3b8; padding: 20px;">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="summary-box">
                <div class="summary-card">
                    <div class="summary-row grand-total">
                        <span>Total Sales Volume:</span>
                        <span class="mono">Rs. {{ number_format($totSales, 2) }}</span>
                    </div>
                </div>
            </div>

        @elseif($reportType === 'profit')
            <!-- 2. Profit Report -->
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Product Details</th>
                        <th class="text-center">Qty Sold</th>
                        <th class="text-right">Cost Price</th>
                        <th class="text-right">Selling Price</th>
                        <th class="text-right">Revenue</th>
                        <th class="text-right">Total Cost</th>
                        <th class="text-right">Margin / Profit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['breakdown'] as $row)
                        <tr>
                            <td><strong>{{ $row['product_name'] }}</strong></td>
                            <td class="text-center">{{ $row['qty_sold'] }}</td>
                            <td class="text-right mono">Rs. {{ number_format($row['buying_price'], 2) }}</td>
                            <td class="text-right mono">Rs. {{ number_format($row['selling_price'], 2) }}</td>
                            <td class="text-right mono">Rs. {{ number_format($row['revenue'], 2) }}</td>
                            <td class="text-right mono">Rs. {{ number_format($row['cost'], 2) }}</td>
                            <td class="text-right mono" style="color: #15803d; font-weight: bold;">Rs. {{ number_format($row['profit'], 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center" style="color: #94a3b8; padding: 20px;">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="summary-box">
                <div class="summary-card">
                    <div class="summary-row">
                        <span>Total Revenue:</span>
                        <span class="mono">Rs. {{ number_format($data['total_revenue'], 2) }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Total Product Cost:</span>
                        <span class="mono">Rs. {{ number_format($data['total_cost'], 2) }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Total Expenses:</span>
                        <span class="mono" style="color: #b91c1c;">Rs. {{ number_format($data['total_expenses'], 2) }}</span>
                    </div>
                    <div class="summary-row grand-total">
                        <span>Net Profit:</span>
                        <span class="mono" style="{{ $data['net_profit'] >= 0 ? 'color: #15803d;' : 'color: #b91c1c;' }}">Rs. {{ number_format($data['net_profit'], 2) }}</span>
                    </div>
                </div>
            </div>

        @elseif($reportType === 'stock')
            <!-- 3. Stock Report -->
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Product Details</th>
                        <th>SKU Code</th>
                        <th>Category</th>
                        <th class="text-center">Stock Level</th>
                        <th class="text-right">Cost Price</th>
                        <th class="text-right">Sale Price</th>
                        <th class="text-right">Total Cost Value</th>
                        <th class="text-right">Total Sale Value</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['products'] as $row)
                        <tr>
                            <td><strong>{{ $row['name'] }}</strong></td>
                            <td class="mono">{{ $row['sku'] }}</td>
                            <td>{{ $row['category'] }}</td>
                            <td class="text-center" style="{{ $row['stock'] < 5 ? 'color: #b91c1c; font-weight: bold;' : '' }}">{{ $row['stock'] }}</td>
                            <td class="text-right mono">Rs. {{ number_format($row['buying_price'], 2) }}</td>
                            <td class="text-right mono">Rs. {{ number_format($row['sale_price'], 2) }}</td>
                            <td class="text-right mono">Rs. {{ number_format($row['cost_value'], 2) }}</td>
                            <td class="text-right mono" style="font-weight: bold;">Rs. {{ number_format($row['sale_value'], 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center" style="color: #94a3b8; padding: 20px;">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="summary-box">
                <div class="summary-card">
                    <div class="summary-row">
                        <span>Total Cost Value:</span>
                        <span class="mono">Rs. {{ number_format($data['total_cost_value'], 2) }}</span>
                    </div>
                    <div class="summary-row grand-total">
                        <span>Total Sale Value:</span>
                        <span class="mono" style="color: #0284c7;">Rs. {{ number_format($data['total_sale_value'], 2) }}</span>
                    </div>
                </div>
            </div>

        @elseif($reportType === 'product')
            <!-- 4. Product Report -->
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Product Details</th>
                        <th>SKU Code</th>
                        <th>Brand</th>
                        <th class="text-center">Quantity Sold</th>
                        <th class="text-center">Free Qty Given</th>
                        <th class="text-right">Revenue Generated</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $row)
                        <tr>
                            <td><strong>{{ $row->product ? $row->product->name : 'Unknown Product' }}</strong></td>
                            <td class="mono">{{ $row->product ? $row->product->sku : 'N/A' }}</td>
                            <td>{{ $row->product ? $row->product->brand : 'N/A' }}</td>
                            <td class="text-center" style="font-weight: bold;">{{ $row->qty_sold }}</td>
                            <td class="text-center">{{ $row->free_qty }}</td>
                            <td class="text-right mono" style="font-weight: bold; color: #0284c7;">Rs. {{ number_format($row->revenue, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center" style="color: #94a3b8; padding: 20px;">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>

        @elseif($reportType === 'customer_payment')
            <!-- 5. Customer Payment Report -->
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Payment Method</th>
                        <th class="text-center">Transaction Count</th>
                        <th class="text-right">Total Revenue Volume</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totVol = 0; @endphp
                    @forelse($data as $row)
                        @php $totVol += $row->total_volume; @endphp
                        <tr>
                            <td style="text-transform: uppercase;"><strong>{{ $row->payment_method }}</strong></td>
                            <td class="text-center">{{ $row->count }}</td>
                            <td class="text-right mono" style="font-weight: bold; color: #0284c7;">Rs. {{ number_format($row->total_volume, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center" style="color: #94a3b8; padding: 20px;">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="summary-box">
                <div class="summary-card">
                    <div class="summary-row grand-total">
                        <span>Total Revenue Volume:</span>
                        <span class="mono">Rs. {{ number_format($totVol, 2) }}</span>
                    </div>
                </div>
            </div>

        @elseif($reportType === 'sales_ref')
            <!-- 6. Sales Ref Report -->
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Salesperson Name</th>
                        <th>Designation</th>
                        <th class="text-center">Total Invoices Created</th>
                        <th class="text-right">Total Sales Volume</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $row)
                        <tr>
                            <td><strong>{{ $row->employee ? $row->employee->name : 'Unknown Reference' }}</strong></td>
                            <td>{{ $row->employee ? $row->employee->designation : 'N/A' }}</td>
                            <td class="text-center">{{ $row->count }}</td>
                            <td class="text-right mono" style="font-weight: bold; color: #0284c7;">Rs. {{ number_format($row->total_volume, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center" style="color: #94a3b8; padding: 20px;">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>

        @elseif($reportType === 'customer_credit')
            <!-- 7. Customer Credit Report -->
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Invoice Number</th>
                        <th>Date Issued</th>
                        <th>Customer Name</th>
                        <th>Contact Mobile</th>
                        <th class="text-right">Grand Total</th>
                        <th class="text-right">Customer Paid</th>
                        <th class="text-right">Outstanding Credit</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totCredit = 0; @endphp
                    @forelse($data as $row)
                        @php $totCredit += abs($row->balance); @endphp
                        <tr>
                            <td class="mono" style="color: #0284c7;">{{ $row->invoice_number }}</td>
                            <td>{{ $row->created_at->format('Y-m-d') }}</td>
                            <td><strong>{{ $row->customer ? $row->customer->name : 'Walk-in' }}</strong></td>
                            <td>{{ $row->customer ? $row->customer->phone : 'N/A' }}</td>
                            <td class="text-right mono">Rs. {{ number_format($row->total, 2) }}</td>
                            <td class="text-right mono">Rs. {{ number_format($row->customer_paid, 2) }}</td>
                            <td class="text-right mono" style="color: #b91c1c; font-weight: bold;">Rs. {{ number_format(abs($row->balance), 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center" style="color: #94a3b8; padding: 20px;">No outstanding customer credits.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="summary-box">
                <div class="summary-card">
                    <div class="summary-row grand-total">
                        <span>Total Outstanding Credit:</span>
                        <span class="mono" style="color: #b91c1c;">Rs. {{ number_format($totCredit, 2) }}</span>
                    </div>
                </div>
            </div>

        @elseif($reportType === 'supplier')
            <!-- 8. Supplier Report -->
            <table class="report-table">
                <thead>
                    <tr>
                        <th>GRN Number</th>
                        <th>Supplier Details</th>
                        <th>Received By</th>
                        <th class="text-center">Date Received</th>
                        <th class="text-right">Total Cost Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totGRN = 0; @endphp
                    @forelse($data as $row)
                        @php $totGRN += $row->total_amount; @endphp
                        <tr>
                            <td class="mono" style="color: #0284c7;">{{ $row->grn_number }}</td>
                            <td>
                                <strong>{{ $row->supplier ? $row->supplier->name : 'N/A' }}</strong>
                                @if($row->supplier && $row->supplier->company_name)
                                    <div style="font-size: 9px; color: #64748b;">{{ $row->supplier->company_name }}</div>
                                @endif
                            </td>
                            <td>{{ $row->receivedBy ? $row->receivedBy->name : 'N/A' }}</td>
                            <td class="text-center">{{ $row->date_received }}</td>
                            <td class="text-right mono" style="font-weight: bold;">Rs. {{ number_format($row->total_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center" style="color: #94a3b8; padding: 20px;">No goods received records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="summary-box">
                <div class="summary-card">
                    <div class="summary-row grand-total">
                        <span>Total Procurement Value:</span>
                        <span class="mono">Rs. {{ number_format($totGRN, 2) }}</span>
                    </div>
                </div>
            </div>

        @elseif($reportType === 'sale_type')
            <!-- 9. Sale Type Report -->
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Sale Type (Channel)</th>
                        <th class="text-center">Invoices Count</th>
                        <th class="text-right">Total Revenue Volume</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totVol = 0; @endphp
                    @forelse($data as $row)
                        @php $totVol += $row->total_volume; @endphp
                        <tr>
                            <td style="text-transform: uppercase;"><strong>{{ $row->sale_type }}</strong></td>
                            <td class="text-center">{{ $row->count }}</td>
                            <td class="text-right mono" style="font-weight: bold; color: #0284c7;">Rs. {{ number_format($row->total_volume, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center" style="color: #94a3b8; padding: 20px;">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="summary-box">
                <div class="summary-card">
                    <div class="summary-row grand-total">
                        <span>Total Sales Volume:</span>
                        <span class="mono">Rs. {{ number_format($totVol, 2) }}</span>
                    </div>
                </div>
            </div>

        @elseif($reportType === 'return')
            <!-- 10. Return Report -->
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Return Number</th>
                        <th>Reference Document</th>
                        <th>Type</th>
                        <th>Reason for Return</th>
                        <th class="text-right">Refund Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totReturn = 0; @endphp
                    @forelse($data as $row)
                        @php $totReturn += $row->refund_amount; @endphp
                        <tr>
                            <td class="mono"><strong>{{ $row->return_number }}</strong></td>
                            <td>
                                @if($row->invoice)
                                    Invoice: {{ $row->invoice->invoice_number }}
                                @elseif($row->supplier)
                                    Supplier: {{ $row->supplier->name }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td style="text-transform: uppercase;">{{ str_replace('_', ' ', $row->type) }}</td>
                            <td>{{ $row->reason }}</td>
                            <td class="text-right mono" style="font-weight: bold; color: #b91c1c;">Rs. {{ number_format($row->refund_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center" style="color: #94a3b8; padding: 20px;">No returns found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="summary-box">
                <div class="summary-card">
                    <div class="summary-row grand-total">
                        <span>Total Refund Payout:</span>
                        <span class="mono" style="color: #b91c1c;">Rs. {{ number_format($totReturn, 2) }}</span>
                    </div>
                </div>
            </div>

        @elseif($reportType === 'expenses')
            <!-- 11. Expenses Report -->
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Expense Category</th>
                        <th class="text-center">Transactions Count</th>
                        <th class="text-right">Total Expense Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totExp = 0; @endphp
                    @forelse($data as $row)
                        @php $totExp += $row->total_amount; @endphp
                        <tr>
                            <td><strong>{{ $row->category }}</strong></td>
                            <td class="text-center">{{ $row->count }}</td>
                            <td class="text-right mono" style="font-weight: bold; color: #b91c1c;">Rs. {{ number_format($row->total_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center" style="color: #94a3b8; padding: 20px;">No expenses recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="summary-box">
                <div class="summary-card">
                    <div class="summary-row grand-total">
                        <span>Total Overhead Expenses:</span>
                        <span class="mono" style="color: #b91c1c;">Rs. {{ number_format($totExp, 2) }}</span>
                    </div>
                </div>
            </div>

        @elseif($reportType === 'attendance')
            <!-- 12. Attendance Report -->
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Employee Name</th>
                        <th>Designation</th>
                        <th class="text-center" style="color: #166534;">Days Present</th>
                        <th class="text-center" style="color: #854d0e;">Days Late</th>
                        <th class="text-center" style="color: #991b1b;">Days Absent</th>
                        <th class="text-center">Attendance Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $row)
                        @php
                            $totalDays = $row->present_days + $row->late_days + $row->absent_days;
                            $rate = $totalDays > 0 ? (($row->present_days + $row->late_days) / $totalDays) * 100 : 0;
                        @endphp
                        <tr>
                            <td><strong>{{ $row->employee ? $row->employee->name : 'Unknown Staff' }}</strong></td>
                            <td>{{ $row->employee ? $row->employee->designation : 'N/A' }}</td>
                            <td class="text-center" style="color: #166534; font-weight: bold;">{{ $row->present_days }}</td>
                            <td class="text-center" style="color: #854d0e; font-weight: bold;">{{ $row->late_days }}</td>
                            <td class="text-center" style="color: #991b1b; font-weight: bold;">{{ $row->absent_days }}</td>
                            <td class="text-center" style="font-weight: bold; color: {{ $rate >= 90 ? '#166534' : ($rate >= 75 ? '#854d0e' : '#991b1b') }}">
                                {{ number_format($rate, 1) }}%
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center" style="color: #94a3b8; padding: 20px;">No attendance logs available.</td></tr>
                    @endforelse
                </tbody>
            </table>

        @elseif($reportType === 'salary')
            <!-- 13. Salary Report -->
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Payslip No</th>
                        <th>Employee Profile</th>
                        <th>Designation</th>
                        <th>Paid For Month</th>
                        <th class="text-center">Payment Date</th>
                        <th>Payment Method</th>
                        <th class="text-right">Amount Paid</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totSalary = 0; @endphp
                    @forelse($data as $row)
                        @php $totSalary += $row->amount_paid; @endphp
                        <tr>
                            <td class="mono">{{ $row->payslip_no }}</td>
                            <td><strong>{{ $row->employee ? $row->employee->name : 'Unknown' }}</strong></td>
                            <td>{{ $row->employee ? $row->employee->designation : 'N/A' }}</td>
                            <td>{{ $row->paid_for_month }}</td>
                            <td class="text-center">{{ $row->payment_date }}</td>
                            <td>{{ $row->payment_method }}</td>
                            <td class="text-right mono" style="font-weight: bold;">Rs. {{ number_format($row->amount_paid, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center" style="color: #94a3b8; padding: 20px;">No salary payment records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="summary-box">
                <div class="summary-card">
                    <div class="summary-row grand-total">
                        <span>Total Payroll Cost:</span>
                        <span class="mono" style="color: #b91c1c;">Rs. {{ number_format($totSalary, 2) }}</span>
                    </div>
                </div>
            </div>
        @endif

        <div class="footer">
            <p>NEURONET ERP - Internal Operations & Financial Reporting Engine</p>
            <p>&copy; {{ date('Y') }} Neuronet. All rights reserved. Confidential document for internal use only.</p>
        </div>
    </div>

    <script>
        // Automatic print trigger on load after short timeout to allow styles to render
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 300);
        };
    </script>
</body>
</html>
