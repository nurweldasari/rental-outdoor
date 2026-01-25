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
        Schema::create('kontrak_franchise', function (Blueprint $table) {
            $table->id('idkontrak_franchise');
            $table->string('gambar_mou',45);
        
            $table->unsignedBigInteger('owner_idowner');
            $table->foreign('owner_idowner')->references('idowner')->on('owner');
        
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kontrak');
    }
};
