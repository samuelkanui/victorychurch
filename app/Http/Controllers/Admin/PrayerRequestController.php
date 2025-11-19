<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrayerRequest;
use App\Models\Group;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PrayerRequestController extends Controller
{
    /**
     * Display a listing of prayer requests.
     */
    public function index(Request $request): Response
    {
        $query = PrayerRequest::with(['user']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by privacy level
        if ($request->filled('privacy')) {
            $query->where('privacy', $request->get('privacy'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $prayers = $query->latest()->paginate(15);

        // Get statistics
        $stats = [
            'total_prayers' => PrayerRequest::count(),
            'public_prayers' => PrayerRequest::where('privacy', 'public')->count(),
            'answered_prayers' => PrayerRequest::where('status', 'answered')->count(),
            'pending_prayers' => PrayerRequest::where('status', 'pending')->count(),
        ];

        return Inertia::render('admin/Prayers/Index', [
            'prayers' => $prayers,
            'stats' => $stats,
            'filters' => $request->only(['search', 'privacy', 'status']),
        ]);
    }

    /**
     * Display the specified prayer request.
     */
    public function show(PrayerRequest $prayer): Response
    {
        $prayer->load(['user']);

        return Inertia::render('admin/Prayers/Show', [
            'prayer' => $prayer,
        ]);
    }

    /**
     * Update the specified prayer request.
     */
    public function update(Request $request, PrayerRequest $prayer)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,answered,closed',
            'admin_notes' => 'nullable|string',
        ]);

        $prayer->update($validated);

        return back()->with('success', 'Prayer request updated successfully.');
    }

    /**
     * Remove the specified prayer request.
     */
    public function destroy(PrayerRequest $prayer)
    {
        $prayer->delete();

        return redirect()->route('admin.prayers.index')
            ->with('success', 'Prayer request deleted successfully.');
    }
}
