<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Permission::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('module', 'like', "%{$search}%");
        }

        $permissions = $query->latest()->paginate(10);
        return view('permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('permissions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions',
            'module' => 'required|string|max:255',
        ]);

        Permission::create($request->validated());

        return redirect()->route('permissions.index')->with('success', "Permission {$request->name} created successfully.");
    }

    public function edit(Permission $permission)
    {
        return view('permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions')->ignore($permission->id)],
            'module' => 'required|string|max:255',
        ]);

        $permission->update($request->validated());

        return redirect()->route('permissions.index')->with('success', "Permission {$request->name} updated successfully.");
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('permissions.index')->with('success', "Permission deleted successfully.");
    }
}
