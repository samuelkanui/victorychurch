<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeletionRequestController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\ResourceController;
use App\Http\Controllers\Admin\PrayerRequestController;
use App\Http\Controllers\Admin\MeetingController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:admin', 'prevent.back'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // User Management
    Route::resource('users', UserController::class);
    
    // Groups Management
    Route::resource('groups', GroupController::class);
    Route::post('/groups/{group}/members', [GroupController::class, 'addMembers'])->name('groups.members.add');
    Route::delete('/groups/{group}/members/{user}', [GroupController::class, 'removeMember'])->name('groups.members.destroy');
    
    // Resources Management
    Route::resource('resources', ResourceController::class);
    Route::get('/resources/{resource}/download', [ResourceController::class, 'download'])->name('resources.download');
    Route::get('/resources/{resource}/preview', [ResourceController::class, 'preview'])->name('resources.preview');
    
    // Prayer Requests Management
    Route::resource('prayers', PrayerRequestController::class);
    
    // Meetings Management
    Route::resource('meetings', MeetingController::class);
    
    // Reports & Analytics
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    
    // Ban Appeals Management
    Route::get('/appeals', [SettingsController::class, 'appeals'])->name('appeals');
    
    // Deletion Requests Management
    Route::get('/deletion-requests', [DeletionRequestController::class, 'index'])->name('deletion-requests.index');
    Route::post('/deletion-requests/{deletionRequest}/approve', [DeletionRequestController::class, 'approve'])->name('deletion-requests.approve');
    Route::post('/deletion-requests/{deletionRequest}/reject', [DeletionRequestController::class, 'reject'])->name('deletion-requests.reject');
    
    // System Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    
    // Security & Roles
    Route::get('/security', [SettingsController::class, 'security'])->name('security');
});
