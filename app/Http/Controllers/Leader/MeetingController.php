<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\Group;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MeetingController extends Controller
{
    /**
     * Display a listing of meetings for the leader's groups.
     */
    public function index(): Response
    {
        $leader = auth()->user();
        
        // Get meetings for groups led by this leader
        $meetings = Meeting::whereHas('group', function ($query) use ($leader) {
                $query->where('leader_id', $leader->id);
            })
            ->with(['group'])
            ->withCount(['attendees'])
            ->latest('scheduled_at')
            ->paginate(15);

        return Inertia::render('leader/Meetings/Index', [
            'meetings' => $meetings,
        ]);
    }

    /**
     * Show the form for creating a new meeting.
     */
    public function create(): Response
    {
        $leader = auth()->user();
        
        // Get groups led by this leader
        $groups = $leader->ledGroups()
            ->where('is_active', true)
            ->get(['id', 'name']);

        return Inertia::render('leader/Meetings/Create', [
            'groups' => $groups,
        ]);
    }

    /**
     * Store a newly created meeting.
     */
    public function store(Request $request)
    {
        $leader = auth()->user();
        
        $validated = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:bible_study,prayer,fellowship,service,other',
            'meeting_type' => 'required|in:physical,online',
            'scheduled_at' => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'location' => 'required_if:meeting_type,physical|nullable|string|max:255',
            'meeting_url' => 'required_if:meeting_type,online|nullable|url',
            'max_attendees' => 'nullable|integer|min:1|max:200',
            'is_recurring' => 'required|boolean',
            'recurrence_pattern' => 'nullable|in:weekly,biweekly,monthly',
        ]);

        // Verify the leader owns this group
        $group = Group::findOrFail($validated['group_id']);
        if ($group->leader_id !== $leader->id) {
            return back()->withErrors(['group_id' => 'You can only create meetings for your own groups.']);
        }

        Meeting::create([
            ...$validated,
            'created_by' => $leader->id,
        ]);

        return redirect()->route('leader.meetings.index')
            ->with('success', 'Meeting created successfully.');
    }

    /**
     * Display the specified meeting.
     */
    public function show(Meeting $meeting): Response
    {
        // Ensure the leader owns this meeting's group
        if ($meeting->group->leader_id !== auth()->id()) {
            abort(403, 'You can only view meetings for your own groups.');
        }

        $meeting->load([
            'group',
            'attendees' => function ($query) {
                $query->with(['user'])
                      ->latest();
            }
        ]);

        return Inertia::render('leader/Meetings/Show', [
            'meeting' => $meeting,
        ]);
    }

    /**
     * Show the form for editing the specified meeting.
     */
    public function edit(Meeting $meeting): Response
    {
        // Ensure the leader owns this meeting's group
        if ($meeting->group->leader_id !== auth()->id()) {
            abort(403, 'You can only edit meetings for your own groups.');
        }

        $leader = auth()->user();
        
        // Get groups led by this leader
        $groups = $leader->ledGroups()
            ->where('is_active', true)
            ->get(['id', 'name']);

        return Inertia::render('leader/Meetings/Edit', [
            'meeting' => $meeting,
            'groups' => $groups,
        ]);
    }

    /**
     * Update the specified meeting.
     */
    public function update(Request $request, Meeting $meeting)
    {
        // Ensure the leader owns this meeting's group
        if ($meeting->group->leader_id !== auth()->id()) {
            abort(403, 'You can only update meetings for your own groups.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:bible_study,prayer,fellowship,service,other',
            'scheduled_at' => 'required|date',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'location' => 'nullable|string|max:255',
            'meeting_url' => 'nullable|url',
            'max_attendees' => 'nullable|integer|min:1|max:200',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
        ]);

        $meeting->update($validated);

        return redirect()->route('leader.meetings.show', $meeting)
            ->with('success', 'Meeting updated successfully.');
    }

    /**
     * Remove the specified meeting.
     */
    public function destroy(Meeting $meeting)
    {
        // Ensure the leader owns this meeting's group
        if ($meeting->group->leader_id !== auth()->id()) {
            abort(403, 'You can only delete meetings for your own groups.');
        }

        // Only allow deletion if meeting hasn't started
        if ($meeting->status === 'in_progress' || $meeting->status === 'completed') {
            return back()->with('error', 'Cannot delete a meeting that has started or completed.');
        }

        $meeting->delete();

        return redirect()->route('leader.meetings.index')
            ->with('success', 'Meeting deleted successfully.');
    }

    /**
     * Record attendance for a meeting.
     */
    public function recordAttendance(Request $request, Meeting $meeting)
    {
        // Ensure the leader owns this meeting's group
        if ($meeting->group->leader_id !== auth()->id()) {
            abort(403, 'You can only record attendance for your own meetings.');
        }

        $validated = $request->validate([
            'attendees' => 'required|array',
            'attendees.*.user_id' => 'required|exists:users,id',
            'attendees.*.status' => 'required|in:present,absent,late',
            'attendees.*.notes' => 'nullable|string|max:255',
        ]);

        // Clear existing attendance records
        $meeting->attendees()->delete();

        // Create new attendance records
        foreach ($validated['attendees'] as $attendee) {
            $meeting->attendees()->create([
                'user_id' => $attendee['user_id'],
                'status' => $attendee['status'],
                'notes' => $attendee['notes'] ?? null,
                'recorded_by' => auth()->id(),
            ]);
        }

        return back()->with('success', 'Attendance recorded successfully.');
    }
}
