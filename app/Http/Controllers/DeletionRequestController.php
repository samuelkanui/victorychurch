<?php

namespace App\Http\Controllers;

use App\Models\DeletionRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DeletionRequestController extends Controller
{
    /**
     * Store a new deletion request
     */
    public function store(Request $request)
    {
        $user = $request->user();

        // Check if user already has a pending request
        if ($user->hasPendingDeletionRequest()) {
            return back()->with('error', 'You already have a pending deletion request.');
        }

        // Check if user has an approved request
        if ($user->hasApprovedDeletionRequest()) {
            return back()->with('error', 'Your deletion request has already been approved. You can now delete your account.');
        }

        // Admins can delete their accounts directly
        if ($user->isAdmin()) {
            return back()->with('error', 'Admins can delete their accounts directly without approval.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|min:10|max:1000',
        ]);

        DeletionRequest::create([
            'user_id' => $user->id,
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        return back()->with('success', 'Your account deletion request has been submitted and is pending admin approval.');
    }

    /**
     * Cancel a pending deletion request
     */
    public function cancel(Request $request)
    {
        $user = $request->user();
        
        $deletionRequest = $user->deletionRequest()->where('status', 'pending')->first();

        if (!$deletionRequest) {
            return back()->with('error', 'No pending deletion request found.');
        }

        $deletionRequest->delete();

        return back()->with('success', 'Your deletion request has been cancelled.');
    }
}
