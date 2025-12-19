<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'points',
        'description',
        'order_id',
        'reference_id',
        'reference_type',
        'expires_at'
    ];

    protected $casts = [
        'points' => 'integer',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public static function getUserBalance($userId): int
    {
        return static::where('user_id', $userId)
            ->where(function($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->sum(\DB::raw('CASE WHEN type = "earn" THEN points ELSE -points END'));
    }
}
