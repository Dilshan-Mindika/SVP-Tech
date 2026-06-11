<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roleRelation');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('role', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }

        $stats = [
            'total_count' => (clone $query)->count(),
            'admin_count' => (clone $query)->whereHas('roleRelation', function($q) { $q->where('name', 'like', '%admin%'); })->count(),
            'staff_count' => (clone $query)->whereHas('roleRelation', function($q) { $q->where('name', 'not like', '%admin%'); })->count(),
            'roles_count' => Role::count(),
        ];

        $users = $query->latest()->paginate(10);
        return view('users.index', compact('users', 'stats'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('users.index')->with('success', "User {$request->name} registered successfully.");
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
        ];

        if (!empty($request->password)) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', "User {$request->name} updated successfully.");
    }

    public function destroy(User $user)
    {
        // Prevent deleting oneself
        if (auth()->id() === $user->id) {
            return back()->withErrors("You cannot delete your own user account.");
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', "User deleted successfully.");
    }
}
