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

                <div style="margin-top:20px; display:flex; gap:10px;">
                    <button type="submit"
                            style="background:#2563eb; color:white; padding:10px 18px; border-radius:6px;">
                        Save
                    </button>

                    <a href="{{ route('customers.index') }}"
                    style="
                            background:#6b7280;
                            color:white;
                            padding:10px 18px;
                            border-radius:6px;
                            text-decoration:none;
                            display:inline-block;
                    ">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
