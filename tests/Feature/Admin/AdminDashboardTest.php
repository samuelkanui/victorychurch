<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_dashboard()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    public function test_non_admin_cannot_access_admin_dashboard()
    {
        $member = User::factory()->create([
            'role' => 'member',
            'is_active' => true,
        ]);

        $response = $this->actingAs($member)->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    public function test_admin_can_view_users_list()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.users.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_groups_list()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.groups.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_create_group()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $leader = User::factory()->create([
            'role' => 'leader',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.groups.store'), [
            'name' => 'Test Group',
            'description' => 'Test Description',
            'leader_id' => $leader->id,
           'max_members' => 20,
        ]);

        $this->assertDatabaseHas('groups', [
            'name' => 'Test Group',
            'leader_id' => $leader->id,
        ]);
    }

    public function test_admin_can_delete_group()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $leader = User::factory()->create([
            'role' => 'leader',
            'is_active' => true,
        ]);

        $group = Group::factory()->create([
            'leader_id' => $leader->id,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.groups.destroy', $group));

        $this->assertDatabaseMissing('groups', [
            'id' => $group->id,
        ]);
    }

    public function test_admin_can_view_reports()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reports'));

        $response->assertStatus(200);
    }
}
