<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private function isFkCompatible(string $tbl, string $col, string $refTbl, string $refCol): bool
    {
        try {
            $db = DB::getDatabaseName();
            $sql = "SELECT TABLE_NAME, COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH, COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND ( (TABLE_NAME = ? AND COLUMN_NAME = ?) OR (TABLE_NAME = ? AND COLUMN_NAME = ?) )";
            $rows = DB::select($sql, [$db, $tbl, $col, $refTbl, $refCol]);
            if (count($rows) < 2) return false;
            // Normalize
            $a = $rows[0]->TABLE_NAME === $tbl ? $rows[0] : $rows[1];
            $b = $rows[0]->TABLE_NAME === $refTbl ? $rows[0] : $rows[1];
            // Compare by COLUMN_TYPE which includes length/unsigned
            return strtolower($a->COLUMN_TYPE) === strtolower($b->COLUMN_TYPE);
        } catch (\Throwable $e) {
            return false;
        }
    }
    public function up(): void
    {
        // Add FKs only if reference tables exist
        $hasProv = Schema::hasTable('loc_provinsis');
        $hasKab = Schema::hasTable('loc_kabkotas');
        $hasKec = Schema::hasTable('loc_kecamatans');
        $hasDesa = Schema::hasTable('loc_desas');

        // Best-effort cleanup: set invalid references to null before adding FKs
        try {
            if ($hasProv && Schema::hasColumn('users','loc_provinsi_id')) {
                DB::statement("UPDATE users u LEFT JOIN loc_provinsis p ON u.loc_provinsi_id = p.id SET u.loc_provinsi_id = NULL WHERE u.loc_provinsi_id IS NOT NULL AND p.id IS NULL");
            }
            if ($hasKab && Schema::hasColumn('users','loc_kabkota_id')) {
                DB::statement("UPDATE users u LEFT JOIN loc_kabkotas k ON u.loc_kabkota_id = k.id SET u.loc_kabkota_id = NULL WHERE u.loc_kabkota_id IS NOT NULL AND k.id IS NULL");
            }
            if ($hasKec && Schema::hasColumn('users','loc_kecamatan_id')) {
                DB::statement("UPDATE users u LEFT JOIN loc_kecamatans c ON u.loc_kecamatan_id = c.id SET u.loc_kecamatan_id = NULL WHERE u.loc_kecamatan_id IS NOT NULL AND c.id IS NULL");
            }
            if ($hasDesa && Schema::hasColumn('users','loc_desa_id')) {
                DB::statement("UPDATE users u LEFT JOIN loc_desas d ON u.loc_desa_id = d.id SET u.loc_desa_id = NULL WHERE u.loc_desa_id IS NOT NULL AND d.id IS NULL");
            }

            if ($hasProv && Schema::hasColumn('stores','loc_provinsi_id')) {
                DB::statement("UPDATE stores s LEFT JOIN loc_provinsis p ON s.loc_provinsi_id = p.id SET s.loc_provinsi_id = NULL WHERE s.loc_provinsi_id IS NOT NULL AND p.id IS NULL");
            }
            if ($hasKab && Schema::hasColumn('stores','loc_kabkota_id')) {
                DB::statement("UPDATE stores s LEFT JOIN loc_kabkotas k ON s.loc_kabkota_id = k.id SET s.loc_kabkota_id = NULL WHERE s.loc_kabkota_id IS NOT NULL AND k.id IS NULL");
            }
            if ($hasKec && Schema::hasColumn('stores','loc_kecamatan_id')) {
                DB::statement("UPDATE stores s LEFT JOIN loc_kecamatans c ON s.loc_kecamatan_id = c.id SET s.loc_kecamatan_id = NULL WHERE s.loc_kecamatan_id IS NOT NULL AND c.id IS NULL");
            }
            if ($hasDesa && Schema::hasColumn('stores','loc_desa_id')) {
                DB::statement("UPDATE stores s LEFT JOIN loc_desas d ON s.loc_desa_id = d.id SET s.loc_desa_id = NULL WHERE s.loc_desa_id IS NOT NULL AND d.id IS NULL");
            }

            if ($hasProv && Schema::hasColumn('outlets','loc_provinsi_id')) {
                DB::statement("UPDATE outlets o LEFT JOIN loc_provinsis p ON o.loc_provinsi_id = p.id SET o.loc_provinsi_id = NULL WHERE o.loc_provinsi_id IS NOT NULL AND p.id IS NULL");
            }
            if ($hasKab && Schema::hasColumn('outlets','loc_kabkota_id')) {
                DB::statement("UPDATE outlets o LEFT JOIN loc_kabkotas k ON o.loc_kabkota_id = k.id SET o.loc_kabkota_id = NULL WHERE o.loc_kabkota_id IS NOT NULL AND k.id IS NULL");
            }
            if ($hasKec && Schema::hasColumn('outlets','loc_kecamatan_id')) {
                DB::statement("UPDATE outlets o LEFT JOIN loc_kecamatans c ON o.loc_kecamatan_id = c.id SET o.loc_kecamatan_id = NULL WHERE o.loc_kecamatan_id IS NOT NULL AND c.id IS NULL");
            }
            if ($hasDesa && Schema::hasColumn('outlets','loc_desa_id')) {
                DB::statement("UPDATE outlets o LEFT JOIN loc_desas d ON o.loc_desa_id = d.id SET o.loc_desa_id = NULL WHERE o.loc_desa_id IS NOT NULL AND d.id IS NULL");
            }
        } catch (\Throwable $e) {
            // ignore, continue to attempt adding constraints best-effort
        }

        // users
        Schema::table('users', function (Blueprint $table) use ($hasProv,$hasKab,$hasKec,$hasDesa) {
            // guard with placeholders; actual compat check is outside closure using isFkCompatible via static self
        });
        if ($hasProv && Schema::hasColumn('users','loc_provinsi_id') && $this->isFkCompatible('users','loc_provinsi_id','loc_provinsis','id')) {
            Schema::table('users', function (Blueprint $table) { $table->foreign('loc_provinsi_id')->references('id')->on('loc_provinsis')->nullOnDelete(); });
        }
        if ($hasKab && Schema::hasColumn('users','loc_kabkota_id') && $this->isFkCompatible('users','loc_kabkota_id','loc_kabkotas','id')) {
            Schema::table('users', function (Blueprint $table) { $table->foreign('loc_kabkota_id')->references('id')->on('loc_kabkotas')->nullOnDelete(); });
        }
        if ($hasKec && Schema::hasColumn('users','loc_kecamatan_id') && $this->isFkCompatible('users','loc_kecamatan_id','loc_kecamatans','id')) {
            Schema::table('users', function (Blueprint $table) { $table->foreign('loc_kecamatan_id')->references('id')->on('loc_kecamatans')->nullOnDelete(); });
        }
        if ($hasDesa && Schema::hasColumn('users','loc_desa_id') && $this->isFkCompatible('users','loc_desa_id','loc_desas','id')) {
            Schema::table('users', function (Blueprint $table) { $table->foreign('loc_desa_id')->references('id')->on('loc_desas')->nullOnDelete(); });
        }

        // stores
        Schema::table('stores', function (Blueprint $table) use ($hasProv,$hasKab,$hasKec,$hasDesa) {});
        if ($hasProv && Schema::hasColumn('stores','loc_provinsi_id') && $this->isFkCompatible('stores','loc_provinsi_id','loc_provinsis','id')) {
            Schema::table('stores', function (Blueprint $table) { $table->foreign('loc_provinsi_id')->references('id')->on('loc_provinsis')->nullOnDelete(); });
        }
        if ($hasKab && Schema::hasColumn('stores','loc_kabkota_id') && $this->isFkCompatible('stores','loc_kabkota_id','loc_kabkotas','id')) {
            Schema::table('stores', function (Blueprint $table) { $table->foreign('loc_kabkota_id')->references('id')->on('loc_kabkotas')->nullOnDelete(); });
        }
        if ($hasKec && Schema::hasColumn('stores','loc_kecamatan_id') && $this->isFkCompatible('stores','loc_kecamatan_id','loc_kecamatans','id')) {
            Schema::table('stores', function (Blueprint $table) { $table->foreign('loc_kecamatan_id')->references('id')->on('loc_kecamatans')->nullOnDelete(); });
        }
        if ($hasDesa && Schema::hasColumn('stores','loc_desa_id') && $this->isFkCompatible('stores','loc_desa_id','loc_desas','id')) {
            Schema::table('stores', function (Blueprint $table) { $table->foreign('loc_desa_id')->references('id')->on('loc_desas')->nullOnDelete(); });
        }

        // outlets
        Schema::table('outlets', function (Blueprint $table) use ($hasProv,$hasKab,$hasKec,$hasDesa) {});
        if ($hasProv && Schema::hasColumn('outlets','loc_provinsi_id') && $this->isFkCompatible('outlets','loc_provinsi_id','loc_provinsis','id')) {
            Schema::table('outlets', function (Blueprint $table) { $table->foreign('loc_provinsi_id')->references('id')->on('loc_provinsis')->nullOnDelete(); });
        }
        if ($hasKab && Schema::hasColumn('outlets','loc_kabkota_id') && $this->isFkCompatible('outlets','loc_kabkota_id','loc_kabkotas','id')) {
            Schema::table('outlets', function (Blueprint $table) { $table->foreign('loc_kabkota_id')->references('id')->on('loc_kabkotas')->nullOnDelete(); });
        }
        if ($hasKec && Schema::hasColumn('outlets','loc_kecamatan_id') && $this->isFkCompatible('outlets','loc_kecamatan_id','loc_kecamatans','id')) {
            Schema::table('outlets', function (Blueprint $table) { $table->foreign('loc_kecamatan_id')->references('id')->on('loc_kecamatans')->nullOnDelete(); });
        }
        if ($hasDesa && Schema::hasColumn('outlets','loc_desa_id') && $this->isFkCompatible('outlets','loc_desa_id','loc_desas','id')) {
            Schema::table('outlets', function (Blueprint $table) { $table->foreign('loc_desa_id')->references('id')->on('loc_desas')->nullOnDelete(); });
        }
    }

    public function down(): void
    {
        // Drop FKs if exist
        foreach (['users','stores','outlets'] as $tbl) {
            Schema::table($tbl, function (Blueprint $table) {
                foreach (['loc_provinsi_id','loc_kabkota_id','loc_kecamatan_id','loc_desa_id'] as $col) {
                    if (Schema::hasColumn($table->getTable(), $col)) {
                        try { $table->dropForeign([$col]); } catch (\Throwable $e) {}
                    }
                }
            });
        }
    }
};
