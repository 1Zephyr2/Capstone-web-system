<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\PetRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetRecordController extends Controller
{
    /**
     * Store a new medical record. Staff/admin can log a record for any pet;
     * an owner can only log a record for their own pet (self-reported history,
     * e.g. home vaccination records, notes for the vet to see later).
     * POST /pets/{pet}/records
     */
    public function store(Request $request, Pet $pet)
    {
        $user = Auth::user();
        abort_if($user->role === 'owner' && $pet->user_id !== $user->id, 403);

        $validated = $request->validate([
            'record_type'   => ['required', 'in:' . implode(',', array_keys(PetRecord::TYPES))],
            'record_date'   => ['required', 'date'],
            'diagnosis'     => ['required', 'string', 'max:255'],
            'vaccine_name'  => ['nullable', 'required_if:record_type,vaccination', 'string', 'max:150'],
            'next_due_date' => ['nullable', 'date'],
            'medications'   => ['nullable', 'string', 'max:1000'],
            'vet_notes'     => ['nullable', 'string', 'max:1000'],
        ]);

        PetRecord::create([
            'pet_id'        => $pet->id,
            'recorded_by'   => Auth::id(),
            'record_type'   => $validated['record_type'],
            'record_date'   => $validated['record_date'],
            'diagnosis'     => $validated['diagnosis'],
            'vaccine_name'  => $validated['vaccine_name'] ?? null,
            'next_due_date' => $validated['next_due_date'] ?? null,
            'medications'   => $validated['medications'] ?? null,
            'vet_notes'     => $validated['vet_notes'] ?? null,
        ]);

        return back()->with('success', "Medical record added for {$pet->name}.");
    }

    /**
     * Update a medical record. Owners may only edit records they logged themselves;
     * staff/admin can edit any record.
     * PATCH /records/{record}
     */
    public function update(Request $request, PetRecord $record)
    {
        $user = Auth::user();
        abort_if($user->role === 'owner' && $record->recorded_by !== $user->id, 403);

        $validated = $request->validate([
            'record_type'   => ['required', 'in:' . implode(',', array_keys(PetRecord::TYPES))],
            'record_date'   => ['required', 'date'],
            'diagnosis'     => ['required', 'string', 'max:255'],
            'vaccine_name'  => ['nullable', 'required_if:record_type,vaccination', 'string', 'max:150'],
            'next_due_date' => ['nullable', 'date'],
            'medications'   => ['nullable', 'string', 'max:1000'],
            'vet_notes'     => ['nullable', 'string', 'max:1000'],
        ]);

        $record->update($validated);

        return back()->with('success', 'Medical record updated.');
    }

    /**
     * Delete a medical record. Owners may only delete records they logged themselves.
     * DELETE /records/{record}
     */
    public function destroy(PetRecord $record)
    {
        $user = Auth::user();
        abort_if($user->role === 'owner' && $record->recorded_by !== $user->id, 403);

        $petName = $record->pet->name;
        $record->delete();

        return back()->with('success', "Record for {$petName} deleted.");
    }
}