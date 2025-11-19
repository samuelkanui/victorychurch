<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GroupController extends Controller
{
    /**
     * Display a listing of groups.
     */
    public function index(Request $request): Response
    {
        $query = Group::with(['leader', 'members']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('leader', function ($leaderQuery) use ($search) {
                      $leaderQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $status = $request->get('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $groups = $query->withCount(['members', 'approvedMembers'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('admin/Groups/Index', [
            'groups' => $groups,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new group.
     */
    public function create(): Response
    {
        $leaders = User::where('role', 'leader')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        // Get members who are NOT in any group
        // Only show members with NO group membership (approved or pending)
        // Members with rejected/banned status CAN join other groups
        $availableMembers = User::where('role', 'member')
            ->where('is_active', true)
            ->whereDoesntHave('groups', function ($query) {
                // Exclude members who are currently in a group (approved or pending)
                $query->whereIn('group_user.status', ['approved', 'pending']);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return Inertia::render('admin/Groups/Create', [
            'leaders' => $leaders,
            'availableMembers' => $availableMembers,
        ]);
    }

    /**
     * Store a newly created group.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'leader_id' => 'required|exists:users,id',
            'max_members' => 'required|integer|min:1|max:100',
            'meeting_schedule' => 'nullable|string|max:255',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'exists:users,id',
        ]);

        // Verify the selected user is actually a leader
        $leader = User::findOrFail($validated['leader_id']);
        if ($leader->role !== 'leader') {
            return back()->withErrors(['leader_id' => 'Selected user must be a leader.']);
        }

        $group = Group::create($validated);

        // Attach selected members to the group with approved status
        if (!empty($validated['member_ids'])) {
            $memberData = [];
            foreach ($validated['member_ids'] as $memberId) {
                $memberData[$memberId] = [
                    'status' => 'approved',
                    'joined_at' => now(),
                ];
            }
            $group->members()->attach($memberData);
        }

        return redirect()->route('admin.groups.index')
            ->with('success', 'Group created successfully.');
    }

    /**
     * Display the specified group.
     */
    public function show(Group $group): Response
    {
        $group->load(['leader', 'members' => function ($query) {
            $query->withPivot(['status', 'role', 'joined_at', 'status_changed_at']);
        }]);

        return Inertia::render('admin/Groups/Show', [
            'group' => $group,
        ]);
    }

    /**
     * Show the form for editing the specified group.
     */
    public function edit(Group $group): Response
    {
        $leaders = User::where('role', 'leader')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $group->load(['members' => function ($query) {
            $query->withPivot('status', 'joined_at')
                  ->orderBy('name');
        }]);

        // Get available members (not in ANY group with approved or pending status)
        // Only show members with NO group membership (approved or pending)
        // Members with rejected/banned status CAN join other groups
        $availableMembers = User::where('role', 'member')
            ->where('is_active', true)
            ->whereDoesntHave('groups', function ($query) {
                // Exclude members who are currently in a group (approved or pending)
                $query->whereIn('group_user.status', ['approved', 'pending']);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return Inertia::render('admin/Groups/Edit', [
            'group' => $group,
            'leaders' => $leaders,
            'availableMembers' => $availableMembers,
        ]);
    }

    /**
     * Update the specified group.
     */
    public function update(Request $request, Group $group)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'leader_id' => 'required|exists:users,id',
            'max_members' => 'required|integer|min:1|max:100',
            'meeting_schedule' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        // Verify the selected user is actually a leader
        $leader = User::findOrFail($validated['leader_id']);
        if ($leader->role !== 'leader') {
            return back()->withErrors(['leader_id' => 'Selected user must be a leader.']);
        }

        $group->update($validated);

        return redirect()->route('admin.groups.index')
            ->with('success', 'Group updated successfully.');
    }

    /**
     * Remove the specified group.
     */
    public function destroy(Group $group)
    {
        $group->delete();

        return redirect()->route('admin.groups.index')
            ->with('success', 'Group deleted successfully.');
    }

    /**
     * Add members to the group.
     */
    public function addMembers(Request $request, Group $group)
    {
        $validated = $request->validate([
            'member_ids' => 'required|array',
            'member_ids.*' => 'exists:users,id',
        ]);

        // Check if group has capacity
        $currentMemberCount = $group->members()->count();
        $availableSpots = $group->max_members - $currentMemberCount;
        
        if (count($validated['member_ids']) > $availableSpots) {
            return back()->with('error', "Cannot add members. Only {$availableSpots} spot(s) available.");
        }

        // Attach members with pending status
        $memberData = [];
        foreach ($validated['member_ids'] as $memberId) {
            $memberData[$memberId] = [
                'status' => 'pending',
                'joined_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        $group->members()->attach($memberData);

        $count = count($validated['member_ids']);
        return back()->with('success', "{$count} member(s) added as pending - awaiting leader approval.");
    }

    /**
     * Remove a member from the group.
     */
    public function removeMember(Group $group, User $user)
    {
        $group->members()->detach($user->id);

        return back()->with('success', "{$user->name} has been removed from {$group->name}.");
    }
}
