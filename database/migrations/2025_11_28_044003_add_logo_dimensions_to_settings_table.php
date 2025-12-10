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
        Schema::table('settings', function (Blueprint $table) {
            $table->integer('logo_width')->nullable()->after('value');
            $table->integer('logo_height')->nullable()->after('logo_width');
            $table->string('logo_object_fit')->default('contain')->after('logo_height');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['logo_width', 'logo_height', 'logo_object_fit']);
        });
    }
};
