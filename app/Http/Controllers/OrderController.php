<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
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

        $order->load(['items.product', 'paymentTransactions', 'deliveryTracking.driver', 'shippingMethod']);
        
        // Check if instant delivery - show map even if tracking not started yet
        $isInstantDelivery = false;
        if ($order->shippingMethod) {
            $isInstantDelivery = $order->shippingMethod->type === 'instant';
        } else {
            // Fallback: check shipping_method_id if shippingMethod relation not loaded
            $shippingMethod = \App\Models\ShippingMethod::find($order->shipping_method_id);
            if ($shippingMethod) {
                $isInstantDelivery = $shippingMethod->type === 'instant';
            } elseif ($order->shipping_method_id == 1) {
                // Fallback: ID 1 is instant delivery
                $isInstantDelivery = true;
            }
        }
        
        // Auto-create tracking record if instant delivery and not exists
        if ($isInstantDelivery && !$order->deliveryTracking) {
            try {
                \App\Models\DeliveryTracking::create([
                    'order_id' => $order->id,
                    'status' => \App\Models\DeliveryTracking::STATUS_PENDING
                ]);
                $order->load('deliveryTracking.driver');
            } catch (\Exception $e) {
                \Log::warning('Failed to create delivery tracking', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        $hasTracking = $order->deliveryTracking !== null;
        
        return view('orders.track', compact('order', 'hasTracking', 'isInstantDelivery'));
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
                    $item->product->increment('stock_qty', $item->quantity);
                    $item->product->refresh();
                    
                    // Log stock movement
                    \App\Services\StockMovementService::logRestore(
                        $item->product,
                        $item->quantity,
                        \App\Models\Order::class,
                        $order->id,
                        $order->order_number,
                        "Stock dikembalikan karena order dibatalkan oleh customer"
                    );
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
            $cart = \App\Models\Cart::getOrCreateCart($user->id, null);

            foreach ($order->items as $item) {
                // Get product
                $product = \App\Models\Product::find($item->product_id);
                
                if ($product && $product->stock_qty > 0) {
                    // Check if item already in cart
                    $existingCartItem = $cart->items()->where('product_id', $item->product_id)->first();

                    if ($existingCartItem) {
                        // Update quantity if already in cart
                        $newQuantity = $existingCartItem->quantity + $item->quantity;
                        if ($newQuantity <= $product->stock_qty) {
                            $existingCartItem->quantity = $newQuantity;
                            $existingCartItem->save();
                            $addedItems++;
                        }
                    } else {
                        // Add new item to cart
                        $cart->items()->create([
                            'product_id' => $item->product_id,
                            'quantity' => min($item->quantity, $product->stock_qty),
                            'price' => $product->price
                        ]);
                        $addedItems++;
                    }
                }
            }

            if ($addedItems > 0) {
                return response()->json([
                    'success' => true, 
                    'message' => "Berhasil menambahkan {$addedItems} item ke keranjang"
                ]);
            } else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Tidak ada item yang bisa ditambahkan. Produk mungkin habis stok.'
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