@extends('layouts.shop')

@section('title', 'Neuronet | Shopping Cart')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Breadcrumb -->
    <nav class="flex mb-8 text-xs font-orbitron font-semibold text-gray-500 gap-2">
        <a href="{{ route('home') }}" class="hover:text-cyber-cyan transition duration-150">HOME</a>
        <span>/</span>
        <span class="text-cyber-cyan">SHOPPING CART</span>
    </nav>

    <h1 class="text-3xl font-extrabold font-orbitron text-white tracking-widest mb-10">
        YOUR COMPONENT DECK
        <span class="block w-20 h-1 bg-cyber-cyan mt-2 shadow-neon-cyan"></span>
    </h1>

    @if(empty($cart))
        <!-- Empty Cart State -->
        <div class="bg-cyber-card/45 border border-cyber-border rounded-3xl p-12 text-center relative overflow-hidden">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-cyber-cyan/5 rounded-full blur-[80px] pointer-events-none"></div>
            
            <div class="relative z-10 space-y-6 py-12">
                <div class="w-20 h-20 bg-cyber-dark border border-cyber-border rounded-full flex items-center justify-center mx-auto text-gray-500">
                    <i class="fa-solid fa-cart-shopping text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold font-orbitron text-white">Cart is Empty</h3>
                <p class="text-sm text-gray-400 max-w-sm mx-auto">
                    You have not selected any hardware modules for your terminal system. Explore the catalog to deck out your build.
                </p>
                <div class="pt-4">
                    <a href="{{ route('catalog') }}" class="px-8 py-4 bg-cyber-cyan hover:bg-cyber-cyan/80 text-cyber-dark font-bold font-orbitron rounded-xl transition duration-300 shadow-neon-cyan">
                        GO TO CATALOG <i class="fa-solid fa-arrow-right-long ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
    @else
        <!-- Cart Table & Summary Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Cart Items List (Col span 8) -->
            <div class="lg:col-span-8 space-y-6">
                <div class="bg-cyber-card/45 border border-cyber-border rounded-2xl overflow-hidden backdrop-blur-sm">
                    <div class="p-6 border-b border-cyber-border/60 bg-cyber-dark/40 flex justify-between items-center">
                        <span class="font-orbitron font-bold text-white text-sm">HARDWARE MODULE</span>
                        <span class="font-orbitron font-bold text-white text-sm hidden md:block">SUBTOTAL</span>
                    </div>

                    <div class="divide-y divide-cyber-border/40">
                        @foreach($cart as $id => $item)
                            <div class="p-6 flex flex-col md:flex-row md:items-center justify-between gap-6">
                                <!-- Product Details -->
                                <div class="flex items-center gap-4 flex-grow">
                                    <div class="w-16 h-16 rounded-xl border border-cyber-border bg-cyber-dark flex items-center justify-center text-gray-400 flex-shrink-0">
                                        @if(!empty($item['image_path']))
                                            <img src="{{ asset('storage/' . $item['image_path']) }}" class="w-full h-full object-cover rounded-xl" alt="{{ $item['name'] }}">
                                        @else
                                            <i class="fa-solid fa-box text-2xl"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="text-[10px] font-orbitron font-bold text-cyber-cyan tracking-wider uppercase bg-cyber-cyan/5 border border-cyber-cyan/20 px-2 py-0.5 rounded">
                                            {{ $item['brand'] }}
                                        </span>
                                        <a href="{{ route('products.show', $item['slug']) }}" class="block mt-1 font-bold text-white hover:text-cyber-cyan transition duration-150">
                                            {{ $item['name'] }}
                                        </a>
                                        <span class="text-sm text-gray-400 font-orbitron">Rs. {{ number_format($item['price'], 2) }}</span>
                                    </div>
                                </div>

                                <!-- Quantity & Action Controls -->
                                <div class="flex items-center justify-between md:justify-end gap-8">
                                    <!-- Quantity Update Form -->
                                    <form action="{{ route('cart.update', $id) }}" method="POST" class="flex items-center bg-cyber-dark border border-cyber-border rounded-lg p-1">
                                        @csrf
                                        <button type="submit" name="quantity" value="{{ $item['quantity'] - 1 }}" class="px-2.5 py-1 text-gray-400 hover:text-cyber-cyan transition duration-150">
                                            <i class="fa-solid fa-minus text-xs"></i>
                                        </button>
                                        <input type="text" class="w-10 bg-transparent text-center border-none focus:ring-0 text-white font-orbitron text-sm p-0" value="{{ $item['quantity'] }}" readonly>
                                        <button type="submit" name="quantity" value="{{ $item['quantity'] + 1 }}" class="px-2.5 py-1 text-gray-400 hover:text-cyber-cyan transition duration-150" {{ $item['quantity'] >= $item['max_stock'] ? 'disabled' : '' }}>
                                            <i class="fa-solid fa-plus text-xs"></i>
                                        </button>
                                    </form>

                                    <!-- Price Subtotal (Mobile only label) -->
                                    <div class="text-right">
                                        <span class="text-base font-bold font-orbitron text-white block">
                                            Rs. {{ number_format($item['price'] * $item['quantity'], 2) }}
                                        </span>
                                    </div>

                                    <!-- Delete Item -->
                                    <form action="{{ route('cart.remove', $id) }}" method="POST" onsubmit="return confirm('Remove this module from configuration?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 border border-cyber-border hover:border-red-500/50 hover:text-red-400 text-gray-500 rounded-lg transition duration-200">
                                            <i class="fa-solid fa-trash-can text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Order Summary Sidebar (Col span 4) -->
            <div class="lg:col-span-4">
                <div class="bg-cyber-card/65 border border-cyber-border rounded-2xl p-6 space-y-6 backdrop-blur-sm relative">
                    <!-- Subtle glow -->
                    <div class="absolute -inset-px border border-cyber-cyan/20 rounded-2xl pointer-events-none"></div>

                    <h3 class="font-orbitron font-bold text-white text-lg tracking-wider border-b border-cyber-border/60 pb-4">
                        SYSTEM CHECKOUT SUMMARY
                    </h3>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between text-gray-400">
                            <span>Hardware Subtotal</span>
                            <span class="font-orbitron text-white">Rs. {{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-400">
                            <span>Bunker Delivery Fee</span>
                            <span class="font-orbitron text-emerald-400">FREE</span>
                        </div>
                        <hr class="border-cyber-border/40 my-4">
                        <div class="flex justify-between text-base font-bold">
                            <span class="text-white">TOTAL ESTIMATE</span>
                            <span class="font-orbitron text-cyber-cyan drop-shadow-[0_0_8px_rgba(0,227,253,0.3)]">
                                Rs. {{ number_format($subtotal, 2) }}
                            </span>
                        </div>
                    </div>

                    <div class="pt-4">
                        <a href="{{ route('checkout.index') }}" class="w-full block py-4 text-center bg-cyber-cyan hover:bg-cyber-cyan/80 text-cyber-dark font-bold font-orbitron rounded-xl transition duration-300 shadow-neon-cyan hover:shadow-neon-cyan-lg transform hover:-translate-y-0.5">
                            PROCEED TO SECURE CHECKOUT <i class="fa-solid fa-shield-halved ml-1"></i>
                        </a>
                        <a href="{{ route('catalog') }}" class="w-full block text-center text-xs font-orbitron text-gray-400 hover:text-cyber-cyan transition duration-150 mt-4">
                            <i class="fa-solid fa-arrow-left-long mr-2"></i> CONTINUE DECKING CATALOG
                        </a>
                    </div>
                </div>
            </div>

        </div>
    @endif
</div>
@endsection
