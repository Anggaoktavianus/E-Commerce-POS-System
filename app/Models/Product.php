<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * Resolve route binding with encoded ID
     */
    public function resolveRouteBinding($value, $field = null)
    {
        // Try to decode if it's an encoded ID
        $decodedId = decode_id($value);
        if ($decodedId !== null) {
            return $this->where($field ?: $this->getRouteKeyName(), $decodedId)->first();
        }
        
        // Fallback to default behavior
        return parent::resolveRouteBinding($value, $field);
    }

    protected $fillable = [
        'store_id',
        'name',
        'description',
        'price',
        'image',
        'category_id',
        'collection_id',
        'is_active',
        'stock',
        'stock_qty',
        'sku',
        'weight',
        'dimensions',
        'shelf_life_days',
        'requires_cold_chain',
        'shipping_type'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'requires_cold_chain' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function inventories()
    {
        return $this->hasMany(OutletProductInventory::class);
    }

    public function outlets()
    {
        return $this->belongsToMany(Outlet::class, 'outlet_product_inventories')
            ->withPivot(['stock', 'price_override', 'status'])
            ->withTimestamps();
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function getFormattedPriceAttribute()
    {
        return 'IDR ' . number_format($this->price, 0, ',', '.');
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? asset($this->image) : asset('fruitables/img/vegetable-item-3.png');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_qty', '>', 0);
    }

    // Accessor untuk kompatibilitas dengan kode yang menggunakan 'stock'
    public function getStockAttribute()
    {
        return $this->stock_qty ?? 0;
    }

    // Mutator untuk kompatibilitas dengan kode yang menggunakan 'stock'
    public function setStockAttribute($value)
    {
        $this->attributes['stock_qty'] = $value;
    }

    // Helper method untuk cek stok tersedia
    public function hasStock($quantity = 1)
    {
        return ($this->stock_qty ?? 0) >= $quantity;
    }

    // Helper method untuk mendapatkan sisa stok setelah pembelian
    public function getRemainingStock($quantity)
    {
        return max(0, ($this->stock_qty ?? 0) - $quantity);
    }

    public function scopeForStore($query, $storeId)
    {
        return $query->where('store_id', $storeId);
    }

    // Fresh product methods
    public function isFreshProduct()
    {
        return $this->shelf_life_days <= 7;
    }

    public function requiresInstantDelivery()
    {
        return $this->isFreshProduct() && $this->shelf_life_days <= 3;
    }

    public function getShippingRequirementsAttribute()
    {
        if ($this->requiresInstantDelivery()) {
            return [
                'max_delivery_days' => 1,
                'requires_instant' => true,
                'preferred_couriers' => ['gosend_instant', 'grab_express'],
                'warning' => 'Produk segar - membutuhkan pengiriman instan!'
            ];
        } elseif ($this->isFreshProduct()) {
            return [
                'max_delivery_days' => 2,
                'requires_instant' => false,
                'preferred_couriers' => ['gosend_instant', 'grab_express', 'sicepat_same_day', 'jne_reg'],
                'warning' => 'Produk segar - pilih pengiriman tercepat'
            ];
        } else {
            return [
                'max_delivery_days' => 7,
                'requires_instant' => false,
                'preferred_couriers' => ['jne', 'jnt', 'pos', 'tiki'],
                'warning' => null
            ];
        }
    }

    public function getFreshnessBadgeAttribute()
    {
        if ($this->requiresInstantDelivery()) {
            return '<span class="badge bg-danger">Extra Segar</span>';
        } elseif ($this->isFreshProduct()) {
            return '<span class="badge bg-warning">Segar</span>';
        } else {
            return '<span class="badge bg-success">Awet</span>';
        }
    }

    public function getShelfLifeTextAttribute()
    {
        $days = $this->shelf_life_days;
        if ($days <= 1) {
            return "1 hari";
        } elseif ($days <= 7) {
            return "{$days} hari";
        } elseif ($days <= 30) {
            $weeks = floor($days / 7);
            return "{$weeks} minggu";
        } else {
            $months = floor($days / 30);
            return "{$months} bulan";
        }
    }

    // Scope for fresh products
    public function scopeFresh($query)
    {
        return $query->where('shelf_life_days', '<=', 7);
    }

    // Scope for products requiring cold chain
    public function scopeColdChain($query)
    {
        return $query->where('requires_cold_chain', true);
    }

    // Scope by shipping type
    public function scopeByShippingType($query, $type)
    {
        return $query->where('shipping_type', $type);
    }
}
