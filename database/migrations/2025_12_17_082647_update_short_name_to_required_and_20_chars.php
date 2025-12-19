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
        // Update stores table
        Schema::table('stores', function (Blueprint $table) {
            if (Schema::hasColumn('stores', 'short_name')) {
                // First, update existing NULL values to a default value
                DB::table('stores')->whereNull('short_name')->update(['short_name' => DB::raw('LEFT(name, 20)')]);
                
                // Change column to not null and reduce size to 20
                $table->string('short_name', 20)->nullable(false)->change();
            }
        });

        // Update outlets table
        Schema::table('outlets', function (Blueprint $table) {
            if (Schema::hasColumn('outlets', 'short_name')) {
                // First, update existing NULL values to a default value
                DB::table('outlets')->whereNull('short_name')->update(['short_name' => DB::raw('LEFT(name, 20)')]);
                
                // Change column to not null and reduce size to 20
                $table->string('short_name', 20)->nullable(false)->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            if (Schema::hasColumn('stores', 'short_name')) {
                $table->string('short_name', 50)->nullable()->change();
            }
        });

        Schema::table('outlets', function (Blueprint $table) {
            if (Schema::hasColumn('outlets', 'short_name')) {
                $table->string('short_name', 50)->nullable()->change();
            }
        });
    }
};
