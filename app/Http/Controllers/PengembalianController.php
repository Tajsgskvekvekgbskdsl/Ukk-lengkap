<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Pengembalian oleh USER (hanya transaksi miliknya sendiri).
 *
 * Alur: Pengembalian -> pilih transaksi aktif -> KEMBALIKAN
 *       -> status jadi dikembalikan + tgl_kembali terisi
 *       -> stok buku +1. Tidak bisa dikembalikan dua kali.
 */
class PengembalianController extends Controller
{
    /**
     * Daftar transaksi aktif milik user yang sedang login.
     */
    public function index()
    {
        $anggota = $this->authUser()->anggota;

        // Paginator KOSONG (bukan collect()) â€” view memanggil firstItem()/links()
        // yang hanya ada pada Paginator, bukan Collection.
        $pengembalians = Transaksi::whereRaw('1 = 0')->paginate(10);

        if ($anggota) {
            $pengembalians = Transaksi::with('buku')
                ->where('id_anggota', $anggota->id_anggota)
                ->where('status', 'dipinjam')
                ->latest('tgl_pinjam')
                ->paginate(10);
        }

        return view('site.pengembalian', compact('pengembalians'));
    }

    /**
     * Proses pengembalian satu buku.
     */
    public function store(Request $request, Transaksi $transaksi)
    {
        $user = $this->authUser();

        // Hanya pemilik transaksi yang boleh mengembalikan.
        if (! $user->anggota || (int) $transaksi->id_anggota !== (int) $user->anggota->id_anggota) {
            abort(403, 'Anda tidak berhak mengembalikan transaksi ini.');
        }

        if ($transaksi->status !== 'dipinjam') {
            return redirect()->route('user.pengembalian.index')
                ->with('error', 'Buku ini sudah dikembalikan sebelumnya.');
        }

        $sudahKembali = false;

        DB::transaction(function () use ($transaksi, &$sudahKembali) {
            // Kunci baris agar double-submit/race tidak mengembalikan dua kali.
            $t = Transaksi::whereKey($transaksi->id_transaksi)->lockForUpdate()->first();

            if ($t->status !== 'dipinjam') {
                $sudahKembali = true;

                return;
            }

                        $t->update([
                'status' => 'dikembalikan',
                'tgl_kembali' => now()->toDateString(),
                'denda' => $t->hitungDenda(),
            ]);

            // Stok buku +1.
            Buku::whereKey($t->id_buku)->increment('stok', 1);
        });

        if ($sudahKembali) {
            return redirect()->route('user.pengembalian.index')
                ->with('error', 'Buku ini sudah dikembalikan sebelumnya.');
        }

        return redirect()->route('user.pengembalian.index')
            ->with('success', 'Buku berhasil dikembalikan. Terima kasih!');
    }
}

