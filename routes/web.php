<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StaffDashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\StaffAuthController;

Route::get("/", function () {
    return view("welcome");
});

Route::get("/dashboard", function () {
    return view("staff.dashboard");
})
    ->middleware(["auth", "verified"])
    ->name("dashboard");

Route::middleware("auth")->group(function () {
    Route::get("/profile", [ProfileController::class, "edit"])->name(
        "profile.edit",
    );
    Route::patch("/profile", [ProfileController::class, "update"])->name(
        "profile.update",
    );
    Route::delete("/profile", [ProfileController::class, "destroy"])->name(
        "profile.destroy",
    );
});

// Staff routes
Route::middleware(["auth"])->group(function () {
    Route::get("/staff/dashboard", [
        StaffDashboardController::class,
        "index",
    ])->name("staff.dashboard");

    Route::get("/staff/directory", function () {
        return view("staff.directory");
    })->name("staff.directory");

    Route::get("/staff/appointments", function () {
        return view("staff.appointments");
    })->name("staff.appointments");

    Route::get("/staff/insights", function () {
        return view("staff.insights");
    })->name("staff.insights");
});
Route::get("/staff/login", [StaffAuthController::class, "showLoginForm"])->name(
    "staff.login",
);
Route::post("/staff/login", [StaffAuthController::class, "login"]);

require __DIR__ . "/auth.php";
