<div class="dash-panel">
    <div class="dash-panel__head">
        <h3>Peminjaman terbaru</h3>
        <a href="{{ route('peminjaman.index') }}">Lihat semua &rarr;</a>
    </div>
    <div class="tabel-wrap tabel-wrap--flat">
        <table class="tabel-buku">
            <thead><tr><th>Peminjam</th><th>Buku</th><th>Tanggal</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse ($terbaru as $p)
                    <tr>
                        <td>{{ $p->anggota->nama ?? '-' }}</td>
                        <td>{{ $p->buku->judul ?? '-' }}</td>
                        <td>{{ $p->tanggal_pinjam?->format('d M Y') }}</td>
                        <td><span class="badge badge--{{ $p->status }}">{{ $p->status }}</span></td>
                        <td><a href="{{ route('peminjaman.show', $p->id) }}" class="btn-ubah">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="tabel-buku__info">Belum ada peminjaman.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
