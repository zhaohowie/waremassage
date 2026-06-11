<?php

namespace App\Http\Controllers;

use App\Models\Appointment;

class AppointmentActivityController extends Controller
{
    public function index(Appointment $appointment)
    {
        $appointment->load(['customer', 'service', 'staff']);

        $activities = $appointment->activities()
            ->with('user')
            ->latest()
            ->get();

        return view('appointment-activities.index', compact('appointment', 'activities'));
    }
}
