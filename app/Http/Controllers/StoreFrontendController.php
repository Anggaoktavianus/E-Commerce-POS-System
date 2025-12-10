<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\Product;
use App\Models\Category;

class StoreFrontendController extends Controller
{
    /**
     * Store homepage
     */
    public function home()
    {
        $store = app('current_store');
        
        // Get products scoped by store
        $products = Product::where('is_active', true)
                          ->where('store_id', $store->id)
                          ->orderBy('created_at', 'desc')
                          ->take(12)
                          ->get();
        
        $categories = Category::where('is_active', true)
                            ->orderBy('name')
                            ->get();
        
        // Get store theme
        $theme = $store->theme ?? 'default';
        
        return view("stores.themes.{$theme}.home", compact('store', 'products', 'categories'));
    }
    
    /**
     * Store products page
     */
    public function products(Request $request)
    {
        $store = app('current_store');
        
        $query = Product::where('is_active', true)->where('store_id', $store->id);
        
        // Filter by category
        if ($request->category) {
            $query->where('category_id', $request->category);
        }
        
        // Search
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }
        
        $products = $query->orderBy('created_at', 'desc')
                         ->paginate(12);
        
        $categories = Category::where('is_active', true)
                            ->orderBy('name')
                            ->get();
        
        $theme = $store->theme ?? 'default';
        
        return view("stores.themes.{$theme}.products", compact('store', 'products', 'categories'));
    }
    
    /**
     * Product detail page
     */
    public function productDetail($slug)
    {
        $store = app('current_store');
        
        $product = Product::where('slug', $slug)
                         ->where('is_active', true)
                         ->where('store_id', $store->id)
                         ->firstOrFail();
        
        // Get related products
        $relatedProducts = Product::where('category_id', $product->category_id)
                                ->where('id', '!=', $product->id)
                                ->where('is_active', true)
                                ->where('store_id', $store->id)
                                ->take(4)
                                ->get();
        
        $theme = $store->theme ?? 'default';
        
        return view("stores.themes.{$theme}.product-detail", compact('store', 'product', 'relatedProducts'));
    }
    
    /**
     * Store about page
     */
    public function about()
    {
        $store = app('current_store');
        $theme = $store->theme ?? 'default';
        
        return view("stores.themes.{$theme}.about", compact('store'));
    }
    
    /**
     * Store contact page
     */
    public function contact()
    {
        $store = app('current_store');
        $theme = $store->theme ?? 'default';
        
        return view("stores.themes.{$theme}.contact", compact('store'));
    }
    
    /**
     * Store cart page
     */
    public function cart()
    {
        $store = app('current_store');
        $theme = $store->theme ?? 'default';
        
        return view("stores.themes.{$theme}.cart", compact('store'));
    }
    
    /**
     * Store checkout page
     */
    public function checkout()
    {
        $store = app('current_store');
        $theme = $store->theme ?? 'default';
        
        return view("stores.themes.{$theme}.checkout", compact('store'));
    }
}

