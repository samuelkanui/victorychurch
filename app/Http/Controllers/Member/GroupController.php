<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GroupController extends Controller
{
    /**
     * Display member's groups.
     */
    public function index(): Response
    {
        $member = auth()->user();
        
        $groups = $member->groups()
            ->withPivot(['status', 'role', 'joined_at', 'status_changed_at'])
            ->with(['leader'])
            ->get();
            
        // Separate groups by status
        $groupsByStatus = [
            'approved' => $groups->where('pivot.status', 'approved'),
            'pending' => $groups->where('pivot.status', 'pending'),
            'rejected' => $groups->where('pivot.status', 'rejected'),
        ];

        return Inertia::render('member/Groups/Index', [
            'groupsByStatus' => $groupsByStatus,
        ]);
    }
    
    /**
     * Display available groups to join.
     */
    public function available(): Response
    {
        $member = auth()->user();
        
        // Get groups the member is not already part of
        $memberGroupIds = $member->groups()->pluck('groups.id');
        
        $availableGroups = Group::where('is_active', true)
            ->whereNotIn('id', $memberGroupIds)
            ->with(['leader'])
            ->get()
            ->filter(function ($group) {
                // Check if group has space for more members
                return $group->current_members_count < $group->max_members;
            })
            ->values(); // Reset array keys

        return Inertia::render('member/Groups/Available', [
            'availableGroups' => $availableGroups,
        ]);
    }
    
    /**
     * Display the specified group.
     */
    public function show(Group $group): Response
    {
        $member = auth()->user();
        
        // Get member's membership status if exists
        $membership = $member->groups()
            ->where('groups.id', $group->id)
            ->withPivot(['status', 'role', 'joined_at'])
            ->first();
        
        $group->load(['leader']);
        
        // Add membership info to group
        if ($membership) {
            $group->membership = [
                'status' => $membership->pivot->status,
                'role' => $membership->pivot->role,
                'joined_at' => $membership->pivot->joined_at,
            ];
        }
        
        // Count approved members
        $group->members_count = $group->members()
            ->where('group_user.status', 'approved')
            ->count();
        
        // Get recent assignments (only if approved member)
        $recentAssignments = [];
        $upcomingMeetings = [];
        $groupPrayers = [];
        
        if ($membership && $membership->pivot->status === 'approved') {
            $recentAssignments = $group->assignments()
                ->where('is_active', true)
                ->with(['submissions' => function ($query) use ($member) {
                    $query->where('user_id', $member->id);
                }])
                ->orderBy('due_date', 'desc')
                ->limit(5)
                ->get();
                
            // Get upcoming meetings
            $upcomingMeetings = $group->meetings()
                ->where('scheduled_at', '>', now())
                ->where('status', 'scheduled')
                ->orderBy('scheduled_at')
                ->limit(5)
                ->get();
                
            // Get group prayer requests
            $groupPrayers = $group->prayerRequests()
                ->with(['user'])
                ->whereIn('privacy', ['public', 'group'])
                ->latest()
                ->limit(10)
                ->get();
        }

        return Inertia::render('member/Groups/Show', [
            'group' => $group,
            'recentAssignments' => $recentAssignments,
            'upcomingMeetings' => $upcomingMeetings,
            'groupPrayers' => $groupPrayers,
        ]);
    }
    
    /**
     * Join a group.
     */
    public function join(Group $group)
    {
        $member = auth()->user();
        
        // Check if group is active and has space
        if (!$group->is_active) {
            return back()->with('error', 'This group is not currently accepting new members.');
        }
        
        if ($group->approvedMembers()->count() >= $group->max_members) {
            return back()->with('error', 'This group is full.');
        }
        
        // Check if already a member
        if ($member->groups()->where('groups.id', $group->id)->exists()) {
            return back()->with('error', 'You are already associated with this group.');
        }
        
        // Add member with pending status
        $member->groups()->attach($group->id, [
            'status' => 'pending',
            'role' => 'member',
            'joined_at' => now(),
        ]);
        
        return back()->with('success', "Your request to join '{$group->name}' has been submitted and is pending approval.");
    }
    
    /**
     * Leave a group.
     */
    public function leave(Group $group)
    {
        $member = auth()->user();
        
        // Check if member is part of this group
        $membership = $member->groups()->where('groups.id', $group->id)->first();
        
        if (!$membership) {
            return back()->with('error', 'You are not a member of this group.');
        }
        
        // Remove member from group
        $member->groups()->detach($group->id);
        
        return redirect()->route('member.groups.index')
            ->with('success', "You have left '{$group->name}' group.");
    }
}
