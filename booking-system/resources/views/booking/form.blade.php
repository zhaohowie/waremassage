<!DOCTYPE html>
<html>
<head>
    <title>Book Appointment</title>
</head>
<body style="font-family:Arial; background:#f3f4f6; padding:40px;">

    <div style="max-width:700px; margin:0 auto; background:white; padding:24px; border-radius:8px;">
        <h1>Book Appointment</h1>

        @if(session('success'))
            <div style="background:#dcfce7; color:#166534; padding:12px; border-radius:6px; margin-bottom:20px;">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('booking.store') }}">
            @csrf

            <h3>Service</h3>
            <select name="service_id" required style="width:100%; padding:8px; margin-bottom:16px;">
                <option value="">Select service</option>
                @foreach($services as $service)
                    <option value="{{ $service->id }}">
                        {{ $service->name }} - ${{ number_format($service->price, 2) }} / {{ $service->duration }} min
                    </option>
                @endforeach
            </select>

            <h3>Staff</h3>
            <select name="staff_id" required style="width:100%; padding:8px; margin-bottom:16px;">
                <option value="">Select staff</option>
                @foreach($staff as $member)
                    <option value="{{ $member->id }}">
                        {{ $member->name }}
                    </option>
                @endforeach
            </select>

            <h3>Date & Time</h3>
            <input type="date" name="appointment_date" required style="width:100%; padding:8px; margin-bottom:16px;">

            <input type="hidden" name="appointment_time" id="appointment_time" required>

            <div id="available-times" style="margin-bottom:16px;">
                Please select service, staff, and date first.
            </div>

            <h3>Your Information</h3>

            <input type="text" name="first_name" placeholder="First Name" required style="width:100%; padding:8px; margin-bottom:12px;">

            <input type="text" name="last_name" placeholder="Last Name" required style="width:100%; padding:8px; margin-bottom:12px;">

            <input type="email" name="email" placeholder="Email" style="width:100%; padding:8px; margin-bottom:12px;">

            <input type="text" name="phone" placeholder="Phone" style="width:100%; padding:8px; margin-bottom:12px;">

            <textarea name="notes" placeholder="Notes" style="width:100%; padding:8px; margin-bottom:16px;"></textarea>

            <button type="submit" style="background:#2563eb; color:white; padding:10px 18px; border-radius:6px; border:none;">
                Book Appointment
            </button>
        </form>
    </div>

    <script>
        const serviceSelect = document.querySelector('select[name="service_id"]');
        const staffSelect = document.querySelector('select[name="staff_id"]');
        const dateInput = document.querySelector('input[name="appointment_date"]');
        const timeInput = document.getElementById('appointment_time');
        const timesContainer = document.getElementById('available-times');

        function loadStaffByService() {
            const serviceId = serviceSelect.value;

            staffSelect.innerHTML = '<option value="">Loading staff...</option>';
            timeInput.value = '';
            timesContainer.innerHTML = 'Please select service, staff, and date first.';

            if (!serviceId) {
                staffSelect.innerHTML = '<option value="">Select staff</option>';
                return;
            }

            fetch(`/booking/staff-by-service?service_id=${serviceId}`)
                .then(response => response.json())
                .then(staff => {
                    staffSelect.innerHTML = '<option value="">Select staff</option>';

                    if (staff.length === 0) {
                        staffSelect.innerHTML = '<option value="">No staff available for this service</option>';
                        return;
                    }

                    staff.forEach(member => {
                        staffSelect.innerHTML += `<option value="${member.id}">${member.name}</option>`;
                    });
                });
        }
        
        function loadAvailableTimes() {
            const serviceId = serviceSelect.value;
            const staffId = staffSelect.value;
            const date = dateInput.value;

            timeInput.value = '';

            if (!serviceId || !staffId || !date) {
                timesContainer.innerHTML = 'Please select service, staff, and date first.';
                return;
            }

            @error('appointment_time')
            <div style="color:#dc2626; margin-bottom:12px;">
                {{ $message }}
            </div>
            @enderror

            timesContainer.innerHTML = 'Loading available times...';

            fetch(`/booking/available-times?service_id=${serviceId}&staff_id=${staffId}&date=${date}`)
                .then(response => response.json())
                .then(times => {
                    if (times.length === 0) {
                        timesContainer.innerHTML = '<div style="color:#dc2626;">No available times for this date.</div>';
                        return;
                    }

                    let html = '';

                    times.forEach(time => {
                        html += `
                            <button type="button"
                                    onclick="selectTime('${time}', this)"
                                    style="margin:4px; padding:8px 12px; border:1px solid #2563eb; border-radius:6px; background:white; color:#2563eb; cursor:pointer;">
                                ${time}
                            </button>
                        `;
                    });

                    timesContainer.innerHTML = html;
                });
        }

        function selectTime(time, button) {
            timeInput.value = time;

            document.querySelectorAll('#available-times button').forEach(btn => {
                btn.style.background = 'white';
                btn.style.color = '#2563eb';
            });

            button.style.background = '#2563eb';
            button.style.color = 'white';
        }

        serviceSelect.addEventListener('change', function () {
            loadStaffByService();
        });

        staffSelect.addEventListener('change', loadAvailableTimes);
        dateInput.addEventListener('change', loadAvailableTimes);
    </script>
</body>
</html>
