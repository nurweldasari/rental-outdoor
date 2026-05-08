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
        Schema::create('skala_bagi_hasil', function (Blueprint $table) {
        $table->id();

        $table->unsignedBigInteger('cabang_idcabang');

        $table->decimal('owner',5,2);
        $table->decimal('cabang',5,2);

        $table->date('berlaku_mulai');

        $table->timestamps();

        $table->foreign('cabang_idcabang')
            ->references('idcabang')
            ->on('cabang')
            ->cascadeOnDelete();
    });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skala_bagi_hasil');
    }
};
