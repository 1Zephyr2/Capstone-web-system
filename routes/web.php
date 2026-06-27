<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\StaffDashboardController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\StaffAppointmentController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\StaffAuthController;
use Illuminate\Support\Facades\Route;

// ── Public Routes ──────────────────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
});

require __DIR__ . '/auth.php';

// ── Customer / Owner Routes ────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        $pets = \App\Models\Pet::where('user_id', auth()->id())->get();
        $appointments = \App\Models\Appointment::with('pet')
            ->where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'approved'])
            ->orderBy('appointment_date')
            ->take(3)
            ->get();
        return view('dashboard', compact('pets', 'appointments'));
    })->name('dashboard');

    // Pets
    Route::post('/pets', [PetController::class, 'store'])->name('pets.store');
    Route::patch('/pets/{pet}', [PetController::class, 'update'])->name('pets.update');
    Route::delete('/pets/{pet}', [PetController::class, 'destroy'])->name('pets.destroy');

    // Appointments
    Route::get('/request-appointment', [AppointmentController::class, 'create'])->name('request.appointment');
    Route::post('/request-appointment', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::patch('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ── Shared Routes (Admin + Staff can view pet details) ─────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/pets/{id}', function ($id) {
        $role = auth()->user()->role;
        if ($role === 'admin' || $role === 'staff') {
            return view('pets.details', ['id' => $id]);
        }
        return view('pets.owner-details', ['id' => $id]);
    })->name('pets.details');

    Route::get('/pets/{id}/edit', function ($id) {
        return view('pets.edit', ['id' => $id]);
    })->name('pets.edit');
});

// ── Staff Login ────────────────────────────────────────────────────────────────
Route::get('/staff/login', [StaffAuthController::class, 'showLoginForm'])->name('staff.login');
Route::post('/staff/login', [StaffAuthController::class, 'login']);

// ── Admin Login ────────────────────────────────────────────────────────────────
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login']);

// ── Admin Routes ───────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn() => redirect()->route('admin.dashboard'));
    Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');
    Route::get('/directory', fn() => view('admin.directory'))->name('directory');
    Route::get('/insights', fn() => view('admin.insights'))->name('insights');
    Route::get('/panel', fn() => view('admin.panel'))->name('panel');

    Route::get('/appointments', [StaffAppointmentController::class, 'index'])->name('appointments');
    Route::patch('/appointments/{appointment}/approve', [StaffAppointmentController::class, 'approve'])->name('appointments.approve');
    Route::patch('/appointments/{appointment}/reject', [StaffAppointmentController::class, 'reject'])->name('appointments.reject');
    Route::patch('/appointments/{appointment}/complete', [StaffAppointmentController::class, 'complete'])->name('appointments.complete');
    Route::patch('/appointments/{appointment}/cancel', [StaffAppointmentController::class, 'cancel'])->name('appointments.cancel');
});

// ── Staff Routes ───────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/', fn() => redirect()->route('staff.dashboard'));
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
    Route::get('/directory', fn() => view('staff.directory'))->name('directory');
    Route::get('/insights', fn() => view('staff.insights'))->name('insights');

    Route::get('/appointments', [StaffAppointmentController::class, 'index'])->name('appointments');
    Route::patch('/appointments/{appointment}/approve', [StaffAppointmentController::class, 'approve'])->name('appointments.approve');
    Route::patch('/appointments/{appointment}/reject', [StaffAppointmentController::class, 'reject'])->name('appointments.reject');
    Route::patch('/appointments/{appointment}/complete', [StaffAppointmentController::class, 'complete'])->name('appointments.complete');
    Route::patch('/appointments/{appointment}/cancel', [StaffAppointmentController::class, 'cancel'])->name('appointments.cancel');
});