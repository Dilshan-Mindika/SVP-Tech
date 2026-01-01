<?php

namespace App\Http\Controllers;

use App\Models\RepairJob;
use Illuminate\Http\Request;

class RepairJobController extends Controller
{
    public function index()
    {
        $jobs = RepairJob::with(['customer', 'technician'])->latest()->get();
        return view('repair_jobs.index', compact('jobs'));
    }

    public function create()
    {
        return view('repair_jobs.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('repair-jobs.index');
    }

    public function show(RepairJob $repairJob)
    {
        return view('repair_jobs.show', compact('repairJob'));
    }

    public function edit(RepairJob $repairJob)
    {
        return view('repair_jobs.edit', compact('repairJob'));
    }

    public function update(Request $request, RepairJob $repairJob)
    {
        return redirect()->route('repair-jobs.index');
    }

    public function destroy(RepairJob $repairJob)
    {
        $repairJob->delete();
        return redirect()->route('repair-jobs.index');
    }
}
