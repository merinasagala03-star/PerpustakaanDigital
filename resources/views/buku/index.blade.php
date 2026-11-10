@extends('layouts.app')

@section('title', 'Daftar Buku')

@section('content')
    <h2>Daftar Buku</h2>
    <p class="section-note">{{ $bukus->total() }} judul tersedia di katalog.</p>

    {{-- Tombol tambah: hanya admin & petugas (permission kelola-data-buku) --}}
    @can('kelola-data-buku')
        <p><a href="{{ route('web.buku.create') }}" class="btn-primary">Tambah Buku</a></p>
    @endcan

    <div class="tabel-wrap">
        <table class="tabel-buku">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kategori</th>
                    <th>Judul</th>
                    <th>Penulis</th>
                    <th>Tahun Terbit</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bukus as $buku)
                    <tr>
                        <td>{{ $bukus->firstItem() + $loop->index }}</td>
                        <td>{{ $buku->kategori->nama ?? '-' }}</td>
                        {{-- [SOAL 1 - A1: XSS] SENGAJA RENTAN: {!! !!} tidak melakukan escaping --}}
                        <td>{!! $buku->judul !!}</td>
                        <td>{{ $buku->penulis }}</td>
                        <td>{{ $buku->tahun_terbit }}</td>
                        <td class="tabel-buku__aksi">
                            <a href="{{ route('web.buku.show', $buku->id) }}" class="btn-ubah">Detail</a>
                            @can('kelola-data-buku')
                                <a href="{{ route('web.buku.edit', $buku->id) }}" class="btn-ubah">Ubah</a>
                                {{-- [SOAL 1 - A3] tautan GET untuk menghapus (rentan CSRF) --}}
                                <a href="{{ route('web.buku.hapus', $buku->id) }}" class="btn-hapus"
                                   onclick="return confirm('Hapus buku ini?')">Hapus</a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="tabel-buku__info">Belum ada data buku.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="paginasi-laravel">{{ $bukus->links() }}</div>
@endsection
