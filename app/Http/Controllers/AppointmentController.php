<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id'           => ['required', 'exists:pets,id'],
            'appointment_date' => ['required', 'date', 'after:today'],
            'appointment_time' => ['required', 'in:' . implode(',', array_keys(Appointment::CLINIC_HOURS))],
            'service_id'       => ['required', 'exists:services,id'],
            'notes'            => ['nullable', 'string', 'max:500'],
        ]);

        $pet = Pet::where('id', $validated['pet_id'])->where('user_id', Auth::id())->firstOrFail();
        $appointmentDatetime = $validated['appointment_date'] . ' ' . $validated['appointment_time'] . ':00';

        if ($this->slotIsTaken($appointmentDatetime)) {
            return back()
                ->withErrors(['appointment_time' => 'That time slot was just booked by someone else. Please choose another.'])
                ->withInput();
        }

        Appointment::create([
            'user_id'          => Auth::id(),
            'pet_id'           => $pet->id,
            'appointment_date' => $appointmentDatetime,
            'service_id'       => $validated['service_id'],
            'status'           => Appointment::STATUS_PENDING,
            'notes'            => $validated['notes'] ?? null,
        ]);

        return redirect()->route('appointments.index')
                         ->with('success', 'Appointment request submitted! We will confirm your booking shortly.');
    }

    public function index()
    {
        $appointments = Appointment::with(['pet', 'service'])
            ->where('user_id', Auth::id())
            ->orderByDesc('appointment_date')
            ->paginate(10);

        return view('pets.appointments', compact('appointments'));
    }

   
    public function history()
    {
        $appointments = Appointment::with(['pet', 'service'])
            ->where('user_id', Auth::id())
            ->orderByDesc('appointment_date')
            ->paginate(15);

        return view('pets.appointment-history', compact('appointments'));
    }

    
    public function edit(Appointment $appointment)
    {
        abort_if($appointment->user_id !== Auth::id(), 403);
        abort_if(!$appointment->isPending() && !$appointment->isApproved(), 403);

        $pets        = Pet::where('user_id', Auth::id())->get();
        $services    = Service::groupedActive();
        $clinicHours = Appointment::CLINIC_HOURS;

        return view('pets.appointment-edit', compact('appointment', 'pets', 'services', 'clinicHours'));
    }

    
    public function update(Request $request, Appointment $appointment)
    {
        abort_if($appointment->user_id !== Auth::id(), 403);
        abort_if(!$appointment->isPending() && !$appointment->isApproved(), 403);

        $validated = $request->validate([
            'appointment_date' => ['required', 'date', 'after:today'],
            'appointment_time' => ['required', 'in:' . implode(',', array_keys(Appointment::CLINIC_HOURS))],
            'service_id'       => ['required', 'exists:services,id'],
            'notes'            => ['nullable', 'string', 'max:500'],
        ]);

        $wasApproved = $appointment->isApproved();
        $appointmentDatetime = $validated['appointment_date'] . ' ' . $validated['appointment_time'] . ':00';

        if ($this->slotIsTaken($appointmentDatetime, $appointment->id)) {
            return back()
                ->withErrors(['appointment_time' => 'That time slot is already booked. Please choose another.'])
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

    
    public function availability(Request $request)
    {
        $request->validate(['date' => ['required', 'date']]);

        $bookedTimes = Appointment::whereDate('appointment_date', $request->date)
            ->whereNotIn('status', [Appointment::STATUS_REJECTED, Appointment::STATUS_CANCELLED])
            ->get()
            ->map(fn($a) => $a->appointment_date->format('H:i'))
            ->values();

        $totalSlots = count(Appointment::CLINIC_HOURS);
        $isPastDate = Carbon::parse($request->date)->startOfDay()->lt(today());

        return response()->json([
            'booked'       => $bookedTimes,
            'fully_booked' => !$isPastDate && $bookedTimes->count() >= $totalSlots,
        ]);
    }

   
    public function monthAvailability(Request $request)
    {
        $request->validate([
            'year'  => ['required', 'integer'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
        ]);

        $start = Carbon::createFromDate($request->year, $request->month, 1)->startOfDay();
        $end   = $start->copy()->endOfMonth()->endOfDay();
        $totalSlots = count(Appointment::CLINIC_HOURS);

        $counts = Appointment::whereBetween('appointment_date', [$start, $end])
            ->whereNotIn('status', [Appointment::STATUS_REJECTED, Appointment::STATUS_CANCELLED])
            ->get()
            ->groupBy(fn($a) => $a->appointment_date->format('Y-m-d'))
            ->map->count();

        $fullyBookedDates = $counts->filter(fn($count) => $count >= $totalSlots)->keys()->values();

        return response()->json(['fully_booked_dates' => $fullyBookedDates]);
    }

    
    private function slotIsTaken(string $datetime, ?int $excludingAppointmentId = null): bool
    {
        return Appointment::where('appointment_date', $datetime)
            ->when($excludingAppointmentId, fn($q) => $q->where('id', '!=', $excludingAppointmentId))
            ->whereNotIn('status', [Appointment::STATUS_REJECTED, Appointment::STATUS_CANCELLED])
            ->exists();
    }
}