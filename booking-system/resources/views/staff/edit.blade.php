<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Staff
        </h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div style="background:white; border-radius:8px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <form method="POST" action="{{ route('staff.update', $staff) }}">
                @csrf
                @method('PUT')

                @include('staff.form')

                <button type="submit"
                        style="background:#2563eb; color:white; padding:10px 18px; border-radius:6px;">
                    Save
                </button>

                <a href="{{ route('staff.index') }}"
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
            </form>
        </div>
    </div>
</x-app-layout>
