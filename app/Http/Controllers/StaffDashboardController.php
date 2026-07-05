<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\User;

class StaffDashboardController extends Controller
{
    public function index()
    {
        $threeMonthsAgo = now()->subMonths(3);

        // Inactive owners — no appointment in last 3 months
        $inactiveOwners = User::where('role', 'owner')
            ->whereDoesntHave('appointments', function($q) use ($threeMonthsAgo) {
                $q->where('appointment_date', '>=', $threeMonthsAgo);
            })
            ->count();

        $stats = [
            'pending_appointments'  => Appointment::where('status', Appointment::STATUS_PENDING)->count(),
            'todays_appointments'   => Appointment::whereDate('appointment_date', today())
                                                  ->where('status', Appointment::STATUS_APPROVED)
                                                  ->count(),
            'total_pets'            => Pet::count(),
            'total_owners'          => User::where('role', 'owner')->count(),
            'inactive_owners'       => $inactiveOwners,
        ];

        $upcomingAppointments = Appointment::with(['user', 'pet'])
            ->upcoming()
            ->orderBy('appointment_date')
            ->take(5)
            ->get();

        $pendingAppointments = Appointment::with(['user', 'pet'])
            ->pending()
            ->orderBy('created_at')
            ->take(5)
            ->get();

        return view('staff.dashboard', compact(
            'stats', 'upcomingAppointments', 'pendingAppointments'
        ));
    }
}