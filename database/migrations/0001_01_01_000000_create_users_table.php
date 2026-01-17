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
        // TABEL USERS
        Schema::create('users', function (Blueprint $table) {
            $table->id('idusers');
            $table->string('nama', 45);
            $table->string('username', 45)->unique();
            $table->string('password', 255);
            $table->string('no_telepon', 45);
            $table->string('alamat', 45);
            $table->string('status', 45);
            $table->timestamps();
        });

        // TABEL SESSIONS
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
    }
};
