<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Group;
use App\Models\PrayerRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(Request $request): Response
    {
        // Real data from the database
        $stats = [
            'totalUsers' => User::count(),
            'activeUsers' => User::where('is_active', true)->count(),
            'newUsersThisMonth' => User::where('created_at', '>=', now()->startOfMonth())->count(),
            'totalGroups' => Group::count(),
            'activeGroups' => Group::where('is_active', true)->count(),
            'totalAssignments' => 0, // Will be Assignment::count() when implemented
            'pendingSubmissions' => 0, // Will be AssignmentSubmission::whereNull('grade')->count() when implemented
            'prayerRequests' => PrayerRequest::where('status', 'pending')->count(),
            'upcomingMeetings' => 0, // Will be Meeting::where('scheduled_at', '>', now())->count() when implemented
            'systemAlerts' => $this->calculateSystemAlerts(),
        ];

        $recentActivity = $this->getRecentActivity();

        return Inertia::render('admin/Dashboard', [
            'stats' => $stats,
            'recentActivity' => $recentActivity,
        ]);
    }

    /**
     * Calculate system alerts based on various conditions
     */
    private function calculateSystemAlerts(): int
    {
        $alerts = 0;

        // Check for inactive users with recent activity
        $inactiveUsers = User::where('is_active', false)
            ->whereNotNull('last_login_at')
            ->where('last_login_at', '>', now()->subDays(30))
            ->count();
        
        $alerts += $inactiveUsers;

        // Check for groups without recent activity
        $inactiveGroups = Group::where('is_active', true)
            ->whereDoesntHave('members', function ($query) {
                $query->where('group_user.updated_at', '>', now()->subDays(30));
            })
            ->count();
        
        $alerts += $inactiveGroups;

        // Check for unverified users
        $unverifiedUsers = User::whereNull('email_verified_at')
            ->where('created_at', '<', now()->subDays(7))
            ->count();
        
        $alerts += $unverifiedUsers;

        return $alerts;
    }

    /**
     * Get recent activity from various models
     */
    private function getRecentActivity(): array
    {
        $activities = [];

        // Recent user registrations
        $recentUsers = User::latest()
            ->limit(3)
            ->get(['id', 'name', 'created_at']);

        foreach ($recentUsers as $user) {
            $activities[] = [
                'id' => 'user_' . $user->id,
                'type' => 'user_registered',
                'description' => 'New user registered',
                'user' => $user->name,
                'timestamp' => $user->created_at->diffForHumans(),
            ];
        }

        // Recent groups created
        $recentGroups = Group::with('leader')
            ->latest()
            ->limit(2)
            ->get();

        foreach ($recentGroups as $group) {
            $activities[] = [
                'id' => 'group_' . $group->id,
                'type' => 'group_created',
                'description' => "New group '{$group->name}' created",
                'user' => $group->leader->name,
                'timestamp' => $group->created_at->diffForHumans(),
            ];
        }

        // Recent prayer requests
        $recentPrayers = PrayerRequest::with('user')
            ->latest()
            ->limit(3)
            ->get();

        foreach ($recentPrayers as $prayer) {
            $userName = $prayer->is_anonymous ? 'Anonymous' : $prayer->user->name;
            $activities[] = [
                'id' => 'prayer_' . $prayer->id,
                'type' => 'prayer_request',
                'description' => "New prayer request: {$prayer->title}",
                'user' => $userName,
                'timestamp' => $prayer->created_at->diffForHumans(),
            ];
        }

        // Sort by timestamp and return latest 8 activities
        return collect($activities)
            ->sortByDesc(function ($activity) {
                // Convert timestamp back to compare
                return now()->parse($activity['timestamp']);
            })
            ->take(8)
            ->values()
            ->toArray();
    }
}
