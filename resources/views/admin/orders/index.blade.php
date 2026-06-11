@extends('layouts.shop')

@section('title', 'Neuronet | Orders Management')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    
    <!-- Heading and Action -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
        <div>
            <h1 class="text-3xl font-extrabold font-orbitron text-white tracking-widest uppercase">
                CONTRACT ORDERS MAINBOARD
            </h1>
            <div class="w-24 h-1 bg-cyber-cyan mt-2 shadow-neon-cyan"></div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2.5 rounded-lg border border-cyber-border bg-cyber-card text-gray-400 hover:text-cyber-cyan hover:border-cyber-cyan/50 font-orbitron text-xs font-bold transition duration-150">
                <i class="fa-solid fa-angle-left mr-2"></i> BACK TO DASHBOARD
            </a>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-cyber-card/45 border border-cyber-border rounded-2xl overflow-hidden backdrop-blur-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-cyber-border bg-cyber-dark/65 text-xs font-orbitron font-bold text-gray-400">
                        <th class="p-4 uppercase tracking-wider">Order Number</th>
                        <th class="p-4 uppercase tracking-wider">Client Info</th>
                        <th class="p-4 uppercase tracking-wider">Date Logged</th>
                        <th class="p-4 uppercase tracking-wider text-right">Order Total</th>
                        <th class="p-4 uppercase tracking-wider text-center">Payment Route</th>
                        <th class="p-4 uppercase tracking-wider text-center">Status</th>
                        <th class="p-4 uppercase tracking-wider text-center">Inspect</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cyber-border/40 text-sm text-gray-300">
                    @forelse($orders as $order)
                        <tr class="hover:bg-cyber-dark/30 transition duration-150">
                            <!-- Order Number -->
                            <td class="p-4 font-bold font-orbitron text-cyber-cyan">
                                {{ $order->order_number }}
                            </td>

                            <!-- Client Info -->
                            <td class="p-4">
                                <strong class="text-white block">{{ $order->customer_name }}</strong>
                                <span class="text-xs text-gray-500 font-mono">{{ $order->customer_email }}</span>
                            </td>

                            <!-- Date Logged -->
                            <td class="p-4 font-mono text-xs">
                                {{ $order->created_at->format('Y-m-d H:i:s') }}
                            </td>

                            <!-- Total -->
                            <td class="p-4 font-bold font-orbitron text-white text-right">
                                Rs. {{ number_format($order->total, 2) }}
                            </td>

                            <!-- Payment -->
                            <td class="p-4 text-center font-semibold text-xs uppercase font-orbitron text-gray-300">
                                {{ $order->payment_method === 'cod' ? 'Cash On Delivery' : 'Bank Transfer' }}
                            </td>

                            <!-- Status Badge -->
                            <td class="p-4 text-center">
                                @if($order->status === 'pending')
                                    <span class="px-2.5 py-1 text-xs font-orbitron font-bold bg-amber-500/10 border border-amber-500/35 text-amber-500 rounded-md">PENDING</span>
                                @elseif($order->status === 'processing')
                                    <span class="px-2.5 py-1 text-xs font-orbitron font-bold bg-blue-500/10 border border-blue-500/35 text-blue-400 rounded-md">PROCESSING</span>
                                @elseif($order->status === 'shipped')
                                    <span class="px-2.5 py-1 text-xs font-orbitron font-bold bg-purple-500/10 border border-purple-500/35 text-purple-400 rounded-md">SHIPPED</span>
                                @elseif($order->status === 'completed')
                                    <span class="px-2.5 py-1 text-xs font-orbitron font-bold bg-emerald-500/10 border border-emerald-500/35 text-emerald-400 rounded-md shadow-neon-emerald">COMPLETED</span>
                                @elseif($order->status === 'cancelled')
                                    <span class="px-2.5 py-1 text-xs font-orbitron font-bold bg-rose-500/10 border border-rose-500/35 text-rose-500 rounded-md">CANCELLED</span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-orbitron font-bold bg-gray-500/10 border border-gray-500/35 text-gray-400 rounded-md">{{ strtoupper($order->status) }}</span>
                                @endif
                            </td>

                            <!-- Action -->
                            <td class="p-4 text-center">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 border border-cyber-border hover:border-cyber-cyan hover:text-cyber-cyan rounded-lg text-gray-400 transition duration-150 text-xs font-orbitron font-semibold">
                                    <i class="fa-solid fa-folder-open text-xs"></i> Inspect
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-500">
                                No order logs registered in database.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        @if($orders->hasPages())
            <div class="p-4 border-t border-cyber-border bg-cyber-dark/40">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
