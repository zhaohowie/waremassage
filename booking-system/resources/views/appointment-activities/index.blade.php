<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Appointment Activities
        </h2>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div style="background:white; border-radius:8px; padding:20px; margin-bottom:20px;">
            <strong>Customer:</strong> {{ $appointment->customer->full_name }} |
            <strong>Service:</strong> {{ $appointment->service->name }} |
            <strong>Date:</strong> {{ $appointment->appointment_date }}
        </div>

        @forelse($activities as $activity)
            <div style="background:white; border-radius:8px; padding:16px; margin-bottom:12px;">
                <strong>{{ ucwords(str_replace('_', ' ', $activity->action)) }}</strong>
                <div>{{ $activity->description }}</div>
                <small>
                    {{ $activity->created_at->format('Y-m-d H:i:s') }}
                    by {{ $activity->user->name ?? 'System' }}
                </small>
            </div>
        @empty
            <div style="background:white; border-radius:8px; padding:20px;">
                No activity found.
            </div>
        @endforelse

        <div style="margin-top:20px; text-align:right;">
            @php
                $returnUrl = request('return_url');
            @endphp

            <div style="margin-top:20px; text-align:right;">
                @if($returnUrl)
                    <a href="{{ $returnUrl }}"
                    style="background:#2563eb; color:white; padding:10px 20px; border-radius:6px; text-decoration:none;">
                        OK
                    </a>
                @else
                    <button type="button"
                            onclick="window.history.back();"
                            style="background:#2563eb; color:white; padding:10px 20px; border:none; border-radius:6px;">
                        OK
                    </button>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
