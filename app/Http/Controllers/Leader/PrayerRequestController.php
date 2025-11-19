<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\PrayerRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PrayerRequestController extends Controller
{
    /**
     * Display prayer requests from the leader's group members.
     */
    public function index(Request $request): Response
    {
        $leader = auth()->user();
        
        // Get IDs of groups led by this leader
        $groupIds = $leader->ledGroups()->pluck('id');

        $query = PrayerRequest::whereHas('user.groups', function ($q) use ($groupIds) {
                $q->whereIn('groups.id', $groupIds)
                  ->where('group_user.status', 'approved');
            })
            ->with(['user', 'user.groups' => function ($q) use ($groupIds) {
                $q->whereIn('groups.id', $groupIds);
            }])
            ->where('privacy', '!=', 'private'); // Leaders can see public and group prayers

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Filter by privacy
        if ($request->filled('privacy')) {
            $query->where('privacy', $request->get('privacy'));
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $prayers = $query->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('leader/Prayers/Index', [
            'prayerRequests' => $prayers,
            'filters' => $request->only(['status', 'privacy', 'search']),
        ]);
    }

    /**
     * Display the specified prayer request.
     */
    public function show(PrayerRequest $prayerRequest): Response
    {
        $leader = auth()->user();
        
        // Ensure the prayer request is from one of the leader's group members
        $groupIds = $leader->ledGroups()->pluck('id');
        $hasAccess = $prayerRequest->user->groups()
            ->whereIn('groups.id', $groupIds)
            ->wherePivot('status', 'approved')
            ->exists();

        if (!$hasAccess || $prayerRequest->privacy === 'private') {
            abort(403, 'You do not have access to this prayer request.');
        }

        $prayerRequest->load(['user']);

        return Inertia::render('leader/Prayers/Show', [
            'prayer' => $prayerRequest,
        ]);
    }

    /**
     * Respond to a prayer request.
     */
    public function respond(Request $request, PrayerRequest $prayerRequest)
    {
        $leader = auth()->user();
        
        // Ensure the prayer request is from one of the leader's group members
        $groupIds = $leader->ledGroups()->pluck('id');
        $hasAccess = $prayerRequest->user->groups()
            ->whereIn('groups.id', $groupIds)
            ->wherePivot('status', 'approved')
            ->exists();

        if (!$hasAccess || $prayerRequest->privacy === 'private') {
            abort(403, 'You do not have access to this prayer request.');
        }

        $validated = $request->validate([
            'response' => 'required|string|max:1000',
        ]);

        // For now, we'll store the response in the prayer request
        // In a full implementation, you might have a separate responses table
        $prayerRequest->update([
            'leader_response' => $validated['response'],
            'responded_at' => now(),
            'responded_by' => $leader->id,
        ]);

        return back()->with('success', 'Response added successfully.');
    }

    /**
     * Moderate a prayer request (mark as inappropriate, etc.).
     */
    public function moderate(Request $request, PrayerRequest $prayerRequest)
    {
        $leader = auth()->user();
        
        // Ensure the prayer request is from one of the leader's group members
        $groupIds = $leader->ledGroups()->pluck('id');
        $hasAccess = $prayerRequest->user->groups()
            ->whereIn('groups.id', $groupIds)
            ->wherePivot('status', 'approved')
            ->exists();

        if (!$hasAccess) {
            abort(403, 'You do not have access to this prayer request.');
        }

        $validated = $request->validate([
            'status' => 'required|in:active,flagged,resolved',
            'moderation_note' => 'nullable|string|max:500',
        ]);

        $prayerRequest->update([
            'status' => $validated['status'],
            'moderation_note' => $validated['moderation_note'] ?? null,
            'moderated_at' => now(),
            'moderated_by' => $leader->id,
        ]);

        return back()->with('success', 'Prayer request moderated successfully.');
    }
}
