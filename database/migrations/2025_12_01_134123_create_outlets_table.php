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
        Schema::create('outlets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->string('name', 255);
            $table->string('code', 50)->unique();
            $table->enum('type', ['main', 'branch', 'pickup_point'])->default('branch');
            $table->string('manager_name', 255)->nullable();
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->text('address');
            $table->string('province', 100);
            $table->string('city', 100);
            $table->string('postal_code', 10);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->json('operating_hours')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['store_id']);
            $table->index(['code']);
            $table->index(['type']);
            $table->index(['is_active']);
            $table->index(['city', 'province']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outlets');
    }
};
