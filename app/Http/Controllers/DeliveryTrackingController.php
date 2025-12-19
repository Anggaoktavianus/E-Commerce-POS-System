<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\DeliveryTracking;
use App\Services\DistanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DeliveryTrackingController extends Controller
{
    /**
     * Get tracking data for customer
     */
    public function getTracking($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->with(['deliveryTracking.driver', 'shippingMethod'])
            ->firstOrFail();
        
        // Check if user owns this order or is admin
        if (Auth::check() && Auth::id() !== $order->user_id && !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized');
        }
        
        $tracking = $order->deliveryTracking;
        
        if (!$tracking) {
            return response()->json([
                'success' => false,
                'message' => 'Tracking belum tersedia untuk pesanan ini',
                'order' => [
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'shipping_method' => $order->shippingMethod?->name ?? 'Pengiriman Instan'
                ]
            ]);
        }
        
        // Get destination coordinates
        $destination = null;
        if ($order->shipping_address) {
            $shippingAddress = $order->shipping_address;
            if (isset($shippingAddress['latitude']) && isset($shippingAddress['longitude'])) {
                $destination = [
                    'lat' => (float) $shippingAddress['latitude'],
                    'lng' => (float) $shippingAddress['longitude'],
                    'address' => $shippingAddress['address'] ?? ''
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'tracking' => [
                'id' => $tracking->id,
                'status' => $tracking->status,
                'status_label' => $this->getStatusLabel($tracking->status),
                'current_location' => $tracking->current_location,
                'estimated_minutes' => $tracking->estimated_minutes,
                'formatted_eta' => $tracking->formatted_eta,
                'distance_km' => $tracking->distance_km,
                'driver' => $tracking->driver ? [
                    'name' => $tracking->driver->name,
                    'phone' => $tracking->driver->phone
                ] : null,
                'picked_at' => $tracking->picked_at?->format('Y-m-d H:i:s'),
                'on_the_way_at' => $tracking->on_the_way_at?->format('Y-m-d H:i:s'),
                'arrived_at' => $tracking->arrived_at?->format('Y-m-d H:i:s'),
            ],
            'order' => [
                'order_number' => $order->order_number,
                'status' => $order->status,
                'shipping_address' => $order->shipping_address,
                'destination' => $destination
            ]
        ]);
    }

    /**
     * Update location (for driver/kurir)
     */
    public function updateLocation(Request $request, $orderId)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        $order = Order::findOrFail($orderId);
        $tracking = DeliveryTracking::firstOrCreate(
            ['order_id' => $order->id],
            [
                'driver_id' => Auth::id(),
                'status' => DeliveryTracking::STATUS_ON_THE_WAY
            ]
        );

        // Update location
        $tracking->updateLocation(
            $request->latitude,
            $request->longitude,
            $request->address
        );

        return response()->json([
            'success' => true,
            'message' => 'Lokasi berhasil diperbarui',
            'tracking' => [
                'latitude' => $tracking->latitude,
                'longitude' => $tracking->longitude,
                'estimated_minutes' => $tracking->estimated_minutes,
                'distance_km' => $tracking->distance_km
            ]
        ]);
    }

    /**
     * Update status (for driver/kurir)
     */
    public function updateStatus(Request $request, $orderId)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:picked,on_the_way,arrived,delivered'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Status tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        $order = Order::findOrFail($orderId);
        $tracking = DeliveryTracking::firstOrCreate(
            ['order_id' => $order->id],
            [
                'driver_id' => Auth::id(),
                'status' => DeliveryTracking::STATUS_PENDING
            ]
        );

        $tracking->updateStatus($request->status);

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diperbarui',
            'tracking' => [
                'status' => $tracking->status,
                'status_label' => $this->getStatusLabel($tracking->status)
            ]
        ]);
    }

    /**
     * Get status label in Indonesian
     */
    private function getStatusLabel($status)
    {
        $labels = [
            DeliveryTracking::STATUS_PENDING => 'Menunggu',
            DeliveryTracking::STATUS_ASSIGNED => 'Kurir Ditetapkan',
            DeliveryTracking::STATUS_PICKED => 'Pesanan Diambil',
            DeliveryTracking::STATUS_ON_THE_WAY => 'Dalam Perjalanan',
            DeliveryTracking::STATUS_ARRIVED => 'Sudah Sampai',
            DeliveryTracking::STATUS_DELIVERED => 'Terkirim'
        ];

        return $labels[$status] ?? $status;
    }
}
