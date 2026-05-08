<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel permintaan (header)
        Schema::create('permintaan', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->bigIncrements('idpermintaan');
            $table->unsignedBigInteger('cabang_idcabang'); // FK cabang
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak', 'sampai'])
                  ->default('menunggu');
            $table->string('tanggal_permintaan', 45);
            $table->string('keterangan', 255)->nullable(); // opsional
            $table->timestamps();

            $table->foreign('cabang_idcabang')
                  ->references('idcabang')->on('cabang')
                  ->onDelete('cascade');
        });

        // Tabel permintaan_produk (detail produk per permintaan)
        Schema::create('permintaan_produk', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->bigIncrements('id'); // PK
            $table->unsignedBigInteger('permintaan_id'); // FK ke permintaan
            $table->unsignedBigInteger('produk_idproduk'); // FK ke produk
            $table->integer('jumlah_diminta'); // jumlah yang diminta cabang
            $table->timestamps();

            $table->foreign('permintaan_id')
                  ->references('idpermintaan')->on('permintaan')
                  ->onDelete('cascade');
            $table->foreign('produk_idproduk')
                  ->references('idproduk')->on('produk')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permintaan_produk');
        Schema::dropIfExists('permintaan');
    }
};
