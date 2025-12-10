<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CartController extends Controller
{
    private function getCart(Request $request): array
    {
        return $request->session()->get('cart', []);
    }

    private function putCart(Request $request, array $cart): void
    {
        $request->session()->put('cart', $cart);
    }

    private function recalcTotals(array $cart, ?array $coupon = null): array
    {
        $subtotal = 0.0;
        foreach ($cart as $item) {
            $subtotal += ($item['price'] ?? 0) * ($item['qty'] ?? 0);
        }
        $shipping = $subtotal > 0 ? 3.0 : 0.0;
        $discount = 0.0;
        if ($coupon) {
            if (($coupon['type'] ?? '') === 'percent') {
                $discount = round($subtotal * (($coupon['value'] ?? 0) / 100), 2);
            } elseif (($coupon['type'] ?? '') === 'fixed') {
                $discount = min($subtotal, (float) ($coupon['value'] ?? 0));
            }
        }
        $total = max(0, round($subtotal - $discount + $shipping, 2));
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
        $validated = $request->validate([
            'id' => 'required|string',
            'name' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|string',
            'qty' => 'nullable|integer|min:1',
        ]);

        $cart = $this->getCart($request);
        $id = $validated['id'];
        $qty = (int)($validated['qty'] ?? 1);

        if (isset($cart[$id])) {
            $cart[$id]['qty'] += $qty;
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

        return redirect()->route('cart')->with('success', 'Item added to cart');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|string',
            'qty' => 'required|integer|min:1',
        ]);

        $cart = $this->getCart($request);
        $id = $validated['id'];
        if (isset($cart[$id])) {
            $cart[$id]['qty'] = (int)$validated['qty'];
            $this->putCart($request, $cart);
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

        return redirect()->route('cart')->with('success', 'Item removed');
    }

    public function clear(Request $request)
    {
        $this->putCart($request, []);
        $request->session()->forget('coupon');
        return redirect()->route('cart')->with('info', 'Cart cleared');
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
            return redirect()->route('cart')->with('success', 'Coupon applied: ' . $coupon['code']);
        } else {
            $request->session()->forget('coupon');
            return redirect()->route('cart')->with('info', 'Coupon removed');
        }
    }
}
