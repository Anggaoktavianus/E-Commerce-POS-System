<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_number',
        'outlet_id',
        'shift_id',
        'user_id',
        'customer_id',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'payment_method',
        'payment_details',
        'cash_received',
        'change_amount',
        'status',
        'cancelled_at',
        'cancelled_by',
        'cancel_reason',
        'receipt_printed',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'cash_received' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'payment_details' => 'array',
        'cancelled_at' => 'datetime',
        'receipt_printed' => 'boolean',
    ];

    // Relationships
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function shift()
    {
        return $this->belongsTo(PosShift::class, 'shift_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function items()
    {
        return $this->hasMany(PosTransactionItem::class);
    }

    public function payments()
    {
        return $this->hasMany(PosPayment::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // Methods
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function canCancel()
    {
        // Bisa di-cancel jika:
        // 1. Status masih completed
        // 2. Shift masih open
        // 3. Belum lebih dari 24 jam
        return $this->isCompleted() 
            && $this->shift->isOpen()
            && $this->created_at->diffInHours(now()) < 24;
    }

    public static function generateTransactionNumber($outletId)
    {
        $outlet = Outlet::findOrFail($outletId);
        $date = now()->format('Ymd');
        $count = self::where('outlet_id', $outletId)
            ->whereDate('created_at', today())
            ->count() + 1;

        return "POS-{$outlet->code}-{$date}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
