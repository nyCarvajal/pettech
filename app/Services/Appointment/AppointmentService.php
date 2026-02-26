<?php

namespace App\Services\Appointment;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Str;

class AppointmentService
{
    public function create(array $data, User $user): Appointment
    {
        $data['tenant_id'] = $user->tenant_id;
        $data['created_by'] = $user->id;
        $data['status'] = $data['status'] ?? 'scheduled';
        $data['code'] = $data['code'] ?? $this->generateCode();

        $this->ensureNoOverlap(
            (int) $user->tenant_id,
            $data['assigned_to_user_id'] ?? null,
            $data['start_at'],
            $data['end_at']
        );

        return Appointment::query()->create($data);
    }

    public function update(Appointment $appointment, array $data, User $user): Appointment
    {
        $tenantId = (int) $appointment->tenant_id;

        $this->ensureNoOverlap(
            $tenantId,
            $data['assigned_to_user_id'] ?? null,
            $data['start_at'],
            $data['end_at'],
            $appointment->id
        );

        $appointment->update($data);

        return $appointment->refresh();
    }

    public function transition(Appointment $appointment, string $toStatus, ?string $note = null): Appointment
    {
        $allowed = [
            'confirm' => 'confirmed',
            'start' => 'in_progress',
            'finish' => 'done',
            'cancel' => 'cancelled',
        ];

        if (! in_array($toStatus, array_values($allowed), true)) {
            abort(422, 'Transición no permitida.');
        }

        $current = $appointment->status;
        $transitions = [
            'scheduled' => ['confirmed', 'cancelled', 'no_show'],
            'confirmed' => ['in_progress', 'cancelled', 'no_show'],
            'in_progress' => ['done', 'cancelled'],
            'done' => [],
            'cancelled' => [],
            'no_show' => [],
        ];

        if (! in_array($toStatus, $transitions[$current] ?? [], true)) {
            abort(422, 'Cambio de estado inválido.');
        }

        $updates = ['status' => $toStatus];

        if ($note) {
            $updates['notes'] = trim(($appointment->notes ? $appointment->notes.PHP_EOL : '').$note);
        }

        $appointment->update($updates);

        return $appointment->refresh();
    }

    public function ensureNoOverlap(
        int $tenantId,
        int|string|null $groomerId,
        string $startAt,
        string $endAt,
        ?int $ignoreAppointmentId = null
    ): void {
        if (! $groomerId) {
            return;
        }

        $query = Appointment::query()
            ->forTenant($tenantId)
            ->where('assigned_to_user_id', (int) $groomerId)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->where('start_at', '<', $endAt)
            ->where('end_at', '>', $startAt);

        if ($ignoreAppointmentId) {
            $query->whereKeyNot($ignoreAppointmentId);
        }

        if ($query->exists()) {
            abort(422, 'El groomer ya tiene una cita solapada en ese horario.');
        }
    }

    private function generateCode(): string
    {
        return 'APT-'.now()->format('Ymd-His').'-'.Str::upper(Str::random(6));
    }
}
