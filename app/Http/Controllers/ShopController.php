<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        // Get store_id from request parameter (from home page store selection or shop page switcher)
        $requestedStoreId = $request->query('store_id');
        
        // Debug: Log the requested store_id
        \Log::info('ShopController - Requested store_id:', ['store_id' => $requestedStoreId]);
        
        // Determine which store to use
        $storeId = null;
        if ($requestedStoreId && $requestedStoreId !== '') {
            // Decode the encoded store_id for security
            $decodedStoreId = decode_id($requestedStoreId);
            \Log::info('ShopController - Decoded store_id:', ['decoded' => $decodedStoreId, 'original' => $requestedStoreId]);
            if ($decodedStoreId !== null) {
                $storeId = $decodedStoreId;
            }
        }
        
        // Get all active stores for store selection
        $stores = DB::table('stores')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        // Get categories directly from database (no cache for real-time updates)
        $categories = DB::table('categories')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Build optimized product query with store join
        $query = DB::table('products')
            ->leftJoin('stores', 'products.store_id', '=', 'stores.id')
            ->where('products.is_active', true);
        
        // Filter by store_id if specified (if empty string from "Semua Store", show all stores)
        if ($storeId !== null) {
            // Filter by specific store
            $query->where('products.store_id', $storeId);
            \Log::info('ShopController - Filtering by store_id:', ['store_id' => $storeId]);
        } else {
            \Log::info('ShopController - No store filter applied, showing all products');
        }
        
        // Always select these fields including store short_name
        $query->select([
            'products.id', 
            'products.name', 
            'products.price', 
            'products.description', 
            'products.short_description', 
            'products.main_image_path', 
            'products.slug', 
            'products.unit', 
            'products.stock_qty', 
            'products.is_featured', 
            'products.is_bestseller',
            'products.store_id',
            'stores.short_name as store_short_name' // Include store short_name
        ]);

        // Filter kategori berdasarkan slug (opsional)
        if ($categorySlug = $request->query('category')) {
            $query->join('product_categories', 'products.id', '=', 'product_categories.product_id')
                ->join('categories', 'product_categories.category_id', '=', 'categories.id')
                ->where('categories.slug', $categorySlug)
                ->where('categories.is_active', true);
            
            // Re-apply store filter after join (only if specific store selected)
            if ($storeId !== null) {
                $query->where('products.store_id', $storeId);
            }
            
            // Re-select all fields including store_id and store short_name after join
            $query->select([
                'products.id', 
                'products.name', 
                'products.price', 
                'products.description', 
                'products.short_description', 
                'products.main_image_path', 
                'products.slug', 
                'products.unit', 
                'products.stock_qty', 
                'products.is_featured', 
                'products.is_bestseller',
                'products.store_id',
                'stores.short_name as store_short_name' // Include store short_name
            ]);
        }

        // Apply search filter with caching
        if ($search = $request->query('search')) {
            $query->where('products.name', 'LIKE', "%{$search}%");
        }

        // Apply sorting with caching
        $sort = $request->query('sort', 'latest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('products.price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('products.price', 'desc');
                break;
            case 'name':
                $query->orderBy('products.name', 'asc');
                break;
            default:
                $query->orderBy('products.created_at', 'desc');
        }

        // Get products directly from database (no cache for real-time stock updates)
        $products = $query->paginate(12)->withQueryString();
        
        // Get selected store info for display
        $selectedStore = null;
        if ($storeId !== null) {
            $selectedStore = DB::table('stores')->where('id', $storeId)->where('is_active', true)->first();
        }
        
        // Pass encoded store_id to view for pagination links
        $encodedStoreId = $requestedStoreId && $requestedStoreId !== '' ? $requestedStoreId : null;
        
        // Current selected store ID (decoded) for comparison in view
        $currentSelectedStoreId = $storeId;

        return view('pages.shop', compact('products', 'categories', 'selectedStore', 'storeId', 'encodedStoreId', 'stores', 'currentSelectedStoreId'));
    }

    public function show(string $slug)
    {
        $storeId = app()->has('current_store') ? app('current_store')->id : 1;
        // Don't cache product detail to ensure stock is always up-to-date
        $product = DB::table('products')
            ->leftJoin('stores', 'products.store_id', '=', 'stores.id')
            ->where('products.slug', $slug)
            ->where('products.is_active', true)
            ->where('products.store_id', $storeId)
            ->select('products.*', 'stores.short_name as store_short_name')
            ->first(); // Get all columns including stock_qty

        abort_unless($product, 404);

        $related = DB::table('products')
            ->leftJoin('stores', 'products.store_id', '=', 'stores.id')
            ->where('products.is_active', true)
            ->where('products.id', '!=', $product->id)
            ->where('products.store_id', $storeId)
            ->select('products.*', 'stores.short_name as store_short_name')
            ->orderByDesc('products.is_featured')
            ->orderBy('products.name')
            ->limit(8)
            ->get();

        $categories = DB::table('categories')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $sidebarFeatured = DB::table('products')
            ->leftJoin('stores', 'products.store_id', '=', 'stores.id')
            ->where('products.is_active', true)
            ->where('products.id', '!=', $product->id)
            ->where('products.store_id', $storeId)
            ->select('products.*', 'stores.short_name as store_short_name')
            ->orderByDesc('products.is_featured')
            ->orderBy('products.name')
            ->limit(6)
            ->get();
        
        $images = DB::table('product_images')
            ->where('product_id', $product->id)
            ->orderBy('sort_order')
            ->get();
        
        $reviews = DB::table('product_reviews')
            ->where('product_id', $product->id)
            ->orderByDesc('created_at')
            ->get();

        return view('pages.shop-detail', [
            'product' => $product,
            'related' => $related,
            'reviews' => $reviews,
            'categories' => $categories,
            'sidebarFeatured' => $sidebarFeatured,
            'images' => $images,
        ]);
    }

    public function storeReview(Request $request, string $slug)
    {
        $storeId = app()->has('current_store') ? app('current_store')->id : 1;
        $product = DB::table('products')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->where('store_id', $storeId)
            ->first();

        abort_unless($product, 404);

        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255'],
            'rating' => ['required','integer','min:1','max:5'],
            'content' => ['required','string'],
        ]);

        DB::table('product_reviews')->insert([
            'product_id' => $product->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'rating' => $data['rating'],
            'content' => $data['content'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('shop.detail', $slug)->with('success', 'Terima kasih atas ulasan Anda.');
    }
}
