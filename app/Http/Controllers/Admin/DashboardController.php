<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get low stock products (threshold: <= 10)
        $lowStockThreshold = 10;
        $lowStockProducts = Product::where('is_active', true)
            ->where('stock_qty', '<=', $lowStockThreshold)
            ->where('stock_qty', '>=', 0)
            ->orderBy('stock_qty', 'asc')
            ->limit(10)
            ->get();
        
        // Get out of stock products
        $outOfStockProducts = Product::where('is_active', true)
            ->where('stock_qty', '<=', 0)
            ->count();
        
        // Get low stock count
        $lowStockCount = Product::where('is_active', true)
            ->where('stock_qty', '>', 0)
            ->where('stock_qty', '<=', $lowStockThreshold)
            ->count();
        
        // Statistics
        $stats = [
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'total_users' => User::count(),
            'revenue' => Order::where('status', 'paid')->sum('total_amount'),
            'low_stock_count' => $lowStockCount,
            'out_of_stock_count' => $outOfStockProducts,
        ];
        
        // Recent orders
        $recentOrders = Order::with('user')
            ->latest()
            ->take(3)
            ->get();
        
        return view('admin.dashboard', compact(
            'stats',
            'lowStockProducts',
            'lowStockCount',
            'outOfStockProducts',
            'recentOrders'
        ));
    }
}
