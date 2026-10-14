<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $primaryKey = 'id_transaksi';

            protected $fillable = [
        'id_anggota',
        'id_buku',
        'tgl_pinjam',
        'tgl_kembali',
        'status',
        'denda',
        'dende_dibayar',
    ];

    protected function casts(): array
    {
        return [
            'tgl_pinjam' => 'date',
            'tgl_kembali' => 'date',
            'denda' => 'decimal:2',
            'dende_dibayar' => 'decimal:2',
        ];
    }

    /**
     * Status transaksi yang masih meminjam buku.
     */
    public static function statusAktif(): array
    {
        return ['dipinjam'];
    }

    public function isAktif(): bool
    {
        return in_array($this->status, self::statusAktif(), true);
    }

    /**
     * Batas tanggal kembali = tgl_pinjam + lama_peminjaman (hari)
     * dari Settings.
     */
    public function batasKembali(): \Illuminate\Support\Carbon
    {
        $lama = (int) Setting::get('lama_peminjaman', 7);

        return $this->tgl_pinjam->copy()->addDays($lama);
    }

    /**
     * Tanggal "titik akhir" untuk perbandingan keterlambatan:
     *  - sudah dikembalikan â†’ pakai tgl_kembali aktual
     *  - belum          â†’ pakai now()
     */
    public function tanggalAkhirHitung(): \Illuminate\Support\Carbon
    {
        return $this->tgl_kembali
            ? $this->tgl_kembali->copy()
            : \Illuminate\Support\Carbon::now();
    }

    /**
     * Hitung jumlah hari terlambat (selalu >= 0).
     */
    public function hariTerlambat(): int
    {
        $batas = $this->batasKembali();
        $akhir = $this->tanggalAkhirHitung();

        if ($akhir->gt($batas)) {
            return (int) $batas->diffInDays($akhir);
        }

        return 0;
    }

    /**
     * Tarif denda per hari yang berlaku untuk transaksi ini
     * (otomatis mengikuti jenis kartu + Setting tarif_khusus_aktif).
     */
    public function tarifDenda(): int
    {
        return Setting::tarifUntuk($this);
    }

    /**
     * Total denda yang harus dibayar (otomatis).
     * Jika sudah dikembalikan & belum terlambat â†’ 0.
     */
    public function hitungDenda(): int
    {
        $hari = $this->hariTerlambat();

        if ($hari <= 0) {
            return 0;
        }

        return $hari * $this->tarifDenda();
    }

    /**
     * Simpan denda yang dihitung ke kolom `denda`.
     * Dipanggil pada Pengembalian & sinkronisasi otomatis.
     */
    public function syncDenda(): void
    {
        $this->update(['denda' => $this->hitungDenda()]);
    }

    /**
     * Denda yang masih harus dibayar (denda - dende_dibayar).
     */
        public function getDendaTunggakAttribute(): float
    {
        return (float) $this->denda - (float) $this->dende_dibayar;
    }

    /**
     * ERD: transaksi N -- 1 anggota.
     */
    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class, 'id_anggota', 'id_anggota');
    }

    /**
     * ERD: transaksi N -- 1 buku.
     */
    public function buku(): BelongsTo
    {
        return $this->belongsTo(Buku::class, 'id_buku', 'id_buku');
    }
}

