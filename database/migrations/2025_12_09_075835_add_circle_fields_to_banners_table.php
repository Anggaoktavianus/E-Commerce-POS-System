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
        Schema::table('banners', function (Blueprint $table) {
            $table->string('circle_number')->nullable();
            $table->string('circle_value')->nullable();
            $table->string('circle_unit')->nullable();
            $table->boolean('show_circle')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn(['circle_number', 'circle_value', 'circle_unit', 'show_circle']);
        });
    }
};
