<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    /**
     * Show the booking form.
     * GET /request-appointment
     */
    public function create()
    {
        $pets = Pet::where('user_id', Auth::id())->get();

        return view('pets.booking-request', [
            'pets'         => $pets,
            'serviceTypes' => Appointment::SERVICE_TYPES,
            'clinicHours'  => Appointment::CLINIC_HOURS,
        ]);
    }

    /**
     * Store a new appointment request.
     * POST /request-appointment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id'           => ['required', 'exists:pets,id'],
            'appointment_date' => ['required', 'date', 'after:today'],
            'appointment_time' => ['required', 'in:' . implode(',', array_keys(Appointment::CLINIC_HOURS))],
            'service_type'     => ['required', 'in:' . implode(',', array_keys(Appointment::SERVICE_TYPES))],
            'notes'            => ['nullable', 'string', 'max:500'],
        ]);

        // Ensure the pet belongs to the authenticated user
        $pet = Pet::where('id', $validated['pet_id'])
                  ->where('user_id', Auth::id())
                  ->firstOrFail();

        // Combine date and time
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

    /**
     * List the authenticated user's appointments.
     * GET /appointments
     */
    public function index()
    {
        $appointments = Appointment::with('pet')
            ->where('user_id', Auth::id())
            ->orderByDesc('appointment_date')
            ->paginate(10);

        return view('pets.appointments', compact('appointments'));
    }

    /**
     * Cancel a pending appointment (owner only).
     * PATCH /appointments/{appointment}/cancel
     */
    public function cancel(Appointment $appointment)
    {
        // Ensure only the owner can cancel their own appointment
        abort_if($appointment->user_id !== Auth::id(), 403);

        // Only pending or approved appointments can be cancelled
        if (!$appointment->isPending() && !$appointment->isApproved()) {
            return back()->with('error', 'This appointment cannot be cancelled.');
        }

        $appointment->update(['status' => Appointment::STATUS_CANCELLED]);

        return back()->with('success', 'Appointment cancelled successfully.');
    }
}