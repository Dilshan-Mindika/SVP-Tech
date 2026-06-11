<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $query = Role::withCount('users');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $stats = [
            'total_count' => (clone $query)->count(),
            'total_permissions' => Permission::count(),
            'roles_with_users' => (clone $query)->has('users')->count(),
            'total_assignments' => \App\Models\User::whereNotNull('role_id')->count(),
        ];

        $roles = $query->latest()->paginate(10);
        return view('roles.index', compact('roles', 'stats'));
    }

    public function create()
    {
        $permissions = Permission::all()->groupBy('module');
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        return redirect()->route('roles.index')->with('success', "Role {$request->name} created successfully.");
    }

    public function edit(Role $role)
    {
        // Don't allow editing permissions of Admin super-role directly if desired, but we can allow it
        $permissions = Permission::all()->groupBy('module');
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        } else {
            $role->permissions()->sync([]);
        }

        return redirect()->route('roles.index')->with('success', "Role {$request->name} updated successfully.");
    }

    public function destroy(Role $role)
    {
        // Prevent deleting Admin role
        if ($role->name === 'Admin' || $role->name === 'Super Admin') {
            return back()->withErrors("The Admin role cannot be deleted.");
        }

        // Prevent deleting role if users are assigned to it
        if ($role->users()->exists()) {
            return back()->withErrors("Cannot delete role {$role->name} as it has users assigned to it.");
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', "Role deleted successfully.");
    }
}
