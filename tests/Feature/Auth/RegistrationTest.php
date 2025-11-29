<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered()
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
    }

    public function test_new_users_can_register()
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // User should NOT be authenticated (OTP verification required)
        $this->assertGuest();
        
        // User should be redirected to OTP verification page
        $response->assertRedirect(route('otp.show'));
        
        // User should exist in database but not be active
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'is_active' => false,
        ]);
    }
}
