<?php

namespace App\Policies;

use App\Models\Peminjaman;
use App\Models\User;

class PeminjamanPolicy
{
    /**
     * [SOAL 4] Siapa yang boleh melihat SATU data peminjaman?
     *  - admin dan petugas : semua peminjaman;
     *  - siswa             : hanya peminjaman miliknya sendiri
     *                        (peminjaman.anggota_id harus sama dengan anggota_id akun yang login).
     * Ini pemeriksaan KEPEMILIKAN pada tingkat data, bukan sekadar pemeriksaan role.
     */
    public function view(User $user, Peminjaman $peminjaman): bool
    {
        if ($user->hasAnyRole(['admin', 'petugas'])) {
            return true;
        }

        return $user->anggota_id !== null
            && (int) $user->anggota_id === (int) $peminjaman->anggota_id;
    }
}
