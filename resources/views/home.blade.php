@extends('layouts.shop')

@section('title', 'Neuronet Computer Store | Cyberpunk Hardware Store')

@section('content')
<!-- Hero Section -->
<div class="relative overflow-hidden py-24 sm:py-32 border-b border-cyber-border/40 bg-gradient-to-b from-cyber-dark to-cyber-dark/40">
    <!-- Glowing background elements -->
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-cyber-cyan/10 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute top-1/2 right-10 w-[300px] h-[300px] bg-purple-500/5 rounded-full blur-[100px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <!-- Text Content -->
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-cyber-cyan/30 bg-cyber-cyan/5 text-xs font-orbitron font-semibold tracking-widest text-cyber-cyan uppercase">
                    <span class="w-2 h-2 rounded-full bg-cyber-cyan animate-pulse"></span> SYSTEM STATUS: ONLINE
                </span>
                
                <h1 class="text-4xl sm:text-6xl font-extrabold font-orbitron tracking-tight text-white leading-none">
                    ENTER THE <br class="hidden sm:block">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyber-cyan to-blue-400 drop-shadow-[0_0_15px_rgba(0,227,253,0.3)]">NEXT GENERATION</span> <br>
                    OF COMPUTING
                </h1>
                
                <p class="text-lg text-gray-400 max-w-xl mx-auto lg:mx-0">
                    Equip your digital setup with high-end, custom-liquid-cooled gaming PCs, elite processors, and raw graphics processing units tailored for future-proof operations.
                </p>

                <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4 pt-4">
                    <a href="{{ route('catalog') }}" class="px-8 py-4 bg-cyber-cyan hover:bg-cyber-cyan/80 text-cyber-dark font-bold font-orbitron rounded-xl transition duration-300 shadow-neon-cyan hover:shadow-neon-cyan-lg transform hover:-translate-y-0.5">
                        EXPLORE CATALOG <i class="fa-solid fa-arrow-right-long ml-2"></i>
                    </a>
                    <a href="{{ route('catalog', ['category' => 'gaming-pcs']) }}" class="px-8 py-4 border border-cyber-border hover:border-cyber-cyan/50 hover:bg-cyber-cyan/5 font-semibold font-orbitron rounded-xl text-gray-300 hover:text-cyber-cyan transition duration-300">
                        CUSTOM RIGS
                    </a>
                </div>
            </div>

            <!-- Graphic Display / Featured Rig -->
            <div class="lg:col-span-5 relative flex justify-center">
                <div class="relative w-80 h-96 sm:w-96 sm:h-[450px] bg-cyber-card/60 border border-cyber-cyan/30 rounded-3xl p-6 shadow-neon-cyan/10 backdrop-blur-sm overflow-hidden group">
                    <!-- Glassmorphism card light beam -->
                    <div class="absolute -inset-x-20 top-0 h-40 bg-gradient-to-b from-cyber-cyan/15 to-transparent rotate-12 blur-md"></div>
                    
                    <div class="h-full flex flex-col justify-between relative z-10">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-xs text-cyber-cyan/80 font-orbitron font-semibold tracking-wider">FEATURED BUILD</span>
                                <h3 class="text-xl font-bold font-orbitron text-white mt-1">APEX HUNTER v1</h3>
                            </div>
                            <span class="px-3 py-1 bg-cyber-cyan/10 border border-cyber-cyan/30 text-cyber-cyan font-orbitron text-xs font-bold rounded-lg">
                                Rs. 2499
                            </span>
                        </div>

                        <!-- Vector Representation or Static Image of PC -->
                        <div class="my-6 flex justify-center relative">
                            <div class="w-48 h-48 rounded-2xl border border-cyber-border bg-cyber-dark/80 flex items-center justify-center relative overflow-hidden group-hover:border-cyber-cyan/50 transition duration-300">
                                <i class="fa-solid fa-desktop text-7xl text-gray-700 group-hover:text-cyber-cyan transition duration-500 transform group-hover:scale-110"></i>
                                <!-- Neon glowing dots -->
                                <div class="absolute top-4 left-4 w-2.5 h-2.5 rounded-full bg-cyber-cyan shadow-neon-cyan animate-pulse"></div>
                                <div class="absolute bottom-4 right-4 w-2 h-2 rounded-full bg-purple-500 animate-ping"></div>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs text-gray-400 line-clamp-2">
                                Liquid cooled Intel i9-14900K and RTX 4080 Super workstation ready for high-fidelity operations.
                            </p>
                            <a href="{{ route('catalog', ['search' => 'Apex Hunter']) }}" class="mt-4 w-full block py-2.5 text-center text-xs font-bold font-orbitron bg-cyber-border text-cyber-cyan border border-cyber-border hover:border-cyber-cyan/50 rounded-xl transition duration-200">
                                VIEW RIG
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Categories Grid -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 relative">
    <div class="text-center max-w-3xl mx-auto mb-16">
        <h2 class="text-3xl font-extrabold font-orbitron text-white tracking-wider">SHOP BY HARDWARE LAYER</h2>
        <div class="w-24 h-1 bg-cyber-cyan mx-auto mt-3 shadow-neon-cyan"></div>
        <p class="text-sm text-gray-400 mt-4">Select the specific component deck to upgrade your current station.</p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
        @foreach($categories as $category)
            <a href="{{ route('catalog', ['category' => $category->slug]) }}" class="group bg-cyber-card/40 border border-cyber-border hover:border-cyber-cyan/50 rounded-2xl p-6 text-center transition duration-300 hover:shadow-neon-cyan/5 flex flex-col justify-between items-center h-44">
                <div class="w-12 h-12 rounded-xl bg-cyber-dark border border-cyber-border flex items-center justify-center text-gray-400 group-hover:text-cyber-cyan group-hover:border-cyber-cyan/35 transition duration-200">
                    @if($category->slug === 'gaming-pcs')
                        <i class="fa-solid fa-desktop text-2xl"></i>
                    @elseif($category->slug === 'gpus')
                        <i class="fa-solid fa-gamepad text-2xl"></i>
                    @elseif($category->slug === 'cpus')
                        <i class="fa-solid fa-microchip text-2xl"></i>
                    @elseif($category->slug === 'ram')
                        <i class="fa-solid fa-memory text-2xl"></i>
                    @elseif($category->slug === 'storage')
                        <i class="fa-solid fa-database text-2xl"></i>
                    @else
                        <i class="fa-solid fa-keyboard text-2xl"></i>
                    @endif
                </div>
                <div>
                    <h3 class="font-orbitron font-semibold text-sm text-white group-hover:text-cyber-cyan transition duration-150 mt-4 leading-tight">{{ $category->name }}</h3>
                    <span class="text-[10px] font-semibold text-gray-500 uppercase tracking-widest mt-2 block">{{ $category->total_products_count }} items</span>
                </div>
            </a>
        @endforeach
    </div>
</div>

<!-- Featured Products Section -->
<div class="bg-cyber-card/10 border-t border-b border-cyber-border/40 py-20 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-end mb-16">
            <div>
                <span class="text-xs font-orbitron font-semibold text-cyber-cyan tracking-wider">CURATED PICKS</span>
                <h2 class="text-3xl font-extrabold font-orbitron text-white mt-1">FEATURED COMPONENT DECK</h2>
                <div class="w-16 h-1 bg-cyber-cyan mt-3 shadow-neon-cyan"></div>
            </div>
            <a href="{{ route('catalog') }}" class="mt-4 sm:mt-0 text-sm font-orbitron font-bold text-cyber-cyan hover:text-white transition duration-150">
                VIEW ENTIRE CATALOG <i class="fa-solid fa-angles-right ml-1"></i>
            </a>
        </div>

        <!-- Product Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($featuredProducts as $product)
                <div class="group bg-cyber-card/65 border border-cyber-border hover:border-cyber-cyan/50 rounded-2xl overflow-hidden transition duration-300 hover:shadow-neon-cyan/5 flex flex-col h-full">
                    <!-- Image Showcase -->
                    <div class="relative bg-cyber-dark/80 border-b border-cyber-border/40 h-56 flex items-center justify-center p-6 overflow-hidden">
                        <i class="fa-solid fa-box text-5xl text-gray-800 group-hover:scale-110 transition duration-300"></i>
                        <span class="absolute top-4 left-4 px-2 py-0.5 bg-cyber-cyan/15 border border-cyber-cyan/35 text-[10px] font-orbitron font-bold text-cyber-cyan rounded-md uppercase tracking-wider">
                            {{ $product->brand }}
                        </span>
                        @if($product->stock <= 0)
                            <div class="absolute inset-0 bg-cyber-dark/90 flex items-center justify-center">
                                <span class="font-orbitron font-bold text-red-500 tracking-wider uppercase border border-red-500/30 px-4 py-2 bg-red-500/10 rounded-md">OUT OF STOCK</span>
                            </div>
                        @elseif($product->stock < 5)
                            <span class="absolute top-4 right-4 px-2 py-0.5 bg-amber-500/10 border border-amber-500/30 text-[10px] font-orbitron font-bold text-amber-500 rounded-md uppercase tracking-wider">
                                LOW STOCK: {{ $product->stock }}
                            </span>
                        @endif
                    </div>

                    <!-- Details -->
                    <div class="p-6 flex flex-col flex-grow justify-between">
                        <div>
                            <span class="text-xs text-gray-500">{{ $product->category->name }}</span>
                            <a href="{{ route('products.show', $product->slug) }}">
                                <h3 class="text-lg font-bold font-orbitron text-white group-hover:text-cyber-cyan transition duration-150 mt-1 leading-snug line-clamp-1">{{ $product->name }}</h3>
                            </a>
                            <p class="text-sm text-gray-400 mt-2 line-clamp-2">{{ $product->description }}</p>
                        </div>
                        
                        <div class="mt-6 flex items-center justify-between">
                            <span class="text-xl font-bold font-orbitron text-white">Rs. {{ number_format($product->price, 2) }}</span>
                            
                            <div class="flex items-center gap-2">
                                <a href="{{ route('products.show', $product->slug) }}" class="p-2 border border-cyber-border hover:border-cyber-cyan hover:text-cyber-cyan rounded-lg text-gray-400 transition duration-150">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>
                                @if($product->stock > 0)
                                    <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 bg-cyber-cyan hover:bg-cyber-cyan/80 text-cyber-dark font-bold text-xs font-orbitron rounded-lg transition duration-150 shadow-neon-cyan">
                                            ADD <i class="fa-solid fa-cart-plus ml-1"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Info banner / features -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Feature 1 -->
        <div class="bg-cyber-card/30 border border-cyber-border rounded-2xl p-8 space-y-3">
            <i class="fa-solid fa-truck-fast text-3xl text-cyber-cyan"></i>
            <h4 class="font-orbitron font-bold text-lg text-white">SECURE TRANSACTIONS & FAST DELIVERIES</h4>
            <p class="text-sm text-gray-400">All components are packaged with extreme ESD-safe materials and shipped tracked straight to your bunker.</p>
        </div>
        <!-- Feature 2 -->
        <div class="bg-cyber-card/30 border border-cyber-border rounded-2xl p-8 space-y-3">
            <i class="fa-solid fa-microchip text-3xl text-cyber-cyan"></i>
            <h4 class="font-orbitron font-bold text-lg text-white">GENUINE HARDWARE ACCREDITATION</h4>
            <p class="text-sm text-gray-400">We source directly from manufacturers. All GPUs, CPUs and RAM carry active official manufacturer warranty.</p>
        </div>
        <!-- Feature 3 -->
        <div class="bg-cyber-card/30 border border-cyber-border rounded-2xl p-8 space-y-3">
            <i class="fa-solid fa-screwdriver-wrench text-3xl text-cyber-cyan"></i>
            <h4 class="font-orbitron font-bold text-lg text-white">NEURONET CUSTOM BUILD CONFIGS</h4>
            <p class="text-sm text-gray-400">Talk to our technicians on Discord. We can build, leak test, and tune custom loops exactly to your budget.</p>
        </div>
    </div>
</div>
@endsection
