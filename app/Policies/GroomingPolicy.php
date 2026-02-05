<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class GroomingPolicy
{
    public function viewAny(User $user): bool { return $user->hasPermission('dashboard.groomer.view'); }

    public function execute(User $user, Appointment $appointment): bool
    {
        return $user->tenant_id === $appointment->tenant_id
            && $appointment->service?->service_type === 'grooming'
            && ($user->hasRole('Admin') || $user->hasRole('Peluquero'));
    }
}
