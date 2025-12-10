<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingMethod;
use App\Services\MidtransService;
use App\Services\SmartShippingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct(
        private MidtransService $midtransService,
        private SmartShippingService $shippingService
    ) {}

    public function index()
    {
        $cart = session()->get('cart', []);
        $coupon = session()->get('coupon');
        
        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Keranjang belanja kosong');
        }

        // Add product data to cart items
        $cartWithProducts = [];
        foreach ($cart as $id => $item) {
            $product = \App\Models\Product::find($id);
            $cartWithProducts[$id] = $item;
            if ($product) {
                $cartWithProducts[$id]['product'] = $product;
                $cartWithProducts[$id]['name'] = $product->name;
                $cartWithProducts[$id]['price'] = $product->price;
                $cartWithProducts[$id]['image'] = $product->main_image_path;
                $cartWithProducts[$id]['qty'] = $item['quantity'] ?? $item['qty'] ?? 1;
            }
        }

        $totals = $this->calculateTotals($cart, $coupon);
        
        return view('pages.checkout', [
            'cart' => $cartWithProducts,
            'coupon' => $coupon,
            'totals' => $totals,
        ]);
    }

    public function process(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email',
                'phone' => 'required|string|max:20',
                'address' => 'required|string',
                'city' => 'required|string',
                'postal_code' => 'required|string',
                'country' => 'required|string',
            ]);

            $cart = session()->get('cart', []);
            if (empty($cart)) {
                return response()->json(['error' => 'Keranjang belanja kosong'], 400);
            }

            DB::beginTransaction();
            try {
                $storeId = app()->has('current_store') ? app('current_store')->id : 1;
                $outletId = $request->input('outlet_id');
                if ($outletId) {
                    // optional, ensure outlet belongs to store
                    $belongs = \App\Models\Outlet::where('id', $outletId)->where('store_id', $storeId)->exists();
                    if (!$belongs) {
                        return response()->json(['error' => 'Outlet tidak valid untuk store aktif'], 422);
                    }
                }
                // Create order
                $order = Order::create([
                    'store_id' => $storeId,
                    'outlet_id' => $outletId,
                    'order_number' => 'ORD-' . time() . '-' . strtoupper(Str::random(4)),
                    'user_id' => auth()->id(),
                    'subtotal' => $this->calculateSubtotal($cart),
                    'shipping_cost' => $this->calculateShipping(),
                    'discount' => $this->calculateDiscount($cart, session()->get('coupon')),
                    'total_amount' => $this->calculateTotalAmount($cart, session()->get('coupon')),
                    'currency' => 'IDR',
                    'status' => 'pending',
                    'shipping_address' => $validated,
                    'midtrans_order_id' => 'ORDER-' . time() . '-' . (auth()->id() ?? 'guest'),
                ]);

                // Create order items
                foreach ($cart as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['id'],
                        'product_name' => $item['name'],
                        'price' => $item['price'],
                        'quantity' => $item['qty'],
                        'total' => $item['price'] * $item['qty'],
                        'product_details' => [
                            'image' => $item['image'] ?? null,
                            'description' => $item['description'] ?? null,
                        ],
                    ]);
                }

                // Create Midtrans transaction
                $midtransResult = $this->midtransService->createTransaction($order);

                if (!$midtransResult['success']) {
                    throw new \Exception($midtransResult['error']);
                }

                DB::commit();

                // Clear cart
                session()->forget(['cart', 'coupon']);

                return response()->json([
                    'success' => true,
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'snap_token' => $midtransResult['snap_token'],
                    'redirect_url' => $midtransResult['redirect_url'],
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Checkout error: ' . $e->getMessage());
                return response()->json(['error' => $e->getMessage()], 500);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed: ' . implode(', ', $e->errors())], 422);
        }
    }

    public function finish(Request $request)
    {
        try {
            $orderId = $request->query('order_id');
            $statusParam = $request->query('status'); // unfinish, error, atau null (finish)
            
            if (!$orderId) {
                return redirect()->route('home')->with('error', 'Order ID tidak ditemukan');
            }

            $order = Order::findOrFail($orderId);

            // Handle status unfinish/error tanpa cek Midtrans
            if ($statusParam === 'unfinish') {
                \Log::info('Payment unfinish', ['order_id' => $order->id]);
                return view('pages.payment.finish', [
                    'order' => $order,
                    'payment_status' => 'unfinish',
                    'message' => 'Pembayaran dibatalkan. Silakan coba lagi.'
                ]);
            }
            
            if ($statusParam === 'error') {
                \Log::info('Payment error', ['order_id' => $order->id]);
                return view('pages.payment.finish', [
                    'order' => $order,
                    'payment_status' => 'error',
                    'message' => 'Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.'
                ]);
            }

            // Check transaction status from Midtrans (untuk status finish)
            $status = $this->midtransService->checkTransactionStatus($order->midtrans_order_id);
            
            if ($status['success']) {
                // Update order status based on Midtrans response
                $this->midtransService->handleNotification($status['data']);
                $order->refresh();
                
                // Convert status data to array if it's an object
                $statusData = is_object($status['data']) ? (array) $status['data'] : $status['data'];
                
                \Log::info('Payment finish page - status updated', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'midtrans_status' => $statusData['transaction_status'] ?? 'unknown'
                ]);
            } else {
                \Log::warning('Payment finish page - failed to check status', [
                    'order_id' => $order->id,
                    'error' => $status['error'] ?? 'Unknown error'
                ]);
            }

            return view('pages.payment.finish', [
                'order' => $order,
                'status' => $statusData ?? null,
                'payment_status' => 'finish',
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Payment finish page error', [
                'error' => $e->getMessage(),
                'order_id' => $request->query('order_id')
            ]);
            
            return redirect()->route('home')->with('error', 'Terjadi kesalahan saat memproses pembayaran');
        }
    }

    public function notification(Request $request)
    {
        $notification = json_decode($request->getContent(), true);
        
        try {
            // Log notification for debugging
            \Log::info('Midtrans notification received', ['notification' => $notification]);
            
            $order = $this->midtransService->handleNotification($notification);
            
            // Log order status after update
            \Log::info('Order status updated', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'paid_at' => $order->paid_at
            ]);
            
            // Send notification to user
            if ($order->status === 'paid') {
                $this->sendPaymentConfirmation($order);
                
                // Clear cart if user is logged in
                if ($order->user_id && auth()->check() && auth()->id() === $order->user_id) {
                    session()->forget(['cart', 'coupon']);
                }
            }
            
            return response()->json(['status' => 'success', 'order_status' => $order->status]);
        } catch (\Exception $e) {
            \Log::error('Midtrans notification error', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    private function sendPaymentConfirmation($order)
    {
        try {
            // Log untuk debugging
            \Log::info('Sending payment confirmation', ['order_id' => $order->id]);
            
            // Di sini Anda bisa tambahkan:
            // - Email ke customer
            // - SMS notifikasi
            // - WhatsApp notifikasi
            // - Push notification
            
            // Contoh: Kirim email (jika ada mail setup)
            // Mail::to($order->shipping_address['email'] ?? $order->user->email)
            //     ->send(new PaymentConfirmationMail($order));
            
        } catch (\Exception $e) {
            \Log::error('Failed to send payment confirmation', ['error' => $e->getMessage()]);
        }
    }

    private function calculateTotals($cart, $coupon = null)
    {
        $subtotal = $this->calculateSubtotal($cart);
        $shipping = $this->calculateShipping();
        $discount = $this->calculateDiscount($cart, $coupon);
        $total = $subtotal + $shipping - $discount;
        
        return [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'discount' => $discount,
            'total' => $total,
        ];
    }

    private function calculateSubtotal($cart)
    {
        return collect($cart)->sum(function ($item) {
            return $item['price'] * $item['qty'];
        });
    }

    private function calculateShipping()
    {
        return 15000; // Fixed shipping cost
    }

    private function calculateDiscount($cart, $coupon = null)
    {
        if (!$coupon) return 0;
        
        $subtotal = $this->calculateSubtotal($cart);
        
        if ($coupon['type'] === 'percent') {
            return $subtotal * ($coupon['value'] / 100);
        } else {
            return min($subtotal, $coupon['value']);
        }
    }

    private function calculateTotalAmount($cart, $coupon = null)
    {
        $subtotal = $this->calculateSubtotal($cart);
        $shipping = $this->calculateShipping();
        $discount = $this->calculateDiscount($cart, $coupon);
        
        return $subtotal + $shipping - $discount;
    }

    /**
     * Show shipping selection page
     */
    public function shipping()
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Keranjang belanja kosong');
        }

        // Convert cart to proper format
        $cartItems = [];
        foreach ($cart as $productId => $item) {
            $product = \App\Models\Product::find($productId);
            if ($product) {
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity']
                ];
            }
        }

        return view('checkout.shipping', compact('cartItems'));
    }

    /**
     * Process payment with shipping
     */
    public function payment(Request $request)
    {
        $request->validate([
            'destination_city' => 'required|string',
            'shipping_method_id' => 'required|exists:shipping_methods,id',
            'shipping_cost' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0'
        ]);

        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Keranjang belanja kosong');
        }

        // Validate fresh product shipping
        $cartItems = [];
        foreach ($cart as $productId => $item) {
            $product = \App\Models\Product::find($productId);
            if ($product) {
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity']
                ];
            }
        }

        $shippingMethod = ShippingMethod::find($request->shipping_method_id);
        $hasFreshProducts = $this->shippingService->hasFreshProducts($cartItems);

        if ($hasFreshProducts && !in_array($shippingMethod->type, ['instant', 'same_day'])) {
            return back()->with('error', 'Metode pengiriman tidak sesuai untuk produk segar. Silakan pilih pengiriman instan atau same day.');
        }

        // Store shipping info in session
        session([
            'shipping' => [
                'destination_city' => $request->destination_city,
                'shipping_method_id' => $request->shipping_method_id,
                'shipping_cost' => $request->shipping_cost,
                'total_amount' => $request->total_amount
            ]
        ]);

        // Proceed to existing checkout flow
        return $this->processCheckout($request);
    }

    /**
     * Get cart data for AJAX
     */
    public function getCartData()
    {
        $cart = session()->get('cart', []);
        $cartItems = [];

        foreach ($cart as $productId => $item) {
            $product = \App\Models\Product::find($productId);
            if ($product) {
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity']
                ];
            }
        }

        return response()->json([
            'success' => true,
            'items' => $cartItems
        ]);
    }

    /**
     * Process checkout with shipping integration
     */
    private function processCheckout(Request $request)
    {
        $cart = session()->get('cart', []);
        $shipping = session()->get('shipping', []);
        $coupon = session()->get('coupon');

        DB::beginTransaction();
        try {
            $storeId = app()->has('current_store') ? app('current_store')->id : 1;
            $outletId = $request->input('outlet_id');
            if ($outletId) {
                $belongs = \App\Models\Outlet::where('id', $outletId)->where('store_id', $storeId)->exists();
                if (!$belongs) {
                    return back()->with('error', 'Outlet tidak valid untuk store aktif');
                }
            }
            $order = Order::create([
                'store_id' => $storeId,
                'outlet_id' => $outletId,
                'user_id' => auth()->id(),
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'status' => 'pending',
                'subtotal' => $this->calculateSubtotal($cart),
                'shipping_cost' => $shipping['shipping_cost'] ?? 0,
                'discount_amount' => $this->calculateDiscount($cart, $coupon),
                'total_amount' => $shipping['total_amount'] ?? $this->calculateTotalAmount($cart, $coupon),
                'notes' => $request->notes,
                'shipping_method_id' => $shipping['shipping_method_id'] ?? null,
                'estimated_delivery_date' => $this->calculateEstimatedDelivery($shipping['shipping_method_id'] ?? null, $shipping['destination_city'] ?? null),
                'shipping_status' => 'pending'
            ]);

            foreach ($cart as $productId => $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity']
                ]);
            }

            // Create Midtrans transaction
            $transaction = $this->midtransService->createTransaction($order);

            DB::commit();

            session()->forget(['cart', 'coupon', 'shipping']);

            return view('checkout.payment', [
                'snapToken' => $transaction['token'],
                'order' => $order
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memproses pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Calculate estimated delivery date
     */
    private function calculateEstimatedDelivery($shippingMethodId, $destinationCity)
    {
        if (!$shippingMethodId || !$destinationCity) {
            return null;
        }

        $shippingMethod = ShippingMethod::find($shippingMethodId);
        if (!$shippingMethod) {
            return null;
        }

        $cost = $shippingMethod->calculateCost('Semarang', $destinationCity, 1);
        if (!$cost) {
            return null;
        }

        // Parse estimated days and calculate delivery date
        $estimatedDays = $cost->estimated_days;
        
        if (str_contains($estimatedDays, 'menit') || str_contains($estimatedDays, 'jam')) {
            // Same day delivery
            return now()->endOfDay();
        } elseif (str_contains($estimatedDays, '-')) {
            // Range like "1-2" - take the maximum
            $days = explode('-', $estimatedDays)[1];
            return now()->addDays((int)$days);
        } else {
            // Single day
            return now()->addDays((int)$estimatedDays);
        }
    }
}