<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixAssetPathsSeeder extends Seeder
{
    public function run(): void
    {
        // Fix settings payment image path
        DB::table('settings')->where('key', 'payment_image_path')->update([
            'value' => 'fruitables/img/payment.png',
        ]);

        // Fix third hero slide image path if exists (by sort_order=3 on carousel key home_hero)
        $carousel = DB::table('carousels')->where('key','home_hero')->first();
        if ($carousel) {
            DB::table('carousel_slides')
                ->where('carousel_id', $carousel->id)
                ->where('sort_order', 3)
                ->update(['image_path' => 'fruitables/img/hero-img.jpg']);
        }

        // Fix banners image paths
        DB::table('banners')
            ->where('position', 'home_top')
            ->update(['image_path' => 'fruitables/img/banner-fruits.jpg']);

        DB::table('banners')
            ->where('position', 'home_middle')
            ->update(['image_path' => 'fruitables/img/baner-1.png']);

        // Fix vegetable item image extension mismatch (.jpg -> .png)
        DB::table('products')
            ->where('main_image_path', 'fruitables/img/vegetable-item-3.jpg')
            ->update(['main_image_path' => 'fruitables/img/vegetable-item-3.png']);
    }
}
