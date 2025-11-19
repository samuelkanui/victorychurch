<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the member's profile.
     */
    public function show(): Response
    {
        $member = auth()->user();
        
        // Get member's groups with statistics
        $groups = $member->groups()
            ->withPivot(['status', 'role', 'joined_at'])
            ->where('group_user.status', 'approved')
            ->with(['leader'])
            ->get();
        
        // Get activity statistics
        $stats = [
            'groups_count' => $groups->count(),
            'prayer_requests_count' => $member->prayerRequests()->count(),
            'assignments_completed' => $member->assignmentSubmissions()->count(),
            'meetings_attended' => $member->meetingAttendances()
                ->where('status', 'present')->count(),
        ];
        
        // Get recent activity - create activity feed
        $recentActivity = [];
        
        // Add recent prayer requests
        $recentPrayers = $member->prayerRequests()
            ->latest()
            ->limit(3)
            ->get();
            
        foreach ($recentPrayers as $prayer) {
            $recentActivity[] = [
                'id' => 'prayer_' . $prayer->id,
                'type' => 'prayer_request',
                'description' => "Submitted prayer request: {$prayer->title}",
                'timestamp' => $prayer->created_at->diffForHumans(),
            ];
        }
        
        // Add recent assignment submissions
        $recentSubmissions = $member->assignmentSubmissions()
            ->with(['assignment'])
            ->latest()
            ->limit(3)
            ->get();
            
        foreach ($recentSubmissions as $submission) {
            $recentActivity[] = [
                'id' => 'submission_' . $submission->id,
                'type' => 'assignment_submission',
                'description' => "Submitted assignment: {$submission->assignment->title}",
                'timestamp' => $submission->created_at->diffForHumans(),
            ];
        }
        
        // Add recent group joins
        foreach ($groups->take(2) as $group) {
            $recentActivity[] = [
                'id' => 'group_' . $group->id,
                'type' => 'group_join',
                'description' => "Joined group: {$group->name}",
                'timestamp' => $group->pivot->joined_at ? \Carbon\Carbon::parse($group->pivot->joined_at)->diffForHumans() : 'Recently',
            ];
        }
        
        // Sort by most recent and limit
        usort($recentActivity, function($a, $b) {
            return strcmp($b['timestamp'], $a['timestamp']);
        });
        $recentActivity = array_slice($recentActivity, 0, 5);

        return Inertia::render('member/Profile/Index', [
            'user' => $member,
            'stats' => $stats,
            'recentActivity' => $recentActivity,
        ]);
    }
    
    /**
     * Show the form for editing the member's profile.
     */
    public function edit(): Response
    {
        $member = auth()->user();
        
        return Inertia::render('member/Profile/Edit', [
            'member' => $member,
        ]);
    }
    
    /**
     * Update the member's profile.
     */
    public function update(Request $request)
    {
        $member = auth()->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $member->id,
            'current_password' => 'nullable|required_with:password',
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);
        
        // Verify current password if changing password
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $member->password)) {
                return back()->withErrors([
                    'current_password' => 'The current password is incorrect.'
                ]);
            }
        }
        
        // Update profile
        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];
        
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($validated['password']);
        }
        
        $member->update($updateData);
        
        return redirect()->route('member.profile.show')
            ->with('success', 'Profile updated successfully.');
    }
}
