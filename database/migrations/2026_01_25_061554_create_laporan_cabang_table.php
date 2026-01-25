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
        Schema::create('laporan_cabang', function (Blueprint $table) {
            $table->id('idlaporan_cabang');
            $table->string('total_pendapatan',45);
            $table->string('periode_bulan',45);
        
            $table->unsignedBigInteger('cabang_idcabang');
            $table->unsignedBigInteger('bagi_hasil_idbagi_hasil');
        
            $table->foreign('cabang_idcabang')->references('idcabang')->on('cabang');
            $table->foreign('bagi_hasil_idbagi_hasil')->references('idbagi_hasil')->on('bagi_hasil');
        
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_cabang');
    }
};
