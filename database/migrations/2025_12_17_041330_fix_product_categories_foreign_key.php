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
        // Get existing foreign key names
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'product_categories' 
            AND CONSTRAINT_NAME LIKE '%foreign%'
        ");
        
        // Drop existing foreign key constraints
        foreach ($foreignKeys as $fk) {
            try {
                DB::statement("ALTER TABLE `product_categories` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
            } catch (\Exception $e) {
                // Ignore if constraint doesn't exist
            }
        }
        
        // Re-add foreign key constraints with correct table references
        Schema::table('product_categories', function (Blueprint $table) {
            $table->foreign('product_id', 'product_categories_product_id_foreign')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');
                  
            $table->foreign('category_id', 'product_categories_category_id_foreign')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['category_id']);
        });
    }
};
