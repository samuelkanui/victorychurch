<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\ResourceProgress;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ResourceController extends Controller
{
    /**
     * Display resources accessible to member.
     */
    public function index(Request $request): Response
    {
        $member = auth()->user();
        $groupIds = $member->groups()
            ->where('group_user.status', 'approved')
            ->pluck('groups.id');

        $query = Resource::where(function ($q) use ($groupIds) {
                $q->where('visibility', 'public')
                  ->orWhere(function ($subQ) use ($groupIds) {
                      $subQ->where('visibility', 'group')
                           ->whereIn('group_id', $groupIds);
                  });
            })
            ->where('is_active', true)
            ->with(['group', 'uploader']);

        // Add progress information for the current member
        $query->with(['progress' => function ($q) use ($member) {
            $q->where('user_id', $member->id);
        }]);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->get('type'));
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->whereJsonContains('categories', $request->get('category'));
        }

        // Filter by progress status
        if ($request->filled('progress')) {
            $status = $request->get('progress');
            if ($status === 'not_started') {
                $query->whereDoesntHave('progress', function ($q) use ($member) {
                    $q->where('user_id', $member->id);
                });
            } else {
                $query->whereHas('progress', function ($q) use ($member, $status) {
                    $q->where('user_id', $member->id)
                      ->where('status', $status);
                });
            }
        }

        $resources = $query->latest('published_at')->paginate(12);

        // Transform resources to include progress status
        $resources->getCollection()->transform(function ($resource) {
            $progress = $resource->progress->first();
            $resource->user_progress = $progress ? [
                'status' => $progress->status,
                'percentage' => $progress->progress_percentage,
                'started_at' => $progress->started_at,
                'completed_at' => $progress->completed_at,
            ] : null;
            unset($resource->progress);
            return $resource;
        });

        // Get available categories
        $categories = Resource::where('is_active', true)
            ->whereNotNull('categories')
            ->pluck('categories')
            ->flatten()
            ->unique()
            ->sort()
            ->values();

        // Get statistics
        $stats = [
            'available_resources' => Resource::where('is_active', true)
                ->where(function ($q) use ($groupIds) {
                    $q->where('visibility', 'public')
                      ->orWhere(function ($subQ) use ($groupIds) {
                          $subQ->where('visibility', 'group')
                               ->whereIn('group_id', $groupIds);
                      });
                })->count(),
            'completed_resources' => ResourceProgress::where('user_id', $member->id)
                ->where('status', 'completed')->count(),
            'in_progress_resources' => ResourceProgress::where('user_id', $member->id)
                ->where('status', 'viewed')->count(),
            'downloaded_resources' => ResourceProgress::where('user_id', $member->id)
                ->where('status', 'downloaded')->count(),
        ];

        return Inertia::render('member/Resources/Index', [
            'resources' => $resources,
            'categories' => $categories,
            'stats' => $stats,
            'filters' => $request->only(['search', 'type', 'category', 'progress']),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Resource $resource): Response
    {
        $member = auth()->user();

        // Check if member can access this resource
        if (!$resource->isAccessibleBy($member)) {
            abort(403, 'You do not have permission to view this resource.');
        }

        $resource->load(['group', 'uploader']);

        // Get or create progress record
        $progress = ResourceProgress::firstOrCreate(
            [
                'resource_id' => $resource->id,
                'user_id' => $member->id,
            ],
            [
                'status' => 'viewed',
                'started_at' => now(),
            ]
        );

        return Inertia::render('member/Resources/Show', [
            'resource' => $resource,
            'progress' => $progress,
            'canDownload' => $resource->type !== 'link',
        ]);
    }

    /**
     * Download a resource file.
     */
    public function download(Resource $resource)
    {
        $member = auth()->user();

        // Check if member can access this resource
        if (!$resource->isAccessibleBy($member)) {
            abort(403, 'You do not have permission to download this resource.');
        }

        // Check if resource has a file
        if (!$resource->file_path || $resource->type === 'link') {
            abort(404, 'File not found.');
        }

        // Check if file exists
        if (!\Storage::disk('public')->exists($resource->file_path)) {
            abort(404, 'File not found.');
        }

        // Update progress
        ResourceProgress::updateOrCreate(
            [
                'resource_id' => $resource->id,
                'user_id' => $member->id,
            ],
            [
                'status' => 'downloaded',
                'started_at' => now(),
            ]
        );

        // Increment download count
        $resource->incrementDownloadCount();

        // Return file download
        return \Storage::disk('public')->download(
            $resource->file_path,
            $resource->file_name
        );
    }

    /**
     * Update progress for a resource.
     */
    public function updateProgress(Request $request, Resource $resource)
    {
        $member = auth()->user();

        // Check if member can access this resource
        if (!$resource->isAccessibleBy($member)) {
            abort(403, 'You do not have permission to update progress for this resource.');
        }

        $validated = $request->validate([
            'progress_percentage' => 'required|integer|min:0|max:100',
        ]);

        $progress = ResourceProgress::firstOrCreate(
            [
                'resource_id' => $resource->id,
                'user_id' => $member->id,
            ],
            [
                'status' => 'viewed',
                'started_at' => now(),
            ]
        );

        $progress->updateProgress($validated['progress_percentage']);

        return response()->json([
            'message' => 'Progress updated successfully.',
            'progress' => $progress->fresh(),
        ]);
    }

    /**
     * Mark resource as completed.
     */
    public function markCompleted(Resource $resource)
    {
        $member = auth()->user();

        // Check if member can access this resource
        if (!$resource->isAccessibleBy($member)) {
            abort(403, 'You do not have permission to mark this resource as completed.');
        }

        $progress = ResourceProgress::firstOrCreate(
            [
                'resource_id' => $resource->id,
                'user_id' => $member->id,
            ],
            [
                'status' => 'viewed',
                'started_at' => now(),
            ]
        );

        $progress->markAsCompleted();

        return back()->with('success', 'Resource marked as completed.');
    }
}
