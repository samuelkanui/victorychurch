<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\PrayerRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PrayerRequestController extends Controller
{
    /**
     * Display member's prayer requests and community prayers.
     */
    public function index(Request $request): Response
    {
        $member = auth()->user();
        
        // Get member's groups
        $groupIds = $member->groups()
            ->where('group_user.status', 'approved')
            ->pluck('groups.id');
        
        $query = PrayerRequest::with(['user']);
        
        // Filter by tab
        $tab = $request->get('tab', 'community');
        
        if ($tab === 'my_prayers') {
            $query->where('user_id', $member->id);
        } else {
            // Community prayers - from member's groups or public
            $query->where(function ($q) use ($groupIds, $member) {
                $q->where('privacy', 'public')
                  ->orWhere(function ($subQ) use ($groupIds) {
                      $subQ->where('privacy', 'group')
                           ->whereHas('user.groups', function ($groupQ) use ($groupIds) {
                               $groupQ->whereIn('groups.id', $groupIds)
                                      ->where('group_user.status', 'approved');
                           });
                  });
            })->where('user_id', '!=', $member->id); // Exclude own prayers
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        
        // Filter by privacy (for my prayers)
        if ($tab === 'my_prayers' && $request->filled('privacy')) {
            $query->where('privacy', $request->get('privacy'));
        }
        
        $prayers = $query->latest()->paginate(10);
        
        // Get statistics
        $stats = [
            'total_prayers' => $member->prayerRequests()->count(),
            'active_prayers' => $member->prayerRequests()->where('status', 'active')->count(),
            'answered_prayers' => $member->prayerRequests()->where('status', 'answered')->count(),
            'urgent_prayers' => $member->prayerRequests()->where('is_urgent', true)->count(),
            'community_prayers' => PrayerRequest::where('privacy', 'public')
                ->orWhere(function ($q) use ($groupIds) {
                    $q->where('privacy', 'group')
                      ->whereHas('user.groups', function ($groupQ) use ($groupIds) {
                          $groupQ->whereIn('groups.id', $groupIds)
                                 ->where('group_user.status', 'approved');
                      });
                })->count(),
        ];

        return Inertia::render('member/Prayers/Index', [
            'prayers' => $prayers,
            'stats' => $stats,
            'currentTab' => $tab,
            'filters' => $request->only(['status', 'privacy']),
        ]);
    }
    
    /**
     * Show the form for creating a new prayer request.
     */
    public function create(): Response
    {
        return Inertia::render('member/Prayers/Create');
    }
    
    /**
     * Store a newly created prayer request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'privacy' => 'required|in:public,group,private',
            'is_anonymous' => 'boolean',
        ]);
        
        $member = auth()->user();
        
        $prayer = $member->prayerRequests()->create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'privacy' => $validated['privacy'],
            'is_anonymous' => $validated['is_anonymous'] ?? false,
            'status' => 'active',
        ]);
        
        return redirect()->route('member.prayers.show', $prayer)
            ->with('success', 'Prayer request created successfully.');
    }
    
    /**
     * Display the specified prayer request.
     */
    public function show(PrayerRequest $prayerRequest): Response
    {
        $member = auth()->user();
        
        // Check if member can view this prayer
        if (!$this->canViewPrayer($prayerRequest, $member)) {
            abort(403, 'You do not have permission to view this prayer request.');
        }
        
        $prayerRequest->load(['user', 'responder', 'moderator']);
        
        // Check if member has prayed for this request
        $hasPrayed = $prayerRequest->prayers()->where('user_id', $member->id)->exists();
        
        // Add prayers count
        $prayerRequest->prayers_count = $prayerRequest->prayers()->count();
        $prayerRequest->has_prayed = $hasPrayed;
        
        return Inertia::render('member/Prayers/Show', [
            'prayerRequest' => $prayerRequest,
            'canEdit' => $prayerRequest->user_id === $member->id,
        ]);
    }
    
    /**
     * Show the form for editing the prayer request.
     */
    public function edit(PrayerRequest $prayerRequest): Response
    {
        $member = auth()->user();
        
        // Only allow editing own prayers
        if ($prayerRequest->user_id !== $member->id) {
            abort(403, 'You can only edit your own prayer requests.');
        }
        
        return Inertia::render('member/Prayers/Edit', [
            'prayerRequest' => $prayerRequest,
        ]);
    }
    
    /**
     * Update the specified prayer request.
     */
    public function update(Request $request, PrayerRequest $prayerRequest)
    {
        $member = auth()->user();
        
        // Only allow updating own prayers
        if ($prayerRequest->user_id !== $member->id) {
            abort(403, 'You can only edit your own prayer requests.');
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'privacy' => 'required|in:public,group,private',
            'is_anonymous' => 'boolean',
            'status' => 'required|in:active,answered,closed',
        ]);
        
        $prayerRequest->update($validated);
        
        return redirect()->route('member.prayers.show', $prayerRequest)
            ->with('success', 'Prayer request updated successfully.');
    }
    
    /**
     * Remove the specified prayer request.
     */
    public function destroy(PrayerRequest $prayerRequest)
    {
        $member = auth()->user();
        
        // Only allow deleting own prayers
        if ($prayerRequest->user_id !== $member->id) {
            abort(403, 'You can only delete your own prayer requests.');
        }
        
        $prayerRequest->delete();
        
        return redirect()->route('member.prayers.index')
            ->with('success', 'Prayer request deleted successfully.');
    }
    
    /**
     * Check if member can view a prayer request.
     */
    private function canViewPrayer(PrayerRequest $prayer, $member): bool
    {
        // Can always view own prayers
        if ($prayer->user_id === $member->id) {
            return true;
        }
        
        // Can view public prayers
        if ($prayer->privacy === 'public') {
            return true;
        }
        
        // Can view group prayers if in same group
        if ($prayer->privacy === 'group') {
            $memberGroupIds = $member->groups()
                ->where('group_user.status', 'approved')
                ->pluck('groups.id');
                
            $prayerUserGroupIds = $prayer->user->groups()
                ->where('group_user.status', 'approved')
                ->pluck('groups.id');
                
            return $memberGroupIds->intersect($prayerUserGroupIds)->isNotEmpty();
        }
        
        // Cannot view private prayers
        return false;
    }
    
    /**
     * Record that a member prayed for a request.
     */
    public function pray(PrayerRequest $prayerRequest)
    {
        $member = auth()->user();
        
        // Check if member can view this prayer
        if (!$this->canViewPrayer($prayerRequest, $member)) {
            abort(403, 'You do not have permission to view this prayer request.');
        }
        
        // Check if already prayed
        if ($prayerRequest->prayers()->where('user_id', $member->id)->exists()) {
            return back()->with('info', 'You have already prayed for this request.');
        }
        
        // Record the prayer
        $prayerRequest->prayers()->attach($member->id, [
            'prayed_at' => now()
        ]);
        
        return back()->with('success', 'Thank you for praying for this request!');
    }
}
