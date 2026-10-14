<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login — {{ \App\Models\Setting::siteName() }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    {{-- Stylesheet tema yang sama dengan website utama --}}
    <link href="{{ asset('css/perpus-site.css') }}?v=2.1" rel="stylesheet">
</head>
<body class="site-body">
    {{-- ============ SPLIT SCREEN : FORM KIRI, ILUSTRASI KANAN ============ --}}
    <div class="auth-split">
        {{-- ===== KIRI : FORM LOGIN ===== --}}
        <div class="auth-form-side">
            <div class="auth-card">
                <a href="{{ route('beranda') }}" class="auth-brand">
                    <span class="brand-mark"><i class="bi bi-book-half"></i></span>
                                        <span class="brand-text">{{ \App\Models\Setting::siteName() }}</span>
                </a>

                <h1 class="auth-title">Login</h1>
                <p class="auth-desc">Masuk untuk meminjam buku dan melihat riwayat bacaanmu.</p>

                @if(session('success'))
                    <div class="alert alert-soft success py-2 px-3 small mb-3">{{ session('success') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-soft danger py-2 px-3 small mb-3">
                        <i class="bi bi-exclamation-circle me-1"></i>{{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="login" class="form-label">Username / Email</label>
                        <div class="input-icon">
                            <i class="bi bi-person"></i>
                            <input type="text" name="login" id="login" value="{{ old('login') }}"
                                class="form-control @error('login') is-invalid @enderror"
                                placeholder="cth: budi atau budi@email.com" required autofocus>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-icon">
                            <i class="bi bi-key"></i>
                            <input type="password" name="password" id="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Password kamu" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label small" for="remember" style="color:var(--muted);">Ingat saya</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-green w-100 py-2">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </button>
                </form>

                <hr style="border-color:var(--garis);" class="my-4">

                <p class="small text-center mb-1 text-muted">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="fw-semibold text-decoration-none">Daftar Menjadi Anggota</a>
                </p>
                <p class="text-center mb-0">
                    <a href="{{ route('beranda') }}" class="small text-muted">&larr; Kembali ke Beranda</a>
                </p>
            </div>
        </div>


        {{-- ===== KANAN : ILUSTRASI PERPUSTAKAAN ===== --}}
        <div class="auth-art-side">
            @include('site._illustrasi')
            <p class="auth-art-caption">Koleksi &middot; Peminjaman &middot; Riwayat Baca</p>
        </div>
    </div>
</body>
</html>

