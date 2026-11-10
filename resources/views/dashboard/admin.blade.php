@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="dash-hero">
        <div>
            <p class="dash-hero__kicker">{{ now()->translatedFormat('l, d F Y') }}</p>
            <h2>Halo, {{ auth()->user()->name }}!</h2>
            <p>Ringkasan seluruh kebutuhan administrasi perpustakaan hari ini.</p>
        </div>
        <span class="badge badge--role dash-hero__role">Admin</span>
    </div>

    <div class="stat-grid">
        <div class="stat stat--indigo"><span class="stat__ikon"><x-ikon nama="buku" /></span><span class="stat__angka">{{ $totalBuku }}</span><span class="stat__label">Judul buku</span></div>
        <div class="stat stat--sky"><span class="stat__ikon"><x-ikon nama="pengguna" /></span><span class="stat__angka">{{ $totalPengguna }}</span><span class="stat__label">Pengguna</span></div>
        <div class="stat stat--amber"><span class="stat__ikon"><x-ikon nama="jam" /></span><span class="stat__angka">{{ $diajukan }}</span><span class="stat__label">Menunggu persetujuan</span></div>
        <div class="stat stat--blue"><span class="stat__ikon"><x-ikon nama="pinjam" /></span><span class="stat__angka">{{ $dipinjam }}</span><span class="stat__label">Sedang dipinjam</span></div>
        <div class="stat stat--green"><span class="stat__ikon"><x-ikon nama="cek" /></span><span class="stat__angka">{{ $dikembalikan }}</span><span class="stat__label">Sudah dikembalikan</span></div>
    </div>

    <div class="dash-kolom">
        @include('dashboard.partials.terbaru')

        <div class="dash-panel">
            <div class="dash-panel__head"><h3>Pengguna per role</h3></div>
            @php $maks = max(1, $penggunaPerRole->max()); @endphp
            <ul class="role-bar">
                @foreach ($penggunaPerRole as $role => $jumlah)
                    <li>
                        <div class="role-bar__baris"><span class="badge badge--role">{{ $role }}</span><strong>{{ $jumlah }}</strong></div>
                        <div class="role-bar__jalur"><div class="role-bar__isi" style="width: {{ round($jumlah / $maks * 100) }}%"></div></div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <h3 class="dash-judul">Menu cepat</h3>
    <div class="menu-kartu">
        <a href="{{ route('web.buku.index') }}"><x-ikon nama="buku" /><strong>Kelola Buku</strong><span>Tambah, ubah, hapus katalog</span></a>
        <a href="{{ route('admin.users.index') }}"><x-ikon nama="pengguna" /><strong>Pengguna &amp; Role</strong><span>Atur akun dan hak akses</span></a>
        <a href="{{ route('peminjaman.index') }}"><x-ikon nama="pinjam" /><strong>Peminjaman</strong><span>Setujui atau tolak pengajuan</span></a>
        <a href="{{ route('pengembalian.index') }}"><x-ikon nama="cek" /><strong>Pengembalian</strong><span>Catat buku yang kembali</span></a>
        <a href="{{ route('laporan.index') }}"><x-ikon nama="laporan" /><strong>Laporan</strong><span>Rekap aktivitas perpustakaan</span></a>
    </div>
@endsection
