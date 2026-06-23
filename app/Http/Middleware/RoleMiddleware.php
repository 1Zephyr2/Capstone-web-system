<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(
        Request $request,
        Closure $next,
        string $role,
    ): Response {
        $user = $request->user();

        // Normalize roles to lowercase for comparison
        if (!$user) {
            return redirect()->route("login");
        }

        if (strtolower($user->role) !== strtolower($role)) {
            \Log::warning(
                "Unauthorized access attempt. User: {$user->email}, Expected Role: {$role}, Actual: {$user->role}",
            );

            // Redirect based on role
            if (strtolower($user->role) === "admin") {
                return redirect()->route("admin.dashboard");
            } elseif (strtolower($user->role) === "staff") {
                return redirect()->route("staff.dashboard");
            } else {
                return redirect()->route("dashboard");
            }
        }

        return $next($request);
    }
}
