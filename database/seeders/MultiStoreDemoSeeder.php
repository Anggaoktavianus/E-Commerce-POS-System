<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class MultiStoreDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Stores
        $stores = [
            [
                'id' => 1,
                'name' => 'Default Store',
                'code' => 'default',
                'domain' => null,
                'owner_name' => 'Owner 1',
                'email' => 'default-store@example.com',
                'phone' => '0800000001',
                'address' => 'Jl. Utama No.1',
                'province' => 'Jawa Tengah',
                'city' => 'Semarang',
                'postal_code' => '50111',
                'tax_id' => null,
                'business_license' => null,
                'logo_url' => null,
                'theme' => 'default',
                'settings' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fresh Market',
                'code' => 'fresh',
                'domain' => null,
                'owner_name' => 'Owner 2',
                'email' => 'fresh@example.com',
                'phone' => '0800000002',
                'address' => 'Jl. Pasar No.2',
                'province' => 'DKI Jakarta',
                'city' => 'Jakarta',
                'postal_code' => '10110',
                'tax_id' => null,
                'business_license' => null,
                'logo_url' => null,
                'theme' => 'default',
                'settings' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($stores as $s) {
            $exists = DB::table('stores')->where('code', $s['code'])->exists();
            if (!$exists) {
                DB::table('stores')->insert($s);
            }
        }

        $storeFreshId = (int) DB::table('stores')->where('code', 'fresh')->value('id');

        // Outlets
        $outlets = [
            // store 1
            ['store_id' => 1, 'name' => 'Default Main', 'code' => 'def-main', 'type' => 'main', 'phone' => '0800000001', 'address' => 'Jl. Utama No.1', 'province' => 'Jawa Tengah', 'city' => 'Semarang', 'postal_code' => '50111', 'is_active' => true],
            ['store_id' => 1, 'name' => 'Default Branch A', 'code' => 'def-a', 'type' => 'branch', 'phone' => '0800000003', 'address' => 'Jl. Cabang A No.3', 'province' => 'Jawa Tengah', 'city' => 'Semarang', 'postal_code' => '50112', 'is_active' => true],
            // store fresh
            ['store_id' => $storeFreshId, 'name' => 'Fresh Main', 'code' => 'fresh-main', 'type' => 'main', 'phone' => '0800000010', 'address' => 'Jl. Pasar No.2', 'province' => 'DKI Jakarta', 'city' => 'Jakarta', 'postal_code' => '10110', 'is_active' => true],
            ['store_id' => $storeFreshId, 'name' => 'Fresh Branch B', 'code' => 'fresh-b', 'type' => 'branch', 'phone' => '0800000011', 'address' => 'Jl. Cabang B No.4', 'province' => 'DKI Jakarta', 'city' => 'Jakarta', 'postal_code' => '10111', 'is_active' => true],
        ];

        foreach ($outlets as $o) {
            $exists = DB::table('outlets')->where('code', $o['code'])->exists();
            if (!$exists) {
                DB::table('outlets')->insert(array_merge($o, [
                    'manager_name' => null,
                    'email' => null,
                    'latitude' => null,
                    'longitude' => null,
                    'operating_hours' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }

        // Categories minimal if empty
        if (DB::table('categories')->count() === 0) {
            DB::table('categories')->insert([
                ['name' => 'Buah', 'slug' => 'buah', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Sayur', 'slug' => 'sayur', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
        $catBuah = (int) DB::table('categories')->where('slug', 'buah')->value('id');
        $catSayur = (int) DB::table('categories')->where('slug', 'sayur')->value('id');

        // Products for store 1 and fresh store
        $products = [
            // store 1
            ['store_id' => 1, 'name' => 'Apel Merah', 'slug' => 'apel-merah-'.Str::random(4), 'sku' => 'APL-RED-1', 'price' => 25000, 'compare_at_price' => null, 'stock_qty' => 50, 'unit' => 'kg', 'is_active' => true, 'is_featured' => true, 'is_bestseller' => true],
            ['store_id' => 1, 'name' => 'Wortel Segar', 'slug' => 'wortel-segar-'.Str::random(4), 'sku' => 'WRT-SEG-1', 'price' => 18000, 'compare_at_price' => null, 'stock_qty' => 80, 'unit' => 'kg', 'is_active' => true, 'is_featured' => false, 'is_bestseller' => false],
            // fresh store
            ['store_id' => $storeFreshId, 'name' => 'Jeruk Manis', 'slug' => 'jeruk-manis-'.Str::random(4), 'sku' => 'JRK-MNS-1', 'price' => 22000, 'compare_at_price' => null, 'stock_qty' => 60, 'unit' => 'kg', 'is_active' => true, 'is_featured' => true, 'is_bestseller' => false],
            ['store_id' => $storeFreshId, 'name' => 'Bayam Hijau', 'slug' => 'bayam-hijau-'.Str::random(4), 'sku' => 'BYM-HIJ-1', 'price' => 12000, 'compare_at_price' => null, 'stock_qty' => 100, 'unit' => 'ikat', 'is_active' => true, 'is_featured' => false, 'is_bestseller' => true],
        ];

        foreach ($products as $p) {
            $exists = DB::table('products')->where('sku', $p['sku'])->where('store_id', $p['store_id'])->exists();
            if (!$exists) {
                DB::table('products')->insert(array_merge($p, [
                    'short_description' => null,
                    'description' => null,
                    'main_image_path' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
                $productId = (int) DB::getPdo()->lastInsertId();
                $categoryId = Str::contains($p['name'], ['Apel','Jeruk']) ? $catBuah : $catSayur;
                DB::table('product_categories')->insert([
                    'product_id' => $productId,
                    'category_id' => $categoryId,
                ]);
            }
        }
    }
}
