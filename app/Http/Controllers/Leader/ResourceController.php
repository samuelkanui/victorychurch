<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ResourceController extends Controller
{
    /**
     * Display resources for leader's groups.
     */
    public function index(Request $request): Response
    {
        $leader = auth()->user();
        $groupIds = $leader->ledGroups()->pluck('id');

        $query = Resource::whereIn('group_id', $groupIds)
            ->with(['group', 'uploader'])
            ->withCount(['progress']);

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

        // Filter by group
        if ($request->filled('group_id')) {
            $query->where('group_id', $request->get('group_id'));
        }

        $resources = $query->latest()->paginate(15);

        // Get leader's groups for filter
        $groups = $leader->ledGroups()->select('id', 'name')->get();

        // Get statistics
        $stats = [
            'total_resources' => Resource::whereIn('group_id', $groupIds)->count(),
            'active_resources' => Resource::whereIn('group_id', $groupIds)->where('is_active', true)->count(),
            'total_downloads' => Resource::whereIn('group_id', $groupIds)->sum('download_count'),
            'public_resources' => Resource::whereIn('group_id', $groupIds)->where('visibility', 'public')->count(),
        ];

        return Inertia::render('leader/Resources/Index', [
            'resources' => $resources,
            'groups' => $groups,
            'stats' => $stats,
            'filters' => $request->only(['search', 'type', 'group_id']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $leader = auth()->user();
        $groups = $leader->ledGroups()->select('id', 'name')->get();

        return Inertia::render('leader/Resources/Create', [
            'groups' => $groups,
        ]);
    }

    /**
     * Store a newly created resource.
     */
    public function store(Request $request)
    {
        $leader = auth()->user();
        $groupIds = $leader->ledGroups()->pluck('id');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:file,link,video,document,audio',
            'group_id' => 'required_if:visibility,group|nullable|in:' . $groupIds->implode(','),
            'visibility' => 'required|in:group,public',
            'external_url' => 'required_if:type,link|nullable|url',
            'file' => 'required_unless:type,link|nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,gif,mp3,mp4,wav,avi,mov,zip|max:51200',
            'categories' => 'nullable|array',
            'categories.*' => 'string|max:50',
        ]);

        // For public resources, use the first group the leader manages
        $groupId = $validated['group_id'] ?? $groupIds->first();
        
        $resourceData = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'group_id' => $groupId,
            'uploaded_by' => auth()->id(),
            'visibility' => $validated['visibility'],
            'categories' => $validated['categories'] ?? [],
            'is_active' => true,
            'published_at' => now(),
        ];

        if ($validated['type'] === 'link') {
            $resourceData['external_url'] = $validated['external_url'];
        } else {
            $file = $request->file('file');
            $fileName = \Str::uuid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('resources', $fileName, 'public');
            
            $resourceData['file_path'] = $filePath;
            $resourceData['file_name'] = $file->getClientOriginalName();
            $resourceData['file_size'] = $file->getSize();
            $resourceData['mime_type'] = $file->getMimeType();
        }

        $resource = Resource::create($resourceData);

        return redirect()->route('leader.resources.index')
            ->with('success', 'Resource uploaded successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Resource $resource): Response
    {
        $leader = auth()->user();
        
        // Check if leader owns this resource's group
        if (!$leader->ledGroups()->where('id', $resource->group_id)->exists()) {
            abort(403, 'You do not have permission to view this resource.');
        }

        $resource->load(['group', 'uploader', 'progress.user']);

        return Inertia::render('leader/Resources/Show', [
            'resource' => $resource,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Resource $resource): Response
    {
        $leader = auth()->user();
        
        // Check if leader owns this resource's group
        if (!$leader->ledGroups()->where('id', $resource->group_id)->exists()) {
            abort(403, 'You do not have permission to edit this resource.');
        }

        $groups = $leader->ledGroups()->select('id', 'name')->get();

        return Inertia::render('leader/Resources/Edit', [
            'resource' => $resource,
            'groups' => $groups,
        ]);
    }

    /**
     * Update the specified resource.
     */
    public function update(Request $request, Resource $resource)
    {
        $leader = auth()->user();
        $groupIds = $leader->ledGroups()->pluck('id');
        
        // Check if leader owns this resource's group
        if (!$groupIds->contains($resource->group_id)) {
            abort(403, 'You do not have permission to update this resource.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'group_id' => 'required|in:' . $groupIds->implode(','),
            'visibility' => 'required|in:group,public',
            'is_active' => 'boolean',
            'external_url' => 'required_if:type,link|nullable|url',
            'categories' => 'nullable|array',
            'categories.*' => 'string|max:50',
        ]);

        $resource->update($validated);

        return redirect()->route('leader.resources.show', $resource)
            ->with('success', 'Resource updated successfully.');
    }

    /**
     * Remove the specified resource.
     */
    public function destroy(Resource $resource)
    {
        $leader = auth()->user();
        
        // Check if leader owns this resource's group
        if (!$leader->ledGroups()->where('id', $resource->group_id)->exists()) {
            abort(403, 'You do not have permission to delete this resource.');
        }

        // Delete file if it exists
        if ($resource->file_path && \Storage::disk('public')->exists($resource->file_path)) {
            \Storage::disk('public')->delete($resource->file_path);
        }

        $resource->delete();

        return redirect()->route('leader.resources.index')
            ->with('success', 'Resource deleted successfully.');
    }

    /**
     * Download the specified resource.
     */
    public function download(Resource $resource)
    {
        $leader = auth()->user();
        
        // Check if leader owns this resource's group
        if (!$leader->ledGroups()->where('id', $resource->group_id)->exists()) {
            abort(403, 'You do not have permission to download this resource.');
        }

        if (!$resource->file_path || !Storage::disk('public')->exists($resource->file_path)) {
            abort(404, 'File not found.');
        }

        $resource->incrementDownloadCount();

        return Storage::disk('public')->download($resource->file_path, $resource->file_name);
    }
}
