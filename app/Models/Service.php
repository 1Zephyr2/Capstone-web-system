<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    const CATEGORIES = ['Grooming Packages', 'Add-ons', 'Ala Carte'];

    const SIZES = ['XS', 'S', 'M', 'L', 'XL', 'G'];

    protected $fillable = ['name', 'category', 'prices', 'is_active', 'is_archived'];

    protected $casts = ['is_active' => 'boolean', 'is_archived' => 'boolean', 'prices' => 'array'];

    /**
     * Price for a given pet size (XS/S/M/L/XL/G). Flat-rate services (stored as
     * {"flat": 500}) ignore the size and always return the flat amount.
     */
    public function priceForSize(?string $size): ?float
    {
        if (!$this->prices) {
            return null;
        }
        if (isset($this->prices['flat'])) {
            return (float) $this->prices['flat'];
        }
        return isset($this->prices[$size]) ? (float) $this->prices[$size] : null;
    }

    public function isFlatRate(): bool
    {
        return isset($this->prices['flat']);
    }

    /**
     * A short price summary for contexts with no specific pet size yet
     * (e.g. the admin services list, or the public services page).
     */
    public function getPriceRangeAttribute(): string
    {
        if (!$this->prices) {
            return 'Price on request';
        }
        if ($this->isFlatRate()) {
            return '₱' . number_format((float) $this->prices['flat'], 2);
        }
        $values = array_map('floatval', array_values($this->prices));
        $min = min($values);
        $max = max($values);
        return $min === $max
            ? '₱' . number_format($min, 2)
            : '₱' . number_format($min, 2) . ' – ₱' . number_format($max, 2);
    }

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