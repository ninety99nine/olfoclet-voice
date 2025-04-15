<?php

namespace App\Providers;

use App\Models\Organization;
use Illuminate\Foundation\Http\Kernel;
use App\Observers\OrganizationObserver;
use Illuminate\Support\ServiceProvider;
use App\Http\Middleware\OrganisationPermission;
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
        Organization::observe(OrganizationObserver::class);

        /** @var Kernel $kernel */
        $kernel = app()->make(Kernel::class);

        //  Reference: https://spatie.be/docs/laravel-permission/v6/basic-usage/teams-permissions
        $kernel->addToMiddlewarePriorityBefore(
            SubstituteBindings::class,
            OrganisationPermission::class
        );
    }
}
