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
            if (!Schema::hasColumn('stores', 'short_name')) {
                $table->string('short_name', 50)->nullable()->after('name');
                $table->index('short_name');
            }
        });

        Schema::table('outlets', function (Blueprint $table) {
            if (!Schema::hasColumn('outlets', 'short_name')) {
                $table->string('short_name', 50)->nullable()->after('name');
                $table->index('short_name');
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
                $table->dropIndex(['short_name']);
                $table->dropColumn('short_name');
            }
        });

        Schema::table('outlets', function (Blueprint $table) {
            if (Schema::hasColumn('outlets', 'short_name')) {
                $table->dropIndex(['short_name']);
                $table->dropColumn('short_name');
            }
        });
    }
};
