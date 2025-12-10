<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\Outlet;

class StoreAdminController extends Controller
{
    /**
     * Store admin dashboard
     */
    public function dashboard()
    {
        $store = app('current_store');
        
        // Get store statistics
        $stats = [
            'total_products' => Product::where('store_id', $store->id)->count(),
            'active_products' => Product::where('store_id', $store->id)->where('is_active', true)->count(),
            'total_categories' => Category::where('store_id', $store->id)->count(),
            'total_outlets' => Outlet::where('store_id', $store->id)->count(),
            'active_outlets' => Outlet::where('store_id', $store->id)->where('is_active', true)->count(),
            'total_orders' => Order::where('store_id', $store->id)->count(),
            'pending_orders' => Order::where('store_id', $store->id)->where('status', 'pending')->count(),
            'completed_orders' => Order::where('store_id', $store->id)->where('status', 'completed')->count(),
        ];
        
        // Get recent orders
        $recentOrders = Order::where('store_id', $store->id)
                            ->with('customer')
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();
        
        // Get top products
        $topProducts = Product::where('store_id', $store->id)
                             ->withCount(['orderItems' => function($query) {
                                 $query->whereHas('order', function($q) use ($store) {
                                     $q->where('store_id', $store->id);
                                 });
                             }])
                             ->orderBy('order_items_count', 'desc')
                             ->take(5)
                             ->get();
        
        return view('stores.admin.dashboard', compact('store', 'stats', 'recentOrders', 'topProducts'));
    }
    
    /**
     * Store products management
     */
    public function products()
    {
        $store = app('current_store');
        
        $products = Product::where('store_id', $store->id)
                          ->with('category')
                          ->orderBy('created_at', 'desc')
                          ->get();
        
        return view('stores.admin.products', compact('store', 'products'));
    }
    
    /**
     * Store categories management
     */
    public function categories()
    {
        $store = app('current_store');
        
        $categories = Category::where('store_id', $store->id)
                             ->withCount('products')
                             ->orderBy('name')
                             ->get();
        
        return view('stores.admin.categories', compact('store', 'categories'));
    }
    
    /**
     * Store orders management
     */
    public function orders()
    {
        $store = app('current_store');
        
        $orders = Order::where('store_id', $store->id)
                      ->with('customer', 'items.product')
                      ->orderBy('created_at', 'desc')
                      ->get();
        
        return view('stores.admin.orders', compact('store', 'orders'));
    }
    
    /**
     * Store outlets management
     */
    public function outlets()
    {
        $store = app('current_store');
        
        $outlets = Outlet::where('store_id', $store->id)
                        ->orderBy('type')
                        ->orderBy('name')
                        ->get();
        
        return view('stores.admin.outlets', compact('store', 'outlets'));
    }
    
    /**
     * Store settings
     */
    public function settings()
    {
        $store = app('current_store');
        
        return view('stores.admin.settings', compact('store'));
    }
    
    /**
     * Update store settings
     */
    public function updateSettings(Request $request)
    {
        $store = app('current_store');
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'theme' => 'required|in:default,modern,minimal,classic',
            'logo_url' => 'nullable|url',
            'is_active' => 'boolean',
        ]);
        
        $store->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'theme' => $request->theme,
            'logo_url' => $request->logo_url,
            'is_active' => $request->is_active ?? true,
        ]);
        
        return redirect()
            ->route('store.admin.settings')
            ->with('success', 'Store settings updated successfully');
    }
    
    /**
     * Store analytics
     */
    public function analytics()
    {
        $store = app('current_store');
        
        // Get monthly sales data
        $monthlySales = Order::where('store_id', $store->id)
                            ->where('status', 'completed')
                            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total) as total')
                            ->groupBy('month')
                            ->orderBy('month')
                            ->get();
        
        // Get top categories
        $topCategories = Category::where('store_id', $store->id)
                                ->withCount(['products' => function($query) {
                                    $query->where('is_active', true);
                                }])
                                ->orderBy('products_count', 'desc')
                                ->take(5)
                                ->get();
        
        return view('stores.admin.analytics', compact('store', 'monthlySales', 'topCategories'));
    }
}
