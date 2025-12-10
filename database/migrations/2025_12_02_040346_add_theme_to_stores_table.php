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
            if (!Schema::hasColumn('stores', 'theme')) {
                $table->string('theme')->default('default')->after('logo_url');
            }
            if (!Schema::hasColumn('stores', 'settings')) {
                $table->json('settings')->nullable()->after('theme');
            }
            if (!Schema::hasColumn('stores', 'domain')) {
                $table->string('domain')->unique()->nullable()->after('code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $drops = [];
            foreach (['theme','settings','domain'] as $col) {
                if (Schema::hasColumn('stores', $col)) {
                    $drops[] = $col;
                }
            }
            if (!empty($drops)) {
                $table->dropColumn($drops);
            }
        });
    }
};
