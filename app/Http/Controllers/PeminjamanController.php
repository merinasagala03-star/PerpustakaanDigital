<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PeminjamanController extends Controller
{
    /**
     * [SOAL 4 - perbaikan A4] Data tetap diambil dengan findOrFail($id), tetapi SEBELUM
     * ditampilkan wajib lolos PeminjamanPolicy::view (pemilik, atau admin/petugas).
     * Selain itu -> 403 Forbidden.
     */
    public function show($id): View
    {
        $peminjaman = Peminjaman::with(['anggota', 'buku'])->findOrFail($id);

        Gate::authorize('view', $peminjaman);

        return view('peminjaman.show', compact('peminjaman'));
    }

    /** Daftar seluruh peminjaman (staf). Permission: kelola-peminjaman. */
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $peminjamans = Peminjaman::with(['anggota.user', 'buku', 'petugas'])
            ->when(
                in_array($status, [Peminjaman::DIAJUKAN, Peminjaman::DIPINJAM, Peminjaman::DITOLAK, Peminjaman::DIKEMBALIKAN], true),
                fn ($q) => $q->where('status', $status)
            )
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('peminjaman.index', compact('peminjamans', 'status'));
    }

    /** Semua role boleh mengajukan peminjaman untuk dirinya sendiri. */
    public function ajukan(Request $request, int $buku): RedirectResponse
    {
        $buku = Buku::findOrFail($buku);
        $anggota = $request->user()->pastikanAnggota();

        $sudahAda = Peminjaman::where('anggota_id', $anggota->id)
            ->where('buku_id', $buku->id)
            ->whereIn('status', [Peminjaman::DIAJUKAN, Peminjaman::DIPINJAM])
            ->exists();

        if ($sudahAda) {
            return back()->with('error', 'Anda sudah mengajukan/meminjam buku ini dan belum mengembalikannya.');
        }

        // anggota_id SELALU diambil dari akun yang login (tidak dari input),
        // sehingga tidak bisa mengajukan atas nama orang lain.
        Peminjaman::create([
            'anggota_id' => $anggota->id,
            'buku_id' => $buku->id,
            'tanggal_pinjam' => now()->toDateString(),
            'status' => Peminjaman::DIAJUKAN,
        ]);

        return redirect()->route('peminjaman.saya')->with('status', 'Pengajuan peminjaman terkirim. Menunggu persetujuan petugas.');
    }

    /** Status peminjaman milik sendiri yang masih berjalan. */
    public function saya(Request $request): View
    {
        $peminjamans = $request->user()->peminjamanSaya()
            ->with('buku')
            ->whereIn('status', [Peminjaman::DIAJUKAN, Peminjaman::DIPINJAM])
            ->latest('id')
            ->get();

        return view('peminjaman.saya', compact('peminjamans'));
    }

    public function setujui(Request $request, Peminjaman $peminjaman): RedirectResponse
    {
        if ($peminjaman->status !== Peminjaman::DIAJUKAN) {
            return back()->with('error', 'Hanya pengajuan yang berstatus "diajukan" yang bisa diproses.');
        }

        $peminjaman->update([
            'status' => Peminjaman::DIPINJAM,
            'tanggal_pinjam' => now()->toDateString(),
            'petugas_id' => $request->user()->id,
        ]);

        return back()->with('status', 'Peminjaman disetujui.');
    }

    public function tolak(Request $request, Peminjaman $peminjaman): RedirectResponse
    {
        if ($peminjaman->status !== Peminjaman::DIAJUKAN) {
            return back()->with('error', 'Hanya pengajuan yang berstatus "diajukan" yang bisa diproses.');
        }

        $peminjaman->update([
            'status' => Peminjaman::DITOLAK,
            'petugas_id' => $request->user()->id,
        ]);

        return back()->with('status', 'Pengajuan peminjaman ditolak.');
    }
}
