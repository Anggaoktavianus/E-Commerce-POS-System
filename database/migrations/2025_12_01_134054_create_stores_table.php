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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('code', 50)->unique();
            $table->string('owner_name', 255);
            $table->string('email')->unique();
            $table->string('phone', 20);
            $table->text('address');
            $table->string('province', 100);
            $table->string('city', 100);
            $table->string('postal_code', 10);
            $table->string('tax_id', 50)->nullable();
            $table->string('business_license', 100)->nullable();
            $table->string('logo_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['code', 'email']);
            $table->index(['is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
