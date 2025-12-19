<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class MobilePromoController extends Controller
{
    public function index()
    {
        $storeId = app()->has('current_store') ? app('current_store')->id : 1;
        
        // Get all active promotional banners
        $promoBanners = DB::table('banners')
            ->where('is_active', true)
            ->whereIn('position', ['home_top', 'home_middle', 'home_bottom', 'promo'])
            ->orderBy('sort_order')
            ->get();
        
        // Get products with discounts or special offers
        // Assuming products might have discount_price or sale_price fields
        $promoProducts = DB::table('products')
            ->where('is_active', true)
            ->where('store_id', $storeId)
            ->where(function($query) {
                $query->where('is_featured', true)
                      ->orWhere('is_bestseller', true);
            })
            ->orderByDesc('is_featured')
            ->orderByDesc('is_bestseller')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
        
        // Get carousel slides that might be promotional
        $promoCarousel = DB::table('carousels')
            ->where('key', 'promo')
            ->where('is_active', true)
            ->first();
        
        $promoSlides = $promoCarousel ? DB::table('carousel_slides')
            ->where('carousel_id', $promoCarousel->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get() : collect();
        
        return view('mobile.promo', compact(
            'promoBanners',
            'promoProducts',
            'promoSlides'
        ));
    }
}
