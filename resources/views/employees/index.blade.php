@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="orbitron-title text-2xl font-black text-slate-100 tracking-wider">EMPLOYEES</h1>
            <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest font-semibold font-sans">Manage employees, jobs, and system account links</p>
        </div>
        <div class="flex flex-wrap gap-2 items-center">
            <!-- Import Excel/CSV -->
            <button onclick="showImportModal('employees', 'Employees', [
                {name: 'Full Name', type: 'string', required: true, desc: 'Employee full name'},
                {name: 'Designation', type: 'string', required: true, desc: 'Job role designation'},
                {name: 'Basic Salary', type: 'numeric', required: true, desc: 'Basic monthly salary in Rs.'},
                {name: 'Phone', type: 'string', required: true, desc: 'Employee personal contact phone'},
                {name: 'Email', type: 'string', required: true, desc: 'Work email address (must be unique)'},
                {name: 'Joining Date', type: 'date', required: true, desc: 'Joining Date (YYYY-MM-DD)'},
                {name: 'Status', type: 'string', required: false, desc: 'Employment status (active or inactive)'}
            ])" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-import text-cyan-400"></i>
                <span>IMPORT</span>
            </button>
            
            <!-- Export Excel -->
            <button onclick="exportExcel('employees')" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-excel text-emerald-400"></i>
                <span>EXPORT EXCEL</span>
            </button>

            <!-- Export PDF -->
            <button onclick="exportPDF('employees')" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-pdf text-rose-400"></i>
                <span>EXPORT PDF</span>
            </button>

            <a href="{{ route('employees.create') }}" class="px-3.5 py-2 bg-cyan-500 text-slate-950 font-bold rounded-lg text-xs transition-all hover:bg-cyan-400 shadow-neon-cyan hover:shadow-neon-cyan-lg flex items-center gap-1.5">
                <i class="fa-solid fa-user-plus"></i>
                <span>ADD NEW EMPLOYEE</span>
            </a>
        </div>
    </div>

    <!-- Statistical KPI Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #22d3ee; opacity: 0.15;">
                <i class="fa-solid fa-user-tie"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Total Staff</span>
            <h3 class="text-xl font-extrabold text-cyan-400 mt-1 mono-text">{{ $stats['total_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Total employees logged</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #34d399; opacity: 0.15;">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Active Staff</span>
            <h3 class="text-xl font-extrabold text-emerald-400 mt-1 mono-text">{{ $stats['active_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Currently active personnel</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #34d399; opacity: 0.15;">
                <i class="fa-solid fa-coins"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Monthly Payroll</span>
            <h3 class="text-xl font-extrabold text-emerald-400 mt-1 mono-text">Rs. {{ number_format($stats['monthly_payroll'], 2) }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Basic payroll commitments</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #94a3b8; opacity: 0.15;">
                <i class="fa-solid fa-id-card-clip"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Designations</span>
            <h3 class="text-xl font-extrabold text-slate-200 mt-1 mono-text">{{ $stats['designations_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Unique roles & departments</span>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
        <form action="{{ route('employees.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="flex-grow w-full md:max-w-md relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, designation, phone..." class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg pl-9 pr-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
            </div>
            <button type="submit" class="w-full md:w-auto px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-lg transition-colors">
                Search Profiles
            </button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-widest font-semibold text-[10px]">
                        <th class="py-3.5 px-6">Employee</th>
                        <th class="py-3.5 px-6">Designation</th>
                        <th class="py-3.5 px-6">Account Link</th>
                        <th class="py-3.5 px-6">Contact Info</th>
                        <th class="py-3.5 px-6 text-right">Base Salary</th>
                        <th class="py-3.5 px-6 text-center">Joining Date</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-850">
                    @forelse($employees as $emp)
                        <tr class="hover:bg-slate-850/20 transition-colors">
                            <!-- ID & Name -->
                            <td class="py-3.5 px-6">
                                <div class="font-bold text-slate-200 tracking-wider">
                                    {{ $emp->name }}
                                </div>
                                <div class="text-[10px] text-slate-500 font-mono mt-0.5">ID: EMP-#{{ str_pad($emp->id, 4, '0', STR_PAD_LEFT) }}</div>
                            </td>
                            <!-- Designation -->
                            <td class="py-3.5 px-6">
                                <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-black bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                                    {{ $emp->designation }}
                                </span>
                            </td>
                            <!-- User Account Link -->
                            <td class="py-3.5 px-6">
                                @if($emp->user)
                                    <div class="flex items-center gap-2">
                                        <span class="h-1.5 w-1.5 rounded-full bg-cyan-400"></span>
                                        <span class="font-medium text-slate-300 font-mono">{{ $emp->user->name }}</span>
                                        <span class="text-[10px] text-slate-500 italic font-sans">({{ $emp->user->email }})</span>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2 text-amber-500/80">
                                        <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                                        <span class="text-[10px] uppercase font-bold tracking-wider">No Account</span>
                                    </div>
                                @endif
                            </td>
                            <!-- Contact Info -->
                            <td class="py-3.5 px-6">
                                <div class="text-slate-300 font-medium">{{ $emp->phone ?? 'N/A' }}</div>
                                <div class="text-[10px] text-slate-500 mt-0.5">{{ $emp->email ?? 'No Email Address' }}</div>
                            </td>
                            <!-- Salary -->
                            <td class="py-3.5 px-6 text-right font-bold text-slate-200 mono-text text-sm">
                                Rs. {{ number_format($emp->salary_amount, 2) }}
                            </td>
                            <!-- Joining Date -->
                            <td class="py-3.5 px-6 text-center text-slate-400 font-mono">
                                {{ \Carbon\Carbon::parse($emp->joining_date)->format('Y-m-d') }}
                            </td>
                            <!-- Status -->
                            <td class="py-3.5 px-6 text-center">
                                @if($emp->status === 'active')
                                    <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        Active
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <!-- Actions -->
                            <td class="py-3.5 px-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('employees.edit', $emp->id) }}" class="p-1.5 bg-slate-850 hover:bg-slate-800 text-slate-300 rounded hover:text-cyan-400 border border-slate-800 transition-all shadow" title="Edit Profile">
                                        <i class="fa-solid fa-user-gear"></i>
                                    </a>
                                    <form action="{{ route('employees.destroy', $emp->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this employee profile?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-slate-850 hover:bg-slate-800 text-slate-500 hover:text-rose-500 rounded border border-slate-800 transition-all shadow" title="Delete Profile">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-600">
                                <i class="fa-solid fa-user-slash text-2xl mb-2 block opacity-40"></i>
                                <span>No employees found.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($employees->hasPages())
            <div class="px-6 py-4 border-t border-slate-800 bg-slate-900/60">
                {{ $employees->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
