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
        Schema::create('rekening', function (Blueprint $table) {
            $table->id('idrekening');
        
            $table->string('nama_bank', 45);
            $table->string('no_rekening', 45);
            $table->string('atas_nama', 45);
        
            $table->foreignId('cabang_idcabang')
                  ->constrained('cabang', 'idcabang')
                  ->cascadeOnDelete();
        
            $table->timestamps();
        });
        
        
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekening');
    }
};
