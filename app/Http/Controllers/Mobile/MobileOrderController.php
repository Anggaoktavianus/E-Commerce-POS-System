<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MobileOrderController extends Controller
{
    /**
     * Encrypt order number for URL
     */
    private function encryptOrderNumber($orderNumber)
    {
        return base64_encode($orderNumber);
    }

    /**
     * Decrypt order number from URL
     */
    private function decryptOrderNumber($encryptedOrderNumber)
    {
        try {
            return base64_decode($encryptedOrderNumber);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Display list of orders
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Order::where('user_id', $user->id)
            ->with('items')
            ->orderBy('created_at', 'desc');
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $orders = $query->paginate(10);
        
        // Get order stats
        $stats = [
            'all' => Order::where('user_id', $user->id)->count(),
            'pending' => Order::where('user_id', $user->id)->where('status', 'pending')->count(),
            'paid' => Order::where('user_id', $user->id)->where('status', 'paid')->count(),
            'processing' => Order::where('user_id', $user->id)->where('status', 'processing')->count(),
            'delivered' => Order::where('user_id', $user->id)->whereIn('status', ['delivered', 'completed'])->count(),
            'cancelled' => Order::where('user_id', $user->id)->where('status', 'cancelled')->count(),
        ];
        
        return view('mobile.transactions', compact('orders', 'stats'));
    }

    /**
     * Display order details
     */
    public function show($orderNumber)
    {
        $decryptedNumber = $this->decryptOrderNumber($orderNumber);
        if (!$decryptedNumber) {
            abort(404, 'Order not found');
        }

        $order = Order::where('order_number', $decryptedNumber)
            ->with(['items.product', 'shippingMethod', 'paymentTransactions'])
            ->first();
        
        if (!$order) {
            abort(404, 'Order not found');
        }

        // Check if user owns this order
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this order');
        }
        
        return view('mobile.order-detail', compact('order'));
    }
}
