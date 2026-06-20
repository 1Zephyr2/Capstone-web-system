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

        if (!$user) {
            return redirect()->route("login");
        }

        if ($user->role !== $role) {
            // Log the failure to see what's happening
            \Log::warning(
                "Unauthorized access attempt. User: {$user->email}, Expected Role: {$role}, Actual: {$user->role}",
            );

            // Redirect based on role
            if ($user->role === "admin") {
                return redirect()->route("admin.dashboard");
            } elseif ($user->role === "staff") {
                return redirect()->route("staff.dashboard");
            } else {
                return redirect()->route("dashboard");
            }
        }

        return $next($request);
    }
}
