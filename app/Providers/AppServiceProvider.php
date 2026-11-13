<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Older MySQL/MariaDB (common on shared hosting) limit index keys to 1000 bytes;
        // utf8mb4 + varchar(255) needs 1020. 191 characters fits everywhere. No effect on SQLite.
        Schema::defaultStringLength(191);

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
