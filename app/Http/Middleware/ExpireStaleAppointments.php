<?php

namespace App\Http\Middleware;

use App\Models\Appointment;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class ExpireStaleAppointments
{
    public function handle(Request $request, Closure $next)
    {
        // Run at most once every 5 minutes so it never slows pages down.
        try {
            Cache::remember('appointments.expire_stale', 300, function () {
                if (Schema::hasTable('appointments')) {
                    Appointment::expireStale();
                }
                return true;
            });
        } catch (\Throwable $e) {
            // never block a page because of cleanup
        }

        return $next($request);
    }
}
