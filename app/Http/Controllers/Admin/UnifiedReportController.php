<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\UnifiedSalesReportService;
use App\Models\Outlet;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UnifiedReportController extends Controller
{
    /**
     * Unified sales dashboard
     */
    public function index(Request $request)
    {
        $outletId = $request->get('outlet_id');
        $dateFrom = $request->get('date_from', today()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', today()->format('Y-m-d'));
        $outlets = Outlet::where('is_active', true)->get();

        $totalSales = UnifiedSalesReportService::getTotalSales($dateFrom, $dateTo, $outletId);
        $comparison = UnifiedSalesReportService::getSalesComparison($dateFrom, $dateTo, $outletId);

        $selectedOutlet = $outletId ? Outlet::find($outletId) : null;

        return view('admin.reports.unified.index', compact(
            'outlets',
            'outletId',
            'dateFrom',
            'dateTo',
            'totalSales',
            'comparison',
            'selectedOutlet'
        ));
    }

    /**
     * Unified product sales
     */
    public function products(Request $request)
    {
        $outletId = $request->get('outlet_id');
        $dateFrom = $request->get('date_from', today()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', today()->format('Y-m-d'));
        $outlets = Outlet::where('is_active', true)->get();

        $productSales = UnifiedSalesReportService::getProductSales($dateFrom, $dateTo, $outletId);
        
        // Load product details
        $productIds = $productSales->pluck('product_id')->unique();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        
        $productSales = $productSales->map(function($item) use ($products) {
            $product = $products->get($item['product_id']);
            $item['product'] = $product;
            return $item;
        });

        $selectedOutlet = $outletId ? Outlet::find($outletId) : null;

        return view('admin.reports.unified.products', compact(
            'outlets',
            'outletId',
            'dateFrom',
            'dateTo',
            'productSales',
            'selectedOutlet'
        ));
    }

    /**
     * Unified category sales
     */
    public function categories(Request $request)
    {
        $outletId = $request->get('outlet_id');
        $dateFrom = $request->get('date_from', today()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', today()->format('Y-m-d'));
        $outlets = Outlet::where('is_active', true)->get();

        $categorySales = UnifiedSalesReportService::getCategorySales($dateFrom, $dateTo, $outletId);

        $selectedOutlet = $outletId ? Outlet::find($outletId) : null;

        return view('admin.reports.unified.categories', compact(
            'outlets',
            'outletId',
            'dateFrom',
            'dateTo',
            'categorySales',
            'selectedOutlet'
        ));
    }
}
