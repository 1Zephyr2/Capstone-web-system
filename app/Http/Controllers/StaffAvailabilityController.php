<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class StaffAvailabilityController extends Controller
{
    /** Staff sets their OWN status. PATCH /staff/availability */
    public function updateSelf(Request $request)
    {
        $this->apply($request, $request->user());
        return back()->with('success', 'Your availability was updated.');
    }

    /** Admin sets any staff member's status. PATCH /admin/staff/{user}/availability */
    public function updateStaff(Request $request, User $user)
    {
        abort_unless(strtolower($user->role) === 'staff', 404);
        $this->apply($request, $user);
        return back()->with('success', $user->name . ' is now marked ' . $user->availability . '.')->with('panel_tab', 'staff');
    }

    private function apply(Request $request, User $user): void
    {
        $data = $request->validate([
            'availability' => ['required', 'in:available,unavailable'],
            'note'         => ['nullable', 'string', 'max:120'],
        ]);

        $user->forceFill([
            'availability'            => $data['availability'],
            'availability_note'       => $data['availability'] === 'unavailable' ? ($data['note'] ?? null) : null,
            'availability_updated_at' => now(),
        ])->save();
    }
}
