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

        $table->decimal('presentase_owner',5,2);
        $table->decimal('presentase_cabang',5,2);

        $table->decimal('nominal_owner',15,2)->nullable();
        $table->decimal('nominal_cabang',15,2)->nullable();

        $table->string('bukti_fee')->nullable();

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
