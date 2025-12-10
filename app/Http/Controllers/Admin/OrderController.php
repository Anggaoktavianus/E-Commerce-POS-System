<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    public function index()
    {
        $stores = DB::table('stores')->where('is_active', true)->orderBy('name')->get();
        return view('admin.orders.index', compact('stores'));
    }

    public function data(Request $request)
    {
        $requestedStoreId = $request->get('store_id');
        $storeId = $requestedStoreId ?: (app()->has('current_store') ? app('current_store')->id : 1);
        $query = DB::table('orders')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.store_id', $storeId)
            ->select([
                'orders.id', 'orders.order_number', 'orders.total_amount', 
                'orders.status', 'orders.payment_method', 'orders.created_at',
                'users.name as customer_name', 'users.email'
            ]);

        return DataTables::of($query)
            ->addIndexColumn() // This adds DT_RowIndex
            ->editColumn('total_amount', function($row) {
                return 'IDR ' . number_format($row->total_amount, 0, ',', '.');
            })
            ->editColumn('status', function($row) {
                $statusColors = [
                    'pending' => 'warning',
                    'paid' => 'success',
                    'failed' => 'danger',
                    'cancelled' => 'secondary',
                    'expired' => 'dark'
                ];
                $color = $statusColors[$row->status] ?? 'secondary';
                $statusText = [
                    'pending' => 'Menunggu',
                    'paid' => 'Dibayar',
                    'failed' => 'Gagal',
                    'cancelled' => 'Dibatalkan',
                    'expired' => 'Kadaluarsa'
                ];
                return '<span class="badge bg-' . $color . '">' . ($statusText[$row->status] ?? ucfirst($row->status)) . '</span>';
            })
            ->editColumn('payment_method', function($row) {
                return $row->payment_method ? '<span class="badge bg-info">' . $row->payment_method . '</span>' : '<span class="text-muted">-</span>';
            })
            ->editColumn('created_at', function($row) {
                return date('d M Y H:i', strtotime($row->created_at));
            })
            ->addColumn('actions', function($row){
                $view = route('admin.orders.show', $row->id);
                return view('admin.orders.partials.actions', compact('view', 'row'))->render();
            })
            ->rawColumns(['status', 'payment_method', 'actions'])
            ->make(true);
    }

    public function show($id)
    {
        $storeId = app()->has('current_store') ? app('current_store')->id : 1;
        $order = Order::with(['items', 'paymentTransactions', 'user'])
            ->where('store_id', $storeId)
            ->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function refreshStatus($id)
    {
        try {
            $order = Order::findOrFail($id);
            
            // Check transaction status from Midtrans
            $midtransService = app(\App\Services\MidtransService::class);
            $status = $midtransService->checkTransactionStatus($order->midtrans_order_id);
            
            if ($status['success']) {
                $midtransService->handleNotification($status['data']);
                $order->refresh();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Status berhasil diperbarui',
                    'new_status' => $order->status,
                    'formatted_status' => $order->formatted_status
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memeriksa status: ' . $status['error']
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
}
