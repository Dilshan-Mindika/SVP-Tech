@extends('layouts.shop')

@section('title', $product->name . ' | Neuronet Computer Store')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Breadcrumbs -->
    <nav class="flex mb-8 text-xs font-orbitron font-semibold tracking-wider text-gray-500 uppercase">
        <a href="{{ route('home') }}" class="hover:text-cyber-cyan transition">HOME</a>
        <span class="mx-2">/</span>
        <a href="{{ route('catalog') }}" class="hover:text-cyber-cyan transition">CATALOG</a>
        <span class="mx-2">/</span>
        <a href="{{ route('catalog', ['category' => $product->category->slug]) }}" class="hover:text-cyber-cyan transition">{{ $product->category->name }}</a>
        <span class="mx-2">/</span>
        <span class="text-gray-300">{{ $product->name }}</span>
    </nav>

    <!-- Product Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 bg-cyber-card/30 border border-cyber-border rounded-3xl p-6 sm:p-10 mb-16">
        <!-- Product Image Panel -->
        <div class="lg:col-span-5 flex justify-center">
            <div class="w-full max-w-md h-96 rounded-2xl bg-cyber-dark/85 border border-cyber-border flex items-center justify-center relative overflow-hidden group">
                <div class="absolute -inset-x-20 top-0 h-40 bg-gradient-to-b from-cyber-cyan/10 to-transparent rotate-12 blur-md"></div>
                <i class="fa-solid fa-microchip text-9xl text-gray-800 group-hover:scale-105 transition duration-500"></i>
                
                <!-- Corner Borders (Sci-fi Aesthetic) -->
                <div class="absolute top-0 left-0 w-6 h-6 border-t-2 border-l-2 border-cyber-cyan/40"></div>
                <div class="absolute top-0 right-0 w-6 h-6 border-t-2 border-r-2 border-cyber-cyan/40"></div>
                <div class="absolute bottom-0 left-0 w-6 h-6 border-b-2 border-l-2 border-cyber-cyan/40"></div>
                <div class="absolute bottom-0 right-0 w-6 h-6 border-b-2 border-r-2 border-cyber-cyan/40"></div>
            </div>
        </div>

        <!-- Product Core Info Panel -->
        <div class="lg:col-span-7 flex flex-col justify-between">
            <div class="space-y-6">
                <!-- Badges -->
                <div class="flex items-center gap-3">
                    <span class="px-2.5 py-1 bg-cyber-cyan/10 border border-cyber-cyan/30 text-xs font-orbitron font-semibold tracking-wider text-cyber-cyan rounded-md">
                        {{ $product->brand }}
                    </span>
                    <span class="text-xs text-gray-500 uppercase tracking-widest">{{ $product->category->name }}</span>
                </div>

                <!-- Title -->
                <h1 class="text-3xl sm:text-4xl font-extrabold font-orbitron text-white leading-tight">
                    {{ $product->name }}
                </h1>

                <!-- Price and Stock -->
                <div class="flex items-center gap-6 py-4 border-t border-b border-cyber-border/40">
                    <div>
                        <span class="text-xs text-gray-500 font-semibold block">PRICE</span>
                        <span class="text-3xl font-bold font-orbitron text-cyber-cyan">Rs. {{ number_format($product->price, 2) }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 font-semibold block">AVAILABILITY</span>
                        @if($product->stock <= 0)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-500/10 border border-red-500/30 text-red-500 text-xs font-orbitron font-bold rounded-lg mt-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> OUT OF STOCK
                            </span>
                        @elseif($product->stock < 5)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-500/10 border border-amber-500/30 text-amber-500 text-xs font-orbitron font-bold rounded-lg mt-1 animate-pulse">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span> LOW STOCK ({{ $product->stock }} LEFT)
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-orbitron font-bold rounded-lg mt-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> {{ $product->stock }} IN STOCK
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <h3 class="text-xs font-orbitron font-bold text-gray-400 tracking-wider mb-2">DESCRIPTION</h3>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        {{ $product->description }}
                    </p>
                </div>
            </div>

            <!-- Add to Cart / Actions -->
            <div class="mt-8 pt-6 border-t border-cyber-border/40">
                @if($product->stock > 0)
                    <form action="{{ route('cart.add', $product->id) }}" method="POST" class="flex flex-wrap items-center gap-4">
                        @csrf
                        <div class="flex items-center bg-cyber-dark border border-cyber-border rounded-xl px-4 py-3">
                            <span class="text-xs font-orbitron font-semibold text-gray-500 mr-3 uppercase">QTY</span>
                            <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" class="w-12 bg-transparent border-0 p-0 text-center text-sm font-semibold focus:ring-0 text-white">
                        </div>
                        <button type="submit" class="flex-grow px-8 py-4 bg-cyber-cyan hover:bg-cyber-cyan/80 text-cyber-dark font-extrabold font-orbitron rounded-xl transition duration-200 shadow-neon-cyan hover:shadow-neon-cyan-lg text-center flex items-center justify-center gap-2">
                            ADD TO CART <i class="fa-solid fa-cart-shopping"></i>
                        </button>
                    </form>
                @else
                    <button disabled class="w-full py-4 bg-cyber-border text-gray-500 font-bold font-orbitron rounded-xl border border-cyber-border cursor-not-allowed text-center uppercase">
                        Product Unavailable
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Specifications Table -->
    @if($product->specifications && count($product->specifications) > 0)
        <div class="bg-cyber-card/30 border border-cyber-border rounded-3xl p-6 sm:p-10 mb-16">
            <h2 class="text-2xl font-extrabold font-orbitron text-white mb-6 tracking-wide flex items-center gap-3">
                <i class="fa-solid fa-gears text-cyber-cyan text-xl"></i> SPECIFICATIONS
            </h2>
            <div class="overflow-x-auto border border-cyber-border/50 rounded-2xl">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-cyber-dark/50 border-b border-cyber-border">
                            <th class="py-4 px-6 text-xs font-orbitron font-bold text-gray-400 tracking-wider">ATTRIBUTE</th>
                            <th class="py-4 px-6 text-xs font-orbitron font-bold text-gray-400 tracking-wider">VALUE</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-cyber-border/30">
                        @foreach($product->specifications as $key => $value)
                            <tr class="hover:bg-cyber-card/20 transition">
                                <td class="py-4 px-6 text-sm font-orbitron font-semibold text-white tracking-wider uppercase bg-cyber-dark/20 w-1/3">{{ $key }}</td>
                                <td class="py-4 px-6 text-sm text-gray-400 font-medium">{{ is_array($value) ? implode(', ', $value) : $value }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
        <div class="space-y-8">
            <div>
                <span class="text-xs font-orbitron font-semibold text-cyber-cyan tracking-wider">UPGRADE PATH</span>
                <h2 class="text-2xl font-extrabold font-orbitron text-white mt-1">RELATED PRODUCTS</h2>
                <div class="w-12 h-1 bg-cyber-cyan mt-3 shadow-neon-cyan"></div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $relProduct)
                    <div class="group bg-cyber-card/65 border border-cyber-border hover:border-cyber-cyan/50 rounded-2xl overflow-hidden transition duration-300 hover:shadow-neon-cyan/5 flex flex-col h-full">
                        <div class="relative bg-cyber-dark/80 border-b border-cyber-border/40 h-40 flex items-center justify-center p-6 overflow-hidden">
                            <i class="fa-solid fa-box text-4xl text-gray-800 group-hover:scale-110 transition duration-300"></i>
                            <span class="absolute top-4 left-4 px-2 py-0.5 bg-cyber-cyan/10 border border-cyber-cyan/30 text-[9px] font-orbitron font-bold text-cyber-cyan rounded-md uppercase tracking-wider">
                                {{ $relProduct->brand }}
                            </span>
                        </div>
                        <div class="p-4 flex flex-col flex-grow justify-between">
                            <div>
                                <a href="{{ route('products.show', $relProduct->slug) }}">
                                    <h3 class="text-sm font-bold font-orbitron text-white group-hover:text-cyber-cyan transition duration-150 leading-tight line-clamp-1">{{ $relProduct->name }}</h3>
                                </a>
                                <span class="text-[10px] text-gray-500 uppercase tracking-widest mt-1 block">{{ $relProduct->category->name }}</span>
                            </div>
                            <div class="mt-4 flex items-center justify-between">
                                <span class="text-sm font-bold font-orbitron text-white">Rs. {{ number_format($relProduct->price, 2) }}</span>
                                <a href="{{ route('products.show', $relProduct->slug) }}" class="p-1.5 border border-cyber-border hover:border-cyber-cyan hover:text-cyber-cyan rounded-md text-gray-400 transition duration-150">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
