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
        Schema::table('stores', function (Blueprint $table) {
            if (!Schema::hasColumn('stores', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable()->after('loc_desa_id');
            }
            if (!Schema::hasColumn('stores', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            }
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            if (Schema::hasColumn('stores', 'latitude')) {
                $table->dropIndex(['latitude', 'longitude']);
                $table->dropColumn(['latitude', 'longitude']);
            }
        });
    }
};
