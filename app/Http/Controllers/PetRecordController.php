<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\PetRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetRecordController extends Controller
{
    /**
     * Store a new medical record (staff/admin only).
     * POST /staff/pets/{pet}/records
     */
    public function store(Request $request, Pet $pet)
    {
        $validated = $request->validate([
            'record_date' => ['required', 'date'],
            'diagnosis'   => ['required', 'string', 'max:255'],
            'medications' => ['nullable', 'string', 'max:1000'],
            'vet_notes'   => ['nullable', 'string', 'max:1000'],
        ]);

        PetRecord::create([
            'pet_id'      => $pet->id,
            'recorded_by' => Auth::id(),
            'record_date' => $validated['record_date'],
            'diagnosis'   => $validated['diagnosis'],
            'medications' => $validated['medications'] ?? null,
            'vet_notes'   => $validated['vet_notes'] ?? null,
        ]);

        return back()->with('success', "Medical record added for {$pet->name}.");
    }

    /**
     * Update a medical record (staff/admin only).
     * PATCH /staff/records/{record}
     */
    public function update(Request $request, PetRecord $record)
    {
        $validated = $request->validate([
            'record_date' => ['required', 'date'],
            'diagnosis'   => ['required', 'string', 'max:255'],
            'medications' => ['nullable', 'string', 'max:1000'],
            'vet_notes'   => ['nullable', 'string', 'max:1000'],
        ]);

        $record->update($validated);

        return back()->with('success', 'Medical record updated.');
    }

    /**
     * Delete a medical record (staff/admin only).
     * DELETE /staff/records/{record}
     */
    public function destroy(PetRecord $record)
    {
        $petName = $record->pet->name;
        $record->delete();

        return back()->with('success', "Record for {$petName} deleted.");
    }
}