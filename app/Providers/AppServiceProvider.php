<?php

namespace App\Providers;

use App\Interfaces\Auth\AuthenticationInterface;
use App\Interfaces\Events\EventInterface;
use App\Interfaces\Events\EventTypeInterface;
use App\Interfaces\Location\LocationInterface;
use App\Interfaces\Reservations\ReservationsInterface;
use App\Services\Auth\AuthenticationService;
use App\Services\Events\EventService;
use App\Services\Events\EventTypeService;
use App\Services\Loaction\LocationService;
use App\Services\Reservations\ReservationsService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
       
        $this->app->bind(AuthenticationInterface::class, AuthenticationService::class);
        $this->app->bind(EventTypeInterface::class, EventTypeService::class);
        $this->app->bind(LocationInterface::class, LocationService::class);
        $this->app->bind(EventInterface::class, EventService::class);
        $this->app->bind(ReservationsInterface::class, ReservationsService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
