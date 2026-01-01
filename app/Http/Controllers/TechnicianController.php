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
    public function index()
    {
        $technicians = Technician::with('user')->get();
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
        // Validation and creation logic here
        // Need to create User first, then Technician
        return redirect()->route('technicians.index');
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
        // Update logic
        return redirect()->route('technicians.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Technician $technician)
    {
        $technician->user->delete(); // Delete user account
        $technician->delete();
        return redirect()->route('technicians.index');
    }
}
