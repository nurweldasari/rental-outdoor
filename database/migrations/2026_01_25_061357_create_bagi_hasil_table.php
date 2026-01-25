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
            $table->string('presentase_owner',45);
            $table->string('presentase_cabang',45);
            $table->string('bukti_fee',45);
            $table->timestamps();
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
