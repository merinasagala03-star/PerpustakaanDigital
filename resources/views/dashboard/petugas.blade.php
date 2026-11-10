@extends('layouts.app')

@section('title', 'Dashboard Petugas')

@section('content')
    <div class="dash-hero dash-hero--petugas">
        <div>
            <p class="dash-hero__kicker">{{ now()->translatedFormat('l, d F Y') }}</p>
            <h2>Halo, {{ auth()->user()->name }}!</h2>
            <p>Operasional harian perpustakaan: proses pengajuan dan catat pengembalian.</p>
        </div>
        <span class="badge badge--role dash-hero__role">Petugas</span>
    </div>

    <div class="stat-grid">
        <div class="stat stat--indigo"><span class="stat__ikon"><x-ikon nama="buku" /></span><span class="stat__angka">{{ $totalBuku }}</span><span class="stat__label">Judul buku</span></div>
        <div class="stat stat--amber"><span class="stat__ikon"><x-ikon nama="jam" /></span><span class="stat__angka">{{ $diajukan }}</span><span class="stat__label">Menunggu persetujuan</span></div>
        <div class="stat stat--blue"><span class="stat__ikon"><x-ikon nama="pinjam" /></span><span class="stat__angka">{{ $dipinjam }}</span><span class="stat__label">Sedang dipinjam</span></div>
    </div>

    @include('dashboard.partials.terbaru')

    <h3 class="dash-judul">Menu cepat</h3>
    <div class="menu-kartu">
        <a href="{{ route('web.buku.index') }}"><x-ikon nama="buku" /><strong>Kelola Buku</strong><span>Tambah, ubah, hapus katalog</span></a>
        <a href="{{ route('peminjaman.index', ['status' => 'diajukan']) }}"><x-ikon nama="pinjam" /><strong>Proses Peminjaman</strong><span>Pengajuan yang menunggu</span></a>
        <a href="{{ route('pengembalian.index') }}"><x-ikon nama="cek" /><strong>Catat Pengembalian</strong><span>Tandai buku yang kembali</span></a>
        <a href="{{ route('laporan.index') }}"><x-ikon nama="laporan" /><strong>Laporan</strong><span>Rekap aktivitas perpustakaan</span></a>
    </div>
@endsection
