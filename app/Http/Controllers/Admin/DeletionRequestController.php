<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeletionRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DeletionRequestController extends Controller
{
    /**
     * Display all deletion requests
     */
    public function index(Request $request): Response
    {
        $status = $request->get('status', 'pending');

        $requests = DeletionRequest::with(['user', 'reviewer'])
            ->when($status, function ($query, $status) {
                if ($status !== 'all') {
                    $query->where('status', $status);
                }
            })
            ->latest()
            ->paginate(15);

        $stats = [
            'pending' => DeletionRequest::pending()->count(),
            'approved' => DeletionRequest::approved()->count(),
            'rejected' => DeletionRequest::rejected()->count(),
            'total' => DeletionRequest::count(),
        ];

        return Inertia::render('admin/DeletionRequests/Index', [
            'requests' => $requests,
            'stats' => $stats,
            'currentStatus' => $status,
        ]);
    }

    /**
     * Approve a deletion request
     */
    public function approve(Request $request, DeletionRequest $deletionRequest)
    {
        if ($deletionRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been reviewed.');
        }

        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $deletionRequest->update([
            'status' => 'approved',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'admin_notes' => $validated['admin_notes'] ?? null,
        ]);

        return back()->with('success', "Deletion request approved. {$deletionRequest->user->name} can now delete their account.");
    }

    /**
     * Reject a deletion request
     */
    public function reject(Request $request, DeletionRequest $deletionRequest)
    {
        if ($deletionRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been reviewed.');
        }

        $validated = $request->validate([
            'admin_notes' => 'required|string|min:10|max:500',
        ]);

        $deletionRequest->update([
            'status' => 'rejected',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'admin_notes' => $validated['admin_notes'],
        ]);

        return back()->with('success', "Deletion request rejected. {$deletionRequest->user->name} has been notified.");
    }
}
