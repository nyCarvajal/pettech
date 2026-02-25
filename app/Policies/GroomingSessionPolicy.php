<?php

namespace App\Policies;

use App\Models\GroomingSession;
use App\Models\User;

class GroomingSessionPolicy
{
    public function viewAny(User $user): bool
    {
        return (bool) $user->is_active;
    }

    public function view(User $user, GroomingSession $groomingSession): bool
    {
        return $groomingSession->groomer_user_id === $user->id
            && (int) $groomingSession->tenant_id === (int) $user->tenant_id;
    }

    public function update(User $user, GroomingSession $groomingSession): bool
    {
        return $this->view($user, $groomingSession);
    }
}
