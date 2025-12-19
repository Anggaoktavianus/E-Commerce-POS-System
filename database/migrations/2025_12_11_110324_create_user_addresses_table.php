<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('label')->nullable()->comment('Label alamat, e.g. Rumah, Kantor, Alamat Utama');
            $table->string('recipient_name')->comment('Nama penerima');
            $table->string('recipient_phone', 20)->comment('Nomor telepon penerima');
            $table->text('address')->comment('Alamat lengkap');
            $table->string('province')->nullable()->comment('Provinsi (teks)');
            $table->string('city')->nullable()->comment('Kota (teks)');
            $table->string('postal_code', 10)->nullable();
            $table->string('country')->default('Indonesia');
            
            // Location references
            $table->unsignedBigInteger('loc_provinsi_id')->nullable();
            $table->unsignedBigInteger('loc_kabkota_id')->nullable();
            $table->unsignedBigInteger('loc_kecamatan_id')->nullable();
            $table->unsignedBigInteger('loc_desa_id')->nullable();
            
            // Coordinates
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Additional info
            $table->text('notes')->nullable()->comment('Catatan tambahan, e.g. Samping SMA 7');
            $table->boolean('is_primary')->default(false)->comment('Alamat utama');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['user_id', 'is_primary']);
            $table->index(['user_id', 'is_active']);
            $table->index(['latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
