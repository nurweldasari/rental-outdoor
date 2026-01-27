<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distribusi_produk', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->bigIncrements('iddistribusi'); // PK
            $table->unsignedBigInteger('permintaan_produk_id'); // FK ke permintaan_produk

            $table->string('tanggal_distribusi', 45);
            $table->integer('jumlah_dikirim'); // jumlah yang dikirim pusat
            $table->string('keterangan', 100)->nullable();
            $table->enum('status_distribusi', ['dikirim', 'diterima'])->default('dikirim');

            $table->timestamps();

            $table->foreign('permintaan_produk_id')
                  ->references('id') // id di tabel permintaan_produk
                  ->on('permintaan_produk')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribusi_produk');
    }
};
