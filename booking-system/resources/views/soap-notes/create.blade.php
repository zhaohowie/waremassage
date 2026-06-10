<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Add SOAP Note
        </h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div style="background:white; border-radius:8px; padding:20px; margin-bottom:20px;">
            <strong>Customer:</strong> {{ $appointment->customer->full_name }} |
            <strong>Service:</strong> {{ $appointment->service->name }} |
            <strong>Date:</strong> {{ $appointment->appointment_date }}
        </div>

        <div style="background:white; border-radius:8px; padding:24px;">
            <form method="POST" action="{{ route('appointments.soap-notes.store', $appointment) }}">
                @csrf

                @include('soap-notes.form')

                <button type="submit"
                        style="background:#2563eb; color:white; padding:10px 18px; border-radius:6px;">
                    Save SOAP Note
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
