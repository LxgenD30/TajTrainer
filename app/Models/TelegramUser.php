<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramUser extends Model
{
    protected $fillable = [
        'user_id',
        'telegram_id',
        'telegram_username',
        'telegram_first_name',
        'telegram_last_name',
        'notifications_enabled',
        'last_interaction',
    ];

    protected $casts = [
        'notifications_enabled' => 'boolean',
        'last_interaction' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
