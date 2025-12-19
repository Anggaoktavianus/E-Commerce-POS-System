<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingMethod;
use App\Models\Cart;
use App\Services\MidtransService;
use App\Services\SmartShippingService;
use App\Services\DistanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct(
        private MidtransService $midtransService,
        private SmartShippingService $shippingService
    ) {}

    /**
     * Get cart data from database or session
     */
    private function getCart(Request $request): array
    {
        $user = Auth::user();
        $sessionId = $request->session()->getId();
        
        // If user is logged in, get cart from database
        if ($user) {
            $cart = Cart::getOrCreateCart($user->id, null);
            if ($cart && $cart->items->count() > 0) {
                return $cart->toSessionArray();
            }
        }
        
        // Fallback to session cart
        $sessionCart = $request->session()->get('cart', []);
        
        // If user is logged in and has session cart, merge it
        if ($user && !empty($sessionCart)) {
            $cart = Cart::getOrCreateCart($user->id, null);
            $cart->mergeWithSessionCart($sessionCart);
            // Clear session cart after merge
            $request->session()->forget('cart');
            return $cart->toSessionArray();
        }
        
        return $sessionCart;
    }

    public function index(Request $request)
    {
        $cart = $this->getCart($request);
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
        
        // Get user's saved information from users table and addresses
        $user = auth()->user();
        $userData = null;
        $selectedAddress = null;
        $userAddresses = collect([]);
        
        if ($user) {
            // Get user addresses
            $userAddresses = \App\Models\UserAddress::where('user_id', $user->id)
                ->where('is_active', true)
                ->orderBy('is_primary', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Get selected address from request or use primary/default
            $addressId = request()->get('address_id');
            if ($addressId) {
                $selectedAddress = $userAddresses->firstWhere('id', $addressId);
            }
            
            if (!$selectedAddress) {
                // Try to get primary address
                $selectedAddress = $userAddresses->firstWhere('is_primary', true);
            }
            
            if (!$selectedAddress && $userAddresses->isNotEmpty()) {
                // Use first address if no primary
                $selectedAddress = $userAddresses->first();
            }
            
            // If no address in user_addresses, fallback to user table
            if (!$selectedAddress) {
                // Get location names
                $locKabkotaName = null;
                $locProvinsiName = null;
                if ($user->loc_kabkota_id) {
                    $locKabkotaName = \DB::table('loc_kabkotas')->where('id', $user->loc_kabkota_id)->value('name');
                }
                if ($user->loc_provinsi_id) {
                    $locProvinsiName = \DB::table('loc_provinsis')->where('id', $user->loc_provinsi_id)->value('name');
                }
                
                $userData = [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'city' => $locKabkotaName ?? $locProvinsiName ?? 'Semarang',
                    'postal_code' => $user->postal_code ?? '',
                    'latitude' => $user->latitude,
                    'longitude' => $user->longitude,
                    'loc_provinsi_name' => $locProvinsiName,
                    'loc_kabkota_name' => $locKabkotaName,
                ];
            } else {
                // Use selected address
                $userData = [
                    'name' => $selectedAddress->recipient_name,
                    'email' => $user->email,
                    'phone' => $selectedAddress->recipient_phone,
                    'address' => $selectedAddress->address,
                    'city' => $selectedAddress->loc_kabkota_name ?? $selectedAddress->city ?? 'Semarang',
                    'postal_code' => $selectedAddress->postal_code ?? '',
                    'latitude' => $selectedAddress->latitude,
                    'longitude' => $selectedAddress->longitude,
                    'loc_provinsi_name' => $selectedAddress->loc_provinsi_name,
                    'loc_kabkota_name' => $selectedAddress->loc_kabkota_name,
                    'address_id' => $selectedAddress->id,
                ];
            }
        }
        
        // Get all active stores with coordinates
        $stores = \App\Models\Store::where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with(['activeOutlets' => function($query) {
                $query->whereNotNull('latitude')
                      ->whereNotNull('longitude')
                      ->where('is_active', true);
            }])
            ->get();
        
        // Get all active outlets with coordinates (for current store or all)
        $currentStore = app()->has('current_store') ? app('current_store') : null;
        $outlets = \App\Models\Outlet::where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->when($currentStore, function($query) use ($currentStore) {
                return $query->where('store_id', $currentStore->id);
            })
            ->with('store')
            ->get();
        
        // Get pickup shipping method ID for frontend
        $pickupMethod = ShippingMethod::where('type', 'pickup')
            ->where('is_active', true)
            ->first();
        
        return view('pages.checkout', [
            'cart' => $cartWithProducts,
            'coupon' => $coupon,
            'totals' => $totals,
            'user' => $userData,
            'selectedAddress' => $selectedAddress,
            'userAddresses' => $userAddresses,
            'stores' => $stores,
            'outlets' => $outlets,
            'pickupMethod' => $pickupMethod,
        ]);
    }

    public function process(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'email' => 'required|email',
                'phone' => 'required|string|max:20',
                'address' => 'required|string',
                'city' => 'required|string',
                'country' => 'required|string',
                'address_id' => 'nullable|integer|exists:user_addresses,id',
            ]);
            
            // If address_id is provided, validate it belongs to the user
            if ($request->address_id) {
                $address = \App\Models\UserAddress::where('id', $request->address_id)
                    ->where('user_id', auth()->id())
                    ->first();
                
                if (!$address) {
                    return response()->json(['error' => 'Alamat tidak valid'], 422);
                }
                
                // Use address data if provided
                $validated['recipient_name'] = $address->recipient_name;
                $validated['recipient_phone'] = $address->recipient_phone;
                $validated['address'] = $address->address;
                $validated['city'] = $address->loc_kabkota_name ?? $address->city;
                $validated['postal_code'] = $address->postal_code;
            }

            $cart = $this->getCart($request);
            if (empty($cart)) {
                return response()->json(['error' => 'Keranjang belanja kosong'], 400);
            }

            // Validasi stok sebelum checkout
            $stockErrors = [];
            foreach ($cart as $id => $item) {
                $product = \App\Models\Product::find($id);
                if (!$product) {
                    $stockErrors[] = "Produk {$item['name']} tidak ditemukan";
                    continue;
                }
                
                $requestedQty = $item['qty'] ?? $item['quantity'] ?? 1;
                $stockQty = (int)($product->stock_qty ?? 0);
                
                // Jika stok habis (0), tidak boleh checkout
                if ($stockQty <= 0) {
                    $stockErrors[] = "Stok {$item['name']} habis";
                } elseif ($requestedQty > $stockQty) {
                    // Validasi: pastikan quantity tidak melebihi stok yang tersedia
                    $stockErrors[] = "Stok {$item['name']} tidak mencukupi. Tersedia: {$stockQty}, Diminta: {$requestedQty}";
                }
            }

            if (!empty($stockErrors)) {
                return response()->json(['error' => implode('. ', $stockErrors)], 400);
            }

            DB::beginTransaction();
            try {
                $storeId = app()->has('current_store') ? app('current_store')->id : 1;
                
                // Handle pickup location (store or outlet)
                $pickupLocationType = $request->input('pickup_location_type');
                $pickupLocationId = $request->input('pickup_location_id');
                $outletId = $request->input('outlet_id');
                
                // If pickup is selected, validate location
                if ($pickupLocationType && $pickupLocationId) {
                    if ($pickupLocationType === 'outlet') {
                        $outletId = $pickupLocationId;
                        // Ensure outlet belongs to store
                        $belongs = \App\Models\Outlet::where('id', $outletId)->where('store_id', $storeId)->exists();
                        if (!$belongs) {
                            return response()->json(['error' => 'Outlet tidak valid untuk store aktif'], 422);
                        }
                    } elseif ($pickupLocationType === 'store') {
                        // Validate store
                        $store = \App\Models\Store::where('id', $pickupLocationId)->where('is_active', true)->first();
                        if (!$store) {
                            return response()->json(['error' => 'Store tidak valid atau tidak aktif'], 422);
                        }
                        $outletId = null; // No outlet for store pickup
                    }
                } elseif ($outletId) {
                    // Legacy support: if outlet_id is provided directly
                    $belongs = \App\Models\Outlet::where('id', $outletId)->where('store_id', $storeId)->exists();
                    if (!$belongs) {
                        return response()->json(['error' => 'Outlet tidak valid untuk store aktif'], 422);
                    }
                }
                // Calculate shipping cost
                $shippingCost = $request->input('shipping_cost', 0);
                $shippingMethodId = $request->input('shipping_method_id');
                
                // Handle pickup method - convert string 'pickup' to actual shipping method ID
                if ($shippingMethodId === 'pickup' || $shippingMethodId === 'Ambil Sendiri') {
                    // Find pickup shipping method from database
                    $pickupMethod = ShippingMethod::where('type', 'pickup')
                        ->where('is_active', true)
                        ->first();
                    
                    if ($pickupMethod) {
                        $shippingMethodId = $pickupMethod->id;
                        $shippingCost = 0; // Pickup is always free
                    } else {
                        // If no pickup method exists, set to null (pickup doesn't require shipping method)
                        $shippingMethodId = null;
                        $shippingCost = 0;
                    }
                }
                
                // Handle instant method - convert string 'instant' to actual shipping method ID
                if ($shippingMethodId === 'instant') {
                    // Find instant shipping method from database
                    $instantMethod = ShippingMethod::where('type', 'instant')
                        ->where('is_active', true)
                        ->first();
                    
                    if ($instantMethod) {
                        $shippingMethodId = $instantMethod->id;
                        // Use the shipping cost calculated in frontend (already provided)
                    } else {
                        // If no instant method exists, try to find by code
                        $instantMethod = ShippingMethod::where('code', 'instant_delivery')
                            ->where('is_active', true)
                            ->first();
                        
                        if ($instantMethod) {
                            $shippingMethodId = $instantMethod->id;
                        } else {
                            // Fallback: set to null if no instant method found
                            $shippingMethodId = null;
                        }
                    }
                }
                
                // If instant delivery, calculate based on distance
                if ($shippingMethodId && is_numeric($shippingMethodId) && $shippingCost > 0) {
                    $shippingMethod = ShippingMethod::find($shippingMethodId);
                    if ($shippingMethod && $shippingMethod->type === 'instant') {
                        // Distance-based cost already calculated in frontend
                        // Use the provided shipping_cost
                    }
                } elseif (!$shippingMethodId || !is_numeric($shippingMethodId)) {
                    // Only calculate shipping if method is not pickup
                    if (!$pickupLocationType) {
                        $shippingCost = $this->calculateShipping();
                    }
                }
                
                // Create order
                $order = Order::create([
                    'store_id' => $storeId,
                    'outlet_id' => $outletId,
                    'order_number' => 'ORD-' . time() . '-' . strtoupper(Str::random(4)),
                    'user_id' => auth()->id(),
                    'subtotal' => $this->calculateSubtotal($cart),
                    'shipping_cost' => $shippingCost,
                    'discount' => $this->calculateDiscount($cart, session()->get('coupon')),
                    'total_amount' => $this->calculateTotalAmount($cart, session()->get('coupon'), $shippingCost),
                    'currency' => 'IDR',
                    'status' => 'pending',
                    'shipping_address' => $validated,
                    'shipping_method_id' => $shippingMethodId,
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

                // Create delivery tracking for instant delivery
                if ($shippingMethodId && is_numeric($shippingMethodId)) {
                    try {
                        $shippingMethod = ShippingMethod::find($shippingMethodId);
                        // Check if instant delivery (by type or by ID = 1 as fallback)
                        $isInstant = false;
                        if ($shippingMethod) {
                            $isInstant = $shippingMethod->type === 'instant';
                        } elseif ($shippingMethodId == 1) {
                            // Fallback: ID 1 is instant delivery
                            $isInstant = true;
                        }
                        
                        if ($isInstant) {
                            \App\Models\DeliveryTracking::firstOrCreate(
                                ['order_id' => $order->id],
                                [
                                    'status' => \App\Models\DeliveryTracking::STATUS_PENDING
                                ]
                            );
                            \Log::info('Delivery tracking created for instant delivery', [
                                'order_id' => $order->id,
                                'order_number' => $order->order_number,
                                'shipping_method_id' => $shippingMethodId,
                                'shipping_method_type' => $shippingMethod ? $shippingMethod->type : 'instant (fallback)'
                            ]);
                        } else {
                            \Log::info('Not creating tracking - not instant delivery', [
                                'order_id' => $order->id,
                                'shipping_method_id' => $shippingMethodId,
                                'shipping_method_type' => $shippingMethod ? $shippingMethod->type : 'null'
                            ]);
                        }
                    } catch (\Exception $e) {
                        \Log::error('Failed to create delivery tracking', [
                            'order_id' => $order->id,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                    }
                } else {
                    \Log::info('Not creating tracking - no shipping method ID', [
                        'order_id' => $order->id,
                        'shipping_method_id' => $shippingMethodId
                    ]);
                }

                // Create Midtrans transaction
                $midtransResult = $this->midtransService->createTransaction($order);

                if (!$midtransResult['success']) {
                    throw new \Exception($midtransResult['error']);
                }

                DB::commit();

                // Jangan hapus cart sekarang, tunggu sampai pembayaran selesai
                // Cart akan dihapus di notification callback atau finish page setelah pembayaran berhasil
                // Hanya hapus coupon dari session
                session()->forget(['coupon']);

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
            // Check if request is from mobile
            $view = $request->is('m/*') || $request->routeIs('mobile.*') ? 'mobile.payment-finish' : 'pages.payment.finish';
            
            return view($view, [
                'order' => $order,
                'payment_status' => 'unfinish',
                'message' => 'Pembayaran dibatalkan. Silakan coba lagi.'
            ]);
            }
            
            if ($statusParam === 'error') {
                \Log::info('Payment error', ['order_id' => $order->id]);
            // Check if request is from mobile
            $view = $request->is('m/*') || $request->routeIs('mobile.*') ? 'mobile.payment-finish' : 'pages.payment.finish';
            
            return view($view, [
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
                
                // Clear cart from database and session if payment is successful
                if ($order->status === 'paid' && $order->user_id) {
                    // Clear from database
                    $userCart = Cart::where('user_id', $order->user_id)->first();
                    if ($userCart) {
                        $userCart->items()->delete();
                        $userCart->delete();
                        \Log::info('Cart cleared from database after payment', [
                            'order_id' => $order->id,
                            'user_id' => $order->user_id
                        ]);
                    }
                    
                    // Clear from session if current user matches
                    if (auth()->check() && auth()->id() === $order->user_id) {
                        session()->forget(['cart', 'coupon']);
                    }
                    
                    // Ensure stock is reduced if order is paid but not yet processed
                    // This handles cases where webhook notification hasn't been received yet or queue worker is not running
                    // Note: ProcessOrderJob might also be dispatched from webhook notification, but it will check processed_at to prevent double processing
                    if (!$order->processed_at) {
                        // Process order directly to ensure stock is immediately reduced
                        // This is safer than relying on queue worker which might not be running
                        $this->processOrderDirectly($order);
                        
                        // Refresh order to get updated processed_at
                        $order->refresh();
                        
                        // Don't dispatch ProcessOrderJob here because:
                        // 1. It might already be dispatched from webhook notification
                        // 2. Job will check processed_at and skip if already processed
                        // 3. We already processed the order directly, so stock is reduced
                        // If email needs to be sent, it can be done separately or in the job
                    } else {
                        \Log::info('Order already processed, skipping', [
                            'order_id' => $order->id,
                            'processed_at' => $order->processed_at
                        ]);
                    }
                }
            } else {
                \Log::warning('Payment finish page - failed to check status', [
                    'order_id' => $order->id,
                    'error' => $status['error'] ?? 'Unknown error'
                ]);
            }

            // Check if request is from mobile
            $view = $request->is('m/*') || $request->routeIs('mobile.*') ? 'mobile.payment-finish' : 'pages.payment.finish';
            
            return view($view, [
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

    /**
     * Process order directly (fallback if queue fails or for immediate processing)
     */
    private function processOrderDirectly(Order $order)
    {
        try {
            \Log::info('Processing order directly', [
                'order_id' => $order->id,
                'order_number' => $order->order_number
            ]);

            // Refresh order to get latest status
            $order->refresh();

            // Check if already processed
            if ($order->processed_at) {
                \Log::info('Order already processed, skipping direct processing', [
                    'order_id' => $order->id,
                    'processed_at' => $order->processed_at
                ]);
                return;
            }

            // Check if order is paid
            if ($order->status !== 'paid') {
                \Log::warning('Order is not paid, cannot process directly', [
                    'order_id' => $order->id,
                    'status' => $order->status
                ]);
                return;
            }

            // Use DB transaction to ensure atomicity
            DB::beginTransaction();
            try {
                // Reload order with items and products relationship
                $order->load(['items.product']);
                
                // Double-check processed_at after reload (prevent race condition)
                if ($order->processed_at) {
                    DB::rollBack();
                    \Log::info('Order already processed, aborting direct processing', [
                        'order_id' => $order->id
                    ]);
                    return;
                }
                
                // Reduce stock for each order item
                foreach ($order->items as $item) {
                    // Try to get product by ID if relationship fails
                    $product = $item->product;
                    if (!$product && $item->product_id) {
                        $product = \App\Models\Product::find($item->product_id);
                    }
                    
                    if ($product) {
                        $oldStock = $product->stock_qty ?? 0;
                        $product->decrement('stock_qty', $item->quantity);
                        $product->refresh();
                        $newStock = $product->stock_qty ?? 0;

                        // Log stock movement
                        \App\Services\StockMovementService::logDecrease(
                            $product,
                            $item->quantity,
                            Order::class,
                            $order->id,
                            $order->order_number,
                            "Stock keluar untuk order #{$order->order_number}"
                        );

                        \Log::info('Stock updated directly', [
                            'product_id' => $item->product_id,
                            'product_name' => $item->product_name ?? $product->name,
                            'quantity' => $item->quantity,
                            'old_stock' => $oldStock,
                            'new_stock' => $newStock
                        ]);
                    } else {
                        \Log::warning('Product not found for order item', [
                            'order_item_id' => $item->id,
                            'product_id' => $item->product_id,
                            'product_name' => $item->product_name ?? 'N/A'
                        ]);
                    }
                }
                
                // Update order status
                $order->update([
                    'status' => 'processing',
                    'processed_at' => now()
                ]);
                
                DB::commit();
                
                \Log::info('Order processed directly successfully', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Failed to process order directly', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

        } catch (\Exception $e) {
            \Log::error('Failed to process order directly', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
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
                
                // Clear cart from database and session if user is logged in
                if ($order->user_id) {
                    // Clear from database
                    $userCart = Cart::where('user_id', $order->user_id)->first();
                    if ($userCart) {
                        $userCart->items()->delete();
                        $userCart->delete();
                    }
                    
                    // Clear from session if current user matches
                    if (auth()->check() && auth()->id() === $order->user_id) {
                        session()->forget(['cart', 'coupon']);
                    }
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
            $qty = $item['qty'] ?? $item['quantity'] ?? 1;
            $price = $item['price'] ?? 0;
            return $price * $qty;
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

    private function calculateTotalAmount($cart, $coupon = null, $shippingCost = null)
    {
        $subtotal = $this->calculateSubtotal($cart);
        $shipping = $shippingCost ?? $this->calculateShipping();
        $discount = $this->calculateDiscount($cart, $coupon);
        
        return $subtotal + $shipping - $discount;
    }

    /**
     * Show shipping selection page
     */
    public function shipping(Request $request)
    {
        $cart = $this->getCart($request);
        
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
                    'quantity' => $item['qty'] ?? $item['quantity'] ?? 1
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

        $cart = $this->getCart($request);
        
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
                    'quantity' => $item['qty'] ?? $item['quantity'] ?? 1
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
    public function getCartData(Request $request)
    {
        $cart = $this->getCart($request);
        $cartItems = [];

        foreach ($cart as $productId => $item) {
            $product = \App\Models\Product::find($productId);
            if ($product) {
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $item['qty'] ?? $item['quantity'] ?? 1
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
        $cart = $this->getCart($request);
        $shipping = session()->get('shipping', []);
        $coupon = session()->get('coupon');

        // Validasi stok sebelum checkout
        $stockErrors = [];
        foreach ($cart as $productId => $item) {
            $product = \App\Models\Product::find($productId);
            if (!$product) {
                $stockErrors[] = "Produk tidak ditemukan";
                continue;
            }
            
            $requestedQty = $item['qty'] ?? $item['quantity'] ?? 1;
            $stockQty = (int)($product->stock_qty ?? 0);
            
            // Jika stok habis (0), tidak boleh checkout
            if ($stockQty <= 0) {
                $stockErrors[] = "Stok produk habis";
            } elseif ($requestedQty > $stockQty) {
                // Validasi: pastikan quantity tidak melebihi stok yang tersedia
                $stockErrors[] = "Stok tidak mencukupi. Tersedia: {$stockQty}, Diminta: {$requestedQty}";
            }
        }

        if (!empty($stockErrors)) {
            return back()->with('error', implode('. ', $stockErrors));
        }

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
                $qty = $item['qty'] ?? $item['quantity'] ?? 1;
                $price = $item['price'] ?? 0;
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $qty,
                    'price' => $price,
                    'subtotal' => $price * $qty
                ]);
            }

            // Create delivery tracking for instant delivery
            $shippingMethodId = $shipping['shipping_method_id'] ?? null;
            if ($shippingMethodId && is_numeric($shippingMethodId)) {
                try {
                    $shippingMethod = ShippingMethod::find($shippingMethodId);
                    if ($shippingMethod && $shippingMethod->type === 'instant') {
                        \App\Models\DeliveryTracking::firstOrCreate(
                            ['order_id' => $order->id],
                            [
                                'status' => \App\Models\DeliveryTracking::STATUS_PENDING
                            ]
                        );
                        \Log::info('Delivery tracking created for instant delivery', [
                            'order_id' => $order->id,
                            'order_number' => $order->order_number,
                            'shipping_method_id' => $shippingMethodId,
                            'shipping_method_type' => $shippingMethod->type
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to create delivery tracking', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Create Midtrans transaction
            $transaction = $this->midtransService->createTransaction($order);

            // Jangan hapus cart sekarang, tunggu sampai pembayaran selesai
            // Cart akan dihapus di notification callback atau finish page setelah pembayaran berhasil

            DB::commit();

            // Hanya hapus session cart, biarkan cart di database tetap ada sampai pembayaran selesai
            session()->forget(['coupon', 'shipping']);

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