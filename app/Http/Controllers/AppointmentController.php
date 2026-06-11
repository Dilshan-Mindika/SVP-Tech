<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

use App\Traits\DateFilterable;

class AppointmentController extends Controller
{
    use DateFilterable;

    public function index(Request $request)
    {
        $query = Appointment::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('appointment_no', 'like', "%{$search}%");
        }

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Apply Date Filter from Trait on appointment_time column
        $this->applyDateFilter($query, $request, 'appointment_time');

        $stats = [
            'total_count' => (clone $query)->count(),
            'pending_count' => (clone $query)->whereIn('status', ['pending', 'scheduled'])->count(),
            'completed_count' => (clone $query)->where('status', 'completed')->count(),
            'cancelled_count' => (clone $query)->where('status', 'cancelled')->count(),
        ];

        $appointments = $query->orderBy('appointment_time', 'asc')->paginate(10);
        return view('appointments.index', compact('appointments', 'stats'));
    }

    public function create()
    {
        return view('appointments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'appointment_time' => 'required|date|after:now',
            'reason' => 'required|string',
        ]);

        $lastApt = Appointment::latest('id')->first();
        $nextId = $lastApt ? $lastApt->id + 1 : 1;
        $aptNo = 'APT-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        Appointment::create([
            'appointment_no' => $aptNo,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_email' => $request->customer_email,
            'appointment_time' => $request->appointment_time,
            'reason' => $request->reason,
            'status' => 'scheduled',
        ]);

        return redirect()->route('appointments.index')->with('success', "Appointment {$aptNo} scheduled successfully.");
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|string|in:scheduled,completed,cancelled',
        ]);

        $appointment->status = $request->status;
        $appointment->save();

        return redirect()->route('appointments.index')->with('success', "Appointment #{$appointment->appointment_no} marked as {$request->status}.");
    }

    public function edit(Appointment $appointment)
    {
        return view('appointments.edit', compact('appointment'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'appointment_time' => 'required|date',
            'reason' => 'required|string',
            'status' => 'required|string|in:scheduled,completed,cancelled',
        ]);

        $appointment->update($request->all());

        return redirect()->route('appointments.index')->with('success', "Appointment #{$appointment->appointment_no} updated successfully.");
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->route('appointments.index')->with('success', "Appointment #{$appointment->appointment_no} deleted successfully.");
    }
}
