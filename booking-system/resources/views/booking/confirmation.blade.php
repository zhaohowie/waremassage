<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmed</title>
</head>
<body style="font-family:Arial; background:#f3f4f6; padding:40px;">

    <div style="max-width:700px; margin:0 auto; background:white; padding:24px; border-radius:8px;">
        <h1 style="color:#16a34a;">Booking Confirmed</h1>

        <p>Thank you. Your appointment has been booked successfully.</p>

        <hr style="margin:20px 0;">

        <h3>Appointment Details</h3>

        <p><strong>Customer:</strong> {{ $appointment->customer->full_name }}</p>
        <p><strong>Service:</strong> {{ $appointment->service->name }}</p>
        <p><strong>Staff:</strong> {{ $appointment->staff->name }}</p>
        <p><strong>Date:</strong> {{ $appointment->appointment_date }}</p>
        <p><strong>Time:</strong> {{ $appointment->appointment_time }}</p>
        <p><strong>Duration:</strong> {{ $appointment->duration }} minutes</p>
        <p><strong>Price:</strong> ${{ number_format($appointment->price, 2) }}</p>
        <p><strong>Status:</strong> {{ ucfirst($appointment->status) }}</p>

        @if($appointment->notes)
            <p><strong>Notes:</strong> {{ $appointment->notes }}</p>
        @endif

        <div style="margin-top:24px;">
            <a href="{{ route('booking.form') }}"
               style="background:#2563eb; color:white; padding:10px 16px; border-radius:6px; text-decoration:none;">
                Book Another Appointment
            </a>
        </div>
    </div>

</body>
</html>
