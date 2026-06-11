@extends('layouts.shop')

@section('title', 'Neuronet | Admin Mainframe')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    
    <!-- Dashboard Heading and Navigation -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
        <div>
            <h1 class="text-3xl font-extrabold font-orbitron text-white tracking-widest uppercase">
                SYSTEM MAINFRAME CONSOLE
            </h1>
            <div class="w-24 h-1 bg-cyber-cyan mt-2 shadow-neon-cyan"></div>
            <p class="text-xs text-gray-500 mt-2 uppercase font-orbitron tracking-widest">Access level: Administrator</p>
        </div>

        <!-- Admin Navigation Deck -->
        <div class="flex flex-wrap gap-3 font-orbitron text-xs font-bold">
            <a href="{{ route('admin.dashboard') }}" class="px-5 py-3 rounded-lg border border-cyber-cyan bg-cyber-cyan/15 text-cyber-cyan shadow-neon-cyan/20 transition duration-150">
                <i class="fa-solid fa-gauge mr-2"></i> DASHBOARD
            </a>
            <a href="{{ route('admin.products') }}" class="px-5 py-3 rounded-lg border border-cyber-border bg-cyber-card text-gray-400 hover:text-cyber-cyan hover:border-cyber-cyan/50 transition duration-150">
                <i class="fa-solid fa-boxes-stacked mr-2"></i> PRODUCTS CRUD
            </a>
            <a href="{{ route('admin.orders') }}" class="px-5 py-3 rounded-lg border border-cyber-border bg-cyber-card text-gray-400 hover:text-cyber-cyan hover:border-cyber-cyan/50 transition duration-150">
                <i class="fa-solid fa-file-invoice-dollar mr-2"></i> ORDERS
            </a>
        </div>
    </div>

    <!-- Analytics Dashboard Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <!-- Revenue -->
        <div class="bg-cyber-card/45 border border-cyber-border rounded-2xl p-6 relative overflow-hidden">
            <div class="absolute right-4 bottom-4 text-cyber-cyan/10 text-5xl"><i class="fa-solid fa-wallet"></i></div>
            <span class="text-xs font-orbitron font-bold text-gray-500 uppercase tracking-widest">TOTAL SALES REVENUE</span>
            <h3 class="text-3xl font-bold font-orbitron text-white mt-2">Rs. {{ number_format($totalSales, 2) }}</h3>
            <span class="text-[10px] text-emerald-400 font-orbitron mt-1 block"><i class="fa-solid fa-circle-nodes mr-1"></i> COMPLETED ORDERS ONLY</span>
        </div>

        <!-- Pending Orders -->
        <div class="bg-cyber-card/45 border border-cyber-border rounded-2xl p-6 relative overflow-hidden">
            <div class="absolute right-4 bottom-4 text-amber-500/10 text-5xl"><i class="fa-solid fa-spinner"></i></div>
            <span class="text-xs font-orbitron font-bold text-gray-500 uppercase tracking-widest">PENDING CONTRACTS</span>
            <h3 class="text-3xl font-bold font-orbitron text-amber-400 mt-2 shadow-neon-amber">{{ $pendingOrders }}</h3>
            <a href="{{ route('admin.orders') }}" class="text-[10px] text-cyber-cyan hover:underline font-orbitron mt-1 block">MANAGE ORDERS <i class="fa-solid fa-angle-right"></i></a>
        </div>

        <!-- Total Users -->
        <div class="bg-cyber-card/45 border border-cyber-border rounded-2xl p-6 relative overflow-hidden">
            <div class="absolute right-4 bottom-4 text-blue-500/10 text-5xl"><i class="fa-solid fa-users"></i></div>
            <span class="text-xs font-orbitron font-bold text-gray-500 uppercase tracking-widest">REGISTERED CLIENTS</span>
            <h3 class="text-3xl font-bold font-orbitron text-white mt-2">{{ $totalUsers }}</h3>
            <span class="text-[10px] text-gray-400 font-orbitron mt-1 block">TOTAL DATABASE USERS</span>
        </div>

        <!-- Out of Stock -->
        <div class="bg-cyber-card/45 border border-cyber-border rounded-2xl p-6 relative overflow-hidden">
            <div class="absolute right-4 bottom-4 text-rose-500/10 text-5xl"><i class="fa-solid fa-circle-exclamation"></i></div>
            <span class="text-xs font-orbitron font-bold text-gray-500 uppercase tracking-widest">OUT OF STOCK ITEMS</span>
            <h3 class="text-3xl font-bold font-orbitron text-red-500 mt-2">{{ $outOfStockCount }}</h3>
            <a href="{{ route('admin.products') }}" class="text-[10px] text-red-400 hover:underline font-orbitron mt-1 block">VIEW PRODUCTS <i class="fa-solid fa-angle-right"></i></a>
        </div>
    </div>

    <!-- Main Content Logs Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12">
        
        <!-- Recent Orders (Col span 7) -->
        <div class="lg:col-span-7 bg-cyber-card/45 border border-cyber-border rounded-2xl p-6 backdrop-blur-sm">
            <h3 class="font-orbitron font-bold text-white text-base tracking-wider border-b border-cyber-border/60 pb-4 flex items-center gap-2">
                <i class="fa-solid fa-receipt text-cyber-cyan"></i> RECENT CONTRACT TRANSMISSIONS
            </h3>
            
            @if($recentOrders->isEmpty())
                <p class="text-sm text-gray-500 py-8 text-center">No orders registered in system database.</p>
            @else
                <div class="divide-y divide-cyber-border/40">
                    @foreach($recentOrders as $order)
                        <div class="py-4 flex justify-between items-center gap-4">
                            <div>
                                <span class="text-xs font-orbitron font-bold text-cyber-cyan">{{ $order->order_number }}</span>
                                <span class="block text-sm text-white mt-0.5">{{ $order->customer_name }}</span>
                                <span class="text-[10px] text-gray-500 font-orbitron">{{ $order->created_at->format('Y-m-d H:i') }}</span>
                            </div>
                            
                            <div class="text-right">
                                <span class="block text-sm font-bold font-orbitron text-white">Rs. {{ number_format($order->total, 2) }}</span>
                                @if($order->status === 'pending')
                                    <span class="inline-block px-2 py-0.5 text-[9px] font-orbitron font-bold bg-amber-500/10 border border-amber-500/30 text-amber-500 rounded uppercase">PENDING</span>
                                @elseif($order->status === 'completed')
                                    <span class="inline-block px-2 py-0.5 text-[9px] font-orbitron font-bold bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 rounded uppercase">COMPLETED</span>
                                @elseif($order->status === 'cancelled')
                                    <span class="inline-block px-2 py-0.5 text-[9px] font-orbitron font-bold bg-rose-500/10 border border-rose-500/30 text-rose-500 rounded uppercase">CANCELLED</span>
                                @else
                                    <span class="inline-block px-2 py-0.5 text-[9px] font-orbitron font-bold bg-blue-500/10 border border-blue-500/30 text-blue-400 rounded uppercase">{{ $order->status }}</span>
                                @endif
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="text-cyber-cyan hover:underline text-xs block mt-1"><i class="fa-solid fa-eye mr-1"></i>Inspect</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Inventory Alerts / Low Stock (Col span 5) -->
        <div class="lg:col-span-5 bg-cyber-card/45 border border-cyber-border rounded-2xl p-6 backdrop-blur-sm">
            <h3 class="font-orbitron font-bold text-white text-base tracking-wider border-b border-cyber-border/60 pb-4 flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-amber-500"></i> CRITICAL INVENTORY WARNINGS
            </h3>

            @if($lowStockProducts->isEmpty())
                <p class="text-sm text-gray-500 py-8 text-center">All hardware decks are fully supplied.</p>
            @else
                <div class="divide-y divide-cyber-border/40">
                    @foreach($lowStockProducts as $prod)
                        <div class="py-4 flex justify-between items-center gap-4">
                            <div>
                                <span class="text-xs text-gray-500">{{ $prod->brand }}</span>
                                <span class="block text-sm font-bold text-white leading-snug line-clamp-1">{{ $prod->name }}</span>
                            </div>
                            
                            <div class="text-right flex-shrink-0">
                                <span class="px-2 py-1 text-xs font-orbitron font-bold rounded {{ $prod->stock === 0 ? 'bg-red-500/10 border border-red-500/30 text-red-500' : 'bg-amber-500/10 border border-amber-500/30 text-amber-500' }}">
                                    STOCK: {{ $prod->stock }}
                                </span>
                                <a href="{{ route('admin.products.edit', $prod->id) }}" class="text-cyber-cyan hover:underline text-xs block mt-1.5 font-orbitron">Restock <i class="fa-solid fa-pen-to-square"></i></a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    <!-- Category Breakdown Deck -->
    <div class="bg-cyber-card/45 border border-cyber-border rounded-2xl p-6 backdrop-blur-sm">
        <h3 class="font-orbitron font-bold text-white text-base tracking-wider border-b border-cyber-border/60 pb-4 mb-6">
            <i class="fa-solid fa-circle-nodes text-cyber-cyan mr-2"></i> HARDWARE DECK SEGMENTATION
        </h3>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-6">
            @foreach($categoriesCount as $cat)
                <div class="bg-cyber-dark/60 border border-cyber-border/80 rounded-xl p-4 text-center">
                    <span class="text-xs font-orbitron text-gray-400 font-bold block truncate">{{ $cat->name }}</span>
                    <strong class="text-2xl font-orbitron text-cyber-cyan mt-2 block">{{ $cat->products_count }}</strong>
                    <span class="text-[9px] text-gray-500 uppercase tracking-widest block mt-1">MODULES</span>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
