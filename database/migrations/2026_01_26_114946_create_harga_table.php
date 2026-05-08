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
        Schema::create('harga', function (Blueprint $table) {
        $table->id('idharga');

        $table->enum('type', ['produk', 'paket']);

        $table->unsignedBigInteger('produk_id')->nullable()->index();
        $table->unsignedBigInteger('paket_id')->nullable()->index();

        $table->integer('harga');
        $table->date('tanggal_berlaku');

        $table->timestamps();

        $table->foreign('produk_id')
            ->references('idproduk')
            ->on('produk')
            ->nullOnDelete();

        $table->foreign('paket_id')
            ->references('id')
            ->on('paket')
            ->nullOnDelete();
    });      
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harga');
    }
};
