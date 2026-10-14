<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Daftar Anggota — {{ \App\Models\Setting::siteName() }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="{{ asset('css/perpus-site.css') }}?v=2.1" rel="stylesheet">
</head>
<body class="site-body">
    {{-- ============ SPLIT SCREEN : FORM KIRI, ILUSTRASI KANAN ============ --}}
    <div class="auth-split">
        {{-- ===== KIRI : FORM REGISTRASI ===== --}}
        <div class="auth-form-side" style="justify-content: flex-start;">
            <div class="auth-card" style="max-width: 520px;">
                <a href="{{ route('beranda') }}" class="auth-brand">
                    <span class="brand-mark"><i class="bi bi-book-half"></i></span>
                                        <span class="brand-text">{{ \App\Models\Setting::siteName() }}</span>
                </a>

                <h1 class="auth-title">Daftar Menjadi Anggota</h1>
                <p class="auth-desc">Buat akun untuk mulai meminjam buku perpustakaan.</p>

                @if(session('success'))
                    <div class="alert alert-soft success py-2 px-3 small mb-3">{{ session('success') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-soft danger py-2 px-3 small mb-3">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif


                {{-- Semua field & nama input sama persis dengan versi lama --}}
                <form method="POST" action="{{ route('register.post') }}">
                    @csrf
                    <div class="row g-2">
                        <div class="col-12">
                            <label class="form-label">Nama Lengkap</label>
                            <div class="input-icon">
                                <i class="bi bi-person-badge"></i>
                                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}"
                                    class="form-control @error('nama_lengkap') is-invalid @enderror" required>
                            </div>
                            @error('nama_lengkap')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Username</label>
                            <div class="input-icon">
                                <i class="bi bi-at"></i>
                                <input type="text" name="username" value="{{ old('username') }}"
                                    class="form-control @error('username') is-invalid @enderror" required>
                            </div>
                            @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Email</label>
                            <div class="input-icon">
                                <i class="bi bi-envelope"></i>
                                <input type="email" name="email" value="{{ old('email') }}"
                                    class="form-control @error('email') is-invalid @enderror" required>
                            </div>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Password</label>
                            <div class="input-icon">
                                <i class="bi bi-key"></i>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror" required>
                            </div>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Konfirmasi Password</label>
                            <div class="input-icon">
                                <i class="bi bi-key-fill"></i>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-12 mt-2"><hr class="my-1"><small style="color:var(--muted);">Data Siswa (opsional)</small></div>

                        <div class="col-12 col-md-6">
                            <label class="form-label">NIS</label>
                            <input type="text" name="nis" value="{{ old('nis') }}"
                                class="form-control @error('nis') is-invalid @enderror">
                            @error('nis')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Kelas</label>
                            <input type="text" name="kelas" value="{{ old('kelas') }}" placeholder="Contoh: X IPA 1"
                                class="form-control @error('kelas') is-invalid @enderror">
                            @error('kelas')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" rows="2"
                                class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat') }}</textarea>
                            @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-green w-100 py-2 mt-3">Daftar Sekarang</button>
                </form>

                <p class="text-center small mt-3 mb-1 text-muted">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="fw-semibold text-decoration-none">Login di sini</a>
                </p>
                <p class="text-center mb-0">
                    <a href="{{ route('beranda') }}" class="small text-muted">&larr; Kembali ke Beranda</a>
                </p>
            </div>
        </div>

        {{-- ===== KANAN : ILUSTRASI ===== --}}
        <div class="auth-art-side">
            @include('site._illustrasi')
            <p class="auth-art-caption">Gratis untuk semua siswa &amp; guru</p>
        </div>
    </div>
</body>
</html>

