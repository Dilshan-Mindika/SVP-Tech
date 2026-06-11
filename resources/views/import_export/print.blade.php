<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print - {{ $title }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1e293b;
            background-color: #ffffff;
            margin: 20px;
            font-size: 11px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .header h1 {
            font-size: 20px;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0f172a;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #64748b;
            font-size: 11px;
        }
        .meta-grid {
            display: grid;
            grid-template-cols: 1fr 1fr;
            margin-bottom: 20px;
            font-size: 11px;
            color: #475569;
        }
        .meta-grid div:last-child {
            text-align: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.5px;
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            text-align: left;
        }
        td {
            border: 1px solid #e2e8f0;
            padding: 7px 10px;
            text-align: left;
        }
        tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        @page {
            size: auto;
            margin: 0;
        }
        @media print {
            body {
                margin: 15mm;
                -webkit-print-color-adjust: exact;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CloudTech Computer Store</h1>
        <p>Internal Mainframe Record Export</p>
    </div>

    <div class="meta-grid">
        <div>
            <strong>Report Title:</strong> {{ strtoupper($title) }}<br>
            <strong>Total Records:</strong> {{ $records->count() }}
        </div>
        <div>
            <strong>Printed Date:</strong> {{ date('Y-m-d H:i:s') }}<br>
            <strong>Printed By:</strong> {{ auth()->user()->name }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                @foreach($fields as $key => $config)
                    <th>{{ $config['label'] }}</th>
                @endforeach
                <th style="width: 130px;">Created At</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $rec)
                <tr>
                    @foreach($fields as $key => $config)
                        <td>
                            @php
                                $val = '';
                                if ($type === 'products' && $key === 'category_name') {
                                    $val = $rec->category ? $rec->category->name : 'N/A';
                                } elseif ($type === 'salaries' && $key === 'employee_email') {
                                    $val = $rec->employee ? $rec->employee->email : 'N/A';
                                } elseif ($type === 'invoices' && $key === 'customer_phone') {
                                    $val = $rec->customer ? $rec->customer->phone : 'N/A';
                                } elseif ($type === 'quotations' && $key === 'customer_phone') {
                                    $val = $rec->customer_phone ?? ($rec->customer ? $rec->customer->phone : 'N/A');
                                } elseif ($type === 'grns' && $key === 'supplier_phone') {
                                    $val = $rec->supplier ? $rec->supplier->phone : 'N/A';
                                } elseif ($type === 'warranties' && $key === 'customer_phone') {
                                    $val = $rec->customer ? $rec->customer->phone : 'N/A';
                                } elseif ($type === 'warranties' && $key === 'invoice_number') {
                                    $val = $rec->invoice ? $rec->invoice->invoice_number : 'N/A';
                                } elseif ($type === 'warranties' && $key === 'product_sku') {
                                    $val = $rec->product ? $rec->product->sku : 'N/A';
                                } elseif ($type === 'returns' && $key === 'invoice_number') {
                                    $val = $rec->invoice ? $rec->invoice->invoice_number : 'N/A';
                                } elseif ($type === 'returns' && $key === 'supplier_phone') {
                                    $val = $rec->supplier ? $rec->supplier->phone : 'N/A';
                                } elseif ($type === 'serials' && $key === 'product_sku') {
                                    $val = $rec->product ? $rec->product->sku : 'N/A';
                                } else {
                                    $val = $rec->$key;
                                }

                                // Format values cleanly
                                if ($config['type'] === 'numeric') {
                                    $val = 'Rs. ' . number_format((float)$val, 2);
                                } elseif ($config['type'] === 'boolean') {
                                    $val = $val ? 'Active' : 'Inactive';
                                }
                            @endphp
                            {{ $val }}
                        </td>
                    @endforeach
                    <td>{{ $rec->created_at->format('Y-m-d H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($fields) + 1 }}" style="text-align: center; color: #64748b; padding: 20px;">
                        No records found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 300);
        };
    </script>
</body>
</html>
