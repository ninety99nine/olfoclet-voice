<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Events\RoleAttached;
use Spatie\Permission\Events\RoleDetached;

class RoleEventListener
{
    /**
     * Handle the RoleAttached event.
     *
     * @param RoleAttached $event
     * @return void
     */
    public function handleRoleAttached(RoleAttached $event): void
    {
        $cacheKey = "super_admin:{$event->model->id}";
        if (!Cache::forget($cacheKey)) {
            Log::warning('Failed to clear super admin cache', ['user_id' => $event->model->id, 'cache_key' => $cacheKey]);
        }
    }

    /**
     * Handle the RoleDetached event.
     *
     * @param RoleDetached $event
     * @return void
     */
    public function handleRoleDetached(RoleDetached $event): void
    {
        $cacheKey = "super_admin:{$event->model->id}";
        if (!Cache::forget($cacheKey)) {
            Log::warning('Failed to clear super admin cache', ['user_id' => $event->model->id, 'cache_key' => $cacheKey]);
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher $events
     * @return void
     */
    public function subscribe($events): void
    {
        $events->listen(
            RoleAttached::class,
            [RoleEventListener::class, 'handleRoleAttached']
        );

        $events->listen(
            RoleDetached::class,
            [RoleEventListener::class, 'handleRoleDetached']
        );
    }
}
