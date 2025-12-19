<?php

namespace App\Http\Controllers\Admin\Pos;

use App\Http\Controllers\Controller;
use App\Models\PosTransaction;
use App\Models\PosTransactionItem;
use App\Models\PosShift;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PosReportController extends Controller
{
    /**
     * Daily sales report
     */
    public function daily(Request $request)
    {
        $outletId = $request->get('outlet_id') ?? session('pos_outlet_id');
        $date = $request->get('date', today()->format('Y-m-d'));
        $outlets = Outlet::where('is_active', true)->get();

        $query = PosTransaction::where('status', 'completed')
            ->whereDate('created_at', $date);

        if ($outletId) {
            $query->where('outlet_id', $outletId);
        }

        $transactions = $query->with(['outlet', 'user', 'customer', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate totals
        $totalSales = $transactions->sum('total_amount');
        $totalTransactions = $transactions->count();
        $totalItems = $transactions->sum(function($t) {
            return $t->items->sum('quantity');
        });

        // Sales by payment method
        $salesByPayment = $transactions->groupBy('payment_method')
            ->map(function($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('total_amount')
                ];
            });

        // Sales by hour
        $salesByHour = $transactions->groupBy(function($t) {
            return $t->created_at->format('H:00');
        })->map(function($group) {
            return $group->sum('total_amount');
        })->sortKeys();

        $selectedOutlet = $outletId ? Outlet::find($outletId) : null;

        return view('admin.pos.reports.daily', compact(
            'outlets',
            'outletId',
            'date',
            'transactions',
            'totalSales',
            'totalTransactions',
            'totalItems',
            'salesByPayment',
            'salesByHour',
            'selectedOutlet'
        ));
    }

    /**
     * Product sales report
     */
    public function product(Request $request)
    {
        $outletId = $request->get('outlet_id') ?? session('pos_outlet_id');
        $dateFrom = $request->get('date_from', today()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', today()->format('Y-m-d'));
        $outlets = Outlet::where('is_active', true)->get();

        $query = PosTransactionItem::whereHas('transaction', function($q) use ($outletId, $dateFrom, $dateTo) {
            $q->where('status', 'completed')
                ->whereDate('created_at', '>=', $dateFrom)
                ->whereDate('created_at', '<=', $dateTo);
            
            if ($outletId) {
                $q->where('outlet_id', $outletId);
            }
        });

        $productSales = $query->select(
                'product_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(total_amount) as total_sales'),
                DB::raw('COUNT(DISTINCT transaction_id) as transaction_count')
            )
            ->with('product')
            ->groupBy('product_id')
            ->orderBy('total_sales', 'desc')
            ->get();

        $selectedOutlet = $outletId ? Outlet::find($outletId) : null;

        return view('admin.pos.reports.product', compact(
            'outlets',
            'outletId',
            'dateFrom',
            'dateTo',
            'productSales',
            'selectedOutlet'
        ));
    }

    /**
     * Category sales report
     */
    public function category(Request $request)
    {
        $outletId = $request->get('outlet_id') ?? session('pos_outlet_id');
        $dateFrom = $request->get('date_from', today()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', today()->format('Y-m-d'));
        $outlets = Outlet::where('is_active', true)->get();

        $query = PosTransactionItem::whereHas('transaction', function($q) use ($outletId, $dateFrom, $dateTo) {
            $q->where('status', 'completed')
                ->whereDate('created_at', '>=', $dateFrom)
                ->whereDate('created_at', '<=', $dateTo);
            
            if ($outletId) {
                $q->where('outlet_id', $outletId);
            }
        })
        ->join('products', 'pos_transaction_items.product_id', '=', 'products.id')
        ->join('categories', 'products.category_id', '=', 'categories.id')
        ->select(
            'categories.id',
            'categories.name',
            DB::raw('SUM(pos_transaction_items.quantity) as total_quantity'),
            DB::raw('SUM(pos_transaction_items.total_amount) as total_sales'),
            DB::raw('COUNT(DISTINCT pos_transaction_items.transaction_id) as transaction_count')
        )
        ->groupBy('categories.id', 'categories.name')
        ->orderBy('total_sales', 'desc');

        $categorySales = $query->get();

        $selectedOutlet = $outletId ? Outlet::find($outletId) : null;

        return view('admin.pos.reports.category', compact(
            'outlets',
            'outletId',
            'dateFrom',
            'dateTo',
            'categorySales',
            'selectedOutlet'
        ));
    }

    /**
     * Payment method report
     */
    public function payment(Request $request)
    {
        $outletId = $request->get('outlet_id') ?? session('pos_outlet_id');
        $dateFrom = $request->get('date_from', today()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', today()->format('Y-m-d'));
        $outlets = Outlet::where('is_active', true)->get();

        $query = PosTransaction::where('status', 'completed')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo);

        if ($outletId) {
            $query->where('outlet_id', $outletId);
        }

        $paymentStats = $query->select(
                'payment_method',
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(total_amount) as total_amount'),
                DB::raw('AVG(total_amount) as avg_amount')
            )
            ->groupBy('payment_method')
            ->orderBy('total_amount', 'desc')
            ->get();

        $totalSales = $paymentStats->sum('total_amount');
        $totalTransactions = $paymentStats->sum('transaction_count');

        $selectedOutlet = $outletId ? Outlet::find($outletId) : null;

        return view('admin.pos.reports.payment', compact(
            'outlets',
            'outletId',
            'dateFrom',
            'dateTo',
            'paymentStats',
            'totalSales',
            'totalTransactions',
            'selectedOutlet'
        ));
    }

    /**
     * Cashier performance report
     */
    public function cashier(Request $request)
    {
        $outletId = $request->get('outlet_id') ?? session('pos_outlet_id');
        $dateFrom = $request->get('date_from', today()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', today()->format('Y-m-d'));
        $outlets = Outlet::where('is_active', true)->get();

        $query = PosTransaction::where('status', 'completed')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo);

        if ($outletId) {
            $query->where('outlet_id', $outletId);
        }

        $cashierStats = $query->select(
                'user_id',
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(total_amount) as total_sales'),
                DB::raw('AVG(total_amount) as avg_transaction'),
                DB::raw('MIN(total_amount) as min_transaction'),
                DB::raw('MAX(total_amount) as max_transaction')
            )
            ->with('user')
            ->groupBy('user_id')
            ->orderBy('total_sales', 'desc')
            ->get();

        $selectedOutlet = $outletId ? Outlet::find($outletId) : null;

        return view('admin.pos.reports.cashier', compact(
            'outlets',
            'outletId',
            'dateFrom',
            'dateTo',
            'cashierStats',
            'selectedOutlet'
        ));
    }

    /**
     * Export report to CSV
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'daily'); // daily, product, category, payment, cashier
        $outletId = $request->get('outlet_id');
        $date = $request->get('date', today()->format('Y-m-d'));
        $dateFrom = $request->get('date_from', today()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', today()->format('Y-m-d'));

        $filename = 'pos_report_' . $type . '_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($type, $outletId, $date, $dateFrom, $dateTo) {
            $file = fopen('php://output', 'w');
            
            // BOM untuk Excel UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            switch ($type) {
                case 'daily':
                    $this->exportDaily($file, $outletId, $date);
                    break;
                case 'product':
                    $this->exportProduct($file, $outletId, $dateFrom, $dateTo);
                    break;
                case 'category':
                    $this->exportCategory($file, $outletId, $dateFrom, $dateTo);
                    break;
                case 'payment':
                    $this->exportPayment($file, $outletId, $dateFrom, $dateTo);
                    break;
                case 'cashier':
                    $this->exportCashier($file, $outletId, $dateFrom, $dateTo);
                    break;
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export daily sales
     */
    private function exportDaily($file, $outletId, $date)
    {
        fputcsv($file, ['Laporan Penjualan Harian - ' . Carbon::parse($date)->format('d/m/Y')]);
        fputcsv($file, []);

        $query = PosTransaction::where('status', 'completed')
            ->whereDate('created_at', $date);

        if ($outletId) {
            $query->where('outlet_id', $outletId);
        }

        $transactions = $query->with(['outlet', 'user', 'customer'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Header
        fputcsv($file, [
            'No',
            'Tanggal',
            'Waktu',
            'No. Transaksi',
            'Outlet',
            'Kasir',
            'Customer',
            'Metode Pembayaran',
            'Total'
        ]);

        // Data
        $no = 1;
        foreach ($transactions as $transaction) {
            fputcsv($file, [
                $no++,
                $transaction->created_at->format('Y-m-d'),
                $transaction->created_at->format('H:i:s'),
                $transaction->transaction_number,
                $transaction->outlet->name ?? '-',
                $transaction->user->name ?? '-',
                $transaction->customer->name ?? 'Walk-in',
                ucfirst($transaction->payment_method),
                number_format($transaction->total_amount, 0, ',', '.')
            ]);
        }

        fputcsv($file, []);
        fputcsv($file, ['Total Transaksi', $transactions->count()]);
        fputcsv($file, ['Total Penjualan', number_format($transactions->sum('total_amount'), 0, ',', '.')]);
    }

    /**
     * Export product sales
     */
    private function exportProduct($file, $outletId, $dateFrom, $dateTo)
    {
        fputcsv($file, ['Laporan Penjualan Produk']);
        fputcsv($file, ['Periode: ' . Carbon::parse($dateFrom)->format('d/m/Y') . ' - ' . Carbon::parse($dateTo)->format('d/m/Y')]);
        fputcsv($file, []);

        $query = PosTransactionItem::whereHas('transaction', function($q) use ($outletId, $dateFrom, $dateTo) {
            $q->where('status', 'completed')
                ->whereDate('created_at', '>=', $dateFrom)
                ->whereDate('created_at', '<=', $dateTo);
            
            if ($outletId) {
                $q->where('outlet_id', $outletId);
            }
        });

        $productSales = $query->select(
                'product_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(total_amount) as total_sales'),
                DB::raw('COUNT(DISTINCT transaction_id) as transaction_count')
            )
            ->with('product')
            ->groupBy('product_id')
            ->orderBy('total_sales', 'desc')
            ->get();

        fputcsv($file, [
            'No',
            'Produk',
            'SKU',
            'Jumlah Terjual',
            'Total Penjualan',
            'Jumlah Transaksi'
        ]);

        $no = 1;
        foreach ($productSales as $item) {
            fputcsv($file, [
                $no++,
                $item->product->name ?? '-',
                $item->product->sku ?? '-',
                $item->total_quantity,
                number_format($item->total_sales, 0, ',', '.'),
                $item->transaction_count
            ]);
        }
    }

    /**
     * Export category sales
     */
    private function exportCategory($file, $outletId, $dateFrom, $dateTo)
    {
        fputcsv($file, ['Laporan Penjualan Kategori']);
        fputcsv($file, ['Periode: ' . Carbon::parse($dateFrom)->format('d/m/Y') . ' - ' . Carbon::parse($dateTo)->format('d/m/Y')]);
        fputcsv($file, []);

        $query = PosTransactionItem::whereHas('transaction', function($q) use ($outletId, $dateFrom, $dateTo) {
            $q->where('status', 'completed')
                ->whereDate('created_at', '>=', $dateFrom)
                ->whereDate('created_at', '<=', $dateTo);
            
            if ($outletId) {
                $q->where('outlet_id', $outletId);
            }
        })
        ->join('products', 'pos_transaction_items.product_id', '=', 'products.id')
        ->join('categories', 'products.category_id', '=', 'categories.id')
        ->select(
            'categories.id',
            'categories.name',
            DB::raw('SUM(pos_transaction_items.quantity) as total_quantity'),
            DB::raw('SUM(pos_transaction_items.total_amount) as total_sales'),
            DB::raw('COUNT(DISTINCT pos_transaction_items.transaction_id) as transaction_count')
        )
        ->groupBy('categories.id', 'categories.name')
        ->orderBy('total_sales', 'desc');

        $categorySales = $query->get();

        fputcsv($file, [
            'No',
            'Kategori',
            'Jumlah Terjual',
            'Total Penjualan',
            'Jumlah Transaksi'
        ]);

        $no = 1;
        foreach ($categorySales as $item) {
            fputcsv($file, [
                $no++,
                $item->name,
                $item->total_quantity,
                number_format($item->total_sales, 0, ',', '.'),
                $item->transaction_count
            ]);
        }
    }

    /**
     * Export payment method
     */
    private function exportPayment($file, $outletId, $dateFrom, $dateTo)
    {
        fputcsv($file, ['Laporan Metode Pembayaran']);
        fputcsv($file, ['Periode: ' . Carbon::parse($dateFrom)->format('d/m/Y') . ' - ' . Carbon::parse($dateTo)->format('d/m/Y')]);
        fputcsv($file, []);

        $query = PosTransaction::where('status', 'completed')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo);

        if ($outletId) {
            $query->where('outlet_id', $outletId);
        }

        $paymentStats = $query->select(
                'payment_method',
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(total_amount) as total_amount'),
                DB::raw('AVG(total_amount) as avg_amount')
            )
            ->groupBy('payment_method')
            ->orderBy('total_amount', 'desc')
            ->get();

        fputcsv($file, [
            'Metode Pembayaran',
            'Jumlah Transaksi',
            'Total Penjualan',
            'Rata-rata Transaksi'
        ]);

        foreach ($paymentStats as $stat) {
            fputcsv($file, [
                ucfirst($stat->payment_method),
                $stat->transaction_count,
                number_format($stat->total_amount, 0, ',', '.'),
                number_format($stat->avg_amount, 0, ',', '.')
            ]);
        }
    }

    /**
     * Export cashier performance
     */
    private function exportCashier($file, $outletId, $dateFrom, $dateTo)
    {
        fputcsv($file, ['Laporan Kinerja Kasir']);
        fputcsv($file, ['Periode: ' . Carbon::parse($dateFrom)->format('d/m/Y') . ' - ' . Carbon::parse($dateTo)->format('d/m/Y')]);
        fputcsv($file, []);

        $query = PosTransaction::where('status', 'completed')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo);

        if ($outletId) {
            $query->where('outlet_id', $outletId);
        }

        $cashierStats = $query->select(
                'user_id',
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(total_amount) as total_sales'),
                DB::raw('AVG(total_amount) as avg_transaction'),
                DB::raw('MIN(total_amount) as min_transaction'),
                DB::raw('MAX(total_amount) as max_transaction')
            )
            ->with('user')
            ->groupBy('user_id')
            ->orderBy('total_sales', 'desc')
            ->get();

        fputcsv($file, [
            'No',
            'Kasir',
            'Jumlah Transaksi',
            'Total Penjualan',
            'Rata-rata Transaksi',
            'Transaksi Terkecil',
            'Transaksi Terbesar'
        ]);

        $no = 1;
        foreach ($cashierStats as $stat) {
            fputcsv($file, [
                $no++,
                $stat->user->name ?? '-',
                $stat->transaction_count,
                number_format($stat->total_sales, 0, ',', '.'),
                number_format($stat->avg_transaction, 0, ',', '.'),
                number_format($stat->min_transaction, 0, ',', '.'),
                number_format($stat->max_transaction, 0, ',', '.')
            ]);
        }
    }
}
