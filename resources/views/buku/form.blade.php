@extends('layouts.app')

@section('title', $buku->exists ? 'Ubah Buku' : 'Tambah Buku')

@section('content')
    <form method="POST"
          action="{{ $buku->exists ? route('web.buku.update', $buku->id) : route('web.buku.store') }}"
          class="form-buku">
        @csrf
        @if ($buku->exists)
            @method('PUT')
        @endif

        <h3>{{ $buku->exists ? 'Ubah Buku' : 'Tambah Buku' }}</h3>

        <div class="form-buku__grid">
            <div class="form-buku__field">
                <label for="kategori_id">Kategori</label>
                <select id="kategori_id" name="kategori_id">
                    <option value="">— Pilih kategori —</option>
                    @foreach ($kategoris as $kategori)
                        <option value="{{ $kategori->id }}" @selected(old('kategori_id', $buku->kategori_id) == $kategori->id)>{{ $kategori->nama }}</option>
                    @endforeach
                </select>
                <small class="form-buku__error">{{ $errors->first('kategori_id') }}</small>
            </div>

            <div class="form-buku__field">
                <label for="judul">Judul</label>
                <input type="text" id="judul" name="judul" maxlength="150" value="{{ old('judul', $buku->judul) }}">
                <small class="form-buku__error">{{ $errors->first('judul') }}</small>
            </div>

            <div class="form-buku__field">
                <label for="penulis">Penulis</label>
                <input type="text" id="penulis" name="penulis" maxlength="100" value="{{ old('penulis', $buku->penulis) }}">
                <small class="form-buku__error">{{ $errors->first('penulis') }}</small>
            </div>

            <div class="form-buku__field">
                <label for="tahun_terbit">Tahun Terbit</label>
                <input type="number" id="tahun_terbit" name="tahun_terbit" min="1900" max="{{ date('Y') }}" value="{{ old('tahun_terbit', $buku->tahun_terbit) }}">
                <small class="form-buku__error">{{ $errors->first('tahun_terbit') }}</small>
            </div>
        </div>

        <div class="form-buku__aksi">
            <button type="submit" class="btn-primary">{{ $buku->exists ? 'Simpan Perubahan' : 'Simpan' }}</button>
            <a href="{{ route('web.buku.index') }}" class="btn-sekunder btn-link">Batal</a>
        </div>
    </form>
@endsection
