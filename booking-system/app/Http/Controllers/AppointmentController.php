<?php

namespace App\Http\Controllers;
use App\Mail\AppointmentConfirmed;
use Illuminate\Support\Facades\Mail;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with([
                'customer',
                'service',
                'staff',
            ])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        return view('appointments.index', compact('appointments'));
    }

    public function bookingForm()
    {
        $services = Service::where('is_active', true)
            ->orderBy('name')
            ->get();

        $staff = Staff::where('is_active', true)
            ->orderBy('name')
            ->get();

        $selectedDate = request('date');
        $selectedTime = request('time');
        $selectedStaffId = request('staff_id');

        return view('booking.form', compact(
            'services',
            'staff',
            'selectedDate',
            'selectedTime',
            'selectedStaffId'
        ));
    }

    public function storeBooking(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'required|exists:staff,id',

            'appointment_date' => 'required|date',
            'appointment_time' => 'required',

            'first_name' => 'required',
            'last_name' => 'required',

            'email' => 'nullable|email',
            'phone' => 'nullable',
        ]);

        $service = Service::findOrFail($request->service_id);

        $customer = Customer::where('email', $request->email)
            ->when(!$request->email, function ($query) use ($request) {
                return $query->where('phone', $request->phone);
            })
            ->first();

        if (!$customer) {
            $customer = Customer::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'notes' => null,
            ]);
        }

        $availableTimesResponse = $this->availableTimes(new Request([
            'service_id' => $request->service_id,
            'staff_id' => $request->staff_id,
            'date' => $request->appointment_date,
        ]));

        $availableTimes = $availableTimesResponse->getData(true);

        if (!in_array($request->appointment_time, $availableTimes)) {
            return back()
                ->withInput()
                ->withErrors([
                    'appointment_time' => 'The selected time is no longer available.',
                ]);
        }

        $appointment = Appointment::create([
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'staff_id' => $request->staff_id,

            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,

            'duration' => $service->duration,
            'price' => $service->price,

            'notes' => $request->notes,
            'status' => 'confirmed',
        ]);

        if ($customer->email) {
            Mail::to($customer->email)->send(new AppointmentConfirmed($appointment));
        }

        return redirect()->route('booking.confirmation', $appointment);
    }

    public function availableTimes(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'required|exists:staff,id',
            'date' => 'required|date',
        ]);

        $service = Service::findOrFail($request->service_id);

        $date = Carbon::parse($request->date);
        $dayOfWeek = $date->dayOfWeek;

        $availabilitySlots = Staff::findOrFail($request->staff_id)
            ->workingHours()
            ->where('schedule_type', 'date_range_weekly')
            ->whereDate('start_date', '<=', $request->date)
            ->whereDate('end_date', '>=', $request->date)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->get();

        $bookedAppointments = Appointment::with('service')
            ->where('staff_id', $request->staff_id)
            ->where('appointment_date', $request->date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->get();

        $availableTimes = [];

        foreach ($availabilitySlots as $slot) {
            $start = Carbon::parse($request->date . ' ' . $slot->start_time);
            $end = Carbon::parse($request->date . ' ' . $slot->end_time);

            $totalBlockTime = $service->duration + $service->cleanup_time;
            
            while ($start->copy()->addMinutes($service->duration)->lte($end)) {
                $slotStart = $start->copy();
                $slotEnd = $start->copy()->addMinutes($service->duration);

                $hasConflict = false;

                foreach ($bookedAppointments as $appointment) {
                    $bookedStart = Carbon::parse($appointment->appointment_date . ' ' . $appointment->appointment_time);
                    $bookedService = $appointment->service;
                    $bookedCleanup = $bookedService ? $bookedService->cleanup_time : 0;
                    $bookedEnd = $bookedStart->copy()->addMinutes($appointment->duration + $bookedCleanup);

                    if ($slotStart->lt($bookedEnd) && $slotEnd->gt($bookedStart)) {
                        $hasConflict = true;
                        break;
                    }
                }

                if (!$hasConflict) {
                    $availableTimes[] = $slotStart->format('H:i');
                }

                $start->addMinutes(15);
            }
        }

        return response()->json(array_values(array_unique($availableTimes)));
    }

    public function staffByService(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
        ]);

        $staff = Staff::where('is_active', true)
            ->whereHas('services', function ($query) use ($request) {
                $query->where('services.id', $request->service_id);
            })
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($staff);
    }

    public function bookingConfirmation(Appointment $appointment)
    {
        $appointment->load(['customer', 'service', 'staff']);

        return view('booking.confirmation', compact('appointment'));
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled,no_show',
        ]);

        $appointment->update([
            'status' => $request->status,
        ]);

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Appointment status updated successfully.');
    }

    public function calendar(Request $request)
    {
        $date = $request->get('date', now()->toDateString());

        $dayOfWeek = \Carbon\Carbon::parse($date)->dayOfWeek;

        $availableStaffIds = \App\Models\StaffWorkingHour::whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->pluck('staff_id')
            ->unique()
            ->values();

            \Log::info('dayOfWeek', [
                'ids' => $availableStaffIds->toArray(),
            ]);

        $staffMembers = Staff::where('is_active', true)
            ->whereIn('id', $availableStaffIds)
            ->orderBy('name')
            ->get();

        $appointments = Appointment::with(['customer', 'service', 'staff'])
            ->where('appointment_date', $date)
            ->whereIn('staff_id', $staffMembers->pluck('id'))
            ->get();

        $resources = $staffMembers->map(fn ($staff) => [
            'id' => (string) $staff->id,
            'title' => $staff->name,
        ]);

        $events = $appointments->map(function ($appointment) {
            $start = $appointment->appointment_date . 'T' . $appointment->appointment_time;

            $end = \Carbon\Carbon::parse($start)
                ->addMinutes($appointment->duration)
                ->format('Y-m-d\TH:i:s');

            return [
                'id' => (string) $appointment->id,
                'resourceId' => (string) $appointment->staff_id,
                'title' => $appointment->customer->full_name . ' - ' . $appointment->service->name,
                'start' => $start,
                'end' => $end,
            ];
        });

        return view('appointments.calendar', compact('date', 'resources', 'events'));
    }

    public function move(Request $request, Appointment $appointment)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
        ]);

        $appointment->update([
            'staff_id' => $request->staff_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Appointment moved successfully.',
        ]);
    }

    public function calendarData(Request $request)
    {
        $date = $request->get('date', now()->toDateString());

        $dayOfWeek = \Carbon\Carbon::parse($date)->dayOfWeek;

        $availableStaffIds = \App\Models\StaffWorkingHour::whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->pluck('staff_id')
            ->unique()
            ->values();

        $staffMembers = Staff::where('is_active', true)
            ->whereIn('id', $availableStaffIds)
            ->orderBy('name')
            ->get();

        $appointments = Appointment::with(['customer', 'service', 'staff'])
            ->where('appointment_date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereIn('staff_id', $staffMembers->pluck('id'))
            ->get();

        $resources = $staffMembers->map(fn ($staff) => [
            'id' => (string) $staff->id,
            'title' => $staff->name,
        ])->values();

        $events = $appointments->map(function ($appointment) {
            $start = $appointment->appointment_date . 'T' . substr($appointment->appointment_time, 0, 5);

            $end = \Carbon\Carbon::parse($start)
                ->addMinutes($appointment->duration)
                ->format('Y-m-d\TH:i:s');

            return [
                'id' => (string) $appointment->id,
                'resourceId' => (string) $appointment->staff_id,
                'title' => ($appointment->customer->full_name ?? 'Customer') . ' - ' . ($appointment->service->name ?? 'Service'),
                'start' => $start,
                'end' => $end,
            ];
        })->values();

        $businessStart = \Carbon\Carbon::parse($date . ' 08:00');
        $businessEnd = \Carbon\Carbon::parse($date . ' 22:00');

        $availabilityRows = \App\Models\StaffWorkingHour::whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->get()
            ->groupBy('staff_id');

        $blockedRows = \App\Models\StaffWorkingHour::where('schedule_type', 'blocked')
            ->whereDate('specific_date', $date)
            ->where('is_available', false)
            ->get();

        $backgroundEvents = collect();

        foreach ($staffMembers as $staff) {
            $availableSlots = $availabilityRows->get($staff->id, collect())
                ->sortBy('start_time')
                ->values();

            $cursor = $businessStart->copy();

            foreach ($availableSlots as $slot) {
                $slotStart = \Carbon\Carbon::parse($date . ' ' . $slot->start_time);
                $slotEnd = \Carbon\Carbon::parse($date . ' ' . $slot->end_time);

                if ($cursor->lt($slotStart)) {
                    $backgroundEvents->push([
                        'resourceId' => (string) $staff->id,
                        'start' => $cursor->format('Y-m-d\TH:i:s'),
                        'end' => $slotStart->format('Y-m-d\TH:i:s'),
                        'display' => 'background',
                        'backgroundColor' => '#e5e7eb',
                    ]);
                }

                if ($cursor->lt($slotEnd)) {
                    $cursor = $slotEnd->copy();
                }
            }

            if ($cursor->lt($businessEnd)) {
                $backgroundEvents->push([
                    'resourceId' => (string) $staff->id,
                    'start' => $cursor->format('Y-m-d\TH:i:s'),
                    'end' => $businessEnd->format('Y-m-d\TH:i:s'),
                    'display' => 'background',
                    'backgroundColor' => '#e5e7eb',
                ]);
            }
        }

        foreach ($blockedRows as $blocked) {
            $backgroundEvents->push([
                'id' => 'blocked-' . $blocked->id,
                'resourceId' => (string) $blocked->staff_id,
                'title' => 'Blocked Time',
                'start' => $date . 'T' . substr($blocked->start_time, 0, 5) . ':00',
                'end' => $date . 'T' . substr($blocked->end_time, 0, 5) . ':00',

                'backgroundColor' => '#4b5563',
                'borderColor' => '#374151',
                'textColor' => '#ffffff',

                'editable' => true,
                'durationEditable' => true,
                'resourceEditable' => true,

                'extendedProps' => [
                    'type' => 'blocked_time',
                    'working_hour_id' => $blocked->id,
                    'staff_id' => $blocked->staff_id,
                    'specific_date' => $blocked->specific_date,
                    'start_time' => substr($blocked->start_time, 0, 5),
                    'end_time' => substr($blocked->end_time, 0, 5),
                ],
            ]);
        }

        $events = $events->concat($backgroundEvents)->values();    
            
        return response()->json([
            'resources' => $resources,
            'events' => $events,
        ]);
    }
}
