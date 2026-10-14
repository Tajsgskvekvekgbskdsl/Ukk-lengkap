<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * ADMIN - CRUD TRANSAKSI.
 * Setiap transaksi wajib terhubung ke anggota dan buku (FK ERD):
 * transaksi.id_anggota -> anggota.id_anggota
 * transaksi.id_buku    -> buku.id_buku
 *
 * Satu transaksi = 1 unit buku. Stok berkurang saat dipinjam,
 * pulih saat dikembalikan / transaksi dihapus.
 */
class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $status = trim((string) $request->input('status'));

        $transaksis = Transaksi::query()
            ->with(['anggota.user', 'buku'])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($w) use ($search) {
                    $w->whereHas('anggota', function ($a) use ($search) {
                        $a->where('nama', 'like', "%{$search}%")
                            ->orWhere('nis', 'like', "%{$search}%");
                    })->orWhereHas('buku', function ($b) use ($search) {
                        $b->where('judul_buku', 'like', "%{$search}%");
                    })->orWhereHas('anggota.user', function ($u) use ($search) {
                        $u->where('username', 'like', "%{$search}%");
                    });
                });
            })
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->latest('tgl_pinjam')
            ->paginate(10)
            ->withQueryString();

        return view('transaksis.index', compact('transaksis', 'search', 'status'));
    }

    public function create()
    {
        $anggotas = Anggota::orderBy('nama')->get();
        $bukus = Buku::where('stok', '>', 0)->orderBy('judul_buku')->get();

        return view('transaksis.create', compact('anggotas', 'bukus'));
    }

    public function store(Request $request)
    {
        $data = $this->validateTransaksi($request);

        DB::transaction(function () use ($data) {
            $buku = Buku::whereKey($data['id_buku'])->lockForUpdate()->firstOrFail();

            if ($buku->stok < 1) {
                throw ValidationException::withMessages([
                    'id_buku' => "Stok buku habis (sisa: {$buku->stok}).",
                ]);
            }

            // Cegah satu anggota meminjam judul yang sama dua kali bersamaan.
            $sudahPinjam = Transaksi::where('id_anggota', $data['id_anggota'])
                ->where('id_buku', $data['id_buku'])
                ->where('status', 'dipinjam')
                ->lockForUpdate()
                ->exists();

            if ($sudahPinjam) {
                throw ValidationException::withMessages([
                    'id_buku' => 'Anggota ini sedang meminjam buku yang sama.',
                ]);
            }

            Transaksi::create(array_merge($data, [
                'tgl_kembali' => null,
                'status' => 'dipinjam',
            ]));

            $berkurang = Buku::whereKey($buku->id_buku)
                ->where('stok', '>=', 1)
                ->decrement('stok', 1);

            if ($berkurang === 0) {
                throw ValidationException::withMessages(['id_buku' => 'Stok buku habis.']);
            }
        });

        return redirect()->route('admin.transaksi.index')
            ->with('success', 'Transaksi peminjaman berhasil dibuat dan stok berkurang.');
    }

    public function show(Transaksi $transaksi)
    {
        $transaksi->load(['anggota.user', 'buku']);

        return view('transaksis.show', compact('transaksi'));
    }

    public function edit(Transaksi $transaksi)
    {
        $anggotas = Anggota::orderBy('nama')->get();
        $bukus = Buku::orderBy('judul_buku')->get();
        $transaksi->load(['anggota', 'buku']);

        return view('transaksis.edit', compact('transaksi', 'anggotas', 'bukus'));
    }

    /**
     * Update dengan penyesuaian stok berbasis status:
     * - aktif (dipinjam) : stok sedang dikurangi 1
     * - dikembalikan     : stok sudah dipulihkan
     * Hanya SELISIH kondisi sebelum/sesudah yang dieksekusi,
     * sehingga stok tidak pernah ganda maupun minus.
     */
    public function update(Request $request, Transaksi $transaksi)
    {
        $data = $this->validateTransaksi($request, withStatus: true);

        DB::transaction(function () use ($data, $transaksi) {
            $oldBuku = Buku::whereKey($transaksi->id_buku)->lockForUpdate()->firstOrFail();

            $newBuku = ((int) $data['id_buku'] === (int) $oldBuku->id_buku)
                ? $oldBuku
                : Buku::whereKey($data['id_buku'])->lockForUpdate()->firstOrFail();

            $wasActive = $transaksi->status === 'dipinjam';
            $willActive = $data['status'] === 'dipinjam';
            $gantiBuku = ! $newBuku->is($oldBuku);

            if ($gantiBuku) {
                // Cek stok buku baru SEBELUM menyentuh stok lama.
                if ($willActive && $newBuku->stok < 1) {
                    throw ValidationException::withMessages([
                        'id_buku' => "Stok buku baru habis (sisa: {$newBuku->stok}).",
                    ]);
                }

                if ($wasActive) {
                    $oldBuku->increment('stok', 1);
                }
                if ($willActive) {
                    $newBuku->decrement('stok', 1);
                }
            } else {
                if ($wasActive && ! $willActive) {
                    // Dikembalikan lewat edit -> stok +1.
                    $oldBuku->increment('stok', 1);
                } elseif (! $wasActive && $willActive) {
                    // Dibuat dipinjam lagi -> cek & kurangi stok.
                    if ($newBuku->stok < 1) {
                        throw ValidationException::withMessages([
                            'id_buku' => "Stok buku habis (sisa: {$newBuku->stok}).",
                        ]);
                    }
                    $newBuku->decrement('stok', 1);
                }
                // was && will -> tidak ada perubahan stok.
            }

            // tgl_kembali hanya relevan ketika sudah dikembalikan.
            $tglKembali = $data['tgl_kembali'] ?? null;
            if ($willActive) {
                $tglKembali = null;
            } elseif (empty($tglKembali)) {
                $tglKembali = now()->toDateString();
            }

                        $transaksi->update([
                'id_anggota' => $data['id_anggota'],
                'id_buku' => $newBuku->id_buku,
                'tgl_pinjam' => $data['tgl_pinjam'],
                'tgl_kembali' => $tglKembali,
                'status' => $data['status'],
            ]);

            // Sinkronkan denda otomatis bila di-status-kan dikembalikan lewat edit admin.
            if ($data['status'] === 'dikembalikan') {
                $transaksi->syncDenda();
            }
        });

        return redirect()->route('admin.transaksi.index')
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Transaksi $transaksi)
    {
        DB::transaction(function () use ($transaksi) {
            // Pulihkan stok bila buku masih dipinjam.
            if ($transaksi->status === 'dipinjam') {
                Buku::whereKey($transaksi->id_buku)->increment('stok', 1);
            }

            $transaksi->delete();
        });

        return redirect()->route('admin.transaksi.index')
            ->with('success', 'Transaksi berhasil dihapus.');
    }

    private function validateTransaksi(Request $request, bool $withStatus = false): array
    {
        $rules = [
            'id_anggota' => ['required', 'integer', 'exists:anggota,id_anggota'],
            'id_buku' => ['required', 'integer', 'exists:buku,id_buku'],
            'tgl_pinjam' => ['required', 'date'],
        ];

        if ($withStatus) {
            $rules['tgl_kembali'] = ['nullable', 'date'];
            $rules['status'] = ['required', 'in:dipinjam,dikembalikan'];
        }

        return $request->validate($rules);
    }
}

