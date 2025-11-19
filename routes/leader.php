<?php

use App\Http\Controllers\Leader\DashboardController;
use App\Http\Controllers\Leader\GroupController;
use App\Http\Controllers\Leader\MemberController;
use App\Http\Controllers\Leader\PrayerRequestController;
use App\Http\Controllers\Leader\AssignmentController;
use App\Http\Controllers\Leader\MeetingController;
use App\Http\Controllers\Leader\ResourceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:leader', 'prevent.back'])->prefix('leader')->name('leader.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // My Groups Management
    Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
    Route::get('/groups/{group}', [GroupController::class, 'show'])->name('groups.show');
    Route::put('/groups/{group}', [GroupController::class, 'update'])->name('groups.update');
    
    // Group Members Management
    Route::get('/groups/{group}/members', [MemberController::class, 'index'])->name('groups.members.index');
    Route::post('/groups/{group}/members/{user}/approve', [MemberController::class, 'approve'])->name('groups.members.approve');
    Route::post('/groups/{group}/members/{user}/reject', [MemberController::class, 'reject'])->name('groups.members.reject');
    Route::post('/groups/{group}/members/{user}/ban', [MemberController::class, 'ban'])->name('groups.members.ban');
    Route::post('/groups/{group}/members/{user}/unban', [MemberController::class, 'unban'])->name('groups.members.unban');
    Route::delete('/groups/{group}/members/{user}', [MemberController::class, 'remove'])->name('groups.members.remove');
    
    // Prayer Requests Management
    Route::get('/prayers', [PrayerRequestController::class, 'index'])->name('prayers.index');
    Route::get('/prayers/{prayerRequest}', [PrayerRequestController::class, 'show'])->name('prayers.show');
    Route::post('/prayers/{prayerRequest}/respond', [PrayerRequestController::class, 'respond'])->name('prayers.respond');
    Route::put('/prayers/{prayerRequest}/moderate', [PrayerRequestController::class, 'moderate'])->name('prayers.moderate');
    
    // Assignments & Bible Studies
    Route::resource('assignments', AssignmentController::class);
    Route::get('/assignments/{assignment}/submissions', [AssignmentController::class, 'submissions'])->name('assignments.submissions');
    Route::post('/assignments/{assignment}/submissions/{submission}/grade', [AssignmentController::class, 'grade'])->name('assignments.submissions.grade');
    
    // Meetings Management
    Route::resource('meetings', MeetingController::class);
    Route::post('/meetings/{meeting}/attendance', [MeetingController::class, 'recordAttendance'])->name('meetings.attendance');
    
    // Resources Management
    Route::resource('resources', ResourceController::class);
    Route::get('/resources/{resource}/download', [ResourceController::class, 'download'])->name('resources.download');
    
    // Reports & Analytics
    Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');
});
