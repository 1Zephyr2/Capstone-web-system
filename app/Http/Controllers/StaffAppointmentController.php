<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class StaffAppointmentController extends Controller
{
    /**
     * List all appointments (for staff & admin booking sheet).
     * GET /staff/appointments  OR  /admin/appointments
     */
    public function index(Request $request)
{
    // The day being viewed in the schedule grid (defaults to today)
    $date = $request->filled('date')
        ? \Carbon\Carbon::parse($request->date)->startOfDay()
        : today();

    // Business hours shown in the grid (skips the 12:00 PM lunch slot)
    $slotTimes = ['09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00', '17:00'];

    $dayAppointments = Appointment::with(['user', 'pet'])
        ->whereDate('appointment_date', $date)
        ->whereNotIn('status', [Appointment::STATUS_REJECTED, Appointment::STATUS_CANCELLED])
        ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
        ->when($request->filled('service'), fn($q) => $q->where('service_id', $request->service))
        ->get()
        ->keyBy(fn($appt) => $appt->appointment_date->format('H:i'));

    $timeSlots = collect($slotTimes)->map(function ($slot) use ($dayAppointments, $date) {
        return [
            'time'        => $slot,
            'label'       => \Carbon\Carbon::parse($slot)->format('g:i A'),
            'datetime'    => $date->copy()->setTimeFromTimeString($slot . ':00'),
            'appointment' => $dayAppointments->get($slot),
        ];
    });

    $serviceTypes = \App\Models\Service::active()->orderBy('category')->orderBy('name')->get();
    $statusOptions = [
        Appointment::STATUS_PENDING,
        Appointment::STATUS_APPROVED,
        Appointment::STATUS_REJECTED,
        Appointment::STATUS_COMPLETED,
        Appointment::STATUS_CANCELLED,
    ];

    $stats = [
        'pending'  => Appointment::where('status', Appointment::STATUS_PENDING)->count(),
        'approved' => Appointment::where('status', Appointment::STATUS_APPROVED)->count(),
        'today'    => Appointment::whereDate('appointment_date', today())
                                  ->where('status', Appointment::STATUS_APPROVED)
                                  ->count(),
    ];

    return view('staff.appointments', compact(
        'timeSlots', 'date', 'serviceTypes', 'statusOptions', 'stats'
    ));
}

    /**
     * Approve a pending appointment.
     * PATCH /staff/appointments/{appointment}/approve
     */
    public function approve(Appointment $appointment)
    {
        abort_if(!$appointment->isPending(), 422, 'Only pending appointments can be approved.');

        $appointment->update(['status' => Appointment::STATUS_APPROVED]);

        return back()->with('success', "Appointment for {$appointment->pet->name} approved.");
    }

    /**
     * Reject a pending appointment with an optional reason.
     * PATCH /staff/appointments/{appointment}/reject
     */
    public function reject(Request $request, Appointment $appointment)
    {
        abort_if(!$appointment->isPending(), 422, 'Only pending appointments can be rejected.');

        $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:300'],
        ]);

        $appointment->update([
            'status'           => Appointment::STATUS_REJECTED,
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', "Appointment for {$appointment->pet->name} rejected.");
    }

    /**
     * Mark an approved appointment as completed.
     * PATCH /staff/appointments/{appointment}/complete
     */
    public function complete(Appointment $appointment)
    {
        abort_if(!$appointment->isApproved(), 422, 'Only approved appointments can be marked complete.');

        $appointment->update(['status' => Appointment::STATUS_COMPLETED]);

        return back()->with('success', "Appointment for {$appointment->pet->name} marked as completed.");
    }

    /**
     * Cancel an approved appointment (staff/admin side).
     * PATCH /staff/appointments/{appointment}/cancel
     */
    public function cancel(Appointment $appointment)
    {
        abort_if(!$appointment->isApproved(), 422, 'Only approved appointments can be cancelled here.');

        $appointment->update(['status' => Appointment::STATUS_CANCELLED]);

        return back()->with('success', "Appointment for {$appointment->pet->name} cancelled.");
    }

    public function updateNotes(Request $request, Appointment $appointment)
{
    $request->validate(['notes' => ['nullable', 'string', 'max:500']]);
    $appointment->update(['notes' => $request->notes]);
    return back()->with('success', 'Notes updated.');
}

/**
 * AJAX search for existing pets/owners (for the walk-in booking modal).
 * GET /staff/appointments/search-pets  OR  /admin/appointments/search-pets
 */
public function searchPets(Request $request)
{
    $q = trim($request->get('q', ''));

    if (strlen($q) < 2) {
        return response()->json([]);
    }

    $pets = \App\Models\Pet::with('user')
        ->where(function ($query) use ($q) {
            $query->where('name', 'like', "%{$q}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$q}%"));
        })
        ->limit(10)
        ->get()
        ->map(fn($pet) => [
            'pet_id'     => $pet->id,
            'pet_name'   => $pet->name,
            'breed'      => $pet->breed,
            'owner_name' => $pet->user->name,
        ]);

    return response()->json($pets);
}

