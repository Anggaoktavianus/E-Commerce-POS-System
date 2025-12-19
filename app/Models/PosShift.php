<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id',
        'user_id',
        'shift_date',
        'shift_number',
        'opening_balance',
        'closing_balance',
        'expected_cash',
        'actual_cash',
        'variance',
        'total_sales',
        'total_transactions',
        'opened_at',
        'closed_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'expected_cash' => 'decimal:2',
        'actual_cash' => 'decimal:2',
        'variance' => 'decimal:2',
        'total_sales' => 'decimal:2',
        'shift_date' => 'date',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    // Relationships
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(PosTransaction::class, 'shift_id');
    }

    public function cashMovements()
    {
        return $this->hasMany(PosCashMovement::class, 'shift_id');
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeForOutlet($query, $outletId)
    {
        return $query->where('outlet_id', $outletId);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Methods
    public function isOpen()
    {
        return $this->status === 'open';
    }

    public function canClose()
    {
        return $this->isOpen() && $this->transactions()->count() > 0;
    }

    public function calculateExpectedCash()
    {
        $cashSales = $this->transactions()
            ->where('payment_method', 'cash')
            ->where('status', 'completed')
            ->sum('total_amount');

        $deposits = $this->cashMovements()
            ->where('type', 'deposit')
            ->sum('amount');

        $withdrawals = $this->cashMovements()
            ->where('type', 'withdrawal')
            ->sum('amount');

        $transfers = $this->cashMovements()
            ->where('type', 'transfer')
            ->sum('amount');

        return $this->opening_balance + $cashSales + $deposits - $withdrawals - $transfers;
    }
}
