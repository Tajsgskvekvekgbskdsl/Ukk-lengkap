<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\KartuAnggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * USER - kelola kartu anggota (Pelajar/Mahasiswa) + foto kartu.
 *
 * Satu user hanya boleh memiliki SATU jenis kartu (atau normal/tidak ada).
 * Tarif denda otomatis dihitung dari jenis kartu ini.
 */
class KartuAnggotaController extends Controller
{
    /** Form pilih jenis kartu + upload foto kartu. */
    public function edit()
    {
        $user   = $this->authUser();
        $kartu  = $user->kartu;          // relasi HasOne (bisa null)

        return view('kartu.edit', compact('kartu'));
    }

    /** Simpan jenis kartu + (opsional) foto kartu. */
    public function update(Request $request)
    {
        $user = $this->authUser();

        $data = $request->validate([
            'jenis' => ['required', Rule::in(['normal', 'pelajar', 'mahasiswa'])],
            'file'  => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
        ], [
            'jenis.required' => 'Pilih jenis kartu terlebih dahulu.',
            'file.image'     => 'File yang diunggah harus berupa gambar (jpg, png).',
            'file.max'       => 'Ukuran foto kartu maksimal 2 MB.',
        ]);

        $update = ['jenis' => $data['jenis']];

        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            // Hapus foto kartu lama bila ada.
                        $kartuLama = $user->kartu;
            if ($kartuLama && $kartuLama->file && Storage::disk('public')->exists($kartuLama->file)) {
                Storage::disk('public')->delete($kartuLama->file);
            }

            $ext       = $request->file('file')->getClientOriginalExtension();
            $namaFile  = 'kartu-' . $user->id_user . '-' . now('id')->format('Ymdhis') . '.' . $ext;
            $path      = $request->file('file')->storeAs('kartu-anggotas', $namaFile, 'public');

            $update['file'] = 'kartu-anggotas/' . $namaFile;
        }

        // normal => kosongkan file agar kembali tanpa kartu (tarif normal).
        if ($data['jenis'] === 'normal' && ! isset($update['file'])) {
            $update['file'] = null;
        }

        $user->kartu()->updateOrCreate(
            ['user_id' => $user->id_user],
            $update
        );

        // Clear cache settings supaya tarif denda ikupdating.
        \Illuminate\Support\Facades\Cache::flush();

        return redirect()->route('user.kartu.edit')
            ->with('success', 'Kartu anggota berhasil disimpan.');
    }

    /** Hapus foto kartu (jenis tetap, hanya foto yang dihapus). */
    public function destroy(Request $request)
    {
        $user  = $this->authUser();
        $kartu = $user->kartu;

                if ($kartu && $kartu->file && Storage::disk('public')->exists($kartu->file)) {
            Storage::disk('public')->delete($kartu->file);
        }

        if ($kartu) {
            $kartu->update(['file' => null]);
        }

        return back()->with('success', 'Foto kartu berhasil dihapus.');
    }
}
