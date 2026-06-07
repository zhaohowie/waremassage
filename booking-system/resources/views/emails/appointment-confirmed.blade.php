<h2>Appointment Confirmed</h2>

<p>Hello {{ $appointment->customer->first_name }},</p>

<p>Your appointment has been confirmed.</p>

<p><strong>Service:</strong> {{ $appointment->service->name }}</p>
<p><strong>Staff:</strong> {{ $appointment->staff->name }}</p>
<p><strong>Date:</strong> {{ $appointment->appointment_date }}</p>
<p><strong>Time:</strong> {{ $appointment->appointment_time }}</p>
<p><strong>Duration:</strong> {{ $appointment->duration }} minutes</p>
<p><strong>Price:</strong> ${{ number_format($appointment->price, 2) }}</p>

<p>Thank you.</p>