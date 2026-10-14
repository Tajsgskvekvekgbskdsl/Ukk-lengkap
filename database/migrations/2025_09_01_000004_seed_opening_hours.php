<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Seed konfigurasi JAM OPERASIONAL ke tabel settings yang sudah ada.
 * Key konsisten: opening_<day>, closing_<day>, closed_<day> (group=operating_hours).
 * Default mencerminkan jadwal tipe perpustakaan: Senin-Sabtu pagi, Minggu Tutup.
 */
return new class extends Migration {
    public function up(): void
    {
        $now = now();
        $rows = [
            ['opening_monday',    '07:00', 'string', 'operating_hours', 'Jam buka Senin.'],
            ['closing_monday',    '15:30', 'string', 'operating_hours', 'Jam tutup Senin.'],
            ['closed_monday',     '0',     'boolean', 'operating_hours', 'Centang bila Senin tutup.'],
            ['opening_tuesday',   '07:00', 'string', 'operating_hours', 'Jam buka Selasa.'],
            ['closing_tuesday',   '15:30', 'string', 'operating_hours', 'Jam tutup Selasa.'],
            ['closed_tuesday',    '0',     'boolean', 'operating_hours', 'Centang bila Selasa tutup.'],
            ['opening_wednesday', '07:00', 'string', 'operating_hours', 'Jam buka Rabu.'],
            ['closing_wednesday', '15:30', 'string', 'operating_hours', 'Jam tutup Rabu.'],
            ['closed_wednesday',  '0',     'boolean', 'operating_hours', 'Centang bila Rabu tutup.'],
            ['opening_thursday',  '07:00', 'string', 'operating_hours', 'Jam buka Kamis.'],
            ['closing_thursday',  '15:30', 'string', 'operating_hours', 'Jam tutup Kamis.'],
            ['closed_thursday',   '0',     'boolean', 'operating_hours', 'Centang bila Kamis tutup.'],
            ['opening_friday',    '07:00', 'string', 'operating_hours', 'Jam buka Jumat.'],
            ['closing_friday',    '11:30', 'string', 'operating_hours', 'Jam tutup Jumat.'],
            ['closed_friday',     '0',     'boolean', 'operating_hours', 'Centang bila Jumat tutup.'],
            ['opening_saturday',  '08:00', 'string', 'operating_hours', 'Jam buka Sabtu.'],
            ['closing_saturday',  '12:00', 'string', 'operating_hours', 'Jam tutup Sabtu.'],
            ['closed_saturday',   '0',     'boolean', 'operating_hours', 'Centang bila Sabtu tutup.'],
            ['opening_sunday',    null,    'string', 'operating_hours', 'Jam buka Minggu.'],
            ['closing_sunday',    null,    'string', 'operating_hours', 'Jam tutup Minggu.'],
            ['closed_sunday',     '1',     'boolean', 'operating_hours', 'Centang bila Minggu tutup (default ON).'],
        ];

        foreach ($rows as [$key, $val, $type, $group, $desc]) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                [
                    'value'      => $val,
                    'type'       => $type,
                    'group'      => $group,
                    'deskripsi'  => $desc,
                    'is_config'  => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }

    public function down(): void
    {
        DB::table('settings')
            ->whereIn('key', [
                'opening_monday','closing_monday','closed_monday',
                'opening_tuesday','closing_tuesday','closed_tuesday',
                'opening_wednesday','closing_wednesday','closed_wednesday',
                'opening_thursday','closing_thursday','closed_thursday',
                'opening_friday','closing_friday','closed_friday',
                'opening_saturday','closing_saturday','closed_saturday',
                'opening_sunday','closing_sunday','closed_sunday',
            ])->delete();
    }
};
