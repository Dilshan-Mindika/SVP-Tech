@extends('layouts.shop')

@section('title', 'CloudTech | Inspect Order')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    
    <!-- Heading and Action -->
    <div class="flex justify-between items-center gap-6 mb-10">
        <div>
            <h1 class="text-3xl font-extrabold font-orbitron text-white tracking-widest uppercase">
                INSPECT ORDER PROTOCOL
            </h1>
            <div class="w-24 h-1 bg-cyber-cyan mt-2 shadow-neon-cyan"></div>
            <p class="text-xs text-gray-500 mt-2 font-mono">ORDER: {{ $order->order_number }}</p>
        </div>

        <a href="{{ route('admin.orders') }}" class="px-4 py-2.5 rounded-lg border border-cyber-border bg-cyber-card text-gray-400 hover:text-cyber-cyan hover:border-cyber-cyan/50 font-orbitron text-xs font-bold transition duration-150">
            <i class="fa-solid fa-angle-left mr-2"></i> CONTRACT DECK
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Order Details Console (Col span 8) -->
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-cyber-card/45 border border-cyber-border rounded-2xl p-6 md:p-8 space-y-6 backdrop-blur-sm">
                
                <h3 class="font-orbitron font-bold text-white text-base tracking-wider border-b border-cyber-border/60 pb-4">
                    HARDWARE DECK COMPONENT BUNDLE
                </h3>

                <!-- Items Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-cyber-border/40 text-xs font-orbitron font-bold text-gray-500">
                                <th class="pb-3 uppercase tracking-wider">Module Name</th>
                                <th class="pb-3 uppercase tracking-wider text-right">Unit Price</th>
                                <th class="pb-3 uppercase tracking-wider text-center">Qty</th>
                                <th class="pb-3 uppercase tracking-wider text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-cyber-border/20 text-sm text-gray-300">
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="py-4">
                                        <span class="text-[9px] font-orbitron font-bold text-cyber-cyan tracking-wider uppercase bg-cyber-cyan/5 border border-cyber-cyan/25 px-1.5 py-0.5 rounded mr-2">
                                            {{ $item->product->brand ?? 'N/A' }}
                                        </span>
                                        @if($item->product)
                                            <a href="{{ route('products.show', $item->product->slug) }}" class="font-bold text-white hover:text-cyber-cyan transition duration-150">
                                                {{ $item->product->name }}
                                            </a>
                                        @else
                                            <span class="text-gray-500 font-bold">Unknown Product (Deleted)</span>
                                        @endif
                                    </td>
                                    <td class="py-4 font-orbitron text-right text-gray-400">
                                        Rs. {{ number_format($item->price, 2) }}
                                    </td>
                                    <td class="py-4 font-orbitron text-center text-white">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="py-4 font-orbitron text-right font-bold text-white">
                                        Rs. {{ number_format($item->price * $item->quantity, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Totals -->
                <div class="border-t border-cyber-border/60 pt-6 flex justify-between items-center">
                    <span class="font-orbitron font-bold text-white">CONTRACT BILLING TOTAL</span>
                    <span class="text-2xl font-bold font-orbitron text-cyber-cyan drop-shadow-[0_0_8px_rgba(0,227,253,0.3)]">
                        Rs. {{ number_format($order->total, 2) }}
                    </span>
                </div>
            </div>

            <!-- Shipping Info Box -->
            <div class="bg-cyber-card/45 border border-cyber-border rounded-2xl p-6 md:p-8 space-y-4 backdrop-blur-sm">
                <h3 class="font-orbitron font-bold text-white text-base tracking-wider border-b border-cyber-border/60 pb-4">
                    CLIENT ROUTING PARAMETERS
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div class="space-y-3">
                        <div>
                            <span class="block text-xs font-orbitron font-semibold text-gray-500">CLIENT NAME</span>
                            <strong class="text-white">{{ $order->customer_name }}</strong>
                        </div>
                        <div>
                            <span class="block text-xs font-orbitron font-semibold text-gray-500">EMAIL REGISTER</span>
                            <span class="text-gray-300 font-mono">{{ $order->customer_email }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-orbitron font-semibold text-gray-500">PHONE COMMUNICATOR</span>
                            <span class="text-gray-300 font-mono">{{ $order->customer_phone }}</span>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <span class="block text-xs font-orbitron font-semibold text-gray-500">SHIPPING DESTINATION</span>
                            <p class="text-gray-300 leading-relaxed">{{ $order->shipping_address }}</p>
                        </div>
                        <div>
                            <span class="block text-xs font-orbitron font-semibold text-gray-500">PAYMENT ROUTING PROTOCOL</span>
                            <strong class="text-white uppercase font-orbitron text-xs bg-cyber-dark px-2.5 py-1 border border-cyber-border rounded-md inline-block mt-1">
                                {{ $order->payment_method === 'cod' ? 'Cash on Delivery' : 'Bank Transfer' }}
                            </strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Management Sideboard (Col span 4) -->
        <div class="lg:col-span-4 bg-cyber-card/65 border border-cyber-border rounded-2xl p-6 space-y-6 backdrop-blur-sm relative">
            <!-- Subtle glow -->
            <div class="absolute -inset-px border border-cyber-cyan/20 rounded-2xl pointer-events-none"></div>

            <h3 class="font-orbitron font-bold text-white text-base tracking-wider border-b border-cyber-border/60 pb-4">
                STATUS MAINFRAME ROUTING
            </h3>

            <!-- Current status badge display -->
            <div class="p-4 bg-cyber-dark border border-cyber-border rounded-xl text-center">
                <span class="block text-xs font-orbitron font-semibold text-gray-500 mb-2">CURRENT STATE</span>
                
                @if($order->status === 'pending')
                    <span class="inline-block px-3 py-1.5 text-sm font-orbitron font-bold bg-amber-500/10 border border-amber-500/35 text-amber-500 rounded-lg">PENDING</span>
                @elseif($order->status === 'processing')
                    <span class="inline-block px-3 py-1.5 text-sm font-orbitron font-bold bg-blue-500/10 border border-blue-500/35 text-blue-400 rounded-lg">PROCESSING</span>
                @elseif($order->status === 'shipped')
                    <span class="inline-block px-3 py-1.5 text-sm font-orbitron font-bold bg-purple-500/10 border border-purple-500/35 text-purple-400 rounded-lg">SHIPPED</span>
                @elseif($order->status === 'completed')
                    <span class="inline-block px-3 py-1.5 text-sm font-orbitron font-bold bg-emerald-500/10 border border-emerald-500/35 text-emerald-400 rounded-lg shadow-neon-emerald">COMPLETED</span>
                @elseif($order->status === 'cancelled')
                    <span class="inline-block px-3 py-1.5 text-sm font-orbitron font-bold bg-rose-500/10 border border-rose-500/35 text-rose-500 rounded-lg">CANCELLED</span>
                @endif
            </div>

            <!-- Status Update Form -->
            <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="space-y-4">
                @csrf
                <div class="space-y-2">
                    <label for="status" class="block text-xs font-orbitron font-bold text-gray-400">UPDATE ROUTING STATE</label>
                    <select name="status" id="status" class="w-full bg-cyber-dark border border-cyber-border focus:border-cyber-cyan focus:ring-1 focus:ring-cyber-cyan text-gray-300 rounded-xl py-3 px-4 transition duration-200">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <button type="submit" class="w-full py-3 bg-cyber-cyan hover:bg-cyber-cyan/80 text-cyber-dark font-extrabold font-orbitron rounded-xl transition duration-300 shadow-neon-cyan hover:shadow-neon-cyan-lg">
                    COMMIT STATE UPDATE <i class="fa-solid fa-cloud-arrow-up ml-1"></i>
                </button>
            </form>

            <hr class="border-cyber-border/40">

            <div class="text-xs text-gray-500 space-y-2 leading-relaxed uppercase font-mono">
                <p>Status parameters definition:</p>
                <p><strong class="text-amber-500">PENDING:</strong> Order received, waiting check/payment confirmation.</p>
                <p><strong class="text-blue-400">PROCESSING:</strong> Components pick and packaging phase.</p>
                <p><strong class="text-purple-400">SHIPPED:</strong> ESD-safe crate handed to tracked dispatch courier.</p>
                <p><strong class="text-emerald-400">COMPLETED:</strong> Crate delivered to coordinates, payment resolved.</p>
                <p><strong class="text-rose-500">CANCELLED:</strong> Transmission aborted.</p>
            </div>
        </div>

    </div>
</div>
@endsection
