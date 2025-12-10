<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // settings
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // menus
        Schema::create('navigation_menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->index(); // header, footer_column_1..n
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('navigation_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('navigation_menu_id')->constrained('navigation_menus')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('navigation_links')->cascadeOnDelete();
            $table->string('label');
            $table->string('url')->nullable();
            $table->string('route_name')->nullable();
            $table->string('target')->nullable(); // _self, _blank
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // carousels
        Schema::create('carousels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('key')->unique(); // e.g., home_hero
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('carousel_slides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carousel_id')->constrained('carousels')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->string('image_path');
            $table->string('mobile_image_path')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // features
        Schema::create('features', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description')->nullable();
            $table->string('icon_class')->nullable();
            $table->string('image_path')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // products
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->nullable();
            $table->string('short_description')->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->decimal('compare_at_price', 12, 2)->nullable();
            $table->integer('stock_qty')->default(0);
            $table->string('unit')->nullable(); // kg, pcs, etc.
            $table->string('main_image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_bestseller')->default(false); // optional flag
            $table->timestamps();
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('image_path');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('product_categories', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->primary(['product_id', 'category_id']);
        });

        // banners
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->string('image_path');
            $table->string('position')->default('home_middle'); // home_top|home_middle|home_bottom
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // collections (bestseller, etc.)
        Schema::create('home_collections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('key')->unique(); // bestseller, featured, etc.
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('home_collection_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('home_collection_id')->constrained('home_collections')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['home_collection_id', 'product_id']);
        });

        // facts (counters)
        Schema::create('facts', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->unsignedBigInteger('value')->default(0);
            $table->string('icon_class')->nullable();
            $table->string('image_path')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // testimonials
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('author_name');
            $table->string('author_title')->nullable();
            $table->string('avatar_path')->nullable();
            $table->text('content');
            $table->unsignedTinyInteger('rating')->default(5); // 1..5
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // social links
        Schema::create('social_links', function (Blueprint $table) {
            $table->id();
            $table->string('platform'); // twitter, facebook, youtube, etc.
            $table->string('url');
            $table->string('icon_class')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_links');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('facts');
        Schema::dropIfExists('home_collection_items');
        Schema::dropIfExists('home_collections');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('features');
        Schema::dropIfExists('carousel_slides');
        Schema::dropIfExists('carousels');
        Schema::dropIfExists('navigation_links');
        Schema::dropIfExists('navigation_menus');
        Schema::dropIfExists('settings');
    }
};
