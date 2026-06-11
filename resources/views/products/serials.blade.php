@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="orbitron-title text-2xl font-black text-slate-100 tracking-wider">SERIALS REGISTRY</h1>
            <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest font-semibold font-sans">Unique Product Serial Number Directory & Lifecycle Status</p>
        </div>
        <div class="flex flex-wrap gap-2 items-center">
            <!-- Import Excel/CSV -->
            <button onclick="showImportModal('serials', 'Product Serials', [
                {name: 'Product SKU', type: 'string', required: true, desc: 'Catalog SKU of the product'},
                {name: 'Serial Number', type: 'string', required: true, desc: 'Physical serial number of the unit'},
                {name: 'Status', type: 'string', required: false, desc: 'Availability status (in_stock, sold, returned)'}
            ])" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-import text-cyan-400"></i>
                <span>IMPORT</span>
            </button>

            <!-- Export Excel -->
            <button onclick="exportExcel('serials')" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-excel text-emerald-400"></i>
                <span>EXPORT EXCEL</span>
            </button>

            <!-- Export PDF -->
            <button onclick="exportPDF('serials')" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-pdf text-rose-400"></i>
                <span>EXPORT PDF</span>
            </button>

            <a href="{{ route('products.index') }}" class="px-3.5 py-2 bg-slate-850 hover:bg-slate-800 text-slate-200 font-bold rounded-lg text-xs transition-colors flex items-center gap-1.5 border border-slate-700">
                <i class="fa-solid fa-boxes-stacked"></i>
                <span>BACK TO INVENTORY</span>
            </a>
        </div>
    </div>

    <!-- Statistical KPI Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #22d3ee; opacity: 0.15;">
                <i class="fa-solid fa-barcode"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Total Serials</span>
            <h3 class="text-xl font-extrabold text-cyan-400 mt-1 mono-text">{{ $stats['total_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Unique barcodes registered</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #34d399; opacity: 0.15;">
                <i class="fa-solid fa-box"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">In Stock</span>
            <h3 class="text-xl font-extrabold text-emerald-400 mt-1 mono-text">{{ $stats['in_stock_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Available for billing</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #60a5fa; opacity: 0.15;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Sold Out</span>
            <h3 class="text-xl font-extrabold text-blue-400 mt-1 mono-text">{{ $stats['sold_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Billed & delivered items</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #f43f5e; opacity: 0.15;">
                <i class="fa-solid fa-rotate-left"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Returned</span>
            <h3 class="text-xl font-extrabold text-rose-500 mt-1 mono-text">{{ $stats['returned_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Returned/Warranty items</span>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
        <form action="{{ route('products.serials') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="flex-grow w-full md:max-w-md relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Serial Number, Product Name or SKU..." class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg pl-9 pr-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
            </div>

            <div class="flex flex-wrap gap-3 w-full md:w-auto">
                <!-- Status Filter -->
                <select name="status" onchange="this.form.submit()" class="bg-slate-950 border border-slate-800 text-slate-300 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                    <option value="all">All Statuses</option>
                    <option value="in_stock" {{ request('status') === 'in_stock' ? 'selected' : '' }}>In Stock</option>
                    <option value="sold" {{ request('status') === 'sold' ? 'selected' : '' }}>Sold</option>
                    <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Returned</option>
                    <option value="repairing" {{ request('status') === 'repairing' ? 'selected' : '' }}>Under Repair</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-lg transition-colors">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Table List -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-widest font-semibold text-[10px]">
                        <th class="py-3.5 px-6">Serial Number</th>
                        <th class="py-3.5 px-6">Product Model</th>
                        <th class="py-3.5 px-6">Product SKU</th>
                        <th class="py-3.5 px-6">Warranty Status</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6">Date Logged</th>
                        <th class="py-3.5 px-6 text-center">Label</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-850">
                    @forelse($serials as $serial)
                        <tr class="hover:bg-slate-800/10 transition-colors">
                            <td class="py-3.5 px-6 font-bold text-cyan-400 tracking-wider uppercase mono-text text-sm">
                                {{ $serial->serial_number }}
                            </td>
                            <td class="py-3.5 px-6 font-bold text-slate-200">
                                {{ $serial->product->name }}
                            </td>
                            <td class="py-3.5 px-6 text-slate-400 uppercase tracking-wider mono-text">
                                {{ $serial->product->sku }}
                            </td>
                            <td class="py-3.5 px-6 text-slate-300">
                                {{ $serial->product->warranty_months }} Months Coverage
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                @if($serial->status === 'in_stock')
                                    <span class="px-2.5 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        In Stock
                                    </span>
                                @elseif($serial->status === 'sold')
                                    <span class="px-2.5 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                        Sold
                                    </span>
                                @elseif($serial->status === 'returned')
                                    <span class="px-2.5 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                        Returned
                                    </span>
                                @elseif($serial->status === 'repairing')
                                    <span class="px-2.5 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                        Repairing
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-slate-500/10 text-slate-400 border border-slate-500/20">
                                        {{ $serial->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6 text-slate-400 mono-text">
                                {{ $serial->created_at ? $serial->created_at->format('Y-m-d H:i') : 'N/A' }}
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                <button onclick="openLabelModal('{{ $serial->serial_number }}', '{{ addslashes($serial->product->name) }}', '{{ $serial->product->sku }}')" class="p-1.5 hover:text-cyan-400 text-slate-500 transition-colors cursor-pointer" title="Generate Barcode / QR Label">
                                    <i class="fa-solid fa-qrcode text-sm"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-600">
                                <i class="fa-solid fa-barcode text-2xl mb-2 block opacity-40"></i>
                                <span>No serial numbers registered in the system database.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($serials->hasPages())
            <div class="px-6 py-4 border-t border-slate-800 bg-slate-900/60">
                {{ $serials->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Label Print Modal -->
<div id="labelModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm hidden">
    <div class="bg-slate-900 border border-slate-800 rounded-xl max-w-sm w-full p-6 space-y-6 relative">
        <button onclick="closeLabelModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-205 transition-colors text-sm cursor-pointer">
            <i class="fa-solid fa-xmark"></i>
        </button>
        
        <div class="text-center">
            <h3 class="orbitron-title text-sm font-bold text-slate-200 uppercase tracking-wider">UNIT IDENTITY LABEL</h3>
            <p class="text-slate-500 text-[9px] uppercase tracking-widest mt-0.5">Generate scannable inventory stickers</p>
        </div>
        
        <!-- Label Preview Card (Will be printed) -->
        <div id="printLabelArea" class="bg-white text-black p-4 rounded-lg flex flex-col items-center justify-center space-y-3 shadow-inner border border-slate-200 mx-auto select-none" style="width: 280px; height: 180px;">
            <div class="text-center" style="width: 100%;">
                <span class="text-[10px] font-black uppercase tracking-wider block truncate text-slate-800" id="lblProdName">Product Name</span>
                <span class="text-[8px] font-bold text-slate-500 block truncate mt-0.5">SKU: <span id="lblProdSku" class="font-mono">SKU-CODE</span></span>
            </div>
            
            <!-- Barcode Render -->
            <div class="flex justify-center" style="width: 100%;">
                <svg id="barcodeCanvas" style="max-width: 100%; height: 50px;"></svg>
            </div>
            
            <div class="flex items-center justify-between gap-4" style="width: 100%; padding-top: 2px;">
                <!-- QR Code Render -->
                <canvas id="qrCanvas" style="width: 45px; height: 45px;"></canvas>
                <div class="text-right" style="flex: 1; min-w: 0;">
                    <span class="text-[7px] font-extrabold text-slate-400 uppercase block tracking-widest">Serial Number</span>
                    <span class="text-[9px] font-black tracking-wider block font-mono text-slate-900 truncate" id="lblSerialVal">SERIAL-NO</span>
                </div>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="flex justify-end gap-3 pt-2">
            <button onclick="closeLabelModal()" class="px-3.5 py-1.5 bg-slate-800 hover:bg-slate-750 text-slate-300 font-bold rounded-lg text-xs transition-colors cursor-pointer">
                CLOSE
            </button>
            <button onclick="printLabel()" class="px-4 py-1.5 bg-cyan-500 text-slate-950 font-black rounded-lg text-xs uppercase tracking-widest transition-all hover:bg-cyan-400 shadow-neon-cyan flex items-center gap-1 cursor-pointer">
                <i class="fa-solid fa-print"></i>
                <span>PRINT LABEL</span>
            </button>
        </div>
    </div>
</div>

<!-- JS Barcode and QR CDNs -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>

<style>
@media print {
    /* Hide everything except the print label area */
    body * {
        visibility: hidden;
        background: none !important;
    }
    #printLabelArea, #printLabelArea * {
        visibility: visible;
    }
    #printLabelArea {
        position: fixed;
        left: 0;
        top: 0;
        width: 50mm !important;
        height: 30mm !important;
        padding: 2mm !important;
        margin: 0 !important;
        border: none !important;
        box-shadow: none !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        align-items: center !important;
        color: black !important;
        background-color: white !important;
    }
    /* Scale elements to fit sticker */
    #lblProdName {
        font-size: 6pt !important;
        line-height: 1.1 !important;
    }
    #lblProdSku {
        font-size: 5pt !important;
    }
    #barcodeCanvas {
        width: 100% !important;
        height: 10mm !important;
        margin-top: 1mm !important;
        margin-bottom: 1mm !important;
    }
    #qrCanvas {
        width: 8mm !important;
        height: 8mm !important;
    }
    #lblSerialVal {
        font-size: 6pt !important;
    }
}
</style>

<script>
function openLabelModal(serial, productName, sku) {
    document.getElementById('lblProdName').textContent = productName;
    document.getElementById('lblProdSku').textContent = sku;
    document.getElementById('lblSerialVal').textContent = serial;
    
    // Generate Barcode
    try {
        JsBarcode("#barcodeCanvas", serial, {
            format: "CODE128",
            lineColor: "#000",
            width: 1.5,
            height: 35,
            displayValue: false,
            margin: 0
        });
    } catch (err) {
        console.error("Barcode generation failed", err);
    }
    
    // Generate QR Code
    try {
        const qrCanvas = document.getElementById('qrCanvas');
        const qr = new QRious({
            element: qrCanvas,
            value: serial,
            size: 100,
            background: 'white',
            foreground: 'black',
            level: 'M'
        });
    } catch (err) {
        console.error("QR Code generation failed", err);
    }
    
    document.getElementById('labelModal').classList.remove('hidden');
}

function closeLabelModal() {
    document.getElementById('labelModal').classList.add('hidden');
}

function printLabel() {
    window.print();
}
</script>
@endsection
