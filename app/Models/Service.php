<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    const CATEGORIES = ['Grooming Packages', 'Grooming Services'];

    protected $fillable = ['name', 'category', 'is_active', 'is_archived'];

    protected $casts = ['is_active' => 'boolean', 'is_archived' => 'boolean'];

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('is_archived', false);
    }

    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }

    public function scopeNotArchived($query)
    {
        return $query->where('is_archived', false);
    }

    /**
     * Active services grouped by category, in CATEGORIES order — for dropdowns.
     */
    public static function groupedActive()
    {
        $services = self::active()->orderBy('name')->get();
        return collect(self::CATEGORIES)->mapWithKeys(fn($cat) => [
            $cat => $services->where('category', $cat)->values(),
        ])->filter(fn($group) => $group->isNotEmpty());
    }
}