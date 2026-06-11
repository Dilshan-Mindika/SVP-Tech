@extends('layouts.shop')

@section('title', 'Neuronet | Order Success')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="bg-cyber-card/45 border border-cyber-border rounded-3xl p-8 md:p-12 backdrop-blur-sm relative overflow-hidden text-center space-y-8">
        
        <!-- Background Neon Glow -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-72 h-72 bg-cyber-cyan/5 rounded-full blur-[80px] pointer-events-none"></div>

        <!-- Success Animation/Icon -->
        <div class="relative flex justify-center">
            <div class="w-20 h-20 rounded-full bg-cyber-cyan/10 border border-cyber-cyan flex items-center justify-center text-cyber-cyan shadow-neon-cyan/20 animate-bounce">
                <i class="fa-solid fa-check text-4xl"></i>
            </div>
        </div>

        <div class="space-y-3">
            <h1 class="text-3xl font-extrabold font-orbitron text-white tracking-widest uppercase">
                TRANSMISSION SUCCESSFUL
            </h1>
            <p class="text-gray-400 text-sm max-w-md mx-auto">
                Your order contract has been received and queued into the Neuronet fulfillment mainframe.
            </p>
        </div>

        <!-- Order Summary Block -->
        <div class="bg-cyber-dark/60 border border-cyber-border/80 rounded-2xl p-6 text-left max-w-xl mx-auto space-y-4">
            <div class="flex justify-between items-center pb-3 border-b border-cyber-border/40 text-xs font-orbitron font-bold">
                <span class="text-gray-500">ORDER NUMBER</span>
                <span class="text-cyber-cyan">{{ $order->order_number }}</span>
            </div>
            
            <div class="grid grid-cols-2 gap-4 text-sm text-gray-400">
                <div>
                    <span class="block text-xs font-orbitron font-semibold text-gray-600">DELIVER TO</span>
                    <strong class="text-white">{{ $order->customer_name }}</strong>
                </div>
                <div>
                    <span class="block text-xs font-orbitron font-semibold text-gray-600">PAYMENT ROUTE</span>
                    <strong class="text-white uppercase">{{ $order->payment_method === 'cod' ? 'Cash on Delivery' : 'Bank Transfer' }}</strong>
                </div>
            </div>

            <div class="pt-2">
                <span class="block text-xs font-orbitron font-semibold text-gray-600">SHIPPING LOCATION</span>
                <p class="text-sm text-gray-300">{{ $order->shipping_address }}</p>
            </div>

            <div class="flex justify-between items-center pt-3 border-t border-cyber-border/40">
                <span class="text-sm font-bold text-white font-orbitron">CONTRACT TOTAL</span>
                <span class="text-xl font-bold font-orbitron text-cyber-cyan drop-shadow-[0_0_8px_rgba(0,227,253,0.3)]">
                    Rs. {{ number_format($order->total, 2) }}
                </span>
            </div>
        </div>

        <!-- Dynamic Payment Instructions -->
        <div class="max-w-xl mx-auto p-6 bg-cyber-cyan/5 border border-cyber-cyan/20 rounded-2xl text-left space-y-4">
            <h4 class="font-orbitron font-bold text-cyber-cyan text-sm tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-circle-info"></i> PROCESSING INSTRUCTIONS
            </h4>
            
            @if($order->payment_method === 'bank_transfer')
                <div class="text-sm text-gray-300 space-y-3">
                    <p>To finalize shipment preparation, please transfer the exact contract amount to our secure bank account:</p>
                    <div class="bg-cyber-dark/80 p-4 rounded-xl space-y-2 border border-cyber-border font-mono text-xs">
                        <div><span class="text-gray-500">Bank:</span> <strong class="text-white">Neuronet Cyberbank Corp</strong></div>
                        <div><span class="text-gray-500">Account No:</span> <strong class="text-cyber-cyan">0090-4829-1092-293</strong></div>
                        <div><span class="text-gray-500">Branch:</span> <strong class="text-white">Neo-Colombo Prime Branch</strong></div>
                        <div><span class="text-gray-500">Ref Code:</span> <strong class="text-cyber-cyan">{{ $order->order_number }}</strong></div>
                    </div>
                    <p class="text-xs text-gray-400">
                        *Please include the Ref Code above in your transfer description. Send proof of transfer receipt to <a href="mailto:sales@neuronet.com" class="text-cyber-cyan hover:underline">sales@neuronet.com</a>.
                    </p>
                </div>
            @else
                <p class="text-sm text-gray-300">
                    Your order is queued as Cash on Delivery. Please prepare the sum of <strong class="text-cyber-cyan font-orbitron">Rs. {{ number_format($order->total, 2) }}</strong> to hand over to the delivery agent. Our dispatch terminal will contact you at <strong class="text-white">{{ $order->customer_phone }}</strong> to confirm routing.
                </p>
            @endif
        </div>

        <!-- Navigation Buttons -->
        <div class="flex flex-wrap items-center justify-center gap-4 pt-6">
            <a href="{{ route('catalog') }}" class="px-8 py-4 bg-cyber-cyan hover:bg-cyber-cyan/80 text-cyber-dark font-bold font-orbitron rounded-xl transition duration-300 shadow-neon-cyan hover:shadow-neon-cyan-lg">
                DECK OUT MORE HARDWARE <i class="fa-solid fa-basket-shopping ml-2"></i>
            </a>
            @auth
                <a href="{{ route('profile.edit') }}" class="px-8 py-4 border border-cyber-border hover:border-cyber-cyan/50 hover:bg-cyber-cyan/5 text-gray-300 hover:text-cyber-cyan font-bold font-orbitron rounded-xl transition duration-300">
                    VIEW CONTRACT STATUS
                </a>
            @endauth
        </div>

    </div>
</div>
@endsection
