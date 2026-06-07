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

        return view('booking.form', compact('services', 'staff'));
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

        $staffMembers = Staff::where('is_active', true)
            ->whereHas('workingHours', function ($query) use ($date, $dayOfWeek) {
                $query->where('schedule_type', 'date_range_weekly')
                    ->whereDate('start_date', '<=', $date)
                    ->whereDate('end_date', '>=', $date)
                    ->where('day_of_week', $dayOfWeek)
                    ->where('is_available', true);
            })
            ->orderBy('name')
            ->get();

        $appointments = Appointment::with(['customer', 'service', 'staff'])
            ->where('appointment_date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereIn('staff_id', $staffMembers->pluck('id'))
            ->get();

        return view('appointments.calendar', compact(
            'date',
            'staffMembers',
            'appointments'
        ));
    }
}
