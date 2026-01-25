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
        Schema::create('penyewaan', function (Blueprint $table) {
            $table->id('idpenyewaan');
        
            $table->dateTime('tanggal_sewa');
            $table->dateTime('tanggal_selesai');
            $table->dateTime('tanggal_kembali');
            $table->string('total',45);
            $table->string('bukti_bayar',45);
            $table->string('status_penyewaan',45);
            $table->string('total_produk',45);
        
            $table->unsignedBigInteger('penyewa_idpenyewa');
            $table->unsignedBigInteger('cabang_idcabang');
            $table->unsignedBigInteger('admin_pusat_idadmin_pusat');
        
            $table->foreign('penyewa_idpenyewa')->references('idpenyewa')->on('penyewa');
            $table->foreign('cabang_idcabang')->references('idcabang')->on('cabang');
            $table->foreign('admin_pusat_idadmin_pusat')->references('idadmin_pusat')->on('admin_pusat');
        
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penyewaan');
    }
};
