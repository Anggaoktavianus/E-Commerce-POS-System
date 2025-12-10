<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Shipping methods table
        if (!Schema::hasTable('shipping_methods')) {
            Schema::create('shipping_methods', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // GoSend Instant, JNE REG
                $table->string('code')->unique(); // gosend_instant, jne_reg
                $table->enum('type', ['instant', 'same_day', 'regular', 'express', 'pickup'])->default('regular');
                $table->string('logo_url')->nullable();
                $table->boolean('is_active')->default(true);
                $table->json('service_areas')->nullable(); // City coverage
                $table->integer('max_distance_km')->nullable(); // For instant delivery
                $table->timestamps();
            });
        }

        // Shipping costs table
        if (!Schema::hasTable('shipping_costs')) {
            Schema::create('shipping_costs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('shipping_method_id')->constrained();
                $table->string('origin_city');
                $table->string('destination_city');
                $table->decimal('cost', 10, 0);
                $table->string('estimated_days');
                $table->decimal('min_weight', 8, 2)->default(0);
                $table->decimal('max_weight', 8, 2)->default(50);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['origin_city', 'destination_city'], 'shipping_cities_index');
                $table->index(['shipping_method_id'], 'shipping_method_index');
            });
        }

        // Add fresh product attributes to products table
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'shelf_life_days')) {
                $table->integer('shelf_life_days')->default(30)->after('price');
            }
            if (!Schema::hasColumn('products', 'requires_cold_chain')) {
                $table->boolean('requires_cold_chain')->default(false)->after('shelf_life_days');
            }
            if (!Schema::hasColumn('products', 'shipping_type')) {
                $table->enum('shipping_type', ['fresh', 'dry', 'frozen'])->default('dry')->after('requires_cold_chain');
            }
        });

        // Update orders table for shipping info
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'shipping_method_id')) {
                $table->foreignId('shipping_method_id')->nullable()->after('status');
            }
            if (!Schema::hasColumn('orders', 'shipping_cost')) {
                $table->decimal('shipping_cost', 10, 0)->default(0)->after('shipping_method_id');
            }
            if (!Schema::hasColumn('orders', 'tracking_number')) {
                $table->string('tracking_number')->nullable()->after('shipping_cost');
            }
            if (!Schema::hasColumn('orders', 'estimated_delivery_date')) {
                $table->date('estimated_delivery_date')->nullable()->after('tracking_number');
            }
            if (!Schema::hasColumn('orders', 'shipping_status')) {
                $table->enum('shipping_status', ['pending', 'picked', 'transit', 'delivered'])->default('pending')->after('estimated_delivery_date');
            }
            if (!Schema::hasColumn('orders', 'shipping_notes')) {
                $table->text('shipping_notes')->nullable()->after('shipping_status');
            }
        });
    }

    public function down()
    {
        Schema::dropIfExists('shipping_costs');
        Schema::dropIfExists('shipping_methods');
        
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['shelf_life_days', 'requires_cold_chain', 'shipping_type']);
        });
        
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['shipping_method_id']);
            $table->dropColumn(['shipping_method_id', 'shipping_cost', 'tracking_number', 'estimated_delivery_date', 'shipping_status', 'shipping_notes']);
        });
    }
};
