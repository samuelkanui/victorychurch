<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ResourceController extends Controller
{
    /**
     * Display a listing of all resources.
     */
    public function index(Request $request): Response
    {
        $query = Resource::with(['group', 'uploader'])
            ->withCount(['progress']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('uploader', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->get('type'));
        }

        // Filter by visibility
        if ($request->filled('visibility')) {
            $query->where('visibility', $request->get('visibility'));
        }

        // Filter by group
        if ($request->filled('group_id')) {
            $query->where('group_id', $request->get('group_id'));
        }

        $resources = $query->latest()->paginate(15);

        // Get groups for filter dropdown
        $groups = Group::select('id', 'name')->orderBy('name')->get();

        // Get statistics
        $stats = [
            'total_resources' => Resource::count(),
            'active_resources' => Resource::where('is_active', true)->count(),
            'total_downloads' => Resource::sum('download_count'),
            'public_resources' => Resource::where('visibility', 'public')->count(),
        ];

        return Inertia::render('admin/Resources/Index', [
            'resources' => $resources,
            'groups' => $groups,
            'stats' => $stats,
            'filters' => $request->only(['search', 'type', 'visibility', 'group_id']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $groups = Group::select('id', 'name')->orderBy('name')->get();

        return Inertia::render('admin/Resources/Create', [
            'groups' => $groups,
        ]);
    }

    /**
     * Store a newly created resource.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:file,link,video,document,audio',
            'group_id' => 'required|exists:groups,id',
            'visibility' => 'required|in:group,public',
            'external_url' => 'required_if:type,link|nullable|url',
            'file' => 'required_unless:type,link|nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,gif,mp3,mp4,wav,avi,mov,zip|max:51200', // 50MB max
            'categories' => 'nullable|array',
            'categories.*' => 'string|max:50',
        ]);

        $resourceData = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'group_id' => $validated['group_id'],
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

        return redirect()->route('admin.resources.show', $resource)
            ->with('success', 'Resource created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Resource $resource): Response
    {
        $resource->load(['group', 'uploader', 'progress.user']);

        return Inertia::render('admin/Resources/Show', [
            'resource' => $resource,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Resource $resource): Response
    {
        $groups = Group::select('id', 'name')->orderBy('name')->get();

        return Inertia::render('admin/Resources/Edit', [
            'resource' => $resource,
            'groups' => $groups,
        ]);
    }

    /**
     * Update the specified resource.
     */
    public function update(Request $request, Resource $resource)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'group_id' => 'required|exists:groups,id',
            'visibility' => 'required|in:group,public',
            'is_active' => 'boolean',
            'external_url' => 'required_if:type,link|nullable|url',
            'categories' => 'nullable|array',
            'categories.*' => 'string|max:50',
        ]);

        $resource->update($validated);

        return redirect()->route('admin.resources.show', $resource)
            ->with('success', 'Resource updated successfully.');
    }

    /**
     * Remove the specified resource.
     */
    public function destroy(Resource $resource)
    {
        // Delete file if it exists
        if ($resource->file_path && \Storage::disk('public')->exists($resource->file_path)) {
            \Storage::disk('public')->delete($resource->file_path);
        }

        $resource->delete();

        return redirect()->route('admin.resources.index')
            ->with('success', 'Resource deleted successfully.');
    }

    /**
     * Download the specified resource.
     */
    public function download(Resource $resource)
    {
        if (!$resource->file_path || !Storage::disk('public')->exists($resource->file_path)) {
            abort(404, 'File not found.');
        }

        $resource->incrementDownloadCount();

        return Storage::disk('public')->download($resource->file_path, $resource->file_name);
    }

    /**
     * Preview the specified resource.
     */
    public function preview(Resource $resource)
    {
        if (!$resource->file_path || !Storage::disk('public')->exists($resource->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->response($resource->file_path);
    }
}
