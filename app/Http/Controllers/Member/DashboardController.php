<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\PrayerRequest;
use App\Models\Assignment;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the member dashboard.
     */
    public function index(): Response
    {
        $member = auth()->user();
        
        // Get member's groups with leader information
        $memberGroups = $member->groups()
            ->with(['leader'])
            ->withPivot(['status', 'joined_at'])
            ->where('group_user.status', 'approved')
            ->get();
            
        $groupIds = $memberGroups->pluck('id');
        
        // Get statistics (with fallbacks to avoid errors)
        $stats = [
            'my_groups' => $memberGroups->count(),
            'pending_applications' => $member->groups()
                ->where('group_user.status', 'pending')
                ->count(),
            'my_prayer_requests' => $member->prayerRequests()->count(),
            'upcoming_meetings' => $groupIds->isNotEmpty() 
                ? Meeting::whereIn('group_id', $groupIds)
                    ->where('scheduled_at', '>', now())
                    ->where('status', 'scheduled')
                    ->count()
                : 0,
            'pending_assignments' => $groupIds->isNotEmpty() 
                ? Assignment::whereIn('group_id', $groupIds)
                    ->where('is_active', true)
                    ->where('due_date', '>', now())
                    ->whereDoesntHave('submissions', function ($query) use ($member) {
                        $query->where('user_id', $member->id);
                    })
                    ->count()
                : 0,
        ];
        
        // Get recent prayer requests from member's groups (with safety check)
        $recentPrayers = $groupIds->isNotEmpty() 
            ? PrayerRequest::whereHas('user.groups', function ($query) use ($groupIds) {
                    $query->whereIn('groups.id', $groupIds)
                          ->where('group_user.status', 'approved');
                })
                ->with(['user'])
                ->where('privacy', '!=', 'private')
                ->where('user_id', '!=', $member->id) // Exclude own prayers
                ->latest()
                ->limit(5)
                ->get()
            : collect();
            
        // Get upcoming meetings (with safety check)
        $upcomingMeetings = $groupIds->isNotEmpty() 
            ? Meeting::whereIn('group_id', $groupIds)
                ->with(['group'])
                ->where('scheduled_at', '>', now())
                ->where('status', 'scheduled')
                ->orderBy('scheduled_at')
                ->limit(3)
                ->get()
            : collect();
            
        // Get pending assignments (with safety check)
        $pendingAssignments = $groupIds->isNotEmpty() 
            ? Assignment::whereIn('group_id', $groupIds)
                ->with(['group'])
                ->where('is_active', true)
                ->where('due_date', '>', now())
                ->whereDoesntHave('submissions', function ($query) use ($member) {
                    $query->where('user_id', $member->id);
                })
                ->orderBy('due_date')
                ->limit(3)
                ->get()
            : collect();
            
        // Get recent activity
        $recentActivity = $this->getRecentActivity($member, $memberGroups);

        return Inertia::render('member/Dashboard', [
            'stats' => $stats,
            'memberGroups' => $memberGroups,
            'recentPrayers' => $recentPrayers,
            'upcomingMeetings' => $upcomingMeetings,
            'pendingAssignments' => $pendingAssignments,
            'recentActivity' => $recentActivity,
        ]);
    }
    
    /**
     * Get recent activity for the member.
     */
    private function getRecentActivity($member, $groups)
    {
        $activities = [];
        $groupIds = $groups->pluck('id');
        
        // Recent group joins
        foreach ($groups->take(3) as $group) {
            $activities[] = [
                'id' => 'group_join_' . $group->id,
                'type' => 'group_join',
                'description' => "Joined '{$group->name}' group",
                'timestamp' => \Carbon\Carbon::parse($group->pivot->joined_at)->diffForHumans(),
                'action_url' => route('member.groups.show', $group),
            ];
        }
        
        // Recent prayer requests
        $memberPrayers = $member->prayerRequests()
            ->latest()
            ->limit(3)
            ->get();
            
        foreach ($memberPrayers as $prayer) {
            $activities[] = [
                'id' => 'prayer_' . $prayer->id,
                'type' => 'prayer_request',
                'description' => "Created prayer request: {$prayer->title}",
                'timestamp' => $prayer->created_at->diffForHumans(),
                'action_url' => route('member.prayers.show', $prayer),
            ];
        }
        
        // Recent assignment submissions
        $recentSubmissions = $member->assignmentSubmissions()
            ->with(['assignment'])
            ->latest()
            ->limit(3)
            ->get();
            
        foreach ($recentSubmissions as $submission) {
            $activities[] = [
                'id' => 'submission_' . $submission->id,
                'type' => 'assignment_submission',
                'description' => "Submitted assignment: {$submission->assignment->title}",
                'timestamp' => $submission->submitted_at->diffForHumans(),
                'action_url' => route('member.assignments.show', $submission->assignment),
            ];
        }
        
        // Sort by timestamp and return latest 10 activities
        usort($activities, function ($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });
        
        return array_slice($activities, 0, 10);
    }
}
