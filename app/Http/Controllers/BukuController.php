<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBukuRequest;
use App\Http\Requests\UpdateBukuRequest;
use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BukuController extends Controller
{
    /** Daftar buku (dirender di server dengan Blade). */
    public function index(): View
    {
        $bukus = Buku::with('kategori')->latest('id')->paginate(10);

        return view('buku.index', compact('bukus'));
    }

    public function show($id): View
    {
        $buku = Buku::with('kategori')->withCount('peminjamans')->findOrFail($id);

        return view('buku.show', compact('buku'));
    }

    /** Form tambah buku. */
    public function create(): View
    {
        return view('buku.form', [
            'buku' => new Buku(),
            'kategoris' => Kategori::orderBy('nama')->get(['id', 'nama']),
        ]);
    }

    public function store(StoreBukuRequest $request): RedirectResponse
    {
        Buku::create($request->validated());

        return redirect()->route('web.buku.index')->with('status', 'Buku berhasil ditambahkan.');
    }

    /** Form ubah buku. */
    public function edit(Buku $buku): View
    {
        return view('buku.form', [
            'buku' => $buku,
            'kategoris' => Kategori::orderBy('nama')->get(['id', 'nama']),
        ]);
    }

    public function update(UpdateBukuRequest $request, Buku $buku): RedirectResponse
    {
        $buku->update($request->validated());

        return redirect()->route('web.buku.index')->with('status', 'Buku berhasil diperbarui.');
    }

    /**
     * [SOAL 2 - perbaikan A2] Query memakai binding (placeholder "?"):
     * nilai 'judul' dikirim terpisah dari string SQL sebagai DATA, bukan bagian dari perintah SQL.
     */
    public function cari(Request $request): JsonResponse
    {
        $judul = $request->query('judul');

        $hasil = DB::select('SELECT * FROM bukus WHERE judul = ?', [$judul]);

        return response()->json($hasil);
    }

    /**
     * [SOAL 2 - perbaikan A3] Penghapusan hanya lewat method DELETE (+ token CSRF),
     * bukan lewat tautan GET.
     */
    public function destroy(Buku $buku): RedirectResponse
    {
        $buku->delete();

        return redirect()->route('web.buku.index')->with('status', 'Buku berhasil dihapus.');
    }
}
