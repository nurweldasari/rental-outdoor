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
        Schema::create('admin_cabang', function (Blueprint $table) {
    $table->id('idadmin_cabang');
    $table->string('gambar_mou',45)->nullable();

    $table->foreignId('users_idusers')
          ->references('idusers')->on('users')
          ->onDelete('cascade');

    $table->foreignId('cabang_idcabang')
          ->references('idcabang')->on('cabang')
          ->onDelete('cascade');

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_cabang');
    }
};
