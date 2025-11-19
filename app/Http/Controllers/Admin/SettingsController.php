<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    /**
     * Display system settings.
     */
    public function index(): Response
    {
        // System information
        $systemInfo = [
            'app_name' => config('app.name'),
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug'),
            'app_url' => config('app.url'),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database_driver' => config('database.default'),
            'cache_driver' => config('cache.default'),
            'queue_driver' => config('queue.default'),
            'mail_driver' => config('mail.default'),
        ];

        // Database statistics
        $databaseStats = [
            'total_users' => \App\Models\User::count(),
            'total_groups' => \App\Models\Group::count(),
            'total_meetings' => \App\Models\Meeting::count(),
            'total_prayers' => \App\Models\PrayerRequest::count(),
            'total_resources' => \App\Models\Resource::count(),
            'database_size' => $this->getDatabaseSize(),
        ];

        // Email configuration status
        $emailConfig = [
            'driver' => config('mail.default'),
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
        ];

        // Security features status
        $securityFeatures = [
            'two_factor_enabled' => true, // Laravel Fortify feature
            'email_verification' => true,
            'password_reset' => true,
            'session_timeout' => config('session.lifetime'),
        ];

        return Inertia::render('admin/Settings/Index', [
            'systemInfo' => $systemInfo,
            'databaseStats' => $databaseStats,
            'emailConfig' => $emailConfig,
            'securityFeatures' => $securityFeatures,
        ]);
    }

    /**
     * Get database size (SQLite only for now)
     */
    private function getDatabaseSize(): string
    {
        try {
            $dbPath = database_path('database.sqlite');
            if (file_exists($dbPath)) {
                $bytes = filesize($dbPath);
                if ($bytes >= 1073741824) {
                    return number_format($bytes / 1073741824, 2) . ' GB';
                } elseif ($bytes >= 1048576) {
                    return number_format($bytes / 1048576, 2) . ' MB';
                } elseif ($bytes >= 1024) {
                    return number_format($bytes / 1024, 2) . ' KB';
                }
                return $bytes . ' bytes';
            }
            return 'N/A';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Display security and roles management.
     */
    public function security(): Response
    {
        $stats = [
            'admin_count' => \App\Models\User::where('role', 'admin')->where('is_active', true)->count(),
            'leader_count' => \App\Models\User::where('role', 'leader')->where('is_active', true)->count(),
            'member_count' => \App\Models\User::where('role', 'member')->where('is_active', true)->count(),
            'total_users' => \App\Models\User::where('is_active', true)->count(),
            'inactive_users' => \App\Models\User::where('is_active', false)->count(),
            'verified_users' => \App\Models\User::whereNotNull('email_verified_at')->count(),
            'unverified_users' => \App\Models\User::whereNull('email_verified_at')->count(),
        ];

        // Recent user activity
        $recentUsers = \App\Models\User::latest('created_at')
            ->take(5)
            ->get(['id', 'name', 'email', 'role', 'created_at', 'is_active', 'email_verified_at']);

        // Users by role breakdown
        $roleDistribution = [
            'admin' => $stats['admin_count'],
            'leader' => $stats['leader_count'],
            'member' => $stats['member_count'],
        ];

        // Security metrics
        $securityMetrics = [
            'active_users' => $stats['total_users'],
            'inactive_users' => $stats['inactive_users'],
            'verified_rate' => $stats['total_users'] > 0 
                ? round(($stats['verified_users'] / $stats['total_users']) * 100, 1) 
                : 0,
            'unverified_count' => $stats['unverified_users'],
            'recent_signups' => \App\Models\User::where('created_at', '>=', now()->subDays(7))->count(),
            'recent_logins' => \App\Models\User::where('last_login_at', '>=', now()->subDays(7))->count(),
        ];

        return Inertia::render('admin/Security/Index', [
            'stats' => $stats,
            'recentUsers' => $recentUsers,
            'roleDistribution' => $roleDistribution,
            'securityMetrics' => $securityMetrics,
        ]);
    }

    /**
     * Display ban appeals management.
     */
    public function appeals(): Response
    {
        return Inertia::render('admin/Appeals/Index');
    }
}
