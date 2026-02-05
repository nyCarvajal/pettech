<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function viewAny(User $user): bool { return $user->hasPermission('agenda.view'); }
    public function view(User $user, Appointment $appointment): bool { return $user->tenant_id === $appointment->tenant_id && $user->hasPermission('agenda.view'); }
    public function create(User $user): bool { return $user->hasPermission('agenda.create'); }
    public function update(User $user, Appointment $appointment): bool { return $user->tenant_id === $appointment->tenant_id && $user->hasPermission('agenda.update'); }
    public function delete(User $user, Appointment $appointment): bool { return $user->tenant_id === $appointment->tenant_id && $user->hasPermission('agenda.cancel'); }
}
