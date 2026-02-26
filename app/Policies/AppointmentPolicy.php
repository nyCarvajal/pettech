<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('recepcion') || $user->hasRole('groomer');
    }

    public function view(User $user, Appointment $appointment): bool
    {
        if ((int) $user->tenant_id !== (int) $appointment->tenant_id) {
            return false;
        }

        if ($user->hasRole('recepcion')) {
            return true;
        }

        return $user->hasRole('groomer') && (int) $appointment->assigned_to_user_id === (int) $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('recepcion');
    }

    public function update(User $user, Appointment $appointment): bool
    {
        return $this->view($user, $appointment);
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        return $user->hasRole('recepcion')
            && (int) $user->tenant_id === (int) $appointment->tenant_id;
    }
}
