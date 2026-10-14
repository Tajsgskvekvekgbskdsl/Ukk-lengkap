@extends('layouts.app')

@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan')

@section('content')
<p class="text-muted mb-4 small">
    Kelola pengaturan global perpustakaan. Klik <strong>Simpan</strong>
    pada tiap kartu untuk menerapkan perubahan.
</p>

<form method="POST" action="{{ route('admin.settings.update') }}" id="settingsForm">
    @csrf
    @method('PUT')

    @foreach ($settings as $group => $items)
        @php
            $groupMeta = [
                'umum'            => ['icon' => 'bi bi-gear-fill',         'title' => 'Pengaturan Umum',        'desc' => 'Nama aplikasi dan pengaturan dasar perpustakaan.'],
                'informasi'       => ['icon' => 'bi bi-info-circle-fill',   'title' => 'Informasi Perpustakaan',  'desc' => 'Identitas, alamat, dan kontak perpustakaan.'],
                'peminjaman'      => ['icon' => 'bi bi-book-fill',          'title' => 'Aturan Peminjaman',       'desc' => 'Durasi maksimal peminjaman buku.'],
                'denda'           => ['icon' => 'bi bi-currency-exchange',  'title' => 'Aturan Denda',            'desc' => 'Tarif denda keterlambatan per jenis kartu.'],
                'operating_hours' => ['icon' => 'bi bi-clock-fill',         'title' => 'Jam Operasional',         'desc' => 'Jadwal buka dan tutup perpustakaan tiap hari.'],
            ][$group] ?? ['icon' => 'bi bi-gear', 'title' => ucfirst($group), 'desc' => ''];
        @endphp

                @if ($group === 'operating_hours')
            {{-- ================================================== --}}
            {{--  CARD KHUSUS: Jam Operasional (layout terpisah)     --}}
            {{-- ================================================== --}}
            @php
                $opItems = $items->keyBy('key');

                $days = [
                    'monday'    => 'Senin',
                    'tuesday'   => 'Selasa',
                    'wednesday' => 'Rabu',
                    'thursday'  => 'Kamis',
                    'friday'    => 'Jumat',
                    'saturday'  => 'Sabtu',
                    'sunday'    => 'Minggu',
                ];

                $timezoneOptions = [
                    'Asia/Jakarta'   => '(GMT+07:00) Asia/Jakarta',
                    'Asia/Pontianak' => '(GMT+07:00) Asia/Pontianak',
                    'Asia/Makassar'  => '(GMT+08:00) Asia/Makassar',
                    'Asia/Jayapura'  => '(GMT+09:00) Asia/Jayapura',
                ];

                $timezoneValue = old('settings.timezone', $opItems['timezone']?->value ?? 'Asia/Jakarta');
            @endphp

            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="{{ $groupMeta['icon'] }} fs-5"></i>
                    <div>
                        <span class="fw-semibold">{{ $groupMeta['title'] }}</span>
                        @if ($groupMeta['desc'])
                            <div class="text-muted small mt-1">{{ $groupMeta['desc'] }}</div>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    {{-- ---- Zona Waktu ---- --}}
                    <div class="mb-4">
                        <label for="settingsTimezone" class="form-label fw-medium mb-1">Zona Waktu</label>
                        <select name="settings[timezone]" id="settingsTimezone" class="form-select form-select-sm">
                            @foreach ($timezoneOptions as $tzValue => $tzLabel)
                                <option value="{{ $tzValue }}" {{ $timezoneValue === $tzValue ? 'selected' : '' }}>{{ $tzLabel }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Zona waktu sistem perpustakaan.</small>
                    </div>

                    <hr class="my-3">

                    {{-- ---- Hari Operasional ---- --}}
                    <div class="small fw-medium text-muted mb-2">Hari Operasional</div>

                    @foreach ($days as $day => $label)
                        @php
                            $oldClosed = old('settings.closed_' . $day);
                            $closed   = $oldClosed !== null
                                ? ($oldClosed === '1' || $oldClosed === 1)
                                : (bool) ($opItems['closed_' . $day]?->value ?? 0);
                            $isOpen   = !$closed;

                            $openTime  = old('settings.opening_' . $day, $opItems['opening_' . $day]?->value ?? '');
                            $closeTime = old('settings.closing_' . $day, $opItems['closing_' . $day]?->value ?? '');
                        @endphp

                        <div class="row g-2 align-items-center" data-day="{{ $day }}">
                            {{-- Checkbox visual (checked = buka) + hidden field closed_<day> --}}
                            <div class="col-auto">
                                <input type="checkbox"
                                       class="form-check-input day-toggle"
                                       data-day="{{ $day }}"
                                       {{ $isOpen ? 'checked' : '' }}>
                                <input type="hidden"
                                       name="settings[closed_{{ $day }}]"
                                       value="{{ $closed ? '1' : '0' }}">
                            </div>

                            <div class="col-auto fw-medium">{{ $label }}</div>

                            {{-- Input jam (muncul bila terbuka) --}}
                            <div class="col-12 col-md-auto time-section {{ $isOpen ? '' : 'd-none' }}">
                                <input type="time"
                                       name="settings[opening_{{ $day }}]"
                                       value="{{ $openTime }}"
                                       class="form-control form-control-sm d-inline-block w-auto mb-1 mb-md-0">
                                <span class="text-muted small mx-2">sampai</span>
                                <input type="time"
                                       name="settings[closing_{{ $day }}]"
                                       value="{{ $closeTime }}"
                                       class="form-control form-control-sm d-inline-block w-auto mb-1 mb-md-0">
                            </div>

                            {{-- Status Tutup (muncul bila tutup) --}}
                            <div class="col-auto closed-section {{ $isOpen ? 'd-none' : '' }}">
                                <span class="text-muted small">Tutup</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="card-footer bg-transparent d-flex justify-content-end">
                    <button type="submit" class="btn btn-green btn-sm">
                        <i class="bi bi-save"></i> Simpan Pengaturan
                    </button>
                </div>
            </div>
        @else
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="{{ $groupMeta['icon'] }} fs-5"></i>
                <div>
                    <span class="fw-semibold">{{ $groupMeta['title'] }}</span>
                    @if ($groupMeta['desc'])
                        <div class="text-muted small mt-1">{{ $groupMeta['desc'] }}</div>
                    @endif
                </div>
            </div>

            <div class="card-body row g-3">
                @foreach ($items as $s)
                    @php
                        $isTime   = str_starts_with($s->key, 'opening_')
                                 || str_starts_with($s->key, 'closing_')
                                 || str_starts_with($s->key, 'closed_');
                        $isClosed = str_starts_with($s->key, 'closed_');
                    @endphp

                    @if ($isTime)
                        @php
                            $day = $isClosed
                                ? substr($s->key, 6)                                       // after "closed_"
                                : (str_starts_with($s->key, 'opening_') ? substr($s->key, 8) : substr($s->key, 8)); // after "opening "/"closing_"
                            $labelDay = match ($day) {
                                'monday'    => 'Senin',
                                'tuesday'   => 'Selasa',
                                'wednesday' => 'Rabu',
                                'thursday'  => 'Kamis',
                                'friday'    => 'Jumat',
                                'saturday'  => 'Sabtu',
                                'sunday'    => 'Minggu',
                                default     => ucfirst($day),
                            };
                            $subLabel = $isClosed
                                ? "{$labelDay} — centang bila tutup"
                                : (str_starts_with($s->key, 'opening_') ? "{$labelDay} — Buka" : "{$labelDay} — Tutup");
                        @endphp

                        @if ($isClosed)
                            <div class="col-12">
                                <label class="form-label mb-1 d-flex align-items-center gap-2">
                                    <input type="checkbox" name="settings[{{ $s->key }}]" value="1"
                                        {{ (bool) $s->value ? 'checked' : '' }} class="form-check-input">
                                    <span>{{ $subLabel }}</span>
                                </label>
                                <small class="text-muted">key: <code>{{ $s->key }}</code></small>
                            </div>
                        @else
                            <div class="col-12 col-md-6">
                                <label class="form-label mb-1">{{ $subLabel }}</label>
                                <input type="time" name="settings[{{ $s->key }}]"
                                    value="{{ old('settings.'.$s->key, $s->value) }}"
                                    class="form-control form-control-sm">
                                <small class="text-muted">key: <code>{{ $s->key }}</code></small>
                            </div>
                        @endif
                    @else
                        <div class="col-12">
                            <label class="form-label mb-1">
                                {{ $s->deskripsi ?: $s->key }}
                                @if ($s->type === 'integer')
                                    <span class="badge bg-secondary">angka</span>
                                @endif
                            </label>

                            @php
                                $isInt = $s->type === 'integer';
                                $classes = 'form-control form-control-sm @error("settings.".$s->key) is-invalid @enderror';
                            @endphp

                            @if ($s->type === 'text')
                                <textarea name="settings[{{ $s->key }}]" rows="2" class="{{ $classes }}"
                                    placeholder="Kosongkan untuk nilai default">{{ old('settings.'.$s->key, $s->value) }}</textarea>
                            @else
                                <input type="{{ $isInt ? 'number' : 'text' }}"
                                    name="settings[{{ $s->key }}]" value="{{ old('settings.'.$s->key, $s->value) }}"
                                    class="{{ $classes }}" min="0" {{ $isInt ? 'step=1' : '' }}>
                            @endif

                            @error("settings.".$s->key)
                                <div class="invalid-feedback d-block small">{{ $message }}</div>
                            @enderror

                            <small class="text-muted">key: <code>{{ $s->key }}</code></small>
                        </div>
                    @endif
                @endforeach
            </div>

                                                    <div class="card-footer bg-transparent d-flex justify-content-end">
                    <button type="submit" class="btn btn-green btn-sm">
                        <i class="bi bi-save"></i> Simpan Pengaturan
                    </button>
                </div>
            </div>
        @endif
    @endforeach

    <div class="d-flex justify-content-end">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">Batal</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
    // Sinkronisasi checkbox visual (checked = buka) dengan hidden field closed_<day>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.day-toggle').forEach(function (cb) {
            cb.addEventListener('change', function () {
                const day         = this.dataset.day;
                const row         = document.querySelector('[data-day="' + day + '"]');
                const hidden      = row.querySelector('input[name="settings[closed_' + day + ']"]');
                const timeSection = row.querySelector('.time-section');
                const closedLabel = row.querySelector('.closed-section');

                if (this.checked) {
                    hidden.value = '0';           // Buka
                    timeSection.classList.remove('d-none');
                    closedLabel.classList.add('d-none');
                } else {
                    hidden.value = '1';           // Tutup
                    timeSection.classList.add('d-none');
                    closedLabel.classList.remove('d-none');
                }
            });
        });
    });
</script>
@endpush
