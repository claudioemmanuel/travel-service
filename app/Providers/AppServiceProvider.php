<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\Repositories\TravelRequestRepositoryInterface;
use App\Repositories\TravelRequestRepository;
use App\Contracts\Services\TravelRequestServiceInterface;
use App\Services\TravelRequestService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TravelRequestRepositoryInterface::class, TravelRequestRepository::class);
        $this->app->bind(TravelRequestServiceInterface::class, TravelRequestService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
