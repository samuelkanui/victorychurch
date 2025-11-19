<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Group;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AssignmentController extends Controller
{
    /**
     * Display a listing of assignments for the leader's groups.
     */
    public function index(): Response
    {
        $leader = auth()->user();
        
        // Get assignments for groups led by this leader
        $assignments = Assignment::whereHas('group', function ($query) use ($leader) {
                $query->where('leader_id', $leader->id);
            })
            ->with(['group'])
            ->withCount(['submissions'])
            ->latest()
            ->paginate(15);

        return Inertia::render('leader/Assignments/Index', [
            'assignments' => $assignments,
        ]);
    }

    /**
     * Show the form for creating a new assignment.
     */
    public function create(): Response
    {
        $leader = auth()->user();
        
        // Get groups led by this leader
        $groups = $leader->ledGroups()
            ->where('is_active', true)
            ->get(['id', 'name']);

        return Inertia::render('leader/Assignments/Create', [
            'groups' => $groups,
        ]);
    }

    /**
     * Store a newly created assignment.
     */
    public function store(Request $request)
    {
        $leader = auth()->user();
        
        $validated = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:bible_study,reflection,memorization,research',
            'due_date' => 'required|date|after:today',
            'max_points' => 'nullable|integer|min:1|max:100',
            'instructions' => 'nullable|string',
        ]);

        // Verify the leader owns this group
        $group = Group::findOrFail($validated['group_id']);
        if ($group->leader_id !== $leader->id) {
            return back()->withErrors(['group_id' => 'You can only create assignments for your own groups.']);
        }

        Assignment::create([
            ...$validated,
            'created_by' => $leader->id,
        ]);

        return redirect()->route('leader.assignments.index')
            ->with('success', 'Assignment created successfully.');
    }

    /**
     * Display the specified assignment.
     */
    public function show(Assignment $assignment): Response
    {
        // Ensure the leader owns this assignment's group
        if ($assignment->group->leader_id !== auth()->id()) {
            abort(403, 'You can only view assignments for your own groups.');
        }

        $assignment->load([
            'group',
            'submissions' => function ($query) {
                $query->with(['user'])
                      ->latest();
            }
        ]);

        return Inertia::render('leader/Assignments/Show', [
            'assignment' => $assignment,
        ]);
    }

    /**
     * Show the form for editing the specified assignment.
     */
    public function edit(Assignment $assignment): Response
    {
        // Ensure the leader owns this assignment's group
        if ($assignment->group->leader_id !== auth()->id()) {
            abort(403, 'You can only edit assignments for your own groups.');
        }

        $leader = auth()->user();
        
        // Get groups led by this leader
        $groups = $leader->ledGroups()
            ->where('is_active', true)
            ->get(['id', 'name']);

        return Inertia::render('leader/Assignments/Edit', [
            'assignment' => $assignment,
            'groups' => $groups,
        ]);
    }

    /**
     * Update the specified assignment.
     */
    public function update(Request $request, Assignment $assignment)
    {
        // Ensure the leader owns this assignment's group
        if ($assignment->group->leader_id !== auth()->id()) {
            abort(403, 'You can only update assignments for your own groups.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:bible_study,reflection,memorization,research',
            'due_date' => 'required|date',
            'max_points' => 'nullable|integer|min:1|max:100',
            'instructions' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        $assignment->update($validated);

        return redirect()->route('leader.assignments.show', $assignment)
            ->with('success', 'Assignment updated successfully.');
    }

    /**
     * Remove the specified assignment.
     */
    public function destroy(Assignment $assignment)
    {
        // Ensure the leader owns this assignment's group
        if ($assignment->group->leader_id !== auth()->id()) {
            abort(403, 'You can only delete assignments for your own groups.');
        }

        // Check if assignment has submissions
        if ($assignment->submissions()->count() > 0) {
            return back()->with('error', 'Cannot delete assignment with existing submissions.');
        }

        $assignment->delete();

        return redirect()->route('leader.assignments.index')
            ->with('success', 'Assignment deleted successfully.');
    }

    /**
     * Display submissions for the specified assignment.
     */
    public function submissions(Assignment $assignment): Response
    {
        // Ensure the leader owns this assignment's group
        if ($assignment->group->leader_id !== auth()->id()) {
            abort(403, 'You can only view submissions for your own assignments.');
        }

        $assignment->load([
            'group',
            'submissions' => function ($query) {
                $query->with(['user'])
                      ->latest();
            }
        ]);

        return Inertia::render('leader/Assignments/Submissions', [
            'assignment' => $assignment,
        ]);
    }

    /**
     * Grade a submission.
     */
    public function grade(Request $request, Assignment $assignment, $submissionId)
    {
        // Ensure the leader owns this assignment's group
        if ($assignment->group->leader_id !== auth()->id()) {
            abort(403, 'You can only grade submissions for your own assignments.');
        }

        $validated = $request->validate([
            'points' => 'required|integer|min:0|max:' . ($assignment->max_points ?? 100),
            'feedback' => 'nullable|string|max:1000',
        ]);

        $submission = $assignment->submissions()->findOrFail($submissionId);
        
        $submission->update([
            'points' => $validated['points'],
            'feedback' => $validated['feedback'],
            'graded_at' => now(),
            'graded_by' => auth()->id(),
        ]);

        return back()->with('success', 'Submission graded successfully.');
    }
}
