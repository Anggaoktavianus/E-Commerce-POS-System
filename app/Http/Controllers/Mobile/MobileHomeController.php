<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class MobileHomeController extends Controller
{
    public function index()
    {
        $storeId = app()->has('current_store') ? app('current_store')->id : 1;
        
        // Settings
        $settings = Cache::remember('mobile.settings', 300, fn() => DB::table('settings')->pluck('value', 'key'));
        
        // Categories untuk navigasi
        $categories = Cache::remember('mobile.categories', 300, fn() => 
            DB::table('categories')
                ->where('is_active', true)
                ->orderBy('name')
                ->get()
        );
        
        // Hero Carousel
        $carousel = DB::table('carousels')->where(['key' => 'home_hero', 'is_active' => true])->first();
        $slides = $carousel ? DB::table('carousel_slides')
            ->where('carousel_id', $carousel->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get() : collect();
        
        // Flash Sale Products (products dengan discount atau featured)
        $flashSaleProducts = DB::table('products')
            ->where('is_active', true)
            ->where('store_id', $storeId)
            ->where(function($query) {
                $query->where('is_featured', true)
                      ->orWhere('is_bestseller', true);
            })
            ->orderByDesc('is_featured')
            ->orderByDesc('is_bestseller')
            ->limit(10)
            ->get();
        
        // Featured Products
        $featuredProducts = DB::table('products')
            ->where('is_active', true)
            ->where('store_id', $storeId)
            ->where('is_featured', true)
            ->orderBy('name')
            ->limit(12)
            ->get();
        
        // Bestseller Products
        $bestsellerProducts = DB::table('products')
            ->where('is_active', true)
            ->where('store_id', $storeId)
            ->where('is_bestseller', true)
            ->orderBy('name')
            ->limit(12)
            ->get();
        
        // Promotional Banners
        $promoBanners = DB::table('banners')
            ->where('is_active', true)
            ->whereIn('position', ['home_top', 'home_middle'])
            ->orderBy('sort_order')
            ->get();
        
        return view('mobile.home', compact(
            'settings',
            'categories',
            'slides',
            'flashSaleProducts',
            'featuredProducts',
            'bestsellerProducts',
            'promoBanners'
        ));
    }
}
