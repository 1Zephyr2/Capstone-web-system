<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PetController extends Controller
{
    /**
     * Store a new pet for the authenticated user.
     * POST /pets
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                => ['required', 'string', 'max:100'],
            'type'                => ['required', 'in:dog,cat,other'],
            'breed'               => ['required', 'string', 'max:100'],
            'size'                => ['required', 'in:' . implode(',', \App\Models\Pet::SIZES)],
            'age'                 => ['required', 'integer', 'min:0', 'max:100'],
            'special_notes'       => ['nullable', 'string', 'max:500'],
            'vaccination_record'  => ['required', 'string', 'max:1000'],
            'medical_conditions'  => ['nullable', 'string', 'max:1000'],
            'grooming_triggers'   => ['nullable', 'string', 'max:1000'],
            'photo'               => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $photoPath = null;

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $photoPath = $request->file('photo')->store('pets', 'public');
        }

        Pet::create([
            'user_id'             => Auth::id(),
            'name'                => $validated['name'],
            'type'                => $validated['type'],
            'breed'               => $validated['breed'],
            'size'                => $validated['size'],
            'age'                 => $validated['age'],
            'special_notes'       => $validated['special_notes'] ?? null,
            'vaccination_record'  => $validated['vaccination_record'] ?? null,
            'medical_conditions'  => $validated['medical_conditions'] ?? null,
            'grooming_triggers'   => $validated['grooming_triggers'] ?? null,
            'photo'               => $photoPath,
        ]);

        return redirect()->route('dashboard')
                         ->with('success', 'Pet added successfully!');
    }

    /**
     * Update an existing pet.
     * PATCH /pets/{pet}
     */
    public function update(Request $request, Pet $pet)
    {
        // Only the owner can update
        abort_if($pet->user_id !== Auth::id(), 403);

        $validated = $request->validate([
            'name'                => ['required', 'string', 'max:100'],
            'type'                => ['required', 'in:dog,cat,other'],
            'breed'               => ['required', 'string', 'max:100'],
            'size'                => ['required', 'in:' . implode(',', \App\Models\Pet::SIZES)],
            'age'                 => ['required', 'integer', 'min:0', 'max:100'],
            'special_notes'       => ['nullable', 'string', 'max:500'],
            'vaccination_record'  => ['required', 'string', 'max:1000'],
            'medical_conditions'  => ['nullable', 'string', 'max:1000'],
            'grooming_triggers'   => ['nullable', 'string', 'max:1000'],
            'photo'               => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            // Delete old photo if it exists
            if ($pet->photo) {
                Storage::disk('public')->delete($pet->photo);
            }
            $validated['photo'] = $request->file('photo')->store('pets', 'public');
        } else {
            // Keep existing photo
            unset($validated['photo']);
        }

        $pet->update($validated);

        return redirect()->route('dashboard')
                         ->with('success', 'Pet updated successfully!');
    }

    /**
     * Delete a pet.
     * DELETE /pets/{pet}
     */
    public function destroy(Pet $pet)
    {
        abort_if($pet->user_id !== Auth::id(), 403);

        if ($pet->photo) {
            Storage::disk('public')->delete($pet->photo);
        }

        $pet->delete();

        return redirect()->route('dashboard')
                         ->with('success', 'Pet removed successfully.');
    }
}