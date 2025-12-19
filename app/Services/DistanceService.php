<?php

namespace App\Services;

class DistanceService
{
    /**
     * Calculate distance between two coordinates using Haversine formula
     * Returns distance in kilometers
     */
    public static function calculateDistance(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {
        // Earth radius in kilometers
        $earthRadius = 6371;

        // Convert degrees to radians
        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);

        // Haversine formula
        $deltaLat = $lat2Rad - $lat1Rad;
        $deltaLon = $lon2Rad - $lon1Rad;

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($deltaLon / 2) * sin($deltaLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return round($distance, 2);
    }

    /**
     * Calculate shipping cost based on distance
     * Cost = distance (km) × price per km
     */
    public static function calculateShippingCost(
        float $distanceKm,
        float $pricePerKm = 5000
    ): int {
        // Minimum distance is 1 km
        $distanceKm = max(1, $distanceKm);
        
        // Calculate cost (round up to nearest 1000)
        $cost = ceil($distanceKm * $pricePerKm / 1000) * 1000;
        
        return (int) $cost;
    }

    /**
     * Get store coordinates (from main outlet, any outlet, or mitra user)
     */
    public static function getStoreCoordinates($store = null)
    {
        if (!$store) {
            $store = app()->has('current_store') ? app('current_store') : null;
        }

        if (!$store) {
            return null;
        }

        // Try to get from main outlet first
        $mainOutlet = $store->mainOutlet();
        if ($mainOutlet && $mainOutlet->latitude && $mainOutlet->longitude) {
            return [
                'latitude' => (float) $mainOutlet->latitude,
                'longitude' => (float) $mainOutlet->longitude,
                'address' => $mainOutlet->full_address,
                'source' => 'outlet'
            ];
        }

        // Try to get from any active outlet
        $outlet = $store->activeOutlets()->whereNotNull('latitude')->whereNotNull('longitude')->first();
        if ($outlet) {
            return [
                'latitude' => (float) $outlet->latitude,
                'longitude' => (float) $outlet->longitude,
                'address' => $outlet->full_address,
                'source' => 'outlet'
            ];
        }

        // Try to get from mitra user (owner of the store)
        // Priority 1: Match by email
        if ($store->email) {
            $mitraUser = \App\Models\User::where('email', $store->email)
                ->where('role', 'mitra')
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->first();
            
            if ($mitraUser) {
                return [
                    'latitude' => (float) $mitraUser->latitude,
                    'longitude' => (float) $mitraUser->longitude,
                    'address' => $mitraUser->address ?? $store->full_address,
                    'source' => 'mitra_user'
                ];
            }
        }

        // Priority 2: Match by company name
        if ($store->name) {
            $mitraUser = \App\Models\User::where('company_name', $store->name)
                ->where('role', 'mitra')
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->first();
            
            if ($mitraUser) {
                return [
                    'latitude' => (float) $mitraUser->latitude,
                    'longitude' => (float) $mitraUser->longitude,
                    'address' => $mitraUser->address ?? $store->full_address,
                    'source' => 'mitra_user'
                ];
            }
        }

        // Priority 3: Match by owner name
        if ($store->owner_name) {
            $mitraUser = \App\Models\User::where('name', $store->owner_name)
                ->where('role', 'mitra')
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->first();
            
            if ($mitraUser) {
                return [
                    'latitude' => (float) $mitraUser->latitude,
                    'longitude' => (float) $mitraUser->longitude,
                    'address' => $mitraUser->address ?? $store->full_address,
                    'source' => 'mitra_user'
                ];
            }
        }

        // Default coordinates for Semarang (if no outlet or user coordinates available)
        return [
            'latitude' => -7.0051,
            'longitude' => 110.4381,
            'address' => $store->full_address ?? 'Semarang, Jawa Tengah',
            'source' => 'default'
        ];
    }

    /**
     * Geocode address to coordinates using Google Geocoding API (optional)
     * If API key not available, returns null
     */
    public static function geocodeAddress(string $address): ?array
    {
        $apiKey = config('services.google.maps_api_key');
        
        if (!$apiKey) {
            return null;
        }

        try {
            $url = 'https://maps.googleapis.com/maps/api/geocode/json?' . http_build_query([
                'address' => $address,
                'key' => $apiKey,
                'region' => 'id'
            ]);

            $response = @file_get_contents($url);
            if (!$response) {
                return null;
            }

            $data = json_decode($response, true);

            if ($data['status'] === 'OK' && !empty($data['results'])) {
                $location = $data['results'][0]['geometry']['location'];
                return [
                    'latitude' => (float) $location['lat'],
                    'longitude' => (float) $location['lng'],
                    'formatted_address' => $data['results'][0]['formatted_address']
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Geocoding error', ['error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Calculate distance and shipping cost for instant delivery
     */
    public static function calculateInstantDeliveryCost(
        string $customerAddress,
        string $customerCity,
        ?float $customerLat = null,
        ?float $customerLon = null,
        float $pricePerKm = null,
        $storeOrCoords = null,
        $shippingMethod = null
    ): array {
        // Get price per km from shipping method if provided, otherwise use parameter or default
        if ($shippingMethod && $shippingMethod->is_distance_based && $shippingMethod->price_per_km) {
            $pricePerKm = (float) $shippingMethod->price_per_km;
        } elseif ($pricePerKm === null) {
            $pricePerKm = 5000; // Default Rp 5.000 per km
        }
        
        $minCost = 0;
        if ($shippingMethod && $shippingMethod->is_distance_based && $shippingMethod->min_cost) {
            $minCost = (float) $shippingMethod->min_cost;
        }
        
        // Get store coordinates
        // If $storeOrCoords is already an array with coordinates, use it directly
        if (is_array($storeOrCoords) && isset($storeOrCoords['latitude']) && isset($storeOrCoords['longitude'])) {
            $storeCoords = $storeOrCoords;
        } else {
            // Otherwise, get from store model
            $storeCoords = self::getStoreCoordinates($storeOrCoords);
        }
        
        if (!$storeCoords || !isset($storeCoords['latitude']) || !isset($storeCoords['longitude'])) {
            return [
                'success' => false,
                'error' => 'Koordinat toko tidak ditemukan. Silakan pilih store/outlet terlebih dahulu.'
            ];
        }

        // Get customer coordinates
        $customerCoords = null;
        
        if ($customerLat && $customerLon) {
            // Use provided coordinates
            $customerCoords = [
                'latitude' => $customerLat,
                'longitude' => $customerLon
            ];
        } else {
            // Try to geocode address
            $fullAddress = $customerAddress . ', ' . $customerCity . ', Indonesia';
            $customerCoords = self::geocodeAddress($fullAddress);
        }

        if (!$customerCoords) {
            return [
                'success' => false,
                'error' => 'Tidak dapat menentukan koordinat alamat pembeli. Silakan pastikan alamat lengkap dan benar.',
                'suggestion' => 'Anda dapat memasukkan koordinat secara manual atau menggunakan alamat yang lebih spesifik.'
            ];
        }

        // Calculate distance
        $distance = self::calculateDistance(
            $storeCoords['latitude'],
            $storeCoords['longitude'],
            $customerCoords['latitude'],
            $customerCoords['longitude']
        );

        // Calculate shipping cost
        $shippingCost = self::calculateShippingCost($distance, $pricePerKm);
        
        // Apply minimum cost if set
        if ($minCost > 0 && $shippingCost < $minCost) {
            $shippingCost = (int) $minCost;
        }

        return [
            'success' => true,
            'distance_km' => $distance,
            'shipping_cost' => $shippingCost,
            'price_per_km' => $pricePerKm,
            'min_cost' => $minCost,
            'store_coordinates' => $storeCoords,
            'customer_coordinates' => $customerCoords,
            'formatted_cost' => 'IDR ' . number_format($shippingCost, 0, ',', '.'),
            'formatted_distance' => number_format($distance, 2, ',', '.') . ' km'
        ];
    }
}
