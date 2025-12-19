<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Base64;

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Debug logging
        \Log::info('Customer Dashboard - User: ' . ($user ? $user->name : 'NULL') . ' (ID: ' . ($user ? $user->id : 'NULL') . ')');
        
        // Get customer's orders
        $orders = Order::where('user_id', $user->id);
        
        // Debug logging
        \Log::info('Customer Dashboard - Orders count: ' . $orders->count());
        
        // Current month orders
        $currentMonthOrders = Order::where('user_id', $user->id)
                                   ->whereMonth('created_at', Carbon::now()->month)
                                   ->whereYear('created_at', Carbon::now()->year);
        
        $monthlyOrderCount = $currentMonthOrders->count();
        $monthlySpent = $currentMonthOrders->sum('total_amount');
        
        // Order status breakdown (all orders for this user)
        // Note: Based on actual enum values in orders table: pending, paid, failed, cancelled, expired
        $orderStats = [
            'pending' => Order::where('user_id', $user->id)->where('status', 'pending')->count(),
            'paid' => Order::where('user_id', $user->id)->where('status', 'paid')->count(),
            'failed' => Order::where('user_id', $user->id)->where('status', 'failed')->count(),
            'cancelled' => Order::where('user_id', $user->id)->where('status', 'cancelled')->count(),
            'expired' => Order::where('user_id', $user->id)->where('status', 'expired')->count(),
        ];
        
        // Total orders
        $totalOrders = Order::where('user_id', $user->id)->count();
        
        // Total spent (all time)
        $totalSpent = Order::where('user_id', $user->id)
            ->where('status', 'paid')
            ->sum('total_amount');
        
        // Recent orders (all orders for this user)
        $recentOrders = Order::where('user_id', $user->id)
                               ->with('items')
                               ->latest()
                               ->take(5)
                               ->get();
        
        // Debug logging
        \Log::info('Customer Dashboard - Recent orders count: ' . $recentOrders->count());
        
        // Wishlist items count (if wishlist table exists)
        $wishlistCount = 0;
        if (DB::getSchemaBuilder()->hasTable('wishlists')) {
            $wishlistCount = DB::table('wishlists')->where('user_id', $user->id)->count();
        }
        
        // Get user addresses count
        $addressCount = \App\Models\UserAddress::where('user_id', $user->id)
            ->where('is_active', true)
            ->count();
        
        // Monthly spending data for chart (last 6 months)
        $monthlySpending = Order::where('user_id', $user->id)
                               ->where('created_at', '>=', Carbon::now()->subMonths(6))
                               ->where('status', '!=', 'cancelled')
                               ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as orders, SUM(total_amount) as spent')
                               ->groupBy('month')
                               ->orderBy('month')
                               ->get();
        
        // Most ordered products
        $favoriteProducts = DB::table('order_items')
                             ->join('orders', 'order_items.order_id', '=', 'orders.id')
                             ->where('orders.user_id', $user->id)
                             ->where('orders.status', '!=', 'cancelled')
                             ->selectRaw('order_items.product_name, COUNT(*) as order_count, SUM(order_items.quantity) as total_quantity')
                             ->groupBy('order_items.product_name', 'order_items.product_id')
                             ->orderBy('order_count', 'desc')
                             ->take(5)
                             ->get();
        
        return view('customer.dashboard', compact(
            'monthlyOrderCount',
            'monthlySpent',
            'orderStats',
            'recentOrders',
            'wishlistCount',
            'monthlySpending',
            'favoriteProducts',
            'addressCount',
            'totalOrders',
            'totalSpent'
        ));
    }
    
    public function datatables(Request $request)
    {
        $user = auth()->user();
        
        $query = Order::where('user_id', $user->id)
                      ->with('items');
        
        // Total records
        $totalRecords = $query->count();
        
        // Apply search
        if ($request->filled('search.value')) {
            $searchTerm = $request->input('search.value');
            $query->where(function($q) use ($searchTerm) {
                $q->where('order_number', 'like', '%' . $searchTerm . '%')
                  ->orWhere('total_amount', 'like', '%' . $searchTerm . '%')
                  ->orWhere('status', 'like', '%' . $searchTerm . '%');
            });
        }
        
        // Filtered records
        $filteredRecords = $query->count();
        
        // Apply ordering
        if ($request->filled('order.0.column')) {
            $columnIndex = $request->input('order.0.column');
            $direction = $request->input('order.0.dir');
            
            $columns = ['order_number', 'created_at', 'total_amount', 'status', 'action'];
            $column = $columns[$columnIndex] ?? 'created_at';
            
            if ($column !== 'action') {
                $query->orderBy($column, $direction);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        // Apply pagination
        $orders = $query->offset($request->input('start', 0))
                       ->limit($request->input('length', 10))
                       ->get();
        
        // Format data for DataTables
        $data = $orders->map(function ($order) {
            return [
                'order_number' => '<div class="fw-medium">' . $order->order_number . '</div>',
                'created_at' => '<div class="text-muted">' . $order->created_at->format('d M Y H:i') . '</div>',
                'total_amount' => '<div class="fw-bold text-primary">Rp ' . number_format($order->total_amount, 0, ',', '.') . '</div>',
                'status' => '<span class="status-badge bg-' . ($order->status_color ?? 'secondary') . '">' . $order->formatted_status . '</span>',
                'action' => $this->generateActionButtons($order)
            ];
        });
        
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }
    
    private function generateActionButtons($order)
    {
        // Encrypt order number for URL
        $encryptedOrderNumber = base64_encode($order->order_number);
        
        $buttons = '<div class="btn-group" role="group">';
        
        // Detail button - always show
        $buttons .= '<a href="#" class="btn btn-action btn-outline-primary" data-bs-toggle="tooltip" title="Lihat Detail" onclick="showOrderDetail(\'' . $encryptedOrderNumber . '\')">';
        $buttons .= '<i class="bx bx-show"></i>';
        $buttons .= '</a>';
        
        // Track button for paid orders
        if (in_array($order->status, ['paid'])) {
            $buttons .= '<a href="#" class="btn btn-action btn-outline-success" data-bs-toggle="tooltip" title="Lacak Pesanan" onclick="trackOrder(\'' . $encryptedOrderNumber . '\')">';
            $buttons .= '<i class="bx bx-map"></i>';
            $buttons .= '</a>';
        }
        
        // Download invoice for paid orders
        if (in_array($order->status, ['paid'])) {
            $buttons .= '<a href="#" class="btn btn-action btn-outline-info" data-bs-toggle="tooltip" title="Download Invoice" onclick="downloadInvoice(\'' . $encryptedOrderNumber . '\')">';
            $buttons .= '<i class="bx bx-download"></i>';
            $buttons .= '</a>';
        }
        
        // Cancel button for pending/paid orders
        if (in_array($order->status, ['pending', 'paid'])) {
            $buttons .= '<a href="#" class="btn btn-action btn-outline-warning" data-bs-toggle="tooltip" title="Batalkan Pesanan" onclick="cancelOrder(\'' . $encryptedOrderNumber . '\')">';
            $buttons .= '<i class="bx bx-x-circle"></i>';
            $buttons .= '</a>';
        }
        
        // Reorder button for cancelled/expired orders
        if (in_array($order->status, ['cancelled', 'expired'])) {
            $buttons .= '<a href="#" class="btn btn-action btn-outline-secondary" data-bs-toggle="tooltip" title="Pesan Lagi" onclick="reorderItems(\'' . $encryptedOrderNumber . '\')">';
            $buttons .= '<i class="bx bx-refresh"></i>';
            $buttons .= '</a>';
        }
        
        $buttons .= '</div>';
        
        return $buttons;
    }
}
