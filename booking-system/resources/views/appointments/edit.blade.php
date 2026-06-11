<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Appointment
        </h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div style="background:white; border-radius:8px; padding:24px;">
            <form method="POST" action="{{ route('appointments.update', $appointment) }}">
                @csrf
                @method('PUT')

                <input type="hidden" name="return_url" value="{{ request('return_url') }}">

                <div style="margin-bottom:16px;">
                    <label>Customer</label>
                    <select name="customer_id" required style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;">
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ $appointment->customer_id == $customer->id ? 'selected' : '' }}>
                                {{ $customer->full_name }} - {{ $customer->phone }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom:16px;">
                    <label>Service</label>
                    <select name="service_id" required style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;">
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ $appointment->service_id == $service->id ? 'selected' : '' }}>
                                {{ $service->name }} - ${{ number_format($service->price, 2) }} / {{ $service->duration }} min
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom:16px;">
                    <label>Staff</label>
                    <select name="staff_id" required style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;">
                        @foreach($staff as $member)
                            <option value="{{ $member->id }}" {{ $appointment->staff_id == $member->id ? 'selected' : '' }}>
                                {{ $member->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom:16px;">
                    <label>Date</label>
                    <input type="date" name="appointment_date" value="{{ $appointment->appointment_date }}" required
                           style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;">
                </div>

                <div style="margin-bottom:16px;">
                    <label>Time</label>
                    <input type="time" name="appointment_time" value="{{ substr($appointment->appointment_time, 0, 5) }}" required
                           style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;">
                </div>

                <div style="margin-bottom:16px;">
                    <label>Status</label>
                    <select name="status" required style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;">
                        @foreach(['pending', 'confirmed', 'completed', 'cancelled', 'no_show'] as $status)
                            <option value="{{ $status }}" {{ $appointment->status == $status ? 'selected' : '' }}>
                                {{ ucwords(str_replace('_', ' ', $status)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom:16px;">
                    <label>Notes</label>
                    <textarea name="notes" rows="4"
                              style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;">{{ $appointment->notes }}</textarea>
                </div>

                <div style="display:flex; gap:10px;">
                    <button type="submit"
                            style="background:#2563eb; color:white; padding:10px 18px; border-radius:6px;">
                        Update Appointment
                    </button>

                    <a href="{{ request('return_url') ?: route('appointments.index') }}"
                       style="background:#6b7280; color:white; padding:10px 18px; border-radius:6px; text-decoration:none;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