/**
 * Create a staff/admin-initiated booking (existing owner or walk-in), auto-approved.
 * POST /staff/appointments  OR  /admin/appointments
 */
public function store(Request $request)
{
    $validated = $request->validate([
        'appointment_date' => ['required', 'date'],
        'service_type'     => ['required', 'in:' . implode(',', array_keys(Appointment::SERVICE_TYPES))],
        'notes'            => ['nullable', 'string', 'max:500'],
        'booking_mode'     => ['required', 'in:existing,walkin'],
        'pet_id'           => ['required_if:booking_mode,existing', 'nullable', 'exists:pets,id'],
        'owner_name'       => ['required_if:booking_mode,walkin', 'nullable', 'string', 'max:100'],
        'pet_name'         => ['required_if:booking_mode,walkin', 'nullable', 'string', 'max:100'],
        'pet_type'         => ['required_if:booking_mode,walkin', 'nullable', 'in:dog,cat,other'],
        'pet_breed'        => ['nullable', 'string', 'max:100'],
    ]);

    $slotDateTime = \Carbon\Carbon::parse($validated['appointment_date']);

    // Prevent double-booking the same slot
    $taken = Appointment::where('appointment_date', $slotDateTime)
        ->whereNotIn('status', [Appointment::STATUS_REJECTED, Appointment::STATUS_CANCELLED])
        ->exists();

    abort_if($taken, 422, 'That time slot is already booked.');

    if ($validated['booking_mode'] === 'existing') {
        $pet    = \App\Models\Pet::findOrFail($validated['pet_id']);
        $userId = $pet->user_id;
        $petId  = $pet->id;
    } else {
        // Lightweight guest account so the FK constraints stay satisfied
        $guest = \App\Models\User::create([
            'name'     => $validated['owner_name'],
            'email'    => 'walkin_' . uniqid() . '@furcare.local',
            'password' => bcrypt(str()->random(32)),
            'role'     => 'customer',
        ]);

        $pet = \App\Models\Pet::create([
            'user_id' => $guest->id,
            'name'    => $validated['pet_name'],
            'type'    => $validated['pet_type'],
            'breed'   => $validated['pet_breed'] ?? 'N/A',
            'age'     => 0,
        ]);

        $userId = $guest->id;
        $petId  = $pet->id;
    }

    Appointment::create([
        'user_id'          => $userId,
        'pet_id'           => $petId,
        'appointment_date' => $slotDateTime,
        'service_type'     => $validated['service_type'],
        'status'           => Appointment::STATUS_APPROVED,
        'notes'            => $validated['notes'] ?? null,
    ]);

    return back()->with('success', 'Walk-in appointment booked and approved.');
}
}