<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define("access-staff", function (User $user) {
            return $user->role === "staff";
        });
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}
