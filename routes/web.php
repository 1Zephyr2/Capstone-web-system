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
Route::middleware(['auth', 'verified', 'role:owner'])->group(function () {

    Route::get('/dashboard', function () {
        $pets = \App\Models\Pet::where('user_id', auth()->id())->get();
        $appointments = \App\Models\Appointment::with(['pet', 'service'])
            ->where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'approved'])
            ->orderBy('appointment_date')
            ->take(3)
            ->get();

        $stats = [
            'total_pets' => $pets->count(),
            'upcoming'   => \App\Models\Appointment::where('user_id', auth()->id())
                                ->whereIn('status', ['pending', 'approved'])
                                ->count(),
            'pending'    => \App\Models\Appointment::where('user_id', auth()->id())
                                ->where('status', 'pending')
                                ->count(),
        ];

        return view('dashboard', compact('pets', 'appointments', 'stats'));
    })->name('dashboard');

    // Pets
    Route::post('/pets', [PetController::class, 'store'])->name('pets.store');
    Route::patch('/pets/{pet}', [PetController::class, 'update'])->name('pets.update');
    Route::delete('/pets/{pet}', [PetController::class, 'destroy'])->name('pets.destroy');

    // Appointments
    Route::get('/request-appointment', [AppointmentController::class, 'create'])->name('request.appointment');
    Route::post('/request-appointment', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');

// Appointment history (all statuses)
    Route::get('/appointments/history', [AppointmentController::class, 'history'])->name('appointments.history');

// Edit appointment (owner)
    Route::get('/appointments/{appointment}/edit-form', [AppointmentController::class, 'edit'])->name('appointments.edit');
    Route::patch('/appointments/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');
    

    Route::patch('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::get('/appointments/availability', [AppointmentController::class, 'availability'])->name('appointments.availability');
Route::get('/appointments/availability-month', [AppointmentController::class, 'monthAvailability'])->name('appointments.availability.month');


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
    Route::post('/staff', [\App\Http\Controllers\AdminStaffController::class, 'store'])->name('staff.store');
Route::delete('/staff/{user}', [\App\Http\Controllers\AdminStaffController::class, 'destroy'])->name('staff.destroy');

// Medical records
Route::post('/pets/{pet}/records', [\App\Http\Controllers\PetRecordController::class, 'store'])->name('records.store');
Route::patch('/records/{record}', [\App\Http\Controllers\PetRecordController::class, 'update'])->name('records.update');
Route::delete('/records/{record}', [\App\Http\Controllers\PetRecordController::class, 'destroy'])->name('records.destroy');

// Edit appointment notes
Route::patch('/appointments/{appointment}/notes', [\App\Http\Controllers\StaffAppointmentController::class, 'updateNotes'])->name('appointments.notes');

Route::get('/appointments/search-pets', [StaffAppointmentController::class, 'searchPets'])->name('appointments.search-pets');
Route::post('/appointments', [StaffAppointmentController::class, 'store'])->name('appointments.store');

// Grooming options
Route::post('/grooming', [\App\Http\Controllers\GroomingOptionController::class, 'store'])->name('grooming.store');
Route::patch('/grooming/{groomingOption}', [\App\Http\Controllers\GroomingOptionController::class, 'toggle'])->name('grooming.toggle');
Route::delete('/grooming/{groomingOption}', [\App\Http\Controllers\GroomingOptionController::class, 'destroy'])->name('grooming.destroy');

// Grooming image upload
Route::post('/grooming/{groomingOption}/image', [\App\Http\Controllers\GroomingOptionController::class, 'updateImage'])->name('grooming.image');

Route::post('/services', [\App\Http\Controllers\ServiceController::class, 'store'])->name('services.store');
Route::patch('/services/{service}', [\App\Http\Controllers\ServiceController::class, 'update'])->name('services.update');
Route::patch('/services/{service}/toggle', [\App\Http\Controllers\ServiceController::class, 'toggle'])->name('services.toggle');
Route::patch('/services/{service}/restore', [\App\Http\Controllers\ServiceController::class, 'restore'])->name('services.restore');
Route::delete('/services/{service}', [\App\Http\Controllers\ServiceController::class, 'destroy'])->name('services.destroy');
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

    // Medical records
Route::post('/pets/{pet}/records', [\App\Http\Controllers\PetRecordController::class, 'store'])->name('records.store');
Route::patch('/records/{record}', [\App\Http\Controllers\PetRecordController::class, 'update'])->name('records.update');
Route::delete('/records/{record}', [\App\Http\Controllers\PetRecordController::class, 'destroy'])->name('records.destroy');

// Edit appointment notes
Route::patch('/appointments/{appointment}/notes', [\App\Http\Controllers\StaffAppointmentController::class, 'updateNotes'])->name('appointments.notes');

Route::get('/appointments/search-pets', [StaffAppointmentController::class, 'searchPets'])->name('appointments.search-pets');
Route::post('/appointments', [StaffAppointmentController::class, 'store'])->name('appointments.store');
});



// Public services page (outside all middleware)
Route::get('/services', fn() => view('services'))->name('services');

// Fallback for serving uploaded images (pet photos, grooming images, result photos)
// directly from storage/app/public, in case the public/storage symlink is missing —
// e.g. after cloning or zipping the project onto a different machine, where
// `php artisan storage:link` either wasn't run yet or the symlink didn't survive
// the copy (this is common on Windows and with plain zip transfers).
Route::get('/storage/{path}', function (string $path) {
    $fullPath = storage_path('app/public/' . $path);
    abort_unless(is_file($fullPath), 404);
    return response()->file($fullPath);
})->where('path', '.*')->name('storage.fallback');

