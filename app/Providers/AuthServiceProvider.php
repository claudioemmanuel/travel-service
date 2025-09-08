<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\TravelRequest;
use App\Policies\TravelRequestPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        TravelRequest::class => TravelRequestPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
