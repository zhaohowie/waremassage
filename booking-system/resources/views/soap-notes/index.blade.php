<x-app-layout>
    <x-slot name="header">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                SOAP Notes
            </h2>

            <a href="{{ route('appointments.soap-notes.create', $appointment) }}"
               style="background:#2563eb; color:white; padding:8px 16px; border-radius:6px; text-decoration:none;">
                + Add SOAP Note
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div style="background:white; border-radius:8px; padding:20px; margin-bottom:20px;">
            <strong>Customer:</strong> {{ $appointment->customer->full_name }} |
            <strong>Service:</strong> {{ $appointment->service->name }} |
            <strong>Date:</strong> {{ $appointment->appointment_date }}
        </div>

        @if(session('success'))
            <div style="background:#dcfce7; color:#166534; padding:12px; border-radius:6px; margin-bottom:20px;">
                {{ session('success') }}
            </div>
        @endif

        @forelse($soapNotes as $note)
            <div style="background:white; border-radius:8px; padding:20px; margin-bottom:16px;">
                <div style="display:flex; justify-content:space-between;">
                    <strong>{{ $note->created_at->format('Y-m-d H:i') }}</strong>

                    <a href="{{ route('soap-notes.edit', $note) }}"
                       style="color:#2563eb;">
                        Edit
                    </a>
                </div>

                <p><strong>S:</strong> {{ $note->subjective }}</p>
                <p><strong>O:</strong> {{ $note->objective }}</p>
                <p><strong>A:</strong> {{ $note->assessment }}</p>
                <p><strong>P:</strong> {{ $note->plan }}</p>
            </div>
        @empty
            <div style="background:white; border-radius:8px; padding:20px; color:#666;">
                No SOAP notes yet.
            </div>
        @endforelse
    </div>

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
                    style="background:#2563eb; color:white; padding:10px 20px; border:none; border-radius:6px; cursor:pointer;">
                OK
            </button>
        @endif
    </div>
</x-app-layout>
