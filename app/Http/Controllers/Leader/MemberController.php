<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MemberController extends Controller
{
    /**
     * Display members of the specified group.
     */
    public function index(Group $group): Response
    {
        // Ensure the leader owns this group
        if ($group->leader_id !== auth()->id()) {
            abort(403, 'You can only manage your own groups.');
        }

        $group->load([
            'members' => function ($query) {
                $query->withPivot(['status', 'role', 'joined_at', 'status_changed_at', 'status_changed_by'])
                      ->orderBy('group_user.joined_at', 'desc');
            }
        ]);

        // Separate members by status
        $membersByStatus = [
            'pending' => $group->members->where('pivot.status', 'pending'),
            'approved' => $group->members->where('pivot.status', 'approved'),
            'rejected' => $group->members->where('pivot.status', 'rejected'),
            'banned' => $group->members->where('pivot.status', 'banned'),
        ];

        return Inertia::render('leader/Groups/Members', [
            'group' => $group,
            'membersByStatus' => $membersByStatus,
        ]);
    }

    /**
     * Approve a member's request to join the group.
     */
    public function approve(Group $group, User $user)
    {
        // Ensure the leader owns this group
        if ($group->leader_id !== auth()->id()) {
            abort(403, 'You can only manage your own groups.');
        }

        // Check if user is already a member
        if (!$group->members()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'User is not a member of this group.');
        }

        // Update member status
        $group->members()->updateExistingPivot($user->id, [
            'status' => 'approved',
            'status_changed_at' => now(),
            'status_changed_by' => auth()->id(),
        ]);

        return back()->with('success', "Approved {$user->name}'s membership request.");
    }

    /**
     * Reject a member's request to join the group.
     */
    public function reject(Group $group, User $user)
    {
        // Ensure the leader owns this group
        if ($group->leader_id !== auth()->id()) {
            abort(403, 'You can only manage your own groups.');
        }

        // Check if user is already a member
        if (!$group->members()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'User is not a member of this group.');
        }

        // Update member status
        $group->members()->updateExistingPivot($user->id, [
            'status' => 'rejected',
            'status_changed_at' => now(),
            'status_changed_by' => auth()->id(),
        ]);

        return back()->with('success', "Rejected {$user->name}'s membership request.");
    }

    /**
     * Ban a member from the group.
     */
    public function ban(Group $group, User $user)
    {
        // Ensure the leader owns this group
        if ($group->leader_id !== auth()->id()) {
            abort(403, 'You can only manage your own groups.');
        }

        // Check if user is already a member
        if (!$group->members()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'User is not a member of this group.');
        }

        // Update member status
        $group->members()->updateExistingPivot($user->id, [
            'status' => 'banned',
            'status_changed_at' => now(),
            'status_changed_by' => auth()->id(),
        ]);

        return back()->with('success', "Banned {$user->name} from the group.");
    }

    /**
     * Unban a member from the group.
     */
    public function unban(Group $group, User $user)
    {
        // Ensure the leader owns this group
        if ($group->leader_id !== auth()->id()) {
            abort(403, 'You can only manage your own groups.');
        }

        // Check if user is banned
        $member = $group->members()->where('user_id', $user->id)->first();
        if (!$member || $member->pivot->status !== 'banned') {
            return back()->with('error', 'User is not banned from this group.');
        }

        // Update member status back to approved
        $group->members()->updateExistingPivot($user->id, [
            'status' => 'approved',
            'status_changed_at' => now(),
            'status_changed_by' => auth()->id(),
        ]);

        return back()->with('success', "Unbanned {$user->name} from the group.");
    }

    /**
     * Remove a member from the group.
     */
    public function remove(Group $group, User $user)
    {
        // Ensure the leader owns this group
        if ($group->leader_id !== auth()->id()) {
            abort(403, 'You can only manage your own groups.');
        }

        // Check if user is a member
        if (!$group->members()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'User is not a member of this group.');
        }

        // Remove the member
        $group->members()->detach($user->id);

        return back()->with('success', "Removed {$user->name} from the group.");
    }
}
