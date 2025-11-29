<?php

namespace Tests\Feature\Member;

use App\Models\User;
use App\Models\Group;
use App\Models\PrayerRequest;
use App\Models\Assignment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_access_dashboard()
    {
        $member = User::factory()->create([
            'role' => 'member',
            'is_active' => true,
        ]);

        $response = $this->actingAs($member)->get(route('member.dashboard'));

        $response->assertStatus(200);
    }

    public function test_member_can_view_available_groups()
    {
        $member = User::factory()->create([
            'role' => 'member',
            'is_active' => true,
        ]);

        $leader = User::factory()->create([
            'role' => 'leader',
            'is_active' => true,
        ]);

        Group::factory()->create([
            'leader_id' => $leader->id,
        ]);

        $response = $this->actingAs($member)->get(route('member.groups.available'));

        $response->assertStatus(200);
    }

    public function test_member_can_join_public_group()
    {
        $member = User::factory()->create([
            'role' => 'member',
            'is_active' => true,
        ]);

        $leader = User::factory()->create([
            'role' => 'leader',
            'is_active' => true,
        ]);

        $group = Group::factory()->create([
            'leader_id' => $leader->id,
        ]);

        $response = $this->actingAs($member)->post(route('member.groups.join', $group));

        $this->assertDatabaseHas('group_user', [
            'group_id' => $group->id,
            'user_id' => $member->id,
            'status' => 'pending',
        ]);
    }

    public function test_member_can_create_prayer_request()
    {
        $member = User::factory()->create([
            'role' => 'member',
            'is_active' => true,
        ]);

        $response = $this->actingAs($member)->post(route('member.prayers.store'), [
            'title' => 'Test Prayer',
            'description' => 'Please pray for me',
            'privacy' => 'public',
            'is_anonymous' => false,
        ]);

        $this->assertDatabaseHas('prayer_requests', [
            'title' => 'Test Prayer',
            'user_id' => $member->id,
            'privacy' => 'public',
        ]);
    }

    public function test_member_can_view_own_prayer_requests()
    {
        $member = User::factory()->create([
            'role' => 'member',
            'is_active' => true,
        ]);

        PrayerRequest::factory()->create([
            'user_id' => $member->id,
        ]);

        $response = $this->actingAs($member)->get(route('member.prayers.index'));

        $response->assertStatus(200);
    }

    public function test_member_can_view_group_assignments()
    {
        $member = User::factory()->create([
            'role' => 'member',
            'is_active' => true,
        ]);

        $leader = User::factory()->create([
            'role' => 'leader',
            'is_active' => true,
        ]);

        $group = Group::factory()->create([
            'leader_id' => $leader->id,
        ]);

        // Add member to group as approved
        $group->members()->attach($member->id, ['status' => 'approved']);

        $response = $this->actingAs($member)->get(route('member.assignments.index'));

        $response->assertStatus(200);
    }

    public function test_member_can_view_own_profile()
    {
        $member = User::factory()->create([
            'role' => 'member',
            'is_active' => true,
        ]);

        $response = $this->actingAs($member)->get(route('member.profile.show'));

        $response->assertStatus(200);
    }

    public function test_member_can_update_own_profile()
    {
        $member = User::factory()->create([
            'role' => 'member',
            'is_active' => true,
            'name' => 'Old Name',
        ]);

        $response = $this->actingAs($member)->put(route('member.profile.update'), [
            'name' => 'New Name',
            'email' => $member->email,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $member->id,
            'name' => 'New Name',
        ]);
    }

    public function test_member_cannot_access_admin_routes()
    {
        $member = User::factory()->create([
            'role' => 'member',
            'is_active' => true,
        ]);

        $response = $this->actingAs($member)->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    public function test_member_cannot_access_leader_routes()
    {
        $member = User::factory()->create([
            'role' => 'member',
            'is_active' => true,
        ]);

        $response = $this->actingAs($member)->get(route('leader.dashboard'));

        $response->assertStatus(403);
    }
}
