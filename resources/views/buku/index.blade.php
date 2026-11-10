@extends('layouts.app')

@section('title', 'Daftar Buku')

@section('content')
    <h2>Daftar Buku</h2>
    <p class="section-note">{{ $bukus->total() }} judul tersedia di katalog.</p>

    {{-- [SOAL 3] Tombol tambah: hanya yang lolos Gate 'kelola-buku' (admin & petugas) --}}
    @can('kelola-buku')
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
                        {{-- [SOAL 2 - perbaikan A1] {{ }} melakukan escaping HTML --}}
                        <td>{{ $buku->judul }}</td>
                        <td>{{ $buku->penulis }}</td>
                        <td>{{ $buku->tahun_terbit }}</td>
                        <td class="tabel-buku__aksi">
                            <a href="{{ route('web.buku.show', $buku->id) }}" class="btn-ubah">Detail</a>
                            @can('kelola-buku')
                                <a href="{{ route('web.buku.edit', $buku->id) }}" class="btn-ubah">Ubah</a>
                                {{-- [SOAL 2 - perbaikan A3] DELETE + @csrf + @method('DELETE') --}}
                                <form method="POST" action="{{ route('web.buku.destroy', $buku->id) }}" class="inline-form"
                                      onsubmit="return confirm('Hapus buku ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-hapus">Hapus</button>
                                </form>
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
