@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
    <section class="home-hero">
        <div class="home-hero__teks">
            <p class="home-hero__kicker">Ruang Baca Digital</p>
            <h2>Cari buku, baca ceritanya, pulangkan tepat waktu.</h2>
            <p>
                Katalog ini menyimpan koleksi buku perpustakaan kami — dari fiksi
                klasik sampai referensi teknis. Masuk untuk menelusuri judul,
                penulis, dan tahun terbitnya, serta mengajukan peminjaman.
            </p>

            <div class="home-hero__aksi">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-hero btn-hero--utama">Buka Halaman Saya</a>
                    @can('lihat-daftar-buku')
                        <a href="{{ route('web.buku.index') }}" class="btn-hero btn-hero--garis">Daftar Buku</a>
                    @endcan
                @else
                    <a href="{{ route('login') }}" class="btn-hero btn-hero--utama">Login</a>
                    <a href="{{ route('register') }}" class="btn-hero btn-hero--garis">Registrasi</a>
                @endauth
            </div>
        </div>

        <div class="home-hero__rak" aria-hidden="true">
            <span style="--t: 70%; --c: #fbbf24"></span><span style="--t: 90%; --c: #34d399"></span>
            <span style="--t: 60%; --c: #60a5fa"></span><span style="--t: 100%; --c: #f472b6"></span>
            <span style="--t: 78%; --c: #a78bfa"></span><span style="--t: 88%; --c: #fb923c"></span>
        </div>
    </section>

    <div class="home-fitur">
        <div class="home-fitur__kartu">
            <span class="home-fitur__ikon"><x-ikon nama="buku" /></span>
            <strong>Telusuri katalog</strong>
            <span>Temukan buku berdasarkan judul, penulis, dan tahun terbit.</span>
        </div>
        <div class="home-fitur__kartu">
            <span class="home-fitur__ikon"><x-ikon nama="pinjam" /></span>
            <strong>Ajukan peminjaman</strong>
            <span>Ajukan dari rumah, petugas akan memprosesnya untuk Anda.</span>
        </div>
        <div class="home-fitur__kartu">
            <span class="home-fitur__ikon"><x-ikon nama="jam" /></span>
            <strong>Pantau riwayat</strong>
            <span>Lihat status dan tanggal kembali agar tidak terlambat.</span>
        </div>
    </div>
@endsection
