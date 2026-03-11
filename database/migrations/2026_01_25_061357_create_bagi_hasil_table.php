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
        Schema::create('bagi_hasil', function (Blueprint $table) {
            $table->id('idbagi_hasil');

            // WAJIB supaya tahu ini milik cabang mana
            $table->unsignedBigInteger('cabang_idcabang');

            // WAJIB supaya tahu periode bulan
            $table->string('bulan');

            // Presentase
            $table->decimal('presentase_owner',5,2);
            $table->decimal('presentase_cabang',5,2);

            // Nominal hasil hitung
            $table->decimal('nominal_owner',15,2)->nullable();
            $table->decimal('nominal_cabang',15,2)->nullable();

            // Bukti transfer cabang ke owner
            $table->string('bukti_fee')->nullable();

            // Status proses
            $table->enum('status', ['draft','menunggu','terkonfirmasi', 'ditolak'])
                ->default('draft');

            $table->timestamps();

            // Foreign key ke cabang
            $table->foreign('cabang_idcabang')
                ->references('idcabang')
                ->on('cabang')
                ->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bagi_hasil');
    }
};
