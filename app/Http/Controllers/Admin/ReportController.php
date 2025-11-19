<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Group;
use App\Models\PrayerRequest;
use App\Models\Meeting;
use App\Models\Resource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    /**
     * Display reports and analytics.
     */
    public function index(): Response
    {
        $totalUsers = User::count();
        $totalGroups = Group::count();
        $activeGroups = Group::where('is_active', true)->count();
        
        // Calculate system health based on activity
        $activityScore = 0;
        if ($totalUsers > 0) $activityScore += 25;
        if ($totalGroups > 0) $activityScore += 25;
        if ($activeGroups > 0) $activityScore += 25;
        if ($activeGroups > 0 && $totalGroups > 0) {
            $activePercentage = ($activeGroups / $totalGroups) * 100;
            if ($activePercentage >= 50) $activityScore += 25;
        }
        
        // Determine health status
        if ($activityScore >= 75) {
            $systemHealth = 'Excellent';
        } elseif ($activityScore >= 50) {
            $systemHealth = 'Good';
        } elseif ($activityScore >= 25) {
            $systemHealth = 'Fair';
        } else {
            $systemHealth = 'Needs Attention';
        }
        
        // Get user growth data (last 6 months)
        $userGrowth = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $userGrowth[] = [
                'month' => $month->format('M Y'),
                'count' => User::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count()
            ];
        }
        
        // Get group activity data
        $groupActivity = Group::with('members')
            ->get()
            ->map(function ($group) {
                return [
                    'name' => $group->name,
                    'members' => $group->members->count(),
                    'is_active' => $group->is_active
                ];
            })
            ->sortByDesc('members')
            ->take(5)
            ->values();
        
        // Get recent prayer requests
        $recentPrayers = PrayerRequest::with('user')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($prayer) {
                return [
                    'title' => $prayer->title,
                    'user' => $prayer->user->name,
                    'created_at' => $prayer->created_at->diffForHumans()
                ];
            });
        
        // Get upcoming meetings
        $upcomingMeetings = Meeting::with('group')
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->take(5)
            ->get()
            ->map(function ($meeting) {
                return [
                    'title' => $meeting->title,
                    'group' => $meeting->group->name ?? 'General',
                    'scheduled_at' => $meeting->scheduled_at->format('M d, Y g:i A')
                ];
            });
        
        $stats = [
            'total_users' => $totalUsers,
            'total_groups' => $totalGroups,
            'total_prayers' => PrayerRequest::count(),
            'total_meetings' => Meeting::count(),
            'total_resources' => Resource::count(),
            'active_groups' => $activeGroups,
            'recent_signups' => User::where('created_at', '>=', now()->subDays(30))->count(),
            'system_health' => $systemHealth,
        ];

        return Inertia::render('admin/Reports/Index', [
            'stats' => $stats,
            'userGrowth' => $userGrowth,
            'groupActivity' => $groupActivity,
            'recentPrayers' => $recentPrayers,
            'upcomingMeetings' => $upcomingMeetings,
        ]);
    }
}
