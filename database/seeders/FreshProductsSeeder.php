<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class FreshProductsSeeder extends Seeder
{
    public function run()
    {
        // Update existing products with fresh product attributes
        $freshProducts = [
            // Fresh vegetables (3-5 days shelf life)
            [
                'name' => 'Sayuran Segar Mix',
                'shelf_life_days' => 3,
                'requires_cold_chain' => true,
                'shipping_type' => 'fresh'
            ],
            [
                'name' => 'Bayam Organik',
                'shelf_life_days' => 2,
                'requires_cold_chain' => true,
                'shipping_type' => 'fresh'
            ],
            [
                'name' => 'Tomat Segar',
                'shelf_life_days' => 5,
                'requires_cold_chain' => true,
                'shipping_type' => 'fresh'
            ],
            [
                'name' => 'Wortel Segar',
                'shelf_life_days' => 7,
                'requires_cold_chain' => true,
                'shipping_type' => 'fresh'
            ],
            
            // Extra fresh products (1-2 days shelf life)
            [
                'name' => 'Susu Segar',
                'shelf_life_days' => 2,
                'requires_cold_chain' => true,
                'shipping_type' => 'fresh'
            ],
            [
                'name' => 'Yogurt Segar',
                'shelf_life_days' => 3,
                'requires_cold_chain' => true,
                'shipping_type' => 'fresh'
            ],
            [
                'name' => 'Telur Segar',
                'shelf_life_days' => 7,
                'requires_cold_chain' => true,
                'shipping_type' => 'fresh'
            ],
            
            // Frozen products (longer shelf life but require cold chain)
            [
                'name' => 'Daging Ayam Beku',
                'shelf_life_days' => 30,
                'requires_cold_chain' => true,
                'shipping_type' => 'frozen'
            ],
            [
                'name' => 'Ikan Salmon Beku',
                'shelf_life_days' => 90,
                'requires_cold_chain' => true,
                'shipping_type' => 'frozen'
            ],
            [
                'name' => 'Udang Beku',
                'shelf_life_days' => 60,
                'requires_cold_chain' => true,
                'shipping_type' => 'frozen'
            ],
            
            // Dry products (no special requirements)
            [
                'name' => 'Berbagai Macam Buah',
                'shelf_life_days' => 14,
                'requires_cold_chain' => false,
                'shipping_type' => 'dry'
            ],
            [
                'name' => 'Kacang-kacangan',
                'shelf_life_days' => 60,
                'requires_cold_chain' => false,
                'shipping_type' => 'dry'
            ],
            [
                'name' => 'Beras Organik',
                'shelf_life_days' => 180,
                'requires_cold_chain' => false,
                'shipping_type' => 'dry'
            ]
        ];

        foreach ($freshProducts as $productData) {
            $product = Product::where('name', $productData['name'])->first();
            
            if ($product) {
                $product->update([
                    'shelf_life_days' => $productData['shelf_life_days'],
                    'requires_cold_chain' => $productData['requires_cold_chain'],
                    'shipping_type' => $productData['shipping_type']
                ]);
                
                $this->command->info("✅ Updated {$product->name} - Shelf life: {$productData['shelf_life_days']} days");
            } else {
                // Create new product if not found
                Product::create([
                    'name' => $productData['name'],
                    'description' => "Produk {$productData['name']} berkualitas tinggi",
                    'price' => rand(10000, 100000),
                    'category_id' => 1, // Assuming category 1 exists
                    'stock' => rand(10, 100),
                    'sku' => 'FRESH-' . strtoupper(str_replace(' ', '-', $productData['name'])),
                    'weight' => rand(1, 5) / 10, // 0.1 to 0.5 kg
                    'shelf_life_days' => $productData['shelf_life_days'],
                    'requires_cold_chain' => $productData['requires_cold_chain'],
                    'shipping_type' => $productData['shipping_type'],
                    'is_active' => true
                ]);
                
                $this->command->info("✅ Created {$productData['name']} - Shelf life: {$productData['shelf_life_days']} days");
            }
        }

        // Summary
        $totalFresh = Product::where('shipping_type', 'fresh')->count();
        $totalFrozen = Product::where('shipping_type', 'frozen')->count();
        $totalDry = Product::where('shipping_type', 'dry')->count();
        $totalColdChain = Product::where('requires_cold_chain', true)->count();
        $extraFresh = Product::where('shelf_life_days', '<=', 3)->count();

        $this->command->info("\n📊 Fresh Product Summary:");
        $this->command->info("🥬 Fresh products: {$totalFresh}");
        $this->command->info("🧊 Frozen products: {$totalFrozen}");
        $this->command->info("📦 Dry products: {$totalDry}");
        $this->command->info("❄️ Cold chain required: {$totalColdChain}");
        $this->command->info("⚡ Extra fresh (≤3 days): {$extraFresh}");
    }
}
