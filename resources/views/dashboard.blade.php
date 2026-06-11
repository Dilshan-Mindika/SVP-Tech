@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Top Cockpit Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="orbitron-title text-2xl font-black text-slate-100 tracking-wider">DASHBOARD</h1>
            <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest font-semibold">Real-time system status and financial overview</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('invoices.create') }}" class="px-4 py-2 bg-cyan-500 text-slate-950 font-bold rounded-lg text-sm transition-all hover:bg-cyan-400 shadow-neon-cyan hover:shadow-neon-cyan-lg flex items-center gap-2">
                <i class="fa-solid fa-plus"></i>
                <span>NEW SALE</span>
            </a>
        </div>
    </div>

    <!-- Financial & Operational KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Daily Revenue Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 relative overflow-hidden transition-all hover:border-slate-700/80 group">
            <div class="absolute -right-6 -bottom-6 text-7xl transition-transform group-hover:scale-110" style="color: #22d3ee; opacity: 0.15;">
                <i class="fa-solid fa-cash-register"></i>
            </div>
            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest block">Daily Sales</span>
            <h2 class="text-2xl font-extrabold text-cyan-400 mt-2 mono-text font-black">Rs. {{ number_format($dailyRevenue, 2) }}</h2>
            <span class="text-[10px] text-emerald-400 font-semibold flex items-center gap-1 mt-2">
                <i class="fa-solid fa-chart-line"></i>
                <span>Calculated from active register logs</span>
            </span>
        </div>

        <!-- Monthly Revenue Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 relative overflow-hidden transition-all hover:border-slate-700/80 group">
            <div class="absolute -right-6 -bottom-6 text-7xl transition-transform group-hover:scale-110" style="color: #94a3b8; opacity: 0.15;">
                <i class="fa-solid fa-money-bill-trend-up"></i>
            </div>
            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest block">Monthly Sales</span>
            <h2 class="text-2xl font-extrabold text-slate-100 mt-2 mono-text">Rs. {{ number_format($monthlyRevenue, 2) }}</h2>
            <span class="text-[10px] text-slate-400 font-semibold flex items-center gap-1 mt-2">
                <span>Month: {{ date('F Y') }}</span>
            </span>
        </div>

        <!-- Monthly Expenses Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 relative overflow-hidden transition-all hover:border-slate-700/80 group">
            <div class="absolute -right-6 -bottom-6 text-7xl transition-transform group-hover:scale-110" style="color: #f43f5e; opacity: 0.15;">
                <i class="fa-solid fa-money-bill-transfer"></i>
            </div>
            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest block">Monthly Expenses</span>
            <h2 class="text-2xl font-extrabold text-rose-500 mt-2 mono-text">Rs. {{ number_format($totalMonthlyExpenses, 2) }}</h2>
            <span class="text-[10px] text-rose-400/80 font-semibold flex items-center gap-1 mt-2">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>Includes salary payments & utilities</span>
            </span>
        </div>

        <!-- Net Profit Card (Glow Accent) -->
        <div class="bg-slate-900 border {{ $netProfit >= 0 ? 'border-emerald-500/30' : 'border-rose-500/30' }} rounded-xl p-5 relative overflow-hidden transition-all hover:border-slate-700 group shadow-[0_0_15px_rgba(16,185,129,0.05)]">
            <div class="absolute -right-6 -bottom-6 text-7xl transition-transform group-hover:scale-110" style="color: #34d399; opacity: 0.15;">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest block">Net Profit</span>
            <h2 class="text-2xl font-extrabold {{ $netProfit >= 0 ? 'text-emerald-400' : 'text-rose-500' }} mt-2 mono-text font-black">
                Rs. {{ number_format($netProfit, 2) }}
            </h2>
            <span class="text-[10px] {{ $netProfit >= 0 ? 'text-emerald-400' : 'text-rose-400' }} font-semibold flex items-center gap-1 mt-2">
                <i class="fa-solid fa-scale-balanced"></i>
                <span>Revenue minus Expenses and COGS</span>
            </span>
        </div>
    </div>

    <!-- Active Operational State -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Active Repairs -->
        <a href="{{ route('repairs.index') }}" class="bg-slate-900/60 border border-slate-800 hover:border-cyan-500/40 p-4 rounded-xl flex items-center justify-between transition-all group">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-lg bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-lg transition-transform group-hover:scale-110">
                    <i class="fa-solid fa-screwdriver-wrench"></i>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block font-semibold">Active Repair Jobs</span>
                    <span class="text-sm font-bold block text-slate-200 mt-0.5">{{ $activeRepairsCount }} units</span>
                </div>
            </div>
            <i class="fa-solid fa-chevron-right text-slate-600 group-hover:text-slate-400 text-xs transition-colors"></i>
        </a>

        <!-- Pending Warranties -->
        <a href="{{ route('warranty.index') }}" class="bg-slate-900/60 border border-slate-800 hover:border-amber-500/40 p-4 rounded-xl flex items-center justify-between transition-all group">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center text-lg transition-transform group-hover:scale-110">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block font-semibold">Pending Warranty Claims</span>
                    <span class="text-sm font-bold block text-slate-200 mt-0.5">{{ $pendingWarrantiesCount }} claims</span>
                </div>
            </div>
            <i class="fa-solid fa-chevron-right text-slate-600 group-hover:text-slate-400 text-xs transition-colors"></i>
        </a>

        <!-- Low Stock Alerts -->
        <a href="{{ route('products.index', ['stock_filter' => 'low']) }}" class="bg-slate-900/60 border border-slate-800 hover:border-rose-500/40 p-4 rounded-xl flex items-center justify-between transition-all group">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-lg bg-rose-500/10 text-rose-500 flex items-center justify-center text-lg transition-transform group-hover:scale-110">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block font-semibold">Low Stock Products</span>
                    <span class="text-sm font-bold block text-slate-200 mt-0.5">{{ $lowStockCount }} items</span>
                </div>
            </div>
            <i class="fa-solid fa-chevron-right text-slate-600 group-hover:text-slate-400 text-xs transition-colors"></i>
        </a>

        <!-- Upcoming Appointments -->
        <a href="{{ route('appointments.index') }}" class="bg-slate-900/60 border border-slate-800 hover:border-emerald-500/40 p-4 rounded-xl flex items-center justify-between transition-all group">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-lg transition-transform group-hover:scale-110">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block font-semibold">Scheduled Today</span>
                    <span class="text-sm font-bold block text-slate-200 mt-0.5">{{ $upcomingAppointmentsCount }} bookings</span>
                </div>
            </div>
            <i class="fa-solid fa-chevron-right text-slate-600 group-hover:text-slate-400 text-xs transition-colors"></i>
        </a>
    </div>

    <!-- Financial Trends Chart Deck -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 space-y-4 shadow-[0_0_20px_rgba(0,0,0,0.2)]">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-3 border-b border-slate-800">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-chart-line text-cyan-400"></i>
                <h3 class="orbitron-title text-sm font-black text-slate-200 uppercase tracking-widest">Financial Node Trends</h3>
            </div>
            <!-- Chart Filters -->
            <div class="flex bg-slate-950 p-1 border border-slate-800 rounded-lg gap-1">
                <button onclick="updateChartFilter('daily')" id="btn-daily" class="chart-tab-btn px-3 py-1 text-[10px] font-bold uppercase tracking-wider rounded-md transition-all bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">Daily</button>
                <button onclick="updateChartFilter('weekly')" id="btn-weekly" class="chart-tab-btn px-3 py-1 text-[10px] font-bold uppercase tracking-wider rounded-md transition-all text-slate-400 hover:text-slate-200">Weekly</button>
                <button onclick="updateChartFilter('monthly')" id="btn-monthly" class="chart-tab-btn px-3 py-1 text-[10px] font-bold uppercase tracking-wider rounded-md transition-all text-slate-400 hover:text-slate-200">Monthly</button>
                <button onclick="updateChartFilter('annually')" id="btn-annually" class="chart-tab-btn px-3 py-1 text-[10px] font-bold uppercase tracking-wider rounded-md transition-all text-slate-400 hover:text-slate-200">Annually</button>
            </div>
        </div>

        <div class="relative h-[280px] w-full">
            <canvas id="financialChart"></canvas>
        </div>
    </div>

    <!-- Alerts, Logs and Recent Activity Lists -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Live System Alerts Deck (2/3 width on wide screens) -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-bell-on text-cyan-400 animate-pulse"></i>
                    <h3 class="orbitron-title text-sm font-black text-slate-200 uppercase tracking-widest">System Notifications</h3>
                </div>
                <span class="text-[10px] bg-cyan-500/15 text-cyan-400 font-bold px-2 py-0.5 rounded-full border border-cyan-500/20 uppercase tracking-wider">Real-time Check</span>
            </div>

            <div class="space-y-2 max-h-[350px] overflow-y-auto pr-1">
                @forelse($alerts as $alert)
                    <div class="p-3.5 rounded-lg border flex items-start justify-between gap-4 transition-all hover:bg-slate-800/20
                        @if($alert['type'] == 'danger') border-rose-500/25 bg-rose-500/5 text-rose-400
                        @elseif($alert['type'] == 'warning') border-amber-500/25 bg-amber-500/5 text-amber-400
                        @elseif($alert['type'] == 'success') border-emerald-500/25 bg-emerald-500/5 text-emerald-400
                        @else border-cyan-500/25 bg-cyan-500/5 text-cyan-400 @endif">
                        <div class="flex gap-3">
                            <span class="mt-1 font-bold text-sm shrink-0">
                                @if($alert['type'] == 'danger') <i class="fa-solid fa-circle-radiation"></i>
                                @elseif($alert['type'] == 'warning') <i class="fa-solid fa-triangle-exclamation"></i>
                                @elseif($alert['type'] == 'success') <i class="fa-solid fa-calendar-check"></i>
                                @else <i class="fa-solid fa-circle-info"></i> @endif
                            </span>
                            <div>
                                <span class="text-[10px] uppercase font-bold tracking-widest block opacity-70">{{ $alert['module'] }}</span>
                                <p class="text-xs font-semibold mt-0.5 leading-relaxed">{{ $alert['message'] }}</p>
                            </div>
                        </div>
                        <span class="text-[9px] uppercase tracking-wider font-bold opacity-60 mt-1 shrink-0">{{ $alert['time'] }}</span>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-500 text-xs border border-dashed border-slate-800 rounded-xl">
                        <i class="fa-solid fa-shield-heart text-2xl mb-2 block opacity-40"></i>
                        <span>System core is nominal. No active warnings detected.</span>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- System Activity Log (1/3 width) -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 space-y-4">
            <div class="pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-slate-400"></i>
                    <h3 class="orbitron-title text-sm font-black text-slate-200 uppercase tracking-widest">Recent Sales</h3>
                </div>
            </div>

            <div class="space-y-3">
                @forelse($recentInvoices as $inv)
                    <div class="p-3 bg-slate-950 border border-slate-850 rounded-lg hover:border-slate-700 transition-colors flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-200 block">{{ $inv->invoice_number }}</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">
                                {{ $inv->customer ? $inv->customer->name : 'Walk-in Customer' }}
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold text-cyan-400 block mono-text">Rs. {{ number_format($inv->total, 2) }}</span>
                            <span class="text-[8px] bg-slate-800 text-slate-300 font-bold px-1.5 py-0.5 rounded uppercase tracking-wider mt-0.5 inline-block">
                                {{ $inv->payment_method }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-600 text-xs">
                        <span>No transactions recorded yet.</span>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Active Hardware Services & Repair Stations -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-microchip text-cyan-400"></i>
                <h3 class="orbitron-title text-sm font-black text-slate-200 uppercase tracking-widest">Recent Repairs</h3>
            </div>
            <a href="{{ route('repairs.index') }}" class="text-xs text-cyan-400 hover:text-cyan-300 font-bold tracking-wider uppercase transition-colors">View All Repairs &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-widest font-semibold text-[10px]">
                        <th class="py-3 px-4">Job Number</th>
                        <th class="py-3 px-4">Customer</th>
                        <th class="py-3 px-4">Device</th>
                        <th class="py-3 px-4">Technician</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Estimate Cost</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-850">
                    @forelse($recentRepairs as $rep)
                        <tr class="hover:bg-slate-800/10 transition-colors">
                            <td class="py-3 px-4 font-bold text-cyan-400">{{ $rep->repair_job_no }}</td>
                            <td class="py-3 px-4 font-semibold text-slate-200">{{ $rep->customer_name }}</td>
                            <td class="py-3 px-4 text-slate-300 font-semibold">{{ $rep->device_model }}</td>
                            <td class="py-3 px-4 text-slate-400 font-medium">
                                {{ $rep->technician ? $rep->technician->name : 'Pending Assignment' }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold
                                    @if($rep->status === 'delivered') bg-emerald-500/10 text-emerald-400 border border-emerald-500/20
                                    @elseif($rep->status === 'ready') bg-cyan-500/10 text-cyan-400 border border-cyan-500/20
                                    @elseif($rep->status === 'repairing') bg-amber-500/10 text-amber-400 border border-amber-500/20
                                    @else bg-slate-800 text-slate-400 border border-slate-700 @endif">
                                    {{ $rep->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right text-slate-200 font-bold mono-text">Rs. {{ number_format($rep->estimate_cost, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-650">
                                <span>No repair jobs logged at this station.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ChartJS CDN & Dynamic Config -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartData = @json($chartData);
        const ctx = document.getElementById('financialChart').getContext('2d');
        
        // Create nice neon gradients
        const revGradient = ctx.createLinearGradient(0, 0, 0, 250);
        revGradient.addColorStop(0, 'rgba(0, 227, 253, 0.25)');
        revGradient.addColorStop(1, 'rgba(0, 227, 253, 0.01)');
        
        const expGradient = ctx.createLinearGradient(0, 0, 0, 250);
        expGradient.addColorStop(0, 'rgba(244, 63, 94, 0.25)');
        expGradient.addColorStop(1, 'rgba(244, 63, 94, 0.01)');

        let currentFilter = 'daily';

        const myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.daily.labels,
                datasets: [
                    {
                        label: 'REVENUE',
                        data: chartData.daily.revenue,
                        borderColor: '#00e3fd',
                        backgroundColor: revGradient,
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3,
                        pointBackgroundColor: '#00e3fd',
                        pointBorderColor: '#020617',
                        pointHoverRadius: 6
                    },
                    {
                        label: 'EXPENSES',
                        data: chartData.daily.expenses,
                        borderColor: '#f43f5e',
                        backgroundColor: expGradient,
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3,
                        pointBackgroundColor: '#f43f5e',
                        pointBorderColor: '#020617',
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            color: '#94a3b8',
                            font: {
                                family: 'Orbitron',
                                size: 10,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: '#0f172a',
                        titleColor: '#f8fafc',
                        bodyColor: '#cbd5e1',
                        borderColor: '#334155',
                        borderWidth: 1,
                        titleFont: { family: 'Orbitron', size: 11 },
                        bodyFont: { family: 'JetBrains Mono', size: 11 }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: 'rgba(51, 65, 85, 0.2)'
                        },
                        ticks: {
                            color: '#94a3b8',
                            font: { family: 'Inter', size: 10 }
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(51, 65, 85, 0.2)'
                        },
                        ticks: {
                            color: '#94a3b8',
                            font: { family: 'JetBrains Mono', size: 10 },
                            callback: function(value) {
                                return 'Rs. ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        window.updateChartFilter = function(filter) {
            currentFilter = filter;
            
            // Update active tab styling
            document.querySelectorAll('.chart-tab-btn').forEach(btn => {
                btn.className = 'chart-tab-btn px-3 py-1 text-[10px] font-bold uppercase tracking-wider rounded-md transition-all text-slate-400 hover:text-slate-200';
            });
            const activeBtn = document.getElementById(`btn-${filter}`);
            activeBtn.className = 'chart-tab-btn px-3 py-1 text-[10px] font-bold uppercase tracking-wider rounded-md transition-all bg-cyan-500/10 text-cyan-400 border border-cyan-500/20';

            // Update chart data
            myChart.data.labels = chartData[filter].labels;
            myChart.data.datasets[0].data = chartData[filter].revenue;
            myChart.data.datasets[1].data = chartData[filter].expenses;
            myChart.update();
        }
    });
</script>
@endsection
