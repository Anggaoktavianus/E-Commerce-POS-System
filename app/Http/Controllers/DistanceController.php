<?php

namespace App\Http\Controllers;

use App\Services\DistanceService;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;

class DistanceController extends Controller
{
    /**
     * Calculate distance and shipping cost for instant delivery
     */
    public function calculateInstantShipping(Request $request)
    {
        $request->validate([
            'address' => 'required|string',
            'city' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'store_type' => 'nullable|string|in:store,outlet',
            'store_id' => 'nullable|integer',
            'store_latitude' => 'nullable|numeric',
            'store_longitude' => 'nullable|numeric',
        ]);

        // If store/outlet is selected, use provided coordinates
        if ($request->store_type && $request->store_id && $request->store_latitude && $request->store_longitude) {
            $storeCoords = [
                'latitude' => (float) $request->store_latitude,
                'longitude' => (float) $request->store_longitude,
                'type' => $request->store_type,
                'id' => $request->store_id,
            ];
        } else {
            // Fallback to current store
            $store = app()->has('current_store') ? app('current_store') : null;
            $storeCoords = DistanceService::getStoreCoordinates($store);
        }
        
        // Get instant delivery method from database
        $instantMethod = ShippingMethod::where('code', 'instant_delivery')
            ->where('is_active', true)
            ->where('is_distance_based', true)
            ->first();
        
        // Use price from database if available, otherwise use default
        $pricePerKm = $instantMethod && $instantMethod->price_per_km 
            ? (float) $instantMethod->price_per_km 
            : 5000; // Default Rp 5.000 per km
        
        $result = DistanceService::calculateInstantDeliveryCost(
            $request->address,
            $request->city,
            $request->latitude,
            $request->longitude,
            $pricePerKm,
            $storeCoords,
            $instantMethod // Pass method for min_cost calculation
        );

        return response()->json($result);
    }
}
