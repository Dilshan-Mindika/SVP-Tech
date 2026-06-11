@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="orbitron-title text-2xl font-black text-slate-100 tracking-wider">PRODUCTS</h1>
            <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest font-semibold font-sans">Manage product catalog, prices, and stock levels</p>
        </div>
        <div class="flex flex-wrap gap-2 items-center">
            <!-- Import Excel/CSV -->
            <button onclick="showImportModal('products', 'Products', [
                {name: 'Product Name', type: 'string', required: true, desc: 'Full name of the product'},
                {name: 'Category', type: 'string', required: true, desc: 'Category name (will find or create)'},
                {name: 'Brand', type: 'string', required: true, desc: 'Brand/Manufacturer name'},
                {name: 'SKU', type: 'string', required: true, desc: 'Stock Keeping Unit (must be unique)'},
                {name: 'Buying Price', type: 'numeric', required: true, desc: 'Unit cost price in Rs.'},
                {name: 'Selling Price', type: 'numeric', required: true, desc: 'Unit retail price in Rs.'},
                {name: 'Stock Level', type: 'integer', required: true, desc: 'Initial stock count'},
                {name: 'Warranty Months', type: 'integer', required: false, desc: 'Warranty duration in months'},
                {name: 'Description', type: 'string', required: false, desc: 'Detailed product specifications/notes'}
            ])" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-import text-cyan-400"></i>
                <span>IMPORT</span>
            </button>

            <!-- Export Excel -->
            <button onclick="exportExcel('products')" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-excel text-emerald-400"></i>
                <span>EXPORT EXCEL</span>
            </button>

            <!-- Export PDF -->
            <button onclick="exportPDF('products')" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-pdf text-rose-400"></i>
                <span>EXPORT PDF</span>
            </button>

            <a href="{{ route('products.serials') }}" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-705 text-slate-200 font-bold rounded-lg text-xs transition-colors flex items-center gap-1.5 border border-slate-700">
                <i class="fa-solid fa-barcode"></i>
                <span>SERIALS</span>
            </a>
            <a href="{{ route('products.create') }}" class="px-3.5 py-2 bg-cyan-500 text-slate-950 font-bold rounded-lg text-xs transition-all hover:bg-cyan-400 shadow-neon-cyan hover:shadow-neon-cyan-lg flex items-center gap-1.5">
                <i class="fa-solid fa-plus"></i>
                <span>ADD PRODUCT</span>
            </a>
        </div>
    </div>

    <!-- Statistical KPI Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #22d3ee; opacity: 0.15;">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Total Products</span>
            <h3 class="text-xl font-extrabold text-cyan-400 mt-1 mono-text">{{ $stats['total_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Catalog items matching</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #34d399; opacity: 0.15;">
                <i class="fa-solid fa-coins"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Stock Value (Retail)</span>
            <h3 class="text-xl font-extrabold text-emerald-400 mt-1 mono-text">Rs. {{ number_format($stats['total_stock_value'], 2) }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Total valuation of stock</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #f59e0b; opacity: 0.15;">
                <i class="fa-solid fa-circle-exclamation text-amber-500/20"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Low Stock</span>
            <h3 class="text-xl font-extrabold text-amber-400 mt-1 mono-text">{{ $stats['low_stock_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Items with < 5 stock</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #f43f5e; opacity: 0.15;">
                <i class="fa-solid fa-triangle-exclamation text-rose-500/20"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Out of Stock</span>
            <h3 class="text-xl font-extrabold text-rose-500 mt-1 mono-text">{{ $stats['out_of_stock_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Empty inventory count</span>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
        <form action="{{ route('products.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="flex-grow w-full md:max-w-md relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Name, SKU or Brand..." class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg pl-9 pr-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
            </div>

            <div class="flex flex-wrap gap-3 w-full md:w-auto">
                <!-- Category filter -->
                <select name="category" onchange="this.form.submit()" class="bg-slate-950 border border-slate-800 text-slate-300 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                    <option value="all">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>

                <!-- Stock filter -->
                <select name="stock_filter" onchange="this.form.submit()" class="bg-slate-950 border border-slate-800 text-slate-300 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                    <option value="all">All Statuses</option>
                    <option value="low" {{ request('stock_filter') === 'low' ? 'selected' : '' }}>Low Stock (< 5)</option>
                    <option value="out" {{ request('stock_filter') === 'out' ? 'selected' : '' }}>Out of Stock (0)</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-lg transition-colors">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-widest font-semibold text-[10px]">
                        <th class="py-3.5 px-6">Image</th>
                        <th class="py-3.5 px-6">SKU</th>
                        <th class="py-3.5 px-6">Product</th>
                        <th class="py-3.5 px-6">Category</th>
                        <th class="py-3.5 px-6 text-right">Cost Price</th>
                        <th class="py-3.5 px-6 text-right">Sale Price</th>
                        <th class="py-3.5 px-6 text-center">Warranty</th>
                        <th class="py-3.5 px-6 text-center">Stock</th>
                        <th class="py-3.5 px-6 text-center">Visibility</th>
                        <th class="py-3.5 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-850">
                    @forelse($products as $prod)
                        <tr class="hover:bg-slate-800/10 transition-colors">
                            <td class="py-3.5 px-6">
                                <img src="{{ asset($prod->image_path ?: 'images/products/default.jpg') }}" alt="{{ $prod->name }}" class="h-10 w-10 object-cover rounded-lg border border-slate-800 bg-slate-950">
                            </td>
                            <td class="py-3.5 px-6 font-bold text-slate-400 tracking-wider uppercase">{{ $prod->sku }}</td>
                            <td class="py-3.5 px-6 max-w-xs">
                                <span class="font-bold text-slate-200 block truncate">{{ $prod->name }}</span>
                                <span class="text-[10px] text-slate-500 block truncate mt-0.5">{{ Str::limit($prod->description, 50) }}</span>
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="text-slate-300 font-semibold block">{{ $prod->brand }}</span>
                                <span class="text-[10px] text-slate-500 block mt-0.5">{{ $prod->category->name }}</span>
                            </td>
                            <td class="py-3.5 px-6 text-right text-slate-400 mono-text">Rs. {{ number_format($prod->buying_price, 2) }}</td>
                            <td class="py-3.5 px-6 text-right text-cyan-400 font-bold mono-text">Rs. {{ number_format($prod->price, 2) }}</td>
                            <td class="py-3.5 px-6 text-center text-slate-300 font-medium">{{ $prod->warranty_months }} months</td>
                            <td class="py-3.5 px-6 text-center">
                                @if($prod->stock == 0)
                                    <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                        Out of Stock
                                    </span>
                                @elseif($prod->stock < 5)
                                    <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                        Low Stock ({{ $prod->stock }})
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        In Stock ({{ $prod->stock }})
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                <form action="{{ route('products.toggle-visibility', $prod->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @if($prod->is_visible)
                                        <button type="submit" class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-cyan-500/15 text-cyan-400 border border-cyan-500/30 hover:bg-cyan-500/25 transition-all flex items-center gap-1 mx-auto cursor-pointer" title="Click to hide from customer storefront">
                                            <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span> Online
                                        </button>
                                    @else
                                        <button type="submit" class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-slate-800 text-slate-500 border border-slate-700 hover:bg-slate-705 transition-all flex items-center gap-1 mx-auto cursor-pointer" title="Click to show on customer storefront">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span> Hidden
                                        </button>
                                    @endif
                                </form>
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('products.edit', $prod->id) }}" class="p-1.5 bg-slate-850 hover:bg-slate-800 text-slate-300 rounded hover:text-cyan-400 transition-all" title="Edit details">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('products.destroy', $prod->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this product? All stock values will be removed.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-slate-850 hover:bg-slate-800 text-slate-300 hover:text-rose-500 rounded transition-all" title="Delete Product">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-600">
                                <i class="fa-solid fa-boxes-stacked text-2xl mb-2 block opacity-40"></i>
                                <span>No products found.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
            <div class="px-6 py-4 border-t border-slate-800 bg-slate-900/60">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
