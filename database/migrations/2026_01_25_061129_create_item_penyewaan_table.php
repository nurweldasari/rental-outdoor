<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_penyewaan', function (Blueprint $table) {
            $table->id('iditem_penyewaan');

            // 🔥 produk biasa (nullable karena paket tidak pakai ini)
            $table->unsignedBigInteger('produk_idproduk')->nullable();

            // 🔥 paket (ini yang kamu butuhkan)
            $table->unsignedBigInteger('paket_id')->nullable();

            $table->unsignedBigInteger('penyewaan_idpenyewaan');

            $table->enum('type', ['produk', 'paket'])->default('produk');

            $table->integer('harga');    // harga saat transaksi (paket / produk)
            $table->integer('qty');      // jumlah (produk atau paket)
            $table->integer('subtotal');

            // ================= RELASI =================
            $table->foreign('produk_idproduk')
                ->references('idproduk')
                ->on('produk')
                ->nullOnDelete();

            $table->foreign('paket_id')
    ->references('id')
    ->on('paket')
    ->nullOnDelete();

            $table->foreign('penyewaan_idpenyewaan')
                ->references('idpenyewaan')
                ->on('penyewaan')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_penyewaan');
    }
};