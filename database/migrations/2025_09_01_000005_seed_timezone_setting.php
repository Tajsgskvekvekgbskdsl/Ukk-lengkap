<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Seed setting timezone ke tabel settings.
 * Key: timezone — nilai default Asia/Jakarta (GMT+7), cocok untuk sistem Indonesia.
 * Dikelompokkan di `operating_hours` agar muncul di kartu Jam Operasional.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => 'timezone'],
            [
                'value'      => 'Asia/Jakarta',
                'type'       => 'string',
                'group'      => 'operating_hours',
                'deskripsi'  => 'Zona waktu perpustakaan (default Asia/Jakarta / GMT+7).',
                'is_config'  => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'timezone')->delete();
    }
};
