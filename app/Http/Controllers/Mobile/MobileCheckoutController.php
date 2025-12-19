<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MobileCheckoutController extends Controller
{
    private function getCart(Request $request): array
    {
        $user = Auth::user();
        $sessionId = $request->session()->getId();
        
        if ($user) {
            $cart = Cart::getOrCreateCart($user->id, null);
            if ($cart && $cart->items->count() > 0) {
                return $cart->toSessionArray();
            }
        }
        
        $sessionCart = $request->session()->get('cart', []);
        
        if ($user && !empty($sessionCart)) {
            $cart = Cart::getOrCreateCart($user->id, null);
            $cart->mergeWithSessionCart($sessionCart);
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
            return redirect()->route('mobile.cart')->with('error', 'Keranjang belanja kosong');
        }
        
        // Add product data to cart items
        $cartWithProducts = [];
        foreach ($cart as $id => $item) {
            $product = Product::find($id);
            $cartWithProducts[$id] = $item;
            if ($product) {
                $cartWithProducts[$id]['product'] = $product;
                $cartWithProducts[$id]['name'] = $product->name;
                $cartWithProducts[$id]['price'] = $product->price;
                $cartWithProducts[$id]['image'] = $product->main_image_path;
                $cartWithProducts[$id]['qty'] = $item['quantity'] ?? $item['qty'] ?? 1;
            }
        }
        
        $totals = [
            'subtotal' => collect($cartWithProducts)->sum(function ($item) {
                return ($item['price'] ?? 0) * ($item['qty'] ?? 1);
            }),
            'shipping' => 0,
            'discount' => 0,
            'total' => 0
        ];
        
        // Calculate discount
        if ($coupon) {
            $subtotal = $totals['subtotal'];
            if ($coupon['type'] === 'percent') {
                $totals['discount'] = round($subtotal * ($coupon['value'] / 100), 2);
            } else {
                $totals['discount'] = min($subtotal, $coupon['value']);
            }
        }
        
        $totals['total'] = $totals['subtotal'] - $totals['discount'];
        
        // Get user data
        $user = auth()->user();
        $userData = null;
        $selectedAddress = null;
        $userAddresses = collect([]);
        
        if ($user) {
            $userAddresses = UserAddress::where('user_id', $user->id)
                ->where('is_active', true)
                ->orderBy('is_primary', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
            
            $addressId = request()->get('address_id');
            if ($addressId) {
                $selectedAddress = $userAddresses->firstWhere('id', $addressId);
            }
            
            if (!$selectedAddress) {
                $selectedAddress = $userAddresses->firstWhere('is_primary', true);
            }
            
            if (!$selectedAddress && $userAddresses->isNotEmpty()) {
                $selectedAddress = $userAddresses->first();
            }
            
            if (!$selectedAddress) {
                $locKabkotaName = null;
                $locProvinsiName = null;
                if ($user->loc_kabkota_id) {
                    $locKabkotaName = DB::table('loc_kabkotas')->where('id', $user->loc_kabkota_id)->value('name');
                }
                if ($user->loc_provinsi_id) {
                    $locProvinsiName = DB::table('loc_provinsis')->where('id', $user->loc_provinsi_id)->value('name');
                }
                
                $userData = [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'city' => $locKabkotaName ?? $locProvinsiName ?? 'Jakarta',
                    'postal_code' => $user->postal_code ?? '',
                ];
            } else {
                $userData = [
                    'name' => $selectedAddress->recipient_name,
                    'email' => $user->email,
                    'phone' => $selectedAddress->recipient_phone,
                    'address' => $selectedAddress->address,
                    'city' => $selectedAddress->loc_kabkota_name ?? $selectedAddress->city ?? 'Jakarta',
                    'postal_code' => $selectedAddress->postal_code ?? '',
                ];
            }
        }
        
        // Get all active stores with coordinates
        $stores = \App\Models\Store::where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();
        
        // Get all active outlets with coordinates
        $outlets = \App\Models\Outlet::where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with('store')
            ->get();
        
        // Get instant shipping method ID for frontend
        $instantMethod = \App\Models\ShippingMethod::where('type', 'instant')
            ->where('is_active', true)
            ->first();
        
        return view('mobile.checkout', [
            'cart' => $cartWithProducts,
            'coupon' => $coupon,
            'totals' => $totals,
            'user' => $userData,
            'selectedAddress' => $selectedAddress,
            'userAddresses' => $userAddresses,
            'stores' => $stores,
            'outlets' => $outlets,
            'instantMethodId' => $instantMethod ? $instantMethod->id : null,
        ]);
    }
}
