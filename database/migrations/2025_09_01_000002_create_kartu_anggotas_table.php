<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kartu anggota (Pelajar / Mahasiswa) — opsional, satu per user.
 * Berelasi 1:1 dengan users.id_user.
 * Dipakai untuk menentukan tarif denda yang berlaku.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kartu_anggotas', function (Blueprint $table) {
            $table->id();
            // unique agar satu user hanya punya satu kartu
            $table->foreignId('user_id')
                ->constrained('users', 'id_user')
                ->onDelete('cascade')
                ->unique();
            // jenis: NORMAL (tidak punya kartu) / PELAJAR / MAHASISWA
            $table->string('jenis')->default('normal');
            // path file kartu (gambar) di storage/app/public/kartu
            $table->string('file')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kartu_anggotas');
    }
};
