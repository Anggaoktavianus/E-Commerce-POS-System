<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'store_id')) {
                $table->unsignedBigInteger('store_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('orders', 'outlet_id')) {
                $table->unsignedBigInteger('outlet_id')->nullable()->after('store_id');
            }

            $table->index(['store_id']);
            $table->index(['outlet_id']);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'outlet_id')) {
                $table->dropIndex(['outlet_id']);
                $table->dropColumn('outlet_id');
            }
            if (Schema::hasColumn('orders', 'store_id')) {
                $table->dropIndex(['store_id']);
                $table->dropColumn('store_id');
            }
        });
    }
};
