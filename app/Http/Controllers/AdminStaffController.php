<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminStaffController extends Controller
{
    /**
     * Store a new staff account.
     * POST /admin/staff
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'staff',
        ]);

        return redirect()->route('admin.panel')
                         ->with('success', "Staff account for {$validated['name']} created successfully.")
                         ->with('panel_tab', 'staff');
    }

    /**
     * Delete a staff account.
     * DELETE /admin/staff/{user}
     */
    public function destroy(User $user)
    {
        // Safety: only allow deleting staff accounts
        abort_if($user->role !== 'staff', 403, 'Can only delete staff accounts.');

        // Prevent deleting yourself
        abort_if($user->id === auth()->id(), 403, 'You cannot delete your own account.');

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.panel')
                         ->with('success', "Staff account for {$name} has been removed.")
                         ->with('panel_tab', 'staff');
    }
}