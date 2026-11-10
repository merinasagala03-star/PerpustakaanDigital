<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Akun demo. Role ditetapkan di sini (jalur berwenang), bukan dari formulir registrasi.
     *
     * [SOAL 5c] Password akun ADMIN tidak lagi "password". Nilainya dibaca dari variabel
     * ADMIN_PASSWORD di .env (tidak ikut di-commit). Jika belum diisi, dibuat acak dan
     * ditampilkan sekali di terminal. Akun petugas & siswa tetap memakai password demo
     * "password" hanya untuk pengujian lokal.
     */
    public function run(): void
    {
        $passwordAdmin = env('ADMIN_PASSWORD');
        if (blank($passwordAdmin)) {
            $passwordAdmin = Str::random(24);
            $this->command?->warn("ADMIN_PASSWORD belum diisi di .env. Password admin acak: {$passwordAdmin}");
        }

        $akun = [
            ['Admin Perpustakaan',  'admin@perpustakaan.test',   'admin',   '081200000001'],
            ['Petugas Perpustakaan', 'petugas@perpustakaan.test', 'petugas', '081200000002'],
            ['Siswa Satu',          'siswa1@perpustakaan.test',  'siswa',   '081200000003'],
            ['Siswa Dua',           'siswa2@perpustakaan.test',  'siswa',   '081200000004'],
        ];

        foreach ($akun as [$nama, $email, $role, $telepon]) {
            $anggota = Anggota::firstOrCreate(
                ['email' => $email],
                ['nama' => $nama, 'no_telepon' => $telepon],
            );

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $nama,
                    'password' => Hash::make('password'),
                    'anggota_id' => $anggota->id,
                ],
            );

            $user->syncRoles([$role]);

            // [SOAL 5c] Pastikan password admin SELALU diganti, juga pada akun yang sudah ada
            // di database (firstOrCreate di atas tidak mengubah record yang sudah ada).
            if ($role === 'admin') {
                $user->forceFill(['password' => $passwordAdmin])->save();   // cast 'hashed' otomatis bcrypt
            }
        }

        // Contoh riwayat peminjaman agar halaman "riwayat" tidak kosong saat demo.
        $buku = Buku::orderBy('id')->take(2)->get();
        foreach (['siswa1@perpustakaan.test' => 0, 'siswa2@perpustakaan.test' => 1] as $email => $i) {
            $user = User::where('email', $email)->first();
            if ($user && isset($buku[$i]) && ! $user->peminjamanSaya()->exists()) {
                Peminjaman::create([
                    'anggota_id' => $user->anggota_id,
                    'buku_id' => $buku[$i]->id,
                    'tanggal_pinjam' => now()->subDays(10)->toDateString(),
                    'tanggal_kembali' => now()->subDays(3)->toDateString(),
                    'status' => Peminjaman::DIKEMBALIKAN,
                ]);
            }
        }
    }
}
