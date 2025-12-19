<?php

namespace App\Services;

use App\Models\OutletProductInventory;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\StockMovementService;

class PosInventoryService
{
    /**
     * Decrease stock for POS transaction
     */
    public static function decreaseStock($outletId, $productId, $quantity, $transactionId, $transactionNumber)
    {
        // Get outlet inventory
        $inventory = OutletProductInventory::where('outlet_id', $outletId)
            ->where('product_id', $productId)
            ->firstOrFail();

        $stockBefore = $inventory->stock;

        // Validate stock
        if ($inventory->stock < $quantity) {
            throw new \Exception("Stok tidak mencukupi. Tersedia: {$inventory->stock}, Diminta: {$quantity}");
        }

        // Update outlet inventory
        $inventory->stock -= $quantity;
        $inventory->save();

        $stockAfter = $inventory->stock;

        // Get product for stock movement
        $product = Product::find($productId);

        // Create stock movement
        StockMovementService::logDecrease(
            $product,
            $quantity,
            'App\Models\PosTransaction',
            $transactionId,
            $transactionNumber,
            "POS Sale - Transaction #{$transactionNumber} (Outlet: {$outletId})",
            $outletId  // Pass outlet_id
        );

        // Optional: Sync global stock
        // self::syncGlobalStock($productId);

        return [
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
        ];
    }

    /**
     * Restore stock for cancelled POS transaction
     */
    public static function restoreStock($outletId, $productId, $quantity, $transactionId, $transactionNumber)
    {
        // Get outlet inventory
        $inventory = OutletProductInventory::where('outlet_id', $outletId)
            ->where('product_id', $productId)
            ->firstOrFail();

        $stockBefore = $inventory->stock;

        // Restore stock
        $inventory->stock += $quantity;
        $inventory->save();

        $stockAfter = $inventory->stock;

        // Get product for stock movement
        $product = Product::find($productId);

        // Create stock movement (restore)
        StockMovementService::logRestore(
            $product,
            $quantity,
            'App\Models\PosTransaction',
            $transactionId,
            $transactionNumber,
            "POS Return - Transaction #{$transactionNumber} (Outlet: {$outletId})",
            $outletId  // Pass outlet_id
        );

        // Optional: Sync global stock
        // self::syncGlobalStock($productId);

        return [
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
        ];
    }

    /**
     * Sync global stock from outlet inventories
     */
    public static function syncGlobalStock($productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return;
        }

        // Calculate total stock from all outlets
        $totalStock = OutletProductInventory::where('product_id', $productId)
            ->sum('stock');

        // Update global stock
        $product->stock_qty = $totalStock;
        $product->save();
    }

    /**
     * Get available stock at outlet
     */
    public static function getStock($outletId, $productId)
    {
        $inventory = OutletProductInventory::where('outlet_id', $outletId)
            ->where('product_id', $productId)
            ->first();

        return $inventory ? $inventory->stock : 0;
    }
}
