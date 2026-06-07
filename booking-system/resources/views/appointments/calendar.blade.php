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

                @for($hour = 8; $hour <= 20; $hour++)
                    <div style="height:80px; border-bottom:1px solid #eee; padding:8px; font-weight:bold;">
                        {{ \Carbon\Carbon::createFromTime($hour, 0)->format('g:i A') }}
                    </div>

                    @foreach($staffMembers as $member)
                        <div style="height:80px; border-left:1px solid #eee; border-bottom:1px solid #eee; position:relative;">
                            @foreach($appointments->where('staff_id', $member->id) as $appointment)
                                @php
                                    $start = \Carbon\Carbon::parse($appointment->appointment_time);
                                    $appointmentHour = (int) $start->format('G');
                                    $appointmentMinute = (int) $start->format('i');

                                    $top = ($appointmentMinute / 60) * 80;
                                    $height = ($appointment->duration / 60) * 80;
                                @endphp

                                @if($appointmentHour == $hour)
                                    <div style="position:absolute;
                                                top:{{ $top }}px;
                                                left:6px;
                                                right:6px;
                                                height:{{ $height }}px;
                                                background:#bae6fd;
                                                border-radius:6px;
                                                padding:8px;
                                                font-size:13px;
                                                overflow:hidden;
                                                box-shadow:0 1px 3px rgba(0,0,0,0.12);">
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
                @endfor

            </div>
        </div>
    </div>
</x-app-layout>
