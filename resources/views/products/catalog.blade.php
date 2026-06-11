@extends('layouts.shop')

@section('title', 'Hardware Deck | Neuronet Computer Store')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Breadcrumbs -->
    <nav class="flex mb-8 text-xs font-orbitron font-semibold tracking-wider text-gray-500 uppercase">
        <a href="{{ route('home') }}" class="hover:text-cyber-cyan transition">HOME</a>
        <span class="mx-2">/</span>
        <span class="text-gray-300">CATALOG</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar Filters -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-cyber-card/70 border border-cyber-border rounded-2xl p-6 sticky top-24">
                <div class="flex items-center justify-between pb-4 border-b border-cyber-border">
                    <h3 class="font-orbitron font-bold text-white tracking-wide flex items-center gap-2">
                        <i class="fa-solid fa-sliders text-cyber-cyan"></i> FILTERS
                    </h3>
                    <a href="{{ route('catalog') }}" class="text-xs text-cyber-cyan hover:text-white transition duration-150">Reset</a>
                </div>

                <form action="{{ route('catalog') }}" method="GET" class="space-y-6 pt-6">
                    <!-- Carry Search Value if exists in query -->
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif

                    <!-- Category Filter -->
                    <div class="space-y-3">
                        <label class="font-orbitron text-xs font-bold text-gray-400 tracking-wider block">CATEGORY</label>
                        <select name="category" onchange="this.form.submit()" class="w-full bg-cyber-dark border border-cyber-border rounded-xl text-sm text-gray-300 focus:border-cyber-cyan focus:ring-cyber-cyan py-2.5 px-3">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                    {{ $category->nested_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Brand Filter -->
                    <div class="space-y-3">
                        <label class="font-orbitron text-xs font-bold text-gray-400 tracking-wider block">BRAND</label>
                        <select name="brand" onchange="this.form.submit()" class="w-full bg-cyber-dark border border-cyber-border rounded-xl text-sm text-gray-300 focus:border-cyber-cyan focus:ring-cyber-cyan py-2.5 px-3">
                            <option value="">All Brands</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand }}" {{ request('brand') == $brand ? 'selected' : '' }}>
                                    {{ $brand }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Price Filter -->
                    <div class="space-y-3">
                        <label class="font-orbitron text-xs font-bold text-gray-400 tracking-wider block">PRICE RANGE (Rs.)</label>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <span class="text-[10px] text-gray-500 block mb-1">MIN</span>
                                <input type="number" name="min_price" value="{{ request('min_price', 0) }}" min="0" class="w-full bg-cyber-dark border border-cyber-border rounded-xl text-xs text-gray-200 focus:border-cyber-cyan focus:ring-cyber-cyan py-2 px-3">
                            </div>
                            <div>
                                <span class="text-[10px] text-gray-500 block mb-1">MAX</span>
                                <input type="number" name="max_price" value="{{ request('max_price', 3000) }}" class="w-full bg-cyber-dark border border-cyber-border rounded-xl text-xs text-gray-200 focus:border-cyber-cyan focus:ring-cyber-cyan py-2 px-3">
                            </div>
                        </div>
                        <button type="submit" class="w-full mt-3 py-2 bg-cyber-border hover:bg-cyber-cyan hover:text-cyber-dark font-bold font-orbitron text-xs rounded-xl text-cyber-cyan transition duration-150 border border-cyber-border hover:border-cyber-cyan">
                            APPLY RANGE
                        </button>
                    </div>

                    <!-- Carry Sort Option -->
                    <input type="hidden" name="sort" value="{{ request('sort', 'featured') }}">
                </form>
            </div>
        </div>

        <!-- Catalog List -->
        <div class="lg:col-span-3 space-y-6">
            <!-- Catalog Header -->
            <div class="bg-cyber-card/40 border border-cyber-border rounded-2xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-sm text-gray-400">
                    Showing <span class="text-white font-semibold">{{ $products->firstItem() ?? 0 }}</span> - <span class="text-white font-semibold">{{ $products->lastItem() ?? 0 }}</span> of <span class="text-white font-semibold">{{ $products->total() }}</span> products
                    @if(request('search'))
                        for "<span class="text-cyber-cyan font-semibold">{{ request('search') }}</span>"
                    @endif
                </div>

                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <label class="text-xs font-orbitron font-semibold text-gray-500 uppercase whitespace-nowrap">SORT BY</label>
                    <form action="{{ request()->fullUrlWithQuery([]) }}" method="GET" class="w-full sm:w-auto">
                        <!-- Propagate active filters to Sort Form -->
                        @foreach(request()->except(['sort', 'page']) as $key => $val)
                            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                        @endforeach
                        
                        <select name="sort" onchange="this.form.submit()" class="bg-cyber-dark border border-cyber-border rounded-xl text-xs text-gray-300 focus:border-cyber-cyan focus:ring-cyber-cyan py-2 px-4 pr-8">
                            <option value="featured" {{ request('sort') == 'featured' ? 'selected' : '' }}>Featured</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest Releases</option>
                        </select>
                    </form>
                </div>
            </div>

            <!-- Empty State -->
            @if($products->isEmpty())
                <div class="bg-cyber-card/20 border border-cyber-border rounded-2xl p-16 text-center">
                    <i class="fa-solid fa-microchip text-6xl text-gray-800 mb-6"></i>
                    <h3 class="text-xl font-orbitron font-bold text-white">NO PRODUCTS FOUND</h3>
                    <p class="text-gray-500 mt-2 max-w-md mx-auto">No products match your filters. Try adjusting your search query or categories.</p>
                    <a href="{{ route('catalog') }}" class="mt-6 inline-block px-6 py-3 bg-cyber-cyan text-cyber-dark font-bold font-orbitron rounded-xl transition duration-150 shadow-neon-cyan">RESET</a>
                </div>
            @else
                <!-- Products Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($products as $product)
                        <div class="group bg-cyber-card/65 border border-cyber-border hover:border-cyber-cyan/50 rounded-2xl overflow-hidden transition duration-300 hover:shadow-neon-cyan/5 flex flex-col h-full">
                            <!-- Image -->
                            <div class="relative bg-cyber-dark/80 border-b border-cyber-border/40 h-48 flex items-center justify-center p-6 overflow-hidden">
                                <i class="fa-solid fa-microchip text-5xl text-gray-800 group-hover:scale-110 transition duration-300"></i>
                                <span class="absolute top-4 left-4 px-2 py-0.5 bg-cyber-cyan/10 border border-cyber-cyan/30 text-[10px] font-orbitron font-bold text-cyber-cyan rounded-md uppercase tracking-wider">
                                    {{ $product->brand }}
                                </span>
                                @if($product->stock <= 0)
                                    <div class="absolute inset-0 bg-cyber-dark/95 flex items-center justify-center">
                                        <span class="font-orbitron font-bold text-red-500 tracking-wider uppercase border border-red-500/30 px-3 py-1.5 bg-red-500/10 rounded-md">OUT OF STOCK</span>
                                    </div>
                                @elseif($product->stock < 5)
                                    <span class="absolute top-4 right-4 px-2 py-0.5 bg-amber-500/15 border border-amber-500/30 text-[10px] font-orbitron font-bold text-amber-500 rounded-md uppercase tracking-wider">
                                        LOW: {{ $product->stock }}
                                    </span>
                                @endif
                            </div>

                            <!-- Details -->
                            <div class="p-5 flex flex-col flex-grow justify-between">
                                <div>
                                    <span class="text-[10px] text-gray-500 uppercase tracking-widest">{{ $product->category->name }}</span>
                                    <a href="{{ route('products.show', $product->slug) }}">
                                        <h3 class="text-base font-bold font-orbitron text-white group-hover:text-cyber-cyan transition duration-150 mt-1 leading-snug line-clamp-1">{{ $product->name }}</h3>
                                    </a>
                                    <p class="text-xs text-gray-400 mt-2 line-clamp-2">{{ $product->description }}</p>
                                </div>
                                
                                <div class="mt-6 flex items-center justify-between">
                                    <span class="text-lg font-bold font-orbitron text-white">Rs. {{ number_format($product->price, 2) }}</span>
                                    
                                    <div class="flex items-center gap-1.5">
                                        <a href="{{ route('products.show', $product->slug) }}" class="p-2 border border-cyber-border hover:border-cyber-cyan hover:text-cyber-cyan rounded-lg text-gray-400 transition duration-150">
                                            <i class="fa-solid fa-eye text-xs"></i>
                                        </a>
                                        @if($product->stock > 0)
                                            <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-3 py-2 bg-cyber-cyan hover:bg-cyber-cyan/80 text-cyber-dark font-bold text-[10px] font-orbitron rounded-lg transition duration-150 shadow-neon-cyan flex items-center gap-1">
                                                    ADD <i class="fa-solid fa-cart-plus"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Custom Pagination -->
                <div class="mt-12">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
