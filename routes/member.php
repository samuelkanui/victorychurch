<?php

use App\Http\Controllers\Member\DashboardController;
use App\Http\Controllers\Member\GroupController;
use App\Http\Controllers\Member\PrayerRequestController;
use App\Http\Controllers\Member\AssignmentController;
use App\Http\Controllers\Member\AssignmentSubmissionController;
use App\Http\Controllers\Member\MeetingController;
use App\Http\Controllers\Member\ProfileController;
use App\Http\Controllers\Member\ResourceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Member Routes
|--------------------------------------------------------------------------
|
| Here are the routes for church members. These routes are protected
| by the 'member' role middleware to ensure only members can access them.
|
*/

Route::middleware(['auth', 'verified', 'role:member', 'prevent.back'])->prefix('member')->name('member.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Groups
    Route::prefix('groups')->name('groups.')->group(function () {
        Route::get('/', [GroupController::class, 'index'])->name('index');
        Route::get('/available', [GroupController::class, 'available'])->name('available');
        Route::post('/{group}/join', [GroupController::class, 'join'])->name('join');
        Route::delete('/{group}/leave', [GroupController::class, 'leave'])->name('leave');
        Route::get('/{group}', [GroupController::class, 'show'])->name('show');
    });
    
    // Prayer Requests
    Route::prefix('prayers')->name('prayers.')->group(function () {
        Route::get('/', [PrayerRequestController::class, 'index'])->name('index');
        Route::get('/create', [PrayerRequestController::class, 'create'])->name('create');
        Route::post('/', [PrayerRequestController::class, 'store'])->name('store');
        Route::get('/{prayerRequest}', [PrayerRequestController::class, 'show'])->name('show');
        Route::post('/{prayerRequest}/pray', [PrayerRequestController::class, 'pray'])->name('pray');
        Route::get('/{prayerRequest}/edit', [PrayerRequestController::class, 'edit'])->name('edit');
        Route::put('/{prayerRequest}', [PrayerRequestController::class, 'update'])->name('update');
        Route::delete('/{prayerRequest}', [PrayerRequestController::class, 'destroy'])->name('destroy');
    });
    
    // Assignments
    Route::prefix('assignments')->name('assignments.')->group(function () {
        Route::get('/', [AssignmentController::class, 'index'])->name('index');
        Route::get('/{assignment}', [AssignmentController::class, 'show'])->name('show');
        Route::get('/{assignment}/submission', [AssignmentSubmissionController::class, 'show'])->name('submission');
        Route::post('/{assignment}/submit', [AssignmentSubmissionController::class, 'store'])->name('submit');
    });
    
    // Meetings
    Route::prefix('meetings')->name('meetings.')->group(function () {
        Route::get('/', [MeetingController::class, 'index'])->name('index');
        Route::get('/{meeting}', [MeetingController::class, 'show'])->name('show');
        Route::post('/{meeting}/rsvp', [MeetingController::class, 'rsvp'])->name('rsvp');
    });
    
    // Resources
    Route::prefix('resources')->name('resources.')->group(function () {
        Route::get('/', [ResourceController::class, 'index'])->name('index');
        Route::get('/{resource}', [ResourceController::class, 'show'])->name('show');
        Route::get('/{resource}/download', [ResourceController::class, 'download'])->name('download');
        Route::post('/{resource}/progress', [ResourceController::class, 'updateProgress'])->name('progress');
        Route::post('/{resource}/complete', [ResourceController::class, 'markCompleted'])->name('complete');
    });
    
    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
    });
});
