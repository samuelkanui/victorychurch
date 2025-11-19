<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\MeetingAttendance;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MeetingController extends Controller
{
    /**
     * Display member's meetings.
     */
    public function index(Request $request): Response
    {
        $member = auth()->user();
        
        // Get member's groups
        $groupIds = $member->groups()
            ->where('group_user.status', 'approved')
            ->pluck('groups.id');
        
        $query = Meeting::whereIn('group_id', $groupIds)
            ->with(['group', 'creator']);
        
        // Filter by status
        $status = $request->get('status', 'upcoming');
        
        if ($status === 'upcoming') {
            $query->where('scheduled_at', '>', now())
                  ->where('status', 'scheduled');
        } elseif ($status === 'past') {
            $query->where('scheduled_at', '<', now())
                  ->whereIn('status', ['completed', 'cancelled']);
        } elseif ($status === 'attended') {
            $query->whereHas('attendances', function ($q) use ($member) {
                $q->where('user_id', $member->id)
                  ->where('status', 'present');
            });
        }
        
        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->get('type'));
        }
        
        $meetings = $query->orderBy('scheduled_at', 'desc')->paginate(10);
        
        // Add RSVP status to each meeting
        $meetings->getCollection()->transform(function ($meeting) use ($member) {
            $attendance = $meeting->attendances()->where('user_id', $member->id)->first();
            $meeting->rsvp_status = $attendance ? $attendance->rsvp_status : null;
            $meeting->attendance_status = $attendance ? $attendance->status : null;
            return $meeting;
        });
        
        // Get statistics
        $stats = [
            'upcoming_meetings' => Meeting::whereIn('group_id', $groupIds)
                ->where('scheduled_at', '>', now())
                ->where('status', 'scheduled')
                ->count(),
            'meetings_attended' => MeetingAttendance::where('user_id', $member->id)
                ->where('status', 'present')
                ->count(),
            'meetings_rsvp_yes' => MeetingAttendance::where('user_id', $member->id)
                ->where('rsvp_status', 'yes')
                ->count(),
            'total_meetings' => Meeting::whereIn('group_id', $groupIds)->count(),
        ];

        return Inertia::render('member/Meetings/Index', [
            'meetings' => $meetings,
            'stats' => $stats,
            'currentStatus' => $status,
            'filters' => $request->only(['type']),
        ]);
    }
    
    /**
     * Display the specified meeting.
     */
    public function show(Meeting $meeting): Response
    {
        $member = auth()->user();
        
        // Check if member is in the meeting's group
        if (!$member->groups()->where('groups.id', $meeting->group_id)
                   ->where('group_user.status', 'approved')->exists()) {
            abort(403, 'You do not have access to this meeting.');
        }
        
        $meeting->load(['group', 'creator']);
        
        // Get member's attendance record
        $attendance = $meeting->attendances()
            ->where('user_id', $member->id)
            ->first();
        
        // Get other attendees (if meeting is past or member has RSVP'd)
        $attendees = collect();
        if ($meeting->scheduled_at < now() || ($attendance && $attendance->rsvp_status === 'yes')) {
            $attendees = $meeting->attendances()
                ->with(['user'])
                ->where('rsvp_status', 'yes')
                ->get();
        }
        
        return Inertia::render('member/Meetings/Show', [
            'meeting' => $meeting,
            'attendance' => $attendance,
            'attendees' => $attendees,
            'canRsvp' => $meeting->scheduled_at > now() && $meeting->status === 'scheduled',
        ]);
    }
    
    /**
     * RSVP to a meeting.
     */
    public function rsvp(Request $request, Meeting $meeting)
    {
        $member = auth()->user();
        
        // Check if member is in the meeting's group
        if (!$member->groups()->where('groups.id', $meeting->group_id)
                   ->where('group_user.status', 'approved')->exists()) {
            abort(403, 'You do not have access to this meeting.');
        }
        
        // Check if meeting is still upcoming
        if ($meeting->scheduled_at <= now() || $meeting->status !== 'scheduled') {
            return back()->with('error', 'You can no longer RSVP to this meeting.');
        }
        
        $validated = $request->validate([
            'rsvp_status' => 'required|in:yes,no,maybe',
            'notes' => 'nullable|string|max:500',
        ]);
        
        // Update or create attendance record
        MeetingAttendance::updateOrCreate(
            [
                'meeting_id' => $meeting->id,
                'user_id' => $member->id,
            ],
            [
                'rsvp_status' => $validated['rsvp_status'],
                'notes' => $validated['notes'] ?? null,
                'rsvp_at' => now(),
                'recorded_by' => $member->id, // Member records their own RSVP
            ]
        );
        
        $statusText = [
            'yes' => 'confirmed your attendance',
            'no' => 'declined',
            'maybe' => 'marked as maybe attending'
        ];
        
        return back()->with('success', "You have {$statusText[$validated['rsvp_status']]} for this meeting.");
    }
}
