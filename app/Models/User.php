<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'id_user';

            protected $fillable = [
        'username',
        'nama_lengkap',
        'email',
        'password',
        'role',
        'status',
        'foto',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Cek apakah user ini admin.
     * Role dibaca dari DATABASE (bukan username tertentu).
     * Normalisasi huruf besar/kecil agar 'ADMIN'/'Admin' di DB tetap dena.
     */
    public function isAdmin(): bool
    {
        return strtolower(trim((string) $this->role)) === 'admin';
    }

    /**
     * Relasi ke kartu anggota (1:1).
     * Jika belum ada â†’ otomatis dibuatkan 'normal' pada akses pertama.
     */
    public function kartu(): HasOne
    {
        return $this->hasOne(KartuAnggota::class, 'user_id', 'id_user');
    }

    /**
     * Dapatkan / otomatis-create kartu anggota milik user.
     */
    public function getKartu(): KartuAnggota
    {
        return $this->kartu()->firstOrCreate(
            ['user_id' => $this->id_user],
            ['jenis' => 'normal']
        );
    }

    /**
     * URL foto profil (default jika belum upload).
     */
        public function getUrlFotoAttribute(): string
    {
        if ($this->foto && Storage::disk('public')->exists($this->foto)) {
            return Storage::url($this->foto);
        }

        return asset('assets/img/default-avatar.png');
    }

    /**
     * ERD: users 1 -- 1 anggota.
     */
    public function anggota(): HasOne
    {
        return $this->hasOne(Anggota::class, 'id_user', 'id_user');
    }

        /**
     * Transaksi milik user ini (melalui anggotanya).
     */
    public function transaksis(): HasManyThrough
    {
        return $this->hasManyThrough(
            Transaksi::class,
            Anggota::class,
            'id_user',
            'id_anggota',
            'id_user',
            'id_anggota'
        );
    }

    /**
     * Total denda belum dibayar (transaksi masih dipinjam).
     */
    public function getDendaTunggakUserAttribute(): float
    {
        return (float) $this->transaksis()->where('status', 'dipinjam')->sum('denda');
    }
}

