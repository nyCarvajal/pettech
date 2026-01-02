<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;

class AuditLogger
{
    public function log(?User $user, string $action, string $entity, ?int $entityId = null, array $payload = [], ?string $ip = null): void
    {
        AuditLog::create([
            'user_id' => $user?->id,
            'action' => $action,
            'entity' => $entity,
            'entity_id' => $entityId,
            'payload_json' => $payload,
            'ip' => $ip,
        ]);
    }
}
