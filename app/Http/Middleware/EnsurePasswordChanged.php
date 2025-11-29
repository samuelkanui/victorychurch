<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChanged
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        // Check if user exists and requires password change
        if ($user && $user->requires_password_change) {
            // Allow access only to password change routes and logout
            $allowedRoutes = [
                'user-password.edit',
                'user-password.update',
                'logout',
            ];
            
            // Check if current route is allowed
            if (!$request->routeIs($allowedRoutes)) {
                return redirect()->route('user-password.edit')
                    ->with('mustChangePassword', true);
            }
        }

        return $next($request);
    }
}
