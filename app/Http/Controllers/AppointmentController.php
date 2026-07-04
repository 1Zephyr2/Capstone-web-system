<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function create()
    {
        $pets = Pet::where('user_id', Auth::id())->get();
        return view('pets.booking-request', [
            'pets'         => $pets,
            'serviceTypes' => Appointment::SERVICE_TYPES,
            'clinicHours'  => Appointment::CLINIC_HOURS,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id'           => ['required', 'exists:pets,id'],
            'appointment_date' => ['required', 'date', 'after:today'],
            'appointment_time' => ['required', 'in:' . implode(',', array_keys(Appointment::CLINIC_HOURS))],
            'service_type'     => ['required', 'in:' . implode(',', array_keys(Appointment::SERVICE_TYPES))],
            'notes'            => ['nullable', 'string', 'max:500'],
        ]);

        $pet = Pet::where('id', $validated['pet_id'])->where('user_id', Auth::id())->firstOrFail();
        $appointmentDatetime = $validated['appointment_date'] . ' ' . $validated['appointment_time'] . ':00';

        Appointment::create([
            'user_id'          => Auth::id(),
            'pet_id'           => $pet->id,
            'appointment_date' => $appointmentDatetime,
            'service_type'     => $validated['service_type'],
            'status'           => Appointment::STATUS_PENDING,
            'notes'            => $validated['notes'] ?? null,
        ]);

        return redirect()->route('appointments.index')
                         ->with('success', 'Appointment request submitted! We will confirm your booking shortly.');
    }

    public function index()
    {
        $appointments = Appointment::with('pet')
            ->where('user_id', Auth::id())
            ->orderByDesc('appointment_date')
            ->paginate(10);

        return view('pets.appointments', compact('appointments'));
    }

    /**
     * Full appointment history — all statuses, paginated.
     * GET /appointments/history
     */
    public function history()
    {
        $appointments = Appointment::with('pet')
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

        $pets         = Pet::where('user_id', Auth::id())->get();
        $serviceTypes = Appointment::SERVICE_TYPES;
        $clinicHours  = Appointment::CLINIC_HOURS;

        return view('pets.appointment-edit', compact('appointment', 'pets', 'serviceTypes', 'clinicHours'));
    }

    /**
     * Update a pending or approved appointment.
     * PATCH /appointments/{appointment}
     */
    public function update(Request $request, Appointment $appointment)
    {
        abort_if($appointment->user_id !== Auth::id(), 403);
        abort_if(!$appointment->isPending() && !$appointment->isApproved(), 403);

        $validated = $request->validate([
            'appointment_date' => ['required', 'date', 'after:today'],
            'appointment_time' => ['required', 'in:' . implode(',', array_keys(Appointment::CLINIC_HOURS))],
            'service_type'     => ['required', 'in:' . implode(',', array_keys(Appointment::SERVICE_TYPES))],
            'notes'            => ['nullable', 'string', 'max:500'],
        ]);

        $wasApproved = $appointment->isApproved();
        $appointmentDatetime = $validated['appointment_date'] . ' ' . $validated['appointment_time'] . ':00';

        $appointment->update([
            'appointment_date' => $appointmentDatetime,
            'service_type'     => $validated['service_type'],
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
}