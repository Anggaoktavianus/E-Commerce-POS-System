<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
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

    private function putCart(Request $request, array $cart): void
    {
        $user = Auth::user();
        $sessionId = $request->session()->getId();
        
        // If user is logged in, save to database
        if ($user) {
            $dbCart = Cart::getOrCreateCart($user->id, null);
            
            // Clear existing items
            $dbCart->items()->delete();
            
            // Add items from cart array
            foreach ($cart as $productId => $item) {
                $dbCart->items()->create([
                    'product_id' => $productId,
                    'quantity' => $item['qty'] ?? $item['quantity'] ?? 1,
                    'price' => $item['price'] ?? 0,
                ]);
            }
            
            // Always update session cart from database to keep it in sync
            // This ensures session cart is always up-to-date
            $request->session()->put('cart', $dbCart->toSessionArray());
        } else {
            // Save to session for guest users
            $request->session()->put('cart', $cart);
            
            // Also save to database with session_id for potential future merge
            $dbCart = Cart::getOrCreateCart(null, $sessionId);
            $dbCart->items()->delete();
            foreach ($cart as $productId => $item) {
                $dbCart->items()->create([
                    'product_id' => $productId,
                    'quantity' => $item['qty'] ?? $item['quantity'] ?? 1,
                    'price' => $item['price'] ?? 0,
                ]);
            }
        }
    }

    private function recalcTotals(array $cart, ?array $coupon = null): array
    {
        $subtotal = 0.0;
        foreach ($cart as $item) {
            $subtotal += ($item['price'] ?? 0) * ($item['qty'] ?? 0);
        }
        // Shipping tidak dihitung di cart, akan dihitung di checkout
        $shipping = 0.0;
        $discount = 0.0;
        if ($coupon) {
            if (($coupon['type'] ?? '') === 'percent') {
                $discount = round($subtotal * (($coupon['value'] ?? 0) / 100), 2);
            } elseif (($coupon['type'] ?? '') === 'fixed') {
                $discount = min($subtotal, (float) ($coupon['value'] ?? 0));
            }
        }
        // Total di cart = subtotal - discount (tanpa shipping)
        $total = max(0, round($subtotal - $discount, 2));
        return [
            'subtotal' => round($subtotal, 2),
            'shipping' => round($shipping, 2),
            'discount' => round($discount, 2),
            'total' => $total,
        ];
    }

    private function getCoupon(Request $request): ?array
    {
        return $request->session()->get('coupon');
    }

    public function index(Request $request)
    {
        $cart = $this->getCart($request);
        $coupon = $this->getCoupon($request);
        
        // Add product data to cart items for fresh product detection
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
        
        $totals = $this->recalcTotals($cart, $coupon);

        return view('pages.cart', [
            'cart' => $cartWithProducts,
            'coupon' => $coupon,
            'subtotal' => $totals['subtotal'],
            'shipping' => $totals['shipping'],
            'discount' => $totals['discount'],
            'total' => $totals['total'],
        ]);
    }

    public function add(Request $request)
    {
        try {
            // Normalize request data
            $requestData = $request->all();
            if (isset($requestData['id'])) {
                $requestData['id'] = (string)$requestData['id']; // Convert to string
            }
            
            $validated = validator($requestData, [
                'id' => 'required',
                'name' => 'required|string',
                'price' => 'required|numeric|min:0',
                'image' => 'nullable|string',
                'qty' => 'nullable|integer|min:1',
            ])->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Cart add validation error', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                $errorMessages = [];
                foreach ($e->errors() as $field => $messages) {
                    foreach ($messages as $message) {
                        $errorMessages[] = ucfirst($field) . ': ' . $message;
                    }
                }
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal: ' . implode(' | ', $errorMessages),
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        // Cek stok produk - convert id to integer if needed
        $productId = is_numeric($validated['id']) ? (int)$validated['id'] : $validated['id'];
        $product = \App\Models\Product::find($productId);
        if (!$product) {
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan'
                ], 404);
            }
            return redirect()->back()->with('error', 'Produk tidak ditemukan');
        }

        $cart = $this->getCart($request);
        $id = (string)$productId; // Ensure ID is string for cart key consistency
        $qty = (int)($validated['qty'] ?? 1);
        $currentQty = isset($cart[$id]) ? (int)($cart[$id]['qty'] ?? 0) : 0;
        $newQty = $currentQty + $qty;

        // Validasi stok tersedia
        $stockQty = (int)($product->stock_qty ?? 0);
        
        // Jika stok habis (0), tidak boleh add
        if ($stockQty <= 0) {
            $errorMessage = 'Stok produk habis';
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 422);
            }
            return redirect()->back()->with('error', $errorMessage);
        }
        
        // Validasi: pastikan total quantity (current + new) tidak melebihi stok yang tersedia
        if ($newQty > $stockQty) {
            $availableStock = $stockQty;
            $maxCanAdd = max(0, $availableStock - $currentQty);
            
            if ($currentQty > 0) {
                $errorMessage = $maxCanAdd <= 0 
                    ? "Stok tidak mencukupi!\n\nStok tersedia: {$availableStock}\nSudah ada di keranjang: {$currentQty}\n\nTidak bisa menambahkan lagi."
                    : "Stok tidak mencukupi!\n\nStok tersedia: {$availableStock}\nSudah ada di keranjang: {$currentQty}\nMaksimal yang bisa ditambahkan: {$maxCanAdd}";
            } else {
                $errorMessage = "Stok tidak mencukupi!\n\nStok tersedia: {$availableStock}\nYang diminta: {$qty}";
            }
            
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'stock_info' => [
                        'available' => $availableStock,
                        'current_in_cart' => $currentQty,
                        'requested' => $qty,
                        'max_can_add' => $maxCanAdd
                    ]
                ], 422);
            }
            
            return redirect()->back()->with('error', $errorMessage);
        }

        if (isset($cart[$id])) {
            $cart[$id]['qty'] = $newQty;
        } else {
            $cart[$id] = [
                'id' => $id,
                'name' => $validated['name'],
                'price' => (float)$validated['price'],
                'qty' => $qty,
                'image' => $validated['image'] ?? null,
            ];
        }

        $this->putCart($request, $cart);
        
        // Get updated cart count for response
        $updatedCart = $this->getCart($request);
        $cartCount = count($updatedCart);

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan ke keranjang',
                'cart_count' => $cartCount,
                'redirect' => route('cart')
            ]);
        }

        return redirect()->route('cart')->with('success', 'Item added to cart');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|string',
            'qty' => 'required|integer|min:1',
        ]);

        // Cek stok produk
        $productId = is_numeric($validated['id']) ? (int)$validated['id'] : $validated['id'];
        $product = \App\Models\Product::find($productId);
        if (!$product) {
            $errorMessage = 'Produk tidak ditemukan';
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'error' => $errorMessage
                ], 404);
            }
            return redirect()->back()->with('error', $errorMessage);
        }

        $cart = $this->getCart($request);
        $id = (string)$productId;
        $newQty = (int)$validated['qty'];
        $stockQty = (int)($product->stock_qty ?? 0);

        // Jika stok habis (0), tidak boleh update
        if ($stockQty <= 0) {
            $errorMessage = 'Stok produk habis';
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'error' => $errorMessage
                ], 422);
            }
            return redirect()->back()->with('error', $errorMessage);
        }
        
        // Validasi: pastikan quantity yang diminta tidak melebihi stok yang tersedia
        if ($newQty > $stockQty) {
            $errorMessage = 'Stok tidak mencukupi. Stok tersedia: ' . $stockQty . ', yang diminta: ' . $newQty;
            
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'error' => $errorMessage
                ], 422);
            }
            
            return redirect()->back()->with('error', $errorMessage);
        }

        if (isset($cart[$id])) {
            $cart[$id]['qty'] = $newQty;
            $this->putCart($request, $cart);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cart updated',
                'redirect' => route('cart')
            ]);
        }

        return redirect()->route('cart')->with('success', 'Cart updated');
    }

    public function remove(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|string',
        ]);

        $cart = $this->getCart($request);
        unset($cart[$validated['id']]);
        $this->putCart($request, $cart);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item removed',
                'redirect' => route('cart')
            ]);
        }

        return redirect()->route('cart')->with('success', 'Item removed');
    }

    public function clear(Request $request)
    {
        $this->putCart($request, []);
        $request->session()->forget('coupon');
        return redirect()->route('cart')->with('info', 'Cart cleared');
    }

    /**
     * Check stock availability (AJAX endpoint)
     */
    public function checkStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = \App\Models\Product::find($request->product_id);
        
        if (!$product) {
            return response()->json([
                'available' => false,
                'message' => 'Produk tidak ditemukan',
                'stock' => 0
            ], 404);
        }

        $stockQty = $product->stock_qty ?? 0;
        $requestedQty = (int) $request->quantity;
        $available = $stockQty >= $requestedQty;

        // Get current cart quantity if exists
        $cart = $this->getCart($request);
        $currentCartQty = isset($cart[$request->product_id]) ? (int)($cart[$request->product_id]['qty'] ?? 0) : 0;
        $maxCanAdd = max(0, $stockQty - $currentCartQty);

        return response()->json([
            'available' => $available,
            'stock' => $stockQty,
            'requested' => $requestedQty,
            'current_cart_qty' => $currentCartQty,
            'max_can_add' => $maxCanAdd,
            'message' => $available 
                ? 'Stok tersedia' 
                : ($stockQty > 0 
                    ? "Stok tidak mencukupi. Stok tersedia: {$stockQty}, maksimal yang bisa ditambahkan: {$maxCanAdd}"
                    : 'Stok habis'),
            'is_out_of_stock' => $stockQty <= 0,
            'is_low_stock' => $stockQty > 0 && $stockQty <= 10
        ]);
    }

    public function applyCoupon(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
        ]);

        $code = strtoupper(trim($validated['code']));
        $coupon = null;
        // Simple demo coupons
        if ($code === 'SAVE10') {
            $coupon = ['code' => 'SAVE10', 'type' => 'percent', 'value' => 10];
        } elseif ($code === 'FLAT5') {
            $coupon = ['code' => 'FLAT5', 'type' => 'fixed', 'value' => 5];
        }

        if ($coupon) {
            $request->session()->put('coupon', $coupon);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Coupon applied: ' . $coupon['code'],
                    'redirect' => route('cart')
                ]);
            }
            
            return redirect()->route('cart')->with('success', 'Coupon applied: ' . $coupon['code']);
        } else {
            $request->session()->forget('coupon');
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Kode kupon tidak valid'
                ], 422);
            }
            
            return redirect()->route('cart')->with('error', 'Kode kupon tidak valid');
        }
    }
}
