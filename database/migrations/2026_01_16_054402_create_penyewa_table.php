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
        Schema::create('penyewa', function (Blueprint $table) {
    $table->id('idpenyewa');

    $table->string('gambar_identitas', 45)->nullable();

    $table->unsignedBigInteger('users_idusers');

    $table->foreign('users_idusers')
          ->references('idusers')
          ->on('users')
          ->onDelete('cascade');

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penyewa');
    }
};
