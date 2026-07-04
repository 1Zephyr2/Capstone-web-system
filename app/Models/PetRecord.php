<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetRecord extends Model
{
    protected $fillable = [
        'pet_id',
        'recorded_by',
        'record_date',
        'diagnosis',
        'medications',
        'vet_notes',
    ];

    protected $casts = [
        'record_date' => 'date',
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}