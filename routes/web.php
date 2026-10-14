<?php

use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\KartuAnggotaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PengembalianController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| WEBSITE PERPUSTAKAAN (publik + user)
|--------------------------------------------------------------------------
| Tampilan user = website perpustakaan (bukan dashboard admin).
*/
Route::get('/', [KatalogController::class, 'beranda'])->name('beranda');
Route::get('/katalog', [KatalogController::class, 'katalog'])->name('katalog');
Route::get('/katalog/{buku}', [KatalogController::class, 'show'])->name('buku.detail');
Route::get('/tentang', [\App\Http\Controllers\TentangController::class, '__invoke'])->name('tentang');

/*
|--------------------------------------------------------------------------
| AUTENTIKASI
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| AREA ADMIN - Dashboard pengelolaan perpustakaan
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

        // CRUD Buku
        Route::resource('buku', BukuController::class);

        // CRUD Anggota
        // PENTING: Str::singular('anggota') menghasilkan 'anggotum' oleh inflector,
        // sehingga route model binding tidak cocok dengan parameter $anggota.
        // Parameter dipaksa bernama 'anggota'.
        Route::resource('anggota', AnggotaController::class)->parameters(['anggota' => 'anggota']);

                // CRUD Transaksi
        Route::resource('transaksi', TransaksiController::class);

        // Pengaturan global (settings)
        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');

        // History seluruh transaksi
        Route::get('/history', [HistoryController::class, 'index'])->name('history');

        // Report
        Route::get('/report/dashboard', [ReportController::class, 'dashboard'])->name('report.dashboard');
        Route::get('/report/buku', [ReportController::class, 'buku'])->name('report.buku');
        Route::get('/report/anggota', [ReportController::class, 'anggota'])->name('report.anggota');
        Route::get('/report/peminjaman', [ReportController::class, 'peminjaman'])->name('report.peminjaman');
        Route::get('/report/pengembalian', [ReportController::class, 'pengembalian'])->name('report.pengembalian');
    });

/*
|--------------------------------------------------------------------------
| AREA ANGGOTA (USER) - fitur keanggotaan di website perpustakaan
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'user'])
    ->name('user.')
    ->group(function () {
                // Peminjaman Saya + aksi pinjam
        Route::get('/peminjaman-saya', [PeminjamanController::class, 'index'])->name('peminjaman.index');
        Route::post('/peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');

        // Pengembalian
        Route::get('/pengembalian', [PengembalianController::class, 'index'])->name('pengembalian.index');
        Route::post('/pengembalian/{transaksi}', [PengembalianController::class, 'store'])->name('pengembalian.store');

        // History milik sendiri
        Route::get('/history', [RiwayatController::class, 'index'])->name('riwayat.index');

        // Kartu anggota (Pelajar / Mahasiswa)
        Route::get('/kartu', [KartuAnggotaController::class, 'edit'])->name('kartu.edit');
        Route::put('/kartu', [KartuAnggotaController::class, 'update'])->name('kartu.update');
        Route::delete('/kartu', [KartuAnggotaController::class, 'destroy'])->name('kartu.destroy');
    });

/*
|--------------------------------------------------------------------------
| PROFIL - dipakai ADMIN dan USER (nama route tetap user.profile.*)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')
    ->name('user.')
    ->group(function () {
        Route::get('/profil', [ProfileController::class, 'show'])->name('profile.index');
        Route::get('/profil/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
        Route::get('/profil/password', [ProfileController::class, 'showChangePassword'])->name('profile.password');
        Route::put('/profil/password', [ProfileController::class, 'changePassword'])->name('profile.password.update');
    });
