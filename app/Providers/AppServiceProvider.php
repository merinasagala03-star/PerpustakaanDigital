<?php

namespace App\Providers;

use App\Models\Peminjaman;
use App\Models\User;
use App\Policies\PeminjamanPolicy;
use App\Policies\UserPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <-- 1. TAMBAHKAN BARIS INI

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // <-- 2. TAMBAHKAN KODE INI UNTUK MENGATASI CSS TERBLOKIR DI VERCEL
        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }

        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Peminjaman::class, PeminjamanPolicy::class);   // [SOAL 4]

        // [SOAL 3] Aturan pengelolaan buku dipusatkan di SATU tempat.
        // Hanya role admin dan petugas yang boleh menambah, mengubah, dan menghapus buku.
        Gate::define('kelola-buku', function (User $user): bool {
            return $user->hasAnyRole(['admin', 'petugas']);
        });

        // Tautan paginasi sederhana (tanpa dependensi kelas Tailwind)
        Paginator::useBootstrapFour();
    }
}