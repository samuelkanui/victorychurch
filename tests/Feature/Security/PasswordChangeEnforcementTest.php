<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordChangeEnforcementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_requiring_password_change_cannot_access_dashboard()
    {
        $user = User::factory()->create([
            'role' => 'member',
            'is_active' => true,
            'requires_password_change' => true,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        // Should be redirected to password change page
        $response->assertRedirect(route('user-password.edit'));
    }

    public function test_user_requiring_password_change_can_access_password_page()
    {
        $user = User::factory()->create([
            'requires_password_change' => true,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('user-password.edit'));

        $response->assertStatus(200);
    }

    public function test_password_change_clears_requirement_flag()
    {
        $user = User::factory()->create([
            'requires_password_change' => true,
            'is_active' => true,
            'password' => bcrypt('old-password'),
        ]);

        $this->actingAs($user)->put(route('user-password.update'), [
            'current_password' => 'old-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        // Flag should be cleared (database stores as 0 for false)
        $this->assertEquals(0, $user->fresh()->requires_password_change);
    }

    public function test_user_requiring_password_change_can_logout()
    {
        $user = User::factory()->create([
            'requires_password_change' => true,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('logout'));

        $this->assertGuest();
        $response->assertRedirect('/');
    }

}
