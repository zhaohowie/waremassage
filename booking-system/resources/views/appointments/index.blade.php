<x-app-layout>
    @if(session('success'))
        <div style="background:#dcfce7; color:#166534; padding:12px; border-radius:6px; margin-bottom:20px;">
            {{ session('success') }}
        </div>
    @endif

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Appointments
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div style="background:white; border-radius:8px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid #ddd;">
                        <th style="text-align:left; padding:12px;">Date</th>
                        <th style="text-align:left; padding:12px;">Time</th>
                        <th style="text-align:left; padding:12px;">Customer</th>
                        <th style="text-align:left; padding:12px;">Service</th>
                        <th style="text-align:left; padding:12px;">Staff</th>
                        <th style="text-align:left; padding:12px;">Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($appointments as $appointment)
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:12px;">{{ $appointment->appointment_date }}</td>
                            <td style="padding:12px;">{{ $appointment->appointment_time }}</td>
                            <td style="padding:12px;">{{ $appointment->customer->full_name ?? '-' }}</td>
                            <td style="padding:12px;">{{ $appointment->service->name ?? '-' }}</td>
                            <td style="padding:12px;">{{ $appointment->staff->name ?? '-' }}</td>
                            <td style="padding:12px;">
                                <form method="POST" action="{{ route('appointments.update-status', $appointment) }}">
                                    @csrf
                                    @method('PATCH')

                                    <select name="status"
                                            onchange="this.form.submit()"
                                            style="border:1px solid #d1d5db; border-radius:6px; padding:6px;">
                                        @foreach(['pending', 'confirmed', 'completed', 'cancelled', 'no_show'] as $status)
                                            <option value="{{ $status }}" {{ $appointment->status === $status ? 'selected' : '' }}>
                                                {{ ucwords(str_replace('_', ' ', $status)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding:20px; text-align:center; color:#666;">
                                No appointments yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
