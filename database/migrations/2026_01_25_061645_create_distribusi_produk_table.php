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
        Schema::create('distribusi_produk', function (Blueprint $table) {
            $table->id('iddistribusi_alat');
        
            $table->string('tanggal_permintaan',45);
            $table->string('tanggal_distribusi',45);
            $table->string('keterangan_produk',45);
            $table->string('status_distribusi',45);
        
            $table->unsignedBigInteger('cabang_idcabang');
            $table->unsignedBigInteger('produk_idproduk');
        
            $table->foreign('cabang_idcabang')->references('idcabang')->on('cabang');
            $table->foreign('produk_idproduk')->references('idproduk')->on('produk');
        
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distribusi_produk');
    }
};
