<?php

namespace App\Services;

use App\Models\ShippingMethod;
use App\Models\ShippingCost;
use App\Models\Product;
use Illuminate\Support\Collection;

class SmartShippingService
{
    /**
     * Get optimal shipping methods based on cart and destination
     */
    public function getOptimalShipping($cartItems, $destination, $origin = 'Semarang')
    {
        $hasFreshProducts = $this->hasFreshProducts($cartItems);
        $totalWeight = $this->calculateTotalWeight($cartItems);
        $sameCity = $this->isSameCity($origin, $destination);
        
        // Always include pickup option for Semarang
        $availableMethods = $this->getAvailableMethods($origin, $destination, $totalWeight);
        
        // Add pickup option if destination is Semarang
        if ($sameCity && $origin === 'Semarang') {
            $pickupMethods = $this->getPickupMethods();
            $availableMethods = $pickupMethods->concat($availableMethods);
        }
        
        // Filter and prioritize based on fresh products
        if ($hasFreshProducts) {
            $availableMethods = $this->prioritizeForFreshProducts($availableMethods, $sameCity);
        }
        
        // Add recommendations and warnings
        return $this->addRecommendations($availableMethods, $hasFreshProducts, $sameCity);
    }
    
    /**
     * Get pickup methods
     */
    private function getPickupMethods()
    {
        $pickupMethod = ShippingMethod::where('code', 'pickup_store')
            ->where('is_active', true)
            ->first();
            
        if (!$pickupMethod) {
            return collect([]);
        }
        
        // Create pickup cost entry
        $pickupCost = (object) [
            'cost' => 0,
            'formatted_cost' => 'GRATIS',
            'estimated_days' => 'Langsung Ambil',
            'estimated_delivery_text' => 'Langsung Ambil',
            'fresh_product_score' => 100,
            'isFreshProductFriendly' => function() { return true; }
        ];
        
        return collect([[
            'id' => $pickupMethod->id,
            'name' => $pickupMethod->name,
            'code' => $pickupMethod->code,
            'type' => $pickupMethod->type,
            'logo' => $pickupMethod->logo_url,
            'cost' => $pickupCost->cost,
            'formatted_cost' => $pickupCost->formatted_cost,
            'estimated_days' => $pickupCost->estimated_days,
            'estimated_text' => $pickupCost->estimated_delivery_text,
            'fresh_product_score' => $pickupCost->fresh_product_score,
            'is_fresh_friendly' => true,
            'type_badge' => '🏪 Pickup',
            'type_color' => 'success'
        ]]);
    }
    
    /**
     * Get all available shipping methods for route
     */
    public function getAvailableMethods($origin, $destination, $weight = 1)
    {
        $methods = ShippingMethod::with(['costs' => function($query) use ($origin, $destination, $weight) {
            $query->where('origin_city', $origin)
                  ->where('destination_city', $destination)
                  ->where('min_weight', '<=', $weight)
                  ->where('max_weight', '>=', $weight)
                  ->where('is_active', true);
        }])
        ->where('is_active', true)
        ->get()
        ->filter(function($method) {
            return $method->costs->isNotEmpty();
        });
        
        return $methods->map(function($method) {
            $cost = $method->costs->first();
            return [
                'id' => $method->id,
                'name' => $method->name,
                'code' => $method->code,
                'type' => $method->type,
                'logo' => $method->logo_url,
                'cost' => $cost->cost,
                'formatted_cost' => $cost->formatted_cost,
                'estimated_days' => $cost->estimated_days,
                'estimated_text' => $cost->estimated_delivery_text,
                'fresh_product_score' => $cost->fresh_product_score,
                'is_fresh_friendly' => $cost->isFreshProductFriendly(),
                'type_badge' => $method->formatted_type,
                'type_color' => $method->type_badge_color
            ];
        })->values();
    }
    
