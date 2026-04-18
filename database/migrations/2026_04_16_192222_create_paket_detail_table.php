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
       Schema::create('paket_detail', function (Blueprint $table) {
    $table->id();

    $table->foreignId('paket_id')
          ->constrained('paket')
          ->cascadeOnDelete();

    // ✅ untuk paket pusat (produk)
    $table->unsignedBigInteger('produk_idproduk')->nullable();

    // ✅ untuk paket cabang (stok cabang)
    $table->unsignedBigInteger('stok_cabang_id')->nullable();

    $table->integer('qty');

    $table->timestamps();

    // 🔗 FK ke produk pusat
    $table->foreign('produk_idproduk')
          ->references('idproduk')
          ->on('produk')
          ->nullOnDelete();

    // 🔗 FK ke stok cabang
    $table->foreign('stok_cabang_id')
          ->references('idstok')
          ->on('stok_cabang')
          ->cascadeOnDelete();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paket_detail');
    }
};
