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
        Schema::create('item_penyewaan', function (Blueprint $table) {
            $table->id('iditem_penyewaan');
        
            $table->unsignedBigInteger('produk_idproduk');
            $table->unsignedBigInteger('harga_idharga');
            $table->unsignedBigInteger('penyewaan_idpenyewaan');
        
            $table->foreign('produk_idproduk')->references('idproduk')->on('produk');
            $table->foreign('harga_idharga')->references('idharga')->on('harga');
            $table->foreign('penyewaan_idpenyewaan')->references('idpenyewaan')->on('penyewaan');
        
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_penyewaan');
    }
};
