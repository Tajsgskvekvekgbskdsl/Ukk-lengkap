<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Pengaturan GLOBAL perpustakaan (key-value).
 * Nilainya di-cache agar tidak query berulang ke database.
 */
class Setting extends Model
{
    protected $table = 'settings';
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = ['key', 'value', 'type', 'group', 'deskripsi', 'is_config'];

    protected $casts = [
        'is_config' => 'boolean',
    ];

        /** Cache selama 1 jam. */
    protected static int $cacheTtl = 3600;

    /**
     * Nama situs — SUMBER YANG SATU untuk nama website/perpustakaan.
     * Semua view (brand, title, footer) wajib pakai ini, bukan hardcode.
     */
    public static function siteName(): string
    {
        return (string) self::get('nama_perpustakaan', 'Perpustakaan');
    }

    /**
     * Ambil nilai setting. Casting otomatis sesuai type.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $row = self::findCached($key);

        if ($row === null) {
            return $default;
        }

        return self::castValue($row->value, $row->type ?? 'string');
    }

    /**
     * Simpan / perbarui nilai setting + invalidasi cache agar langsung global.
     */
    public static function set(string $key, mixed $value, string $type = 'string', string $group = 'umum', ?string $deskripsi = null): void
    {
        self::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_bool($value) ? (int) $value : (string) $value,
                'type' => $type,
                'group' => $group,
                'deskripsi' => $deskripsi ?? '',
            ]
        );

        self::forget($key);
    }

    /**
     * Invalidasi cache satu key.
     * Dipanggil setelah penulisan langsung ke DB (mis. SettingsController::update)
     * supaya nilai baru langsung terlihat di seluruh situs.
     */
    public static function forget(string $key): void
    {
        Cache::forget(self::cacheKey($key));
    }

    /**
     * Cache-aware lookup.
     *
     * Cache menyimpan ARRAY (value + type), BUKAN objek Eloquent, sehingga
     * unserialize selalu aman dan tidak pernah menghasilkan
     * __PHP_Incomplete_Class bila kelas model berubah. Return type ?static
     * tetap konsisten: mengembalikan instance Setting (hydrated) atau null.
     */
    protected static function findCached(string $key): ?static
    {
        $data = Cache::remember(self::cacheKey($key), self::$cacheTtl, function () use ($key) {
            $row = self::where('key', $key)->first();

            if ($row === null) {
                return null;
            }

            return ['value' => $row->value, 'type' => $row->type ?? 'string'];
        });

        if (! is_array($data)) {
            return null;
        }

        return new self([
            'key'   => $key,
            'value' => $data['value'],
            'type'  => $data['type'],
        ]);
    }

    protected static function cacheKey(string $key): string
    {
        return 'setting:'.$key;
    }

    /**
     * Casting nilai sesuai tipe
     * (dilakukan di aplikasi, bukan DB, supaya fleksibel).
     */
    protected static function castValue(mixed $value, string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match (strtolower($type)) {
            'integer', 'int' => (int) $value,
            'boolean', 'bool' => (bool) $value,
            'text' => (string) $value,
            default => (string) $value,
        };
    }

    /**
     * Semua pengaturan denda dalam bentuk array terkelompok.
     */
    public static function dendaConfig(): array
    {
        return [
            'lama_peminjaman' => (int) self::get('lama_peminjaman', 7),
            'tarif_khusus_aktif' => (bool) self::get('tarif_khusus_aktif', false),
            'denda_normal' => (int) self::get('denda_normal', 1000),
            'denda_pelajar' => (int) self::get('denda_pelajar', 500),
            'denda_mahasiswa' => (int) self::get('denda_mahasiswa', 2000),
        ];
    }

    /**
     * Tarif denda per hari yang berlaku untuk transaksi tertentu,
     * dihitung otomatis dari jenis kartu anggota (atau normal bila tidak ada).
     */
    public static function tarifUntuk(?Transaksi $transaksi = null): int
    {
        $cfg = self::dendaConfig();
        $tarif = $cfg['denda_normal'];

        if ($transaksi !== null && $cfg['tarif_khusus_aktif']) {
            $kartu = optional($transaksi->anggota?->user?->kartu)->jenis;

            $tarif = match ($kartu) {
                'pelajar' => $cfg['denda_pelajar'],
                'mahasiswa' => $cfg['denda_mahasiswa'],
                default => $cfg['denda_normal'],
            };
        }

                return $tarif;
    }

    /**
     * Jadwal buka/tutup mingguan (SUMBER SATU untuk jam operasional).
     * Dipakai oleh footer, Tentang, beranda, dsb.
     *
     * @return array{day:string,open:?string,close:?string,closed:bool}[]  urut Senin -> Minggu
     */
    public static function openingHours(): array
    {
        $days = [
            'monday'    => 'Senin',
            'tuesday'   => 'Selasa',
            'wednesday' => 'Rabu',
            'thursday'  => 'Kamis',
            'friday'    => 'Jumat',
            'saturday'  => 'Sabtu',
            'sunday'    => 'Minggu',
        ];

        $out = [];
        foreach ($days as $day => $label) {
            $closed   = (bool) self::get('closed_'.$day, false);
            $rawOpen  = self::get('opening_'.$day);
            $rawClose = self::get('closing_'.$day);

            $out[] = [
                'day'    => $label,
                'open'   => $closed ? null : self::formatTime($rawOpen),
                'close'  => $closed ? null : self::formatTime($rawClose),
                'closed' => $closed,
            ];
        }
        return $out;
    }

    /**
     * Normalisasi nilai time ("07:00" / "07.00" / "07:00:00") -> "07.00".
     * null bila kosong / tidak valid.
     */
    protected static function formatTime(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (preg_match('/^(\d{1,2})[.:](\d{2})(:\d{2})?$/', trim($value), $m)) {
            return sprintf('%02d.%s', (int) $m[1], $m[2]);
        }
        return null;
    }
}
