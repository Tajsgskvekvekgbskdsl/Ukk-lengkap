<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Kartu anggota (Pelajar / Mahasiswa) — opsional, satu per user.
 * Jika tidak ada: jenis = 'normal' (denda normal).
 * Berelasi 1:1 ke users.id_user.
 */
class KartuAnggota extends Model
{
    protected $table = 'kartu_anggotas';

    protected $fillable = [
        'user_id',
        'jenis',
        'file',
    ];

    /**
     * Relasi ke user. FK = user_id → users.id_user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    /** Apakah kartu ini memberikan tarif khusus (bukan normal). */
    public function isKhusus(): bool
    {
        return in_array($this->jenis, ['pelajar', 'mahasiswa'], true);
    }

    /** Label yang rapi untuk ditampilkan. */
    public function getJenisLabelAttribute(): string
    {
        return match ($this->jenis) {
            'pelajar' => 'Pelajar',
            'mahasiswa' => 'Mahasiswa',
            default => 'Normal',
        };
    }

    /** URL file kartu (atau null bila belum upload). */
    public function getFileUrlAttribute(): ?string
    {
        if (empty($this->file)) {
            return null;
        }

        $file = trim($this->file);

        if (Str::startsWith($file, ['http://', 'https://'])) {
            return $file;
        }

        return asset('storage/' . ltrim($file, '/'));
    }
}
