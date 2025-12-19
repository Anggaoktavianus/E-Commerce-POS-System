<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        // Settings & Socials
        $settings = Cache::remember('home.settings', 300, fn() => DB::table('settings')->pluck('value', 'key'));
        $socialLinks = Cache::remember('home.social_links', 300, fn() => DB::table('social_links')->where('is_active', true)->orderBy('sort_order')->get());

        // Menus
        $headerMenu = Cache::remember('home.header_menu', 300, fn() => DB::table('navigation_menus')->where(['location' => 'header', 'is_active' => true])->first());
        $headerLinks = Cache::remember('home.header_links', 300, function() use ($headerMenu){
            if (!$headerMenu) return collect();

            // Ambil semua link (top-level dan child) untuk menu header
            $allLinks = DB::table('navigation_links')
                ->leftJoin('pages', 'navigation_links.page_id', '=', 'pages.id')
                ->where('navigation_links.navigation_menu_id', $headerMenu->id)
                ->where('navigation_links.is_active', true)
                ->select('navigation_links.*', 'pages.slug as page_slug')
                ->orderBy('navigation_links.sort_order')
                ->get();

            // Kelompokkan berdasarkan parent_id untuk membangun tree
            $grouped = $allLinks->groupBy('parent_id');

            $buildTree = function ($parentId) use (&$buildTree, $grouped) {
                $children = $grouped->get($parentId, collect());
                return $children->map(function ($link) use (&$buildTree, $grouped) {
                    $link->children = $buildTree($link->id);
                    return $link;
                });
            };

            // Top-level links (parent_id = null) dengan children di properti children
            return $buildTree(null);
        });
        $footerMenus = Cache::remember('home.footer_menus', 300, function(){
            return DB::table('navigation_menus')
                ->where('location', 'like', 'footer_column_%')
                ->where('is_active', true)
                ->orderBy('location')
                ->get()
                ->map(function ($menu) {
                    $links = DB::table('navigation_links')
                        ->leftJoin('pages', 'navigation_links.page_id', '=', 'pages.id')
                        ->where('navigation_links.navigation_menu_id', $menu->id)
                        ->whereNull('navigation_links.parent_id')
                        ->where('navigation_links.is_active', true)
                        ->select('navigation_links.*', 'pages.slug as page_slug')
                        ->orderBy('navigation_links.sort_order')
                        ->get();
                    $menu->links = $links;
                    return $menu;
                });
        });

        // Hero/Carousel
        $slides = Cache::remember('home.slides.home_hero', 300, function(){
            $carousel = DB::table('carousels')->where(['key' => 'home_hero', 'is_active' => true])->first();
            if (!$carousel) return collect();
            return DB::table('carousel_slides')
                ->where('carousel_id', $carousel->id)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        });

        // Features
        $features = Cache::remember('home.features', 300, fn() => DB::table('features')->where('is_active', true)->orderBy('sort_order')->get());

        // Fetch active stores for store selection
        $stores = Cache::remember('home.stores', 300, fn() => DB::table('stores')->where('is_active', true)->orderBy('name')->get());
        
        // Get selected store from request (if any) - decode encoded store_id
        $requestedStoreId = request('store_id');
        $selectedStoreId = null;
        if ($requestedStoreId && $requestedStoreId !== '') {
            $decodedId = decode_id($requestedStoreId);
            if ($decodedId !== null) {
                $selectedStoreId = $decodedId;
            }
        }


        // Specific Vegetables carousel data (backward compatibility with the template section)
        $vegetableProducts = (function(){
            $veggiesCat = DB::table('categories')->first();
            if (!$veggiesCat) return collect();
            return DB::table('products')
                ->join('product_categories', 'products.id', '=', 'product_categories.product_id')
                ->leftJoin('stores', 'products.store_id', '=', 'stores.id')
                ->where('product_categories.category_id', $veggiesCat->id)
                ->where('products.is_active', true)
                ->select('products.*', 'stores.short_name as store_short_name')
                ->orderByDesc('products.is_featured')
                ->orderBy('products.name')
                ->limit(8)
                ->get();
        })(); 

        // Banners (no cache to avoid stale during edits)
        $bannersTop = DB::table('banners')->where(['position' => 'home_top', 'is_active' => true])->orderBy('sort_order')->get();
        $bannersMiddle = DB::table('banners')->where(['position' => 'home_middle', 'is_active' => true])->orderBy('sort_order')->get();
        $bannersBottom = DB::table('banners')->where(['position' => 'home_bottom', 'is_active' => true])->orderBy('sort_order')->get();

        // Bestseller Collection
        $bestsellerItems = Cache::remember('home.bestseller', 300, function(){
            $bestseller = DB::table('home_collections')->where(['key' => 'bestseller', 'is_active' => true])->first();
            if (!$bestseller) return collect();
            return DB::table('home_collection_items')
                ->join('products', 'home_collection_items.product_id', '=', 'products.id')
                ->leftJoin('stores', 'products.store_id', '=', 'stores.id')
                ->where('home_collection_items.home_collection_id', $bestseller->id)
                ->select('products.*', 'home_collection_items.sort_order', 'stores.short_name as store_short_name')
                ->orderBy('home_collection_items.sort_order')
                ->get();
        });

        // Facts & Testimonials
        $facts = Cache::remember('home.facts', 300, fn() => DB::table('facts')->where('is_active', true)->orderBy('sort_order')->get());
        $testimonials = Cache::remember('home.testimonials', 300, fn() => DB::table('testimonials')->where('is_active', true)->orderBy('sort_order')->get());

        return view('home', [
            'settings' => $settings,
            'socialLinks' => $socialLinks,
            'headerLinks' => $headerLinks,
            'footerMenus' => $footerMenus,
            'slides' => $slides,
            'features' => $features,
            'stores' => $stores,
            'selectedStoreId' => $selectedStoreId,
            'bannersTop' => $bannersTop,
            'bannersMiddle' => $bannersMiddle,
            'bannersBottom' => $bannersBottom,
            'bestsellerItems' => $bestsellerItems,
            'facts' => $facts,
            'testimonials' => $testimonials,
            'vegetableProducts' => $vegetableProducts,
        ]);
    }
}
