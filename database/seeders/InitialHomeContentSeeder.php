<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InitialHomeContentSeeder extends Seeder
{
    public function run(): void
    {
        // SETTINGS
        DB::table('settings')->insertOrIgnore([
            ['key' => 'brand_name', 'value' => 'Fruitables'],
            ['key' => 'brand_tagline', 'value' => 'Fresh products'],
            ['key' => 'address', 'value' => '123 Market Street, City'],
            ['key' => 'phone', 'value' => '+62 812 3456 7890'],
            ['key' => 'email', 'value' => 'hello@example.com'],
            ['key' => 'newsletter_text', 'value' => 'Get updates and offers'],
            ['key' => 'payment_image_path', 'value' => 'fruitables/img/payment.png'],
        ]);

        // MENUS
        $headerMenuId = DB::table('navigation_menus')->insertGetId([
            'name' => 'Header', 'location' => 'header', 'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $footer1Id = DB::table('navigation_menus')->insertGetId([
            'name' => 'Footer Column 1', 'location' => 'footer_column_1', 'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);

        DB::table('navigation_links')->insert([
            ['navigation_menu_id' => $headerMenuId, 'parent_id' => null, 'label' => 'Home', 'url' => '/', 'route_name' => null, 'target' => '_self', 'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['navigation_menu_id' => $headerMenuId, 'parent_id' => null, 'label' => 'Shop', 'url' => '/shop', 'route_name' => null, 'target' => '_self', 'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['navigation_menu_id' => $headerMenuId, 'parent_id' => null, 'label' => 'Cart', 'url' => '/cart', 'route_name' => null, 'target' => '_self', 'sort_order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['navigation_menu_id' => $headerMenuId, 'parent_id' => null, 'label' => 'Contact', 'url' => '/contact', 'route_name' => null, 'target' => '_self', 'sort_order' => 4, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            ['navigation_menu_id' => $footer1Id, 'parent_id' => null, 'label' => 'About', 'url' => '#', 'route_name' => null, 'target' => '_self', 'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['navigation_menu_id' => $footer1Id, 'parent_id' => null, 'label' => 'Contact', 'url' => '/contact', 'route_name' => null, 'target' => '_self', 'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // CAROUSEL
        $carouselId = DB::table('carousels')->insertGetId([
            'name' => 'Home Hero', 'key' => 'home_hero', 'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('carousel_slides')->insert([
            [
                'carousel_id' => $carouselId,
                'title' => 'Fresh & Organic',
                'subtitle' => 'Best quality fruits and vegetables',
                'button_text' => 'Shop Now',
                'button_url' => '/shop',
                'image_path' => 'fruitables/img/hero-img-1.png',
                'mobile_image_path' => null,
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'carousel_id' => $carouselId,
                'title' => 'Daily Fresh Picks',
                'subtitle' => 'Handpicked for your family',
                'button_text' => 'Explore',
                'button_url' => '/shop',
                'image_path' => 'fruitables/img/hero-img-2.jpg',
                'mobile_image_path' => null,
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'carousel_id' => $carouselId,
                'title' => 'Healthy & Tasty',
                'subtitle' => 'Eat better, live better',
                'button_text' => 'Get Started',
                'button_url' => '/shop',
                'image_path' => 'fruitables/img/hero-img.jpg',
                'mobile_image_path' => null,
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => now(), 'updated_at' => now(),
            ],
        ]);

        // FEATURES
        DB::table('features')->insert([
            ['title' => 'Free Shipping', 'description' => 'On all orders over $50', 'icon_class' => 'fas fa-shipping-fast', 'image_path' => null, 'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Always Fresh', 'description' => 'Product well package', 'icon_class' => 'fas fa-leaf', 'image_path' => null, 'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Superior Quality', 'description' => 'Quality Products', 'icon_class' => 'fas fa-award', 'image_path' => null, 'sort_order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Support', 'description' => '24/7 support', 'icon_class' => 'fas fa-headset', 'image_path' => null, 'sort_order' => 4, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // CATEGORIES
        $fruitsId = DB::table('categories')->insertGetId([
            'name' => 'Fruits', 'slug' => 'fruits', 'parent_id' => null, 'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $veggiesId = DB::table('categories')->insertGetId([
            'name' => 'Vegetables', 'slug' => 'vegetables', 'parent_id' => null, 'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);

        // PRODUCTS
        $products = [
            ['name' => 'Red Apple', 'slug' => 'red-apple', 'price' => 2.99, 'stock_qty' => 100, 'unit' => 'kg', 'main_image_path' => 'fruitables/img/fruite-item-1.jpg', 'is_active' => true, 'is_featured' => true, 'is_bestseller' => true],
            ['name' => 'Orange', 'slug' => 'orange', 'price' => 1.99, 'stock_qty' => 150, 'unit' => 'kg', 'main_image_path' => 'fruitables/img/fruite-item-2.jpg', 'is_active' => true, 'is_featured' => false, 'is_bestseller' => true],
            ['name' => 'Banana', 'slug' => 'banana', 'price' => 1.50, 'stock_qty' => 200, 'unit' => 'kg', 'main_image_path' => 'fruitables/img/fruite-item-3.jpg', 'is_active' => true, 'is_featured' => false, 'is_bestseller' => false],
            ['name' => 'Broccoli', 'slug' => 'broccoli', 'price' => 2.20, 'stock_qty' => 120, 'unit' => 'kg', 'main_image_path' => 'fruitables/img/vegetable-item-1.jpg', 'is_active' => true, 'is_featured' => true, 'is_bestseller' => true],
            ['name' => 'Tomato', 'slug' => 'tomato', 'price' => 1.30, 'stock_qty' => 180, 'unit' => 'kg', 'main_image_path' => 'fruitables/img/vegetable-item-2.jpg', 'is_active' => true, 'is_featured' => false, 'is_bestseller' => false],
            ['name' => 'Carrot', 'slug' => 'carrot', 'price' => 1.10, 'stock_qty' => 160, 'unit' => 'kg', 'main_image_path' => 'fruitables/img/vegetable-item-3.jpg', 'is_active' => true, 'is_featured' => false, 'is_bestseller' => false],
        ];

        $productIds = [];
        foreach ($products as $p) {
            $productIds[] = DB::table('products')->insertGetId(array_merge([
                'sku' => null,
                'short_description' => null,
                'description' => null,
                'compare_at_price' => null,
                'created_at' => now(), 'updated_at' => now(),
            ], $p));
        }

        // PRODUCT CATEGORIES (assign fruits/vegetables)
        foreach ($productIds as $idx => $pid) {
            $catId = $idx < 3 ? $fruitsId : $veggiesId;
            DB::table('product_categories')->insert([
                'product_id' => $pid,
                'category_id' => $catId,
            ]);
        }

        // BANNERS
        DB::table('banners')->insert([
            [
                'title' => 'Fresh Fruits 50% Off', 'subtitle' => 'Limited Time Offer', 'button_text' => 'Shop Fruits', 'button_url' => '/shop',
                'image_path' => 'fruitables/img/banner-fruits.jpg', 'position' => 'home_top', 'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'title' => 'Organic Vegetables', 'subtitle' => 'Healthy and Green', 'button_text' => 'Shop Veggies', 'button_url' => '/shop',
                'image_path' => 'fruitables/img/baner-1.png', 'position' => 'home_middle', 'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
            ],
        ]);

        // COLLECTIONS (BESTSELLER)
        $bestsellerId = DB::table('home_collections')->insertGetId([
            'name' => 'Bestseller', 'key' => 'bestseller', 'description' => 'Top selling items', 'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $order = 1;
        foreach ($productIds as $pid) {
            DB::table('home_collection_items')->insert([
                'home_collection_id' => $bestsellerId,
                'product_id' => $pid,
                'sort_order' => $order++,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // FACTS
        DB::table('facts')->insert([
            ['label' => 'Happy Customers', 'value' => 1234, 'icon_class' => 'bx bx-smile', 'image_path' => null, 'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Quality Products', 'value' => 245, 'icon_class' => 'bx bx-badge-check', 'image_path' => null, 'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Orders Served', 'value' => 5821, 'icon_class' => 'bx bx-cart', 'image_path' => null, 'sort_order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => 'Awards', 'value' => 12, 'icon_class' => 'bx bx-award', 'image_path' => null, 'sort_order' => 4, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // TESTIMONIALS
        DB::table('testimonials')->insert([
            ['author_name' => 'Ari', 'author_title' => 'Customer', 'avatar_path' => null, 'content' => 'Produk fresh dan pengiriman cepat!', 'rating' => 5, 'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['author_name' => 'Bima', 'author_title' => 'Customer', 'avatar_path' => null, 'content' => 'Harga terjangkau, kualitas bagus.', 'rating' => 5, 'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['author_name' => 'Citra', 'author_title' => 'Customer', 'avatar_path' => null, 'content' => 'Pilihan buah dan sayur lengkap!', 'rating' => 4, 'sort_order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // SOCIAL LINKS
        DB::table('social_links')->insert([
            ['platform' => 'twitter', 'url' => 'https://twitter.com/', 'icon_class' => 'fab fa-twitter', 'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['platform' => 'facebook', 'url' => 'https://facebook.com/', 'icon_class' => 'fab fa-facebook-f', 'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['platform' => 'youtube', 'url' => 'https://youtube.com/', 'icon_class' => 'fab fa-youtube', 'sort_order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['platform' => 'instagram', 'url' => 'https://instagram.com/', 'icon_class' => 'fab fa-instagram', 'sort_order' => 4, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
