<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            if (!Schema::hasColumn('stores', 'loc_provinsi_id')) {
                $table->unsignedBigInteger('loc_provinsi_id')->nullable()->after('province');
                $table->unsignedBigInteger('loc_kabkota_id')->nullable()->after('loc_provinsi_id');
                $table->unsignedBigInteger('loc_kecamatan_id')->nullable()->after('loc_kabkota_id');
                $table->unsignedBigInteger('loc_desa_id')->nullable()->after('loc_kecamatan_id');
                $table->index(['loc_provinsi_id']);
                $table->index(['loc_kabkota_id']);
                $table->index(['loc_kecamatan_id']);
                $table->index(['loc_desa_id']);
            }
        });

        Schema::table('outlets', function (Blueprint $table) {
            if (!Schema::hasColumn('outlets', 'loc_provinsi_id')) {
                $table->unsignedBigInteger('loc_provinsi_id')->nullable()->after('province');
                $table->unsignedBigInteger('loc_kabkota_id')->nullable()->after('loc_provinsi_id');
                $table->unsignedBigInteger('loc_kecamatan_id')->nullable()->after('loc_kabkota_id');
                $table->unsignedBigInteger('loc_desa_id')->nullable()->after('loc_kecamatan_id');
                $table->index(['loc_provinsi_id']);
                $table->index(['loc_kabkota_id']);
                $table->index(['loc_kecamatan_id']);
                $table->index(['loc_desa_id']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            foreach (['loc_provinsi_id','loc_kabkota_id','loc_kecamatan_id','loc_desa_id'] as $col) {
                if (Schema::hasColumn('stores', $col)) {
                    $table->dropIndex([$col]);
                    $table->dropColumn($col);
                }
            }
        });
        Schema::table('outlets', function (Blueprint $table) {
            foreach (['loc_provinsi_id','loc_kabkota_id','loc_kecamatan_id','loc_desa_id'] as $col) {
                if (Schema::hasColumn('outlets', $col)) {
                    $table->dropIndex([$col]);
                    $table->dropColumn($col);
                }
            }
        });
    }
};
