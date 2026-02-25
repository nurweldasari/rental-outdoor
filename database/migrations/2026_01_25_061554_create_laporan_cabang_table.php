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

            $table->decimal('total_pendapatan',15,2); // wajib decimal
            $table->string('periode_bulan',7); // format: 2025-06

            $table->unsignedBigInteger('cabang_idcabang');
            $table->unsignedBigInteger('bagi_hasil_idbagi_hasil')->nullable();

            $table->foreign('cabang_idcabang')
                ->references('idcabang')
                ->on('cabang')
                ->onDelete('cascade');

            $table->foreign('bagi_hasil_idbagi_hasil')
                ->references('idbagi_hasil')
                ->on('bagi_hasil')
                ->nullOnDelete();

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
