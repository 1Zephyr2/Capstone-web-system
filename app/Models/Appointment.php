<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    const STATUS_PENDING   = 'pending';
    const STATUS_APPROVED  = 'approved';
    const STATUS_REJECTED  = 'rejected';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    const SERVICE_TYPES = [
        'grooming'          => 'Grooming',
        'veterinary'        => 'Veterinary Checkup',
        'vaccination'       => 'Vaccination',
        'boarding'          => 'Boarding',
    ];

    const CLINIC_HOURS = [
        '09:00' => '9:00 AM',
        '10:00' => '10:00 AM',
        '11:00' => '11:00 AM',
        '12:00' => '12:00 PM',
        '13:00' => '1:00 PM',
        '14:00' => '2:00 PM',
        '15:00' => '3:00 PM',
        '16:00' => '4:00 PM',
        '17:00' => '5:00 PM',
    ];

    protected $fillable = [
        'user_id',
        'pet_id',
        'appointment_date',
        'service_type',
        'status',
        'notes',
        'rejection_reason',
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    // ── Helpers ────────────────────────────────────────────────────

    public function getServiceLabelAttribute(): string
    {
        return self::SERVICE_TYPES[$this->service_type] ?? ucfirst($this->service_type);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING   => 'bg-yellow-100 text-yellow-800',
            self::STATUS_APPROVED  => 'bg-green-100 text-green-800',
            self::STATUS_REJECTED  => 'bg-red-100 text-red-800',
            self::STATUS_COMPLETED => 'bg-blue-100 text-blue-800',
            self::STATUS_CANCELLED => 'bg-gray-100 text-gray-600',
            default                => 'bg-gray-100 text-gray-600',
        };
    }

    public function isPending(): bool    { return $this->status === self::STATUS_PENDING; }
    public function isApproved(): bool   { return $this->status === self::STATUS_APPROVED; }
    public function isRejected(): bool   { return $this->status === self::STATUS_REJECTED; }
    public function isCompleted(): bool  { return $this->status === self::STATUS_COMPLETED; }
    public function isCancelled(): bool  { return $this->status === self::STATUS_CANCELLED; }

    // ── Scopes ─────────────────────────────────────────────────────

    public function scopePending($query)    { return $query->where('status', self::STATUS_PENDING); }
    public function scopeApproved($query)   { return $query->where('status', self::STATUS_APPROVED); }
    public function scopeUpcoming($query)
    {
        return $query->where('status', self::STATUS_APPROVED)
                     ->where('appointment_date', '>=', now());
    }
}