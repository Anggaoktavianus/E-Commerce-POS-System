<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get or create cart for user or session
     */
    public static function getOrCreateCart($userId = null, $sessionId = null)
    {
        if ($userId) {
            $cart = static::where('user_id', $userId)->first();
            if (!$cart) {
                $cart = static::create([
                    'user_id' => $userId,
                    'session_id' => null,
                ]);
            }
            return $cart;
        } elseif ($sessionId) {
            $cart = static::where('session_id', $sessionId)->whereNull('user_id')->first();
            if (!$cart) {
                $cart = static::create([
                    'user_id' => null,
                    'session_id' => $sessionId,
                ]);
            }
            return $cart;
        }
        
        return null;
    }

    /**
     * Merge session cart with user cart
     */
    public function mergeWithSessionCart($sessionCart)
    {
        foreach ($sessionCart as $productId => $item) {
            $existingItem = $this->items()->where('product_id', $productId)->first();
            
            if ($existingItem) {
                // Update quantity if needed
                $newQty = ($item['qty'] ?? $item['quantity'] ?? 1) + $existingItem->quantity;
                $existingItem->update([
                    'quantity' => $newQty,
                    'price' => $item['price'] ?? $existingItem->price,
                ]);
            } else {
                // Add new item
                $this->items()->create([
                    'product_id' => $productId,
                    'quantity' => $item['qty'] ?? $item['quantity'] ?? 1,
                    'price' => $item['price'] ?? 0,
                ]);
            }
        }
    }

    /**
     * Convert cart to session format
     */
    public function toSessionArray()
    {
        $sessionCart = [];
        
        foreach ($this->items as $item) {
            $product = $item->product;
            if ($product) {
                $sessionCart[$item->product_id] = [
                    'id' => $item->product_id,
                    'name' => $product->name,
                    'price' => $item->price,
                    'qty' => $item->quantity,
                    'image' => $product->main_image_path ?? null,
                ];
            }
        }
        
        return $sessionCart;
    }
}
