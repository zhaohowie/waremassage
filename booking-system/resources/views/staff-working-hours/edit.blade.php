<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Availability - {{ $staff->name }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div style="background:white; border-radius:8px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">

            <form method="POST" action="{{ route('staff.working-hours.update', $staff) }}">
                @csrf
                @method('PUT')

                <h3 style="font-size:18px; font-weight:bold; margin-bottom:12px;">
                    Availability Slots
                </h3>

                <div id="availability-slots">
                    @foreach($availabilitySlots as $index => $slot)
                        <div class="availability-row" style="display:flex; gap:12px; margin-bottom:12px; align-items:center; flex-wrap:wrap;">
                            <input type="date"
                                   name="availability_slots[{{ $index }}][start_date]"
                                   value="{{ $slot->start_date }}"
                                   style="padding:8px; border:1px solid #d1d5db; border-radius:6px;">

                            <input type="date"
                                   name="availability_slots[{{ $index }}][end_date]"
                                   value="{{ $slot->end_date }}"
                                   style="padding:8px; border:1px solid #d1d5db; border-radius:6px;">

                            <select name="availability_slots[{{ $index }}][day_of_week]"
                                    style="padding:8px; border:1px solid #d1d5db; border-radius:6px;">
                                @foreach($days as $dayNumber => $dayName)
                                    <option value="{{ $dayNumber }}" {{ $slot->day_of_week == $dayNumber ? 'selected' : '' }}>
                                        {{ $dayName }}
                                    </option>
                                @endforeach
                            </select>

                            <input type="time"
                                   name="availability_slots[{{ $index }}][start_time]"
                                   value="{{ $slot->start_time }}"
                                   style="padding:8px; border:1px solid #d1d5db; border-radius:6px;">

                            <input type="time"
                                   name="availability_slots[{{ $index }}][end_time]"
                                   value="{{ $slot->end_time }}"
                                   style="padding:8px; border:1px solid #d1d5db; border-radius:6px;">

                            <button type="button"
                                    onclick="this.parentElement.remove()"
                                    style="color:#dc2626;">
                                Remove
                            </button>
                        </div>
                    @endforeach
                </div>

                <button type="button"
                        onclick="addAvailabilitySlot()"
                        style="background:#16a34a; color:white; padding:8px 14px; border-radius:6px; margin-top:12px;">
                    + Add Availability Slot
                </button>

                <div style="margin-top:28px;">
                    <button type="submit"
                            style="background:#2563eb; color:white; padding:10px 18px; border-radius:6px;">
                        Save Availability
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
        let availabilityIndex = {{ $availabilitySlots->count() }};

        function addAvailabilitySlot() {
            const container = document.getElementById('availability-slots');

            const html = `
                <div class="availability-row" style="display:flex; gap:12px; margin-bottom:12px; align-items:center; flex-wrap:wrap;">
                    <input type="date"
                           name="availability_slots[${availabilityIndex}][start_date]"
                           style="padding:8px; border:1px solid #d1d5db; border-radius:6px;">

                    <input type="date"
                           name="availability_slots[${availabilityIndex}][end_date]"
                           style="padding:8px; border:1px solid #d1d5db; border-radius:6px;">

                    <select name="availability_slots[${availabilityIndex}][day_of_week]"
                            style="padding:8px; border:1px solid #d1d5db; border-radius:6px;">
                        @foreach($days as $dayNumber => $dayName)
                            <option value="{{ $dayNumber }}">{{ $dayName }}</option>
                        @endforeach
                    </select>

                    <input type="time"
                           name="availability_slots[${availabilityIndex}][start_time]"
                           style="padding:8px; border:1px solid #d1d5db; border-radius:6px;">

                    <input type="time"
                           name="availability_slots[${availabilityIndex}][end_time]"
                           style="padding:8px; border:1px solid #d1d5db; border-radius:6px;">

                    <button type="button"
                            onclick="this.parentElement.remove()"
                            style="color:#dc2626;">
                        Remove
                    </button>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', html);
            availabilityIndex++;
        }
    </script>
</x-app-layout>
