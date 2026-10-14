@extends('layouts.site')

@section('title')
{{ \App\Models\Setting::siteName() }}
@endsection

@section('meta_description', 'Perpustakaan online untuk mencari koleksi buku, melihat informasi buku, dan mengakses layanan perpustakaan.')

@section('content')
    {{-- ================= HERO : teks KIRI, ilustrasi KANAN ================= --}}
    <section class="hero">
        <div class="site-container">
            <div class="row align-items-center g-4 g-lg-5">
                <div class="col-lg-6">
                    <span class="hero-eyebrow"><i class="bi bi-stars"></i> Sistem Informasi Perpustakaan</span>
                    <h1 class="hero-title">
                        Selamat Datang di<br>
                                                <span class="hl">{{ \App\Models\Setting::siteName() }}</span>
                    </h1>
                    <p class="hero-subtitle">
                        Cari buku lewat katalog daring, pinjam dengan mudah, dan
                        tingkatkan minat baca — semua dalam satu aplikasi
                        perpustakaan.
                    </p>

                    {{-- Search bar --}}
                    <form method="GET" action="{{ route('katalog') }}" class="hero-search">
                        <input type="text" name="search" placeholder="Cari judul buku atau pengarang..." aria-label="Cari buku">
                        <button type="submit" class="btn btn-green px-3">
                            <i class="bi bi-search"></i> Cari Buku
                        </button>
                    </form>

                    @guest
                        <div class="hero-cta">
                            <a href="{{ route('register') }}" class="btn btn-green"><i class="bi bi-person-plus"></i> Daftar Menjadi Anggota</a>
                            <a href="{{ route('login') }}" class="btn btn-outline-navy"><i class="bi bi-box-arrow-in-right"></i> Login</a>
                        </div>
                    @endguest
                </div>

                <div class="col-lg-6">
                    <div class="hero-art">
                        @include('site._illustrasi')
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="site-container page-body pt-4">
        {{-- ================= STATISTIK ================= --}}
        <div class="row g-3 g-md-4 mb-5">
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="bi bi-journal-richtext"></i></div>
                    <div class="stat-number">{{ $totalJudul }}</div>
                    <p class="stat-label">Koleksi Judul</p>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="bi bi-stack"></i></div>
                    <div class="stat-number">{{ $totalBuku }}</div>
                    <p class="stat-label">Total Stok Buku</p>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon pink"><i class="bi bi-people"></i></div>
                    <div class="stat-number">{{ $totalAnggota }}</div>
                    <p class="stat-label">Anggota Terdaftar</p>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon yellow"><i class="bi bi-clock"></i></div>
                    <div class="stat-number">07.00</div>
                    <p class="stat-label">Buka Setiap Hari<br>sampai 15.30 WIB</p>
                </div>
            </div>
        </div>


        {{-- ================= BUKU TERBARU ================= --}}
        <div class="section-head">
            <h2>📚 Buku Terbaru</h2>
            <a href="{{ route('katalog') }}" class="link-more">Lihat Semua Katalog <i class="bi bi-arrow-right"></i></a>
        </div>

        @if($bukuTerbaru->isEmpty())
            <div class="empty-state mb-5">
                <div><i class="bi bi-book es-icon"></i></div>
                <p class="mb-0 fw-semibold">Belum ada koleksi buku.</p>
            </div>
        @else
            <div class="row g-3 g-md-4 mb-5">
                @foreach($bukuTerbaru as $buku)
                    @include('site._buku-card', ['buku' => $buku])
                @endforeach
            </div>
        @endif

        {{-- ================= KATEGORI ================= --}}
        @if($kategori->isNotEmpty())
            <div class="section-head">
                <h2><i class="bi bi-tags-fill me-2" style="color:#16C94A;"></i>Jelajahi Kategori</h2>
            </div>
            <div class="d-flex flex-wrap gap-2 mb-5">
                @foreach($kategori as $k)
                    <a href="{{ route('katalog', ['kategori' => $k]) }}" class="chip">
                        {{ $k }} <i class="bi bi-arrow-right-short"></i>
                    </a>
                @endforeach
            </div>
        @endif

        {{-- ================= INFORMASI PERPUSTAKAAN ================= --}}
        <div class="card mb-5">
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="stat-icon green mb-2"><i class="bi bi-clock-history"></i></div>
                        <h6 class="fw-bold mb-1">Jam Layanan</h6>
                        <p class="small text-muted mb-0">Senin–Jumat: 07.00 – 15.30 WIB<br>Sabtu: 08.00 – 12.00 WIB</p>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-icon blue mb-2"><i class="bi bi-journal-check"></i></div>
                        <h6 class="fw-bold mb-1">Ketentuan Peminjaman</h6>
                                                <p class="small text-muted mb-0">Maksimal {{ \App\Models\Setting::get('lama_peminjaman', 7) }} hari per peminjaman<br>Satu judul satu anggota</p>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-icon yellow mb-2"><i class="bi bi-person-badge"></i></div>
                        <h6 class="fw-bold mb-1">Cara Menjadi Anggota</h6>
                        <p class="small text-muted mb-0">Klik "Daftar Anggota", isi NIS &amp; kelas, lalu langsung bisa meminjam.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ LOGIN DI BERANDA (khusus pengunjung) ============
             Layout WAJIB: form login di KIRI, ilustrasi di KANAN. --}}
        @guest
            <div class="login-home" id="login-beranda">
                <div class="lh-form">
                    <span class="hero-eyebrow"><i class="bi bi-shield-lock"></i> Sudah punya akun?</span>
                    <h2 class="mt-2 mb-1" style="font-size:1.6rem;">Login Anggota</h2>
                    <p class="text-muted mb-4" style="font-size:0.95rem;">
                        Masuk dengan username atau email untuk meminjam buku dan melihat riwayat bacaanmu.
                    </p>

                    @if($errors->any())
                        <div class="alert alert-soft danger py-2 px-3 small mb-3">
                            <i class="bi bi-exclamation-circle me-1"></i> {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.post') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="bh-login" class="form-label">Username / Email</label>
                            <div class="input-icon">
                                <i class="bi bi-person"></i>
                                <input type="text" name="login" id="bh-login" value="{{ old('login') }}"
                                    class="form-control @error('login') is-invalid @enderror"
                                    placeholder="cth: budi atau budi@email.com" required autofocus>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="bh-password" class="form-label">Password</label>
                            <div class="input-icon">
                                <i class="bi bi-key"></i>
                                <input type="password" name="password" id="bh-password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Password kamu" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-green w-100 py-2">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </button>
                    </form>

                    <p class="small text-center mt-3 mb-0 text-muted">
                        Belum jadi anggota? <a href="{{ route('register') }}" class="fw-semibold">Daftar sekarang</a>
                    </p>
                </div>
                <div class="lh-art">
                    @include('site._illustrasi')
                </div>
            </div>
        @endguest

    </div>
@endsection
