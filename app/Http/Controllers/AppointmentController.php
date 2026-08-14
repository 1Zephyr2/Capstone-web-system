<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class AppointmentController extends Controller
{
    public function create()
    {
        $pets = Pet::where('user_id', Auth::id())->get();
        return view('pets.booking-request', [
            'pets'        => $pets,
            'services'    => Service::groupedActive(),
            'clinicHours' => Appointment::CLINIC_HOURS,
        ]);
    }

    /**
     * Create one or more appointments in a single submission (one pet, or several
     * pets sharing the same time slot, or several pets each with their own slot).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'appointment_date' => ['required', 'date', 'after:today', function ($attribute, $value, $fail) {
                $day = \Carbon\Carbon::parse($value)->dayOfWeek;
                if (in_array($day, [\Carbon\Carbon::SATURDAY, \Carbon\Carbon::SUNDAY])) {
                    $fail('We are closed on weekends. Please choose a weekday.');
                }
            }],
            'time_mode'        => ['required', 'in:same,separate'],
            'appointment_time' => ['required_if:time_mode,same', 'nullable', 'in:' . implode(',', array_keys(Appointment::CLINIC_HOURS))],
            'pets'             => ['required', 'array', 'min:1'],
            'pets.*'           => ['required', 'exists:pets,id'],
            'services'         => ['required', 'array'],
            'services.*'       => ['required', 'exists:services,id'],
            'times'            => ['required_if:time_mode,separate', 'array'],
            'times.*'          => ['nullable', 'in:' . implode(',', array_keys(Appointment::CLINIC_HOURS))],
            'notes'            => ['nullable', 'string', 'max:500'],
        ]);

        // Make sure every selected pet actually belongs to this owner
        $ownedCount = Pet::where('user_id', Auth::id())->whereIn('id', $validated['pets'])->count();
        abort_if($ownedCount !== count($validated['pets']), 403, 'Invalid pet selection.');

        // Build the list of individual bookings to create
        $bookings = [];
        foreach ($validated['pets'] as $petId) {
            $time = $validated['time_mode'] === 'same'
                ? $validated['appointment_time']
                : ($validated['times'][$petId] ?? null);

            if (!$time) {
                return back()->withErrors(['times' => 'Please choose a time for each pet.'])->withInput();
            }

            $serviceId = $validated['services'][$petId] ?? null;
            if (!$serviceId) {
                return back()->withErrors(['services' => 'Please choose a service for each pet.'])->withInput();
            }

            $bookings[] = [
                'pet_id'     => $petId,
                'datetime'   => $validated['appointment_date'] . ' ' . $time . ':00',
                'service_id' => $serviceId,
            ];
        }

        // Check slot capacity, accounting for how many pets in THIS submission want the same slot
        $neededPerSlot = collect($bookings)->groupBy('datetime')->map->count();
        foreach ($neededPerSlot as $datetime => $needed) {
            $existing = Appointment::countInSlot($datetime);
            if ($existing + $needed > Appointment::MAX_PER_SLOT) {
                return back()
                    ->withErrors(['appointment_time' => 'The ' . Carbon::parse($datetime)->format('g:i A') . ' slot doesn\'t have room for all selected pets. Please choose another time.'])
                    ->withInput();
            }
        }

        $groupId = (string) Str::uuid();
        $petNames = [];

        foreach ($bookings as $booking) {
            Appointment::create([
                'user_id'          => Auth::id(),
                'pet_id'           => $booking['pet_id'],
                'appointment_date' => $booking['datetime'],
                'service_id'       => $booking['service_id'],
                'status'           => Appointment::STATUS_PENDING,
                'notes'            => $validated['notes'] ?? null,
                'booking_group_id' => $groupId,
            ]);
            $petNames[] = \App\Models\Pet::find($booking['pet_id'])?->name;
        }

        // Notify all staff/admin so nobody misses a new request
        $staffAndAdmin = \App\Models\User::whereIn('role', ['staff', 'admin'])->get(['id', 'role']);
        $petList = implode(', ', array_filter($petNames));
        foreach ($staffAndAdmin as $recipient) {
            \App\Models\AppNotification::notify(
                $recipient->id,
                'new_request',
                'New Appointment Request',
                Auth::user()->name . " requested an appointment for {$petList}.",
                route($recipient->role . '.appointments')
            );
        }

        $count = count($bookings);

        return redirect()->route('appointments.index')->with('success', $count > 1
            ? "Appointment requests submitted for {$count} pets! We will confirm your bookings shortly."
            : 'Appointment request submitted! We will confirm your booking shortly.');
    }

    public function index(Request $request)
    {
        // Status priority so the list reads naturally: needs-attention first, done/cancelled last
        // (CASE WHEN instead of MySQL's FIELD() — this project runs on SQLite)
        $statusOrder = "CASE status
            WHEN 'pending' THEN 1
            WHEN 'approved' THEN 2
            WHEN 'completed' THEN 3
            WHEN 'rejected' THEN 4
            WHEN 'cancelled' THEN 5
            ELSE 6 END";

        $appointments = Appointment::with(['pet', 'service'])
            ->where('user_id', Auth::id())
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->orderByRaw($statusOrder)
            ->orderByDesc('appointment_date')
            ->paginate(10)
            ->withQueryString();

        return view('pets.appointments', compact('appointments'));
    }

    /**
     * Full appointment history — all statuses, paginated.
     * GET /appointments/history
     */
    public function history()
    {
        $appointments = Appointment::with(['pet', 'service'])
            ->where('user_id', Auth::id())
            ->orderByDesc('appointment_date')
            ->paginate(15);

        return view('pets.appointment-history', compact('appointments'));
    }

    /**
     * Show edit form for a pending or approved appointment.
     * GET /appointments/{appointment}/edit-form
     */
    public function edit(Appointment $appointment)
    {
        abort_if($appointment->user_id !== Auth::id(), 403);
        abort_if(!$appointment->isPending() && !$appointment->isApproved(), 403);

        $pets        = Pet::where('user_id', Auth::id())->get();
        $services    = Service::groupedActive();
        $clinicHours = Appointment::CLINIC_HOURS;

        return view('pets.appointment-edit', compact('appointment', 'pets', 'services', 'clinicHours'));
    }

    /**
     * Update a pending or approved appointment (single-pet edit; unaffected by
     * multi-pet booking since each appointment row is edited independently).
     * PATCH /appointments/{appointment}
     */
    public function update(Request $request, Appointment $appointment)
    {
        abort_if($appointment->user_id !== Auth::id(), 403);
        abort_if(!$appointment->isPending() && !$appointment->isApproved(), 403);

        $validated = $request->validate([
            'appointment_date' => ['required', 'date', 'after:today', function ($attribute, $value, $fail) {
                $day = \Carbon\Carbon::parse($value)->dayOfWeek;
                if (in_array($day, [\Carbon\Carbon::SATURDAY, \Carbon\Carbon::SUNDAY])) {
                    $fail('We are closed on weekends. Please choose a weekday.');
                }
            }],
            'appointment_time' => ['required', 'in:' . implode(',', array_keys(Appointment::CLINIC_HOURS))],
            'service_id'       => ['required', 'exists:services,id'],
            'notes'            => ['nullable', 'string', 'max:500'],
        ]);

        $wasApproved = $appointment->isApproved();
        $appointmentDatetime = $validated['appointment_date'] . ' ' . $validated['appointment_time'] . ':00';

        if (!Appointment::slotHasCapacity($appointmentDatetime, $appointment->id)) {
            return back()
                ->withErrors(['appointment_time' => 'That time slot is fully booked. Please choose another.'])
                ->withInput();
        }

        $appointment->update([
            'appointment_date' => $appointmentDatetime,
            'service_id'       => $validated['service_id'],
            'notes'            => $validated['notes'] ?? null,
            'status'           => $wasApproved ? Appointment::STATUS_PENDING : $appointment->status,
        ]);

        $msg = 'Appointment updated successfully.';
        if ($wasApproved) {
            $msg .= ' It has been reset to pending for staff review.';
        }

        return redirect()->route('appointments.index')->with('success', $msg);
    }

    public function cancel(Appointment $appointment)
    {
        abort_if($appointment->user_id !== Auth::id(), 403);

        if (!$appointment->isPending() && !$appointment->isApproved()) {
            return back()->with('error', 'This appointment cannot be cancelled.');
        }

        $appointment->update(['status' => Appointment::STATUS_CANCELLED]);
        return back()->with('success', 'Appointment cancelled successfully.');
    }

    /**
     * Per-slot booked counts for a given date, so the calendar UI can disable
     * only the slots that are truly at capacity (not just "any" booking).
     * GET /appointments/availability?date=YYYY-MM-DD
     */
    public function availability(Request $request)
    {
        $request->validate(['date' => ['required', 'date']]);

        $counts = Appointment::whereDate('appointment_date', $request->date)
            ->whereNotIn('status', [Appointment::STATUS_REJECTED, Appointment::STATUS_CANCELLED])
            ->get()
            ->groupBy(fn($a) => $a->appointment_date->format('H:i'))
            ->map->count();

        $full = $counts->filter(fn($count) => $count >= Appointment::MAX_PER_SLOT)->keys()->values();

        $totalSlots = count(Appointment::CLINIC_HOURS);
        $isPastDate = Carbon::parse($request->date)->startOfDay()->lt(today());

        return response()->json([
            'full'         => $full,
            'counts'       => $counts,
            'max_per_slot' => Appointment::MAX_PER_SLOT,
            'fully_booked' => !$isPastDate && $full->count() >= $totalSlots,
        ]);
    }

    /**
     * Which dates in a given month are fully booked (every slot at capacity).
     * GET /appointments/availability-month?year=2026&month=7
     */
    public function monthAvailability(Request $request)
    {
        $request->validate([
            'year'  => ['required', 'integer'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
        ]);

        $start = Carbon::createFromDate($request->year, $request->month, 1)->startOfDay();
        $end   = $start->copy()->endOfMonth()->endOfDay();
        $totalSlots = count(Appointment::CLINIC_HOURS);

        $appointments = Appointment::whereBetween('appointment_date', [$start, $end])
            ->whereNotIn('status', [Appointment::STATUS_REJECTED, Appointment::STATUS_CANCELLED])
            ->get();

        $fullyBookedDates = $appointments
            ->groupBy(fn($a) => $a->appointment_date->format('Y-m-d'))
            ->filter(function ($dayAppointments) use ($totalSlots) {
                $fullSlotCount = $dayAppointments
                    ->groupBy(fn($a) => $a->appointment_date->format('H:i'))
                    ->filter(fn($slotAppointments) => $slotAppointments->count() >= Appointment::MAX_PER_SLOT)
                    ->count();
                return $fullSlotCount >= $totalSlots;
            })
            ->keys()
            ->values();

        return response()->json(['fully_booked_dates' => $fullyBookedDates]);
    }
}
