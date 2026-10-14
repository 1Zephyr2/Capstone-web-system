<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(["name", "email", "phone", "password", "role", "availability", "availability_note", "availability_updated_at"])]
#[Hidden(["password", "remember_token"])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function isAvailable(): bool
    {
        return ($this->availability ?? 'available') === 'available';
    }

    protected function casts(): array
    {
        return [
            "email_verified_at" => "datetime",
            "password"          => "hashed",
            "availability_updated_at" => "datetime",
        ];
    }
}