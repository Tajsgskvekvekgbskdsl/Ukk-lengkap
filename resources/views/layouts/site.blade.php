@php $siteName = \App\Models\Setting::siteName(); @endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', $siteName)</title>

    <meta name="description"
        content="@yield('meta_description', 'Perpustakaan online untuk mencari koleksi buku, melihat informasi buku, dan mengakses layanan perpustakaan.')">

    <meta name="robots" content="index, follow">

    {{-- Font: Inter (body) + Poppins (heading) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    {{-- Stylesheet utama website perpustakaan (halaman user) --}}
    <link href="{{ asset('css/perpus-site.css') }}?v=2.1" rel="stylesheet">
</head>
<body class="site-body">

    {{-- ================= NAVBAR ================= --}}
    <nav class="navbar navbar-expand-lg site-navbar">
        <div class="site-container">
            <a class="site-brand me-auto" href="{{ route('beranda') }}">
                <span class="brand-mark"><i class="bi bi-book-half"></i></span>
                                <span class="brand-text">{{ $siteName }}</span>
            </a>

            <button class="navbar-toggler custom border-0" type="button"
                data-bs-toggle="collapse" data-bs-target="#siteNav"
                aria-controls="siteNav" aria-expanded="false" aria-label="Menu">
                <span class="bar"></span><span class="bar"></span><span class="bar"></span>
            </button>

            <div class="collapse navbar-collapse" id="siteNav">
                <ul class="site-menu mx-lg-auto my-2 my-lg-0" role="list">
                    <li><a href="{{ route('beranda') }}" class="{{ request()->routeIs('beranda') ? 'active' : '' }}">Beranda</a></li>
                    <li><a href="{{ route('katalog') }}" class="{{ request()->routeIs('katalog') || request()->routeIs('buku.detail') ? 'active' : '' }}">Katalog Buku</a></li>
                    <li><a href="{{ route('tentang') }}" class="{{ request()->routeIs('tentang') ? 'active' : '' }}">Tentang</a></li>

                    @auth
                        @unless(auth()->user()->isAdmin())
                            <li><a href="{{ route('user.peminjaman.index') }}" class="{{ request()->routeIs('user.peminjaman.index') ? 'active' : '' }}">Peminjaman Saya</a></li>
                            <li><a href="{{ route('user.pengembalian.index') }}" class="{{ request()->routeIs('user.pengembalian.index') ? 'active' : '' }}">Pengembalian</a></li>
                            <li><a href="{{ route('user.riwayat.index') }}" class="{{ request()->routeIs('user.riwayat.index') ? 'active' : '' }}">History</a></li>
                            <li><a href="{{ route('user.profile.index') }}" class="{{ request()->routeIs('user.profile.*') ? 'active' : '' }}">Profil</a></li>
                        @endunless
                    @endauth
                </ul>

                <div class="nav-actions">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-navy">
                                <i class="bi bi-speedometer2"></i> Dashboard Admin
                            </a>
                        @endif
                        <a href="{{ route('user.profile.index') }}" class="user-chip">
                            <i class="bi bi-person-circle"></i> {{ auth()->user()->username }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-ghost-danger">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-navy">Login</a>
                        <a href="{{ route('register') }}" class="btn btn-sm btn-green">Daftar Anggota</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- ================= KONTEN ================= --}}
    <main class="site-main">
        @if(session('success'))
            <div class="site-container pt-3">
                <div class="alert alert-soft success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="site-container pt-3">
                <div class="alert alert-soft danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    {{-- ================= FOOTER ================= --}}
    <footer class="site-footer">
        <div class="site-container">
            <div class="row gy-4">
                <div class="col-md-5">
                    <div class="f-brand">
                        <span class="brand-mark"><i class="bi bi-book-half"></i></span>
                                            <span>{{ $siteName }}</span>
                    </div>
                    <p class="mb-0" style="max-width: 320px;">
                        Sistem Informasi Perpustakaan untuk mencari koleksi buku
                        lewat katalog daring, meminjam dengan mudah, dan
                        meningkatkan minat baca.
                    </p>
                </div>
                                <div class="col-md-3 col-6">
                    <h6>Jam Operasional</h6>
                    <ul class="mb-0">
                        @foreach (\App\Models\Setting::openingHours() as $oh)
                            <li>{{ $oh['day'] }}<br>
                                @if ($oh['closed'])
                                    <span class="badge bg-secondary">Tutup</span>
                                @else
                                    {{ $oh['open'] }} &ndash; {{ $oh['close'] }} WIB
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-md-4 col-6">
                    <h6>Kontak</h6>
                    <ul>
                        <li><i class="bi bi-info-circle me-2" style="color:#16C94A;"></i>Hubungi petugas perpustakaan untuk bantuan lebih lanjut.</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                                &copy; {{ date('Y') }} {{ $siteName }} — Sistem Informasi Perpustakaan.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>

