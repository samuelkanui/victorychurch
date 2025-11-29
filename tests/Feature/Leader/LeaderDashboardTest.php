<?php

namespace Tests\Feature\Leader;

use App\Models\User;
use App\Models\Group;
use App\Models\Assignment;
use App\Models\Meeting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaderDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_leader_can_access_dashboard()
    {
        $leader = User::factory()->create([
            'role' => 'leader',
            'is_active' => true,
        ]);

        $response = $this->actingAs($leader)->get(route('leader.dashboard'));

        $response->assertStatus(200);
    }

    public function test_non_leader_cannot_access_leader_dashboard()
    {
        $member = User::factory()->create([
            'role' => 'member',
            'is_active' => true,
        ]);

        $response = $this->actingAs($member)->get(route('leader.dashboard'));

        $response->assertStatus(403);
    }

    public function test_leader_can_view_their_groups()
    {
        $leader = User::factory()->create([
            'role' => 'leader',
            'is_active' => true,
        ]);

        Group::factory()->create([
            'leader_id' => $leader->id,
        ]);

        $response = $this->actingAs($leader)->get(route('leader.groups.index'));

        $response->assertStatus(200);
    }

    public function test_leader_can_create_assignment()
    {
        $leader = User::factory()->create([
            'role' => 'leader',
            'is_active' => true,
        ]);

        $group = Group::factory()->create([
            'leader_id' => $leader->id,
        ]);

        $response = $this->actingAs($leader)->post(route('leader.assignments.store'), [
            'group_id' => $group->id,
            'title' => 'Test Assignment',
            'description' => 'Test Description',
            'type' => 'bible_study',
            'due_date' => now()->addDays(7)->format('Y-m-d'),
        ]);

        $this->assertDatabaseHas('assignments', [
            'title' => 'Test Assignment',
            'group_id' => $group->id,
            'type' => 'bible_study',
        ]);
    }

    public function test_leader_can_create_meeting()
    {
        $leader = User::factory()->create([
            'role' => 'leader',
            'is_active' => true,
        ]);

        $group = Group::factory()->create([
            'leader_id' => $leader->id,
        ]);

        $response = $this->actingAs($leader)->post(route('leader.meetings.store'), [
            'group_id' => $group->id,
            'title' => 'Test Meeting',
            'description' => 'Test Description',
            'scheduled_at' => now()->addDays(1)->format('Y-m-d H:i:s'),
            'type' => 'fellowship',
            'meeting_type' => 'online',
            'meeting_link' => 'https://zoom.us/test', // Note: Controller expects meeting_url for online meetings
            'meeting_url' => 'https://zoom.us/test',
            'duration_minutes' => 60,
            'is_recurring' => false,
        ]);

        $this->assertDatabaseHas('meetings', [
            'title' => 'Test Meeting',
            'group_id' => $group->id,
            'type' => 'fellowship',
        ]);
    }

    public function test_leader_can_view_group_members()
    {
        $leader = User::factory()->create([
            'role' => 'leader',
            'is_active' => true,
        ]);

        $group = Group::factory()->create([
            'leader_id' => $leader->id,
        ]);

        $response = $this->actingAs($leader)->get(route('leader.groups.members.index', $group));

        $response->assertStatus(200);
    }

    public function test_leader_cannot_access_other_leaders_groups()
    {
        $leader1 = User::factory()->create([
            'role' => 'leader',
            'is_active' => true,
        ]);

        $leader2 = User::factory()->create([
            'role' => 'leader',
            'is_active' => true,
        ]);

        $group = Group::factory()->create([
            'leader_id' => $leader2->id,
        ]);

        $response = $this->actingAs($leader1)->get(route('leader.groups.show', $group));

        $response->assertStatus(403);
    }
}
