<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class OrderController extends Controller
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
     * Find order by encrypted order number
     */
    private function findOrderByNumber($orderNumber)
    {
        $decryptedNumber = $this->decryptOrderNumber($orderNumber);
        if (!$decryptedNumber) {
            return null;
        }

        return Order::where('order_number', $decryptedNumber)->first();
    }

    /**
     * Test method to verify controller works
     */
    public function test()
    {
        return response()->json(['message' => 'OrderController is working!']);
    }

    /**
     * Display order details
     */
    public function show($orderNumber)
    {
        $order = $this->findOrderByNumber($orderNumber);
        
        if (!$order) {
            abort(404, 'Order not found');
        }

        // Check if user owns this order
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this order');
        }

        $order->load(['items.product', 'paymentTransactions']);
        
        return view('orders.show', compact('order'));
    }

    /**
     * Show order tracking page
     */
    public function track($orderNumber)
    {
        $order = $this->findOrderByNumber($orderNumber);
        
        if (!$order) {
            abort(404, 'Order not found');
        }

        // Check if user owns this order
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this order');
        }

        $order->load(['items.product', 'paymentTransactions']);
        
        return view('orders.track', compact('order'));
    }

    /**
     * Download order invoice PDF
     */
    public function invoice($orderNumber)
    {
        $order = $this->findOrderByNumber($orderNumber);
        
        if (!$order) {
            abort(404, 'Order not found');
        }

        // Check if user owns this order
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this order');
        }

        $order->load(['items.product', 'paymentTransactions']);
        
        // For now, return a simple HTML view instead of PDF
        return view('orders.invoice', compact('order'));
    }

    /**
     * Cancel an order
     */
    public function cancel($orderNumber, Request $request)
    {
        $order = $this->findOrderByNumber($orderNumber);
        
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        // Check if user owns this order
        if ($order->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }

        // Check if order can be cancelled
        if (!in_array($order->status, ['pending', 'paid'])) {
            return response()->json(['success' => false, 'message' => 'Order cannot be cancelled at this stage']);
        }

        try {
            // Update order status
            $order->status = 'cancelled';
            $order->cancelled_at = now();
            $order->cancel_reason = $request->input('reason', 'Cancelled by customer');
            $order->save();

            // Restore stock if needed
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->stock += $item->quantity;
                    $item->product->save();
                }
            }

            return response()->json([
                'success' => true, 
                'message' => 'Order cancelled successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Failed to cancel order: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Reorder items from a previous order
     */
    public function reorder($orderNumber, Request $request)
    {
        $order = $this->findOrderByNumber($orderNumber);
        
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        // Check if user owns this order
        if ($order->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }

        try {
            $user = Auth::user();
            $addedItems = 0;

            foreach ($order->items as $item) {
                // Check if product still exists and has stock
                if ($item->product && $item->product->stock > 0) {
                    // Check if item already in cart
                    $existingCartItem = Cart::where('user_id', $user->id)
                        ->where('product_id', $item->product_id)
                        ->first();

                    if ($existingCartItem) {
                        // Update quantity if already in cart
                        $newQuantity = $existingCartItem->quantity + $item->quantity;
                        if ($newQuantity <= $item->product->stock) {
                            $existingCartItem->quantity = $newQuantity;
                            $existingCartItem->save();
                            $addedItems++;
                        }
                    } else {
                        // Add new item to cart
                        Cart::create([
                            'user_id' => $user->id,
                            'product_id' => $item->product_id,
                            'quantity' => min($item->quantity, $item->product->stock),
                            'price' => $item->product->price
                        ]);
                        $addedItems++;
                    }
                }
            }

            if ($addedItems > 0) {
                return response()->json([
                    'success' => true, 
                    'message' => "Successfully added {$addedItems} items to cart"
                ]);
            } else {
                return response()->json([
                    'success' => false, 
                    'message' => 'No items could be added to cart. Products may be out of stock.'
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Failed to reorder items: ' . $e->getMessage()
            ]);
        }
    }
}