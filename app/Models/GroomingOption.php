<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroomingOption extends Model
{
    protected $fillable = ['type', 'name', 'description', 'is_active', 'image'];

    protected $casts = ['is_active' => 'boolean'];

    public function scopeStyles($query) { return $query->where('type', 'style')->where('is_active', true); }
    public function scopeAddons($query) { return $query->where('type', 'addon')->where('is_active', true); }
    public function scopeActive($query) { return $query->where('is_active', true); }

    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return null;
    }
}