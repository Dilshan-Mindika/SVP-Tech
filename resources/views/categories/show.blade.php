@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex items-center gap-3 pb-4 border-b border-slate-800">
        <a href="{{ route('categories.index') }}" class="p-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-400 hover:text-slate-200 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="orbitron-title text-xl font-black text-slate-100 tracking-wider">CATEGORY PROFILE</h1>
            <p class="text-slate-400 text-xs mt-0.5 uppercase tracking-widest font-semibold font-sans">Details, subcategories hierarchy, and catalog associations</p>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Category details card -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 relative overflow-hidden">
                <!-- Decorative background glow -->
                <div class="absolute -right-16 -top-16 w-32 h-32 bg-cyan-500/10 rounded-full blur-2xl"></div>

                <div class="flex flex-col items-center text-center space-y-4 relative z-10 pb-6 border-b border-slate-800">
                    <div class="h-16 w-16 bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 rounded-2xl flex items-center justify-center text-3xl shadow-neon-cyan/10">
                        <i class="fa-solid {{ $category->icon ?: 'fa-tag' }}"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-100">{{ $category->name }}</h2>
                        <span class="text-xs text-slate-400 mono-text font-semibold select-all mt-1 block">/categories/{{ $category->slug }}</span>
                    </div>
                </div>

                <div class="py-6 space-y-4 border-b border-slate-800 text-xs">
                    <div>
                        <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider block mb-1">Parent Classification</span>
                        @if($category->parent)
                            <a href="{{ route('categories.show', $category->parent) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-950 border border-slate-800 text-cyan-400 hover:text-cyan-300 font-semibold rounded-lg hover:border-cyan-500/30 transition-all">
                                <i class="fa-solid {{ $category->parent->icon ?: 'fa-folder' }} text-[10px]"></i>
                                <span>{{ $category->parent->name }}</span>
                            </a>
                        @else
                            <span class="text-slate-400 italic">None (Root Category)</span>
                        @endif
                    </div>

                    <div>
                        <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider block mb-1">Description</span>
                        <p class="text-slate-300 leading-relaxed">{{ $category->description ?: 'No detailed description available.' }}</p>
                    </div>

                    <div>
                        <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider block mb-1">Products Statistics</span>
                        <div class="grid grid-cols-2 gap-2 mt-1">
                            <div class="bg-slate-950 border border-slate-805 p-2 rounded-lg text-center">
                                <span class="text-[9px] text-slate-500 block">Direct Products</span>
                                <span class="text-sm font-extrabold text-cyan-400 mono-text block mt-0.5">{{ $category->products()->count() }}</span>
                            </div>
                            <div class="bg-slate-950 border border-slate-805 p-2 rounded-lg text-center">
                                <span class="text-[9px] text-slate-500 block">Total (Recursive)</span>
                                <span class="text-sm font-extrabold text-emerald-400 mono-text block mt-0.5">{{ $category->total_products_count }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-6 flex justify-between gap-2">
                    @if(Auth::user()->hasPermission('update-categories'))
                    <a href="{{ route('categories.edit', $category) }}" class="flex-1 px-4 py-2 bg-slate-850 hover:bg-slate-800 text-slate-200 border border-slate-800 hover:border-slate-700 text-center font-bold rounded-lg text-xs transition-colors">
                        EDIT PROFILE
                    </a>
                    @endif
                    @if(Auth::user()->hasPermission('delete-categories'))
                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline flex-1" onsubmit="return confirm('Are you sure you want to delete category \'{{ $category->name }}\'?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-rose-500/10 hover:bg-rose-500 text-rose-500 hover:text-slate-950 border border-rose-500/20 hover:border-rose-500 text-center font-black rounded-lg text-xs uppercase tracking-wider transition-all cursor-pointer">
                            DELETE
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <!-- Subcategories (children) List Card -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-800">
                    <h3 class="orbitron-title text-xs font-bold text-slate-300 uppercase tracking-wider">Subcategories ({{ $category->children()->count() }})</h3>
                    @if(Auth::user()->hasPermission('create-categories'))
                    <a href="{{ route('categories.create', ['parent_id' => $category->id]) }}" class="text-[10px] bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 hover:bg-cyan-500 hover:text-slate-950 px-2 py-1 rounded font-bold transition-all flex items-center gap-1">
                        <i class="fa-solid fa-plus text-[9px]"></i> ADD
                    </a>
                    @endif
                </div>
                <div class="space-y-3">
                    @forelse($category->children as $child)
                        <div class="p-3 bg-slate-950 border border-slate-805 hover:border-cyan-500/30 rounded-lg flex items-center justify-between gap-4 transition-all">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="h-8 w-8 bg-slate-800 text-slate-400 rounded-md flex items-center justify-center shrink-0">
                                    <i class="fa-solid {{ $child->icon ?: 'fa-tag' }} text-[11px]"></i>
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('categories.show', $child) }}" class="text-xs font-bold text-slate-200 hover:text-cyan-400 block truncate">{{ $child->name }}</a>
                                    <span class="text-[9px] text-slate-500 block truncate mt-0.5">{{ $child->description }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <span class="text-[9px] text-slate-500 bg-slate-900 border border-slate-800 rounded px-1.5 py-0.5 mono-text">{{ $child->total_products_count }} items</span>
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('categories.show', $child) }}" class="p-1 hover:text-cyan-400 text-slate-500 transition-colors" title="View subcategory">
                                        <i class="fa-solid fa-eye text-[11px]"></i>
                                    </a>
                                    @if(Auth::user()->hasPermission('update-categories'))
                                    <a href="{{ route('categories.edit', $child) }}" class="p-1 hover:text-emerald-400 text-slate-500 transition-colors" title="Edit subcategory">
                                        <i class="fa-solid fa-pen text-[11px]"></i>
                                    </a>
                                    @endif
                                    @if(Auth::user()->hasPermission('delete-categories'))
                                    <form action="{{ route('categories.destroy', $child) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete subcategory \'{{ $child->name }}\'?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 hover:text-rose-500 text-slate-500 transition-colors cursor-pointer" title="Delete subcategory">
                                            <i class="fa-solid fa-trash-can text-[11px]"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-500 text-xs italic text-center py-2">No nested subcategories logged under this node.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Products table card -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                    <h3 class="orbitron-title text-sm font-bold text-slate-200 tracking-wide uppercase">Products Classification List</h3>
                    <span class="px-2.5 py-1 bg-slate-950 border border-slate-800 rounded text-slate-400 font-semibold text-[10px] mono-text">
                        Showing {{ $products->count() }} of {{ $products->total() }} matching
                    </span>
                </div>

                <div class="overflow-x-auto border border-slate-800/60 rounded-xl">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-widest font-semibold text-[10px] bg-slate-950/40">
                                <th class="py-3 px-4">Image</th>
                                <th class="py-3 px-4">SKU</th>
                                <th class="py-3 px-4">Product Name</th>
                                <th class="py-3 px-4">Category</th>
                                <th class="py-3 px-4 text-right">Selling Price</th>
                                <th class="py-3 px-4 text-center">Stock</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-850">
                            @forelse($products as $prod)
                                <tr class="hover:bg-slate-800/10 transition-colors">
                                    <td class="py-3 px-4">
                                        <img src="{{ asset($prod->image_path ?: 'images/products/default.jpg') }}" alt="{{ $prod->name }}" class="h-8 w-8 object-cover rounded-lg border border-slate-800 bg-slate-950">
                                    </td>
                                    <td class="py-3 px-4 font-bold text-slate-400 uppercase tracking-wider font-mono">{{ $prod->sku }}</td>
                                    <td class="py-3 px-4 font-bold text-slate-200">
                                        <a href="{{ route('products.edit', $prod) }}" class="hover:text-cyan-400 transition-colors">
                                            {{ $prod->name }}
                                        </a>
                                    </td>
                                    <td class="py-3 px-4 text-slate-400">
                                        <span class="px-2 py-0.5 bg-slate-800 border border-slate-700/80 rounded text-slate-300 text-[10px]">
                                            {{ $prod->category->name }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-right font-semibold text-emerald-400 mono-text">Rs. {{ number_format($prod->price, 2) }}</td>
                                    <td class="py-3 px-4 text-center">
                                        @if($prod->stock <= 0)
                                            <span class="px-2 py-0.5 bg-rose-500/10 border border-rose-500/20 text-rose-500 text-[9px] font-bold rounded uppercase tracking-wider">Out of Stock</span>
                                        @elseif($prod->stock < 5)
                                            <span class="px-2 py-0.5 bg-amber-500/10 border border-amber-500/20 text-amber-500 text-[9px] font-bold rounded uppercase tracking-wider">Low: {{ $prod->stock }}</span>
                                        @else
                                            <span class="px-2 py-0.5 bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[9px] font-bold rounded uppercase tracking-wider">{{ $prod->stock }} Units</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 text-center text-slate-500">
                                        No products logged in this category or subcategories yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($products->hasPages())
                    <div class="mt-4">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
