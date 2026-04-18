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
        Schema::create('paket', function (Blueprint $table) {
    $table->id();
    $table->string('nama_paket');
    $table->integer('harga_paket');
    $table->string('gambar_paket')->nullable();
    
    // nullable = paket pusat
    $table->unsignedBigInteger('cabang_id')->nullable();

    $table->boolean('is_template')->default(false);

    $table->timestamps();

    // ✅ FK manual (karena PK cabang = idcabang)
    $table->foreign('cabang_id')
          ->references('idcabang')
          ->on('cabang')
          ->nullOnDelete();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paket');
    }
};
