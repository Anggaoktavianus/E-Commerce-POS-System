<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $productId = $request->get('product_id');
        $product = $productId ? Product::find($productId) : null;
        
        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('admin.stock-movements.index', compact('product', 'products', 'productId'));
    }

    public function data(Request $request)
    {
        $productId = $request->get('product_id');
        $type = $request->get('type');
        
        $query = StockMovement::with(['product', 'user'])
            ->orderBy('created_at', 'desc');
        
        if ($productId) {
            $query->where('product_id', $productId);
        }
        
        if ($type) {
            $query->where('type', $type);
        }
        
        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('product.name', function($row) {
                return $row->product ? $row->product->name : 'N/A';
            })
            ->editColumn('type', function($row) {
                $badgeColor = match($row->type) {
                    'in' => 'success',
                    'out' => 'danger',
                    'adjustment' => 'info',
                    'restore' => 'warning',
                    default => 'secondary'
                };
                return '<span class="badge bg-'.$badgeColor.'">'.$row->type_label.'</span>';
            })
            ->editColumn('quantity', function($row) {
                $sign = $row->type === 'out' ? '-' : '+';
                $color = $row->type === 'out' ? 'text-danger' : 'text-success';
                return '<span class="'.$color.'">'.$sign.abs($row->quantity).'</span>';
            })
            ->editColumn('old_stock', function($row) {
                return number_format($row->old_stock);
            })
            ->editColumn('new_stock', function($row) {
                return '<strong>'.number_format($row->new_stock).'</strong>';
            })
            ->editColumn('reference_number', function($row) {
                if ($row->reference_number) {
                    return '<a href="'.route('admin.orders.index', ['search' => $row->reference_number]).'" class="text-primary">'.$row->reference_number.'</a>';
                }
                return '-';
            })
            ->editColumn('user.name', function($row) {
                return $row->user ? $row->user->name : 'System';
            })
            ->editColumn('created_at', function($row) {
                return $row->created_at->format('d/m/Y H:i');
            })
            ->addColumn('notes_display', function($row) {
                return $row->notes ? '<small class="text-muted">'.$row->notes.'</small>' : '-';
            })
            ->rawColumns(['type', 'quantity', 'new_stock', 'reference_number', 'notes_display'])
            ->make(true);
    }

    public function show($productId)
    {
        $decodedId = \decode_id((string) $productId);
        abort_if(!$decodedId, 404);
        
        $product = Product::findOrFail($decodedId);
        $movements = StockMovement::where('product_id', $decodedId)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.stock-movements.show', compact('product', 'movements'));
    }

    /**
     * Export stock report to CSV
     */
    public function export(Request $request)
    {
        $productId = $request->get('product_id');
        $type = $request->get('type');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        $query = StockMovement::with(['product', 'user'])
            ->orderBy('created_at', 'desc');
        
        if ($productId) {
            $query->where('product_id', $productId);
        }
        
        if ($type) {
            $query->where('type', $type);
        }
        
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }
        
        $movements = $query->get();
        
        $filename = 'stock_report_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($movements) {
            $file = fopen('php://output', 'w');
            
            // BOM untuk Excel UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            fputcsv($file, [
                'Tanggal',
                'Waktu',
                'Produk',
                'SKU',
                'Tipe',
                'Jumlah',
                'Stok Lama',
                'Stok Baru',
                'Referensi',
                'User',
                'Catatan'
            ]);
            
            // Data
            foreach ($movements as $movement) {
                fputcsv($file, [
                    $movement->created_at->format('Y-m-d'),
                    $movement->created_at->format('H:i:s'),
                    $movement->product ? $movement->product->name : 'N/A',
                    $movement->product ? ($movement->product->sku ?? '-') : '-',
                    $movement->type_label,
                    $movement->type === 'out' ? '-' . abs($movement->quantity) : '+' . abs($movement->quantity),
                    $movement->old_stock,
                    $movement->new_stock,
                    $movement->reference_number ?? '-',
                    $movement->user ? $movement->user->name : 'System',
                    $movement->notes ?? '-'
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export stock summary (current stock per product)
     */
    public function exportSummary(Request $request)
    {
        $storeId = app()->has('current_store') ? app('current_store')->id : null;
        
        $query = Product::with(['category', 'store'])
            ->where('is_active', true)
            ->orderBy('name');
        
        if ($storeId) {
            $query->where('store_id', $storeId);
        }
        
        $products = $query->get();
        
        $filename = 'stock_summary_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            
            // BOM untuk Excel UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            fputcsv($file, [
                'Produk',
                'SKU',
                'Kategori',
                'Store',
                'Stok Tersedia',
                'Unit',
                'Harga',
                'Status Stok'
            ]);
            
            // Data
            foreach ($products as $product) {
                $stockQty = $product->stock_qty ?? 0;
                $status = match(true) {
                    $stockQty <= 0 => 'Habis',
                    $stockQty <= 10 => 'Terbatas',
                    default => 'Tersedia'
                };
                
                fputcsv($file, [
                    $product->name,
                    $product->sku ?? '-',
                    $product->category ? $product->category->name : '-',
                    $product->store ? $product->store->name : '-',
                    $stockQty,
                    $product->unit ?? 'pcs',
                    number_format($product->price ?? 0, 0, ',', '.'),
                    $status
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
