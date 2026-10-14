<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * ADMIN - Pengelolaan pengaturan GLOBAL perpustakaan (key-value).
 */
class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::orderByRaw("FIELD(`group`,'informasi','peminjaman','denda','operating_hours')")
            ->orderBy('key')
            ->get()
            ->groupBy('group');

        return view('settings.index', compact('settings'));
    }

    /** Bulk-update seluruh value settings dari form. */
    public function update(Request $request)
    {
        $payload = $request->validate([
            'settings'   => ['required', 'array'],
            'settings.*' => ['nullable', 'string'],
        ]);

        // Validasi jam operasional:
        //  - opening_/closing_* : HH:MM (atau kosong bila ditandai Tutup)
        //  - closing_* tidak boleh lebih awal dari opening_*
        foreach (['monday','tuesday','wednesday','thursday','friday','saturday','sunday'] as $day) {
            $open    = $payload['settings']['opening_'.$day] ?? null;
            $close   = $payload['settings']['closing_'.$day] ?? null;
            $closed  = (bool) ($payload['settings']['closed_'.$day] ?? false);

            foreach ([$open, $close] as $v) {
                if ($v !== null && $v !== '' && !preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $v)) {
                    throw ValidationException::withMessages([
                        'settings.opening_'.$day => "Jam harus berupa format HH:MM yang valid.",
                    ]);
                }
            }

            if (!$closed && $open !== '' && $close !== '' && strtotime($close) < strtotime($open)) {
                throw ValidationException::withMessages([
                    'settings.closing_'.$day => "Jam tutup tidak boleh lebih awal dari jam buka.",
                ]);
            }
        }

        $allowed = Setting::query()->pluck('key')->all();

        DB::transaction(function () use ($payload, $allowed) {
            foreach ($payload['settings'] as $key => $value) {
                if (! in_array($key, $allowed, true)) {
                    continue;
                }

                if (str_starts_with($key, 'closed_')) {
                    $value = $value ? '1' : '0';
                } elseif (str_starts_with($key, 'opening_') || str_starts_with($key, 'closing_')) {
                    $value = $value === '' || $value === null ? null : trim($value);
                }

                Setting::where('key', $key)->update([
                    'value'      => $value ?? '',
                    'updated_at' => now(),
                ]);

                // Cache invalidation -> nilai baru langsung terlihat di seluruh situs.
                Setting::forget($key);
            }
        });

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}