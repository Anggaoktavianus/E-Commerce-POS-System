<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add processed_at column
            if (!Schema::hasColumn('orders', 'processed_at')) {
                $table->timestamp('processed_at')->nullable()->after('paid_at');
            }
            
            // Add other missing columns if they don't exist
            if (!Schema::hasColumn('orders', 'shipped_at')) {
                $table->timestamp('shipped_at')->nullable()->after('processed_at');
            }
            if (!Schema::hasColumn('orders', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable()->after('shipped_at');
            }
            if (!Schema::hasColumn('orders', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('delivered_at');
            }
            if (!Schema::hasColumn('orders', 'cancel_reason')) {
                $table->text('cancel_reason')->nullable()->after('cancelled_at');
            }
            if (!Schema::hasColumn('orders', 'tracking_number')) {
                $table->string('tracking_number')->nullable()->after('cancel_reason');
            }
            if (!Schema::hasColumn('orders', 'store_id')) {
                $table->foreignId('store_id')->nullable()->after('id')->constrained()->onDelete('set null');
            }
            if (!Schema::hasColumn('orders', 'outlet_id')) {
                $table->foreignId('outlet_id')->nullable()->after('store_id')->constrained()->onDelete('set null');
            }
            if (!Schema::hasColumn('orders', 'shipping_method_id')) {
                $table->foreignId('shipping_method_id')->nullable()->after('shipping_address')->constrained()->onDelete('set null');
            }
        });
        
        // Update status enum to include new statuses if needed
        // Check current enum values first
        try {
            DB::statement("ALTER TABLE `orders` MODIFY COLUMN `status` ENUM('pending', 'paid', 'processing', 'shipped', 'delivered', 'completed', 'failed', 'cancelled', 'expired') DEFAULT 'pending'");
        } catch (\Exception $e) {
            // If enum modification fails, it might already have the values or table doesn't exist
            \Log::warning('Failed to modify status enum: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'processed_at')) {
                $table->dropColumn('processed_at');
            }
            if (Schema::hasColumn('orders', 'shipped_at')) {
                $table->dropColumn('shipped_at');
            }
            if (Schema::hasColumn('orders', 'delivered_at')) {
                $table->dropColumn('delivered_at');
            }
            if (Schema::hasColumn('orders', 'cancelled_at')) {
                $table->dropColumn('cancelled_at');
            }
            if (Schema::hasColumn('orders', 'cancel_reason')) {
                $table->dropColumn('cancel_reason');
            }
            if (Schema::hasColumn('orders', 'tracking_number')) {
                $table->dropColumn('tracking_number');
            }
            
            // Drop foreign keys if they exist (with error handling)
            if (Schema::hasColumn('orders', 'store_id')) {
                try {
                    $table->dropForeign(['store_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist
                }
                $table->dropColumn('store_id');
            }
            if (Schema::hasColumn('orders', 'outlet_id')) {
                try {
                    $table->dropForeign(['outlet_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist
                }
                $table->dropColumn('outlet_id');
            }
            if (Schema::hasColumn('orders', 'shipping_method_id')) {
                try {
                    $table->dropForeign(['shipping_method_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist
                }
                $table->dropColumn('shipping_method_id');
            }
        });
        
        // Revert status enum (optional, might cause issues if data exists)
        // DB::statement("ALTER TABLE `orders` MODIFY COLUMN `status` ENUM('pending', 'paid', 'failed', 'cancelled', 'expired') DEFAULT 'pending'");
    }
};
