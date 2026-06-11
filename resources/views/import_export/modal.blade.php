<!-- Reusable Cyber Import Modal -->
<div id="cyberImportModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm transition-all duration-300">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-2xl overflow-hidden shadow-2xl relative drop-shadow-[0_0_20px_rgba(0,227,253,0.15)] flex flex-col max-h-[90vh]">
        
        <!-- Header -->
        <div class="border-b border-slate-850 p-5 flex items-center justify-between bg-slate-900/50">
            <div>
                <h3 id="importModalTitle" class="orbitron-title text-base font-black text-cyan-400 tracking-wider">IMPORT RECORDS</h3>
                <p id="importModalSubtitle" class="text-[10px] text-slate-400 mt-1 uppercase font-semibold font-sans">Mainframe spreadsheet data injector</p>
            </div>
            <button onclick="closeImportModal()" class="text-slate-400 hover:text-slate-200 transition-colors">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Scrollable Body Content -->
        <div class="p-6 overflow-y-auto flex-grow space-y-5 text-xs text-slate-300">
            <!-- Alert Instructions -->
            <div class="bg-cyan-500/5 border border-cyan-500/20 rounded-xl p-4 flex gap-3">
                <div class="text-cyan-400 mt-0.5">
                    <i class="fa-solid fa-circle-info text-base"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-200 uppercase tracking-wider text-[10px]">Data Layout Instructions</h4>
                    <p class="mt-1 text-slate-400 leading-relaxed">
                        Please upload a <strong>CSV</strong> or <strong>Excel (.xlsx / .xls)</strong> file. Ensure your spreadsheet contains headers matching the exact column names specified in the table below. Values must satisfy the specified validation constraints to avoid database insertion errors.
                    </p>
                </div>
            </div>

            <!-- Expected Schema Columns Description -->
            <div>
                <h4 class="font-bold text-slate-200 uppercase tracking-wider text-[10px] mb-2.5">Expected Column Schema</h4>
                <div class="bg-slate-950 border border-slate-850 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-[11px] border-collapse">
                        <thead>
                            <tr class="bg-slate-900 border-b border-slate-850 text-slate-400 uppercase tracking-widest font-semibold text-[9px]">
                                <th class="py-2.5 px-4">Column Header</th>
                                <th class="py-2.5 px-2">Type</th>
                                <th class="py-2.5 px-2">Required</th>
                                <th class="py-2.5 px-4">Description</th>
                            </tr>
                        </thead>
                        <tbody id="importSchemaTableBody" class="divide-y divide-slate-900 text-slate-300">
                            <!-- Dynamic Schema Rows Filled by JS -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Download Sample and Dropzone Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- Left: Download Template Card -->
                <div class="bg-slate-950 border border-slate-850 rounded-xl p-4 flex flex-col justify-between items-center text-center">
                    <div class="text-amber-500/80 my-1 flex gap-2 justify-center">
                        <i class="fa-solid fa-file-csv text-2xl"></i>
                        <i class="fa-solid fa-file-excel text-2xl text-emerald-500"></i>
                    </div>
                    <div>
                        <span class="block font-bold text-slate-200 uppercase text-[9px] tracking-wider">Sample Template</span>
                        <p class="text-[10px] text-slate-500 mt-1">Get a sample CSV or Excel spreadsheet filled with format templates.</p>
                    </div>
                    <div class="w-full space-y-2 mt-3">
                        <a id="importSampleDownloadBtn" href="#" class="block w-full py-1.5 bg-slate-900 hover:bg-slate-850 border border-slate-800 text-slate-300 hover:text-cyan-400 rounded-lg text-[9px] font-bold tracking-wider transition-all text-center">
                            <i class="fa-solid fa-file-csv mr-1 text-amber-500"></i> CSV TEMPLATE
                        </a>
                        <a id="importSampleExcelBtn" href="#" class="block w-full py-1.5 bg-slate-900 hover:bg-slate-850 border border-slate-800 text-slate-300 hover:text-cyan-400 rounded-lg text-[9px] font-bold tracking-wider transition-all text-center">
                            <i class="fa-solid fa-file-excel mr-1 text-emerald-500"></i> EXCEL TEMPLATE
                        </a>
                    </div>
                </div>

                <!-- Right: File Upload Dropzone -->
                <div class="md:col-span-2 bg-slate-950 border-2 border-dashed border-slate-800 rounded-xl p-6 flex flex-col items-center justify-center text-center cursor-pointer hover:border-cyan-500/60 transition-colors group relative" id="importDropzone">
                    <input type="file" id="importFileInput" class="absolute inset-0 opacity-0 cursor-pointer" accept=".csv, .xlsx, .xls">
                    <div class="text-cyan-400 group-hover:scale-105 transition-transform duration-200 mb-2">
                        <i class="fa-solid fa-cloud-arrow-up text-3xl"></i>
                    </div>
                    <span class="block font-bold text-slate-200 text-[10px] uppercase tracking-wider">Drag & Drop File Here</span>
                    <span class="block text-[10px] text-slate-500 mt-1">Accepts CSV, XLSX, XLS spreadsheets up to 10MB</span>
                    <span id="selectedFileName" class="mt-2 text-cyan-400 font-mono text-[10px] font-semibold hidden"></span>
                </div>
            </div>

            <!-- Console Log Output Box -->
            <div id="importConsoleBox" class="hidden">
                <h4 class="font-bold text-slate-200 uppercase tracking-wider text-[9px] mb-1.5 flex items-center justify-between">
                    <span>Mainframe Injector Console Logs</span>
                    <button onclick="clearImportLogs()" class="text-slate-500 hover:text-slate-300 text-[8px] uppercase tracking-widest font-black transition-colors"><i class="fa-solid fa-eraser mr-1"></i> Clear</button>
                </h4>
                <div id="importConsoleOutput" class="bg-slate-950 border border-slate-850 p-4 rounded-xl font-mono text-[10px] text-slate-400 max-h-36 overflow-y-auto leading-relaxed space-y-1 scrollbar-thin">
                    <!-- Logs populated here -->
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="border-t border-slate-850 p-4 bg-slate-900/50 flex justify-end gap-3">
            <button onclick="closeImportModal()" class="px-4 py-2 border border-slate-800 hover:border-slate-700 text-slate-300 text-xs font-bold rounded-lg transition-colors">
                Cancel
            </button>
            <button id="importSubmitBtn" onclick="processImportFile()" disabled class="px-5 py-2 bg-slate-800 text-slate-500 cursor-not-allowed font-bold rounded-lg text-xs tracking-wider transition-all flex items-center gap-2">
                <i class="fa-solid fa-bolt"></i>
                <span>INJECT RECORDS</span>
            </button>
        </div>
    </div>
</div>
