@extends('layouts.app')

@section('title', 'Detail Peminjaman')

@section('content')
    <h2>Detail Peminjaman #{{ $peminjaman->id }}</h2>

    <div class="detail-buku">
        <div class="detail-buku__row">
            <span class="detail-buku__label">Peminjam</span>
            <span class="detail-buku__value detail-buku__value--title">{{ $peminjaman->anggota->nama ?? '-' }}</span>
        </div>
        <div class="detail-buku__row">
            <span class="detail-buku__label">Email</span>
            <span class="detail-buku__value">{{ $peminjaman->anggota->email ?? '-' }}</span>
        </div>
        <div class="detail-buku__row">
            <span class="detail-buku__label">No. telepon</span>
            <span class="detail-buku__value">{{ $peminjaman->anggota->no_telepon ?? '-' }}</span>
        </div>
        <div class="detail-buku__row">
            <span class="detail-buku__label">Buku</span>
            <span class="detail-buku__value">{{ $peminjaman->buku->judul ?? '-' }}</span>
        </div>
        <div class="detail-buku__row">
            <span class="detail-buku__label">Tanggal pinjam</span>
            <span class="detail-buku__value">{{ $peminjaman->tanggal_pinjam?->format('d-m-Y') }}</span>
        </div>
        <div class="detail-buku__row">
            <span class="detail-buku__label">Tanggal kembali</span>
            <span class="detail-buku__value">{{ $peminjaman->tanggal_kembali?->format('d-m-Y') ?? '-' }}</span>
        </div>
        <div class="detail-buku__row">
            <span class="detail-buku__label">Status</span>
            <span class="detail-buku__value"><span class="badge badge--{{ $peminjaman->status }}">{{ $peminjaman->status }}</span></span>
        </div>
    </div>

    <p class="menu-cepat">
        <a href="{{ route('peminjaman.saya') }}" class="btn-sekunder btn-link">« Peminjaman saya</a>
    </p>
@endsection
