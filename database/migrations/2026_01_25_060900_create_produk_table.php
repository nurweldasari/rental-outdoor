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
        Schema::create('produk', function (Blueprint $table) {
            $table->id('idproduk');
            $table->string('nama_produk',45);
            $table->integer('stok');
            $table->string('gambar_produk',45);
        
            $table->unsignedBigInteger('kategori_idkategori');
            $table->unsignedBigInteger('cabang_idcabang');
            $table->unsignedBigInteger('harga_idharga');
            $table->unsignedBigInteger('admin_pusat_idadmin_pusat');
        
            $table->foreign('kategori_idkategori')->references('idkategori')->on('kategori');
            $table->foreign('cabang_idcabang')->references('idcabang')->on('cabang');
            $table->foreign('harga_idharga')->references('idharga')->on('harga');
            $table->foreign('admin_pusat_idadmin_pusat')->references('idadmin_pusat')->on('admin_pusat');
        
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
