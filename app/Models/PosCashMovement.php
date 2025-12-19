<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosCashMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'shift_id',
        'outlet_id',
        'user_id',
        'type',
        'amount',
        'reason',
        'reference_number',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function shift()
    {
        return $this->belongsTo(PosShift::class, 'shift_id');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
