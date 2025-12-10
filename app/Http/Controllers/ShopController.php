<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Services\CacheService;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $storeId = app()->has('current_store') ? app('current_store')->id : 1;
        
        // Cache categories for better performance
        $categories = Cache::remember("categories_store_{$storeId}", 3600, function () {
            return DB::table('categories')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        });

        // Build optimized product query
        $query = DB::table('products')
            ->where('is_active', true)
            ->where('store_id', $storeId)
            ->select(['id', 'name', 'price', 'description', 'image', 'category_id', 'slug']);

        // Filter kategori berdasarkan slug (opsional)
        if ($categorySlug = $request->query('category')) {
            $query->join('product_categories', 'products.id', '=', 'product_categories.product_id')
                ->join('categories', 'product_categories.category_id', '=', 'categories.id')
                ->where('categories.slug', $categorySlug)
                ->select('products.*');
        }

        // Apply search filter with caching
        if ($search = $request->query('search')) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        // Apply sorting with caching
        $sort = $request->query('sort', 'latest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->latest();
        }

        // Cache products with pagination
        $cacheKey = "products_store_{$storeId}_" . md5(json_encode($request->query()));
        $products = Cache::remember($cacheKey, 1800, function () use ($query, $request) {
            return $query->paginate(12)->withQueryString();
        });

        return view('shop.index', compact('products', 'categories'));
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

        $related = DB::table('products')
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->where('store_id', $storeId)
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->limit(8)
            ->get();

        $categories = DB::table('categories')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $sidebarFeatured = DB::table('products')
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->where('store_id', $storeId)
            ->orderByDesc('is_featured')
            ->orderBy('name')
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
