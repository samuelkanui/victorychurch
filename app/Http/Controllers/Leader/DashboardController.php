<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use App\Models\PrayerRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the leader dashboard.
     */
    public function index(): Response
    {
        $leader = auth()->user();
        
        // Get leader's groups with member counts
        $groups = $leader->ledGroups()
            ->withCount(['members', 'approvedMembers'])
            ->with(['members' => function ($query) {
                $query->wherePivot('status', 'pending')
                      ->withPivot(['status', 'joined_at']);
            }])
            ->get();

        // Calculate statistics
        $stats = [
            'total_groups' => $groups->count(),
            'total_members' => $groups->sum('approved_members_count'),
            'pending_requests' => $groups->sum(function ($group) {
                return $group->members->count();
            }),
            'active_groups' => $groups->where('is_active', true)->count(),
        ];

        // Get recent prayer requests from leader's groups
        $groupIds = $groups->pluck('id');
        $recentPrayers = PrayerRequest::whereHas('user.groups', function ($query) use ($groupIds) {
                $query->whereIn('groups.id', $groupIds)
                      ->where('group_user.status', 'approved');
            })
            ->with(['user'])
            ->where('privacy', '!=', 'private')
            ->latest()
            ->limit(5)
            ->get();

        // Get recent activity
        $recentActivity = $this->getRecentActivity($leader, $groups);

        return Inertia::render('leader/Dashboard', [
            'stats' => $stats,
            'groups' => $groups,
            'recentPrayers' => $recentPrayers,
            'recentActivity' => $recentActivity,
        ]);
    }

    /**
     * Display reports and analytics.
     */
    public function reports(): Response
    {
        $leader = auth()->user();
        
        // Get detailed analytics for leader's groups
        $groups = $leader->ledGroups()
            ->withCount(['members', 'approvedMembers'])
            ->with(['members' => function ($query) {
                $query->withPivot(['status', 'joined_at']);
            }])
            ->get();

        // Group growth analytics
        $groupGrowth = $groups->map(function ($group) {
            $membersByMonth = $group->members()
                ->where('group_user.status', 'approved')
                ->selectRaw("strftime('%Y-%m', group_user.joined_at) as month, COUNT(*) as count")
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            return [
                'group' => $group,
                'growth' => $membersByMonth,
            ];
        });

        // Prayer request analytics
        $prayerAnalytics = PrayerRequest::whereHas('user.groups', function ($query) use ($groups) {
                $query->whereIn('groups.id', $groups->pluck('id'))
                      ->where('group_user.status', 'approved');
            })
            ->selectRaw("strftime('%Y-%m', created_at) as month, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return Inertia::render('leader/Reports', [
            'groups' => $groups,
            'groupGrowth' => $groupGrowth,
            'prayerAnalytics' => $prayerAnalytics,
        ]);
    }

    /**
     * Get recent activity for the leader's groups
     */
    private function getRecentActivity($leader, $groups): array
    {
        $activities = [];

        // Recent member requests
        foreach ($groups as $group) {
            $pendingMembers = $group->members()
                ->wherePivot('status', 'pending')
                ->withPivot(['joined_at'])
                ->latest('group_user.joined_at')
                ->limit(3)
                ->get();

            foreach ($pendingMembers as $member) {
                $activities[] = [
                    'id' => 'member_request_' . $group->id . '_' . $member->id,
                    'type' => 'member_request',
                    'description' => "New member request for '{$group->name}'",
                    'user' => $member->name,
                    'group' => $group->name,
                    'timestamp' => \Carbon\Carbon::parse($member->pivot->joined_at)->diffForHumans(),
                    'sort_timestamp' => \Carbon\Carbon::parse($member->pivot->joined_at),
                    'action_url' => route('leader.groups.members.index', $group),
                ];
            }
        }

        // Recent prayer requests
        $groupIds = $groups->pluck('id');
        $recentPrayers = PrayerRequest::whereHas('user.groups', function ($query) use ($groupIds) {
                $query->whereIn('groups.id', $groupIds)
                      ->where('group_user.status', 'approved');
            })
            ->with(['user'])
            ->where('privacy', '!=', 'private')
            ->latest()
            ->limit(3)
            ->get();

        foreach ($recentPrayers as $prayer) {
            $userName = $prayer->is_anonymous ? 'Anonymous' : ($prayer->user->name ?? 'Unknown User');
            $activities[] = [
                'id' => 'prayer_' . $prayer->id,
                'type' => 'prayer_request',
                'description' => "New prayer request: {$prayer->title}",
                'user' => $userName,
                'timestamp' => $prayer->created_at->diffForHumans(),
                'sort_timestamp' => $prayer->created_at,
                'action_url' => route('leader.prayers.show', $prayer),
            ];
        }

        // Sort by timestamp and return latest 10 activities
        return collect($activities)
            ->sortByDesc('sort_timestamp')
            ->take(10)
            ->values()
            ->toArray();
    }
}
