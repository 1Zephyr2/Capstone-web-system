<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AdminStaffController extends Controller
{
    /**
     * Store a new staff account.
     * POST /admin/staff
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // On failure go straight back to the Staff tab with the form reopened,
        // so the admin can see what to fix instead of a silent redirect.
        if ($validator->fails()) {
            return redirect()->route('admin.panel')
                             ->withErrors($validator)
                             ->withInput($request->except('password', 'password_confirmation'))
                             ->with('panel_tab', 'staff')
                             ->with('open_add_staff', true);
        }

        $validated = $validator->validated();

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