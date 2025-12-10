<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hanya tambahkan kolom jika belum ada
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'mitra', 'customer'])->default('customer')->after('password');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'company_name')) {
                $table->string('company_name')->nullable()->after('address');
            }
            if (!Schema::hasColumn('users', 'company_address')) {
                $table->text('company_address')->nullable()->after('company_name');
            }
            if (!Schema::hasColumn('users', 'company_phone')) {
                $table->string('company_phone', 20)->nullable()->after('company_address');
            }
            if (!Schema::hasColumn('users', 'npwp')) {
                $table->string('npwp', 25)->nullable()->after('company_phone');
            }
            if (!Schema::hasColumn('users', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('npwp');
            }
            // Hanya tambahkan softDeletes jika belum ada
            if (!Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'role',
                'phone',
                'address',
                'company_name',
                'company_address',
                'company_phone',
                'npwp',
                'is_verified'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }

            if (Schema::hasColumn('users', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};