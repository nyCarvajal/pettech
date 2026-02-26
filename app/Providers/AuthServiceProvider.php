<?php

namespace App\Providers;

use App\Models\GroomingSession;
use App\Policies\PetPolicy;
use App\Policies\CustomerPolicy;
use App\Models\Pet;
use App\Models\Customer;
use App\Policies\GroomingSessionPolicy;
// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        GroomingSession::class => GroomingSessionPolicy::class,
        Customer::class => CustomerPolicy::class,
        Pet::class => PetPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
