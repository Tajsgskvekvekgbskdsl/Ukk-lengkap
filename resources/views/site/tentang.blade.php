@extends('layouts.site')

@section('title')
    Tentang {{ \App\Models\Setting::siteName() }}
@endsection

@section('content')
    <header class="page-header">
        <div class="site-container">
            <h1>
                <i class="bi bi-info-circle me-2" style="color:#16C94A;"></i>
                                 {{ \App\Models\Setting::get('nama_perpustakaan', 'Perpustakaan') }}
            </h1>
            <p>{{ \App\Models\Setting::get('deskripsi', 'Sistem Informasi Perpustakaan — katalog daring, peminjaman, dan pengembalian buku.') }}</p>
        </div>
    </header>

    <div class="site-container page-body">
        {{-- Ringkasan singkat --}}
        <div class="card mb-4" style="box-shadow: var(--shadow-halus);">
            <div class="card-body p-4 p-md-5">
                <p class="mb-0" style="font-size:1.02rem; max-width:820px;">
                    Website ini digunakan untuk melihat koleksi buku, melakukan
                    peminjaman, pengembalian, dan melihat riwayat transaksi
                    perpustakaan.
                </p>
            </div>
        </div>

        {{-- Cara pakai + aturan denda --}}
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card h-100" style="box-shadow: var(--shadow-halus);">
                    <div class="card-body p-4">
                        <div class="stat-icon green mb-3"><i class="bi bi-journals"></i></div>
                        <h5 class="fw-bold mb-3">Cara Menggunakan</h5>
                        <ul class="mb-0 small text-muted" style="line-height:1.9;">
                            <li><i class="bi bi-check2 me-2" style="color:#16C94A;"></i>Daftar akun gratis pakai NIS &amp; kelas.</li>
                            <li><i class="bi bi-check2 me-2" style="color:#16C94A;"></i>Cari buku lewat katalog daring.</li>
                            <li><i class="bi bi-check2 me-2" style="color:#16C94A;"></i>Buka halaman detail buku, klik "Pinjam".</li>
                            <li><i class="bi bi-check2 me-2" style="color:#16C94A;"></i>Kembalikan buku tepat waktu lewat halaman pengembalian.</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100" style="box-shadow: var(--shadow-halus);">
                    <div class="card-body p-4">
                        <div class="stat-icon yellow mb-3"><i class="bi bi-journal-check"></i></div>
                        <h5 class="fw-bold mb-3">Aturan Peminjaman &amp; Denda</h5>

                                                @php
                            $cfg       = \App\Models\Setting::dendaConfig();
                            $lama      = $cfg['lama_peminjaman'];
                            $aktif     = $cfg['tarif_khusus_aktif'];
                            $normal    = $cfg['denda_normal'];
                            $pelajar   = $cfg['denda_pelajar'];
                            $mahasiswa = $cfg['denda_mahasiswa'];
                        @endphp

                        <ul class="mb-0 small text-muted" style="line-height:1.9;">
                            <li><i class="bi bi-dot me-1"></i>Masa pinjam {{ $lama }} hari sejak tanggal peminjaman.</li>
                            <li><i class="bi bi-dot me-1"></i>Satu anggota satu judul satu eksemplar.</li>
                            <li><i class="bi bi-dot me-1"></i>{{ $aktif ? 'Tarif denda khusus aktif' : 'Tarif denda khusus dimatikan' }}; Denda Normal
                                Rp{{ number_format($normal, 0, ',', '.') }}/hari.</li>
                            @if ($aktif)
                                <li><i class="bi bi-dot me-1"></i>Punya kartu Pelajar: Denda Pelajar Rp{{ number_format($pelajar, 0, ',', '.') }}/hari.</li>
                                <li><i class="bi bi-dot me-1"></i>Punya kartu Mahasiswa: Denda Mahasiswa Rp{{ number_format($mahasiswa, 0, ',', '.') }}/hari.</li>
                            @else
                                <li><i class="bi bi-dot me-1"></i>Tarif khusus dimatikan — semua memakai Denda Normal.</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kontak + logo --}}
        @php
            $telp   = \App\Models\Setting::get('telepon', null);
            $email  = \App\Models\Setting::get('email', null);
            $alamat = \App\Models\Setting::get('alamat', null);
            $logo   = \App\Models\Setting::get('logo', null);
        @endphp

        <div class="row g-4 mb-4">
            @if ($email || $telp || $alamat)
                <div class="col-md-6">
                    <div class="card h-100" style="box-shadow: var(--shadow-halus);">
                        <div class="card-body p-4">
                            <div class="stat-icon blue mb-3"><i class="bi bi-telephone-outbound"></i></div>
                            <h5 class="fw-bold mb-3">Kontak</h5>
                            <ul class="mb-0 small text-muted" style="line-height:1.9;">
                                @if ($email)  <li>Email: {{ $email }}</li>@endif
                                @if ($telp)   <li>Telepon: {{ $telp }}</li>@endif
                                @if ($alamat) <li>Alamat: {{ $alamat }}</li>@endif
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
            @if ($logo)
                <div class="col-md-6 d-flex align-items-stretch">
                    <div class="card h-100" style="box-shadow: var(--shadow-halus);">
                        <div class="card-body p-4 d-flex align-items-center justify-content-center">
                            <img src="{{ asset($logo) }}" alt="Logo" class="img-fluid" style="max-height:90px;">
                        </div>
                    </div>
                </div>
            @endif
        </div>

                {{-- Jam Operasional (dinamis dari Settings) --}}
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card h-100" style="box-shadow: var(--shadow-halus);">
                    <div class="card-body p-4">
                        <div class="stat-icon orange mb-3"><i class="bi bi-clock"></i></div>
                        <h5 class="fw-bold mb-3">Jam Operasional</h5>
                        <table class="table table-borderless table-sm mb-0">
                            <tbody>
                                @foreach (\App\Models\Setting::openingHours() as $oh)
                                    <tr>
                                        <td class="fw-semibold" style="width:110px;">{{ $oh['day'] }}</td>
                                        <td class="mb-0">
                                            @if ($oh['closed'])
                                                <span class="badge bg-secondary">Tutup</span>
                                            @else
                                                {{ $oh['open'] }} &ndash; {{ $oh['close'] }} WIB
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ajakan ke pengunjung tidak terdaftar --}}
        @guest
            <div class="card mb-4" style="box-shadow: var(--shadow-halus);">
                <div class="card-body p-4 text-center p-md-5">
                    <h3 class="fw-bold mb-2">Butuh akun untuk meminjam buku?</h3>
                    <p class="text-muted mb-4">Daftar gratis dengan NIS dan kelasmu, lalu langsung bisa meminjam.</p>
                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        <a href="{{ route('register') }}" class="btn btn-green"><i class="bi bi-person-plus"></i> Daftar Anggota</a>
                        <a href="{{ route('katalog') }}" class="btn btn-outline-navy"><i class="bi bi-collection"></i> Lihat Katalog</a>
                    </div>
                </div>
            </div>
        @endguest
    </div>
@endsection