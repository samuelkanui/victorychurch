<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GroupController extends Controller
{
    /**
     * Display a listing of the leader's groups.
     */
    public function index(): Response
    {
        $leader = auth()->user();
        
        $groups = $leader->ledGroups()
            ->withCount(['members', 'approvedMembers'])
            ->with(['members' => function ($query) {
                $query->where('group_user.status', 'pending')
                      ->withPivot(['status', 'joined_at']);
            }])
            ->get();

        return Inertia::render('leader/Groups/Index', [
            'groups' => $groups,
        ]);
    }

    /**
     * Display the specified group.
     */
    public function show(Group $group): Response
    {
        // Ensure the leader owns this group
        if ($group->leader_id !== auth()->id()) {
            abort(403, 'You can only view your own groups.');
        }

        $group->load([
            'members' => function ($query) {
                $query->withPivot(['status', 'role', 'joined_at', 'status_changed_at']);
            }
        ]);

        // Get member statistics
        $memberStats = [
            'total' => $group->members->count(),
            'approved' => $group->members->where('pivot.status', 'approved')->count(),
            'pending' => $group->members->where('pivot.status', 'pending')->count(),
            'rejected' => $group->members->where('pivot.status', 'rejected')->count(),
            'banned' => $group->members->where('pivot.status', 'banned')->count(),
        ];

        return Inertia::render('leader/Groups/Show', [
            'group' => $group,
            'memberStats' => $memberStats,
        ]);
    }

    /**
     * Update the specified group.
     */
    public function update(Request $request, Group $group)
    {
        // Ensure the leader owns this group
        if ($group->leader_id !== auth()->id()) {
            abort(403, 'You can only update your own groups.');
        }

        $validated = $request->validate([
            'description' => 'nullable|string',
            'meeting_schedule' => 'nullable|string|max:255',
            'max_members' => 'required|integer|min:1|max:100',
            'is_active' => 'required|boolean',
        ]);

        $group->update($validated);

        return redirect()->route('leader.groups.show', $group)
            ->with('success', 'Group updated successfully.');
    }
}
