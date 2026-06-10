<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\SoapNote;
use Illuminate\Http\Request;

class SoapNoteController extends Controller
{
    public function index(Appointment $appointment)
    {
        $appointment->load(['customer', 'service', 'staff']);

        $soapNotes = $appointment->soapNotes()
            ->with('staff')
            ->latest()
            ->get();

        return view('soap-notes.index', compact('appointment', 'soapNotes'));
    }

    public function create(Appointment $appointment)
    {
        $appointment->load(['customer', 'service', 'staff']);

        return view('soap-notes.create', compact('appointment'));
    }

    public function store(Request $request, Appointment $appointment)
    {
        $request->validate([
            'subjective' => 'nullable',
            'objective' => 'nullable',
            'assessment' => 'nullable',
            'plan' => 'nullable',
        ]);

        SoapNote::create([
            'appointment_id' => $appointment->id,
            'staff_id' => $appointment->staff_id,
            'subjective' => $request->subjective,
            'objective' => $request->objective,
            'assessment' => $request->assessment,
            'plan' => $request->plan,
        ]);

        return redirect()
            ->route('appointments.soap-notes.index', $appointment)
            ->with('success', 'SOAP note added successfully.');
    }

    public function edit(SoapNote $soapNote)
    {
        $soapNote->load(['appointment.customer', 'appointment.service', 'appointment.staff']);

        return view('soap-notes.edit', compact('soapNote'));
    }

    public function update(Request $request, SoapNote $soapNote)
    {
        $request->validate([
            'subjective' => 'nullable',
            'objective' => 'nullable',
            'assessment' => 'nullable',
            'plan' => 'nullable',
        ]);

        $soapNote->update([
            'subjective' => $request->subjective,
            'objective' => $request->objective,
            'assessment' => $request->assessment,
            'plan' => $request->plan,
        ]);

        return redirect()
            ->route('appointments.soap-notes.index', $soapNote->appointment_id)
            ->with('success', 'SOAP note updated successfully.');
    }
}
