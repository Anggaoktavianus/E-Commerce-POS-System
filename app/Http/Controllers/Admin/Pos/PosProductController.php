<?php

namespace App\Http\Controllers\Admin\Pos;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\OutletProductInventory;
use App\Services\PosInventoryService;
use Illuminate\Http\Request;

class PosProductController extends Controller
{
    /**
     * Search products for POS
     */
    public function search(Request $request)
    {
        // Handle coupon check
        if ($request->has('coupon_check') && $request->coupon_check) {
            return $this->checkCoupon($request);
        }

        $request->validate([
            'query' => 'required|string|min:1',
            'outlet_id' => 'required|exists:outlets,id',
        ]);

        $query = $request->query;
        $outletId = $request->outlet_id;

        $products = Product::where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            })
            ->with(['category'])
            ->limit(20)
            ->get();

        // Add stock information for each product
        $products->each(function($product) use ($outletId) {
            $product->stock_at_outlet = PosInventoryService::getStock($outletId, $product->id);
            $product->has_stock = $product->stock_at_outlet > 0;
        });

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Check coupon validity
     */
    private function checkCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
            'customer_id' => 'nullable|exists:users,id',
        ]);

        try {
            $result = \App\Services\PosCouponService::applyCoupon(
                $request->code,
                $request->subtotal,
                $request->customer_id
            );

            return response()->json([
                'success' => true,
                'coupon' => $result['coupon'],
                'discount' => $result['discount'],
                'message' => 'Kupon valid'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get product by barcode/SKU
     */
    public function byBarcode(Request $request, $code)
    {
        $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
        ]);

        $outletId = $request->outlet_id;

        $product = Product::where('is_active', true)
            ->where(function($q) use ($code) {
                $q->where('sku', $code)
                  ->orWhere('sku', 'like', "%{$code}%");
            })
            ->with(['category'])
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        // Add stock information
        $product->stock_at_outlet = PosInventoryService::getStock($outletId, $product->id);
        $product->has_stock = $product->stock_at_outlet > 0;

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    /**
     * Get stock info for product at outlet
     */
    public function stockInfo(Request $request, $productId)
    {
        $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
        ]);

        $outletId = $request->outlet_id;
        $stock = PosInventoryService::getStock($outletId, $productId);

        return response()->json([
            'success' => true,
            'data' => [
                'product_id' => $productId,
                'outlet_id' => $outletId,
                'stock' => $stock,
                'has_stock' => $stock > 0,
            ]
        ]);
    }
}
