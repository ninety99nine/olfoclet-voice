<?php

namespace Tests\Unit\Listeners;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Events\RoleAttached;
use Spatie\Permission\Events\RoleDetached;
use Tests\TestCase;

class RoleEventListenerTest extends TestCase
{
    protected $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = new AuthService();
    }

    public function test_cache_invalidated_on_role_attached()
    {
        $user = User::factory()->create();

        // Cache super admin status
        $this->authService->isSuperAdmin($user);
        $this->assertTrue(Cache::has("super_admin:{$user->id}"));

        // Trigger RoleAttached event
        event(new RoleAttached($user, 'super_admin'));

        // Cache should be invalidated
        $this->assertFalse(Cache::has("super_admin:{$user->id}"));
    }

    public function test_cache_invalidated_on_role_detached()
    {
        $user = User::factory()->create();
        $user->assignRole(['name' => 'super_admin', 'organization_id' => null]);

        // Cache super admin status
        $this->assertTrue($this->authService->isSuperAdmin($user));
        $this->assertTrue(Cache::has("super_admin:{$user->id}"));

        // Trigger RoleDetached event
        event(new RoleDetached($user, 'super_admin'));

        // Cache should be invalidated
        $this->assertFalse(Cache::has("super_admin:{$user->id}"));
        $this->assertFalse($this->authService->isSuperAdmin($user));
    }

    public function test_role_event_listener_registered()
    {
        $user = User::factory()->create();

        // Cache super admin status
        $this->authService->isSuperAdmin($user);
        $this->assertTrue(Cache::has("super_admin:{$user->id}"));

        // Trigger event to verify subscriber is handling it
        event(new RoleAttached($user, 'super_admin'));

        // Cache should be invalidated by RoleEventListener
        $this->assertFalse(Cache::has("super_admin:{$user->id}"));
    }
}
