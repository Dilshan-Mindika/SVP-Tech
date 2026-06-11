@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <h1 class="orbitron-title text-2xl font-black text-slate-100 tracking-wider">REPORTS TERMINAL</h1>
            <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest font-semibold font-sans">Business intelligence, payroll, stock, and profit analytics</p>
        </div>
        
        <div class="flex gap-2">
            <button onclick="triggerPrint()" class="px-4 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-350 hover:text-slate-200 text-xs font-bold transition-all flex items-center gap-2">
                <i class="fa-solid fa-print"></i>
                <span>PRINT REPORT</span>
            </button>
            <button onclick="triggerDownload()" class="px-4 py-2 bg-cyan-500 text-slate-950 font-bold rounded-lg text-xs transition-all hover:bg-cyan-400 shadow-neon-cyan flex items-center gap-2">
                <i class="fa-solid fa-file-arrow-down"></i>
                <span>DOWNLOAD CSV</span>
            </button>
        </div>
    </div>

    <!-- Stats Grid -->
    @if(isset($stats) && count($stats) > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @foreach($stats as $stat)
            @php
                $colorClass = 'text-cyan-400';
                if ($stat['color'] === 'rose') $colorClass = 'text-rose-400';
                elseif ($stat['color'] === 'emerald') $colorClass = 'text-emerald-400';
                elseif ($stat['color'] === 'amber') $colorClass = 'text-amber-400';
                elseif ($stat['color'] === 'slate') $colorClass = 'text-slate-300';
            @endphp
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group transition-all hover:border-slate-700/80">
                <div class="absolute -right-4 -bottom-4 text-6xl transition-transform group-hover:scale-110" style="color: #94a3b8; opacity: 0.15;">
                    <i class="fa-solid {{ $stat['icon'] }}"></i>
                </div>
                <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">{{ $stat['title'] }}</span>
                <h3 class="text-xl font-extrabold {{ $colorClass }} mt-1 mono-text font-black">{{ $stat['value'] }}</h3>
                <span class="text-[9px] text-slate-500 font-semibold block mt-1">{{ $stat['sub'] }}</span>
            </div>
        @endforeach
    </div>
    @endif

    <!-- Filters Section -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
        <form action="{{ route('reports.index') }}" method="GET" id="reportsForm" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Select Report Type</label>
                <select name="report_type" id="reportTypeSelect" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                    <option value="sales" {{ $reportType === 'sales' ? 'selected' : '' }}>1. Sales Report (Invoices Summary)</option>
                    <option value="profit" {{ $reportType === 'profit' ? 'selected' : '' }}>2. Profit Report (Net Earnings)</option>
                    <option value="stock" {{ $reportType === 'stock' ? 'selected' : '' }}>3. Stock Report (Inventory Valuation)</option>
                    <option value="product" {{ $reportType === 'product' ? 'selected' : '' }}>4. Product Report (Sales Movements)</option>
                    <option value="customer_payment" {{ $reportType === 'customer_payment' ? 'selected' : '' }}>5. Customer Payment Report (Methods)</option>
                    <option value="sales_ref" {{ $reportType === 'sales_ref' ? 'selected' : '' }}>6. Sales Ref Report (Salespersons)</option>
                    <option value="customer_credit" {{ $reportType === 'customer_credit' ? 'selected' : '' }}>7. Customer Credit Report (Balances)</option>
                    <option value="supplier" {{ $reportType === 'supplier' ? 'selected' : '' }}>8. Supplier Report (GRNs Received)</option>
                    <option value="sale_type" {{ $reportType === 'sale_type' ? 'selected' : '' }}>9. Sale Type Report (Shop vs Online)</option>
                    <option value="return" {{ $reportType === 'return' ? 'selected' : '' }}>10. Return Report (Product Returns)</option>
                    <option value="expenses" {{ $reportType === 'expenses' ? 'selected' : '' }}>11. Expenses Report (Category Summaries)</option>
                    <option value="attendance" {{ $reportType === 'attendance' ? 'selected' : '' }}>12. Attendance Report (Staff Logs)</option>
                    <option value="salary" {{ $reportType === 'salary' ? 'selected' : '' }}>13. Salary Report (Payroll Details)</option>
                </select>
            </div>

            <div>
                <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">From Date</label>
                <input type="date" name="from_date" value="{{ $fromDate }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
            </div>

            <div>
                <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">To Date</label>
                <input type="date" name="to_date" value="{{ $toDate }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
            </div>

            <div>
                <button type="submit" class="w-full py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-lg transition-colors">
                    GENERATE REPORT
                </button>
            </div>
        </form>
    </div>

    <!-- Report Visual Analytics Graph -->
    @if($chartData && count($chartData['labels']) > 0)
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 relative overflow-hidden">
        <!-- Background Glow Accent -->
        <div class="absolute -top-24 -right-24 h-48 w-48 rounded-full bg-cyan-500/5 blur-3xl"></div>
        <h3 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-chart-simple text-cyan-400"></i>
            <span>Visual Analytics Trend</span>
        </h3>
        <div class="h-80 w-full relative">
            <canvas id="reportsChart"></canvas>
        </div>
    </div>
    @endif

    <!-- Active Report Presentation -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <div class="p-5 border-b border-slate-800 bg-slate-900/60 flex justify-between items-center">
            <h3 class="orbitron-title text-sm font-black text-cyan-400 uppercase tracking-widest">
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
            </h3>
            <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">
                Period: {{ $fromDate }} to {{ $toDate }}
            </span>
        </div>

        <div class="overflow-x-auto">
            @if($reportType === 'sales')
                <!-- 1. Sales Report -->
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-widest font-semibold text-[10px] bg-slate-950/20">
                            <th class="py-3 px-6">Invoice Number</th>
                            <th class="py-3 px-6">Date</th>
                            <th class="py-3 px-6">Customer</th>
                            <th class="py-3 px-6">Type</th>
                            <th class="py-3 px-6">Payment</th>
                            <th class="py-3 px-6 text-center">Paid Status</th>
                            <th class="py-3 px-6 text-right">Subtotal</th>
                            <th class="py-3 px-6 text-right">Discount</th>
                            <th class="py-3 px-6 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-850">
                        @php $totSales = 0; @endphp
                        @forelse($data as $row)
                            @php $totSales += $row->total; @endphp
                            <tr class="hover:bg-slate-800/10 transition-colors">
                                <td class="py-3.5 px-6 font-bold text-cyan-400"><a href="{{ route('invoices.show', $row->id) }}">{{ $row->invoice_number }}</a></td>
                                <td class="py-3.5 px-6 text-slate-400">{{ $row->created_at->format('Y-m-d H:i') }}</td>
                                <td class="py-3.5 px-6 text-slate-200 font-semibold">{{ $row->customer ? $row->customer->name : 'Walk-in' }}</td>
                                <td class="py-3.5 px-6 text-slate-300">{{ $row->sale_type }}</td>
                                <td class="py-3.5 px-6 text-slate-300">{{ $row->payment_method }}</td>
                                <td class="py-3.5 px-6 text-center">
                                    <span class="px-1.5 py-0.5 rounded text-[8px] uppercase tracking-wider font-bold {{ $row->is_paid ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400' }}">
                                        {{ $row->is_paid ? 'Paid' : 'Unpaid' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-6 text-right mono-text text-slate-300">Rs. {{ number_format($row->subtotal, 2) }}</td>
                                <td class="py-3.5 px-6 text-right mono-text text-rose-400">-Rs. {{ number_format($row->discount, 2) }}</td>
                                <td class="py-3.5 px-6 text-right mono-text text-slate-100 font-bold">Rs. {{ number_format($row->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="py-8 text-center text-slate-600">No records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-5 border-t border-slate-800 bg-slate-950/40 flex justify-end font-bold text-xs text-cyan-400">
                    <span>Total Sales Volume: <strong class="text-slate-100 font-black ml-2 mono-text text-sm">Rs. {{ number_format($totSales, 2) }}</strong></span>
                </div>

            @elseif($reportType === 'profit')
                <!-- 2. Profit Report -->
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-widest font-semibold text-[10px] bg-slate-950/20">
                            <th class="py-3 px-6">Product Details</th>
                            <th class="py-3 px-6 text-center">Qty Sold</th>
                            <th class="py-3 px-6 text-right">Cost Price</th>
                            <th class="py-3 px-6 text-right">Selling Price</th>
                            <th class="py-3 px-6 text-right">Revenue</th>
                            <th class="py-3 px-6 text-right">Total Cost</th>
                            <th class="py-3 px-6 text-right">Margin / Profit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-850">
                        @forelse($data['breakdown'] as $row)
                            <tr class="hover:bg-slate-800/10 transition-colors">
                                <td class="py-3.5 px-6 font-bold text-slate-200">{{ $row['product_name'] }}</td>
                                <td class="py-3.5 px-6 text-center font-semibold text-slate-300">{{ $row['qty_sold'] }}</td>
                                <td class="py-3.5 px-6 text-right mono-text text-slate-400">Rs. {{ number_format($row['buying_price'], 2) }}</td>
                                <td class="py-3.5 px-6 text-right mono-text text-slate-300">Rs. {{ number_format($row['selling_price'], 2) }}</td>
                                <td class="py-3.5 px-6 text-right mono-text text-slate-200">Rs. {{ number_format($row['revenue'], 2) }}</td>
                                <td class="py-3.5 px-6 text-right mono-text text-slate-200">Rs. {{ number_format($row['cost'], 2) }}</td>
                                <td class="py-3.5 px-6 text-right mono-text text-emerald-400 font-bold">Rs. {{ number_format($row['profit'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-8 text-center text-slate-600">No records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-6 border-t border-slate-800 bg-slate-950/40 grid grid-cols-2 md:grid-cols-4 gap-4 text-xs font-semibold text-slate-400">
                    <div class="space-y-1">
                        <span>Total Revenue:</span>
                        <div class="text-sm font-black text-slate-200 mono-text">Rs. {{ number_format($data['total_revenue'], 2) }}</div>
                    </div>
                    <div class="space-y-1">
                        <span>Total Product Cost:</span>
                        <div class="text-sm font-black text-slate-200 mono-text">Rs. {{ number_format($data['total_cost'], 2) }}</div>
                    </div>
                    <div class="space-y-1">
                        <span>Total Overhead Expenses:</span>
                        <div class="text-sm font-black text-rose-400 mono-text">Rs. {{ number_format($data['total_expenses'], 2) }}</div>
                    </div>
                    <div class="space-y-1 border-l border-slate-800 pl-4">
                        <span class="text-cyan-400">Net Profit Margin:</span>
                        <div class="text-base font-black {{ $data['net_profit'] >= 0 ? 'text-emerald-400' : 'text-rose-500' }} mono-text">Rs. {{ number_format($data['net_profit'], 2) }}</div>
                    </div>
                </div>

            @elseif($reportType === 'stock')
                <!-- 3. Stock Report -->
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-widest font-semibold text-[10px] bg-slate-950/20">
                            <th class="py-3 px-6">Product Details</th>
                            <th class="py-3 px-6">SKU Code</th>
                            <th class="py-3 px-6">Category</th>
                            <th class="py-3 px-6 text-center">Stock Level</th>
                            <th class="py-3 px-6 text-right">Cost Price</th>
                            <th class="py-3 px-6 text-right">Sale Price</th>
                            <th class="py-3 px-6 text-right">Total Cost Value</th>
                            <th class="py-3 px-6 text-right">Total Sale Value</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-850">
                        @forelse($data['products'] as $row)
                            <tr class="hover:bg-slate-800/10 transition-colors">
                                <td class="py-3.5 px-6 font-bold text-slate-200">{{ $row['name'] }}</td>
                                <td class="py-3.5 px-6 font-semibold text-slate-400 font-mono">{{ $row['sku'] }}</td>
                                <td class="py-3.5 px-6 text-slate-300">{{ $row['category'] }}</td>
                                <td class="py-3.5 px-6 text-center font-bold {{ $row['stock'] < 5 ? 'text-rose-500' : 'text-slate-200' }}">{{ $row['stock'] }}</td>
                                <td class="py-3.5 px-6 text-right mono-text text-slate-400">Rs. {{ number_format($row['buying_price'], 2) }}</td>
                                <td class="py-3.5 px-6 text-right mono-text text-slate-300">Rs. {{ number_format($row['sale_price'], 2) }}</td>
                                <td class="py-3.5 px-6 text-right mono-text text-slate-200">Rs. {{ number_format($row['cost_value'], 2) }}</td>
                                <td class="py-3.5 px-6 text-right mono-text text-slate-200 font-bold">Rs. {{ number_format($row['sale_value'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="py-8 text-center text-slate-600">No records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-5 border-t border-slate-800 bg-slate-950/40 flex gap-6 justify-end font-bold text-xs">
                    <span class="text-slate-400">Total Stock Valuation (Cost): <strong class="text-slate-100 font-black ml-2 mono-text text-sm">Rs. {{ number_format($data['total_cost_value'], 2) }}</strong></span>
                    <span class="text-cyan-400">Total Stock Valuation (Retail): <strong class="text-cyan-400 font-black ml-2 mono-text text-sm">Rs. {{ number_format($data['total_sale_value'], 2) }}</strong></span>
                </div>

            @elseif($reportType === 'product')
                <!-- 4. Product Report -->
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-widest font-semibold text-[10px] bg-slate-950/20">
                            <th class="py-3 px-6">Product Details</th>
                            <th class="py-3 px-6">SKU Code</th>
                            <th class="py-3 px-6">Brand</th>
                            <th class="py-3 px-6 text-center">Quantity Sold</th>
                            <th class="py-3 px-6 text-center">Free Qty Given</th>
                            <th class="py-3 px-6 text-right">Revenue Generated</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-850">
                        @forelse($data as $row)
                            <tr class="hover:bg-slate-800/10 transition-colors">
                                <td class="py-3.5 px-6 font-bold text-slate-200">{{ $row->product ? $row->product->name : 'Unknown Product' }}</td>
                                <td class="py-3.5 px-6 font-semibold text-slate-400 font-mono">{{ $row->product ? $row->product->sku : 'N/A' }}</td>
                                <td class="py-3.5 px-6 text-slate-300">{{ $row->product ? $row->product->brand : 'N/A' }}</td>
                                <td class="py-3.5 px-6 text-center font-bold text-slate-200">{{ $row->qty_sold }}</td>
                                <td class="py-3.5 px-6 text-center text-slate-400 font-semibold">{{ $row->free_qty }}</td>
                                <td class="py-3.5 px-6 text-right font-bold text-cyan-400 mono-text">Rs. {{ number_format($row->revenue, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-8 text-center text-slate-600">No records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>

            @elseif($reportType === 'customer_payment')
                <!-- 5. Customer Payment Report -->
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-widest font-semibold text-[10px] bg-slate-950/20">
                            <th class="py-3 px-6">Payment Method</th>
                            <th class="py-3 px-6 text-center">Transaction Count</th>
                            <th class="py-3 px-6 text-right">Total Revenue Volume</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-850">
                        @php $totVol = 0; @endphp
                        @forelse($data as $row)
                            @php $totVol += $row->total_volume; @endphp
                            <tr class="hover:bg-slate-800/10 transition-colors">
                                <td class="py-3.5 px-6 font-bold text-slate-200 uppercase">{{ $row->payment_method }}</td>
                                <td class="py-3.5 px-6 text-center font-bold text-slate-350">{{ $row->count }}</td>
                                <td class="py-3.5 px-6 text-right font-black text-cyan-400 mono-text">Rs. {{ number_format($row->total_volume, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-8 text-center text-slate-600">No records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-5 border-t border-slate-800 bg-slate-950/40 flex justify-end font-bold text-xs text-cyan-400">
                    <span>Total Sales Volume: <strong class="text-slate-100 font-black ml-2 mono-text text-sm">Rs. {{ number_format($totVol, 2) }}</strong></span>
                </div>

            @elseif($reportType === 'sales_ref')
                <!-- 6. Sales Ref Report -->
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-widest font-semibold text-[10px] bg-slate-950/20">
                            <th class="py-3 px-6">Salesperson Name</th>
                            <th class="py-3 px-6">Designation</th>
                            <th class="py-3 px-6 text-center">Total Invoices Created</th>
                            <th class="py-3 px-6 text-right">Total Sales Volume</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-850">
                        @forelse($data as $row)
                            <tr class="hover:bg-slate-800/10 transition-colors">
                                <td class="py-3.5 px-6 font-bold text-slate-200">{{ $row->employee ? $row->employee->name : 'Unknown Reference' }}</td>
                                <td class="py-3.5 px-6 text-slate-300 font-semibold">{{ $row->employee ? $row->employee->designation : 'N/A' }}</td>
                                <td class="py-3.5 px-6 text-center font-bold text-slate-400">{{ $row->count }}</td>
                                <td class="py-3.5 px-6 text-right font-bold text-cyan-400 mono-text">Rs. {{ number_format($row->total_volume, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-8 text-center text-slate-600">No records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>

            @elseif($reportType === 'customer_credit')
                <!-- 7. Customer Credit Report -->
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-widest font-semibold text-[10px] bg-slate-950/20">
                            <th class="py-3 px-6">Invoice Number</th>
                            <th class="py-3 px-6">Date Issued</th>
                            <th class="py-3 px-6">Customer Name</th>
                            <th class="py-3 px-6">Contact Mobile</th>
                            <th class="py-3 px-6 text-right">Grand Total</th>
                            <th class="py-3 px-6 text-right">Customer Paid</th>
                            <th class="py-3 px-6 text-right">Outstanding Credit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-850">
                        @php $totCredit = 0; @endphp
                        @forelse($data as $row)
                            @php $totCredit += abs($row->balance); @endphp
                            <tr class="hover:bg-slate-800/10 transition-colors">
                                <td class="py-3.5 px-6 font-bold text-cyan-400"><a href="{{ route('invoices.show', $row->id) }}">{{ $row->invoice_number }}</a></td>
                                <td class="py-3.5 px-6 text-slate-450">{{ $row->created_at->format('Y-m-d') }}</td>
                                <td class="py-3.5 px-6 text-slate-200 font-bold">{{ $row->customer ? $row->customer->name : 'Walk-in' }}</td>
                                <td class="py-3.5 px-6 text-slate-400 font-semibold">{{ $row->customer ? $row->customer->phone : 'N/A' }}</td>
                                <td class="py-3.5 px-6 text-right mono-text text-slate-350">Rs. {{ number_format($row->total, 2) }}</td>
                                <td class="py-3.5 px-6 text-right mono-text text-slate-350">Rs. {{ number_format($row->customer_paid, 2) }}</td>
                                <td class="py-3.5 px-6 text-right mono-text text-rose-450 font-black">Rs. {{ number_format(abs($row->balance), 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-8 text-center text-slate-600">No outstanding customer credits.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-5 border-t border-slate-800 bg-slate-950/40 flex justify-end font-bold text-xs text-rose-450">
                    <span>Total Outstanding Credit: <strong class="text-rose-450 font-black ml-2 mono-text text-sm">Rs. {{ number_format($totCredit, 2) }}</strong></span>
                </div>

            @elseif($reportType === 'supplier')
                <!-- 8. Supplier Report -->
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-widest font-semibold text-[10px] bg-slate-950/20">
                            <th class="py-3 px-6">GRN Number</th>
                            <th class="py-3 px-6">Supplier Details</th>
                            <th class="py-3 px-6">Received By</th>
                            <th class="py-3 px-6 text-center">Date Received</th>
                            <th class="py-3 px-6 text-right">Total Cost Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-850">
                        @php $totGRN = 0; @endphp
                        @forelse($data as $row)
                            @php $totGRN += $row->total_amount; @endphp
                            <tr class="hover:bg-slate-800/10 transition-colors">
                                <td class="py-3.5 px-6 font-bold text-cyan-400">{{ $row->grn_number }}</td>
                                <td class="py-3.5 px-6">
                                    <span class="font-bold text-slate-200 block">{{ $row->supplier ? $row->supplier->name : 'N/A' }}</span>
                                    <span class="text-[10px] text-slate-500 block">{{ $row->supplier ? $row->supplier->company_name : '' }}</span>
                                </td>
                                <td class="py-3.5 px-6 text-slate-300 font-semibold">{{ $row->receivedBy ? $row->receivedBy->name : 'N/A' }}</td>
                                <td class="py-3.5 px-6 text-center text-slate-400 font-mono">{{ $row->date_received }}</td>
                                <td class="py-3.5 px-6 text-right font-bold text-slate-200 mono-text">Rs. {{ number_format($row->total_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-8 text-center text-slate-600">No goods received records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-5 border-t border-slate-800 bg-slate-950/40 flex justify-end font-bold text-xs text-cyan-400">
                    <span>Total Procurement Value: <strong class="text-slate-100 font-black ml-2 mono-text text-sm">Rs. {{ number_format($totGRN, 2) }}</strong></span>
                </div>

            @elseif($reportType === 'sale_type')
                <!-- 9. Sale Type Report -->
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-widest font-semibold text-[10px] bg-slate-950/20">
                            <th class="py-3 px-6">Sale Type (Channel)</th>
                            <th class="py-3 px-6 text-center">Invoices Count</th>
                            <th class="py-3 px-6 text-right">Total Revenue Volume</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-850">
                        @php $totVol = 0; @endphp
                        @forelse($data as $row)
                            @php $totVol += $row->total_volume; @endphp
                            <tr class="hover:bg-slate-800/10 transition-colors">
                                <td class="py-3.5 px-6 font-bold text-slate-200 uppercase">{{ $row->sale_type }}</td>
                                <td class="py-3.5 px-6 text-center font-bold text-slate-350">{{ $row->count }}</td>
                                <td class="py-3.5 px-6 text-right font-black text-cyan-400 mono-text">Rs. {{ number_format($row->total_volume, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-8 text-center text-slate-600">No records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-5 border-t border-slate-800 bg-slate-950/40 flex justify-end font-bold text-xs text-cyan-400">
                    <span>Total Sales Volume: <strong class="text-slate-100 font-black ml-2 mono-text text-sm">Rs. {{ number_format($totVol, 2) }}</strong></span>
                </div>

            @elseif($reportType === 'return')
                <!-- 10. Return Report -->
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-widest font-semibold text-[10px] bg-slate-950/20">
                            <th class="py-3 px-6">Return Number</th>
                            <th class="py-3 px-6">Reference Document</th>
                            <th class="py-3 px-6">Type</th>
                            <th class="py-3 px-6">Reason for Return</th>
                            <th class="py-3 px-6 text-right">Refund Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-850">
                        @php $totReturn = 0; @endphp
                        @forelse($data as $row)
                            @php $totReturn += $row->refund_amount; @endphp
                            <tr class="hover:bg-slate-800/10 transition-colors">
                                <td class="py-3.5 px-6 font-bold text-slate-200">{{ $row->return_number }}</td>
                                <td class="py-3.5 px-6 text-cyan-450 font-bold">
                                    @if($row->invoice)
                                        Invoice: {{ $row->invoice->invoice_number }}
                                    @elseif($row->supplier)
                                        Supplier: {{ $row->supplier->name }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="py-3.5 px-6 font-semibold uppercase tracking-wider text-[10px] text-slate-400">{{ str_replace('_', ' ', $row->type) }}</td>
                                <td class="py-3.5 px-6 text-slate-300 font-medium">{{ $row->reason }}</td>
                                <td class="py-3.5 px-6 text-right font-bold text-rose-400 mono-text">Rs. {{ number_format($row->refund_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-8 text-center text-slate-600">No returns found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-5 border-t border-slate-800 bg-slate-950/40 flex justify-end font-bold text-xs text-rose-400">
                    <span>Total Refund Payout: <strong class="text-rose-400 font-black ml-2 mono-text text-sm">Rs. {{ number_format($totReturn, 2) }}</strong></span>
                </div>

            @elseif($reportType === 'expenses')
                <!-- 11. Expenses Report -->
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-widest font-semibold text-[10px] bg-slate-950/20">
                            <th class="py-3 px-6">Expense Category</th>
                            <th class="py-3 px-6 text-center">Transactions Count</th>
                            <th class="py-3 px-6 text-right">Total Expense Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-850">
                        @php $totExp = 0; @endphp
                        @forelse($data as $row)
                            @php $totExp += $row->total_amount; @endphp
                            <tr class="hover:bg-slate-800/10 transition-colors">
                                <td class="py-3.5 px-6 font-bold text-slate-200">{{ $row->category }}</td>
                                <td class="py-3.5 px-6 text-center font-bold text-slate-350">{{ $row->count }}</td>
                                <td class="py-3.5 px-6 text-right font-bold text-rose-400 mono-text">Rs. {{ number_format($row->total_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-8 text-center text-slate-600">No expenses recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-5 border-t border-slate-800 bg-slate-950/40 flex justify-end font-bold text-xs text-rose-400">
                    <span>Total Overhead Expenses: <strong class="text-rose-450 font-black ml-2 mono-text text-sm">Rs. {{ number_format($totExp, 2) }}</strong></span>
                </div>

            @elseif($reportType === 'attendance')
                <!-- 12. Attendance Report -->
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-widest font-semibold text-[10px] bg-slate-950/20">
                            <th class="py-3 px-6">Employee Name</th>
                            <th class="py-3 px-6">Designation</th>
                            <th class="py-3 px-6 text-center text-emerald-400">Days Present</th>
                            <th class="py-3 px-6 text-center text-amber-400">Days Late</th>
                            <th class="py-3 px-6 text-center text-rose-450">Days Absent</th>
                            <th class="py-3 px-6 text-center font-bold">Attendance Rate</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-850">
                        @forelse($data as $row)
                            @php
                                $totalDays = $row->present_days + $row->late_days + $row->absent_days;
                                $rate = $totalDays > 0 ? (($row->present_days + $row->late_days) / $totalDays) * 100 : 0;
                            @endphp
                            <tr class="hover:bg-slate-800/10 transition-colors">
                                <td class="py-3.5 px-6 font-bold text-slate-200">{{ $row->employee ? $row->employee->name : 'Unknown Staff' }}</td>
                                <td class="py-3.5 px-6 text-slate-350 font-semibold">{{ $row->employee ? $row->employee->designation : 'N/A' }}</td>
                                <td class="py-3.5 px-6 text-center text-emerald-400 font-bold">{{ $row->present_days }}</td>
                                <td class="py-3.5 px-6 text-center text-amber-400 font-bold">{{ $row->late_days }}</td>
                                <td class="py-3.5 px-6 text-center text-rose-400 font-bold">{{ $row->absent_days }}</td>
                                <td class="py-3.5 px-6 text-center font-black {{ $rate >= 90 ? 'text-emerald-400' : ($rate >= 75 ? 'text-amber-400' : 'text-rose-400') }}">
                                    {{ number_format($rate, 1) }}%
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-8 text-center text-slate-600">No attendance logs available.</td></tr>
                        @endforelse
                    </tbody>
                </table>

            @elseif($reportType === 'salary')
                <!-- 13. Salary Report -->
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-widest font-semibold text-[10px] bg-slate-950/20">
                            <th class="py-3 px-6">Payslip No</th>
                            <th class="py-3 px-6">Employee Profile</th>
                            <th class="py-3 px-6">Designation</th>
                            <th class="py-3 px-6">Paid For Month</th>
                            <th class="py-3 px-6 text-center">Payment Date</th>
                            <th class="py-3 px-6">Payment Method</th>
                            <th class="py-3 px-6 text-right">Amount Paid</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-850">
                        @php $totSalary = 0; @endphp
                        @forelse($data as $row)
                            @php $totSalary += $row->amount_paid; @endphp
                            <tr class="hover:bg-slate-800/10 transition-colors">
                                <td class="py-3.5 px-6 font-bold text-cyan-400 font-mono">{{ $row->payslip_no }}</td>
                                <td class="py-3.5 px-6 font-bold text-slate-200">{{ $row->employee ? $row->employee->name : 'Unknown' }}</td>
                                <td class="py-3.5 px-6 text-slate-350 font-semibold">{{ $row->employee ? $row->employee->designation : 'N/A' }}</td>
                                <td class="py-3.5 px-6 text-slate-300 font-medium">{{ $row->paid_for_month }}</td>
                                <td class="py-3.5 px-6 text-center text-slate-450 font-mono">{{ $row->payment_date }}</td>
                                <td class="py-3.5 px-6 text-slate-400">{{ $row->payment_method }}</td>
                                <td class="py-3.5 px-6 text-right font-black text-slate-100 mono-text">Rs. {{ number_format($row->amount_paid, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-8 text-center text-slate-600">No salary payment records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-5 border-t border-slate-800 bg-slate-950/40 flex justify-end font-bold text-xs text-rose-400">
                    <span>Total Payroll Cost: <strong class="text-rose-450 font-black ml-2 mono-text text-sm">Rs. {{ number_format($totSalary, 2) }}</strong></span>
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function triggerPrint() {
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('print', '1');
    window.open(`${window.location.pathname}?${urlParams.toString()}`, '_blank');
}

function triggerDownload() {
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('download', '1');
    window.location.href = `${window.location.pathname}?${urlParams.toString()}`;
}

document.addEventListener('DOMContentLoaded', function () {
    const chartConfig = @json($chartData);
    
    if (chartConfig && chartConfig.labels && chartConfig.labels.length > 0) {
        const ctx = document.getElementById('reportsChart').getContext('2d');
        
        const options = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    labels: {
                        color: '#94a3b8',
                        font: { family: 'Orbitron', size: 10, weight: 'bold' }
                    }
                },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#f8fafc',
                    bodyColor: '#cbd5e1',
                    borderColor: '#334155',
                    borderWidth: 1,
                    titleFont: { family: 'Orbitron', size: 11 },
                    bodyFont: { family: 'JetBrains Mono', size: 11 }
                }
            }
        };
        
        if (chartConfig.type === 'line' || chartConfig.type === 'bar') {
            options.scales = {
                x: {
                    grid: { color: 'rgba(51, 65, 85, 0.1)' },
                    ticks: { color: '#94a3b8', font: { family: 'Inter', size: 9 } }
                },
                y: {
                    grid: { color: 'rgba(51, 65, 85, 0.1)' },
                    ticks: {
                        color: '#94a3b8',
                        font: { family: 'JetBrains Mono', size: 9 },
                        callback: function(value) {
                            if (chartConfig.datasets[0] && chartConfig.datasets[0].label.includes('%')) {
                                return value + '%';
                            }
                            return 'Rs. ' + value.toLocaleString();
                        }
                    }
                }
            };
            
            if (chartConfig.datasets.length > 1 && chartConfig.datasets[1].yAxisID === 'y1') {
                options.scales.y1 = {
                    position: 'right',
                    grid: { drawOnChartArea: false },
                    ticks: {
                        color: '#94a3b8',
                        font: { family: 'JetBrains Mono', size: 9 },
                        callback: function(value) {
                            return 'Rs. ' + value.toLocaleString();
                        }
                    }
                };
            }
        }

        chartConfig.datasets.forEach((ds, idx) => {
            if (chartConfig.type === 'line') {
                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                if (ds.borderColor === '#00e3fd') {
                    gradient.addColorStop(0, 'rgba(0, 227, 253, 0.25)');
                    gradient.addColorStop(1, 'rgba(0, 227, 253, 0.01)');
                    ds.pointBackgroundColor = '#00e3fd';
                } else if (ds.borderColor === '#f43f5e') {
                    gradient.addColorStop(0, 'rgba(244, 63, 94, 0.25)');
                    gradient.addColorStop(1, 'rgba(244, 63, 94, 0.01)');
                    ds.pointBackgroundColor = '#f43f5e';
                } else if (ds.borderColor === '#10b981') {
                    gradient.addColorStop(0, 'rgba(16, 185, 129, 0.25)');
                    gradient.addColorStop(1, 'rgba(16, 185, 129, 0.01)');
                    ds.pointBackgroundColor = '#10b981';
                } else {
                    gradient.addColorStop(0, 'rgba(168, 85, 247, 0.25)');
                    gradient.addColorStop(1, 'rgba(168, 85, 247, 0.01)');
                    ds.pointBackgroundColor = '#a855f7';
                }
                ds.backgroundColor = gradient;
                ds.borderWidth = 2;
                ds.fill = true;
                ds.tension = 0.35;
                ds.pointBorderColor = '#020617';
                ds.pointHoverRadius = 6;
            } else if (chartConfig.type === 'bar') {
                ds.borderRadius = 4;
                ds.borderWidth = 0;
            }
        });

        new Chart(ctx, {
            type: chartConfig.type,
            data: chartConfig,
            options: options
        });
    }
});
</script>
@endsection
