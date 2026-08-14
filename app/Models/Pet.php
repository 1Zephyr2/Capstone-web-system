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
        'size',
        'age',
        'special_notes',
        'photo',
        'vaccination_record',
        'medical_conditions',
        'grooming_triggers',
    ];

    // Matches Bark Pack's actual grooming price tiers (extra-small to giant)
    const SIZES = ['XS', 'S', 'M', 'L', 'XL', 'G'];

    const SIZE_LABELS = [
        'XS' => 'Extra Small',
        'S'  => 'Small',
        'M'  => 'Medium',
        'L'  => 'Large',
        'XL' => 'Extra Large',
        'G'  => 'Giant',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function records(): HasMany
    {
        return $this->hasMany(PetRecord::class)->orderByDesc('record_date');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return null;
    }
}