<?php

namespace App\Jobs;

use App\Models\Appointment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendAppointmentWhatsappReminderJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $appointmentId)
    {
    }

    public function handle(): void
    {
        $appointment = Appointment::query()->find($this->appointmentId);

        if (! $appointment) {
            return;
        }

        // Stub de integración:
        // Aquí se conectaría con tu proveedor de WhatsApp Business API
        // y se enviaría el recordatorio usando phone del customer.
        logger()->info('WhatsApp reminder stub executed', [
            'appointment_id' => $appointment->id,
            'customer_id' => $appointment->customer_id,
            'start_at' => $appointment->start_at,
        ]);
    }
}
