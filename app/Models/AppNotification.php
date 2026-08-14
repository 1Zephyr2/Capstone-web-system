<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppNotification extends Model
{
    protected $table = 'app_notifications';

    protected $fillable = ['user_id', 'type', 'title', 'message', 'link', 'read_at'];

    protected $casts = ['read_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    /**
     * Create a notification for a user. Central place so every trigger point
     * (appointment approved/rejected/completed, new request, etc.) stays consistent.
     */
    public static function notify(int $userId, string $type, string $title, string $message, ?string $link = null): self
    {
        return self::create([
            'user_id' => $userId,
            'type'    => $type,
            'title'   => $title,
            'message' => $message,
            'link'    => $link,
        ]);
    }
}
