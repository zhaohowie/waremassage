<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;

class StaffWorkingHourController extends Controller
{
    public function edit(Staff $staff)
    {
        $days = [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];

        $availabilitySlots = $staff->workingHours()
            ->orderBy('start_date')
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return view('staff-working-hours.edit', compact(
            'staff',
            'days',
            'availabilitySlots'
        ));
    }

    public function update(Request $request, Staff $staff)
    {
        $request->validate([
            'availability_slots' => 'nullable|array',
            'availability_slots.*.start_date' => 'required|date',
            'availability_slots.*.end_date' => 'required|date',
            'availability_slots.*.day_of_week' => 'required|integer|min:0|max:6',
            'availability_slots.*.start_time' => 'required',
            'availability_slots.*.end_time' => 'required',
        ]);

        $staff->workingHours()->delete();

        foreach ($request->input('availability_slots', []) as $slot) {
            $staff->workingHours()->create([
                'schedule_type' => 'date_range_weekly',
                'start_date' => $slot['start_date'],
                'end_date' => $slot['end_date'],
                'day_of_week' => $slot['day_of_week'],
                'specific_date' => null,
                'start_time' => $slot['start_time'],
                'end_time' => $slot['end_time'],
                'is_available' => true,
            ]);
        }

        return redirect()
            ->route('staff.index')
            ->with('success', 'Availability updated successfully.');
    }

    public function blockTimeForm(\App\Models\Staff $staff, Request $request)
    {
        $date = $request->get('date');
        $startTime = $request->get('start_time');

        return view('staff-working-hours.block-time', compact(
            'staff',
            'date',
            'startTime'
        ));
    }

    public function storeBlockTime(Request $request, \App\Models\Staff $staff)
    {
        $request->validate([
            'specific_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'notes' => 'nullable',
        ]);

        $staff->workingHours()->create([
            'schedule_type' => 'blocked',
            'day_of_week' => null,
            'specific_date' => $request->specific_date,
            'start_date' => null,
            'end_date' => null,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_available' => false,
        ]);

        return redirect()->route('appointments.calendar')
            ->with('success', 'Time blocked successfully.');
    }

    public function moveBlockedTime(Request $request, \App\Models\StaffWorkingHour $workingHour)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'specific_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        $workingHour->update([
            'staff_id' => $request->staff_id,
            'specific_date' => $request->specific_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return response()->json(['success' => true]);
    }

    public function deleteBlockedTime(\App\Models\StaffWorkingHour $workingHour)
    {
        $workingHour->delete();

        return response()->json(['success' => true]);
    }
}
