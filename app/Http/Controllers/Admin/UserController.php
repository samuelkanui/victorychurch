<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): Response
    {
        $query = User::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->where('role', $request->get('role'));
        }

        $users = $query->select([
                'id',
                'name', 
                'email',
                'role',
                'email_verified_at',
                'last_login_at',
                'is_active',
                'created_at',
            ])
            ->withCount(['groups'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'role']),
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): Response
    {
        return Inertia::render('admin/Users/Create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,leader,member',
        ]);

        // Store plain password for email
        $plainPassword = $validated['password'];

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($plainPassword),
            'role' => $validated['role'],
            'email_verified_at' => null, // Require OTP verification on first login
            'is_active' => false, // Will be activated after OTP verification
        ]);

        // Send email with account credentials
        \Illuminate\Support\Facades\Mail::to($user->email)
            ->send(new \App\Mail\UserAccountCreated($user, $plainPassword));

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully. Login credentials have been sent to their email.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): Response
    {
        // Load relationships when they exist
        // $user->load(['groups', 'assignmentSubmissions', 'prayerRequests']);

        return Inertia::render('admin/Users/Show', [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): Response
    {
        return Inertia::render('admin/Users/Edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,leader,member',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Add checks to prevent deleting users with important relationships
        // if ($user->groups()->exists() || $user->assignmentSubmissions()->exists()) {
        //     return back()->with('error', 'Cannot delete user with existing groups or submissions.');
        // }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
