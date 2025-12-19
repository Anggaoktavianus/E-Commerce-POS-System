<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_receipt_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->nullable()->constrained('outlets')->onDelete('cascade')->comment('NULL = global template');
            $table->string('name', 255);
            $table->text('template_content')->comment('HTML/Blade template');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('outlet_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_receipt_templates');
    }
};
