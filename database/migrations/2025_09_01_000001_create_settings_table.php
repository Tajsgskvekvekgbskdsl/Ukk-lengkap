<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Tabel pengaturan GLOBAL perpustakaan (key-value).
 * Semua nilai konfigurasi situs disimpan di sini agar bisa
 * diatur lewat admin Settings tanpa sentuh kode.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            // type: string|boolean|integer  — untuk casting nilai
            $table->string('type')->default('string');
            // group: memudahkan pengelompokan di view settings
            $table->string('group')->default('umum');
            $table->text('deskripsi')->nullable();
            $table->boolean('is_config')->default(false);
            $table->timestamps();
        });

        // Seed nilai default agar website langsung pakai tanpa harus input manual.
        DB::table('settings')->insert([
            ['key' => 'nama_perpustakaan', 'value' => 'Perpustakaan', 'type' => 'string', 'group' => 'informasi', 'deskripsi' => 'Nama website / perpustakaan.', 'is_config' => false, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'deskripsi', 'value' => 'Website perpustakaan untuk mencari koleksi buku, meminjam, mengembalikan, dan melihat riwayat transaksi.', 'type' => 'string', 'group' => 'informasi', 'deskripsi' => 'Deskripsi singkat tampil di beranda dan About.', 'is_config' => false, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'telepon', 'value' => null, 'type' => 'string', 'group' => 'informasi', 'deskripsi' => 'Nomor telepon kontak perpustakaan.', 'is_config' => false, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'email', 'value' => null, 'type' => 'string', 'group' => 'informasi', 'deskripsi' => 'Email resmi perpustakaan.', 'is_config' => false, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'alamat', 'value' => null, 'type' => 'text', 'group' => 'informasi', 'deskripsi' => 'Alamat lokasi perpustakaan.', 'is_config' => false, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'lama_peminjaman', 'value' => '7', 'type' => 'integer', 'group' => 'peminjaman', 'deskripsi' => 'Berapa hari maksimal peminjaman satu buku.', 'is_config' => false, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'denda_normal', 'value' => '1000', 'type' => 'integer', 'group' => 'denda', 'deskripsi' => 'Tarif denda per hari untuk pengguna tanpa kartu.', 'is_config' => false, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'denda_pelajar', 'value' => '500', 'type' => 'integer', 'group' => 'denda', 'deskripsi' => 'Tarif denda per hari untuk kartu pelajar.', 'is_config' => false, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'denda_mahasiswa', 'value' => '2000', 'type' => 'integer', 'group' => 'denda', 'deskripsi' => 'Tarif denda per hari untuk kartu mahasiswa.', 'is_config' => false, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'tarif_khusus_aktif', 'value' => '1', 'type' => 'boolean', 'group' => 'denda', 'deskripsi' => 'Jika ON, peminjam dengan kartu dapat tarif denda khusus.', 'is_config' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};

