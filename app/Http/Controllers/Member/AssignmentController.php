<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AssignmentController extends Controller
{
    /**
     * Display member's assignments.
     */
    public function index(Request $request): Response
    {
        $member = auth()->user();
        
        // Get member's groups
        $groupIds = $member->groups()
            ->where('group_user.status', 'approved')
            ->pluck('groups.id');
        
        // If member has no approved groups, show empty assignments
        if ($groupIds->isEmpty()) {
            $emptyPagination = new \Illuminate\Pagination\LengthAwarePaginator(
                [], 0, 10, 1, ['path' => request()->url()]
            );
            
            return Inertia::render('member/Assignments/Index', [
                'assignments' => $emptyPagination,
                'currentStatus' => $request->get('status', 'all'),
                'filters' => $request->only(['type']),
            ]);
        }
        
        $query = Assignment::whereIn('group_id', $groupIds)
            ->with(['group', 'creator'])
            ->where('is_active', true);
        
        // Filter by status
        $status = $request->get('status', 'all');
        
        if ($status === 'pending') {
            $query->whereDoesntHave('submissions', function ($q) use ($member) {
                $q->where('user_id', $member->id);
            })->where('due_date', '>', now());
        } elseif ($status === 'submitted') {
            $query->whereHas('submissions', function ($q) use ($member) {
                $q->where('user_id', $member->id);
            });
        } elseif ($status === 'overdue') {
            $query->whereDoesntHave('submissions', function ($q) use ($member) {
                $q->where('user_id', $member->id);
            })->where('due_date', '<', now());
        }
        
        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->get('type'));
        }
        
        $assignments = $query->latest()->paginate(10);
        
        // Add submission status to each assignment
        $assignments->getCollection()->transform(function ($assignment) use ($member) {
            $submission = $assignment->submissions()->where('user_id', $member->id)->first();
            $assignment->submission_status = $submission ? 'submitted' : 
                ($assignment->due_date < now() ? 'overdue' : 'pending');
            $assignment->submission = $submission;
            return $assignment;
        });

        return Inertia::render('member/Assignments/Index', [
            'assignments' => $assignments,
            'currentStatus' => $status,
            'filters' => $request->only(['type']),
        ]);
    }
    
    /**
     * Display the specified assignment.
     */
    public function show(Assignment $assignment): Response
    {
        $member = auth()->user();
        
        // Check if member is in the assignment's group
        if (!$member->groups()->where('groups.id', $assignment->group_id)
                   ->where('group_user.status', 'approved')->exists()) {
            abort(403, 'You do not have access to this assignment.');
        }
        
        $assignment->load(['group', 'creator']);
        
        // Get member's submission if exists
        $submission = $assignment->submissions()
            ->where('user_id', $member->id)
            ->first();
        
        // Determine submission status
        $submissionStatus = $submission ? 'submitted' : 
            ($assignment->due_date < now() ? 'overdue' : 'pending');
        
        return Inertia::render('member/Assignments/Show', [
            'assignment' => $assignment,
            'submission' => $submission,
            'submissionStatus' => $submissionStatus,
            'canSubmit' => !$submission && $assignment->due_date > now(),
        ]);
    }
    
    /**
     * Submit an assignment.
     */
    public function submit(Request $request, Assignment $assignment)
    {
        $member = auth()->user();
        
        // Check if member is in the assignment's group
        if (!$member->groups()->where('groups.id', $assignment->group_id)
                   ->where('group_user.status', 'approved')->exists()) {
            abort(403, 'You do not have access to this assignment.');
        }
        
        // Check if assignment is still active and not overdue
        if (!$assignment->is_active) {
            return back()->with('error', 'This assignment is no longer active.');
        }
        
        // Check if already submitted
        if ($assignment->submissions()->where('user_id', $member->id)->exists()) {
            return back()->with('error', 'You have already submitted this assignment.');
        }
        
        $validated = $request->validate([
            'content' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240', // 10MB max
        ]);
        
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('assignments', 'public');
        }
        
        $submission = AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'user_id' => $member->id,
            'content' => $validated['content'],
            'file_path' => $filePath,
            'submitted_at' => now(),
        ]);
        
        return redirect()->route('member.assignments.show', $assignment)
            ->with('success', 'Assignment submitted successfully.');
    }
    
    /**
     * Display member's submission for an assignment.
     */
    public function submission(Assignment $assignment): Response
    {
        $member = auth()->user();
        
        // Check if member is in the assignment's group
        if (!$member->groups()->where('groups.id', $assignment->group_id)
                   ->where('group_user.status', 'approved')->exists()) {
            abort(403, 'You do not have access to this assignment.');
        }
        
        $submission = $assignment->submissions()
            ->where('user_id', $member->id)
            ->with(['grader'])
            ->first();
        
        if (!$submission) {
            abort(404, 'No submission found for this assignment.');
        }
        
        $assignment->load(['group', 'creator']);
        
        return Inertia::render('member/Assignments/Submission', [
            'assignment' => $assignment,
            'submission' => $submission,
        ]);
    }
}
