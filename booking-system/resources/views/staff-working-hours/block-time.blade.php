<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Block Time - {{ $staff->name }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div style="background:white; border-radius:8px; padding:24px;">
            <form method="POST" action="{{ route('staff.block-time.store', $staff) }}">
                @csrf

                <div style="margin-bottom:16px;">
                    <label style="display:block; margin-bottom:6px;">Date</label>
                    <input type="date"
                           name="specific_date"
                           value="{{ $date }}"
                           required
                           style="width:100%; border:1px solid #d1d5db; border-radius:6px; padding:8px;">
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block; margin-bottom:6px;">Start Time</label>
                    <input type="time"
                           name="start_time"
                           value="{{ $startTime }}"
                           required
                           style="width:100%; border:1px solid #d1d5db; border-radius:6px; padding:8px;">
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block; margin-bottom:6px;">End Time</label>
                    <input type="time"
                           name="end_time"
                           required
                           style="width:100%; border:1px solid #d1d5db; border-radius:6px; padding:8px;">
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block; margin-bottom:6px;">Notes</label>
                    <textarea name="notes"
                              style="width:100%; border:1px solid #d1d5db; border-radius:6px; padding:8px;"></textarea>
                </div>

                <button type="submit"
                        style="background:#dc2626; color:white; padding:10px 18px; border-radius:6px;">
                    Block Time
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
