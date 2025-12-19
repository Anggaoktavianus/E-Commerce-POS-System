<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_cash_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_id')->constrained('pos_shifts')->onDelete('restrict');
            $table->foreignId('outlet_id')->constrained('outlets')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->enum('type', ['deposit', 'withdrawal', 'transfer', 'adjustment']);
            $table->decimal('amount', 15, 2);
            $table->text('reason')->nullable();
            $table->string('reference_number', 100)->nullable();
            $table->timestamps();

            $table->index('shift_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_cash_movements');
    }
};
