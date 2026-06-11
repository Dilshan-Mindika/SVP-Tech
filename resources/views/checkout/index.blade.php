@extends('layouts.shop')

@section('title', 'CloudTech | Secure Checkout')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Breadcrumb -->
    <nav class="flex mb-8 text-xs font-orbitron font-semibold text-gray-500 gap-2">
        <a href="{{ route('home') }}" class="hover:text-cyber-cyan transition duration-150">HOME</a>
        <span>/</span>
        <a href="{{ route('cart.index') }}" class="hover:text-cyber-cyan transition duration-150">CART</a>
        <span>/</span>
        <span class="text-cyber-cyan">CHECKOUT</span>
    </nav>

    <h1 class="text-3xl font-extrabold font-orbitron text-white tracking-widest mb-10">
        SECURE CHECKOUT TERMINAL
        <span class="block w-20 h-1 bg-cyber-cyan mt-2 shadow-neon-cyan"></span>
    </h1>

    <form action="{{ route('checkout.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        @csrf

        <!-- Checkout Form details (Col span 7) -->
        <div class="lg:col-span-7 bg-cyber-card/45 border border-cyber-border rounded-2xl p-6 md:p-8 space-y-6 backdrop-blur-sm">
            <h3 class="font-orbitron font-bold text-white text-lg tracking-wider border-b border-cyber-border/60 pb-4 flex items-center gap-2">
                <i class="fa-solid fa-user-shield text-cyber-cyan"></i> DELIVERY & CONTRACT DETAILS
            </h3>

            <!-- Customer Name -->
            <div class="space-y-2">
                <label for="customer_name" class="block text-xs font-orbitron font-bold text-gray-400">CUSTOMER FULL NAME</label>
                <input type="text" name="customer_name" id="customer_name" required 
                       value="{{ old('customer_name', auth()->check() ? auth()->user()->name : '') }}" 
                       class="w-full bg-cyber-dark border border-cyber-border focus:border-cyber-cyan focus:ring-1 focus:ring-cyber-cyan text-gray-200 rounded-xl py-3 px-4 transition duration-200">
                @error('customer_name')
                    <span class="text-xs text-red-400 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Email & Phone Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label for="customer_email" class="block text-xs font-orbitron font-bold text-gray-400">EMAIL ADDRESS</label>
                    <input type="email" name="customer_email" id="customer_email" required 
                           value="{{ old('customer_email', auth()->check() ? auth()->user()->email : '') }}" 
                           class="w-full bg-cyber-dark border border-cyber-border focus:border-cyber-cyan focus:ring-1 focus:ring-cyber-cyan text-gray-200 rounded-xl py-3 px-4 transition duration-200">
                    @error('customer_email')
                        <span class="text-xs text-red-400 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="customer_phone" class="block text-xs font-orbitron font-bold text-gray-400">CONTACT PHONE</label>
                    <input type="text" name="customer_phone" id="customer_phone" required 
                           value="{{ old('customer_phone') }}" placeholder="+94 XX XXX XXXX"
                           class="w-full bg-cyber-dark border border-cyber-border focus:border-cyber-cyan focus:ring-1 focus:ring-cyber-cyan text-gray-200 rounded-xl py-3 px-4 transition duration-200">
                    @error('customer_phone')
                        <span class="text-xs text-red-400 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="space-y-2">
                <label for="shipping_address" class="block text-xs font-orbitron font-bold text-gray-400">SHIPPING DESTINATION / ADDRESS</label>
                <textarea name="shipping_address" id="shipping_address" rows="4" required placeholder="Bunker No, Street Name, City, Country"
                          class="w-full bg-cyber-dark border border-cyber-border focus:border-cyber-cyan focus:ring-1 focus:ring-cyber-cyan text-gray-200 rounded-xl py-3 px-4 transition duration-200">{{ old('shipping_address') }}</textarea>
                @error('shipping_address')
                    <span class="text-xs text-red-400 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Payment Method Choice -->
            <div class="space-y-4">
                <label class="block text-xs font-orbitron font-bold text-gray-400">PAYMENT ROUTING PROTOCOL</label>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Cash On Delivery -->
                    <label class="relative flex flex-col p-4 border border-cyber-border rounded-xl cursor-pointer hover:border-cyber-cyan bg-cyber-dark/40 transition duration-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-orbitron font-bold text-white text-sm">CASH ON DELIVERY</span>
                            <input type="radio" name="payment_method" value="cod" checked class="text-cyber-cyan focus:ring-cyber-cyan border-cyber-border bg-cyber-dark">
                        </div>
                        <span class="text-xs text-gray-400">Pay in cash upon physical delivery at your designated secure coordinates.</span>
                    </label>

                    <!-- Bank Transfer -->
                    <label class="relative flex flex-col p-4 border border-cyber-border rounded-xl cursor-pointer hover:border-cyber-cyan bg-cyber-dark/40 transition duration-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-orbitron font-bold text-white text-sm">BANK TRANSFER</span>
                            <input type="radio" name="payment_method" value="bank_transfer" class="text-cyber-cyan focus:ring-cyber-cyan border-cyber-border bg-cyber-dark">
                        </div>
                        <span class="text-xs text-gray-400">Transfer directly to our corporate bank account and upload transaction details.</span>
                    </label>
                </div>
                @error('payment_method')
                    <span class="text-xs text-red-400 block">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Order Summary Sidebar (Col span 5) -->
        <div class="lg:col-span-5 bg-cyber-card/65 border border-cyber-border rounded-2xl p-6 md:p-8 space-y-6 backdrop-blur-sm relative">
            <!-- Subtle glow -->
            <div class="absolute -inset-px border border-cyber-cyan/20 rounded-2xl pointer-events-none"></div>

            <h3 class="font-orbitron font-bold text-white text-lg tracking-wider border-b border-cyber-border/60 pb-4">
                CONFIG ORDER SUMMARY
            </h3>

            <!-- Cart Items Summary List -->
            <div class="divide-y divide-cyber-border/40 max-h-72 overflow-y-auto pr-2">
                @foreach($cart as $item)
                    <div class="py-4 flex justify-between gap-4">
                        <div class="flex gap-3">
                            <span class="text-sm font-orbitron font-semibold text-cyber-cyan">x{{ $item['quantity'] }}</span>
                            <div>
                                <span class="block text-sm font-bold text-white leading-tight">{{ $item['name'] }}</span>
                                <span class="text-xs text-gray-500 font-orbitron">Rs. {{ number_format($item['price'], 2) }} each</span>
                            </div>
                        </div>
                        <span class="text-sm font-bold font-orbitron text-white">
                            Rs. {{ number_format($item['price'] * $item['quantity'], 2) }}
                        </span>
                    </div>
                @endforeach
            </div>

            <!-- Totals -->
            <div class="border-t border-cyber-border/60 pt-4 space-y-3 text-sm">
                <div class="flex justify-between text-gray-400">
                    <span>Hardware Cost</span>
                    <span class="font-orbitron text-white">Rs. {{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-gray-400">
                    <span>Delivery Route cost</span>
                    <span class="font-orbitron text-emerald-400">FREE</span>
                </div>
                <hr class="border-cyber-border/40 my-4">
                <div class="flex justify-between text-base font-bold">
                    <span class="text-white font-orbitron">TOTAL PROTOCOL CHARGE</span>
                    <span class="font-orbitron text-cyber-cyan drop-shadow-[0_0_8px_rgba(0,227,253,0.3)] text-xl">
                        Rs. {{ number_format($subtotal, 2) }}
                    </span>
                </div>
            </div>

            <!-- Submit Order -->
            <div class="pt-4">
                <button type="submit" class="w-full py-4 bg-cyber-cyan hover:bg-cyber-cyan/80 text-cyber-dark font-extrabold font-orbitron rounded-xl transition duration-300 shadow-neon-cyan hover:shadow-neon-cyan-lg transform hover:-translate-y-0.5 text-center flex items-center justify-center gap-2">
                    TRANSMIT ORDER CONTRACT <i class="fa-solid fa-satellite-dish animate-pulse"></i>
                </button>
                <p class="text-[10px] text-gray-500 text-center mt-3">
                    By submitting, you authorize the immediate packaging and deployment check of requested hardware modules.
                </p>
            </div>
        </div>
    </form>
</div>
@endsection
