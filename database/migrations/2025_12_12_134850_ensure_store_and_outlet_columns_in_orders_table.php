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
            // Add store_id if it doesn't exist
            if (!Schema::hasColumn('orders', 'store_id')) {
                $table->foreignId('store_id')->nullable()->after('id')->constrained('stores')->onDelete('set null');
                $table->index('store_id');
            }
            
            // Add outlet_id if it doesn't exist
            if (!Schema::hasColumn('orders', 'outlet_id')) {
                $table->foreignId('outlet_id')->nullable()->after('store_id')->constrained('outlets')->onDelete('set null');
                $table->index('outlet_id');
            }
        });
        
        // If columns exist but don't have foreign keys, add them
        try {
            // Check if foreign key exists for store_id
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'orders' 
                AND COLUMN_NAME = 'store_id' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            if (empty($foreignKeys) && Schema::hasColumn('orders', 'store_id')) {
                DB::statement('ALTER TABLE `orders` ADD CONSTRAINT `orders_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE SET NULL');
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to add store_id foreign key: ' . $e->getMessage());
        }
        
        try {
            // Check if foreign key exists for outlet_id
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'orders' 
                AND COLUMN_NAME = 'outlet_id' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            if (empty($foreignKeys) && Schema::hasColumn('orders', 'outlet_id')) {
                DB::statement('ALTER TABLE `orders` ADD CONSTRAINT `orders_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE SET NULL');
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to add outlet_id foreign key: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop foreign keys first
            if (Schema::hasColumn('orders', 'outlet_id')) {
                try {
                    $table->dropForeign(['outlet_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist
                }
                $table->dropIndex(['outlet_id']);
                $table->dropColumn('outlet_id');
            }
            
            if (Schema::hasColumn('orders', 'store_id')) {
                try {
                    $table->dropForeign(['store_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist
                }
                $table->dropIndex(['store_id']);
                $table->dropColumn('store_id');
            }
        });
    }
};
