<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment->load(['customer', 'service', 'staff']);
    }

    public function build()
    {
        return $this->subject('Appointment Confirmation')
            ->view('emails.appointment-confirmed');
    }
}
