<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permintaan_produk', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->bigIncrements('idpermintaan'); // BIGINT UNSIGNED
            $table->unsignedBigInteger('cabang_idcabang'); // FK cabang
            $table->unsignedBigInteger('produk_idproduk'); // FK produk
            $table->integer('jumlah_diminta'); // jumlah yang diminta cabang
            $table->string('tanggal_permintaan', 45);
           // status enum
    $table->enum('status', ['menunggu', 'disetujui', 'ditolak', 'sampai'])
          ->default('menunggu');

    $table->string('keterangan', 255)->nullable(); // opsional
    $table->timestamps();

            $table->foreign('cabang_idcabang')->references('idcabang')->on('cabang')->onDelete('cascade');
            $table->foreign('produk_idproduk')->references('idproduk')->on('produk')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permintaan_produk');
    }
};
