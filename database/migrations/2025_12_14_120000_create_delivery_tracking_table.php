<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('delivery_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('status')->default('pending'); // pending, assigned, picked, on_the_way, arrived, delivered
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 10, 8)->nullable();
            $table->string('address')->nullable();
            $table->integer('estimated_minutes')->nullable(); // ETA dalam menit
            $table->decimal('distance_km', 8, 2)->nullable(); // Jarak tersisa dalam km
            $table->timestamp('picked_at')->nullable();
            $table->timestamp('on_the_way_at')->nullable();
            $table->timestamp('arrived_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('order_id');
            $table->index('status');
            $table->index(['latitude', 'longitude']);
        });
        
        // Add tracking fields to orders table
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'driver_id')) {
                $table->foreignId('driver_id')->nullable()->after('shipping_method_id')->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('orders', 'tracking_enabled')) {
                $table->boolean('tracking_enabled')->default(false)->after('driver_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_tracking');
        
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'driver_id')) {
                $table->dropForeign(['driver_id']);
                $table->dropColumn('driver_id');
            }
            if (Schema::hasColumn('orders', 'tracking_enabled')) {
                $table->dropColumn('tracking_enabled');
            }
        });
    }
};
