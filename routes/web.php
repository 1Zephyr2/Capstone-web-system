<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StaffDashboardController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\StaffAuthController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get("/", function () {
    return view("welcome");
});

// Auth Routes (Login/Register/Logout)
require __DIR__ . "/auth.php";

// Customer/Owner Routes
Route::middleware(["auth", "verified"])->group(function () {
    Route::get("/dashboard", function () {
        return view("dashboard"); // This is the Customer Dashboard
    })->name("dashboard");

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

// Admin Routes
Route::middleware(["auth", "role:admin"])->group(function () {
    Route::get("/admin/dashboard", function () {
        return view("admin.dashboard");
    })->name("admin.dashboard");

    Route::get("/admin/appointments", function () {
        return view("admin.appointments");
    })->name("admin.appointments");

    Route::get("/admin/directory", function () {
        return view("admin.directory");
    })->name("admin.directory");

    Route::get("/admin/insights", function () {
        return view("admin.insights");
    })->name("admin.insights");

    Route::get("/admin/panel", function () {
        return view("admin.panel");
    })->name("admin.panel");
});

// Staff Routes
Route::middleware(["auth", "role:staff"])->group(function () {
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

Route::get("/admin/login", [AdminAuthController::class, "showLoginForm"])->name(
    "admin.login",
);
Route::post("/admin/login", [AdminAuthController::class, "login"]);