    /**
     * Check if cart contains fresh products
     */
    public function hasFreshProducts($cartItems)
    {
        foreach ($cartItems as $item) {
            if ($item['product']->isFreshProduct()) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Calculate total weight of cart
     */
    public function calculateTotalWeight($cartItems)
    {
        $totalWeight = 0;
        foreach ($cartItems as $item) {
            $totalWeight += ($item['product']->weight ?? 1) * $item['quantity'];
        }
        return max($totalWeight, 1); // Minimum 1kg
    }
    
    /**
     * Check if same city delivery
     */
    private function isSameCity($origin, $destination)
    {
        return strtolower($origin) === strtolower($destination);
    }
    
    /**
     * Prioritize shipping methods for fresh products
     */
    private function prioritizeForFreshProducts($methods, $sameCity)
    {
        // Sort by fresh product score (highest first)
        $sorted = $methods->sortByDesc('fresh_product_score');
        
        // Add special recommendations for same city
        if ($sameCity) {
            $sorted = $sorted->map(function($method) {
                if ($method['type'] === 'pickup') {
                    $method['badge'] = '🏪 PERFECT - Ambil Langsung';
                    $method['badge_type'] = 'success';
                    $method['recommended'] = true;
                    $method['urgency'] = 'immediate';
                    $method['urgency_text'] = '🏪 Siap Diambil';
                    $method['cost_efficiency'] = 'free';
                    $method['cost_text'] = '🆓 GRATIS';
                } elseif ($method['type'] === 'instant') {
                    $method['badge'] = '🌟 PERFECT untuk Produk Segar';
                    $method['badge_type'] = 'success';
                    $method['recommended'] = true;
                    $method['urgency'] = 'immediate';
                    $method['urgency_text'] = '🚀 Kirim Sekarang';
                    $method['cost_efficiency'] = 'premium';
                    $method['cost_text'] = '💎 Premium';
                } elseif (in_array($method['type'], ['same_day'])) {
                    $method['badge'] = '✅ Bagus untuk Produk Segar';
                    $method['badge_type'] = 'info';
                    $method['urgency'] = 'same_day';
                    $method['urgency_text'] = '⚡ Hari Ini';
                    $method['cost_efficiency'] = 'moderate';
                    $method['cost_text'] = '⚖️ Sedang';
                } else {
                    $method['warning'] = '⚠️ Tidak direkomendasikan untuk produk segar';
                    $method['warning_type'] = 'danger';
                    $method['urgency'] = 'regular';
                    $method['urgency_text'] = '🚚 Reguler';
                    $method['cost_efficiency'] = 'economical';
                    $method['cost_text'] = '💰 Hemat';
                }
                return $method;
            });
        } else {
            $sorted = $sorted->map(function($method) {
                if ($method['fresh_product_score'] >= 60) {
                    $method['badge'] = '✅ Cukup Baik untuk Produk Segar';
                    $method['badge_type'] = 'warning';
                    $method['urgency'] = 'fast';
                    $method['urgency_text'] = '📦 Cepat';
                    $method['cost_efficiency'] = 'moderate';
                    $method['cost_text'] = '⚖️ Sedang';
                } else {
                    $method['warning'] = '⚠️ Risiko tinggi untuk produk segar';
                    $method['warning_type'] = 'warning';
                    $method['urgency'] = 'regular';
                    $method['urgency_text'] = '🚚 Reguler';
                    $method['cost_efficiency'] = 'economical';
                    $method['cost_text'] = '💰 Hemat';
                }
                return $method;
            });
        }
        
        return $sorted->values();
    }
    
    /**
     * Add recommendations and warnings
     */
    private function addRecommendations($methods, $hasFreshProducts, $sameCity)
    {
        return $methods->map(function($method) use ($hasFreshProducts, $sameCity) {
            // Add delivery urgency indicator
            if ($method['type'] === 'instant') {
                $method['urgency'] = 'immediate';
                $method['urgency_text'] = '🚀 Kirim Sekarang';
            } elseif ($method['type'] === 'same_day') {
                $method['urgency'] = 'same_day';
                $method['urgency_text'] = '⚡ Hari Ini';
            } elseif ($method['estimated_days'] === '1-2') {
                $method['urgency'] = 'fast';
                $method['urgency_text'] = '📦 Cepat';
            } else {
                $method['urgency'] = 'regular';
                $method['urgency_text'] = '🚚 Reguler';
            }
            
            // Add cost efficiency indicator
            if ($method['cost'] <= 25000) {
                $method['cost_efficiency'] = 'economical';
                $method['cost_text'] = '💰 Hemat';
            } elseif ($method['cost'] <= 50000) {
                $method['cost_efficiency'] = 'moderate';
                $method['cost_text'] = '⚖️ Sedang';
            } else {
                $method['cost_efficiency'] = 'premium';
                $method['cost_text'] = '💎 Premium';
            }
            
            return $method;
        });
    }
    
    /**
     * Get shipping method by ID
     */
    public function getShippingMethod($methodId)
    {
        return ShippingMethod::find($methodId);
    }
    
    /**
     * Calculate shipping cost for specific method
     */
    public function calculateShippingCost($methodId, $origin, $destination, $weight = 1)
    {
        $method = $this->getShippingMethod($methodId);
        if (!$method) {
            return null;
        }
        
        $cost = $method->calculateCost($origin, $destination, $weight);
        if (!$cost) {
            return null;
        }
        
        return [
            'method' => $method,
            'cost' => $cost->cost,
            'formatted_cost' => $cost->formatted_cost,
            'estimated_days' => $cost->estimated_days,
            'estimated_text' => $cost->estimated_delivery_text
        ];
    }
    
    /**
     * Check if shipping method is available for route
     */
    public function isShippingAvailable($methodId, $origin, $destination)
    {
        $method = $this->getShippingMethod($methodId);
        if (!$method) {
            return false;
        }
        
        return $method->isAvailable($origin, $destination);
    }
    
    /**
     * Get fresh product warnings for cart
     */
    public function getFreshProductWarnings($cartItems, $selectedMethod = null)
    {
        $warnings = [];
        $hasFreshProducts = $this->hasFreshProducts($cartItems);
        
        if (!$hasFreshProducts) {
            return collect($warnings);
        }
        
        $extraFreshProducts = collect($cartItems)->filter(function($item) {
            return $item['product']->requiresInstantDelivery();
        });
        
        if ($extraFreshProducts->isNotEmpty()) {
            $warnings[] = [
                'type' => 'danger',
                'title' => '🚨 Produk Extra Segar',
                'message' => 'Keranjang Anda mengandung produk dengan masa kadaluarsa ≤ 3 hari. Pilih pengiriman instan untuk kualitas terbaik!',
                'icon' => 'bx-error-circle'
            ];
        } else {
            $warnings[] = [
                'type' => 'warning',
                'title' => '🥬 Produk Segar',
                'message' => 'Keranjang Anda mengandung produk segar. Pilih pengiriman tercepat untuk menjaga kesegaran.',
                'icon' => 'bx-info-circle'
            ];
        }
        
        if ($selectedMethod && !$selectedMethod['is_fresh_friendly']) {
            $warnings[] = [
                'type' => 'danger',
                'title' => '⚠️ Pengiriman Tidak Sesuai',
                'message' => 'Metode pengiriman yang dipilih tidak direkomendasikan untuk produk segar. Produk bisa rusak sebelum sampai!',
                'icon' => 'bx-x-circle'
            ];
        }
        
        return collect($warnings);
    }
}
