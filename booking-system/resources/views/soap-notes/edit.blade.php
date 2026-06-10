<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit SOAP Note
        </h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div style="background:white; border-radius:8px; padding:20px; margin-bottom:20px;">
            <strong>Customer:</strong> {{ $soapNote->appointment->customer->full_name }} |
            <strong>Service:</strong> {{ $soapNote->appointment->service->name }} |
            <strong>Date:</strong> {{ $soapNote->appointment->appointment_date }}
        </div>

        <div style="background:white; border-radius:8px; padding:24px;">
            <form method="POST" action="{{ route('soap-notes.update', $soapNote) }}">
                @csrf
                @method('PUT')

                @include('soap-notes.form', ['soapNote' => $soapNote])

                <button type="submit"
                        style="background:#2563eb; color:white; padding:10px 18px; border-radius:6px;">
                    Update SOAP Note
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
