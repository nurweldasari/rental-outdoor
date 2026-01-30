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
        Schema::create('stok_cabang', function (Blueprint $table) {
    $table->id('idstok');

    $table->unsignedBigInteger('produk_idproduk');
    $table->unsignedBigInteger('cabang_idcabang');
    $table->integer('jumlah')->default(0); // stok aktual cabang

    $table->boolean('is_active')->default(true);
    $table->timestamps();

    $table->foreign('produk_idproduk')->references('idproduk')->on('produk');
    $table->foreign('cabang_idcabang')->references('idcabang')->on('cabang');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_cabang');
    }
};
