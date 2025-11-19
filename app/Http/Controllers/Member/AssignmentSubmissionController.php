<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class AssignmentSubmissionController extends Controller
{
    /**
     * Show the submission form or existing submission
     */
    public function show(Assignment $assignment): Response
    {
        $member = auth()->user();
        
        // Verify access
        if (!$assignment->group->members()->where('user_id', $member->id)
            ->where('group_user.status', 'approved')
            ->exists()) {
            abort(403, 'You do not have access to this assignment.');
        }

        $submission = $assignment->submissions()
            ->where('user_id', $member->id)
            ->first();

        return Inertia::render('member/Assignments/Submission', [
            'assignment' => $assignment->load(['group', 'creator']),
            'submission' => $submission
        ]);
    }

    /**
     * Store a new assignment submission
     */
    public function store(Request $request, Assignment $assignment)
    {
        $member = auth()->user();
        
        // Verify access
        if (!$assignment->group->members()->where('user_id', $member->id)
            ->where('group_user.status', 'approved')
            ->exists()) {
            abort(403, 'You do not have access to this assignment.');
        }

        // Prevent duplicate submissions
        if ($assignment->submissions()->where('user_id', $member->id)->exists()) {
            return back()->with('error', 'You have already submitted this assignment.');
        }

        // Prevent late submissions
        if ($assignment->due_date < now()) {
            return back()->with('error', 'The due date for this assignment has passed.');
        }

        $validated = $request->validate([
            'content' => 'required|string|min:10',
            'file' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240' // 10MB max
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store("submissions/assignments/{$assignment->id}", 'public');
        }

        $submission = new AssignmentSubmission([
            'content' => $validated['content'],
            'file_path' => $filePath,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $submission->assignment()->associate($assignment);
        $submission->user()->associate($member);
        $submission->save();

        return redirect()
            ->route('member.assignments.show', $assignment)
            ->with('success', 'Your assignment has been submitted successfully!');
    }
}
