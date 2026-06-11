@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-slate-800">
        <div>
            <h1 class="orbitron-title text-xl font-black text-slate-100 tracking-wider">EDIT ROLE: {{ $role->name }}</h1>
            <p class="text-slate-400 text-xs mt-0.5 uppercase tracking-widest font-semibold font-sans">Modify role attributes and authorization mappings</p>
        </div>
        <a href="{{ route('roles.index') }}" class="px-4 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-400 hover:text-slate-200 text-xs transition-colors">
            <i class="fa-solid fa-arrow-left mr-2"></i>Back to List
        </a>
    </div>

    <form action="{{ route('roles.update', $role->id) }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- General Info -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 space-y-4">
            <h3 class="orbitron-title text-xs font-black text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2">
                General Profile Details
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-1">
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Role Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $role->name) }}" placeholder="e.g. Sales Executive" required class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                </div>
                <div class="md:col-span-2">
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Description / Notes</label>
                    <input type="text" name="description" value="{{ old('description', $role->description) }}" placeholder="Brief summary of responsibility permissions" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                </div>
            </div>
        </div>

        <!-- Permission Configuration Group -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                <h3 class="orbitron-title text-xs font-black text-cyan-400 uppercase tracking-widest">
                    Configure Permissions Map
                </h3>
                <div class="flex gap-2">
                    <button type="button" onclick="toggleAllCheckboxes(true)" class="text-[9px] bg-slate-800 text-slate-300 font-bold px-2 py-1 rounded hover:bg-slate-750 transition-colors uppercase tracking-widest">Select All</button>
                    <button type="button" onclick="toggleAllCheckboxes(false)" class="text-[9px] bg-slate-800 text-slate-300 font-bold px-2 py-1 rounded hover:bg-slate-750 transition-colors uppercase tracking-widest">Clear All</button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pt-2">
                @foreach($permissions as $module => $modPermissions)
                    <div class="bg-slate-950 border border-slate-850 p-4 rounded-xl space-y-3">
                        <div class="flex justify-between items-center border-b border-slate-900 pb-1.5">
                            <span class="text-xs font-bold text-slate-200 uppercase tracking-wider">{{ $module }}</span>
                            <input type="checkbox" onchange="toggleModuleCheckboxes('{{ Str::slug($module) }}', this.checked)" class="rounded border-slate-800 bg-slate-900 text-cyan-500 focus:ring-cyan-500">
                        </div>
                        
                        <div class="space-y-2" id="module-{{ Str::slug($module) }}">
                            @foreach($modPermissions as $perm)
                                <div class="flex items-center justify-between py-0.5">
                                    <label for="perm-{{ $perm->id }}" class="text-[11px] text-slate-400 font-medium select-none cursor-pointer hover:text-slate-200">
                                        {{ str_replace('-', ' ', $perm->name) }}
                                    </label>
                                    <input type="checkbox" name="permissions[]" value="{{ $perm->id }}" id="perm-{{ $perm->id }}" {{ in_array($perm->id, $rolePermissions) ? 'checked' : '' }} class="rounded border-slate-800 bg-slate-900 text-cyan-500 focus:ring-cyan-500">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end pt-4 border-t border-slate-800">
            <button type="submit" class="px-5 py-2.5 bg-cyan-500 text-slate-950 font-black rounded-lg text-xs uppercase tracking-widest hover:bg-cyan-400 transition-colors shadow-neon-cyan">
                Update Role Configuration
            </button>
        </div>
    </form>
</div>

<script>
function toggleAllCheckboxes(status) {
    const checkboxes = document.querySelectorAll('input[type="checkbox"][name="permissions[]"]');
    checkboxes.forEach(c => c.checked = status);
}

function toggleModuleCheckboxes(slug, status) {
    const container = document.getElementById('module-' + slug);
    if (container) {
        const checkboxes = container.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(c => c.checked = status);
    }
}
</script>
@endsection
