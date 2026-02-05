<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_penyewaan', function (Blueprint $table) {
            $table->id('iditem_penyewaan');

            $table->unsignedBigInteger('produk_idproduk');
            $table->unsignedBigInteger('penyewaan_idpenyewaan');

            $table->integer('harga'); // harga saat penyewaan
            $table->integer('qty');   // jumlah produk disewa
            $table->integer('subtotal');

            $table->foreign('produk_idproduk')->references('idproduk')->on('produk');
            $table->foreign('penyewaan_idpenyewaan')->references('idpenyewaan')->on('penyewaan');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_penyewaan');
    }
};
