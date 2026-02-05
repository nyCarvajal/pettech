<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\InventoryMovement;
use App\Models\Invoice;
use App\Policies\AppointmentPolicy;
use App\Policies\GroomingPolicy;
use App\Policies\InventoryMovementPolicy;
use App\Policies\InvoicePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Appointment::class => AppointmentPolicy::class,
        Invoice::class => InvoicePolicy::class,
        InventoryMovement::class => InventoryMovementPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('grooming.execute', [GroomingPolicy::class, 'execute']);
    }
}
