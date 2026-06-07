<x-app-layout>
    <x-slot name="header">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Calendar
            </h2>

            <form method="GET" action="{{ route('appointments.calendar') }}">
                <input type="date"
                       name="date"
                       value="{{ $date }}"
                       onchange="this.form.submit()"
                       style="padding:8px; border:1px solid #d1d5db; border-radius:6px;">
            </form>
        </div>
    </x-slot>

    <div class="py-6">
        <div style="background:white; overflow:auto;">

            <div style="display:grid; grid-template-columns:80px repeat({{ max($staffMembers->count(), 1) }}, minmax(220px, 1fr)); border-bottom:1px solid #ddd; position:sticky; top:0; background:white; z-index:10;">
                <div style="padding:16px; font-weight:bold;">Time</div>

                @foreach($staffMembers as $member)
                    <div style="padding:16px; text-align:center; border-left:1px solid #eee;">
                        <div style="width:54px; height:54px; border-radius:50%; background:#eef2ff; margin:0 auto 8px; display:flex; align-items:center; justify-content:center; font-weight:bold;">
                            {{ strtoupper(substr($member->name, 0, 1)) }}
                        </div>
                        <strong>{{ $member->name }}</strong>
                    </div>
                @endforeach
            </div>

            <div style="display:grid; grid-template-columns:80px repeat({{ max($staffMembers->count(), 1) }}, minmax(220px, 1fr)); position:relative;">

                @for($hour = 8; $hour < 20; $hour++)
                    @foreach([0, 15, 30, 45] as $minute)
                        @php
                            $timeLabel = \Carbon\Carbon::createFromTime($hour, $minute)->format('g:i A');
                            $timeValue = \Carbon\Carbon::createFromTime($hour, $minute)->format('H:i');
                        @endphp

                        <div style="height:24px; border-bottom:1px solid #f1f5f9; padding:2px 8px; font-size:12px; color:#64748b;">
                            @if($minute === 0)
                                <strong>{{ $timeLabel }}</strong>
                            @else
                                {{ $timeLabel }}
                            @endif
                        </div>

                        @foreach($staffMembers as $member)
                            <div onclick="openSlotMenu(event, '{{ $member->id }}', '{{ $member->name }}', '{{ $date }}', '{{ $timeValue }}')"
                                ondragover="event.preventDefault()"
                                ondrop="dropAppointment(event, '{{ $member->id }}', '{{ $date }}', '{{ $timeValue }}')"
                                style="height:24px;
                                        border-left:1px solid #eee;
                                        border-bottom:1px solid #f1f5f9;
                                        position:relative;
                                        cursor:pointer;">

                                @foreach($appointments->where('staff_id', $member->id) as $appointment)
                                    @php
                                        $start = \Carbon\Carbon::parse($appointment->appointment_time);
                                        $appointmentHour = (int) $start->format('G');
                                        $appointmentMinute = (int) $start->format('i');

                                        $totalMinutesFromHour = $appointmentMinute;
                                        $top = ($totalMinutesFromHour / 15) * 24;
                                        $height = ($appointment->duration / 15) * 24;
                                    @endphp

                                    @if($appointmentHour == $hour && $appointmentMinute == $minute)
                                        <div draggable="true"
                                            ondragstart="dragAppointment(event, '{{ $appointment->id }}')"
                                            onclick="event.stopPropagation();"
                                            style="position:absolute;
                                                    top:0;
                                                    left:6px;
                                                    right:6px;
                                                    height:{{ $height }}px;
                                                    background:#bae6fd;
                                                    border-radius:6px;
                                                    padding:6px;
                                                    font-size:12px;
                                                    overflow:hidden;
                                                    z-index:5;
                                                    box-shadow:0 1px 3px rgba(0,0,0,0.12);"
                                            onclick="event.stopPropagation();">
                                            <strong>
                                                {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}
                                                -
                                                {{ \Carbon\Carbon::parse($appointment->appointment_time)->addMinutes($appointment->duration)->format('g:i A') }}
                                            </strong>
                                            <br>
                                            {{ $appointment->customer->full_name ?? 'Customer' }}
                                            <br>
                                            {{ $appointment->service->name ?? 'Service' }}
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endforeach
                    @endforeach
                @endfor

            </div>
        </div>
    </div>

    <div id="slot-menu"
        style="display:none;
                position:fixed;
                background:white;
                border:1px solid #ddd;
                border-radius:8px;
                box-shadow:0 8px 20px rgba(0,0,0,0.15);
                z-index:9999;
                min-width:190px;
                overflow:hidden;">

        <div id="slot-menu-title"
            style="padding:10px 12px; font-weight:bold; border-bottom:1px solid #eee;">
        </div>

        <button type="button"
                onclick="addAppointmentFromSlot()"
                style="display:block; width:100%; padding:10px 12px; border:none; background:white; text-align:left; cursor:pointer;">
            + Add Appointment
        </button>

        <button type="button"
                onclick="blockTimeFromSlot()"
                style="display:block; width:100%; padding:10px 12px; border:none; background:white; text-align:left; cursor:pointer;">
            Block Time
        </button>
    </div>

    <script>
        let selectedSlot = {};

        function openSlotMenu(event, staffId, staffName, date, time) {
            selectedSlot = {
                staffId: staffId,
                staffName: staffName,
                date: date,
                time: time
            };

            const menu = document.getElementById('slot-menu');
            const title = document.getElementById('slot-menu-title');

            title.innerHTML = staffName + '<br><span style="font-weight:normal; color:#666;">' + date + ' ' + time + '</span>';

            menu.style.display = 'block';
            menu.style.left = event.clientX + 'px';
            menu.style.top = event.clientY + 'px';
        }

        function addAppointmentFromSlot() {
            alert('Add appointment for staff ID ' + selectedSlot.staffId + ' at ' + selectedSlot.date + ' ' + selectedSlot.time);

            // Later this will open an add appointment modal.
        }

        function blockTimeFromSlot() {
            alert('Block time for staff ID ' + selectedSlot.staffId + ' at ' + selectedSlot.date + ' ' + selectedSlot.time);

            // Later this will create a blocked-time record.
        }

        document.addEventListener('click', function(event) {
            const menu = document.getElementById('slot-menu');

            if (!menu.contains(event.target) && !event.target.closest('[onclick^="openSlotMenu"]')) {
                menu.style.display = 'none';
            }
        });

        let draggedAppointmentId = null;

        function dragAppointment(event, appointmentId) {
            draggedAppointmentId = appointmentId;
            event.dataTransfer.setData('text/plain', appointmentId);
        }

        function dropAppointment(event, staffId, date, time) {
            event.preventDefault();
            event.stopPropagation();

            const appointmentId = draggedAppointmentId || event.dataTransfer.getData('text/plain');

            if (!appointmentId) {
                return;
            }

            fetch(`/appointments/${appointmentId}/move`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    staff_id: staffId,
                    appointment_date: date,
                    appointment_time: time
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Could not move appointment.');
                }
            })
            .catch(() => {
                alert('Move failed.');
            });
        }
    </script>    
</x-app-layout>
