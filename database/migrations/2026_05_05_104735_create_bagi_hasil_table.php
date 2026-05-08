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

        $table->unsignedBigInteger('cabang_idcabang');

        $table->unsignedBigInteger('skala_id')->nullable();

        $table->string('bulan');

        $table->decimal('presentase_owner',5,2);
        $table->decimal('presentase_cabang',5,2);

        $table->integer('nominal_owner')->nullable();
        $table->integer('nominal_cabang')->nullable();

        $table->string('bukti_fee')->nullable();

        $table->enum('status', ['terkunci','menunggu','terkonfirmasi','ditolak'])
            ->default('terkunci');

        $table->timestamps();

        $table->foreign('cabang_idcabang')
            ->references('idcabang')
            ->on('cabang')
            ->cascadeOnDelete();

        $table->foreign('skala_id')
            ->references('id')
            ->on('skala_bagi_hasil')
            ->nullOnDelete();
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
