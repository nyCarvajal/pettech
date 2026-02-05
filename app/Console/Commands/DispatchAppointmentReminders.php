<?php

namespace App\Console\Commands;

use App\Jobs\SendAppointmentWhatsappReminderJob;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DispatchAppointmentReminders extends Command
{
    protected $signature = 'appointments:dispatch-reminders {--minutes=120 : Minutos antes de la cita para enviar recordatorio}';

    protected $description = 'Despacha jobs de recordatorios WhatsApp para citas próximas.';

    public function handle(): int
    {
        $minutes = (int) $this->option('minutes');
        $windowStart = Carbon::now()->addMinutes($minutes - 5);
        $windowEnd = Carbon::now()->addMinutes($minutes + 5);

        $appointments = Appointment::query()
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->whereBetween('start_at', [$windowStart, $windowEnd])
            ->get();

        foreach ($appointments as $appointment) {
            SendAppointmentWhatsappReminderJob::dispatch($appointment->id);
        }

        $this->info("Recordatorios despachados: {$appointments->count()}");

        return self::SUCCESS;
    }
}
