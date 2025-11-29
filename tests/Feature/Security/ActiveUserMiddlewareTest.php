<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActiveUserMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_inactive_user_cannot_access_dashboard()
    {
        $user = User::factory()->create([
            'role' => 'member',
            'is_active' => false,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        // Should be redirected to login with error
        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
    }

    public function test_active_user_can_access_dashboard()
    {
        $user = User::factory()->create([
            'role' => 'member',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        // Should redirect to member dashboard (not blocked)
        $response->assertRedirect(route('member.dashboard'));
    }

    public function test_inactive_admin_cannot_access_admin_dashboard()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => false,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_inactive_leader_cannot_access_leader_dashboard()
    {
        $leader = User::factory()->create([
            'role' => 'leader',
            'is_active' => false,
        ]);

        $response = $this->actingAs($leader)->get(route('leader.dashboard'));

        $response->assertRedirect(route('login'));
    }
}
