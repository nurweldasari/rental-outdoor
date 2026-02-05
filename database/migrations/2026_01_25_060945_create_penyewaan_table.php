<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penyewaan', function (Blueprint $table) {
            $table->id('idpenyewaan');

            // tanggal sewa & selesai
            $table->dateTime('tanggal_sewa');
            $table->dateTime('tanggal_selesai');
            $table->dateTime('tanggal_kembali')->nullable(); // akan diisi saat dikembalikan

            // total & produk
            $table->integer('total'); // total harga
            $table->integer('total_produk'); // jumlah produk

            // pembayaran
            $table->enum('status_penyewaan', ['menunggu_pembayaran', 'sedang_disewa', 'selesai', 'dibatalkan'])
                  ->default('menunggu_pembayaran');
            $table->enum('metode_bayar', ['cash', 'transfer'])->nullable();
            $table->dateTime('batas_pembayaran')->nullable(); // untuk 2 jam limit
            $table->string('bukti_bayar', 100)->nullable();

            // relasi
            $table->unsignedBigInteger('penyewa_idpenyewa');
            $table->unsignedBigInteger('cabang_idcabang');
            $table->unsignedBigInteger('admin_pusat_idadmin_pusat');

            $table->foreign('penyewa_idpenyewa')->references('idpenyewa')->on('penyewa');
            $table->foreign('cabang_idcabang')->references('idcabang')->on('cabang');
            $table->foreign('admin_pusat_idadmin_pusat')->references('idadmin_pusat')->on('admin_pusat');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penyewaan');
    }
};
