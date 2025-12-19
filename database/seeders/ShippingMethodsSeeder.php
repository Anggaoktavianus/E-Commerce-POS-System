<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShippingMethod;
use App\Models\ShippingCost;

class ShippingMethodsSeeder extends Seeder
{
    public function run()
    {
        // Create shipping methods
        $methods = [
            [
                'name' => 'Pengiriman Instan (Berdasarkan Jarak)',
                'code' => 'instant_delivery',
                'type' => 'instant',
                'logo_url' => null,
                'is_active' => true,
                'max_distance_km' => null, // No limit
                'price_per_km' => 5000, // Rp 5.000 per km
                'is_distance_based' => true,
                'min_cost' => 10000, // Minimum Rp 10.000
                'service_areas' => ['Semarang']
            ],
            [
                'name' => 'GoSend Instant',
                'code' => 'gosend_instant',
                'type' => 'instant',
                'logo_url' => 'https://logos.gojek.io/gojek-brand-logo.png',
                'is_active' => true,
                'max_distance_km' => 20,
                'price_per_km' => null,
                'is_distance_based' => false,
                'min_cost' => null,
                'service_areas' => ['Semarang']
            ],
            [
                'name' => 'GrabExpress Instant',
                'code' => 'grab_express',
                'type' => 'instant',
                'logo_url' => 'https://d3i4yxtzkt99as5.cloudfront.net/grab-id-cms/production/cms_images/grab-express-logo.png',
                'is_active' => true,
                'max_distance_km' => 20,
                'price_per_km' => null,
                'is_distance_based' => false,
                'min_cost' => null,
                'service_areas' => ['Semarang']
            ],
            [
                'name' => 'SiCepat Same Day',
                'code' => 'sicepat_same_day',
                'type' => 'same_day',
                'logo_url' => 'https://www.sicepat.com/images/logo.png',
                'is_active' => true,
                'max_distance_km' => null,
                'price_per_km' => null,
                'is_distance_based' => false,
                'min_cost' => null,
                'service_areas' => ['Semarang']
            ],
            [
                'name' => 'JNE OKE',
                'code' => 'jne_oke',
                'type' => 'regular',
                'logo_url' => 'https://www.jne.co.id/frontend/images/logo-jne.png',
                'is_active' => true,
                'max_distance_km' => null,
                'price_per_km' => null,
                'is_distance_based' => false,
                'min_cost' => null,
                'service_areas' => null
            ],
            [
                'name' => 'JNE REG',
                'code' => 'jne_reg',
                'type' => 'regular',
                'logo_url' => 'https://www.jne.co.id/frontend/images/logo-jne.png',
                'is_active' => true,
                'max_distance_km' => null,
                'price_per_km' => null,
                'is_distance_based' => false,
                'min_cost' => null,
                'service_areas' => null
            ],
            [
                'name' => 'JNE YES',
                'code' => 'jne_yes',
                'type' => 'express',
                'logo_url' => 'https://www.jne.co.id/frontend/images/logo-jne.png',
                'is_active' => true,
                'max_distance_km' => null,
                'price_per_km' => null,
                'is_distance_based' => false,
                'min_cost' => null,
                'service_areas' => null
            ],
            [
                'name' => 'JNT Express',
                'code' => 'jnt_reg',
                'type' => 'regular',
                'logo_url' => 'https://www.jnt.co.id/images/logo.png',
                'is_active' => true,
                'max_distance_km' => null,
                'price_per_km' => null,
                'is_distance_based' => false,
                'min_cost' => null,
                'service_areas' => null
            ],
            [
                'name' => 'SiCepat REG',
                'code' => 'sicepat_reg',
                'type' => 'regular',
                'logo_url' => 'https://www.sicepat.com/images/logo.png',
                'is_active' => true,
                'max_distance_km' => null,
                'price_per_km' => null,
                'is_distance_based' => false,
                'min_cost' => null,
                'service_areas' => null
            ],
            [
                'name' => 'POS Kilat Khusus',
                'code' => 'pos_kilat',
                'type' => 'regular',
                'logo_url' => 'https://www.posindonesia.co.id/assets/images/logo-pos.png',
                'is_active' => true,
                'max_distance_km' => null,
                'price_per_km' => null,
                'is_distance_based' => false,
                'min_cost' => null,
                'service_areas' => null
            ]
        ];

        foreach ($methods as $method) {
            ShippingMethod::updateOrCreate(
                ['code' => $method['code']],
                $method
            );
        }

        // Create shipping costs for major routes
        $routes = [
            // Semarang routes (NEW - Origin)
            ['Semarang', 'Semarang', 'gosend_instant', 22000, '60 menit'],
            ['Semarang', 'Semarang', 'grab_express', 20000, '60-90 menit'],
            ['Semarang', 'Semarang', 'sicepat_same_day', 18000, '6 jam'],
            ['Semarang', 'Semarang', 'jne_oke', 7000, '2-3'],
            ['Semarang', 'Semarang', 'jne_reg', 9000, '1-2'],
            ['Semarang', 'Semarang', 'jnt_reg', 8000, '1-2'],
            ['Semarang', 'Semarang', 'sicepat_reg', 10000, '1-2'],

            ['Semarang', 'Jakarta', 'sicepat_same_day', 75000, '12 jam'],
            ['Semarang', 'Jakarta', 'jne_oke', 15000, '4-6'],
            ['Semarang', 'Jakarta', 'jne_reg', 20000, '2-3'],
            ['Semarang', 'Jakarta', 'jne_yes', 30000, '1-2'],
            ['Semarang', 'Jakarta', 'jnt_reg', 18000, '2-3'],
            ['Semarang', 'Jakarta', 'sicepat_reg', 17000, '2-3'],

            ['Semarang', 'Surabaya', 'sicepat_same_day', 65000, '12 jam'],
            ['Semarang', 'Surabaya', 'jne_oke', 12000, '3-5'],
            ['Semarang', 'Surabaya', 'jne_reg', 15000, '2-3'],
            ['Semarang', 'Surabaya', 'jnt_reg', 14000, '2-3'],

            ['Semarang', 'Bandung', 'jne_oke', 13000, '3-5'],
            ['Semarang', 'Bandung', 'jne_reg', 17000, '2-3'],
            ['Semarang', 'Bandung', 'jnt_reg', 16000, '2-3'],

            ['Semarang', 'Medan', 'jne_oke', 22000, '6-9'],
            ['Semarang', 'Medan', 'jne_reg', 32000, '3-4'],
            ['Semarang', 'Medan', 'jne_yes', 42000, '2-3'],
            ['Semarang', 'Medan', 'jnt_reg', 30000, '3-4'],

            // Jakarta routes (existing)
            ['Jakarta', 'Jakarta', 'gosend_instant', 25000, '60 menit'],
            ['Jakarta', 'Jakarta', 'grab_express', 23000, '60-90 menit'],
            ['Jakarta', 'Jakarta', 'sicepat_same_day', 20000, '6 jam'],
            ['Jakarta', 'Jakarta', 'jne_oke', 8000, '2-3'],
            ['Jakarta', 'Jakarta', 'jne_reg', 10000, '1-2'],
            ['Jakarta', 'Jakarta', 'jnt_reg', 9000, '1-2'],
            ['Jakarta', 'Jakarta', 'sicepat_reg', 11000, '1-2'],

            ['Jakarta', 'Surabaya', 'gosend_instant', 150000, '6-8 jam'],
            ['Jakarta', 'Surabaya', 'grab_express', 140000, '6-8 jam'],
            ['Jakarta', 'Surabaya', 'sicepat_same_day', 85000, '12 jam'],
            ['Jakarta', 'Surabaya', 'jne_oke', 18000, '6-9'],
            ['Jakarta', 'Surabaya', 'jne_reg', 22000, '2-3'],
            ['Jakarta', 'Surabaya', 'jne_yes', 28000, '1-2'],
            ['Jakarta', 'Surabaya', 'jnt_reg', 20000, '2-3'],
            ['Jakarta', 'Surabaya', 'sicepat_reg', 19000, '2-3'],

            ['Jakarta', 'Bandung', 'gosend_instant', 80000, '3-4 jam'],
            ['Jakarta', 'Bandung', 'grab_express', 75000, '3-4 jam'],
            ['Jakarta', 'Bandung', 'sicepat_same_day', 45000, '8 jam'],
            ['Jakarta', 'Bandung', 'jne_oke', 12000, '3-5'],
            ['Jakarta', 'Bandung', 'jne_reg', 15000, '1-2'],
            ['Jakarta', 'Bandung', 'jnt_reg', 14000, '1-2'],
            ['Jakarta', 'Bandung', 'sicepat_reg', 13000, '1-2'],

            ['Jakarta', 'Medan', 'jne_oke', 25000, '7-10'],
            ['Jakarta', 'Medan', 'jne_reg', 35000, '3-4'],
            ['Jakarta', 'Medan', 'jne_yes', 45000, '2-3'],
            ['Jakarta', 'Medan', 'jnt_reg', 32000, '3-4'],
            ['Jakarta', 'Medan', 'sicepat_reg', 30000, '3-4'],

            // Surabaya routes
            ['Surabaya', 'Surabaya', 'gosend_instant', 22000, '60 menit'],
            ['Surabaya', 'Surabaya', 'grab_express', 20000, '60-90 menit'],
            ['Surabaya', 'Surabaya', 'sicepat_same_day', 18000, '6 jam'],
            ['Surabaya', 'Surabaya', 'jne_oke', 7000, '2-3'],
            ['Surabaya', 'Surabaya', 'jne_reg', 9000, '1-2'],
            ['Surabaya', 'Surabaya', 'jnt_reg', 8000, '1-2'],

            ['Surabaya', 'Jakarta', 'gosend_instant', 150000, '6-8 jam'],
            ['Surabaya', 'Jakarta', 'grab_express', 140000, '6-8 jam'],
            ['Surabaya', 'Jakarta', 'sicepat_same_day', 85000, '12 jam'],
            ['Surabaya', 'Jakarta', 'jne_oke', 18000, '6-9'],
            ['Surabaya', 'Jakarta', 'jne_reg', 22000, '2-3'],
            ['Surabaya', 'Jakarta', 'jnt_reg', 20000, '2-3'],

            ['Surabaya', 'Bandung', 'jne_oke', 20000, '5-7'],
            ['Surabaya', 'Bandung', 'jne_reg', 25000, '2-3'],
            ['Surabaya', 'Bandung', 'jnt_reg', 23000, '2-3'],

            // Bandung routes
            ['Bandung', 'Bandung', 'gosend_instant', 20000, '60 menit'],
            ['Bandung', 'Bandung', 'grab_express', 18000, '60-90 menit'],
            ['Bandung', 'Bandung', 'sicepat_same_day', 16000, '6 jam'],
            ['Bandung', 'Bandung', 'jne_oke', 6000, '2-3'],
            ['Bandung', 'Bandung', 'jne_reg', 8000, '1-2'],
            ['Bandung', 'Bandung', 'jnt_reg', 7000, '1-2'],

            ['Bandung', 'Jakarta', 'gosend_instant', 80000, '3-4 jam'],
            ['Bandung', 'Jakarta', 'grab_express', 75000, '3-4 jam'],
            ['Bandung', 'Jakarta', 'sicepat_same_day', 45000, '8 jam'],
            ['Bandung', 'Jakarta', 'jne_oke', 12000, '3-5'],
            ['Bandung', 'Jakarta', 'jne_reg', 15000, '1-2'],
            ['Bandung', 'Jakarta', 'jnt_reg', 14000, '1-2'],
        ];

        foreach ($routes as $route) {
            $method = ShippingMethod::where('code', $route[2])->first();
            if ($method) {
                // Skip creating shipping_costs for distance-based methods
                // They calculate cost dynamically based on distance
                if ($method->is_distance_based) {
                    continue;
                }
                
                ShippingCost::updateOrCreate(
                    [
                        'shipping_method_id' => $method->id,
                        'origin_city' => $route[0],
                        'destination_city' => $route[1],
                    ],
                    [
                        'cost' => $route[3],
                        'estimated_days' => $route[4],
                        'min_weight' => 0.5,
                        'max_weight' => 50,
                        'is_active' => true
                    ]
                );
            }
        }

        $this->command->info('✅ Shipping methods and costs seeded successfully!');
        $this->command->info('📦 ' . count($methods) . ' shipping methods created');
        $this->command->info('🚚 ' . count($routes) . ' shipping routes created');
    }
}
