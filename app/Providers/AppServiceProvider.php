<?php

namespace App\Providers;

use App\Models\Organization;
use App\Listeners\RoleEventListener;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Http\Kernel;
use App\Observers\OrganizationObserver;
use Illuminate\Support\ServiceProvider;
use App\Http\Middleware\OrganisationPermission;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Middleware\SubstituteBindings;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();

        //  Events
        Event::subscribe(RoleEventListener::class);

        //  Observers
        Organization::observe(OrganizationObserver::class);

        /**
         *  Reference: https://spatie.be/docs/laravel-permission/v6/basic-usage/teams-permissions
         *
         *  @var Kernel $kernel
         */
        $kernel = app()->make(Kernel::class);

        $kernel->addToMiddlewarePriorityBefore(
            SubstituteBindings::class,
            OrganisationPermission::class
        );
    }
}
