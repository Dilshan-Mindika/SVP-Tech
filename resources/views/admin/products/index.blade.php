@extends('layouts.shop')

@section('title', 'Neuronet | Products Management')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    
    <!-- Heading and Action -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
        <div>
            <h1 class="text-3xl font-extrabold font-orbitron text-white tracking-widest uppercase">
                PRODUCTS INVENTORY DECK
            </h1>
            <div class="w-24 h-1 bg-cyber-cyan mt-2 shadow-neon-cyan"></div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2.5 rounded-lg border border-cyber-border bg-cyber-card text-gray-400 hover:text-cyber-cyan hover:border-cyber-cyan/50 font-orbitron text-xs font-bold transition duration-150">
                <i class="fa-solid fa-angle-left mr-2"></i> BACK TO DASHBOARD
            </a>
            <a href="{{ route('admin.products.create') }}" class="px-5 py-2.5 bg-cyber-cyan hover:bg-cyber-cyan/80 text-cyber-dark font-orbitron text-xs font-bold rounded-lg transition duration-150 shadow-neon-cyan flex items-center gap-2">
                <i class="fa-solid fa-circle-plus"></i> DEPLOY NEW PRODUCT
            </a>
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-cyber-card/45 border border-cyber-border rounded-2xl overflow-hidden backdrop-blur-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-cyber-border bg-cyber-dark/65 text-xs font-orbitron font-bold text-gray-400">
                        <th class="p-4 uppercase tracking-wider">Image</th>
                        <th class="p-4 uppercase tracking-wider">Product Info</th>
                        <th class="p-4 uppercase tracking-wider">Category</th>
                        <th class="p-4 uppercase tracking-wider text-right">Price</th>
                        <th class="p-4 uppercase tracking-wider text-center">Stock</th>
                        <th class="p-4 uppercase tracking-wider text-center">Status</th>
                        <th class="p-4 uppercase tracking-wider text-center">Visibility</th>
                        <th class="p-4 uppercase tracking-wider text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cyber-border/40 text-sm text-gray-300">
                    @forelse($products as $product)
                        <tr class="hover:bg-cyber-dark/30 transition duration-150">
                            <!-- Image -->
                            <td class="p-4">
                                <div class="w-12 h-12 rounded-lg border border-cyber-border bg-cyber-dark flex items-center justify-center text-gray-500 overflow-hidden flex-shrink-0">
                                    @if($product->image_path)
                                        <img src="{{ asset('storage/' . $product->image_path) }}" class="w-full h-full object-cover" alt="{{ $product->name }}">
                                    @else
                                        <i class="fa-solid fa-box text-xl"></i>
                                    @endif
                                </div>
                            </td>

                            <!-- Product Info -->
                            <td class="p-4">
                                <span class="text-[10px] font-orbitron font-bold text-cyber-cyan tracking-wider uppercase bg-cyber-cyan/5 border border-cyber-cyan/25 px-1.5 py-0.5 rounded">
                                    {{ $product->brand }}
                                </span>
                                <strong class="block text-white mt-1.5 text-base leading-tight">{{ $product->name }}</strong>
                                <span class="text-xs text-gray-500 font-mono">Slug: {{ $product->slug }}</span>
                            </td>

                            <!-- Category -->
                            <td class="p-4 font-semibold">
                                {{ $product->category->name }}
                            </td>

                            <!-- Price -->
                            <td class="p-4 font-bold font-orbitron text-white text-right">
                                Rs. {{ number_format($product->price, 2) }}
                            </td>

                            <!-- Stock -->
                            <td class="p-4 text-center">
                                <span class="font-bold font-orbitron px-2.5 py-1 rounded-md text-xs {{ $product->stock === 0 ? 'bg-red-500/10 border border-red-500/30 text-red-500' : ($product->stock < 5 ? 'bg-amber-500/10 border border-amber-500/30 text-amber-500' : 'bg-cyber-dark border border-cyber-border text-gray-300') }}">
                                    {{ $product->stock }}
                                </span>
                            </td>

                            <!-- Status / Featured -->
                            <td class="p-4 text-center">
                                @if($product->is_featured)
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 text-[10px] font-orbitron font-bold bg-purple-500/10 border border-purple-500/30 text-purple-400 rounded-md">
                                        <span class="w-1.5 h-1.5 rounded-full bg-purple-400 animate-pulse"></span> FEATURED
                                    </span>
                                @else
                                    <span class="text-xs text-gray-500 uppercase tracking-widest font-orbitron">Standard</span>
                                @endif
                            </td>

                            <!-- Visibility Toggle -->
                            <td class="p-4 text-center">
                                <form action="{{ route('admin.products.toggle-visibility', $product->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @if($product->is_visible)
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-orbitron font-bold bg-cyan-500/10 border border-cyber-cyan/30 text-cyber-cyan rounded-md hover:bg-cyan-500/20 transition-all cursor-pointer" title="Click to hide on storefront">
                                            <span class="w-1.5 h-1.5 rounded-full bg-cyber-cyan shadow-neon-cyan animate-pulse"></span> ONLINE
                                        </button>
                                    @else
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-orbitron font-bold bg-gray-800 border border-gray-700 text-gray-500 rounded-md hover:bg-gray-750 transition-all cursor-pointer" title="Click to show on storefront">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span> HIDDEN
                                        </button>
                                    @endif
                                </form>
                            </td>

                            <!-- Actions -->
                            <td class="p-4">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Edit Link -->
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="p-2 border border-cyber-border hover:border-cyber-cyan hover:text-cyber-cyan text-gray-400 rounded-lg transition duration-150" title="Edit Product">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <!-- Delete Button Form -->
                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('DANGER: Delete product module and permanently erase inventory details?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 border border-cyber-border hover:border-red-500/50 hover:text-red-400 text-gray-500 rounded-lg transition duration-150" title="Delete Product">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-gray-500">
                                No products cataloged in system inventory database.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        @if($products->hasPages())
            <div class="p-4 border-t border-cyber-border bg-cyber-dark/40">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
