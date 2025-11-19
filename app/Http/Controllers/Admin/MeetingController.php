<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MeetingController extends Controller
{
    /**
     * Display a listing of meetings.
     */
    public function index(): Response
    {
        $meetings = Meeting::with(['group', 'creator'])
            ->latest('scheduled_at')
            ->paginate(15);

        $stats = [
            'total_meetings' => Meeting::count(),
            'upcoming_meetings' => Meeting::where('scheduled_at', '>', now())->count(),
            'completed_meetings' => Meeting::where('status', 'completed')->count(),
            'cancelled_meetings' => Meeting::where('status', 'cancelled')->count(),
        ];

        return Inertia::render('admin/Meetings/Index', [
            'meetings' => $meetings,
            'stats' => $stats,
        ]);
    }

    /**
     * Display the specified meeting.
     */
    public function show(Meeting $meeting): Response
    {
        $meeting->load(['group', 'creator', 'attendances.user']);

        // Get attendance statistics
        $attendanceStats = [
            'total' => $meeting->attendances->count(),
            'confirmed' => $meeting->attendances->where('rsvp_status', 'yes')->count(),
            'declined' => $meeting->attendances->where('rsvp_status', 'no')->count(),
            'maybe' => $meeting->attendances->where('rsvp_status', 'maybe')->count(),
        ];

        return Inertia::render('admin/Meetings/Show', [
            'meeting' => $meeting,
            'attendanceStats' => $attendanceStats,
        ]);
    }
}
