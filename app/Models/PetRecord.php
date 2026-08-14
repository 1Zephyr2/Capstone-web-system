<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetRecord extends Model
{
    const TYPE_CHECKUP    = 'checkup';
    const TYPE_VACCINATION = 'vaccination';
    const TYPE_NOTE       = 'note';

    const TYPES = [
        self::TYPE_CHECKUP     => 'Checkup',
        self::TYPE_VACCINATION => 'Vaccination',
        self::TYPE_NOTE        => 'Note',
    ];

    protected $fillable = [
        'pet_id',
        'recorded_by',
        'record_type',
        'record_date',
        'diagnosis',
        'vaccine_name',
        'next_due_date',
        'medications',
        'vet_notes',
    ];

    protected $casts = [
        'record_date'   => 'date',
        'next_due_date' => 'date',
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->record_type] ?? ucfirst($this->record_type);
    }

    public function getRecorderRoleLabelAttribute(): string
    {
        return match($this->recorder?->role) {
            'admin' => 'Admin',
            'staff' => 'Staff',
            'owner' => 'Owner (self-logged)',
            default => 'Unknown',
        };
    }
}