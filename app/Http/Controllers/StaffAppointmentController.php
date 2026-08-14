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
    $viewMode = $request->get('view', 'day'); // 'day' or 'week'

    // The day being viewed in the schedule grid (defaults to today)
    $date = $request->filled('date')
        ? \Carbon\Carbon::parse($request->date)->startOfDay()
        : today();

    // Business hours shown in the grid (skips the 12:00 PM lunch slot)
    $slotTimes = ['09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00', '17:00'];

    $weekDays = null;
    $weekStart = null;
    $weekEnd = null;

    if ($viewMode === 'week') {
        $weekStart = $date->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
        $weekEnd   = $weekStart->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);

        $weekAppointments = Appointment::with(['user', 'pet'])
            ->whereBetween('appointment_date', [$weekStart, $weekEnd->copy()->endOfDay()])
            ->whereNotIn('status', [Appointment::STATUS_REJECTED, Appointment::STATUS_CANCELLED])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('service'), fn($q) => $q->where('service_id', $request->service))
            ->get()
            ->groupBy(fn($appt) => $appt->appointment_date->format('Y-m-d') . ' ' . $appt->appointment_date->format('H:i'));

        $weekDays = collect(range(0, 6))->map(function ($i) use ($weekStart, $slotTimes, $weekAppointments) {
            $day = $weekStart->copy()->addDays($i);
            $dayKey = $day->format('Y-m-d');

            return [
                'date'       => $day,
                'label'      => $day->format('D'),
                'day_num'    => $day->format('j'),
                'is_today'   => $day->isToday(),
                'is_weekend' => in_array($day->dayOfWeek, [\Carbon\Carbon::SATURDAY, \Carbon\Carbon::SUNDAY]),
                'slots'      => collect($slotTimes)->map(function ($slot) use ($weekAppointments, $dayKey) {
                    $appts = $weekAppointments->get($dayKey . ' ' . $slot, collect());
                    return [
                        'time'         => $slot,
                        'appointments' => $appts,
                    ];
                }),
            ];
        });
    }

    $dayAppointments = Appointment::with(['user', 'pet'])
        ->whereDate('appointment_date', $date)
        ->whereNotIn('status', [Appointment::STATUS_REJECTED, Appointment::STATUS_CANCELLED])
        ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
        ->when($request->filled('service'), fn($q) => $q->where('service_id', $request->service))
        ->get()
        ->groupBy(fn($appt) => $appt->appointment_date->format('H:i'));

    $timeSlots = collect($slotTimes)->map(function ($slot) use ($dayAppointments, $date) {
        $appointmentsInSlot = $dayAppointments->get($slot, collect());
        return [
            'time'         => $slot,
            'label'        => \Carbon\Carbon::parse($slot)->format('g:i A'),
            'datetime'     => $date->copy()->setTimeFromTimeString($slot . ':00'),
            'appointments' => $appointmentsInSlot,
            'full'         => $appointmentsInSlot->count() >= Appointment::MAX_PER_SLOT,
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
        'timeSlots', 'date', 'serviceTypes', 'statusOptions', 'stats',
        'viewMode', 'weekDays', 'weekStart', 'weekEnd'
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

        \App\Models\AppNotification::notify(
            $appointment->user_id,
            'appointment_approved',
            'Appointment Confirmed',
            "Your appointment for {$appointment->pet->name} ({$appointment->service_label}) on {$appointment->appointment_date->format('M d, Y g:i A')} has been approved.",
            route('appointments.index')
        );

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

        \App\Models\AppNotification::notify(
            $appointment->user_id,
            'appointment_rejected',
            'Appointment Rejected',
            "Your appointment for {$appointment->pet->name} on {$appointment->appointment_date->format('M d, Y g:i A')} was rejected." . ($request->rejection_reason ? ' Reason: ' . $request->rejection_reason : ''),
            route('appointments.index')
        );

        return back()->with('success', "Appointment for {$appointment->pet->name} rejected.");
    }

    /**
     * Send a heads-up to the owner before actually marking the service complete
     * (e.g. "almost done, please prepare for pickup").
     * PATCH /staff/appointments/{appointment}/notify-almost-done
     */
    public function notifyAlmostDone(Appointment $appointment)
    {
        abort_if(!$appointment->isApproved(), 422, 'Only in-progress appointments can send this notice.');

        \App\Models\AppNotification::notify(
            $appointment->user_id,
            'almost_done',
            'Almost Done!',
            "{$appointment->pet->name}'s {$appointment->service_label} is almost done — please get ready for pickup soon.",
            route('appointments.index')
        );

        return back()->with('success', "Owner notified that {$appointment->pet->name} is almost ready.");
    }

    /**
     * Mark an approved appointment as completed — optionally with a result photo
     * and pickup details if someone other than the registered owner picked up the pet.
     * PATCH /staff/appointments/{appointment}/complete
     */
    public function complete(Request $request, Appointment $appointment)
    {
        abort_if(!$appointment->isApproved(), 422, 'Only approved appointments can be marked complete.');

        $validated = $request->validate([
            'result_photo'     => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'different_pickup' => ['nullable', 'boolean'],
            'picked_up_by'     => ['required_if:different_pickup,1', 'nullable', 'string', 'max:100'],
            'pickup_note'      => ['required_if:different_pickup,1', 'nullable', 'string', 'max:500'],
        ]);

        $data = [
            'status'           => Appointment::STATUS_COMPLETED,
            'different_pickup' => $request->boolean('different_pickup'),
            'picked_up_by'     => $request->boolean('different_pickup') ? $validated['picked_up_by'] : null,
            'pickup_note'      => $request->boolean('different_pickup') ? $validated['pickup_note'] : null,
        ];

        if ($request->hasFile('result_photo') && $request->file('result_photo')->isValid()) {
            $data['result_photo'] = $request->file('result_photo')->store('appointments/results', 'public');
        }

        $appointment->update($data);

        \App\Models\AppNotification::notify(
            $appointment->user_id,
            'appointment_completed',
            'Service Completed',
            "{$appointment->pet->name}'s {$appointment->service_label} is complete and ready for pickup.",
            route('appointments.index')
        );

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
        ->sortBy(fn($pet) => $pet->user->name)
        ->values()
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
        'appointment_date' => ['required', 'date', function ($attribute, $value, $fail) {
            $day = \Carbon\Carbon::parse($value)->dayOfWeek;
            if (in_array($day, [\Carbon\Carbon::SATURDAY, \Carbon\Carbon::SUNDAY])) {
                $fail('We are closed on weekends. Please choose a weekday.');
            }
            if (\Carbon\Carbon::parse($value)->isPast()) {
                $fail('That time slot has already passed. Please choose a current or upcoming time.');
            }
        }],
        'service_id'       => ['required', 'exists:services,id'],
        'notes'            => ['nullable', 'string', 'max:500'],
        'booking_mode'     => ['required', 'in:existing,walkin'],
        'pet_id'           => ['required_if:booking_mode,existing', 'nullable', 'exists:pets,id'],
        'owner_name'       => ['required_if:booking_mode,walkin', 'nullable', 'string', 'max:100'],
        'pet_name'         => ['required_if:booking_mode,walkin', 'nullable', 'string', 'max:100'],
        'pet_type'         => ['required_if:booking_mode,walkin', 'nullable', 'in:dog,cat,other'],
        'pet_breed'        => ['nullable', 'string', 'max:100'],
        'pet_size'         => ['nullable', 'in:' . implode(',', \App\Models\Pet::SIZES)],
    ]);

    $slotDateTime = \Carbon\Carbon::parse($validated['appointment_date']);

    // Allow multiple pets per slot up to capacity (parallel stations/staff)
    abort_if(!Appointment::slotHasCapacity($slotDateTime), 422, 'That time slot is fully booked.');

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
            'role'     => 'owner',
        ]);

        $pet = \App\Models\Pet::create([
            'user_id' => $guest->id,
            'name'    => $validated['pet_name'],
            'type'    => $validated['pet_type'],
            'breed'   => $validated['pet_breed'] ?? 'N/A',
            'size'    => $validated['pet_size'] ?? null,
            'age'     => 0,
        ]);

        $userId = $guest->id;
        $petId  = $pet->id;
    }

    Appointment::create([
        'user_id'          => $userId,
        'pet_id'           => $petId,
        'appointment_date' => $slotDateTime,
        'service_id'       => $validated['service_id'],
        'status'           => Appointment::STATUS_APPROVED,
        'notes'            => $validated['notes'] ?? null,
    ]);

    return back()->with('success', 'Walk-in appointment booked and approved.');
}
}