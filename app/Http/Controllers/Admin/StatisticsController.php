<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $filter = $request->get('filter', 'monthly'); // daily, weekly, monthly, yearly
        
        // Overall Statistics
        $stats = [
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'paid_orders' => Order::where('status', 'paid')->count(),
            'processing_orders' => Order::where('status', 'processing')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'delivered_orders' => Order::where('status', 'delivered')->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')->count(),
            'total_users' => User::count(),
            'total_revenue' => Order::whereIn('status', ['paid', 'processing', 'completed', 'delivered'])->sum('total_amount'),
            'today_revenue' => Order::whereIn('status', ['paid', 'processing', 'completed', 'delivered'])
                ->whereDate('created_at', today())
                ->sum('total_amount'),
            'month_revenue' => Order::whereIn('status', ['paid', 'processing', 'completed', 'delivered'])
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_amount'),
        ];

        // Revenue data based on filter
        $revenueData = $this->getRevenueData($filter);

        // Orders by Status
        $ordersByStatus = Order::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Top Products (by quantity sold)
        $topProducts = OrderItem::select(
                'products.id',
                'products.name',
                'products.price',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('orders.status', ['paid', 'processing', 'completed', 'delivered'])
            ->groupBy('products.id', 'products.name', 'products.price')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();

        // Recent Orders
        $recentOrders = Order::with('user')
            ->latest()
            ->take(10)
            ->get();

        // Orders by Day (Last 30 days)
        $ordersByDay = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(CASE WHEN status IN ("paid", "processing", "completed", "delivered") THEN total_amount ELSE 0 END) as revenue')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(29))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->map(function($item) {
                return [
                    'date' => $item->date,
                    'count' => (int) $item->count,
                    'revenue' => (float) $item->revenue
                ];
            });

        // User Growth (Last 6 months)
        $userGrowth = User::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get()
            ->map(function($item) {
                return [
                    'year' => (int) $item->year,
                    'month' => (int) $item->month,
                    'count' => (int) $item->count
                ];
            });

        return view('admin.statistics.index', compact(
            'stats',
            'revenueData',
            'ordersByStatus',
            'topProducts',
            'recentOrders',
            'ordersByDay',
            'userGrowth',
            'filter'
        ));
    }

    private function getRevenueData($filter)
    {
        $query = Order::whereIn('status', ['paid', 'processing', 'completed', 'delivered']);
        
        switch ($filter) {
            case 'daily':
                // Last 30 days
                $query->where('created_at', '>=', Carbon::now()->subDays(29)->startOfDay())
                    ->select(
                        DB::raw('DATE(created_at) as date'),
                        DB::raw('YEAR(created_at) as year'),
                        DB::raw('MONTH(created_at) as month'),
                        DB::raw('DAY(created_at) as day'),
                        DB::raw('SUM(total_amount) as revenue'),
                        DB::raw('COUNT(*) as order_count')
                    )
                    ->groupBy('date', 'year', 'month', 'day')
                    ->orderBy('date', 'asc');
                break;
                
            case 'weekly':
                // Last 12 weeks
                $query->where('created_at', '>=', Carbon::now()->subWeeks(11)->startOfWeek())
                    ->select(
                        DB::raw('YEAR(created_at) as year'),
                        DB::raw('WEEK(created_at) as week'),
                        DB::raw('MIN(DATE(created_at)) as date'),
                        DB::raw('SUM(total_amount) as revenue'),
                        DB::raw('COUNT(*) as order_count')
                    )
                    ->groupBy('year', 'week')
                    ->orderBy('year', 'asc')
                    ->orderBy('week', 'asc');
                break;
                
            case 'monthly':
                // Last 12 months
                $query->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
                    ->select(
                        DB::raw('YEAR(created_at) as year'),
                        DB::raw('MONTH(created_at) as month'),
                        DB::raw('SUM(total_amount) as revenue'),
                        DB::raw('COUNT(*) as order_count')
                    )
                    ->groupBy('year', 'month')
                    ->orderBy('year', 'asc')
                    ->orderBy('month', 'asc');
                break;
                
            case 'yearly':
                // Last 5 years
                $query->where('created_at', '>=', Carbon::now()->subYears(4)->startOfYear())
                    ->select(
                        DB::raw('YEAR(created_at) as year'),
                        DB::raw('SUM(total_amount) as revenue'),
                        DB::raw('COUNT(*) as order_count')
                    )
                    ->groupBy('year')
                    ->orderBy('year', 'asc');
                break;
        }
        
        return $query->get()
            ->map(function($item) use ($filter) {
                $data = [
                    'revenue' => (float) $item->revenue,
                    'order_count' => (int) $item->order_count
                ];
                
                if ($filter === 'daily') {
                    $data['year'] = (int) $item->year;
                    $data['month'] = (int) $item->month;
                    $data['day'] = (int) $item->day;
                    $data['date'] = $item->date;
                } elseif ($filter === 'weekly') {
                    $data['year'] = (int) $item->year;
                    $data['week'] = (int) $item->week;
                    $data['date'] = $item->date;
                } elseif ($filter === 'monthly') {
                    $data['year'] = (int) $item->year;
                    $data['month'] = (int) $item->month;
                } elseif ($filter === 'yearly') {
                    $data['year'] = (int) $item->year;
                }
                
                return $data;
            })
            ->values();
    }
}
