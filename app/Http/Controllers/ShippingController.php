<?php

namespace App\Http\Controllers;

use App\Services\SmartShippingService;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShippingController extends Controller
{
    protected $shippingService;
    
    public function __construct(SmartShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
    }
    
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
    
    /**
     * Get available shipping methods for cart
     */
    public function getAvailableMethods(Request $request)
    {
        $cart = $this->getCart($request);
        $destination = $request->destination_city;
        $origin = $request->origin_city ?? 'Jakarta';
        
        if (!$destination) {
            return response()->json([
                'success' => false,
                'message' => 'Destination city is required'
            ], 400);
        }
        
        // Convert cart items to proper format
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
        
        if (empty($cartItems)) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty'
            ], 400);
        }
        
        // Get optimal shipping methods
        $shippingMethods = $this->shippingService->getOptimalShipping($cartItems, $destination, $origin);
        
        // Get fresh product warnings
        $warnings = $this->shippingService->getFreshProductWarnings($cartItems);
        
        return response()->json([
            'success' => true,
            'data' => [
                'methods' => $shippingMethods,
                'warnings' => $warnings,
                'cart_summary' => [
                    'total_items' => count($cartItems),
                    'has_fresh_products' => $this->shippingService->hasFreshProducts($cartItems),
                    'total_weight' => $this->shippingService->calculateTotalWeight($cartItems)
                ]
            ]
        ]);
    }
    
    /**
     * Calculate cost for specific shipping method
     */
    public function calculateCost(Request $request)
    {
        $request->validate([
            'shipping_method_id' => 'required|exists:shipping_methods,id',
            'origin_city' => 'required|string',
            'destination_city' => 'required|string',
            'weight' => 'nullable|numeric|min:0.1'
        ]);
        
        $origin = $request->origin_city;
        $destination = $request->destination_city;
        $weight = $request->weight ?? 1;
        
        $cost = $this->shippingService->calculateShippingCost(
            $request->shipping_method_id,
            $origin,
            $destination,
            $weight
        );
        
        if (!$cost) {
            return response()->json([
                'success' => false,
                'message' => 'Shipping method not available for this route'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $cost
        ]);
    }
    
    /**
     * Check if shipping method is available
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'shipping_method_id' => 'required|exists:shipping_methods,id',
            'origin_city' => 'required|string',
            'destination_city' => 'required|string'
        ]);
        
        $available = $this->shippingService->isShippingAvailable(
            $request->shipping_method_id,
            $request->origin_city,
            $request->destination_city
        );
        
        return response()->json([
            'success' => true,
            'data' => [
                'available' => $available,
                'message' => $available ? 'Shipping available' : 'Shipping not available for this route'
            ]
        ]);
    }
    
    /**
     * Get shipping method details
     */
    public function getMethodDetails($methodId)
    {
        $method = $this->shippingService->getShippingMethod($methodId);
        
        if (!$method) {
            return response()->json([
                'success' => false,
                'message' => 'Shipping method not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $method->id,
                'name' => $method->name,
                'code' => $method->code,
                'type' => $method->type,
                'formatted_type' => $method->formatted_type,
                'type_color' => $method->type_badge_color,
                'logo' => $method->logo_url,
                'service_areas' => $method->service_areas,
                'max_distance_km' => $method->max_distance_km
            ]
        ]);
    }
    
    /**
     * Get supported cities
     */
    public function getSupportedCities()
    {
        // Get cities from shipping costs
        $cities = \App\Models\ShippingCost::distinct()
            ->select('origin_city as city')
            ->pluck('city')
            ->merge(
                \App\Models\ShippingCost::distinct()
                    ->select('destination_city as city')
                    ->pluck('city')
            )
            ->unique()
            ->sort()
            ->values();
        
        return response()->json([
            'success' => true,
            'data' => $cities
        ]);
    }
    
    /**
     * Get instant delivery cities (for fresh products)
     */
    public function getInstantDeliveryCities()
    {
        // Get cities where instant delivery is available
        $instantMethods = \App\Models\ShippingMethod::where('type', 'instant')
            ->where('is_active', true)
            ->get();
        
        $cities = collect();
        
        foreach ($instantMethods as $method) {
            if ($method->service_areas) {
                $cities = $cities->merge($method->service_areas);
            }
            
            // Also get cities from shipping costs for instant methods
            $methodCities = \App\Models\ShippingCost::where('shipping_method_id', $method->id)
                ->distinct()
                ->select('origin_city as city')
                ->pluck('city');
            
            $cities = $cities->merge($methodCities);
        }
        
        return response()->json([
            'success' => true,
            'data' => $cities->unique()->sort()->values()
        ]);
    }
    
    /**
     * Validate shipping for fresh products
     */
    public function validateFreshProductShipping(Request $request)
    {
        $request->validate([
            'shipping_method_id' => 'required|exists:shipping_methods,id',
            'cart_items' => 'required|array',
            'cart_items.*.product_id' => 'required|exists:products,id',
            'cart_items.*.quantity' => 'required|integer|min:1'
        ]);
        
        // Get cart items
        $cartItems = [];
        foreach ($request->cart_items as $item) {
            $product = \App\Models\Product::find($item['product_id']);
            if ($product) {
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity']
                ];
            }
        }
        
        // Get shipping method
        $method = $this->shippingService->getShippingMethod($request->shipping_method_id);
        
        // Get warnings
        $warnings = $this->shippingService->getFreshProductWarnings($cartItems, [
            'type' => $method->type,
            'is_fresh_friendly' => in_array($method->type, ['instant', 'same_day'])
        ]);
        
        $hasFreshProducts = $this->shippingService->hasFreshProducts($cartItems);
        $isRecommended = $warnings->isEmpty() || !collect($warnings)->contains('type', 'danger');
        
        return response()->json([
            'success' => true,
            'data' => [
                'has_fresh_products' => $hasFreshProducts,
                'is_recommended' => $isRecommended,
                'warnings' => $warnings,
                'shipping_method' => [
                    'name' => $method->name,
                    'type' => $method->type,
                    'is_fresh_friendly' => in_array($method->type, ['instant', 'same_day'])
                ]
            ]
        ]);
    }
}
