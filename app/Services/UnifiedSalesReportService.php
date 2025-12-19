<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PosTransaction;
use App\Models\PosTransactionItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UnifiedSalesReportService
{
    /**
     * Get total sales (online + POS)
     * 
     * @param string $dateFrom
     * @param string $dateTo
     * @param int|null $outletId
     * @return array
     */
    public static function getTotalSales($dateFrom, $dateTo, $outletId = null)
    {
        // Online sales
        $onlineQuery = Order::where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom, $dateTo]);

        if ($outletId) {
            $onlineQuery->where('outlet_id', $outletId);
        }

        $onlineSales = $onlineQuery->sum('total_amount');
        $onlineCount = $onlineQuery->count();

        // POS sales
        $posQuery = PosTransaction::where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom, $dateTo]);

        if ($outletId) {
            $posQuery->where('outlet_id', $outletId);
        }

        $posSales = $posQuery->sum('total_amount');
        $posCount = $posQuery->count();

        $totalSales = $onlineSales + $posSales;

        return [
            'online' => [
                'total' => $onlineSales,
                'count' => $onlineCount,
                'percentage' => $totalSales > 0 
                    ? round(($onlineSales / $totalSales) * 100, 2) 
                    : 0
            ],
            'pos' => [
                'total' => $posSales,
                'count' => $posCount,
                'percentage' => $totalSales > 0 
                    ? round(($posSales / $totalSales) * 100, 2) 
                    : 0
            ],
            'total' => $totalSales,
            'total_count' => $onlineCount + $posCount
        ];
    }

    /**
     * Get sales by product (online + POS)
     * 
     * @param string $dateFrom
     * @param string $dateTo
     * @param int|null $outletId
     * @return \Illuminate\Support\Collection
     */
    public static function getProductSales($dateFrom, $dateTo, $outletId = null)
    {
        // Online sales
        $onlineItems = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', [$dateFrom, $dateTo]);

        if ($outletId) {
            $onlineItems->where('orders.outlet_id', $outletId);
        }

        $onlineSales = $onlineItems
            ->select(
                'order_items.product_id',
                DB::raw('SUM(order_items.quantity) as quantity'),
                DB::raw('SUM(order_items.total) as total'),
                DB::raw('COUNT(DISTINCT orders.id) as order_count')
            )
            ->groupBy('order_items.product_id')
            ->get()
            ->keyBy('product_id');

        // POS sales
        $posItems = PosTransactionItem::join('pos_transactions', 'pos_transaction_items.transaction_id', '=', 'pos_transactions.id')
            ->where('pos_transactions.status', 'completed')
            ->whereBetween('pos_transactions.created_at', [$dateFrom, $dateTo]);

        if ($outletId) {
            $posItems->where('pos_transactions.outlet_id', $outletId);
        }

        $posSales = $posItems
            ->select(
                'pos_transaction_items.product_id',
                DB::raw('SUM(pos_transaction_items.quantity) as quantity'),
                DB::raw('SUM(pos_transaction_items.total_amount) as total'),
                DB::raw('COUNT(DISTINCT pos_transactions.id) as transaction_count')
            )
            ->groupBy('pos_transaction_items.product_id')
            ->get()
            ->keyBy('product_id');

        // Merge results
        $allProductIds = $onlineSales->keys()->merge($posSales->keys())->unique();
        
        $merged = $allProductIds->map(function($productId) use ($onlineSales, $posSales) {
            $online = $onlineSales->get($productId);
            $pos = $posSales->get($productId);
            
            $onlineQty = $online->quantity ?? 0;
            $onlineTotal = $online->total ?? 0;
            $onlineCount = $online->order_count ?? 0;
            $posQty = $pos->quantity ?? 0;
            $posTotal = $pos->total ?? 0;
            $posCount = $pos->transaction_count ?? 0;
            
            return [
                'product_id' => $productId,
                'online_quantity' => $onlineQty,
                'online_total' => $onlineTotal,
                'online_count' => $onlineCount,
                'pos_quantity' => $posQty,
                'pos_total' => $posTotal,
                'pos_count' => $posCount,
                'total_quantity' => $onlineQty + $posQty,
                'total_sales' => $onlineTotal + $posTotal,
                'total_count' => $onlineCount + $posCount,
            ];
        })->values()->sortByDesc('total_sales');

        return $merged;
    }

    /**
     * Get sales by category (online + POS)
     * 
     * @param string $dateFrom
     * @param string $dateTo
     * @param int|null $outletId
     * @return \Illuminate\Support\Collection
     */
    public static function getCategorySales($dateFrom, $dateTo, $outletId = null)
    {
        // Online sales by category
        $onlineItems = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', [$dateFrom, $dateTo]);

        if ($outletId) {
            $onlineItems->where('orders.outlet_id', $outletId);
        }

        $onlineSales = $onlineItems
            ->select(
                'categories.id',
                'categories.name',
                DB::raw('SUM(order_items.quantity) as quantity'),
                DB::raw('SUM(order_items.total) as total'),
                DB::raw('COUNT(DISTINCT orders.id) as order_count')
            )
            ->groupBy('categories.id', 'categories.name')
            ->get()
            ->keyBy('id');

        // POS sales by category
        $posItems = PosTransactionItem::join('pos_transactions', 'pos_transaction_items.transaction_id', '=', 'pos_transactions.id')
            ->join('products', 'pos_transaction_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('pos_transactions.status', 'completed')
            ->whereBetween('pos_transactions.created_at', [$dateFrom, $dateTo]);

        if ($outletId) {
            $posItems->where('pos_transactions.outlet_id', $outletId);
        }

        $posSales = $posItems
            ->select(
                'categories.id',
                'categories.name',
                DB::raw('SUM(pos_transaction_items.quantity) as quantity'),
                DB::raw('SUM(pos_transaction_items.total_amount) as total'),
                DB::raw('COUNT(DISTINCT pos_transactions.id) as transaction_count')
            )
            ->groupBy('categories.id', 'categories.name')
            ->get()
            ->keyBy('id');

        // Merge results
        $allCategoryIds = $onlineSales->keys()->merge($posSales->keys())->unique();
        
        $merged = $allCategoryIds->map(function($categoryId) use ($onlineSales, $posSales) {
            $online = $onlineSales->get($categoryId);
            $pos = $posSales->get($categoryId);
            
            $onlineQty = $online->quantity ?? 0;
            $onlineTotal = $online->total ?? 0;
            $onlineCount = $online->order_count ?? 0;
            $posQty = $pos->quantity ?? 0;
            $posTotal = $pos->total ?? 0;
            $posCount = $pos->transaction_count ?? 0;
            
            return [
                'category_id' => $categoryId,
                'category_name' => $online->name ?? $pos->name,
                'online_quantity' => $onlineQty,
                'online_total' => $onlineTotal,
                'online_count' => $onlineCount,
                'pos_quantity' => $posQty,
                'pos_total' => $posTotal,
                'pos_count' => $posCount,
                'total_quantity' => $onlineQty + $posQty,
                'total_sales' => $onlineTotal + $posTotal,
                'total_count' => $onlineCount + $posCount,
            ];
        })->values()->sortByDesc('total_sales');

        return $merged;
    }

    /**
     * Get sales comparison (online vs POS) with daily breakdown
     * 
     * @param string $dateFrom
     * @param string $dateTo
     * @param int|null $outletId
     * @return array
     */
    public static function getSalesComparison($dateFrom, $dateTo, $outletId = null)
    {
        $totalSales = self::getTotalSales($dateFrom, $dateTo, $outletId);
        
        // Daily breakdown
        $dates = [];
        $currentDate = Carbon::parse($dateFrom);
        $endDate = Carbon::parse($dateTo);
        
        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            
            $dailyOnline = Order::where('status', 'completed')
                ->whereDate('created_at', $dateStr);
            if ($outletId) {
                $dailyOnline->where('outlet_id', $outletId);
            }
            $dailyOnlineTotal = $dailyOnline->sum('total_amount');
            $dailyOnlineCount = $dailyOnline->count();
            
            $dailyPos = PosTransaction::where('status', 'completed')
                ->whereDate('created_at', $dateStr);
            if ($outletId) {
                $dailyPos->where('outlet_id', $outletId);
            }
            $dailyPosTotal = $dailyPos->sum('total_amount');
            $dailyPosCount = $dailyPos->count();
            
            $dates[] = [
                'date' => $dateStr,
                'date_formatted' => $currentDate->format('d/m/Y'),
                'online' => $dailyOnlineTotal,
                'online_count' => $dailyOnlineCount,
                'pos' => $dailyPosTotal,
                'pos_count' => $dailyPosCount,
                'total' => $dailyOnlineTotal + $dailyPosTotal,
                'total_count' => $dailyOnlineCount + $dailyPosCount
            ];
            
            $currentDate->addDay();
        }
        
        return [
            'summary' => $totalSales,
            'daily' => $dates
        ];
    }
}
