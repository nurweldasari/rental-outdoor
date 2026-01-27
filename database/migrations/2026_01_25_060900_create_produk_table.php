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
    $table->integer('stok_pusat');   
    $table->integer('harga');
    $table->string('jenis_skala',45);
    $table->string('gambar_produk',100)->nullable();

    // ✅ TAMBAH KATEGORI
    $table->unsignedBigInteger('kategori_idkategori');

    $table->unsignedBigInteger('admin_pusat_idadmin_pusat');
    $table->timestamps();

    // foreign key
    $table->foreign('kategori_idkategori')
          ->references('idkategori')
          ->on('kategori');
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
