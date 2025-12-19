<?php

namespace App\Services;

use App\Models\StockMovement;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class StockMovementService
{
    /**
     * Log stock movement
     */
    public static function log(
        Product $product,
        string $type,
        int $quantity,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $referenceNumber = null,
        ?string $notes = null,
        ?int $userId = null,
        ?int $outletId = null
    ): StockMovement {
        $oldStock = $product->stock_qty ?? 0;
        
        // Calculate new stock based on type
        $newStock = match($type) {
            'in', 'restore' => $oldStock + abs($quantity),
            'out' => max(0, $oldStock - abs($quantity)),
            'adjustment' => $oldStock + $quantity, // Can be positive or negative
            default => $oldStock
        };

        $movement = StockMovement::create([
            'product_id' => $product->id,
            'outlet_id' => $outletId,
            'type' => $type,
            'quantity' => $quantity,
            'old_stock' => $oldStock,
            'new_stock' => $newStock,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'reference_number' => $referenceNumber,
            'notes' => $notes,
            'user_id' => $userId ?? auth()->id(),
        ]);

        Log::info('Stock movement logged', [
            'movement_id' => $movement->id,
            'product_id' => $product->id,
            'type' => $type,
            'quantity' => $quantity,
            'old_stock' => $oldStock,
            'new_stock' => $newStock,
        ]);

        return $movement;
    }

    /**
     * Log stock decrease (out)
     */
    public static function logDecrease(
        Product $product,
        int $quantity,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $referenceNumber = null,
        ?string $notes = null,
        ?int $outletId = null
    ): StockMovement {
        return self::log(
            $product,
            'out',
            -abs($quantity),
            $referenceType,
            $referenceId,
            $referenceNumber,
            $notes ?? "Stock keluar: {$quantity} unit",
            null,
            $outletId
        );
    }

    /**
     * Log stock increase (in)
     */
    public static function logIncrease(
        Product $product,
        int $quantity,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $referenceNumber = null,
        ?string $notes = null
    ): StockMovement {
        return self::log(
            $product,
            'in',
            abs($quantity),
            $referenceType,
            $referenceId,
            $referenceNumber,
            $notes ?? "Stock masuk: {$quantity} unit"
        );
    }

    /**
     * Log stock restore
     */
    public static function logRestore(
        Product $product,
        int $quantity,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $referenceNumber = null,
        ?string $notes = null,
        ?int $outletId = null
    ): StockMovement {
        return self::log(
            $product,
            'restore',
            abs($quantity),
            $referenceType,
            $referenceId,
            $referenceNumber,
            $notes ?? "Stock dikembalikan: {$quantity} unit",
            null,
            $outletId
        );
    }

    /**
     * Log manual adjustment
     */
    public static function logAdjustment(
        Product $product,
        int $quantity,
        ?string $notes = null,
        ?int $userId = null
    ): StockMovement {
        return self::log(
            $product,
            'adjustment',
            $quantity,
            null,
            null,
            null,
            $notes ?? "Penyesuaian manual stok",
            $userId
        );
    }

    /**
     * Log manual adjustment with explicit old_stock and new_stock values
     * Use this when you need to ensure correct old_stock value (e.g., after product has been updated)
     */
    public static function logAdjustmentWithValues(
        Product $product,
        int $quantity,
        int $oldStock,
        int $newStock,
        ?string $notes = null,
        ?int $userId = null
    ): StockMovement {
        $movement = StockMovement::create([
            'product_id' => $product->id,
            'type' => 'adjustment',
            'quantity' => $quantity,
            'old_stock' => $oldStock,
            'new_stock' => $newStock,
            'reference_type' => null,
            'reference_id' => null,
            'reference_number' => null,
            'notes' => $notes ?? "Penyesuaian manual stok",
            'user_id' => $userId ?? auth()->id(),
        ]);

        Log::info('Stock movement logged (with explicit values)', [
            'movement_id' => $movement->id,
            'product_id' => $product->id,
            'type' => 'adjustment',
            'quantity' => $quantity,
            'old_stock' => $oldStock,
            'new_stock' => $newStock,
        ]);

        return $movement;
    }
}
