<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MobileShopController extends Controller
{
    public function index(Request $request)
    {
        $storeId = app()->has('current_store') ? app('current_store')->id : 1;
        
        // Get categories
        $categories = DB::table('categories')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        // Build product query
        $query = DB::table('products')
            ->where('products.is_active', true)
            ->where('products.store_id', $storeId)
            ->select(['products.id', 'products.name', 'products.price', 'products.description', 'products.short_description', 'products.main_image_path', 'products.slug', 'products.unit', 'products.stock_qty', 'products.is_featured', 'products.is_bestseller']);
        
        // Filter by category
        if ($categorySlug = $request->query('category')) {
            $query->join('product_categories', 'products.id', '=', 'product_categories.product_id')
                ->join('categories', 'product_categories.category_id', '=', 'categories.id')
                ->where('categories.slug', $categorySlug)
                ->where('categories.is_active', true);
        }
        
        // Search filter
        if ($search = $request->query('search')) {
            $query->where('products.name', 'LIKE', "%{$search}%");
        }
        
        // Stock filter
        if ($stock = $request->query('stock')) {
            if ($stock === 'available') {
                $query->where('products.stock_qty', '>', 0);
            } elseif ($stock === 'out_of_stock') {
                $query->where('products.stock_qty', '<=', 0);
            }
        }
        
        // Price range filter
        if ($minPrice = $request->query('min_price')) {
            $query->where('products.price', '>=', $minPrice);
        }
        if ($maxPrice = $request->query('max_price')) {
            $query->where('products.price', '<=', $maxPrice);
        }
        
        // Rating filter
        if ($minRating = $request->query('min_rating')) {
            $productIdsWithRating = DB::table('product_reviews')
                ->select('product_id')
                ->groupBy('product_id')
                ->havingRaw('AVG(rating) >= ?', [$minRating])
                ->pluck('product_id');
            $query->whereIn('products.id', $productIdsWithRating);
        }
        
        // Sorting
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
            case 'rating':
                // Sort by average rating
                $query->leftJoin(DB::raw('(SELECT product_id, AVG(rating) as avg_rating FROM product_reviews GROUP BY product_id) as ratings'), 'products.id', '=', 'ratings.product_id')
                    ->orderByDesc('ratings.avg_rating')
                    ->orderByDesc('products.created_at');
                break;
            case 'sold':
                // Sort by total sold
                $query->leftJoin(DB::raw('(SELECT order_items.product_id, SUM(order_items.quantity) as total_sold FROM order_items JOIN orders ON order_items.order_id = orders.id WHERE orders.status IN ("delivered", "completed") GROUP BY order_items.product_id) as sold'), 'products.id', '=', 'sold.product_id')
                    ->orderByDesc('sold.total_sold')
                    ->orderByDesc('products.created_at');
                break;
            default:
                $query->orderBy('products.created_at', 'desc');
        }
        
        // Get min and max price for filter
        $priceRange = DB::table('products')
            ->where('is_active', true)
            ->where('store_id', $storeId)
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();
        
        $products = $query->paginate(20)->withQueryString();
        
        // Add rating and sold count to each product
        foreach ($products as $product) {
            // Get reviews for this product
            $reviews = DB::table('product_reviews')
                ->where('product_id', $product->id)
                ->get();
            
            // Calculate average rating
            $product->average_rating = $reviews->count() > 0 
                ? round($reviews->avg('rating'), 1) 
                : 0;
            $product->total_reviews = $reviews->count();
            
            // Calculate total sold (from delivered orders)
            $product->total_sold = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('order_items.product_id', $product->id)
                ->whereIn('orders.status', ['delivered', 'completed'])
                ->sum('order_items.quantity');
        }
        
        return view('mobile.shop', compact('products', 'categories', 'priceRange'));
    }
    
    public function show(string $slug)
    {
        $storeId = app()->has('current_store') ? app('current_store')->id : 1;
        
        $product = DB::table('products')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->where('store_id', $storeId)
            ->first();
        
        abort_unless($product, 404);
        
        // Get product images
        $images = DB::table('product_images')
            ->where('product_id', $product->id)
            ->orderBy('sort_order')
            ->get();
        
        // Related products
        $related = DB::table('products')
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->where('store_id', $storeId)
            ->orderByDesc('is_featured')
            ->limit(8)
            ->get();
        
        // Get product reviews
        $reviews = DB::table('product_reviews')
            ->where('product_id', $product->id)
            ->orderByDesc('created_at')
            ->get();
        
        // Calculate average rating
        $averageRating = $reviews->count() > 0 
            ? round($reviews->avg('rating'), 1) 
            : 0;
        $totalReviews = $reviews->count();
        
        // Calculate total sold (from delivered orders)
        $totalSold = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('order_items.product_id', $product->id)
            ->whereIn('orders.status', ['delivered', 'completed'])
            ->sum('order_items.quantity');
        
        // Check if user can review (has delivered order with this product)
        $canReview = false;
        $userOrderId = null;
        $hasReviewed = false;
        
        if (auth()->check()) {
            $user = auth()->user();
            
            // Check if user has delivered order with this product
            $userOrder = DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->where('orders.user_id', $user->id)
                ->where('order_items.product_id', $product->id)
                ->whereIn('orders.status', ['delivered', 'completed'])
                ->whereNotNull('orders.delivered_at')
                ->select('orders.id', 'orders.order_number')
                ->first();
            
            if ($userOrder) {
                $canReview = true;
                $userOrderId = $userOrder->id;
                
                // Check if user already reviewed this product for this order
                $existingReview = DB::table('product_reviews')
                    ->where('user_id', $user->id)
                    ->where('product_id', $product->id)
                    ->where('order_id', $userOrder->id)
                    ->first();
                
                $hasReviewed = $existingReview !== null;
            }
        }
        
        // Get recommended products (frequently bought together)
        $recommendedProducts = $this->getRecommendedProducts($product->id, $storeId);
        
        return view('mobile.shop-detail', compact('product', 'images', 'related', 'reviews', 'averageRating', 'totalReviews', 'totalSold', 'canReview', 'userOrderId', 'hasReviewed', 'recommendedProducts'));
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
        
        // Check if user is authenticated
        if (!auth()->check()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda harus login terlebih dahulu untuk memberikan ulasan.'
                ], 401);
            }
            return redirect()->route('mobile.login')->with('error', 'Anda harus login terlebih dahulu untuk memberikan ulasan.');
        }
        
        $user = auth()->user();
        
        // Validate that user has delivered order with this product
        $userOrder = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.user_id', $user->id)
            ->where('order_items.product_id', $product->id)
            ->whereIn('orders.status', ['delivered', 'completed'])
            ->whereNotNull('orders.delivered_at')
            ->select('orders.id', 'orders.order_number')
            ->first();
        
        if (!$userOrder) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda hanya dapat memberikan ulasan untuk produk yang sudah Anda terima.'
                ], 403);
            }
            return redirect()->route('mobile.shop.detail', $slug)->with('error', 'Anda hanya dapat memberikan ulasan untuk produk yang sudah Anda terima.');
        }
        
        // Check if user already reviewed this product for this order
        $existingReview = DB::table('product_reviews')
            ->where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->where('order_id', $userOrder->id)
            ->first();
        
        if ($existingReview) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah memberikan ulasan untuk produk ini pada pesanan ini.'
                ], 422);
            }
            return redirect()->route('mobile.shop.detail', $slug)->with('error', 'Anda sudah memberikan ulasan untuk produk ini pada pesanan ini.');
        }
        
        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'content' => ['required', 'string', 'min:10'],
        ]);
        
        DB::table('product_reviews')->insert([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'order_id' => $userOrder->id,
            'name' => $user->name,
            'email' => $user->email,
            'rating' => $data['rating'],
            'content' => $data['content'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Terima kasih atas ulasan Anda.'
            ]);
        }
        
        return redirect()->route('mobile.shop.detail', $slug)->with('success', 'Terima kasih atas ulasan Anda.');
    }
    
    private function getRecommendedProducts($productId, $storeId, $limit = 4)
    {
        // Get products that are frequently bought together
        // Find orders that contain this product, then get other products from those orders
        $relatedProductIds = DB::table('order_items as oi1')
            ->join('order_items as oi2', 'oi1.order_id', '=', 'oi2.order_id')
            ->join('orders', 'oi1.order_id', '=', 'orders.id')
            ->where('oi1.product_id', $productId)
            ->where('oi2.product_id', '!=', $productId)
            ->whereIn('orders.status', ['delivered', 'completed'])
            ->where('orders.store_id', $storeId)
            ->select('oi2.product_id', DB::raw('COUNT(*) as frequency'))
            ->groupBy('oi2.product_id')
            ->orderByDesc('frequency')
            ->limit($limit)
            ->pluck('product_id');
        
        if ($relatedProductIds->isEmpty()) {
            // Fallback: get featured or bestseller products
            return DB::table('products')
                ->where('is_active', true)
                ->where('store_id', $storeId)
                ->where('id', '!=', $productId)
                ->where(function($query) {
                    $query->where('is_featured', true)
                          ->orWhere('is_bestseller', true);
                })
                ->orderByDesc('is_featured')
                ->orderByDesc('is_bestseller')
                ->limit($limit)
                ->get();
        }
        
        return DB::table('products')
            ->whereIn('id', $relatedProductIds)
            ->where('is_active', true)
            ->where('store_id', $storeId)
            ->get();
    }
}
