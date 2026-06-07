<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Add Customer
        </h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div style="background:white; border-radius:8px; padding:24px;">
            <form method="POST" action="{{ route('customers.store') }}">
                @csrf

                @include('customers.form')

                <button type="submit"
                        style="background:#2563eb; color:white; padding:8px 16px; border-radius:6px;">
                    Save Customer
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
