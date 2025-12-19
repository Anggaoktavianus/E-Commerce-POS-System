<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DeliveryTracking extends Model
{
    use HasFactory;

    protected $table = 'delivery_tracking';

    protected $fillable = [
        'order_id',
        'driver_id',
        'status',
        'latitude',
        'longitude',
        'address',
        'estimated_minutes',
        'distance_km',
        'picked_at',
        'on_the_way_at',
        'arrived_at',
        'notes'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'distance_km' => 'decimal:2',
        'estimated_minutes' => 'integer',
        'picked_at' => 'datetime',
        'on_the_way_at' => 'datetime',
        'arrived_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_PICKED = 'picked';
    const STATUS_ON_THE_WAY = 'on_the_way';
    const STATUS_ARRIVED = 'arrived';
    const STATUS_DELIVERED = 'delivered';

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Update location and calculate ETA
     */
    public function updateLocation($latitude, $longitude, $address = null)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        if ($address) {
            $this->address = $address;
        }
        
        // Calculate ETA if we have destination
        if ($this->order && $this->order->shipping_address) {
            $destination = $this->order->shipping_address;
            if (isset($destination['latitude']) && isset($destination['longitude'])) {
                $this->calculateETA(
                    $latitude,
                    $longitude,
                    $destination['latitude'],
                    $destination['longitude']
                );
            }
        }
        
        $this->save();
    }

    /**
     * Calculate ETA based on distance
     */
    private function calculateETA($lat1, $lon1, $lat2, $lon2)
    {
        // Haversine formula to calculate distance
        $earthRadius = 6371; // Earth radius in km
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;
        
        $this->distance_km = round($distance, 2);
        
        // Estimate time: average speed 30 km/h for instant delivery
        $averageSpeed = 30; // km/h
        $estimatedHours = $distance / $averageSpeed;
        $this->estimated_minutes = max(5, round($estimatedHours * 60)); // Minimum 5 minutes
        
        return $this;
    }

    /**
     * Update status
     */
    public function updateStatus($status)
    {
        $this->status = $status;
        
        switch ($status) {
            case self::STATUS_PICKED:
                $this->picked_at = now();
                break;
            case self::STATUS_ON_THE_WAY:
                $this->on_the_way_at = now();
                break;
            case self::STATUS_ARRIVED:
                $this->arrived_at = now();
                break;
        }
        
        $this->save();
        
        // Update order status if needed
        if ($status === self::STATUS_DELIVERED && $this->order) {
            $this->order->update([
                'status' => 'delivered',
                'delivered_at' => now()
            ]);
        }
        
        return $this;
    }

    /**
     * Get formatted ETA
     */
    public function getFormattedETAAttribute()
    {
        if (!$this->estimated_minutes) {
            return 'Menghitung...';
        }
        
        if ($this->estimated_minutes < 60) {
            return $this->estimated_minutes . ' menit';
        }
        
        $hours = floor($this->estimated_minutes / 60);
        $minutes = $this->estimated_minutes % 60;
        
        if ($minutes > 0) {
            return $hours . ' jam ' . $minutes . ' menit';
        }
        
        return $hours . ' jam';
    }

    /**
     * Get current location for map
     */
    public function getCurrentLocationAttribute()
    {
        if (!$this->latitude || !$this->longitude) {
            return null;
        }
        
        return [
            'lat' => (float) $this->latitude,
            'lng' => (float) $this->longitude,
            'address' => $this->address
        ];
    }

    /**
     * Get status label in Indonesian
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            self::STATUS_PENDING => 'Menunggu',
            self::STATUS_ASSIGNED => 'Kurir Ditetapkan',
            self::STATUS_PICKED => 'Pesanan Diambil',
            self::STATUS_ON_THE_WAY => 'Dalam Perjalanan',
            self::STATUS_ARRIVED => 'Sudah Sampai',
            self::STATUS_DELIVERED => 'Terkirim'
        ];

        return $labels[$this->status] ?? $this->status;
    }
}
