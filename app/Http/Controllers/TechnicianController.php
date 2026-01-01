<?php

namespace App\Http\Controllers;

use App\Models\Technician;
use App\Models\User;
use Illuminate\Http\Request;

class TechnicianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Technician::with('user')
            ->withCount(['repairJobs as completed_jobs_count' => function ($query) {
                $query->whereIn('repair_status', ['completed', 'delivered']);
            }]);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%")
                       ->orWhere('phone', 'like', "%{$search}%");
                })
                ->orWhere('specialty', 'like', "%{$search}%");
            });
        }

        $technicians = $query->get();
        return view('technicians.index', compact('technicians'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('technicians.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,technician',
            'specialty' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'], // Ensure this is mapped correctly
            'phone' => $request->phone,
        ]);

        if ($validated['role'] === 'technician') {
            Technician::create([
                'user_id' => $user->id,
                'specialty' => $validated['specialty'],
                'total_jobs' => 0,
                'performance_score' => 0,
            ]);
        }

        return redirect()->route('technicians.index')->with('success', 'Technician/User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Technician $technician)
    {
        return view('technicians.show', compact('technician'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Technician $technician)
    {
        return view('technicians.edit', compact('technician'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Technician $technician)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $technician->user_id,
            'password' => 'nullable|min:6',
            'role' => 'required|in:admin,technician',
            'specialty' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role' => $validated['role'],
        ];

        if ($request->filled('password')) {
            $userData['password'] = bcrypt($validated['password']);
        }

        $technician->user->update($userData);

        $technician->update([
            'specialty' => $validated['specialty'],
        ]);

        return redirect()->route('technicians.index')->with('success', 'Technician updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Technician $technician)
    {
        $technician->user->delete(); // Delete user account
        $technician->delete();
        return redirect()->route('technicians.index')->with('success', 'Technician deleted successfully.');
    }
}
