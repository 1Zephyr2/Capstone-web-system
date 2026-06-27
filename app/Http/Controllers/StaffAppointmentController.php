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
        $query = Appointment::with(['user', 'pet'])
            ->orderBy('appointment_date', 'asc');

        // Optional filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->date);
        }

        if ($request->filled('service')) {
            $query->where('service_type', $request->service);
        }

        $appointments  = $query->paginate(15)->withQueryString();
        $serviceTypes  = Appointment::SERVICE_TYPES;
        $statusOptions = [
            Appointment::STATUS_PENDING,
            Appointment::STATUS_APPROVED,
            Appointment::STATUS_REJECTED,
            Appointment::STATUS_COMPLETED,
            Appointment::STATUS_CANCELLED,
        ];

        // Stats for dashboard summary cards
        $stats = [
            'pending'   => Appointment::where('status', Appointment::STATUS_PENDING)->count(),
            'approved'  => Appointment::where('status', Appointment::STATUS_APPROVED)->count(),
            'today'     => Appointment::whereDate('appointment_date', today())
                                      ->where('status', Appointment::STATUS_APPROVED)
                                      ->count(),
        ];

        return view('staff.appointments', compact(
            'appointments', 'serviceTypes', 'statusOptions', 'stats'
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
}