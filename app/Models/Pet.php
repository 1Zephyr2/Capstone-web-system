<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Pet extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'breed',
        'age',
        'special_notes',
        'photo',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get the full public URL for the pet's photo,
     * or null if no photo is set.
     */
    public function getPhotoUrlAttribute(): ?string
{
    if ($this->photo) {
        return asset('storage/' . $this->photo);
    }
    return null;
}
}