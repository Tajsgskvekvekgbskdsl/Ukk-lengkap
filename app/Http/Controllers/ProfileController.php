<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * PROFIL - dipakai ADMIN dan USER.
 */
class ProfileController extends Controller
{
        public function show()
    {
        $user = $this->authUser()->loadMissing('anggota', 'kartu');

        return view('profile.index', compact('user'));
    }

    public function edit()
    {
        $user = $this->authUser();

        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = $this->authUser();

                $data = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nis' => 'nullable|string|max:30',
            'kelas' => 'nullable|string|max:30',
            'alamat' => 'nullable|string|max:500',
            'foto' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $user->update([
            'nama_lengkap' => $data['nama_lengkap'],
        ]);

        // Upload / ganti foto profil ke disk publik (storage/app/public).
        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }

                        $ext      = $request->file('foto')->getClientOriginalExtension();
            // Argumen ke-1 now() adalah TIMEZONE, bukan label. Nilai 'id' bukan
            // timezone valid -> Carbon melempar InvalidTimeZoneException.
            // Timestamp Unix tidak butuh timezone, jadi pakai now()->timestamp.
            $namaFile = $user->id_user . '_' . now()->timestamp . '_' . Str::random(6) . '.' . $ext;

            $request->file('foto')->storeAs('foto-profiles', $namaFile, 'public');

            $user->update(['foto' => 'foto-profiles/' . $namaFile]);
        }

        if ($user->anggota) {
            $user->anggota->update([
                'nama' => $data['nama_lengkap'],
                'nis' => $data['nis'] ?? null,
                'kelas' => $data['kelas'] ?? null,
                'alamat' => $data['alamat'] ?? null,
            ]);
        }

        return redirect()->route('user.profile.index')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    public function showChangePassword()
    {
        return view('profile.change-password');
    }

    public function changePassword(Request $request)
    {
        $user = $this->authUser();

        $data = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Password di database mungkin masih plaintext (dibuat via phpMyAdmin/SQL).
        // BcryptHasher::check() melempar RuntimeException untuk nilai non-Bcrypt,
        // sehingga harus ditangani agar tidak 500.
        $stored = (string) $user->password;

        if (str_starts_with($stored, '$2y$')) {
            $passwordBenar = Hash::check($data['current_password'], $stored);
        } else {
            // Format lama (plaintext): bandingkan langsung, hash_equals anti timing-attack.
            $passwordBenar = hash_equals($stored, $data['current_password']);
        }

        if (! $passwordBenar) {
            return back()->withErrors([
                'current_password' => 'Password saat ini salah.',
            ]);
        }

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('user.profile.index')
            ->with('success', 'Password berhasil diubah.');
    }
}
