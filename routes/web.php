<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PengembalianController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RiwayatController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route web — Sistem Perpustakaan Digital (Modul V: Autentikasi & RBAC)
|--------------------------------------------------------------------------
| 'auth'       -> harus login (Soal 2)
| 'permission' -> middleware Spatie Laravel Permission (Soal 5)
| Route login/register/logout ada di routes/auth.php (middleware 'guest').
*/

// Halaman publik
Route::view('/', 'home')->name('home');

// Semua route di bawah ini WAJIB login
Route::middleware('auth')->group(function () {

    // /dashboard hanya "pengarah": admin -> dashboard admin, petugas -> dashboard petugas, siswa -> daftar buku
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ----- Dashboard per role -----
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
        ->middleware('permission:akses-dashboard-admin')->name('admin.dashboard');

    Route::get('/petugas/dashboard', [DashboardController::class, 'petugas'])
        ->middleware('permission:akses-dashboard-petugas')->name('petugas.dashboard');

    // ----- Buku (semua role boleh melihat; kelola lewat /api/buku) -----
    Route::middleware('permission:lihat-daftar-buku')->group(function () {
        Route::get('/buku', [BukuController::class, 'index'])->name('web.buku.index');
        Route::get('/buku/{id}', [BukuController::class, 'show'])->whereNumber('id')->name('web.buku.show');
    });

    // ----- Kelola buku lewat halaman web (admin & petugas) -----
    Route::middleware('permission:kelola-data-buku')->group(function () {
        Route::get('/buku/create', [BukuController::class, 'create'])->name('web.buku.create');
        Route::post('/buku', [BukuController::class, 'store'])->name('web.buku.store');
        Route::get('/buku/{buku}/edit', [BukuController::class, 'edit'])->whereNumber('buku')->name('web.buku.edit');
        Route::put('/buku/{buku}', [BukuController::class, 'update'])->whereNumber('buku')->name('web.buku.update');
        // A3 - penghapusan lewat DELETE (form @csrf + @method('DELETE'))
        Route::delete('/buku/{buku}', [BukuController::class, 'destroy'])->whereNumber('buku')->name('web.buku.destroy');
    });

    // =====================================================================
    // [SOAL 2] Perbaikan celah. Route GET penghapusan (A3) sudah DIHAPUS.
    // =====================================================================
    // A2 - query memakai binding (lihat BukuController::cari)
    Route::get('/buku-cari', [BukuController::class, 'cari'])->name('web.buku.cari');
    // A4 - IDOR: detail peminjaman (otorisasi ditambahkan pada Soal 4)
    Route::get('/peminjaman/{id}', [PeminjamanController::class, 'show'])
        ->whereNumber('id')->name('peminjaman.show');

    // ----- Mengajukan peminjaman (semua role) -----
    Route::post('/buku/{buku}/pinjam', [PeminjamanController::class, 'ajukan'])
        ->whereNumber('buku')
        ->middleware('permission:mengajukan-peminjaman-buku')->name('peminjaman.ajukan');

    // ----- Data milik sendiri (identitas tetap dicek di controller/policy) -----
    Route::get('/peminjaman-saya', [PeminjamanController::class, 'saya'])
        ->middleware('permission:melihat-status-peminjaman-sendiri')->name('peminjaman.saya');

    Route::get('/riwayat-peminjaman', [RiwayatController::class, 'saya'])
        ->middleware('permission:melihat-riwayat-peminjaman-sendiri')->name('riwayat.saya');

    // Riwayat milik user lain: otorisasi lewat UserPolicy::viewRiwayat (403 untuk siswa lain)
    Route::get('/riwayat-peminjaman/{user}', [RiwayatController::class, 'show'])->name('riwayat.user');

    // ----- Profil sendiri -----
    Route::get('/profil', [ProfileController::class, 'edit'])
        ->middleware('permission:lihat-profil-sendiri')->name('profile.edit');
    Route::patch('/profil', [ProfileController::class, 'update'])
        ->middleware('permission:ubah-profil-sendiri')->name('profile.update');

    // ----- Operasional perpustakaan: admin & petugas -----
    Route::middleware('permission:kelola-peminjaman')->group(function () {
        Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
    });
    Route::post('/peminjaman/{peminjaman}/setujui', [PeminjamanController::class, 'setujui'])
        ->middleware('permission:memproses-persetujuan-peminjaman')->name('peminjaman.setujui');
    Route::post('/peminjaman/{peminjaman}/tolak', [PeminjamanController::class, 'tolak'])
        ->middleware('permission:memproses-persetujuan-peminjaman')->name('peminjaman.tolak');

    Route::get('/pengembalian', [PengembalianController::class, 'index'])
        ->middleware('permission:kelola-pengembalian')->name('pengembalian.index');
    Route::post('/pengembalian/{peminjaman}/kembalikan', [PengembalianController::class, 'kembalikan'])
        ->middleware('permission:mencatat-pengembalian-buku')->name('pengembalian.kembalikan');

    Route::get('/laporan', [LaporanController::class, 'index'])
        ->middleware('permission:lihat-laporan-perpustakaan')->name('laporan.index');

    // ----- Kelola akun & role: HANYA admin (petugas -> 403) -----
    Route::prefix('admin')->name('admin.')->middleware('permission:kelola-data-pengguna')->group(function () {
        Route::resource('pengguna', UserController::class)
            ->parameters(['pengguna' => 'user'])
            ->names('users')
            ->except('show');
    });
});

require __DIR__.'/auth.php';
