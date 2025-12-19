<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MobileComparisonController extends Controller
{
    public function index(Request $request)
    {
        $productIds = $request->get('products', []);
        
        if (empty($productIds) || count($productIds) > 4) {
            return redirect()->route('mobile.shop')->with('error', 'Pilih 1-4 produk untuk dibandingkan');
        }
        
        $storeId = app()->has('current_store') ? app('current_store')->id : 1;
        
        $products = DB::table('products')
            ->whereIn('id', $productIds)
            ->where('is_active', true)
            ->where('store_id', $storeId)
            ->get();
        
        // Get additional data for each product
        foreach ($products as $product) {
            // Images
            $product->images = DB::table('product_images')
                ->where('product_id', $product->id)
                ->orderBy('sort_order')
                ->get();
            
            // Reviews
            $reviews = DB::table('product_reviews')
                ->where('product_id', $product->id)
                ->get();
            $product->average_rating = $reviews->count() > 0 
                ? round($reviews->avg('rating'), 1) 
                : 0;
            $product->total_reviews = $reviews->count();
            
            // Total sold
            $product->total_sold = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('order_items.product_id', $product->id)
                ->whereIn('orders.status', ['delivered', 'completed'])
                ->sum('order_items.quantity');
        }
        
        return view('mobile.comparison', compact('products'));
    }
    
    public function add(Request $request)
    {
        $productId = $request->input('product_id');
        $comparison = session('comparison', []);
        
        if (!in_array($productId, $comparison)) {
            if (count($comparison) >= 4) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maksimal 4 produk untuk dibandingkan'
                ], 422);
            }
            $comparison[] = $productId;
            session(['comparison' => $comparison]);
        }
        
        return response()->json([
            'success' => true,
            'count' => count($comparison),
            'message' => 'Produk ditambahkan ke perbandingan'
        ]);
    }
    
    public function remove(Request $request)
    {
        $productId = $request->input('product_id');
        $comparison = session('comparison', []);
        
        $comparison = array_values(array_filter($comparison, function($id) use ($productId) {
            return $id != $productId;
        }));
        
        session(['comparison' => $comparison]);
        
        return response()->json([
            'success' => true,
            'count' => count($comparison),
            'message' => 'Produk dihapus dari perbandingan'
        ]);
    }
    
    public function clear()
    {
        session()->forget('comparison');
        
        return response()->json([
            'success' => true,
            'message' => 'Perbandingan dibersihkan'
        ]);
    }
}
